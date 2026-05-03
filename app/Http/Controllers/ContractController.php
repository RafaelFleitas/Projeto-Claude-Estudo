<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Contract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContractController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Contract::class);

        $filters   = $request->only(['search', 'status']);
        $contracts = Contract::with('user')
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('contrato', 'like', "%{$s}%")
                ->orWhere('projeto', 'like', "%{$s}%"))
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('contracts/index', [
            'contracts' => $contracts,
            'filters'   => $filters,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Contract::class);

        return Inertia::render('contracts/create');
    }

    public function store(StoreContractRequest $request): RedirectResponse
    {
        $request->user()->contracts()->create($request->validated());

        return redirect()->route('contracts.index')
            ->with('toast', ['type' => 'success', 'message' => 'Contrato criado com sucesso.']);
    }

    public function show(Contract $contract): Response
    {
        $this->authorize('view', $contract);

        $contract->load(['user', 'pdfs.generatedBy']);

        return Inertia::render('contracts/show', [
            'contract' => $contract,
        ]);
    }

    public function edit(Contract $contract): Response
    {
        $this->authorize('update', $contract);

        return Inertia::render('contracts/edit', [
            'contract' => $contract,
        ]);
    }

    public function update(UpdateContractRequest $request, Contract $contract): RedirectResponse
    {
        $contract->update($request->validated());

        return redirect()->route('contracts.show', $contract)
            ->with('toast', ['type' => 'success', 'message' => 'Contrato atualizado com sucesso.']);
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        $this->authorize('delete', $contract);

        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('toast', ['type' => 'success', 'message' => 'Contrato removido com sucesso.']);
    }
}
