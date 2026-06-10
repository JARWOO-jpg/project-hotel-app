<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Transaction;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'payment_type' => 'required|in:full,dp',
            'payment_method' => 'required|in:cash,transfer,credit_card,e_wallet',
        ]);

        $room = Room::findOrFail($request->room_id);

        // Overbooking check
        if (!$room->isAvailableForDates($request->check_in, $request->check_out)) {
            // Check waiting list option
            if ($request->accept_waiting_list) {
                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'room_id' => $room->id,
                    'check_in_date' => $request->check_in,
                    'check_out_date' => $request->check_out,
                    'guests' => $request->guests,
                    'status' => 'waiting_list',
                    'payment_type' => $request->payment_type,
                    'total_price' => $room->price_per_night * ((strtotime($request->check_out) - strtotime($request->check_in)) / 86400),
                    'special_request' => $request->special_request,
                ]);

                return redirect()->route('booking.status', $booking->id)
                    ->with('info', 'Anda masuk ke Waiting List. Kami akan menghubungi Anda jika kamar tersedia.');
            }

            return back()->with('error', 'Maaf, kamar sudah penuh. Silakan pilih tanggal atau tipe kamar lain.');
        }

        $nights = (int) (strtotime($request->check_out) - strtotime($request->check_in)) / 86400;
        $totalPrice = $room->price_per_night * $nights;
        $dpAmount = $request->payment_type === 'dp' ? $totalPrice * 0.5 : 0;
        $paidAmount = $request->payment_type === 'dp' ? $dpAmount : $totalPrice;

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'room_id' => $room->id,
            'check_in_date' => $request->check_in,
            'check_out_date' => $request->check_out,
            'guests' => $request->guests,
            'status' => 'confirmed',
            'payment_type' => $request->payment_type,
            'total_price' => $totalPrice,
            'dp_amount' => $dpAmount,
            'paid_amount' => $paidAmount,
            'special_request' => $request->special_request,
        ]);

        // Create transaction
        Transaction::create([
            'booking_id' => $booking->id,
            'type' => $request->payment_type === 'dp' ? 'dp_payment' : 'payment',
            'amount' => $paidAmount,
            'payment_method' => $request->payment_method,
            'status' => 'success',
            'notes' => $request->payment_type === 'dp'
                ? 'Down Payment 50% dari total Rp ' . number_format($totalPrice, 0, ',', '.')
                : 'Pembayaran lunas',
        ]);

        return redirect()->route('booking.status', $booking->id)
            ->with('success', 'Booking berhasil! Status: Terkonfirmasi.');
    }

    public function status(Booking $booking)
    {
        // Ensure user can only see their own booking
        if ($booking->user_id !== auth()->id() && !in_array(auth()->user()->role, ['admin', 'receptionist'])) {
            abort(403);
        }

        $booking->load(['room', 'transactions', 'services']);

        return view('guest.booking-status', compact('booking'));
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
