<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    channels: NavItem[];
}>();

const page = usePage();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarMenu>
            <SidebarMenuItem key="Dashboard">
                <SidebarMenuButton
                    as-child
                    :is-active="urlIsActive(dashboard().url, page.url)"
                    tooltip="Dashboard"
                >
                    <Link :href="dashboard().url">
                        <span>Workspaces</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Channels</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="channel in channels" :key="channel.title">
                <SidebarMenuButton
                    as-child
                    :is-active="urlIsActive(channel.href, page.url)"
                    :tooltip="channel.title"
                >
                    <Link :href="channel.href">
                        <component :is="channel.icon" />
                        <span>{{ channel.title }}</span>
                        <span class="text-gray-400/50">{{
                            channel.parentName
                        }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
