<?php

namespace App\Enums;

enum GeneratedReportStatus: string
{
    case Pending    = 'pending';
    case Processing = 'processing';
    case Completed  = 'completed';
    case Failed     = 'failed';

    public function label(): string
    {
        return match ($this) {
            GeneratedReportStatus::Pending    => 'Pendente',
            GeneratedReportStatus::Processing => 'Processando',
            GeneratedReportStatus::Completed  => 'Concluído',
            GeneratedReportStatus::Failed     => 'Falhou',
        };
    }
}
