<?php
namespace App\Models\Sanctum;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use MongoDB\Laravel\Eloquent\Model;

class PersonalAccessToken extends SanctumPersonalAccessToken {
    protected $connection = 'mongodb';
}