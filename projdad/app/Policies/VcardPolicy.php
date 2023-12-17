<?php

namespace App\Policies;

use App\Models\Vcard;
use App\Models\User;

class VcardPolicy
{
    public function viewAny(User $user)
    {
        return $user->user_type == "A";
    }

    public function view(User $user, Vcard $model)
    {
        return $user->user_type == "A" || $user->id == $model->phone_number;
    }
    
    public function update(User $user, Vcard $model)
    {
        return $user->id == $model->phone_number;
    }

    public function delete(User $user, Vcard $model)
    {
        return $user->user_type == "A" || $user->id == $model->phone_number;
    }

    public function block(User $user, Vcard $model)
    {
        return $user->user_type == "A";
    }
}