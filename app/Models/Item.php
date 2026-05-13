<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model as MongoModel;

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

    protected $casts = [
        'status' => 'string',
    ];

    // Relationship
    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id', '_id');
    }

    // Search Scope
    public function scopeSearchText($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->whereRaw([
            '$or' => [
                ['name' => ['$regex' => $search, '$options' => 'i']],
            ]
        ]);
    }
}