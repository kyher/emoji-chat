<script setup lang="ts">
import { destroy, edit, view } from '@/routes/channel';
import { Workspace } from '@/types';
import { Form, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Card from '../ui/card/Card.vue';

defineProps<{
    workspace: Workspace;
}>();
const page = usePage();
const auth = computed(() => page.props.auth);
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
                <Link
                    :href="edit(channel.id)"
                    class="mt-2 w-25 w-fit cursor-pointer rounded bg-yellow-500 p-2 text-white"
                    v-if="channel.owner_id === auth.user.id"
                >
                    Edit
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
