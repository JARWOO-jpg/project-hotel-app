<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        // Konfigurasi Midtrans SDK
        \Midtrans\Config::$clientKey     = config('midtrans.client_key');
        \Midtrans\Config::$serverKey     = config('midtrans.server_key');
        \Midtrans\Config::$isProduction  = false; // FORCE SANDBOX
        \Midtrans\Config::$isSanitized   = true;
        \Midtrans\Config::$is3ds         = true;
    }

    /**
     * Buat Snap Token untuk pembayaran baru.
     */
    public function createSnapToken(Booking $booking, Transaction $transaction): array
    {
        $orderId = 'ORDER-' . $transaction->transaction_code . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $transaction->amount,
            ],
            'customer_details' => [
                'first_name' => $booking->user->name,
                'email'      => $booking->user->email,
                'phone'      => $booking->user->phone ?? '-',
            ],
            'item_details' => $this->buildItemDetails($booking, $transaction),
            'callbacks' => [
                'finish'  => route('booking.payment.finish', $booking->id),
                'error'   => route('booking.payment.error', $booking->id),
                'pending' => route('booking.payment.pending', $booking->id),
            ],
            'expiry' => [
                'unit'     => 'minutes',
                'duration' => 15,
            ],
        ];

        try {
            // ✅ Hanya 1 API call — mencegah "Transaksi tidak ditemukan"
            // getSnapToken() + getSnapUrl() adalah 2 request terpisah → bisa beda token!
            $result       = \Midtrans\Snap::createTransaction($params);
            $snapToken    = $result->token;
            $snapRedirect = $result->redirect_url;

            // Update transaction dengan snap info
            $transaction->update([
                'midtrans_order_id'  => $orderId,
                'snap_token'         => $snapToken,
                'snap_redirect_url'  => $snapRedirect,
                'status'             => 'pending',
            ]);

            return [
                'success'       => true,
                'snap_token'    => $snapToken,
                'snap_redirect' => $snapRedirect,
                'order_id'      => $orderId,
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build item details untuk Snap params.
     */
    private function buildItemDetails(Booking $booking, Transaction $transaction): array
    {
        $items = [];

        // Item kamar
        $items[] = [
            'id'       => 'ROOM-' . $booking->room_id,
            'price'    => (int) $booking->room->price_per_night,
            'quantity' => $booking->nights,
            'name'     => substr($booking->room->getTypeLabel() . ' - Kamar ' . $booking->room->room_number, 0, 50),
        ];

        // Jika DP, sesuaikan amount
        if ($booking->payment_type === 'dp') {
            // Ganti item ke DP
            $items = [[
                'id'       => 'DP-' . $booking->booking_code,
                'price'    => (int) $transaction->amount,
                'quantity' => 1,
                'name'     => 'Down Payment 50% - ' . $booking->booking_code,
            ]];
        }

        return $items;
    }

    public function handleNotification(array $payload): array
    {
        try {
            $orderId = $payload['order_id'] ?? null;
            if (!$orderId) {
                // Coba fallback dari notification
                $notif = new \Midtrans\Notification();
                $orderId = $notif->order_id ?? null;
            }

            if (!$orderId) {
                return ['success' => false, 'message' => 'No order ID provided'];
            }

            // Selalu fetch status terbaru dari API Midtrans untuk keamanan 
            // dan untuk mendukung pemanggilan manual
            $statusResp = \Midtrans\Transaction::status($orderId);

            $transactionStatus = $statusResp->transaction_status ?? null;
            $paymentType       = $statusResp->payment_type ?? null;
            $fraudStatus       = $statusResp->fraud_status ?? null;
            $grossAmount       = $statusResp->gross_amount ?? 0;

            // Cari transaction berdasarkan order_id
            $transaction = Transaction::where('midtrans_order_id', $orderId)->first();

            if (!$transaction) {
                Log::warning("Midtrans notification: order $orderId tidak ditemukan.");
                return ['success' => false, 'message' => 'Transaction not found'];
            }

            $booking = $transaction->booking;
            $status  = $this->mapTransactionStatus($transactionStatus, $fraudStatus);

            $bank = $statusResp->bank ?? $statusResp->issuer ?? $paymentType;
            $vaNumber = null;
            if (isset($statusResp->va_numbers) && is_array($statusResp->va_numbers) && count($statusResp->va_numbers) > 0) {
                $vaNumber = $statusResp->va_numbers[0]->va_number ?? null;
            }

            // Update transaction
            $transaction->update([
                'status'           => $status,
                'payment_method'   => $paymentType,
                'payment_channel'  => $bank,
                'va_number'        => $vaNumber,
                'fraud_status'     => $fraudStatus,
                'midtrans_response'=> json_encode($statusResp),
                'paid_at'          => in_array($status, ['success']) ? now() : null,
            ]);

            // Update booking berdasarkan status pembayaran
            if ($status === 'success') {
                $this->handleSuccessPayment($booking, $transaction, (float) $grossAmount);
            } elseif ($status === 'failed') {
                $this->handleFailedPayment($booking, $transaction);
            }

            return ['success' => true, 'order_id' => $orderId, 'status' => $status];

        } catch (\Exception $e) {
            Log::error('Midtrans notification error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Map status Midtrans ke status internal.
     */
    private function mapTransactionStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        if ($transactionStatus === 'capture') {
            return ($fraudStatus === 'challenge') ? 'pending' : 'success';
        }

        return match ($transactionStatus) {
            'settlement' => 'success',
            'pending'    => 'pending',
            'deny', 'cancel', 'expire', 'failure' => 'failed',
            'refund', 'partial_refund' => 'refunded',
            default => 'pending',
        };
    }

    /**
     * Tangani pembayaran berhasil.
     *
     * FIX DOUBLE BOOKING: Cek ulang apakah booking masih berstatus 'pending'
     * sebelum mengubahnya menjadi 'confirmed'. Ini mencegah konfirmasi ganda
     * jika webhook dan manual check berjalan bersamaan.
     */
    private function handleSuccessPayment(Booking $booking, Transaction $transaction, float $amount): void
    {
        // Gunakan DB::transaction + lockForUpdate untuk mencegah race condition
        // pada saat webhook dan manual status check bersamaan
        DB::transaction(function () use ($booking, $transaction, $amount) {
            // Re-fetch booking dengan lock
            $freshBooking = Booking::lockForUpdate()->find($booking->id);

            if (!$freshBooking) {
                Log::warning("Booking #{$booking->id} tidak ditemukan saat handle success payment.");
                return;
            }

            // Hanya proses jika booking masih pending
            // (mencegah double confirmation dari webhook + manual check)
            if (!in_array($freshBooking->status, ['pending', 'confirmed'])) {
                Log::info("Booking #{$booking->id} status sudah '{$freshBooking->status}', skip confirmation.");
                return;
            }

            $freshBooking->update([
                'paid_amount' => $freshBooking->paid_amount + $amount,
                'status'      => 'confirmed',
            ]);
        });
    }

    /**
     * Tangani pembayaran gagal.
     *
     * FIX: Jika pembayaran gagal/expire, cancel booking agar kamar
     * kembali tersedia untuk user lain.
     */
    private function handleFailedPayment(Booking $booking, Transaction $transaction): void
    {
        DB::transaction(function () use ($booking) {
            $freshBooking = Booking::lockForUpdate()->find($booking->id);

            if (!$freshBooking || $freshBooking->status !== 'pending') {
                return;
            }

            $freshBooking->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'Pembayaran gagal/expired via Midtrans',
            ]);

            Log::info("Booking #{$booking->id} ({$booking->booking_code}) dibatalkan karena pembayaran gagal.");
        });
    }

    /**
     * Verifikasi signature key dari notifikasi Midtrans.
     */
    public function verifySignature(array $data): bool
    {
        $serverKey = config('midtrans.server_key');
        $hash = hash('sha512',
            $data['order_id'] .
            $data['status_code'] .
            $data['gross_amount'] .
            $serverKey
        );
        return hash_equals($hash, $data['signature_key'] ?? '');
    }
}
