<script setup lang="ts">
import {
    destroy,
    store,
} from '@/actions/App/Http/Controllers/WorkspaceController';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { view } from '@/routes/workspace';
import { Workspace, type BreadcrumbItem } from '@/types';
import { Form, Head, Link } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
defineProps<{
    errors: {
        name: string;
    };
    workspaces: Workspace[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <h1 class="text-2xl">Workspaces</h1>
            <h2 class="text-xl">Add a workspace below</h2>

            <Form
                class="flex w-75 flex-col gap-2"
                :action="store()"
                method="POST"
                resetOnSuccess
            >
                <Input id="name" type="text" name="name" placeholder="Name" />
                <p v-if="errors?.name">{{ errors?.name }}</p>
                <Button type="submit" class="cursor-pointer">Add</Button>
            </Form>

            <hr />
            <h2 class="text-xl">Workspaces</h2>

            <div class="grid grid-cols-3 gap-3">
                <Card
                    v-for="workspace in workspaces"
                    :key="workspace.id"
                    class="no-wrap overflow-hidden p-4 text-nowrap text-ellipsis"
                >
                    {{ workspace.name }}
                    <div class="flex gap-2">
                        <Link
                            :href="view(workspace.id)"
                            class="mt-2 w-25 w-fit cursor-pointer rounded bg-blue-500 p-2 text-white"
                        >
                            View
                        </Link>
                        <Form
                            :action="destroy(workspace.id)"
                            method="DELETE"
                            class="mt-2"
                        >
                            <button
                                type="submit"
                                class="cursor-pointer rounded bg-red-500 p-2 text-white"
                            >
                                Delete
                            </button>
                        </Form>
                    </div>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
