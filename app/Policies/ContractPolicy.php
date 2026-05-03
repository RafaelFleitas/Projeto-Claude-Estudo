<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Contract $contract): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Contract $contract): bool
    {
        return $user->isAdmin() || $contract->user_id === $user->id;
    }

    public function delete(User $user, Contract $contract): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Contract $contract): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Contract $contract): bool
    {
        return $user->isAdmin();
    }

    public function generatePdf(User $user, Contract $contract): bool
    {
        return true;
    }
}
