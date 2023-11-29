<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Vcard;


class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'vcard',
        'type',
        'name',
    ];

    public function vcard()
    {
        return $this->belongsTo(Vcard::class);
    }

    public function getTypeNameAttribute()
    {
        switch ($this->status) {
            case 'C':
                return 'Crédito';
            case 'D':
                return 'Débito';
         
        }
    }
}
