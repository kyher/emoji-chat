<script setup lang="ts">
import { editStore } from '@/actions/App/Http/Controllers/ChannelController';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { view } from '@/routes/workspace';
import { BreadcrumbItem, Channel, Workspace } from '@/types';
import { Form, Head } from '@inertiajs/vue3';

const { workspace, channel } = defineProps<{
    channel: Channel;
    workspace: Workspace;
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
    <Head title="Edit Channel" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <h1 class="text-2xl">Edit {{ channel.name }}</h1>
            <Card class="w-100 p-4">
                <Form :action="editStore(channel.id)" method="POST">
                    <Input :placeholder="channel.name" name="name" />
                    <Button type="submit" class="mt-2">Save</Button>
                </Form>
            </Card>
        </div>
    </AppLayout>
</template>
