<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function __construct(private MidtransService $midtrans) {}

    /**
     * Buat Snap Token dan kembalikan ke frontend.
     */
    public function createSnapToken(Request $request, Booking $booking)
    {
        // Pastikan booking milik user ini
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        // ✅ Cek apakah Midtrans SDK sudah diinstall
        if (!class_exists('\Midtrans\Config')) {
            return response()->json([
                'error' => 'Midtrans SDK belum diinstall. Jalankan perintah berikut di terminal: <br><code>composer require midtrans/midtrans-php</code>',
            ], 500);
        }

        // ✅ Cek credentials
        $serverKey = config('midtrans.server_key', '');
        if (empty($serverKey) || str_contains($serverKey, 'xxxxxxxxxxxx')) {
            return response()->json([
                'error' => 'Midtrans Server Key belum diisi. Update MIDTRANS_SERVER_KEY di file .env dengan credentials dari dashboard.midtrans.com',
            ], 500);
        }

        // Cari apakah sudah ada transaksi pending untuk booking ini
        $transaction = $booking->transactions()->where('status', 'pending')->latest()->first();

        // Jika sudah ada token, kita bisa cek dulu statusnya, 
        // tapi untuk efisiensi kita kembalikan token yang ada
        if ($transaction && $transaction->snap_token) {
            return response()->json([
                'snap_token'   => $transaction->snap_token,
                'redirect_url' => $transaction->snap_redirect_url,
            ]);
        }

        // Jika tidak ada, buat transaction baru
        $amount = $booking->payment_type === 'dp'
            ? (float) $booking->dp_amount
            : (float) $booking->total_price;

        $transaction = Transaction::create([
            'booking_id'     => $booking->id,
            'type'           => $booking->payment_type === 'dp' ? 'dp_payment' : 'payment',
            'amount'         => $amount,
            'payment_method' => 'midtrans',
            'status'         => 'pending',
            'notes'          => 'Menunggu pembayaran via Midtrans',
        ]);

        $result = $this->midtrans->createSnapToken($booking->load('user', 'room'), $transaction);

        if (!$result['success']) {
            // Hapus transaction yang gagal agar tidak ada orphan record
            $transaction->delete();
            return response()->json(['error' => $result['message']], 500);
        }

        return response()->json([
            'snap_token'   => $result['snap_token'],
            'redirect_url' => $result['snap_redirect'],
        ]);
    }


    /**
     * Webhook / Payment Notification dari Midtrans.
     * Endpoint ini harus EXEMPT dari CSRF.
     */
    public function notification(Request $request)
    {
        $payload = $request->all();

        Log::info('Midtrans Notification: ' . json_encode($payload));

        // Verifikasi signature
        if (!$this->midtrans->verifySignature($payload)) {
            Log::warning('Midtrans: Invalid signature key!');
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $result = $this->midtrans->handleNotification($payload);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 400);
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * Halaman setelah pembayaran selesai (redirect dari Snap).
     */
    public function finish(Request $request, Booking $booking)
    {
        $booking->load(['room', 'transactions', 'services']);

        // BUG FIX: Saat testing di localhost, Midtrans tidak bisa mengirim webhook.
        // Kita paksa cek status transaksi langsung ke API Midtrans.
        $transaction = $booking->transactions()->where('payment_method', 'midtrans')->latest()->first();
        
        if ($transaction && $transaction->midtrans_order_id && $transaction->status === 'pending') {
            try {
                $statusResp = \Midtrans\Transaction::status($transaction->midtrans_order_id);
                // Kita gunakan method handleNotification yang sudah ada untuk proses
                // karena response formatnya mirip dengan webhook
                $payload = (array) $statusResp;
                // Kita bypass signature verify untuk manual check ini
                $result = $this->midtrans->handleNotification($payload);
                if ($result['success'] && $result['status'] === 'success') {
                    return redirect()->route('booking.status', $booking->id)
                        ->with('success', '✅ Pembayaran berhasil! Booking Anda telah dikonfirmasi.');
                }
            } catch (\Exception $e) {
                Log::error('Manual status check error: ' . $e->getMessage());
            }
        }

        return redirect()->route('booking.status', $booking->id)
            ->with('success', '✅ Proses pembayaran selesai (Menunggu konfirmasi otomatis).');
    }

    /**
     * Halaman jika pembayaran error.
     */
    public function error(Booking $booking)
    {
        return redirect()->route('booking.status', $booking->id)
            ->with('error', '❌ Pembayaran gagal. Silakan coba kembali.');
    }

    /**
     * Halaman jika pembayaran pending.
     */
    public function pending(Booking $booking)
    {
        return redirect()->route('booking.status', $booking->id)
            ->with('info', '⏳ Pembayaran Anda sedang diproses. Selesaikan dalam 24 jam.');
    }
}
