<?php

namespace App\Enums;

enum ReportModule: string
{
    case Contracts = 'contracts';
    case Audits    = 'audits';

    public function label(): string
    {
        return match ($this) {
            ReportModule::Contracts => 'Contratos',
            ReportModule::Audits    => 'Auditoria',
        };
    }
}
