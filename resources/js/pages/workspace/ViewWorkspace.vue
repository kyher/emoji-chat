<script setup lang="ts">
import AddChannelForm from '@/components/Workspace/AddChannelForm.vue';
import AddUser from '@/components/Workspace/AddUser.vue';
import ChannelList from '@/components/Workspace/ChannelList.vue';
import UserList from '@/components/Workspace/UserList.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { Workspace, type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
const { workspace } = defineProps<{
    workspace: Workspace;
    errors: {
        name?: string;
        email?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: workspace.name,
        href: '',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <h1 class="text-2xl">{{ workspace.name }}</h1>
            <hr />
            <AddChannelForm :workspace="workspace" />

            <hr />
            <h2 class="text-xl">Channels</h2>
            <ChannelList :workspace="workspace" />
            <h2 class="text-xl">Users</h2>
            <UserList :workspace="workspace" />
            <AddUser :workspace="workspace" />
        </div>
    </AppLayout>
</template>
