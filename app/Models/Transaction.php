<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Vcard;
use App\Models\Category;


class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
      'id',
      'vcard',
      'date',
      'datetime',
      'type',
      'value',
      'old_balance',
      'new_balance',
      'payment_type',
      'payment_reference',
      'pair_transaction',
      'pair_vcard',
      'category_id',
      'description',
    ];

    public function vcard_details()
    {
        return $this->belongsTo(Vcard::class,'vcard');
    }

    public function pair_vcard_details()
    {
        return $this->belongsTo(Vcard::class,'pair_vcard');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
}
