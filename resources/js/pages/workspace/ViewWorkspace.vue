<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { destroy, store, view } from '@/routes/channel';
import { Workspace, type BreadcrumbItem } from '@/types';
import { Form, Head, Link } from '@inertiajs/vue3';
const { workspace } = defineProps<{
    workspace: Workspace;
    errors: {
        name: string;
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
            <Form
                class="flex w-75 flex-col gap-2"
                :action="store()"
                method="POST"
                resetOnSuccess
                :transform="(data) => ({ ...data, workspace_id: workspace.id })"
            >
                <Input id="name" type="text" name="name" placeholder="Name" />
                <p v-if="errors?.name">{{ errors?.name }}</p>
                <Button type="submit" class="cursor-pointer">Add</Button>
            </Form>
            <hr />
            <h2 class="text-xl">Channels</h2>
            <div class="grid grid-cols-3 gap-3">
                <Card
                    v-for="channel in workspace.channels"
                    :key="channel.id"
                    class="no-wrap overflow-hidden p-4 text-nowrap text-ellipsis"
                >
                    {{ channel.name }}
                    <div class="flex gap-2">
                        <Link
                            :href="view(channel.id)"
                            class="mt-2 w-25 w-fit cursor-pointer rounded bg-blue-500 p-2 text-white"
                        >
                            View
                        </Link>
                        <Form
                            :action="destroy(channel.id)"
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
