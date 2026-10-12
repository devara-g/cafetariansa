<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'payment_reference',
        'gateway_transaction_id',
        'gateway',
        'payment_method',
        'amount',
        'fee',
        'status',
        'snap_token',
        'checkout_url',
        'qr_code_url',
        'qr_string',
        'va_number',
        'paid_at',
        'expires_at',
        'gateway_response',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'fee' => 'integer',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
            'gateway_response' => 'array',
        ];
    }

    /**
     * Order for this payment.
     *
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Webhook / audit logs for this payment.
     *
     * @return HasMany<PaymentLog, $this>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    /**
     * Check if payment is successful/settled.
     */
    public function isSuccess(): bool
    {
        return in_array($this->status, ['paid', 'settled'], true);
    }

    /**
     * Check if payment is still pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Format amount in Rupiah.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp '.number_format($this->amount, 0, ',', '.');
    }
}
