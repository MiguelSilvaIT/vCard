<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VcardResource extends JsonResource
{
    public static $format = 'default';
    public function toArray($request)
    {
        switch (VcardResource::$format) {
            case 'detailed':   
                return [
                    'phone' => $this->phone_number,
                    'name' => $this->name,
                    'email' => $this->email,
                    'photo' => $this->photo_url,
                    'blocked' => $this->blocked,
                    'balance' => $this->balance,
                    'max_debit' => $this->max_debit,
                    'created_at' => $this->created_at,
                ];
            default:
                return [
                    // simple format
                    'phone' => $this->phone_number,
                    'name' => $this->name,
                    'photo' => $this->photo_url,
                    'balance' => $this->balance,
                ];
        }
    }
}
