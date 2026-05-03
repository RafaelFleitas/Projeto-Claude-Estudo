<?php

namespace App\Enums;

enum ContractStatus: string
{
    case Pending   = 'pending';
    case Active    = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            ContractStatus::Pending   => 'Pendente',
            ContractStatus::Active    => 'Ativo',
            ContractStatus::Completed => 'Concluído',
            ContractStatus::Cancelled => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            ContractStatus::Pending   => 'yellow',
            ContractStatus::Active    => 'blue',
            ContractStatus::Completed => 'green',
            ContractStatus::Cancelled => 'red',
        };
    }
}
