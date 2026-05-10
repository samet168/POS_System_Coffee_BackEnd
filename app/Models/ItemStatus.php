<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ItemStatus extends Model
{
    protected $collection = 'item_statuses';

    protected $fillable = ['status_name'];

    public function items()
    {
        return $this->hasMany(Item::class, 'item_status_id');
    }
}
