import { Head, Form, Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AdminUserController from '@/actions/App/Http/Controllers/Admin/UserController';
import type { PaginatedData, User, UserRole } from '@/types';

interface Props {
    users: PaginatedData<User>;
}

const roleLabels: Record<UserRole, string> = {
    admin: 'Admin',
    user: 'Usuário',
};

const roleVariants: Record<UserRole, 'default' | 'secondary'> = {
    admin: 'default',
    user: 'secondary',
};

export default function AdminUsersIndex({ users }: Props) {
    return (
        <>
            <Head title="Usuários" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <Heading title="Usuários" description="Gerencie os usuários do sistema." />
                    <Link href={AdminUserController.create.url()}>
                        <Button>Novo Usuário</Button>
                    </Link>
                </div>

                <div className="rounded-md border">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b bg-muted/50">
                                <th className="px-4 py-3 text-left font-medium">Nome</th>
                                <th className="px-4 py-3 text-left font-medium">E-mail</th>
                                <th className="px-4 py-3 text-left font-medium">Função</th>
                                <th className="px-4 py-3 text-left font-medium">Data de cadastro</th>
                                <th className="px-4 py-3 text-left font-medium">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            {users.data.length === 0 ? (
                                <tr>
                                    <td colSpan={5} className="px-4 py-8 text-center text-muted-foreground">
                                        Nenhum usuário encontrado.
                                    </td>
                                </tr>
                            ) : (
                                users.data.map((user) => (
                                    <tr key={user.id} className="border-b last:border-0 hover:bg-muted/30">
                                        <td className="px-4 py-3 font-medium">{user.name}</td>
                                        <td className="px-4 py-3 text-muted-foreground">{user.email}</td>
                                        <td className="px-4 py-3">
                                            <Badge variant={roleVariants[user.role]}>
                                                {roleLabels[user.role]}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3 text-muted-foreground">
                                            {new Date(user.created_at).toLocaleDateString('pt-BR')}
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="flex items-center gap-2">
                                                <Link href={AdminUserController.edit.url(user)}>
                                                    <Button variant="ghost" size="sm">Editar</Button>
                                                </Link>
                                                <Form {...AdminUserController.destroy.form.delete(user)}>
                                                    {({ processing }) => (
                                                        <button
                                                            type="submit"
                                                            disabled={processing}
                                                            className="rounded px-2 py-1 text-sm text-destructive hover:bg-destructive/10 disabled:opacity-50"
                                                            onClick={(e) => {
                                                                if (!confirm('Tem certeza que deseja excluir este usuário?')) {
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

                {users.links.length > 3 && (
                    <div className="flex items-center justify-center gap-1">
                        {users.links.map((link, index) => (
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

AdminUsersIndex.layout = {
    breadcrumbs: [
        { title: 'Usuários', href: AdminUserController.index.url() },
    ],
};
