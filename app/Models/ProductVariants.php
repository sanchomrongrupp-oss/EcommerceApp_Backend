<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariants extends Model
{
    // Explicitly tell the model to use the mongodb connection
    protected $connection = 'mongodb';
    
    // Tell the model which collection to use
    protected $collection = 'product_variants';

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'price',
        'stock',
        'sku',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }   

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItems::class);
    }
}
