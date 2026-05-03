import { Head, Link } from '@inertiajs/react';
import { AlertCircle, CheckCircle2, Clock, FileText, TrendingUp, XCircle } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import ContractController from '@/actions/App/Http/Controllers/ContractController';
import { dashboard } from '@/routes';
import type { Contract, ContractStatus } from '@/types';

interface Stats {
    total: number;
    pending: number;
    active: number;
    completed: number;
    cancelled: number;
    totalValue: string;
}

interface MonthlyData {
    month: string;
    count: number;
    total: string | null;
}

interface Props {
    stats: Stats;
    recentContracts: Contract[];
    monthlyData: MonthlyData[];
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

function StatCard({
    label,
    value,
    icon: Icon,
    color,
    description,
}: {
    label: string;
    value: string | number;
    icon: React.ElementType;
    color: string;
    description?: string;
}) {
    return (
        <div className="rounded-xl border bg-card p-5 shadow-sm">
            <div className="flex items-center justify-between">
                <p className="text-sm font-medium text-muted-foreground">{label}</p>
                <div className={`rounded-lg p-2 ${color}`}>
                    <Icon className="size-4 text-white" />
                </div>
            </div>
            <p className="mt-3 text-3xl font-bold tracking-tight">{value}</p>
            {description && <p className="mt-1 text-xs text-muted-foreground">{description}</p>}
        </div>
    );
}

export default function Dashboard({ stats, recentContracts, monthlyData }: Props) {
    const maxCount = Math.max(...monthlyData.map((m) => m.count), 1);

    return (
        <>
            <Head title="Dashboard" />

            <div className="flex flex-col gap-6 p-4">
                {/* Stat cards */}
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                    <div className="xl:col-span-2">
                        <StatCard
                            label="Total de Contratos"
                            value={stats.total}
                            icon={FileText}
                            color="bg-blue-600"
                            description="todos os contratos"
                        />
                    </div>
                    <StatCard
                        label="Pendentes"
                        value={stats.pending}
                        icon={Clock}
                        color="bg-amber-500"
                    />
                    <StatCard
                        label="Ativos"
                        value={stats.active}
                        icon={TrendingUp}
                        color="bg-green-600"
                    />
                    <StatCard
                        label="Concluídos"
                        value={stats.completed}
                        icon={CheckCircle2}
                        color="bg-sky-600"
                    />
                    <StatCard
                        label="Cancelados"
                        value={stats.cancelled}
                        icon={XCircle}
                        color="bg-red-600"
                    />
                </div>

                {/* Value card */}
                <div className="rounded-xl border bg-gradient-to-br from-blue-700 to-cyan-600 p-5 shadow-sm text-white">
                    <p className="text-sm font-medium opacity-80">Valor Total em Contratos</p>
                    <p className="mt-2 text-4xl font-bold tracking-tight">R$ {stats.totalValue}</p>
                    <p className="mt-1 text-xs opacity-60">soma de todos os valores cadastrados</p>
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    {/* Monthly bar chart */}
                    <div className="rounded-xl border bg-card p-5 shadow-sm">
                        <h2 className="mb-4 text-base font-semibold">Contratos por Mês (últimos 6 meses)</h2>
                        {monthlyData.length === 0 ? (
                            <div className="flex h-40 items-center justify-center text-sm text-muted-foreground">
                                <AlertCircle className="mr-2 size-4" />
                                Nenhum contrato registrado ainda.
                            </div>
                        ) : (
                            <div className="flex h-40 items-end gap-3">
                                {monthlyData.map((item) => {
                                    const height = Math.round((item.count / maxCount) * 100);
                                    const [year, month] = item.month.split('-');
                                    const label = new Date(`${year}-${month}-01`).toLocaleDateString('pt-BR', {
                                        month: 'short',
                                        year: '2-digit',
                                    });
                                    return (
                                        <div key={item.month} className="flex flex-1 flex-col items-center gap-1">
                                            <span className="text-xs font-medium text-muted-foreground">{item.count}</span>
                                            <div className="w-full rounded-t-md bg-blue-600" style={{ height: `${height}%` }} />
                                            <span className="text-[10px] text-muted-foreground">{label}</span>
                                        </div>
                                    );
                                })}
                            </div>
                        )}
                    </div>

                    {/* Status breakdown */}
                    <div className="rounded-xl border bg-card p-5 shadow-sm">
                        <h2 className="mb-4 text-base font-semibold">Distribuição por Status</h2>
                        <div className="space-y-3">
                            {(
                                [
                                    { key: 'active', label: 'Ativos', color: 'bg-green-500' },
                                    { key: 'pending', label: 'Pendentes', color: 'bg-amber-400' },
                                    { key: 'completed', label: 'Concluídos', color: 'bg-sky-500' },
                                    { key: 'cancelled', label: 'Cancelados', color: 'bg-red-400' },
                                ] as const
                            ).map(({ key, label, color }) => {
                                const count = stats[key];
                                const pct = stats.total > 0 ? Math.round((count / stats.total) * 100) : 0;
                                return (
                                    <div key={key}>
                                        <div className="mb-1 flex justify-between text-sm">
                                            <span className="text-muted-foreground">{label}</span>
                                            <span className="font-medium">
                                                {count} <span className="text-xs text-muted-foreground">({pct}%)</span>
                                            </span>
                                        </div>
                                        <div className="h-2 w-full rounded-full bg-muted">
                                            <div
                                                className={`h-2 rounded-full ${color} transition-all`}
                                                style={{ width: `${pct}%` }}
                                            />
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </div>

                {/* Recent contracts */}
                <div className="rounded-xl border bg-card shadow-sm">
                    <div className="flex items-center justify-between border-b px-5 py-4">
                        <h2 className="text-base font-semibold">Últimos Contratos</h2>
                        <Link
                            href={ContractController.index.url()}
                            className="text-sm text-blue-600 hover:underline"
                        >
                            Ver todos
                        </Link>
                    </div>
                    {recentContracts.length === 0 ? (
                        <div className="flex h-32 items-center justify-center text-sm text-muted-foreground">
                            <AlertCircle className="mr-2 size-4" />
                            Nenhum contrato cadastrado ainda.
                        </div>
                    ) : (
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b bg-muted/30 text-xs uppercase text-muted-foreground">
                                    <th className="px-5 py-3 text-left">Contrato</th>
                                    <th className="px-5 py-3 text-left">Projeto</th>
                                    <th className="px-5 py-3 text-left">Status</th>
                                    <th className="px-5 py-3 text-right">Valor</th>
                                    <th className="px-5 py-3 text-right">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                {recentContracts.map((contract) => (
                                    <tr key={contract.id} className="border-b last:border-0 hover:bg-muted/20">
                                        <td className="px-5 py-3 font-medium">
                                            <Link
                                                href={ContractController.show.url(contract)}
                                                className="hover:text-blue-600 hover:underline"
                                            >
                                                {contract.contrato}
                                            </Link>
                                        </td>
                                        <td className="px-5 py-3 text-muted-foreground">{contract.projeto ?? '—'}</td>
                                        <td className="px-5 py-3">
                                            <Badge variant={statusVariants[contract.status]}>
                                                {statusLabels[contract.status]}
                                            </Badge>
                                        </td>
                                        <td className="px-5 py-3 text-right text-muted-foreground">
                                            {contract.valor_total
                                                ? `R$ ${Number(contract.valor_total).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`
                                                : '—'}
                                        </td>
                                        <td className="px-5 py-3 text-right text-muted-foreground">
                                            {new Date(contract.created_at).toLocaleDateString('pt-BR')}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
};
