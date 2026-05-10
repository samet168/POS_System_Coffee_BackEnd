<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Item extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'items';

    protected $fillable = [
        'item_category_id',
        'item_status_id',
        'name',
        'image'
    ];

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }

    public function status()
    {
        return $this->belongsTo(ItemStatus::class, 'item_status_id');
    }

    public function sizes()
    {
        return $this->hasMany(ItemSizePrice::class, 'item_id');
    }
    public function ice_options()
    {
        return $this->belongsToMany(IceLevel::class, 'item_ice_options');
    }

    public function sugar_options()
    {
        return $this->belongsToMany(SugarLevel::class, 'item_sugar_options');
    }
}
