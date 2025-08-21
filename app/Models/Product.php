<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'regular_price',
        'sale_price',
        'category',
        'stock',
        'image_url',
    ];

    // Accessor for unified price field
    public function getPriceAttribute()
    {
        return $this->sale_price ?? $this->regular_price;
    }
}
