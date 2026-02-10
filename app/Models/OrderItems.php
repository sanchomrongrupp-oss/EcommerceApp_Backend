<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    // Explicitly tell the model to use the mongodb connection
    protected $connection = 'mongodb';
    
    // Tell the model which collection to use
    protected $collection = 'order_items';

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'qty',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariants::class);
    }
}
