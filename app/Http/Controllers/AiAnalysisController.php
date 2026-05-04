<?php

namespace App\Http\Controllers;

use App\Models\ContractAttachment;
use App\Models\ContractPdf;
use App\Models\GeneratedReport;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AiAnalysisController extends Controller
{
    private const DEFAULT_PROMPT_DOCUMENT = 'Faça um resumo executivo deste documento em português, destacando os pontos principais, valores, datas e partes envolvidas.';

    private const DEFAULT_PROMPT_REPORT = 'Faça um resumo executivo deste relatório em português, destacando os principais dados, tendências e informações relevantes.';

    public function analyzeContractPdf(Request $request, ContractPdf $contractPdf): JsonResponse
    {
        $contractPdf->load('contract');
        $this->authorize('view', $contractPdf->contract);

        abort_if(! Storage::exists($contractPdf->file_path), 404);

        $validated = $request->validate([
            'prompt' => ['sometimes', 'string', 'max:1000'],
        ]);

        $prompt = $validated['prompt'] ?? self::DEFAULT_PROMPT_DOCUMENT;

        try {
            $result = GeminiService::make()->analyze($contractPdf->file_path, $prompt);

            return response()->json(['analysis' => $result]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['error' => 'Não foi possível concluir a análise. Tente novamente.'], 500);
        }
    }

    public function analyzeAttachment(Request $request, ContractAttachment $contractAttachment): JsonResponse
    {
        $contractAttachment->load('contract');
        $this->authorize('view', $contractAttachment->contract);

        abort_if(! Storage::exists($contractAttachment->file_path), 404);

        $validated = $request->validate([
            'prompt' => ['sometimes', 'string', 'max:1000'],
        ]);

        $prompt = $validated['prompt'] ?? self::DEFAULT_PROMPT_DOCUMENT;

        try {
            $result = GeminiService::make()->analyze($contractAttachment->file_path, $prompt);

            return response()->json(['analysis' => $result]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['error' => 'Não foi possível concluir a análise. Tente novamente.'], 500);
        }
    }

    public function analyzeReport(Request $request, GeneratedReport $report): JsonResponse
    {
        $this->authorize('view', $report);

        abort_if($report->status->value !== 'completed' || ! Storage::exists($report->file_path), 404);

        $validated = $request->validate([
            'prompt' => ['sometimes', 'string', 'max:1000'],
        ]);

        $prompt = $validated['prompt'] ?? self::DEFAULT_PROMPT_REPORT;

        try {
            $result = GeminiService::make()->analyze($report->file_path, $prompt);

            return response()->json(['analysis' => $result]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['error' => 'Não foi possível concluir a análise. Tente novamente.'], 500);
        }
    }
}
