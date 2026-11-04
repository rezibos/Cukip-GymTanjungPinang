<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'membership_id', 'invoice_number', 'amount',
        'payment_proof', 'status', 'paid_at', 'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'status' => TransactionStatus::class,
            'amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Transaction $transaction) {
            if (empty($transaction->invoice_number)) {
                $transaction->invoice_number = static::generateInvoiceNumber();
            }
        });
    }

    // Format: INV-YYYYMMDD-XXXX (urut per hari, dimulai dari 0001)
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';

        $lastNumber = static::where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $next = $lastNumber ? ((int) substr($lastNumber, -4)) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        return $this->payment_proof ? Storage::disk('public')->url($this->payment_proof) : null;
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp' . number_format($this->amount, 0, ',', '.');
    }
}