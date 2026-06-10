<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReceptionistController extends Controller
{
    public function dashboard()
    {
        $todayCheckIns = Booking::where('check_in_date', today())
            ->where('status', 'confirmed')
            ->with(['user', 'room'])
            ->get();

        $todayCheckOuts = Booking::where('check_out_date', today())
            ->where('status', 'checked_in')
            ->with(['user', 'room'])
            ->get();

        $currentGuests = Booking::where('status', 'checked_in')
            ->with(['user', 'room'])
            ->get();

        $rooms = Room::all();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $availableRooms = Room::where('status', 'available')->count();

        return view('receptionist.dashboard', compact(
            'todayCheckIns', 'todayCheckOuts', 'currentGuests',
            'rooms', 'occupiedRooms', 'availableRooms'
        ));
    }

    public function searchBooking(Request $request)
    {
        $search = $request->search;

        $bookings = Booking::with(['user', 'room'])
            ->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        if ($request->ajax()) {
            return response()->json(['bookings' => $bookings]);
        }

        return view('receptionist.search', compact('bookings', 'search'));
    }

    public function checkIn(Booking $booking)
    {
        $booking->load(['user', 'room', 'transactions']);

        return view('receptionist.check-in', compact('booking'));
    }

    public function processCheckIn(Request $request, Booking $booking)
    {
        // Validate status
        if ($booking->status === 'pending') {
            return back()->with('error', 'Booking masih Pending! Tamu harus menyelesaikan pembayaran terlebih dahulu.');
        }

        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Status booking tidak valid untuk check-in. Status saat ini: ' . $booking->getStatusLabel());
        }

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $room = Room::findOrFail($request->room_id);

        if ($room->status !== 'available') {
            return back()->with('error', 'Kamar ' . $room->room_number . ' tidak tersedia.');
        }

        // Assign room & process check-in
        $booking->update([
            'room_id' => $room->id,
            'status' => 'checked_in',
            'actual_check_in' => Carbon::now(),
        ]);

        $room->update(['status' => 'occupied']);

        return redirect()->route('receptionist.dashboard')
            ->with('success', 'Check-in berhasil! Kamar ' . $room->room_number . ' telah di-assign untuk ' . $booking->user->name);
    }

    public function addService(Request $request, Booking $booking)
    {
        $request->validate([
            'service_type' => 'required|in:room_service,laundry,spa,transport,other',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        $total = $request->amount * $request->quantity;

        Service::create([
            'booking_id' => $booking->id,
            'service_type' => $request->service_type,
            'description' => $request->description,
            'amount' => $request->amount,
            'quantity' => $request->quantity,
            'total' => $total,
            'status' => 'completed',
        ]);

        return back()->with('success', 'Layanan berhasil ditambahkan. Total: Rp ' . number_format($total, 0, ',', '.'));
    }

    public function checkOut(Booking $booking)
    {
        if ($booking->status !== 'checked_in') {
            return redirect()->route('receptionist.dashboard')
                ->with('error', 'Booking belum check-in.');
        }

        $booking->load(['user', 'room', 'services', 'transactions']);

        $nights = $booking->nights;
        $roomCharge = $booking->total_price;
        $servicesTotal = $booking->getServicesTotal();
        $totalBill = $roomCharge + $servicesTotal;
        $remaining = $totalBill - $booking->paid_amount;

        return view('receptionist.check-out', compact(
            'booking', 'nights', 'roomCharge', 'servicesTotal', 'totalBill', 'remaining'
        ));
    }

    public function processCheckOut(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,transfer,credit_card,e_wallet',
        ]);

        $servicesTotal = $booking->getServicesTotal();
        $totalBill = $booking->total_price + $servicesTotal;
        $remaining = $totalBill - $booking->paid_amount;

        if ($remaining > 0) {
            Transaction::create([
                'booking_id' => $booking->id,
                'type' => 'checkout_payment',
                'amount' => $remaining,
                'payment_method' => $request->payment_method,
                'status' => 'success',
                'notes' => 'Pelunasan saat check-out (Sisa + Layanan tambahan)',
            ]);
        }

        $booking->update([
            'status' => 'checked_out',
            'actual_check_out' => Carbon::now(),
            'paid_amount' => $totalBill,
        ]);

        // Release room
        if ($booking->room) {
            $booking->room->update(['status' => 'available']);
        }

        return redirect()->route('booking.invoice', $booking->id)
            ->with('success', 'Check-out berhasil! Invoice final telah dibuat.');
    }

    public function guestBill(Booking $booking)
    {
        $booking->load(['user', 'room', 'services', 'transactions']);

        return view('receptionist.guest-bill', compact('booking'));
    }
}
