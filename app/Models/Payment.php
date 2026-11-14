<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'checkout_items',
        'checkout_batch',
        'order_id',
        'gross_amount',
        'status',
        'creates_account',
        'account_password',
        'credentials_sent_at',
        'midtrans_transaction_id',
        'midtrans_status',
        'payment_type',
        'fraud_status',
        'snap_token',
        'redirect_url',
        'last_notification',
        'paid_at',
        'payment_confirmation_sent_at',
    ];

    protected $hidden = [
        'account_password',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'integer',
            'checkout_items' => 'array',
            'status' => PaymentStatus::class,
            'creates_account' => 'boolean',
            'account_password' => 'encrypted',
            'credentials_sent_at' => 'datetime',
            'last_notification' => 'array',
            'paid_at' => 'datetime',
            'payment_confirmation_sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
