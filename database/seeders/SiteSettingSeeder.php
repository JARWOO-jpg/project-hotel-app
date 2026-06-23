<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── Hero Section ──
            ['key' => 'hero_image', 'group' => 'hero', 'label' => 'Foto Banner Utama (Hero Image)', 'type' => 'image', 'value' => null],
            ['key' => 'hero_title', 'group' => 'hero', 'label' => 'Judul Hero', 'type' => 'text', 'value' => 'Selamat Datang di Hotel Nusantara'],
            ['key' => 'hero_subtitle', 'group' => 'hero', 'label' => 'Sub-Judul Hero', 'type' => 'textarea', 'value' => 'Pengalaman menginap mewah di jantung kota dengan sentuhan budaya Nusantara'],

            // ── Deskripsi Hotel ──
            ['key' => 'about_title', 'group' => 'hero', 'label' => 'Judul Tentang Kami', 'type' => 'text', 'value' => 'Tentang Hotel Nusantara'],
            ['key' => 'about_description', 'group' => 'hero', 'label' => 'Deskripsi Tentang Kami', 'type' => 'textarea', 'value' => 'Hotel Nusantara adalah destinasi menginap premium yang memadukan keanggunan modern dengan kehangatan budaya Nusantara.'],
            ['key' => 'about_image', 'group' => 'hero', 'label' => 'Foto Tentang Kami', 'type' => 'image', 'value' => null],

            // ── Footer ──
            ['key' => 'footer_address', 'group' => 'footer', 'label' => 'Alamat Hotel', 'type' => 'textarea', 'value' => 'Jl. Jenderal Sudirman No. 123, Bandung, Jawa Barat 40111'],
            ['key' => 'footer_email', 'group' => 'footer', 'label' => 'Email Resmi', 'type' => 'text', 'value' => 'info@hotelnusantara.com'],
            ['key' => 'footer_whatsapp', 'group' => 'footer', 'label' => 'Nomor WhatsApp', 'type' => 'text', 'value' => '+62 812 3456 7890'],
            ['key' => 'footer_maps_url', 'group' => 'footer', 'label' => 'Link Google Maps', 'type' => 'url', 'value' => 'https://maps.google.com'],
            ['key' => 'footer_copyright', 'group' => 'footer', 'label' => 'Teks Hak Cipta', 'type' => 'text', 'value' => '© 2026 Hotel Nusantara. All rights reserved.'],

            // ── Sosial Media ──
            ['key' => 'social_instagram', 'group' => 'social', 'label' => 'Link Instagram', 'type' => 'url', 'value' => '#'],
            ['key' => 'social_tiktok', 'group' => 'social', 'label' => 'Link TikTok', 'type' => 'url', 'value' => '#'],
            ['key' => 'social_youtube', 'group' => 'social', 'label' => 'Link YouTube', 'type' => 'url', 'value' => '#'],

            // ── Promo / Banner ──
            ['key' => 'promo_active', 'group' => 'promo', 'label' => 'Aktifkan Banner Promo?', 'type' => 'text', 'value' => '0'],
            ['key' => 'promo_title', 'group' => 'promo', 'label' => 'Judul Promo', 'type' => 'text', 'value' => 'Promo Spesial!'],
            ['key' => 'promo_description', 'group' => 'promo', 'label' => 'Deskripsi Promo', 'type' => 'textarea', 'value' => 'Nikmati diskon spesial untuk pemesanan bulan ini.'],
            ['key' => 'promo_image', 'group' => 'promo', 'label' => 'Gambar Banner Promo', 'type' => 'image', 'value' => null],
            ['key' => 'promo_link', 'group' => 'promo', 'label' => 'Link Tujuan Promo', 'type' => 'url', 'value' => ''],

            // ── Fasilitas Section ──
            ['key' => 'facility_title', 'group' => 'facility', 'label' => 'Judul Seksi Fasilitas', 'type' => 'text', 'value' => 'Pengalaman Istimewa'],
            ['key' => 'facility_subtitle', 'group' => 'facility', 'label' => 'Sub-Judul Seksi Fasilitas', 'type' => 'textarea', 'value' => 'Nikmati berbagai fasilitas premium yang kami sediakan untuk kenyamanan Anda selama menginap'],
        ];

        foreach ($settings as $s) {
            SiteSetting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
