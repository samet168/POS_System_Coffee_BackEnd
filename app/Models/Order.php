<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Order extends Model
{
    protected $collection = 'orders';

    protected $fillable = [
        'order_status_id',
        'discount_id',
        'total_amount',
        'table_number',
        'user_id'
    ];

    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'order_id');
    }

    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function orderItems()
{
    return $this->hasMany(OrderItem::class, 'order_id');
}
}