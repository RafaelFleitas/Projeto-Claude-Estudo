<?php

namespace App\Policies;

use App\Models\User;

class AuditPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function export(User $user): bool
    {
        return $user->isAdmin();
    }
}
