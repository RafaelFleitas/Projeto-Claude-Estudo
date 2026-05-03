<?php

namespace App\Models;

use Database\Factories\ContractPdfFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['contract_id', 'generated_by', 'validation_code', 'file_path', 'file_name', 'file_size_bytes', 'generated_at'])]
class ContractPdf extends Model
{
    /** @use HasFactory<ContractPdfFactory> */
    use HasFactory;

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }
}
