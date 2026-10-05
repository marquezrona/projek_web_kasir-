<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'invoice',
        'cashier_id',
        'cashier_name',
        'customer_type',
        'customer_contact',
        'subtotal',
        'discount_percent',
        'discount_amount',
        'tax',
        'other_fee',
        'total',
        'paid',
        'change',
        'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax' => 'decimal:2',
            'other_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'paid' => 'decimal:2',
            'change' => 'decimal:2',
        ];
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
