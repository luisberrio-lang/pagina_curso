<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class PaymentLink extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';

    protected $guarded = ['*'];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_PAID => 'Pagado',
            self::STATUS_EXPIRED => 'Expirado',
            self::STATUS_CANCELLED => 'Cancelado',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function publicToken(): string
    {
        return Crypt::decryptString($this->token_ciphertext);
    }

    public function publicUrl(): string
    {
        return route('payment-links.show', ['token' => $this->publicToken()]);
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function markExpiredIfNeeded(): void
    {
        if ($this->status === self::STATUS_PENDING && $this->expires_at?->isPast()) {
            $this->forceFill(['status' => self::STATUS_EXPIRED])->save();
        }
    }

    public function canBeCancelled(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
