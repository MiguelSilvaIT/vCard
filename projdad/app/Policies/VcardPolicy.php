<?php

namespace App\Policies;

use App\Models\User;

class VcardPolicy
{
    public function __construct()
    {
        //
    }
    public function update_max_debit(User $user)
    {
        return $user->user_type == 'A' ;
    }
    public function view_transactions(User $user, User $model)
    {
        return true;
    }
    
}
