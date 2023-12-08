<?php

namespace App\Policies;

use App\Models\User;

class VcardPolicy
{
    public function update_max_debit(User $user)
    {
        return $user->user_type == 'A' ;
    }
 
 
}
