<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:cancel-expired-bookings';

    /**
     * The console command description.
     */
    protected $description = 'Batalkan booking berstatus pending yang sudah melewati batas waktu pembayaran (15 menit)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Cari semua booking pending yang dibuat lebih dari 15 menit lalu
        $expiredBookings = Booking::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(15))
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('Tidak ada booking expired yang perlu dibatalkan.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($expiredBookings as $booking) {
            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'Otomatis dibatalkan: Batas waktu pembayaran 15 menit terlampaui.',
            ]);

            $count++;

            Log::info("Auto-cancelled expired booking: {$booking->booking_code} " .
                "(Room #{$booking->room_id}, " .
                "Created: {$booking->created_at->format('Y-m-d H:i:s')})");
        }

        $this->info("✅ {$count} booking expired berhasil dibatalkan.");
        return self::SUCCESS;
    }
}
