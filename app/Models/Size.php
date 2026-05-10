<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Size extends Model
{
    protected $collection = 'sizes';

    protected $fillable = ['size_name', 'size_code'];

    public function itemSizePrices()
    {
        return $this->hasMany(ItemSizePrice::class, 'size_id');
    }
}