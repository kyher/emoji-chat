import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
    parentName?: string;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    channels: Channel[];
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export type Message = {
    id: number;
    content: string;
    user: string;
    created_at: string;
};

type Role = 'administrator' | 'user';

type ResourceUser = {
    id: number;
    name: string;
    email: string;
    role: string;
};

export type Channel = {
    id: number;
    name: string;
    owner_id: number;
    messages?: Message[];
    workspace: string;
    users: ResourceUser[];
};

export interface Workspace {
    id: number;
    name: string;
    owner_id: number;
    channels?: Channel[];
    users?: ResourceUser[];
}
