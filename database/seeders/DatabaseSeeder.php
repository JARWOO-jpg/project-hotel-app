<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ─── Admin User ──────────────────────────────
        User::create([
            'name' => 'Admin Hotel',
            'email' => 'admin@hotel.com',
            'phone' => '08123456789',
            'password' => 'password',
            'role' => 'admin',
        ]);

        // ─── Receptionist User ───────────────────────
        User::create([
            'name' => 'Resepsionis',
            'email' => 'resepsionis@hotel.com',
            'phone' => '08234567890',
            'password' => 'password',
            'role' => 'receptionist',
        ]);

        // ─── Guest User ─────────────────────────────
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@gmail.com',
            'phone' => '08345678901',
            'password' => 'password',
            'role' => 'guest',
        ]);

        // ─── Rooms ───────────────────────────────────
        $rooms = [
            // Superior Rooms
            ['room_number' => '101', 'type' => 'superior', 'price_per_night' => 500000, 'capacity' => 2, 'description' => 'Kamar nyaman dengan pemandangan taman. Dilengkapi AC, TV LED 42", WiFi, dan kamar mandi dengan shower.', 'amenities' => ['AC', 'TV LED 42"', 'WiFi', 'Shower', 'Mini Bar']],
            ['room_number' => '102', 'type' => 'superior', 'price_per_night' => 500000, 'capacity' => 2, 'description' => 'Kamar nyaman dengan desain modern. Dilengkapi fasilitas lengkap untuk kenyamanan Anda.', 'amenities' => ['AC', 'TV LED 42"', 'WiFi', 'Shower', 'Mini Bar']],
            ['room_number' => '103', 'type' => 'superior', 'price_per_night' => 500000, 'capacity' => 2, 'description' => 'Kamar standar dengan fasilitas lengkap dan nyaman.', 'amenities' => ['AC', 'TV LED 42"', 'WiFi', 'Shower', 'Mini Bar']],
            ['room_number' => '104', 'type' => 'superior', 'price_per_night' => 500000, 'capacity' => 2, 'description' => 'Kamar dengan suasana tenang dan nyaman.', 'amenities' => ['AC', 'TV LED 42"', 'WiFi', 'Shower', 'Mini Bar']],

            // Deluxe Rooms
            ['room_number' => '201', 'type' => 'deluxe', 'price_per_night' => 850000, 'capacity' => 2, 'description' => 'Kamar luas dengan pemandangan kota. King bed, bathtub, dan balkon pribadi.', 'amenities' => ['AC', 'TV LED 50"', 'WiFi', 'Bathtub', 'Balkon', 'Mini Bar', 'Coffee Maker']],
            ['room_number' => '202', 'type' => 'deluxe', 'price_per_night' => 850000, 'capacity' => 2, 'description' => 'Kamar deluxe dengan fasilitas premium dan pemandangan indah.', 'amenities' => ['AC', 'TV LED 50"', 'WiFi', 'Bathtub', 'Balkon', 'Mini Bar', 'Coffee Maker']],
            ['room_number' => '203', 'type' => 'deluxe', 'price_per_night' => 850000, 'capacity' => 3, 'description' => 'Kamar deluxe keluarga dengan ruang ekstra.', 'amenities' => ['AC', 'TV LED 50"', 'WiFi', 'Bathtub', 'Balkon', 'Mini Bar', 'Coffee Maker']],

            // Junior Suites
            ['room_number' => '301', 'type' => 'junior_suite', 'price_per_night' => 1500000, 'capacity' => 3, 'description' => 'Suite elegan dengan ruang tamu terpisah, king bed premium, dan pemandangan panorama.', 'amenities' => ['AC', 'TV LED 55"', 'WiFi', 'Jacuzzi', 'Balkon Luas', 'Mini Bar', 'Coffee Maker', 'Living Room', 'Safe Box']],
            ['room_number' => '302', 'type' => 'junior_suite', 'price_per_night' => 1500000, 'capacity' => 3, 'description' => 'Suite mewah dengan sentuhan tradisional Nusantara.', 'amenities' => ['AC', 'TV LED 55"', 'WiFi', 'Jacuzzi', 'Balkon Luas', 'Mini Bar', 'Coffee Maker', 'Living Room', 'Safe Box']],

            // Presidential Suite
            ['room_number' => '401', 'type' => 'presidential_suite', 'price_per_night' => 3500000, 'capacity' => 4, 'description' => 'Pengalaman menginap paling mewah. Suite terluas dengan ruang tamu, ruang makan, dapur kecil, dan pemandangan 360°.', 'amenities' => ['AC', 'TV LED 65"', 'WiFi', 'Jacuzzi', 'Private Pool', 'Balkon Panorama', 'Mini Bar Premium', 'Espresso Machine', 'Living Room', 'Dining Room', 'Kitchenette', 'Butler Service', 'Safe Box']],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
