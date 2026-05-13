<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model as MongoModel;
use App\Models\ItemSizePrice;

class Item extends MongoModel
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'items';

    protected $fillable = [
        'item_category_id',
        'name',
        'image',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id', '_id');
    }
    public function sizePrices()
    {
        return $this->hasMany(ItemSizePrice::class, 'item_id');
    }
}