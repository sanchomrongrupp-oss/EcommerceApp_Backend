<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupProduct extends Model
{
    protected $fillable = ['title'];
    public function group_products()
    {
        return $this->hasMany(GroupProduct::class);
    }
}
