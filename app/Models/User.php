<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable;  // រក្សាទុក
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = ['name', 'email', 'password', 'token', 'role', 'image'];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    // User.php
// app/Models/User.php
public function scopeSearchText($query, $search)
{
    if (empty($search)) return $query;

    return $query->whereRaw([
        '$or' => [
            ['name'  => ['$regex' => $search, '$options' => 'i']],
            ['email' => ['$regex' => $search, '$options' => 'i']]
        ]
    ]);
}

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public $timestamps = true;          

    protected $dates = ['created_at', 'updated_at'];
}