import { Link, usePage } from '@inertiajs/react';
import { BarChart2, ClipboardList, FileText, LayoutGrid, Users } from 'lucide-react';
import ContractController from '@/actions/App/Http/Controllers/ContractController';
import AuditController from '@/actions/App/Http/Controllers/AuditController';
import ReportController from '@/actions/App/Http/Controllers/ReportController';
import AdminUserController from '@/actions/App/Http/Controllers/Admin/UserController';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { Auth, NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

const gestaoNavItems: NavItem[] = [
    {
        title: 'Contratos',
        href: ContractController.index(),
        icon: FileText,
    },
];

const auditoriaNavItems: NavItem[] = [
    {
        title: 'Log de Auditoria',
        href: AuditController.index(),
        icon: ClipboardList,
    },
    {
        title: 'Relatórios',
        href: ReportController.index(),
        icon: BarChart2,
    },
];

const adminNavItems: NavItem[] = [
    {
        title: 'Usuários',
        href: AdminUserController.index(),
        icon: Users,
    },
];

const footerNavItems: NavItem[] = [];

export function AppSidebar() {
    const { auth } = usePage<{ auth: Auth }>().props;
    const isAdmin = auth.user.role === 'admin';

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} label="Plataforma" />
                <NavMain items={gestaoNavItems} label="Gestão" />
                <NavMain items={auditoriaNavItems} label="Auditoria" />
                {isAdmin && <NavMain items={adminNavItems} label="Admin" />}
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
