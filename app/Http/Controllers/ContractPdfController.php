<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractPdf;
use App\Services\ContractPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractPdfController extends Controller
{
    public function store(Request $request, Contract $contract, ContractPdfService $service): RedirectResponse
    {
        $this->authorize('generatePdf', $contract);

        $service->generate($contract, $request->user());

        return redirect()->route('contracts.show', $contract)
            ->with('toast', ['type' => 'success', 'message' => 'PDF gerado e arquivado com sucesso.']);
    }

    public function download(ContractPdf $contractPdf): StreamedResponse
    {
        abort_if(!Storage::exists($contractPdf->file_path), 404, 'Arquivo não encontrado.');

        return Storage::download($contractPdf->file_path, $contractPdf->file_name);
    }

    public function destroy(ContractPdf $contractPdf): RedirectResponse
    {
        $this->authorize('delete', $contractPdf->contract);

        $contractId = $contractPdf->contract_id;
        Storage::delete($contractPdf->file_path);
        $contractPdf->delete();

        return redirect()->route('contracts.show', $contractId)
            ->with('toast', ['type' => 'success', 'message' => 'PDF removido com sucesso.']);
    }
}
