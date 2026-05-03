import { Head, Form, Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import ReportController from '@/actions/App/Http/Controllers/ReportController';
import type { User } from '@/types';

interface Props {
    users: User[];
}

export default function ReportsCreate({ users }: Props) {
    return (
        <>
            <Head title="Gerar Relatório" />

            <div className="space-y-6">
                <Heading title="Gerar Relatório" description="Configure os parâmetros do relatório." />

                <Form {...ReportController.store.form()}>
                    {({ processing, errors }) => (
                        <div className="space-y-8 max-w-2xl">
                            <div className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="module">Módulo <span className="text-destructive">*</span></Label>
                                    <select
                                        id="module"
                                        name="module"
                                        required
                                        className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    >
                                        <option value="">Selecione um módulo</option>
                                        <option value="contracts">Contratos</option>
                                        <option value="audits">Auditorias</option>
                                    </select>
                                    <InputError message={errors.module} />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="format">Formato <span className="text-destructive">*</span></Label>
                                    <select
                                        id="format"
                                        name="format"
                                        required
                                        className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    >
                                        <option value="">Selecione um formato</option>
                                        <option value="excel">Excel</option>
                                        <option value="pdf">PDF</option>
                                        <option value="csv">CSV</option>
                                    </select>
                                    <InputError message={errors.format} />
                                </div>
                            </div>

                            <div className="space-y-4">
                                <h3 className="text-base font-medium">Filtros <span className="text-sm font-normal text-muted-foreground">(opcionais)</span></h3>

                                <div className="space-y-2">
                                    <Label htmlFor="filters_user_id">Usuário</Label>
                                    <select
                                        id="filters_user_id"
                                        name="filters[user_id]"
                                        className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    >
                                        <option value="">Todos os usuários</option>
                                        {users.map((user) => (
                                            <option key={user.id} value={user.id}>{user.name}</option>
                                        ))}
                                    </select>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="filters_ip_address">Endereço IP</Label>
                                    <Input id="filters_ip_address" name="filters[ip_address]" placeholder="Ex: 192.168.1.1" />
                                </div>

                                <div className="grid grid-cols-2 gap-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="filters_date_from">Data de</Label>
                                        <Input id="filters_date_from" type="date" name="filters[date_from]" />
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="filters_date_to">Data até</Label>
                                        <Input id="filters_date_to" type="date" name="filters[date_to]" />
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="filters_status">Status (contratos)</Label>
                                    <select
                                        id="filters_status"
                                        name="filters[status]"
                                        className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    >
                                        <option value="">Todos</option>
                                        <option value="pending">Pendente</option>
                                        <option value="active">Ativo</option>
                                        <option value="completed">Concluído</option>
                                        <option value="cancelled">Cancelado</option>
                                    </select>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="filters_event">Evento (auditorias)</Label>
                                    <select
                                        id="filters_event"
                                        name="filters[event]"
                                        className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    >
                                        <option value="">Todos</option>
                                        <option value="created">Criado</option>
                                        <option value="updated">Atualizado</option>
                                        <option value="deleted">Excluído</option>
                                        <option value="restored">Restaurado</option>
                                    </select>
                                </div>
                            </div>

                            <div className="flex items-center gap-3">
                                <Button type="submit" disabled={processing}>
                                    Gerar Relatório
                                </Button>
                                <Link href={ReportController.index.url()}>
                                    <Button type="button" variant="outline">Cancelar</Button>
                                </Link>
                            </div>
                        </div>
                    )}
                </Form>
            </div>
        </>
    );
}

ReportsCreate.layout = {
    breadcrumbs: [
        { title: 'Relatórios', href: ReportController.index.url() },
        { title: 'Novo Relatório', href: ReportController.create.url() },
    ],
};
