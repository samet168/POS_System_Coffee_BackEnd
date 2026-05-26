<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'order_items';

    protected $fillable = [
        'order_id',
        'item_id',
        'size_id',
        'ice_level',
        'sugar_level',
        'quantity',
        'unit_price',
        'sub_total',
    ];

    protected $casts = [
        'quantity'    => 'integer',
        'unit_price'  => 'decimal:2',
        'sub_total'   => 'decimal:2',
    ];

    // Constants for Enum
    const ICE_LEVELS = ['low', 'medium', 'high'];
    const SUGAR_LEVELS = ['0%', '25%', '50%', '75%', '100%'];

    // ==================== Relationships ====================

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
        public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }


    // ==================== Auto Calculate Sub Total ====================

    protected static function booted()
    {
        static::saving(function ($orderItem) {
            if ($orderItem->quantity && $orderItem->unit_price) {
                $orderItem->sub_total = $orderItem->quantity * $orderItem->unit_price;
            }
        });
    }
}