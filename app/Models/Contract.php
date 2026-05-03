<?php

namespace App\Models;

use App\Enums\ContractStatus;
use Database\Factories\ContractFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['contrato', 'numero_relatorio', 'projeto', 'task_azure', 'nota_fiscal', 'valor_total', 'status', 'user_id'])]
class Contract extends Model implements Auditable
{
    /** @use HasFactory<ContractFactory> */
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected array $auditInclude = ['contrato', 'numero_relatorio', 'projeto', 'task_azure', 'nota_fiscal', 'valor_total', 'status'];

    protected array $auditEvents = ['created', 'updated', 'deleted', 'restored'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pdfs(): HasMany
    {
        return $this->hasMany(ContractPdf::class);
    }

    protected function casts(): array
    {
        return [
            'valor_total' => 'decimal:2',
            'status'      => ContractStatus::class,
        ];
    }
}
