<?php

namespace App\Http\Requests;

use App\Models\GeneratedReport;
use Illuminate\Foundation\Http\FormRequest;

class GenerateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', GeneratedReport::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'module'     => ['required', 'in:contracts,audits'],
            'format'     => ['required', 'in:excel,pdf,csv'],
            'user_id'    => ['nullable', 'integer', 'exists:users,id'],
            'ip_address' => ['nullable', 'ip'],
            'date_from'  => ['nullable', 'date', 'before_or_equal:date_to'],
            'date_to'    => ['nullable', 'date', 'after_or_equal:date_from'],
            'status'     => ['nullable', 'in:pending,active,completed,cancelled'],
            'event'      => ['nullable', 'in:created,updated,deleted,restored'],
        ];
    }
}
