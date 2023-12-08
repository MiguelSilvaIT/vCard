<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;
    protected $table = 'users';
    protected $casts = [
        'password' => 'hashed',
    ];
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}
