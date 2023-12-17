<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;

class CategoryPolicy
{
    /**
     * Create a new policy instance.
     */
    //generate crud policies
    public function viewAny(User $user)
    {
        return false;
    }

    public function view(User $user, Category $model)
    {
        return $user->user_type == "V" && $user->id == $model->vcard;
    }

    public function create(User $user)
    {
        return $user->user_type == "V";
    }

    public function update(User $user, Category $model)
    {
        return $user->user_type == "V" && $user->id == $model->vcard;
    }

    public function delete(User $user, Category $model)
    {
        return $user->user_type == "V" && $user->id == $model->vcard;;
    }
}
