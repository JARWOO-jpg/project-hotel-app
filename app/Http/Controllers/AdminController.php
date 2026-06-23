<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\SiteSetting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Facility;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalRevenue = Transaction::where('status', 'success')
            ->whereIn('type', ['payment', 'dp_payment', 'checkout_payment'])
            ->sum('amount');

        $monthlyRevenue = Transaction::where('status', 'success')
            ->whereIn('type', ['payment', 'dp_payment', 'checkout_payment'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $totalBookings = Booking::count();
        $activeBookings = Booking::whereIn('status', ['pending', 'confirmed'])->count();
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $availableRooms = Room::where('status', 'available')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        // Monthly revenue chart data (last 6 months)
        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = Transaction::where('status', 'success')
                ->whereIn('type', ['payment', 'dp_payment', 'checkout_payment'])
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('amount');
            $revenueChart[] = [
                'month' => $date->translatedFormat('M Y'),
                'revenue' => $revenue,
            ];
        }

        // Room occupancy by type
        $roomStats = Room::selectRaw('type, count(*) as total, sum(case when status = "occupied" then 1 else 0 end) as occupied')
            ->groupBy('type')
            ->get();

        // Recent bookings
        $recentBookings = Booking::with(['user', 'room'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Guest demographics
        $guestStats = User::where('role', 'guest')
            ->selectRaw('count(*) as total')
            ->first();

        $waitingList = Booking::where('status', 'waiting_list')
            ->with(['user', 'room'])
            ->get();

        // Riwayat tamu yang sudah check-out hari ini
        $todayCheckedOut = Booking::where('status', 'checked_out')
            ->whereDate('actual_check_out', today())
            ->with(['user', 'room', 'services', 'transactions'])
            ->orderBy('actual_check_out', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue', 'monthlyRevenue', 'totalBookings', 'activeBookings',
            'totalRooms', 'occupiedRooms', 'availableRooms', 'occupancyRate',
            'revenueChart', 'roomStats', 'recentBookings', 'guestStats', 'waitingList',
            'todayCheckedOut'
        ));
    }

    // ─── Room Management ─────────────────────────────
    public function rooms()
    {
        $rooms = Room::orderBy('room_number')->get();
        return view('admin.rooms.index', compact('rooms'));
    }

    public function createRoom()
    {
        return view('admin.rooms.form');
    }

    public function storeRoom(Request $request)
    {
        $request->validate([
            'room_number' => 'required|string|unique:rooms',
            'type' => 'required|in:superior,deluxe,junior_suite,presidential_suite',
            'price_per_night' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'required|in:available,maintenance',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'room_number', 'type', 'price_per_night', 'capacity', 'description', 'status'
        ]);
        $data['amenities'] = $request->input('amenities', []);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('rooms', 'public');
        }

        Room::create($data);

        return redirect()->route('admin.rooms')->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function editRoom(Room $room)
    {
        return view('admin.rooms.form', compact('room'));
    }

    public function updateRoom(Request $request, Room $room)
    {
        $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number,' . $room->id,
            'type' => 'required|in:superior,deluxe,junior_suite,presidential_suite',
            'price_per_night' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'required|in:available,occupied,maintenance',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'room_number', 'type', 'price_per_night', 'capacity', 'description', 'status'
        ]);
        $data['amenities'] = $request->input('amenities', []);

        if ($request->hasFile('image')) {
            if ($room->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($room->image);
            }
            $data['image'] = $request->file('image')->store('rooms', 'public');
        }

        $room->update($data);

        return redirect()->route('admin.rooms')->with('success', 'Kamar berhasil diperbarui.');
    }

    public function deleteRoom(Room $room)
    {
        if ($room->status === 'occupied') {
            return back()->with('error', 'Tidak dapat menghapus kamar yang sedang terisi.');
        }

        $room->delete();
        return redirect()->route('admin.rooms')->with('success', 'Kamar berhasil dihapus.');
    }

    // ─── Facility Management ─────────────────────────────
    public function facilities()
    {
        $facilities = Facility::all();
        return view('admin.facilities.index', compact('facilities'));
    }

    public function createFacility()
    {
        return view('admin.facilities.form');
    }

    public function storeFacility(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'icon', 'description']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('facilities', 'public');
        }

        Facility::create($data);

        return redirect()->route('admin.facilities')->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function editFacility(Facility $facility)
    {
        return view('admin.facilities.form', compact('facility'));
    }

    public function updateFacility(Request $request, Facility $facility)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'icon', 'description']);

        if ($request->hasFile('image')) {
            if ($facility->image) {
                Storage::disk('public')->delete($facility->image);
            }
            $data['image'] = $request->file('image')->store('facilities', 'public');
        }

        $facility->update($data);

        return redirect()->route('admin.facilities')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function deleteFacility(Facility $facility)
    {
        if ($facility->image) {
            Storage::disk('public')->delete($facility->image);
        }
        $facility->delete();
        return redirect()->route('admin.facilities')->with('success', 'Fasilitas berhasil dihapus.');
    }

    // ─── Booking Management ──────────────────────────
    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'room']);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('booking_code', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$request->search}%"));
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function handleWaitingList(Request $request, Booking $booking)
    {
        $action = $request->action; // 'approve' or 'reject'

        if ($action === 'approve') {
            $room = Room::find($booking->room_id);
            if ($room && $room->isAvailableForDates($booking->check_in_date, $booking->check_out_date)) {
                $booking->update(['status' => 'confirmed']);
                return back()->with('success', 'Booking dari waiting list telah disetujui.');
            }
            return back()->with('error', 'Kamar masih belum tersedia.');
        }

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => 'Ditolak dari waiting list - kamar tidak tersedia',
        ]);

        return back()->with('info', 'Booking telah ditolak dari waiting list.');
    }

    // ─── User Management ─────────────────────────────
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.form');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'role' => 'required|in:guest,receptionist,admin',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function editUser(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:guest,receptionist,admin',
            'password' => 'nullable|string|min:6',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'role']);

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil dihapus.');
    }

    // ─── Reports ─────────────────────────────────────
    public function reports(Request $request)
    {
        $period = $request->period ?? 'monthly';
        $year = $request->year ?? now()->year;

        // Revenue report
        $revenueData = [];
        if ($period === 'monthly') {
            for ($m = 1; $m <= 12; $m++) {
                $revenue = Transaction::where('status', 'success')
                    ->whereIn('type', ['payment', 'dp_payment', 'checkout_payment'])
                    ->whereMonth('created_at', $m)
                    ->whereYear('created_at', $year)
                    ->sum('amount');
                $revenueData[] = [
                    'label' => Carbon::create($year, $m, 1)->translatedFormat('M'),
                    'value' => $revenue,
                ];
            }
        }

        // Occupancy data
        $occupancyData = [];
        $rooms = Room::all();
        foreach (['superior', 'deluxe', 'junior_suite', 'presidential_suite'] as $type) {
            $total = $rooms->where('type', $type)->count();
            $occupied = $rooms->where('type', $type)->where('status', 'occupied')->count();
            $occupancyData[] = [
                'type' => Room::make(['type' => $type])->getTypeLabel(),
                'total' => $total,
                'occupied' => $occupied,
                'rate' => $total > 0 ? round(($occupied / $total) * 100, 1) : 0,
            ];
        }

        // Guest demographics
        $guestDemographics = [
            'total_guests' => User::where('role', 'guest')->count(),
            'total_bookings' => Booking::count(),
            'avg_stay' => Booking::whereNotNull('actual_check_out')
                ->selectRaw('AVG(DATEDIFF(check_out_date, check_in_date)) as avg_stay')
                ->value('avg_stay') ?? 0,
            'repeat_guests' => User::where('role', 'guest')
                ->has('bookings', '>', 1)
                ->count(),
        ];

        return view('admin.reports', compact('revenueData', 'occupancyData', 'guestDemographics', 'period', 'year'));
    }

    // ─── CMS (Content Management System) ─────────────
    public function cms()
    {
        $groups = [
            'hero'     => ['title' => 'Visual & Teks Halaman Depan',   'icon' => '🖼️', 'desc' => 'Hero image, judul, dan deskripsi hotel'],
            'facility' => ['title' => 'Seksi Fasilitas Hotel',         'icon' => '🏨', 'desc' => 'Judul, sub-judul, dan daftar fasilitas hotel'],
            'footer'   => ['title' => 'Footer & Informasi Kontak',     'icon' => '📍', 'desc' => 'Alamat, telepon, email, dan hak cipta'],
            'social'   => ['title' => 'Tautan Media Sosial',           'icon' => '🔗', 'desc' => 'Instagram, TikTok, YouTube'],
            'promo'    => ['title' => 'Kampanye & Banner Promo',       'icon' => '📣', 'desc' => 'Banner promosi dan event spesial'],
        ];

        if (\App\Models\SiteSetting::count() === 0) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'SiteSettingSeeder']);
        }

        // Auto-seed facility CMS keys if missing
        if (!SiteSetting::where('key', 'facility_title')->exists()) {
            SiteSetting::updateOrCreate(['key' => 'facility_title'], ['group' => 'facility', 'label' => 'Judul Seksi Fasilitas', 'type' => 'text', 'value' => 'Pengalaman Istimewa']);
            SiteSetting::updateOrCreate(['key' => 'facility_subtitle'], ['group' => 'facility', 'label' => 'Sub-Judul Seksi Fasilitas', 'type' => 'textarea', 'value' => 'Nikmati berbagai fasilitas premium yang kami sediakan untuk kenyamanan Anda selama menginap']);
        }

        $settings = [];
        foreach (array_keys($groups) as $group) {
            $settings[$group] = SiteSetting::getGroup($group);
        }

        $facilities = Facility::all();

        return view('admin.cms', compact('groups', 'settings', 'facilities'));
    }

    public function updateCms(Request $request)
    {
        $settings = SiteSetting::all();

        foreach ($settings as $setting) {
            if ($setting->type === 'image') {
                // Handle file upload
                if ($request->hasFile('setting_' . $setting->key)) {
                    $request->validate([
                        'setting_' . $setting->key => 'image|max:3072',
                    ]);

                    // Hapus gambar lama
                    if ($setting->value) {
                        Storage::disk('public')->delete($setting->value);
                    }

                    $path = $request->file('setting_' . $setting->key)->store('cms', 'public');
                    $setting->update(['value' => $path]);
                }
            } else {
                // Handle text/textarea/url (Bisa menerima input kosong / null)
                if ($request->has('setting_' . $setting->key)) {
                    $setting->update(['value' => $request->input('setting_' . $setting->key)]);
                }
            }
        }

        // Facility Updates
        if ($request->has('facility') && is_array($request->facility)) {
            foreach ($request->facility as $id => $data) {
                $facility = Facility::find($id);
                if ($facility) {
                    $facility->name = $data['name'] ?? $facility->name;
                    $facility->description = $data['description'] ?? $facility->description;

                    if (isset($data['image']) && $request->hasFile("facility.{$id}.image")) {
                        $request->validate([
                            "facility.{$id}.image" => 'image|max:3072',
                        ]);

                        if ($facility->image) {
                            Storage::disk('public')->delete($facility->image);
                        }

                        $facility->image = $request->file("facility.{$id}.image")->store('facilities', 'public');
                    }
                    $facility->save();
                }
            }
        }

        return redirect()->route('admin.cms')->with('success', 'Pengaturan website dan fasilitas berhasil diperbarui.');
    }
}
