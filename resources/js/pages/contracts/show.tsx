import { Head, Form, Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import ContractController from '@/actions/App/Http/Controllers/ContractController';
import ContractPdfController from '@/actions/App/Http/Controllers/ContractPdfController';
import type { Contract, ContractStatus } from '@/types';

interface Props {
    contract: Contract;
}

const statusLabels: Record<ContractStatus, string> = {
    pending: 'Pendente',
    active: 'Ativo',
    completed: 'Concluído',
    cancelled: 'Cancelado',
};

const statusVariants: Record<ContractStatus, 'default' | 'secondary' | 'outline' | 'destructive'> = {
    pending: 'secondary',
    active: 'default',
    completed: 'outline',
    cancelled: 'destructive',
};

export default function ContractsShow({ contract }: Props) {
    return (
        <>
            <Head title={contract.contrato} />

            <div className="space-y-8">
                <div className="flex items-start justify-between">
                    <Heading title={contract.contrato} description="Detalhes do contrato" />
                    <div className="flex items-center gap-2">
                        <Link href={ContractController.edit.url(contract)}>
                            <Button variant="outline">Editar</Button>
                        </Link>
                        <Form {...ContractController.destroy.form.delete(contract)}>
                            {({ processing }) => (
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="rounded-md border border-destructive px-4 py-2 text-sm text-destructive hover:bg-destructive/10 disabled:opacity-50"
                                    onClick={(e) => {
                                        if (!confirm('Tem certeza que deseja excluir este contrato?')) {
                                            e.preventDefault();
                                        }
                                    }}
                                >
                                    Excluir
                                </button>
                            )}
                        </Form>
                    </div>
                </div>

                <div className="rounded-md border">
                    <dl className="divide-y">
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Contrato</dt>
                            <dd className="col-span-2 text-sm">{contract.contrato}</dd>
                        </div>
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Número do Relatório</dt>
                            <dd className="col-span-2 text-sm">{contract.numero_relatorio ?? '—'}</dd>
                        </div>
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Projeto</dt>
                            <dd className="col-span-2 text-sm">{contract.projeto ?? '—'}</dd>
                        </div>
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Task Azure</dt>
                            <dd className="col-span-2 text-sm">{contract.task_azure ?? '—'}</dd>
                        </div>
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Nota Fiscal</dt>
                            <dd className="col-span-2 text-sm">{contract.nota_fiscal ?? '—'}</dd>
                        </div>
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Valor Total</dt>
                            <dd className="col-span-2 text-sm">{contract.valor_total ?? '—'}</dd>
                        </div>
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Status</dt>
                            <dd className="col-span-2 text-sm">
                                <Badge variant={statusVariants[contract.status]}>
                                    {statusLabels[contract.status]}
                                </Badge>
                            </dd>
                        </div>
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Responsável</dt>
                            <dd className="col-span-2 text-sm">{contract.user?.name ?? '—'}</dd>
                        </div>
                        <div className="grid grid-cols-3 px-4 py-3">
                            <dt className="text-sm font-medium text-muted-foreground">Criado em</dt>
                            <dd className="col-span-2 text-sm">
                                {new Date(contract.created_at).toLocaleDateString('pt-BR', {
                                    day: '2-digit', month: '2-digit', year: 'numeric',
                                    hour: '2-digit', minute: '2-digit',
                                })}
                            </dd>
                        </div>
                    </dl>
                </div>

                <section className="space-y-4">
                    <div className="flex items-center justify-between">
                        <h3 className="text-lg font-semibold">PDFs Gerados</h3>
                        <Form {...ContractPdfController.store.form(contract)}>
                            {({ processing }) => (
                                <Button type="submit" disabled={processing}>
                                    Gerar Novo PDF
                                </Button>
                            )}
                        </Form>
                    </div>

                    <div className="rounded-md border">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b bg-muted/50">
                                    <th className="px-4 py-3 text-left font-medium">Arquivo</th>
                                    <th className="px-4 py-3 text-left font-medium">Gerado em</th>
                                    <th className="px-4 py-3 text-left font-medium">Gerado por</th>
                                    <th className="px-4 py-3 text-left font-medium">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                {!contract.pdfs || contract.pdfs.length === 0 ? (
                                    <tr>
                                        <td colSpan={4} className="px-4 py-8 text-center text-muted-foreground">
                                            Nenhum PDF gerado ainda.
                                        </td>
                                    </tr>
                                ) : (
                                    contract.pdfs.map((pdf) => (
                                        <tr key={pdf.id} className="border-b last:border-0 hover:bg-muted/30">
                                            <td className="px-4 py-3">{pdf.file_name}</td>
                                            <td className="px-4 py-3 text-muted-foreground">
                                                {new Date(pdf.generated_at).toLocaleDateString('pt-BR', {
                                                    day: '2-digit', month: '2-digit', year: 'numeric',
                                                    hour: '2-digit', minute: '2-digit',
                                                })}
                                            </td>
                                            <td className="px-4 py-3">{pdf.generated_by_user?.name ?? '—'}</td>
                                            <td className="px-4 py-3">
                                                <div className="flex items-center gap-2">
                                                    <a
                                                        href={ContractPdfController.download.url(pdf)}
                                                        className="rounded px-2 py-1 text-sm text-primary hover:bg-primary/10"
                                                    >
                                                        Download
                                                    </a>
                                                    <Form {...ContractPdfController.destroy.form.delete(pdf)}>
                                                        {({ processing }) => (
                                                            <button
                                                                type="submit"
                                                                disabled={processing}
                                                                className="rounded px-2 py-1 text-sm text-destructive hover:bg-destructive/10 disabled:opacity-50"
                                                                onClick={(e) => {
                                                                    if (!confirm('Excluir este PDF?')) {
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
                </section>
            </div>
        </>
    );
}

ContractsShow.layout = {
    breadcrumbs: [
        { title: 'Contratos', href: ContractController.index.url() },
        { title: 'Contrato', href: '' },
    ],
};
