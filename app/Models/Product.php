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
        'description',
        'price'
    ];
}