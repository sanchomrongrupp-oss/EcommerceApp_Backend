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
        'category_id',
        'title',
        'description',
        'image',
        'price',
        'rating_avg',
        'rating_count',
    ];

    // Cast size and color to array
    protected $casts = [
        'rating_avg' => 'float',
        'rating_count' => 'integer',
    ];

    // Get the category that the product belongs to
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariants::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlists::class);
    }
}