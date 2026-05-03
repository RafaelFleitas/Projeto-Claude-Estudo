import { Head, Form, Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import ContractController from '@/actions/App/Http/Controllers/ContractController';
import type { Contract } from '@/types';

interface Props {
    contract: Contract;
}

export default function ContractsEdit({ contract }: Props) {
    return (
        <>
            <Head title="Editar Contrato" />

            <div className="space-y-6">
                <Heading title="Editar Contrato" description={`Editando: ${contract.contrato}`} />

                <Form {...ContractController.update.form.patch(contract)}>
                    {({ processing, errors }) => (
                        <div className="space-y-6 max-w-2xl">
                            <div className="space-y-2">
                                <Label htmlFor="contrato">Contrato <span className="text-destructive">*</span></Label>
                                <Input id="contrato" name="contrato" defaultValue={contract.contrato} required />
                                <InputError message={errors.contrato} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="numero_relatorio">Número do Relatório</Label>
                                <Input id="numero_relatorio" name="numero_relatorio" defaultValue={contract.numero_relatorio ?? ''} />
                                <InputError message={errors.numero_relatorio} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="projeto">Projeto</Label>
                                <Input id="projeto" name="projeto" defaultValue={contract.projeto ?? ''} />
                                <InputError message={errors.projeto} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="task_azure">Task Azure</Label>
                                <Input id="task_azure" name="task_azure" defaultValue={contract.task_azure ?? ''} />
                                <InputError message={errors.task_azure} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="nota_fiscal">Nota Fiscal</Label>
                                <Input id="nota_fiscal" name="nota_fiscal" defaultValue={contract.nota_fiscal ?? ''} />
                                <InputError message={errors.nota_fiscal} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="valor_total">Valor Total</Label>
                                <Input id="valor_total" name="valor_total" defaultValue={contract.valor_total ?? ''} />
                                <InputError message={errors.valor_total} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="status">Status</Label>
                                <select
                                    id="status"
                                    name="status"
                                    defaultValue={contract.status}
                                    className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                >
                                    <option value="pending">Pendente</option>
                                    <option value="active">Ativo</option>
                                    <option value="completed">Concluído</option>
                                    <option value="cancelled">Cancelado</option>
                                </select>
                                <InputError message={errors.status} />
                            </div>

                            <div className="flex items-center gap-3">
                                <Button type="submit" disabled={processing}>
                                    Salvar Alterações
                                </Button>
                                <Link href={ContractController.show.url(contract)}>
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

ContractsEdit.layout = {
    breadcrumbs: [
        { title: 'Contratos', href: ContractController.index.url() },
        { title: 'Contrato', href: '' },
        { title: 'Editar', href: '' },
    ],
};
