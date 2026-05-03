import { Head, Form, Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AdminUserController from '@/actions/App/Http/Controllers/Admin/UserController';

export default function AdminUsersCreate() {
    return (
        <>
            <Head title="Novo Usuário" />

            <div className="space-y-6">
                <Heading title="Novo Usuário" description="Preencha os dados para criar um novo usuário." />

                <Form {...AdminUserController.store.form()}>
                    {({ processing, errors }) => (
                        <div className="space-y-6 max-w-2xl">
                            <div className="space-y-2">
                                <Label htmlFor="name">Nome <span className="text-destructive">*</span></Label>
                                <Input id="name" name="name" required autoFocus />
                                <InputError message={errors.name} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="email">E-mail <span className="text-destructive">*</span></Label>
                                <Input id="email" name="email" type="email" required />
                                <InputError message={errors.email} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="password">Senha <span className="text-destructive">*</span></Label>
                                <Input id="password" name="password" type="password" required />
                                <InputError message={errors.password} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="role">Função</Label>
                                <select
                                    id="role"
                                    name="role"
                                    defaultValue="user"
                                    className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                >
                                    <option value="user">Usuário</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <InputError message={errors.role} />
                            </div>

                            <div className="flex items-center gap-3">
                                <Button type="submit" disabled={processing}>
                                    Criar Usuário
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

AdminUsersCreate.layout = {
    breadcrumbs: [
        { title: 'Usuários', href: AdminUserController.index.url() },
        { title: 'Novo Usuário', href: AdminUserController.create.url() },
    ],
};
