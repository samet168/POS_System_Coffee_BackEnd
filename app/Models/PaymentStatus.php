<?php

namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;

class PaymentStatus extends Model
{
    protected $collection = 'payment_statuses';

    protected $fillable = ['status_name'];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'payment_status_id');
    }
}
