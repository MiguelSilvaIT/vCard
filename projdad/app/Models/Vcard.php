<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Notifications\Notifiable;


class Vcard extends Authenticatable
{
    use HasFactory;
    use SoftDeletes;
    use Notifiable;

    protected $primaryKey = 'phone_number';
    public $incrementing = false;

    protected $casts = [
        'password' => 'hashed',
        'confirmation_code' => 'hashed',
        'custom_options' => 'array',
        'custom_data' => 'array'
    ];

    protected $fillable = [
        'phone_number',
        'name',
        'email',
        'photo_url',
        'password',
        'confirmation_code',
        'custom_data',
        'custom_options',
        'blocked',
        'balance',
        'max_debit'
    ];

    
  
    public function transactions()
    {
        return $this->hasMany(Transaction::class,'vcard');
    }

    public function pairTransactions()
    {
        return $this->hasMany(Transaction::class,'pair_vcard');
    }
    
    public function categories()
    {
        return $this->hasMany(Category::class,'vcard');
    }
}
