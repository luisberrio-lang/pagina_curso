<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const PAYMENT_PENDING = 'pending';

    protected $fillable = [
        'order_number', 'public_token', 'checkout_token_hash', 'user_id',
        'first_name', 'last_name', 'email', 'phone',
        'document_type', 'document_number', 'subtotal', 'total', 'currency',
        'status', 'payment_status',
    ];

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
}
