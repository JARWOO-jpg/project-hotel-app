<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_code', 'booking_id', 'type', 'amount',
        'payment_method', 'status', 'notes',
        // Midtrans fields
        'midtrans_order_id', 'snap_token', 'snap_redirect_url',
        'payment_channel', 'va_number', 'fraud_status',
        'midtrans_response', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'             => 'decimal:2',
            'midtrans_response'  => 'array',
            'paid_at'            => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($trx) {
            if (empty($trx->transaction_code)) {
                $trx->transaction_code = 'TRX-' . strtoupper(Str::random(10));
            }
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'payment' => 'Pembayaran Lunas',
            'dp_payment' => 'Down Payment',
            'refund' => 'Refund',
            'service_charge' => 'Biaya Layanan',
            'checkout_payment' => 'Pembayaran Check-Out',
            default => $this->type,
        };
    }
}
