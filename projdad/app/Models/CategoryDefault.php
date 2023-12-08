<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CategoryDefault extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $timestamps = false;

    protected $table = 'default_categories';

    protected $fillable = [
        'id',
        'type',
        'name',
    ];


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
