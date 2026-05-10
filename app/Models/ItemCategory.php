<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ItemCategory extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'item_categories';

    protected $fillable = ['name'];

    public function items()
    {
        return $this->hasMany(Item::class, 'item_category_id');
    }
}