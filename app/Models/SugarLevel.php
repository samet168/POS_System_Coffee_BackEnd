<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class SugarLevel extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'sugar_levels';
    protected $fillable = [
        'name',
        'description'
    ];
    
}
