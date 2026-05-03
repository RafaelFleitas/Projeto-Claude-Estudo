import { Head, Form, Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import ReportController from '@/actions/App/Http/Controllers/ReportController';
import TelegramController from '@/actions/App/Http/Controllers/TelegramController';
import type { GeneratedReport, PaginatedData } from '@/types';

interface Props {
    reports: PaginatedData<GeneratedReport>;
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

export default function ReportsIndex({ reports }: Props) {
    return (
        <>
            <Head title="Relatórios" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <Heading title="Relatórios" description="Gerencie os relatórios gerados pelo sistema." />
                    <Link href={ReportController.create.url()}>
                        <Button>Novo Relatório</Button>
                    </Link>
                </div>

                <div className="rounded-md border">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b bg-muted/50">
                                <th className="px-4 py-3 text-left font-medium">Módulo</th>
                                <th className="px-4 py-3 text-left font-medium">Formato</th>
                                <th className="px-4 py-3 text-left font-medium">Status</th>
                                <th className="px-4 py-3 text-left font-medium">Gerado por</th>
                                <th className="px-4 py-3 text-left font-medium">Data de criação</th>
                                <th className="px-4 py-3 text-left font-medium">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            {reports.data.length === 0 ? (
                                <tr>
                                    <td colSpan={6} className="px-4 py-8 text-center text-muted-foreground">
                                        Nenhum relatório encontrado.
                                    </td>
                                </tr>
                            ) : (
                                reports.data.map((report) => (
                                    <tr key={report.id} className="border-b last:border-0 hover:bg-muted/30">
                                        <td className="px-4 py-3">{moduleLabels[report.module]}</td>
                                        <td className="px-4 py-3">{formatLabels[report.format]}</td>
                                        <td className="px-4 py-3">
                                            <Badge variant={statusVariants[report.status]}>
                                                {statusLabels[report.status]}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3">{report.generated_by_user?.name ?? '—'}</td>
                                        <td className="px-4 py-3 text-muted-foreground">
                                            {new Date(report.created_at).toLocaleDateString('pt-BR', {
                                                day: '2-digit', month: '2-digit', year: 'numeric',
                                                hour: '2-digit', minute: '2-digit',
                                            })}
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="flex items-center gap-2">
                                                <Link href={ReportController.show.url(report)}>
                                                    <Button variant="ghost" size="sm">Ver</Button>
                                                </Link>
                                                {report.status === 'completed' && (
                                                    <>
                                                        <a
                                                            href={ReportController.download.url(report)}
                                                            className="rounded px-2 py-1 text-sm text-primary hover:bg-primary/10"
                                                        >
                                                            Download
                                                        </a>
                                                        <Form {...TelegramController.send.form.post(report)}>
                                                            {({ processing }) => (
                                                                <button
                                                                    type="submit"
                                                                    disabled={processing}
                                                                    className="rounded px-2 py-1 text-sm text-blue-600 hover:bg-blue-50 disabled:opacity-50"
                                                                >
                                                                    Telegram
                                                                </button>
                                                            )}
                                                        </Form>
                                                    </>
                                                )}
                                                <Form {...ReportController.destroy.form.delete(report)}>
                                                    {({ processing }) => (
                                                        <button
                                                            type="submit"
                                                            disabled={processing}
                                                            className="rounded px-2 py-1 text-sm text-destructive hover:bg-destructive/10 disabled:opacity-50"
                                                            onClick={(e) => {
                                                                if (!confirm('Excluir este relatório?')) {
                                                                    e.preventDefault();
                                                                }
                                                            }}
                                                        >
                                                            Excluir
                                                        </button>
                                                    )}
                                                </Form>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>

                {reports.links.length > 3 && (
                    <div className="flex items-center justify-center gap-1">
                        {reports.links.map((link, index) => (
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

ReportsIndex.layout = {
    breadcrumbs: [
        { title: 'Relatórios', href: ReportController.index.url() },
    ],
};
