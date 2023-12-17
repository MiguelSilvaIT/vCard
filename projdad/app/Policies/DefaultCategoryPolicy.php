<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DefaultCategory;

class DefaultCategoryPolicy
{
    public function viewAny(User $user)
    {
        return $user->user_type == "A";
    }

    public function view(User $user)
    {
        return $user->user_type == "A";
    }

    public function create(User $user)
    {
        return $user->user_type == "A";
    }

    public function update(User $user)
    {
        return $user->user_type == "A";
    }

    public function delete(User $user)
    {
        return $user->user_type == "A";
    }
}
