<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const PAYMENT_PENDING = 'pending';

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

    public function publicCustomerName(): string
    {
        $initial = mb_substr($this->last_name, 0, 1);

        return trim($this->first_name.' '.($initial !== '' ? $initial.'.' : ''));
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
