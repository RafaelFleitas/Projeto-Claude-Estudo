<?php

namespace App\Policies;

use App\Models\GeneratedReport;
use App\Models\User;

class GeneratedReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, GeneratedReport $report): bool
    {
        return $user->isAdmin() || $report->generated_by === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, GeneratedReport $report): bool
    {
        return $user->isAdmin() || $report->generated_by === $user->id;
    }

    public function download(User $user, GeneratedReport $report): bool
    {
        return $user->isAdmin() || $report->generated_by === $user->id;
    }
}
