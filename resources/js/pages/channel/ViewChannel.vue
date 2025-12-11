<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { view } from '@/routes/workspace';
import { Channel, Workspace, type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
const { workspace, channel } = defineProps<{
    workspace: Workspace;
    channel: Channel;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: workspace.name,
        href: view(workspace.id).url,
    },
    {
        title: channel.name,
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
            <h1 class="text-2xl">{{ channel.name }}</h1>
        </div>
    </AppLayout>
</template>
