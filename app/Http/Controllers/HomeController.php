<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $rooms = Room::where('status', '!=', 'maintenance')
            ->orderBy('price_per_night')
            ->get();

        return view('guest.home', compact('rooms'));
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'room_type' => 'nullable|string',
        ]);

        $query = Room::where('status', '!=', 'maintenance');

        if ($request->room_type && $request->room_type !== 'all') {
            $query->where('type', $request->room_type);
        }

        $rooms = $query->get()->filter(function ($room) use ($request) {
            return $room->isAvailableForDates($request->check_in, $request->check_out);
        });

        // If no rooms available, suggest alternative dates/types
        $suggestions = [];
        if ($rooms->isEmpty()) {
            // Suggest other room types for same dates
            $altRooms = Room::where('status', '!=', 'maintenance')
                ->get()
                ->filter(fn($r) => $r->isAvailableForDates($request->check_in, $request->check_out));

            if ($altRooms->isNotEmpty()) {
                $suggestions['alternative_types'] = $altRooms;
            }

            // Suggest next available dates for requested type
            $nextDates = $this->findNextAvailableDates($request->room_type, $request->check_in);
            if ($nextDates) {
                $suggestions['next_dates'] = $nextDates;
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'rooms' => $rooms->values(),
                'suggestions' => $suggestions,
                'count' => $rooms->count(),
            ]);
        }

        return view('guest.home', [
            'rooms' => Room::where('status', '!=', 'maintenance')->get(),
            'availableRooms' => $rooms,
            'suggestions' => $suggestions,
            'searchPerformed' => true,
            'checkIn' => $request->check_in,
            'checkOut' => $request->check_out,
            'roomType' => $request->room_type,
        ]);
    }

    private function findNextAvailableDates($type, $fromDate)
    {
        $query = Room::where('status', '!=', 'maintenance');
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }
        $rooms = $query->get();

        for ($i = 1; $i <= 14; $i++) {
            $checkIn = date('Y-m-d', strtotime($fromDate . " +{$i} days"));
            $checkOut = date('Y-m-d', strtotime($checkIn . " +1 day"));

            foreach ($rooms as $room) {
                if ($room->isAvailableForDates($checkIn, $checkOut)) {
                    return ['check_in' => $checkIn, 'check_out' => $checkOut];
                }
            }
        }
        return null;
    }
}
