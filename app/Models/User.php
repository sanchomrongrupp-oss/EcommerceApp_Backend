<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'phone',
        'date_of_birth',
        'email',
        'password',
        'role',
        'profile_image'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $appends = ['profile_image_url'];

    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image ? url($this->profile_image) : null;
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }
}