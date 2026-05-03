<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('contract'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'contrato'         => ['required', 'string', 'max:255'],
            'numero_relatorio' => ['nullable', 'string', 'max:255'],
            'projeto'          => ['nullable', 'string', 'max:255'],
            'task_azure'       => ['nullable', 'string', 'max:255'],
            'nota_fiscal'      => ['nullable', 'string', 'max:255'],
            'valor_total'      => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'status'           => ['required', 'in:pending,active,completed,cancelled'],
        ];
    }
}
