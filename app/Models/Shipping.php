<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'shipping_address',
        'status',
        'carrier',
        'tracking_number',
        'estimated_delivery_date',
        'delivery_date',
        'shipping_cost',
    ];

    protected $dates = ['estimated_delivery_date', 'delivery_date'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
