<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class OrderStatus extends Model
{
    protected $collection = 'order_statuses';

    protected $fillable = ['status_name'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'order_status_id');
    }
}
