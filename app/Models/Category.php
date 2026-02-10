<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// If using MongoDB (jenssegers/laravel-mongodb), use this:
// use MongoDB\Laravel\Eloquent\Model; 
use MongoDB\Laravel\Eloquent\Model; 

class Category extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'categories';

    protected $fillable = [
        'title',
        'status',
    ];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}