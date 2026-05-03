<?php

namespace App\Http\Controllers;

use App\Enums\GeneratedReportStatus;
use App\Http\Requests\GenerateReportRequest;
use App\Jobs\GenerateReportJob;
use App\Models\GeneratedReport;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', GeneratedReport::class);

        $reports = GeneratedReport::with('generatedBy')
            ->when(!$request->user()->isAdmin(),
                fn ($q) => $q->where('generated_by', $request->user()->id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('reports/index', [
            'reports' => $reports,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', GeneratedReport::class);

        $users = User::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('reports/create', [
            'users' => $users,
        ]);
    }

    public function store(GenerateReportRequest $request): RedirectResponse
    {
        $report = GeneratedReport::create([
            'generated_by' => $request->user()->id,
            'module'       => $request->validated('module'),
            'format'       => $request->validated('format'),
            'filters'      => $request->only(['user_id', 'ip_address', 'date_from', 'date_to', 'status', 'event']),
            'status'       => GeneratedReportStatus::Pending,
        ]);

        GenerateReportJob::dispatch($report, $request->only(['user_id', 'ip_address', 'date_from', 'date_to', 'status', 'event']));

        return redirect()->route('reports.index')
            ->with('toast', ['type' => 'success', 'message' => 'Relatório em geração. Aguarde a conclusão.']);
    }

    public function show(GeneratedReport $report): Response
    {
        $this->authorize('view', $report);

        $report->load('generatedBy');

        return Inertia::render('reports/show', [
            'report' => $report,
        ]);
    }

    public function download(GeneratedReport $report): StreamedResponse
    {
        $this->authorize('download', $report);

        abort_if($report->status !== GeneratedReportStatus::Completed, 404, 'Relatório ainda não está disponível.');
        abort_if(!Storage::exists($report->file_path), 404, 'Arquivo não encontrado.');

        return Storage::download($report->file_path, $report->file_name);
    }

    public function destroy(GeneratedReport $report): RedirectResponse
    {
        $this->authorize('delete', $report);

        if ($report->file_path && Storage::exists($report->file_path)) {
            Storage::delete($report->file_path);
        }

        $report->delete();

        return redirect()->route('reports.index')
            ->with('toast', ['type' => 'success', 'message' => 'Relatório removido com sucesso.']);
    }
}
