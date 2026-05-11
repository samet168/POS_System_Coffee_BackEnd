<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemSizePrice extends Model
{

    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'item_size_prices';

    protected $fillable = [
        'item_id',
        'size_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // ==================== Relationships ====================

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }
}