import { Head, Form, Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AdminUserController from '@/actions/App/Http/Controllers/Admin/UserController';
import type { UserRole } from '@/types';

interface Props {
    user: { id: number; name: string; email: string; role: UserRole };
}

export default function AdminUsersEdit({ user }: Props) {
    return (
        <>
            <Head title="Editar Usuário" />

            <div className="space-y-6">
                <Heading title="Editar Usuário" description={`Editando: ${user.name}`} />

                <Form {...AdminUserController.update.form.patch(user)}>
                    {({ processing, errors }) => (
                        <div className="space-y-6 max-w-2xl">
                            <div className="space-y-2">
                                <Label htmlFor="name">Nome <span className="text-destructive">*</span></Label>
                                <Input id="name" name="name" defaultValue={user.name} required />
                                <InputError message={errors.name} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="email">E-mail <span className="text-destructive">*</span></Label>
                                <Input id="email" name="email" type="email" defaultValue={user.email} required />
                                <InputError message={errors.email} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="role">Função</Label>
                                <select
                                    id="role"
                                    name="role"
                                    defaultValue={user.role}
                                    className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                >
                                    <option value="user">Usuário</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <InputError message={errors.role} />
                            </div>

                            <div className="flex items-center gap-3">
                                <Button type="submit" disabled={processing}>
                                    Salvar Alterações
                                </Button>
                                <Link href={AdminUserController.index.url()}>
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

AdminUsersEdit.layout = {
    breadcrumbs: [
        { title: 'Usuários', href: AdminUserController.index.url() },
        { title: 'Usuário', href: '' },
        { title: 'Editar', href: '' },
    ],
};
