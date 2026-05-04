<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractAttachmentController extends Controller
{
    public function store(Request $request, Contract $contract): RedirectResponse
    {
        $this->authorize('uploadAttachment', $contract);

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg', 'max:20480'],
        ]);

        $uploadedFile = $request->file('file');
        $extension = $uploadedFile->guessExtension() ?? $uploadedFile->getClientOriginalExtension();
        $fileName = Str::uuid().'.'.$extension;
        $filePath = $uploadedFile->storeAs('contract-attachments/'.$contract->id, $fileName, 'local');

        ContractAttachment::create([
            'contract_id' => $contract->id,
            'uploaded_by' => $request->user()->id,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'mime_type' => $uploadedFile->getMimeType(),
            'file_size_bytes' => $uploadedFile->getSize(),
        ]);

        return redirect()->route('contracts.show', $contract)
            ->with('toast', ['type' => 'success', 'message' => 'Anexo enviado com sucesso.']);
    }

    public function download(ContractAttachment $contractAttachment): StreamedResponse
    {
        $this->authorize('view', $contractAttachment->contract);

        abort_if(! Storage::disk('local')->exists($contractAttachment->file_path), 404, 'Arquivo não encontrado.');

        return Storage::disk('local')->download($contractAttachment->file_path, $contractAttachment->original_name);
    }

    public function destroy(ContractAttachment $contractAttachment): RedirectResponse
    {
        $this->authorize('deleteAttachment', $contractAttachment->contract);

        $contractId = $contractAttachment->contract_id;
        Storage::disk('local')->delete($contractAttachment->file_path);
        $contractAttachment->delete();

        return redirect()->route('contracts.show', $contractId)
            ->with('toast', ['type' => 'success', 'message' => 'Anexo removido com sucesso.']);
    }
}
