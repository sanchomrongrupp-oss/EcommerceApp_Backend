<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// If using MongoDB (jenssegers/laravel-mongodb), use this:
// use MongoDB\Laravel\Eloquent\Model; 
use MongoDB\Laravel\Eloquent\Model; 

class GroupProduct extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'group_products';

    protected $fillable = [
        'title',
        'status',
        'parent_id', // Added this
    ];

    // Get the parent of this group
    public function parent()
    {
        return $this->belongsTo(GroupProduct::class, 'parent_id');
    }

    // Get children of this group
    public function children()
    {
        return $this->hasMany(GroupProduct::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'group_product_id');
    }
}