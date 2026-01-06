<script setup lang="ts">
import { editStore } from '@/actions/App/Http/Controllers/WorkspaceController';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import Input from '@/components/ui/input/Input.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { Workspace, type BreadcrumbItem } from '@/types';
import { Form, Head } from '@inertiajs/vue3';
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
            <h1 class="text-2xl">Edit {{ workspace.name }}</h1>
            <Card class="w-100 p-4">
                <Form :action="editStore(workspace.id)" method="POST">
                    <Input
                        type="text"
                        :placeholder="workspace.name"
                        name="name"
                    />
                    <Button type="submit" class="mt-2">Save</Button>
                </Form>
            </Card>
        </div>
    </AppLayout>
</template>
