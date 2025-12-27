<script setup lang="ts">
import { destroy, view } from '@/routes/channel';
import { Workspace } from '@/types';
import { Form, Link } from '@inertiajs/vue3';
import Card from '../ui/card/Card.vue';

defineProps<{
    workspace: Workspace;
}>();
</script>

<template>
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
</template>
