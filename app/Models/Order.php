<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';

    protected $guarded = ['*'];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'public_token';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function publicCustomerName(): string
    {
        if (blank($this->last_name)) {
            $parts = preg_split('/\s+/u', trim($this->first_name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            if (count($parts) < 2) {
                return $parts[0] ?? '';
            }

            return $parts[0].' '.mb_substr($parts[array_key_last($parts)], 0, 1).'.';
        }

        $initial = mb_substr($this->last_name, 0, 1);

        return trim($this->first_name.' '.($initial !== '' ? $initial.'.' : ''));
    }

    public function customerFullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function maskedEmail(): string
    {
        [$local, $domain] = array_pad(explode('@', $this->email, 2), 2, '');
        if ($local === '' || $domain === '') {
            return 'correo protegido';
        }

        return mb_substr($local, 0, 1).str_repeat('*', max(3, mb_strlen($local) - 1)).'@'.$domain;
    }
}
