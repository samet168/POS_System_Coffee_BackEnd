<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Invoice extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'invoices';

    protected $fillable = [
        'order_ids', // Array នៃ IDs
        'invoice_no',
        'payment_status_id',
        'payment_type_id',
        'total_amount',
        'total_paid',
        'change_amount'
    ];

    public function order()
    {
        return $this->belongsTo(OrderItem::class, 'order_id');
    }

    public function paymentStatus()
    {
        return $this->belongsTo(PaymentStatus::class, 'payment_status_id');
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }
}
