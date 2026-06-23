<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $room = Room::findOrFail($request->room_id);
        $checkIn = $request->check_in;
        $checkOut = $request->check_out;
        $guests = $request->guests ?? 1;

        if (!$room->isAvailableForDates($checkIn, $checkOut)) {
            return back()->with('error', 'Kamar tidak tersedia untuk tanggal yang dipilih.');
        }

        $nights = (int) (strtotime($checkOut) - strtotime($checkIn)) / 86400;
        $totalPrice = $room->price_per_night * $nights;

        return view('guest.booking', compact('room', 'checkIn', 'checkOut', 'guests', 'nights', 'totalPrice'));
    }

    /**
     * Store booking dengan DB::transaction() + Pessimistic Locking.
     *
     * Alur:
     * 1. Validasi input
     * 2. Mulai database transaction
     * 3. Cek ketersediaan kamar dengan lockForUpdate() (mencegah race condition)
     * 4. Jika tersedia → buat booking 'pending'
     * 5. Jika tidak tersedia → rollback & return error 409
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id'      => 'required|exists:rooms,id',
            'check_in'     => 'required|date|after_or_equal:today',
            'check_out'    => 'required|date|after:check_in',
            'guests'         => 'required|integer|min:1',
            'payment_type'   => 'required|in:full,dp',
            'identity_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle photo upload SEBELUM transaction (filesystem I/O diluar DB lock)
        $identityPhotoPath = null;
        if ($request->hasFile('identity_photo')) {
            $identityPhotoPath = $request->file('identity_photo')->store('identity_photos', 'public');
        }

        $room = Room::findOrFail($request->room_id);

        try {
            $booking = DB::transaction(function () use ($request, $room, $identityPhotoPath) {

                // ══════════════════════════════════════════════════════════════
                // PESSIMISTIC LOCK: Cek ketersediaan dengan lockForUpdate()
                // Ini mengunci row booking terkait kamar ini sehingga
                // request lain yang bersamaan harus MENUNGGU sampai
                // transaksi ini selesai (commit/rollback).
                // ══════════════════════════════════════════════════════════════
                $isAvailable = $room->isAvailableForDatesLocked(
                    $request->check_in,
                    $request->check_out
                );

                if (!$isAvailable) {
                    // Throw exception agar DB::transaction() otomatis rollback
                    throw new \App\Exceptions\RoomUnavailableException(
                        'Kamar sudah tidak tersedia untuk tanggal yang dipilih.'
                    );
                }

                $nights     = (int) (strtotime($request->check_out) - strtotime($request->check_in)) / 86400;
                $totalPrice = $room->price_per_night * $nights;
                $dpAmount   = $request->payment_type === 'dp' ? $totalPrice * 0.5 : 0;

                // Buat booking dengan status 'pending'
                return Booking::create([
                    'user_id'        => auth()->id(),
                    'room_id'        => $room->id,
                    'check_in_date'  => $request->check_in,
                    'check_out_date' => $request->check_out,
                    'guests'         => $request->guests,
                    'status'         => 'pending',
                    'payment_type'   => $request->payment_type,
                    'total_price'    => $totalPrice,
                    'dp_amount'      => $dpAmount,
                    'paid_amount'    => 0,
                    'identity_photo' => $identityPhotoPath,
                    'special_request'=> $request->special_request,
                ]);
            });

            // Sukses → redirect ke halaman pembayaran Midtrans
            return redirect()->route('booking.payment', $booking->id)
                ->with('info', '🏨 Booking berhasil dibuat! Selesaikan pembayaran dalam 15 menit.');

        } catch (\App\Exceptions\RoomUnavailableException $e) {
            // Hapus foto yang sudah di-upload jika booking gagal
            if ($identityPhotoPath) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($identityPhotoPath);
            }

            // Jika request AJAX → return JSON 409
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'type'  => 'room_unavailable',
                ], 409);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Booking $booking)
    {
        // Ensure user can only see their own booking
        if ($booking->user_id !== auth()->id() && !in_array(auth()->user()->role, ['admin', 'receptionist'])) {
            abort(403);
        }

        $booking->load(['room', 'transactions', 'services']);

        // BUG FIX: Sinkronisasi manual status transaksi dengan Midtrans
        // Karena di localhost webhook tidak bisa masuk, kita cek status secara proaktif
        // setiap kali user membuka halaman status booking.
        $transaction = $booking->transactions()->where('payment_method', 'midtrans')->where('status', 'pending')->latest()->first();
        if ($transaction && $transaction->midtrans_order_id) {
            try {
                $statusResp = \Midtrans\Transaction::status($transaction->midtrans_order_id);
                app(\App\Services\MidtransService::class)->handleNotification((array) $statusResp);
                
                // Refresh data setelah diupdate
                $booking->refresh();
                $booking->load(['room', 'transactions', 'services']);
            } catch (\Exception $e) {
                // Abaikan jika error (misal order belum ada di midtrans)
            }
        }

        return view('guest.booking-status', compact('booking'));
    }

    /**
     * Halaman pembayaran Midtrans Snap.
     */
    public function payment(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return redirect()->route('booking.status', $booking->id)
                ->with('info', 'Booking ini sudah dalam status ' . $booking->getStatusLabel());
        }

        $booking->load(['room', 'user']);

        $payAmount = $booking->payment_type === 'dp'
            ? (float) $booking->dp_amount
            : (float) ($booking->total_price - $booking->paid_amount);

        return view('guest.payment', compact('booking', 'payAmount'));
    }

    public function myBookings()
    {
        $bookings = auth()->user()->bookings()
            ->with('room')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guest.my-bookings', compact('bookings'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Booking tidak dapat dibatalkan.');
        }

        $refundAmount = 0;
        $refundMessage = '';

        if ($booking->paid_amount > 0) {
            // Refund 80% if cancelled (20% admin fee)
            $refundAmount = $booking->paid_amount * 0.8;
            $refundMessage = 'Refund sebesar Rp ' . number_format($refundAmount, 0, ',', '.') . ' akan diproses dalam 3-5 hari kerja. (Potongan 20% biaya admin)';

            Transaction::create([
                'booking_id' => $booking->id,
                'type' => 'refund',
                'amount' => $refundAmount,
                'payment_method' => $booking->transactions()->first()->payment_method ?? 'transfer',
                'status' => 'pending',
                'notes' => 'Refund pembatalan booking',
            ]);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => request('reason', 'Dibatalkan oleh tamu'),
            'refund_amount' => $refundAmount,
        ]);

        // Release room
        if ($booking->room) {
            $booking->room->update(['status' => 'available']);
        }

        $message = 'Booking berhasil dibatalkan.';
        if ($refundMessage) {
            $message .= ' ' . $refundMessage;
        }

        return redirect()->route('booking.my-bookings')->with('success', $message);
    }

    public function invoice(Booking $booking)
    {
        if ($booking->user_id !== auth()->id() && !in_array(auth()->user()->role, ['admin', 'receptionist'])) {
            abort(403);
        }

        $booking->load(['room', 'user', 'transactions', 'services']);

        return view('guest.invoice', compact('booking'));
    }
}
