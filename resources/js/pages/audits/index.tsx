import { Head, Link, router } from '@inertiajs/react';
import { Fragment, useRef, useState } from 'react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuditController from '@/actions/App/Http/Controllers/AuditController';
import type { AuditEntry, PaginatedData, User } from '@/types';

interface Props {
    audits: PaginatedData<AuditEntry>;
    filters: {
        user_id?: string;
        event?: string;
        ip_address?: string;
        date_from?: string;
        date_to?: string;
        module?: string;
    };
    users: User[];
}

const eventLabels: Record<AuditEntry['event'], string> = {
    created: 'Criado',
    updated: 'Atualizado',
    deleted: 'Excluído',
    restored: 'Restaurado',
};

const eventVariants: Record<AuditEntry['event'], 'default' | 'secondary' | 'destructive' | 'outline'> = {
    created: 'default',
    updated: 'secondary',
    deleted: 'destructive',
    restored: 'outline',
};

function simplifyAuditableType(auditableType: string): string {
    return auditableType.replace(/^App\\Models\\/, '');
}

const fieldLabels: Record<string, string> = {
    contrato: 'Contrato',
    numero_relatorio: 'Número Relatório',
    projeto: 'Projeto',
    task_azure: 'Task Azure',
    nota_fiscal: 'Nota Fiscal',
    valor_total: 'Valor Total',
    status: 'Status',
    name: 'Nome',
    email: 'E-mail',
    password: 'Senha',
    created_at: 'Criado em',
    updated_at: 'Atualizado em',
    deleted_at: 'Excluído em',
    user_id: 'Usuário',
    description: 'Descrição',
    start_date: 'Data Início',
    end_date: 'Data Fim',
    due_date: 'Data Vencimento',
};

function formatFieldLabel(key: string): string {
    if (fieldLabels[key]) {
        return fieldLabels[key];
    }
    return key
        .split('_')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

function AuditDetails({ audit }: { audit: AuditEntry }) {
    const { event, old_values, new_values } = audit;

    if (event === 'created') {
        return (
            <div className="space-y-1">
                <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide mb-2">Valores criados</p>
                {new_values && Object.keys(new_values).length > 0 ? (
                    <table className="w-full text-xs">
                        <thead>
                            <tr className="text-muted-foreground">
                                <th className="text-left pr-4 py-1 font-medium w-1/4">Campo</th>
                                <th className="text-left py-1 font-medium">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            {Object.entries(new_values).map(([key, val]) => (
                                <tr key={key} className="border-t border-border/40">
                                    <td className="pr-4 py-1 text-muted-foreground">{formatFieldLabel(key)}</td>
                                    <td className="py-1 text-green-700 dark:text-green-400 font-mono break-all">{String(val ?? '—')}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                ) : (
                    <p className="text-xs text-muted-foreground">Sem dados disponíveis.</p>
                )}
            </div>
        );
    }

    if (event === 'deleted') {
        return (
            <div className="space-y-1">
                <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide mb-2">Valores excluídos</p>
                {old_values && Object.keys(old_values).length > 0 ? (
                    <table className="w-full text-xs">
                        <thead>
                            <tr className="text-muted-foreground">
                                <th className="text-left pr-4 py-1 font-medium w-1/4">Campo</th>
                                <th className="text-left py-1 font-medium">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            {Object.entries(old_values).map(([key, val]) => (
                                <tr key={key} className="border-t border-border/40">
                                    <td className="pr-4 py-1 text-muted-foreground">{formatFieldLabel(key)}</td>
                                    <td className="py-1 text-red-700 dark:text-red-400 font-mono break-all">{String(val ?? '—')}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                ) : (
                    <p className="text-xs text-muted-foreground">Sem dados disponíveis.</p>
                )}
            </div>
        );
    }

    if (event === 'updated') {
        const allKeys = Array.from(new Set([
            ...Object.keys(old_values ?? {}),
            ...Object.keys(new_values ?? {}),
        ]));

        return (
            <div className="space-y-1">
                <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide mb-2">Alterações realizadas</p>
                {allKeys.length > 0 ? (
                    <table className="w-full text-xs">
                        <thead>
                            <tr className="text-muted-foreground">
                                <th className="text-left pr-4 py-1 font-medium w-1/4">Campo</th>
                                <th className="text-left pr-4 py-1 font-medium w-[37.5%]">Antes</th>
                                <th className="text-left py-1 font-medium w-[37.5%]">Depois</th>
                            </tr>
                        </thead>
                        <tbody>
                            {allKeys.map((key) => (
                                <tr key={key} className="border-t border-border/40">
                                    <td className="pr-4 py-1 text-muted-foreground">{formatFieldLabel(key)}</td>
                                    <td className="pr-4 py-1 text-red-700 dark:text-red-400 font-mono break-all">{String(old_values?.[key] ?? '—')}</td>
                                    <td className="py-1 text-green-700 dark:text-green-400 font-mono break-all">{String(new_values?.[key] ?? '—')}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                ) : (
                    <p className="text-xs text-muted-foreground">Sem alterações registradas.</p>
                )}
            </div>
        );
    }

    return <p className="text-xs text-muted-foreground">Sem detalhes disponíveis para este evento.</p>;
}

export default function AuditsIndex({ audits, filters, users }: Props) {
    const [expandedId, setExpandedId] = useState<number | null>(null);
    const userIdRef = useRef<HTMLSelectElement>(null);
    const eventRef = useRef<HTMLSelectElement>(null);
    const moduleRef = useRef<HTMLSelectElement>(null);
    const ipRef = useRef<HTMLInputElement>(null);
    const dateFromRef = useRef<HTMLInputElement>(null);
    const dateToRef = useRef<HTMLInputElement>(null);

    function applyFilters(e: React.FormEvent) {
        e.preventDefault();
        router.get(
            AuditController.index.url(),
            {
                user_id: userIdRef.current?.value ?? '',
                event: eventRef.current?.value ?? '',
                module: moduleRef.current?.value ?? '',
                ip_address: ipRef.current?.value ?? '',
                date_from: dateFromRef.current?.value ?? '',
                date_to: dateToRef.current?.value ?? '',
            },
            { preserveState: true, replace: true },
        );
    }

    return (
        <>
            <Head title="Log de Auditoria" />

            <div className="space-y-6">
                <Heading title="Log de Auditoria" description="Registro de todas as ações realizadas no sistema." />

                <form onSubmit={applyFilters} className="rounded-md border p-4 space-y-4">
                    <div className="grid grid-cols-2 gap-4 md:grid-cols-3">
                        <div className="space-y-1">
                            <Label htmlFor="filter-user">Usuário</Label>
                            <select
                                id="filter-user"
                                ref={userIdRef}
                                defaultValue={filters.user_id ?? ''}
                                className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                <option value="">Todos</option>
                                {users.map((user) => (
                                    <option key={user.id} value={user.id}>{user.name}</option>
                                ))}
                            </select>
                        </div>

                        <div className="space-y-1">
                            <Label htmlFor="filter-event">Evento</Label>
                            <select
                                id="filter-event"
                                ref={eventRef}
                                defaultValue={filters.event ?? ''}
                                className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                <option value="">Todos</option>
                                <option value="created">Criado</option>
                                <option value="updated">Atualizado</option>
                                <option value="deleted">Excluído</option>
                                <option value="restored">Restaurado</option>
                            </select>
                        </div>

                        <div className="space-y-1">
                            <Label htmlFor="filter-module">Módulo</Label>
                            <select
                                id="filter-module"
                                ref={moduleRef}
                                defaultValue={filters.module ?? ''}
                                className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                <option value="">Todos</option>
                                <option value="contracts">Contratos</option>
                                <option value="users">Usuários</option>
                            </select>
                        </div>

                        <div className="space-y-1">
                            <Label htmlFor="filter-ip">Endereço IP</Label>
                            <Input id="filter-ip" ref={ipRef} defaultValue={filters.ip_address ?? ''} placeholder="Ex: 192.168.1.1" />
                        </div>

                        <div className="space-y-1">
                            <Label htmlFor="filter-date-from">Data de</Label>
                            <Input id="filter-date-from" type="date" ref={dateFromRef} defaultValue={filters.date_from ?? ''} />
                        </div>

                        <div className="space-y-1">
                            <Label htmlFor="filter-date-to">Data até</Label>
                            <Input id="filter-date-to" type="date" ref={dateToRef} defaultValue={filters.date_to ?? ''} />
                        </div>
                    </div>

                    <Button type="submit">Aplicar Filtros</Button>
                </form>

                <div className="rounded-md border">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b bg-muted/50">
                                <th className="px-4 py-3 text-left font-medium">Evento</th>
                                <th className="px-4 py-3 text-left font-medium">Módulo</th>
                                <th className="px-4 py-3 text-left font-medium">Registro</th>
                                <th className="px-4 py-3 text-left font-medium">Usuário</th>
                                <th className="px-4 py-3 text-left font-medium">IP</th>
                                <th className="px-4 py-3 text-left font-medium">Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            {audits.data.length === 0 ? (
                                <tr>
                                    <td colSpan={6} className="px-4 py-8 text-center text-muted-foreground">
                                        Nenhum registro de auditoria encontrado.
                                    </td>
                                </tr>
                            ) : (
                                audits.data.map((audit) => {
                                    const isExpanded = expandedId === audit.id;
                                    return (
                                        <Fragment key={audit.id}>
                                            <tr
                                                className="border-b hover:bg-muted/30 cursor-pointer select-none"
                                                onClick={() => setExpandedId(isExpanded ? null : audit.id)}
                                            >
                                                <td className="px-4 py-3">
                                                    <Badge variant={eventVariants[audit.event]}>
                                                        {eventLabels[audit.event]}
                                                    </Badge>
                                                </td>
                                                <td className="px-4 py-3 text-muted-foreground">
                                                    {simplifyAuditableType(audit.auditable_type)}
                                                </td>
                                                <td className="px-4 py-3 font-mono text-xs text-muted-foreground">
                                                    #{audit.auditable_id}
                                                </td>
                                                <td className="px-4 py-3">{audit.user?.name ?? '—'}</td>
                                                <td className="px-4 py-3 font-mono text-xs">{audit.ip_address ?? '—'}</td>
                                                <td className="px-4 py-3 text-muted-foreground">
                                                    <div className="flex items-center justify-between gap-2">
                                                        <span>
                                                            {new Date(audit.created_at).toLocaleDateString('pt-BR', {
                                                                day: '2-digit', month: '2-digit', year: 'numeric',
                                                                hour: '2-digit', minute: '2-digit',
                                                            })}
                                                        </span>
                                                        <span className="text-muted-foreground/50 text-xs">{isExpanded ? '▲' : '▼'}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            {isExpanded && (
                                                <tr className="border-b bg-muted/20">
                                                    <td colSpan={6} className="px-6 py-4">
                                                        <AuditDetails audit={audit} />
                                                    </td>
                                                </tr>
                                            )}
                                        </Fragment>
                                    );
                                })
                            )}
                        </tbody>
                    </table>
                </div>

                {audits.links.length > 3 && (
                    <div className="flex items-center justify-center gap-1">
                        {audits.links.map((link, index) => (
                            link.url ? (
                                <Link
                                    key={index}
                                    href={link.url}
                                    className={`rounded px-3 py-1 text-sm ${
                                        link.active
                                            ? 'bg-primary text-primary-foreground'
                                            : 'hover:bg-muted'
                                    }`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ) : (
                                <span
                                    key={index}
                                    className="rounded px-3 py-1 text-sm text-muted-foreground"
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            )
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}

AuditsIndex.layout = {
    breadcrumbs: [
        { title: 'Log de Auditoria', href: AuditController.index.url() },
    ],
};
