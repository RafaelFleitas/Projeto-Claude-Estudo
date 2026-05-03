<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\ContractPdf;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractPdfService
{
    public function generate(Contract $contract, User $generatedBy): ContractPdf
    {
        $validationCode = (string) Str::uuid();
        $validationUrl  = route('contract.validate', $validationCode);

        $qrCodeSvg = $this->generateQrCodeSvg($validationUrl);

        $contract->load('user');

        $pdf = Pdf::loadView('pdf.contract', [
            'contract'      => $contract,
            'contractPdf'   => null,
            'validationUrl' => $validationUrl,
            'qrCodeSvg'     => $qrCodeSvg,
        ]);

        $directory = 'reports/contracts';
        $fileName  = "contract-{$contract->id}-{$validationCode}.pdf";
        $filePath  = "{$directory}/{$fileName}";

        Storage::put($filePath, $pdf->output());

        $fileSize = Storage::size($filePath);

        return ContractPdf::create([
            'contract_id'      => $contract->id,
            'generated_by'     => $generatedBy->id,
            'validation_code'  => $validationCode,
            'file_path'        => $filePath,
            'file_name'        => $fileName,
            'file_size_bytes'  => $fileSize,
            'generated_at'     => now(),
        ]);
    }

    private function generateQrCodeSvg(string $url): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd(),
        );

        $writer = new Writer($renderer);

        return $writer->writeString($url);
    }
}
