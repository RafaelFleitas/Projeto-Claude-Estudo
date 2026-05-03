import { Head, Form, Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import ReportController from '@/actions/App/Http/Controllers/ReportController';
import TelegramController from '@/actions/App/Http/Controllers/TelegramController';
import type { GeneratedReport } from '@/types';

interface Props {
    report: GeneratedReport;
}

const statusLabels: Record<GeneratedReport['status'], string> = {
    pending: 'Pendente',
    processing: 'Processando',
    completed: 'Concluído',
    failed: 'Falhou',
};

const statusVariants: Record<GeneratedReport['status'], 'default' | 'secondary' | 'outline' | 'destructive'> = {
    pending: 'secondary',
    processing: 'default',
    completed: 'outline',
    failed: 'destructive',
};

const moduleLabels: Record<GeneratedReport['module'], string> = {
    contracts: 'Contratos',
    audits: 'Auditorias',
};

const formatLabels: Record<GeneratedReport['format'], string> = {
    excel: 'Excel',
    pdf: 'PDF',
    csv: 'CSV',
};

function formatBytes(bytes: number | null): string {
    if (bytes === null) return '—';
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

export default function ReportsShow({ report }: Props) {
    return (
        <>
            <Head title={`Relatório — ${moduleLabels[report.module]} (${formatLabels[report.format]})`} />

            <div className="space-y-6">
                <div className="flex items-start justify-between">
                    <Heading
                        title={`${moduleLabels[report.module]} — ${formatLabels[report.format]}`}
                        description="Detalhes do relatório gerado."
                    />
                    <Link href={ReportController.index.url()}>
                        <Button variant="outline">Voltar</Button>
                    </Link>
                </div>

                {report.status === 'failed' && report.error_message && (
                    <div className="rounded-md border border-destructive/50 bg-destructive/10 px-4 py-3 text-sm text-destructive">
                        <strong>Erro:</strong> {report.error_message}
                    </div>
                )}

                {report.status === 'completed' && (
                    <div className="flex gap-3">
                        <a href={ReportController.download.url(report)}>
                            <Button>Download</Button>
                        </a>
                        <Form {...TelegramController.send.form.post(report)}>
                            {({ processing }) => (
                                <Button type="submit" variant="outline" disabled={processing}>
                                    Enviar via Telegram
                                </Button>
                            )}
                        </Form>
                    </div>
                )}

                <div className="rounded-md border">
                    <dl className="divide-y">
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Módulo</dt>
                            <dd className="col-span-2 text-sm">{moduleLabels[report.module]}</dd>
                        </div>
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Formato</dt>
                            <dd className="col-span-2 text-sm">{formatLabels[report.format]}</dd>
                        </div>
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Status</dt>
                            <dd className="col-span-2 text-sm">
                                <Badge variant={statusVariants[report.status]}>
                                    {statusLabels[report.status]}
                                </Badge>
                            </dd>
                        </div>
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Gerado por</dt>
                            <dd className="col-span-2 text-sm">{report.generated_by_user?.name ?? '—'}</dd>
                        </div>
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Criado em</dt>
                            <dd className="col-span-2 text-sm">
                                {new Date(report.created_at).toLocaleDateString('pt-BR', {
                                    day: '2-digit', month: '2-digit', year: 'numeric',
                                    hour: '2-digit', minute: '2-digit',
                                })}
                            </dd>
                        </div>
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Gerado em</dt>
                            <dd className="col-span-2 text-sm">
                                {report.generated_at
                                    ? new Date(report.generated_at).toLocaleDateString('pt-BR', {
                                        day: '2-digit', month: '2-digit', year: 'numeric',
                                        hour: '2-digit', minute: '2-digit',
                                    })
                                    : '—'}
                            </dd>
                        </div>
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Tamanho</dt>
                            <dd className="col-span-2 text-sm">{formatBytes(report.file_size_bytes)}</dd>
                        </div>
                        {report.filters && Object.keys(report.filters).length > 0 && (
                            <div className="grid grid-cols-3 px-4 py-3">
                                <dt className="text-sm font-medium text-muted-foreground">Filtros aplicados</dt>
                                <dd className="col-span-2">
                                    <pre className="rounded bg-muted p-2 text-xs overflow-auto">
                                        {JSON.stringify(report.filters, null, 2)}
                                    </pre>
                                </dd>
                            </div>
                        )}
                    </dl>
                </div>
            </div>
        </>
    );
}

ReportsShow.layout = {
    breadcrumbs: [
        { title: 'Relatórios', href: ReportController.index.url() },
        { title: 'Relatório', href: '' },
    ],
};
