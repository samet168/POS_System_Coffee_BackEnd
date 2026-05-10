<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model; 

class Discount extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'discounts';

    protected $fillable = [
        'name',
        'type',
        'value',
        'start_date',
        'end_date'
    ];

    // ⚠️ Relationship នេះប្រហែលមិន work 100% ក្នុង MongoDB
    public function orders()
    {
        return $this->hasMany(Order::class, 'discount_id');
    }
}