<?php

namespace App\Models;

// IMPORTANT: Change this from Illuminate\Database\Eloquent\Model
use MongoDB\Laravel\Eloquent\Model; 

class Product extends Model
{
    // Explicitly tell the model to use the mongodb connection
    protected $connection = 'mongodb';
    
    // Tell the model which collection to use
    protected $collection = 'products';

    // Define which fields can be filled
    protected $fillable = [
        'title',
        'image',
        'description',
        'size',
        'color',
        'price',
        'category',
        'rating',
        'group_product_id'
    ];

    // Cast size and color to array
    protected $casts = [
        'size' => 'array',
        'color' => 'array',
        'rating' => 'array',
        'group_product_id' => 'array',
    ];

    // Get the group product that owns the product
    public function group()
    {
        return $this->belongsTo(GroupProduct::class, 'group_product_id');
    }
}