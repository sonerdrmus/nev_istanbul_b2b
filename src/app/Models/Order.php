<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function isAccessibleBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->is_admin) {
            return true;
        }

        if ($this->user_id && (int) $this->user_id === (int) $user->id) {
            return true;
        }

        return filled($this->customer_email)
            && filled($user->email)
            && strcasecmp((string) $this->customer_email, (string) $user->email) === 0;
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'SIP-' . date('Ymd');
        $last = static::where('order_number', 'like', $prefix . '%')->orderByDesc('id')->first();
        $seq = $last ? (int) substr($last->order_number, -4) + 1 : 1;

        return $prefix . '-' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
