<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = [
        'booking_id', 'service_type', 'description',
        'amount', 'quantity', 'total', 'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getServiceTypeLabel(): string
    {
        return match($this->service_type) {
            'room_service' => 'Room Service',
            'laundry' => 'Laundry',
            'spa' => 'Spa & Wellness',
            'transport' => 'Transportasi',
            'other' => 'Lainnya',
            default => $this->service_type,
        };
    }
}
