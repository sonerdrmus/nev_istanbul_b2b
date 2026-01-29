<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'payment_method',
        'bank_account_id',
        'status',
        'total',
        'shipping_method_id',
        'shipping_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
        ];
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'SIP-' . date('Ymd');
        $last = static::where('order_number', 'like', $prefix . '%')->orderByDesc('id')->first();
        $seq = $last ? (int) substr($last->order_number, -4) + 1 : 1;

        return $prefix . '-' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
