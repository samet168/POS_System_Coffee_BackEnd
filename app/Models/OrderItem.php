<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class OrderItem extends Model
{
    protected $collection = 'order_items';

protected $fillable = [
        'order_id', 'item_id', 'size_id', 'ice_level_id', 'sugar_level_id', 
        'quantity', 'unit_price', 'sub_total'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
    public function iceLevel()
    {
        return $this->belongsTo(IceLevel::class, 'ice_level_id');
    }

    public function sugarLevel()
    {
        return $this->belongsTo(SugarLevel::class, 'sugar_level_id');
    }
}
