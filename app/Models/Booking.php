<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $fillable = [
        'booking_code', 'user_id', 'room_id', 'check_in_date', 'check_out_date',
        'actual_check_in', 'actual_check_out', 'guests', 'status', 'identity_photo', 'payment_type',
        'total_price', 'dp_amount', 'paid_amount', 'special_request',
        'cancellation_reason', 'refund_amount',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'actual_check_in' => 'datetime',
            'actual_check_out' => 'datetime',
            'total_price' => 'decimal:2',
            'dp_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = 'HTL-' . strtoupper(Str::random(8));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function getNightsAttribute(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    public function getServicesTotal(): float
    {
        return $this->services()->where('status', '!=', 'cancelled')->sum('total');
    }

    public function getRemainingBalance(): float
    {
        $totalBill = $this->total_price + $this->getServicesTotal();
        return $totalBill - $this->paid_amount;
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'confirmed' => 'badge-info',
            'checked_in' => 'badge-success',
            'checked_out' => 'badge-secondary',
            'cancelled' => 'badge-danger',
            'waiting_list' => 'badge-waiting',
            default => 'badge-secondary',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'confirmed' => 'Terkonfirmasi',
            'checked_in' => 'Check-In',
            'checked_out' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'waiting_list' => 'Waiting List',
            default => $this->status,
        };
    }
}
