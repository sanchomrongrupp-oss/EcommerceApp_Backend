<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    // Explicitly tell the model to use the mongodb connection
    protected $connection = 'mongodb';
    
    // Tell the model which collection to use
    protected $collection = 'cart_items';

    protected $fillable = [
        'cart_id',
        'product_variant_id',
        'qty',
        'price',
    ];

    public function cart()
    {
        return $this->belongsTo(Carts::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariants::class);
    }
}
