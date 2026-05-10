<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class PaymentType extends Model
{
    protected $collection = 'payment_types';

    protected $fillable = ['type_name'];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'payment_type_id');
    }
}
