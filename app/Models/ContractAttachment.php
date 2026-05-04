<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['contract_id', 'uploaded_by', 'file_path', 'file_name', 'original_name', 'mime_type', 'file_size_bytes'])]
class ContractAttachment extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected array $auditInclude = ['contract_id', 'original_name', 'mime_type', 'file_size_bytes'];

    protected array $auditEvents = ['created', 'deleted'];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
