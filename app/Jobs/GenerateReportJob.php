<?php

namespace App\Jobs;

use App\Models\GeneratedReport;
use App\Services\ReportExportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateReportJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public readonly GeneratedReport $report,
        public readonly array $filters,
    ) {}

    public function handle(ReportExportService $service): void
    {
        $service->generate($this->report, $this->filters);
    }

    public function failed(\Throwable $exception): void
    {
        $this->report->update([
            'status'        => \App\Enums\GeneratedReportStatus::Failed,
            'error_message' => $exception->getMessage(),
        ]);
    }
}
