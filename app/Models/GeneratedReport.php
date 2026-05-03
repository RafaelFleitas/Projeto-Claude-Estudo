<?php

namespace App\Models;

use App\Enums\GeneratedReportStatus;
use App\Enums\ReportFormat;
use App\Enums\ReportModule;
use Database\Factories\GeneratedReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['generated_by', 'module', 'format', 'file_path', 'file_name', 'file_size_bytes', 'filters', 'status', 'error_message', 'generated_at'])]
class GeneratedReport extends Model
{
    /** @use HasFactory<GeneratedReportFactory> */
    use HasFactory;

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    protected function casts(): array
    {
        return [
            'filters'      => 'array',
            'generated_at' => 'datetime',
            'status'       => GeneratedReportStatus::class,
            'module'       => ReportModule::class,
            'format'       => ReportFormat::class,
        ];
    }
}
