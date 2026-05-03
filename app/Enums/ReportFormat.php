<?php

namespace App\Enums;

enum ReportFormat: string
{
    case Excel = 'excel';
    case Pdf   = 'pdf';
    case Csv   = 'csv';

    public function label(): string
    {
        return match ($this) {
            ReportFormat::Excel => 'Excel (.xlsx)',
            ReportFormat::Pdf   => 'PDF',
            ReportFormat::Csv   => 'CSV',
        };
    }

    public function extension(): string
    {
        return match ($this) {
            ReportFormat::Excel => 'xlsx',
            ReportFormat::Pdf   => 'pdf',
            ReportFormat::Csv   => 'csv',
        };
    }

    public function mimeType(): string
    {
        return match ($this) {
            ReportFormat::Excel => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ReportFormat::Pdf   => 'application/pdf',
            ReportFormat::Csv   => 'text/csv',
        };
    }
}
