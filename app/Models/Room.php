<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'room_number', 'type', 'price_per_night', 'capacity',
        'description', 'image', 'status', 'amenities',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'price_per_night' => 'decimal:2',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'superior' => 'Superior Room',
            'deluxe' => 'Deluxe Room',
            'junior_suite' => 'Junior Suite',
            'presidential_suite' => 'Presidential Suite',
            default => $this->type,
        };
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeByType($query, $type)
    {
        if ($type && $type !== 'all') {
            return $query->where('type', $type);
        }
        return $query;
    }

    /**
     * Check if room is available for specific dates.
     *
     * FIX DOUBLE BOOKING: Status 'pending' sekarang juga dianggap TIDAK tersedia.
     * Booking pending akan otomatis di-cancel oleh Cron Job jika tidak dibayar
     * dalam 15 menit, sehingga kamar kembali tersedia.
     */
    public function isAvailableForDates($checkIn, $checkOut): bool
    {
        if ($this->status === 'maintenance') return false;

        return !$this->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->where(function ($q2) use ($checkIn, $checkOut) {
                    $q2->where('check_in_date', '<', $checkOut)
                        ->where('check_out_date', '>', $checkIn);
                });
            })->exists();
    }

    /**
     * Sama seperti isAvailableForDates tapi menggunakan Pessimistic Locking.
     * Digunakan di dalam DB::transaction() saat proses store booking
     * untuk mencegah Race Condition.
     */
    public function isAvailableForDatesLocked($checkIn, $checkOut): bool
    {
        if ($this->status === 'maintenance') return false;

        return !$this->bookings()
            ->lockForUpdate()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->where(function ($q2) use ($checkIn, $checkOut) {
                    $q2->where('check_in_date', '<', $checkOut)
                        ->where('check_out_date', '>', $checkIn);
                });
            })->exists();
    }
}
