import { Head, Form, Link, router } from '@inertiajs/react';
import { useRef } from 'react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import ContractController from '@/actions/App/Http/Controllers/ContractController';
import type { Contract, ContractStatus, PaginatedData } from '@/types';

interface Props {
    contracts: PaginatedData<Contract>;
    filters: { search?: string; status?: string };
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

export default function ContractsIndex({ contracts, filters }: Props) {
    const searchRef = useRef<HTMLInputElement>(null);
    const statusRef = useRef<HTMLSelectElement>(null);

    function applyFilters() {
        router.get(
            ContractController.index.url(),
            {
                search: searchRef.current?.value ?? '',
                status: statusRef.current?.value ?? '',
            },
            { preserveState: true, replace: true },
        );
    }

    function handleKeyDown(e: React.KeyboardEvent) {
        if (e.key === 'Enter') {
            applyFilters();
        }
    }

    return (
        <>
            <Head title="Contratos" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <Heading title="Contratos" description="Gerencie os contratos do sistema." />
                    <Link href={ContractController.create.url()}>
                        <Button>Novo Contrato</Button>
                    </Link>
                </div>

                <div className="flex gap-3">
                    <Input
                        ref={searchRef}
                        placeholder="Buscar contrato..."
                        defaultValue={filters.search ?? ''}
                        onKeyDown={handleKeyDown}
                        className="max-w-xs"
                    />
                    <select
                        ref={statusRef}
                        defaultValue={filters.status ?? ''}
                        onChange={applyFilters}
                        className="rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    >
                        <option value="">Todos os status</option>
                        <option value="pending">Pendente</option>
                        <option value="active">Ativo</option>
                        <option value="completed">Concluído</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                    <Button variant="outline" onClick={applyFilters}>
                        Filtrar
                    </Button>
                </div>

                <div className="rounded-md border">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b bg-muted/50">
                                <th className="px-4 py-3 text-left font-medium">Contrato</th>
                                <th className="px-4 py-3 text-left font-medium">Projeto</th>
                                <th className="px-4 py-3 text-left font-medium">Status</th>
                                <th className="px-4 py-3 text-left font-medium">Valor</th>
                                <th className="px-4 py-3 text-left font-medium">Responsável</th>
                                <th className="px-4 py-3 text-left font-medium">Data</th>
                                <th className="px-4 py-3 text-left font-medium">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            {contracts.data.length === 0 ? (
                                <tr>
                                    <td colSpan={7} className="px-4 py-8 text-center text-muted-foreground">
                                        Nenhum contrato encontrado.
                                    </td>
                                </tr>
                            ) : (
                                contracts.data.map((contract) => (
                                    <tr key={contract.id} className="border-b last:border-0 hover:bg-muted/30">
                                        <td className="px-4 py-3 font-medium">{contract.contrato}</td>
                                        <td className="px-4 py-3 text-muted-foreground">{contract.projeto ?? '—'}</td>
                                        <td className="px-4 py-3">
                                            <Badge variant={statusVariants[contract.status]}>
                                                {statusLabels[contract.status]}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3">{contract.valor_total ?? '—'}</td>
                                        <td className="px-4 py-3">{contract.user?.name ?? '—'}</td>
                                        <td className="px-4 py-3 text-muted-foreground">
                                            {new Date(contract.created_at).toLocaleDateString('pt-BR')}
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="flex items-center gap-2">
                                                <Link href={ContractController.show.url(contract)}>
                                                    <Button variant="ghost" size="sm">Ver</Button>
                                                </Link>
                                                <Link href={ContractController.edit.url(contract)}>
                                                    <Button variant="ghost" size="sm">Editar</Button>
                                                </Link>
                                                <Form {...ContractController.destroy.form.delete(contract)}>
                                                    {({ processing }) => (
                                                        <button
                                                            type="submit"
                                                            disabled={processing}
                                                            className="rounded px-2 py-1 text-sm text-destructive hover:bg-destructive/10 disabled:opacity-50"
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
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>

                {contracts.links.length > 3 && (
                    <div className="flex items-center justify-center gap-1">
                        {contracts.links.map((link, index) => (
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

ContractsIndex.layout = {
    breadcrumbs: [
        {
            title: 'Contratos',
            href: ContractController.index.url(),
        },
    ],
};
