<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Transaction;

class TransactionPolicy
{
    /**
     * Create a new policy instance.
     */
    //generate crud policies
    public function viewAny(User $user)
    {
        return false;
    }

    public function view(User $user, Transaction $model)
    {
        return $user->user_type == "V" && $user->id == $model->vcard;
    }

    public function create(User $user)
    {
        return $user ? true : false;
    }

    public function update(User $user)
    {
        return $user->user_type == "V";
    }

    public function delete(User $user)
    {
        return false;
    }
}
