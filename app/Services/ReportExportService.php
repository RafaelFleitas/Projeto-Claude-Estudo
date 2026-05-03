<?php

namespace App\Services;

use App\Enums\GeneratedReportStatus;
use App\Enums\ReportFormat;
use App\Enums\ReportModule;
use App\Models\Contract;
use App\Models\GeneratedReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Models\Audit;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportExportService
{
    public function generate(GeneratedReport $report, array $filters): void
    {
        $report->update(['status' => GeneratedReportStatus::Processing]);

        try {
            $data     = $this->fetchData($report->module, $filters);
            $filePath = match ($report->format) {
                ReportFormat::Excel => $this->exportExcel($report, $data),
                ReportFormat::Csv   => $this->exportCsv($report, $data),
                ReportFormat::Pdf   => $this->exportPdf($report, $data),
            };

            $report->update([
                'status'       => GeneratedReportStatus::Completed,
                'file_path'    => $filePath,
                'file_name'    => basename($filePath),
                'file_size_bytes' => Storage::size($filePath),
                'generated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $report->update([
                'status'        => GeneratedReportStatus::Failed,
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function fetchData(ReportModule $module, array $filters): array
    {
        return match ($module) {
            ReportModule::Contracts => $this->fetchContracts($filters),
            ReportModule::Audits    => $this->fetchAudits($filters),
        };
    }

    private function fetchContracts(array $filters): array
    {
        $query = Contract::with('user');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->get()->map(fn (Contract $c) => [
            'ID'               => $c->id,
            'Contrato'         => $c->contrato,
            'Nº Relatório'     => $c->numero_relatorio,
            'Projeto'          => $c->projeto,
            'Task Azure'       => $c->task_azure,
            'Nota Fiscal'      => $c->nota_fiscal,
            'Valor Total'      => $c->valor_total,
            'Status'           => $c->status?->label(),
            'Criado Por'       => $c->user?->name,
            'Criado Em'        => $c->created_at?->format('d/m/Y H:i'),
        ])->toArray();
    }

    private function fetchAudits(array $filters): array
    {
        $query = Audit::with('user');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['ip_address'])) {
            $query->where('ip_address', $filters['ip_address']);
        }
        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->get()->map(fn (Audit $a) => [
            'ID'         => $a->id,
            'Usuário'    => $a->user?->name ?? 'Sistema',
            'Ação'       => $a->event,
            'Modelo'     => class_basename($a->auditable_type),
            'ID Modelo'  => $a->auditable_id,
            'IP'         => $a->ip_address,
            'URL'        => $a->url,
            'Data/Hora'  => $a->created_at?->format('d/m/Y H:i:s'),
        ])->toArray();
    }

    private function buildSpreadsheet(GeneratedReport $report, array $data): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        if (!empty($data)) {
            $headers = array_keys($data[0]);
            $sheet->fromArray([$headers], null, 'A1');
            $sheet->fromArray($data, null, 'A2');
        }

        return $spreadsheet;
    }

    private function exportExcel(GeneratedReport $report, array $data): string
    {
        $spreadsheet = $this->buildSpreadsheet($report, $data);
        $fileName    = "report-{$report->id}-" . now()->format('Y-m-d-His') . '.xlsx';
        $filePath    = "reports/{$fileName}";
        $tmpPath     = sys_get_temp_dir() . '/' . $fileName;

        (new Xlsx($spreadsheet))->save($tmpPath);
        Storage::put($filePath, file_get_contents($tmpPath));
        unlink($tmpPath);

        return $filePath;
    }

    private function exportCsv(GeneratedReport $report, array $data): string
    {
        $spreadsheet = $this->buildSpreadsheet($report, $data);
        $fileName    = "report-{$report->id}-" . now()->format('Y-m-d-His') . '.csv';
        $filePath    = "reports/{$fileName}";
        $tmpPath     = sys_get_temp_dir() . '/' . $fileName;

        $writer = new Csv($spreadsheet);
        $writer->setDelimiter(';');
        $writer->setEnclosure('"');
        $writer->save($tmpPath);
        Storage::put($filePath, file_get_contents($tmpPath));
        unlink($tmpPath);

        return $filePath;
    }

    private function exportPdf(GeneratedReport $report, array $data): string
    {
        $fileName = "report-{$report->id}-" . now()->format('Y-m-d-His') . '.pdf';
        $filePath = "reports/{$fileName}";

        $pdf = Pdf::loadView('pdf.report', [
            'report' => $report,
            'data'   => $data,
        ])->setPaper('a4', 'landscape');

        Storage::put($filePath, $pdf->output());

        return $filePath;
    }
}
