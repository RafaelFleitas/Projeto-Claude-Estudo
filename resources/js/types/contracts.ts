import type { User } from './auth';

export type ContractStatus = 'pending' | 'active' | 'completed' | 'cancelled';

export type Contract = {
    id: number;
    contrato: string;
    numero_relatorio: string | null;
    projeto: string | null;
    task_azure: string | null;
    nota_fiscal: string | null;
    valor_total: string | null;
    status: ContractStatus;
    user_id: number;
    user?: User;
    pdfs?: ContractPdf[];
    attachments?: ContractAttachment[];
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
};

export type ContractAttachment = {
    id: number;
    contract_id: number;
    uploaded_by: number;
    file_path: string;
    file_name: string;
    original_name: string;
    mime_type: string | null;
    file_size_bytes: number | null;
    created_at: string;
    uploaded_by_user?: User;
};

export type ContractPdf = {
    id: number;
    contract_id: number;
    generated_by: number;
    validation_code: string;
    file_path: string;
    file_name: string;
    file_size_bytes: number | null;
    generated_at: string;
    generated_by_user?: User;
};

export type GeneratedReport = {
    id: number;
    generated_by: number;
    module: 'contracts' | 'audits';
    format: 'excel' | 'pdf' | 'csv';
    file_path: string | null;
    file_name: string | null;
    file_size_bytes: number | null;
    filters: Record<string, unknown> | null;
    status: 'pending' | 'processing' | 'completed' | 'failed';
    error_message: string | null;
    generated_at: string | null;
    created_at: string;
    generated_by_user?: User;
};

export type AuditEntry = {
    id: number;
    user_id: number | null;
    user_type: string | null;
    event: 'created' | 'updated' | 'deleted' | 'restored';
    auditable_type: string;
    auditable_id: number;
    old_values: Record<string, unknown> | null;
    new_values: Record<string, unknown> | null;
    url: string | null;
    ip_address: string | null;
    user_agent: string | null;
    created_at: string;
    user?: User;
};

export type PaginatedData<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: Array<{ url: string | null; label: string; active: boolean }>;
};
