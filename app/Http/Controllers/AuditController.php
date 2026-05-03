<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Audit::class);

        $filters = $request->only(['user_id', 'event', 'ip_address', 'date_from', 'date_to', 'module']);

        $moduleMap = [
            'contracts' => \App\Models\Contract::class,
            'users'     => \App\Models\User::class,
        ];

        $audits = Audit::with('user')
            ->when($filters['user_id'] ?? null, fn ($q, $v) => $q->where('user_id', $v))
            ->when($filters['event'] ?? null, fn ($q, $v) => $q->where('event', $v))
            ->when($filters['ip_address'] ?? null, fn ($q, $v) => $q->where('ip_address', $v))
            ->when($filters['date_from'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when(isset($filters['module'], $moduleMap[$filters['module']]),
                fn ($q) => $q->where('auditable_type', $moduleMap[$filters['module']]))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $users = \App\Models\User::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('audits/index', [
            'audits'  => $audits,
            'filters' => $filters,
            'users'   => $users,
        ]);
    }
}
