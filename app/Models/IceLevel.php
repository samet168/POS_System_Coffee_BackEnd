<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class IceLevel extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'ice_levels';

    protected $fillable = [
        'name',
        'description'
    ];

    public function items() {
            return $this->belongsToMany(Item::class, null, 'ice_level_ids', 'item_ids');
        }

}
