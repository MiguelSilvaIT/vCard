<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Transaction;
use App\Models\Category;
use Laravel\Passport\HasApiTokens;

class Vcard extends Authenticatable
{
    use HasFactory,HasApiTokens ;
    use SoftDeletes;
    protected $primaryKey = 'phone_number';
    public $incrementing = false;
    
    protected $fillable = [
        'phone_number',
        'name',
        'email',
        'phone',
        'photo_url',
        'password',
        'confirmation_code',
        'blocked',
        'balance',
        'max_debit',
    ];

    
  
    public function transactions()
    {
        return $this->hasMany(Transaction::class,'vcard');
    }
    
    public function categories()
    {
        return $this->hasMany(Category::class,'vcard');
    }
}
