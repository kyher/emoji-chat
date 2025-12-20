<script setup lang="ts">
import ChannelController from '@/actions/App/Http/Controllers/ChannelController';
import WorkspaceController from '@/actions/App/Http/Controllers/WorkspaceController';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
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
import { AppPageProps } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';
const page = usePage<AppPageProps>();

const workspaces = computed(() => page.props.workspaces);
const channels = computed(() => page.props.channels);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain
                :workspaces="
                    workspaces.map((workspace) => {
                        return {
                            title: workspace.name,
                            href: WorkspaceController.view(workspace.id).url,
                        };
                    })
                "
                :channels="
                    channels.map((channel) => {
                        return {
                            title: channel.name,
                            href: ChannelController.view(channel.id).url,
                            parentName: channel.workspace,
                        };
                    })
                "
            />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
