<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ItemSizePrice extends Model
{
    protected $collection = 'item_size_prices';

    protected $fillable = [
        'item_id',
        'size_id',
        'price'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
}
