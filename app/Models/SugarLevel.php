<?php
namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;

class SugarLevel extends Model {
    protected $fillable = ['level_name'];

    public function items() {
        return $this->belongsToMany(Item::class, null, 'sugar_level_ids', 'item_ids');
    }
}