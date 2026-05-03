<?php

namespace App\Http\Controllers;

use App\Models\ContractPdf;
use Inertia\Inertia;
use Inertia\Response;

class ValidationController extends Controller
{
    public function show(string $code): Response
    {
        $contractPdf = ContractPdf::where('validation_code', $code)
            ->with(['contract.user', 'generatedBy'])
            ->firstOrFail();

        return Inertia::render('validate/show', [
            'contract'    => $contractPdf->contract,
            'contractPdf' => $contractPdf,
            'validationUrl' => request()->url(),
        ]);
    }
}
