<?php

namespace App\Http\Controllers;

use App\Enums\GeneratedReportStatus;
use App\Models\GeneratedReport;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function send(Request $request, GeneratedReport $report, TelegramService $telegram): RedirectResponse
    {
        $this->authorize('download', $report);

        abort_if($report->status !== GeneratedReportStatus::Completed, 422, 'Relatório ainda não foi gerado.');

        $caption = "📄 Relatório #{$report->id} — {$report->module->label()} ({$report->format->label()})\n"
            . "Gerado em: " . $report->generated_at?->format('d/m/Y H:i');

        $telegram->sendDocument($report->file_path, $caption);

        return back()->with('toast', ['type' => 'success', 'message' => 'Relatório enviado ao Telegram com sucesso.']);
    }
}
