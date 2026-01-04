<script setup lang="ts">
import ChannelUserController from '@/actions/App/Http/Controllers/ChannelUserController';
import Message from '@/components/Message.vue';
import Button from '@/components/ui/button/Button.vue';
import {
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { store } from '@/routes/message';
import { view } from '@/routes/workspace';
import { Channel, ResourceUser, Workspace, type BreadcrumbItem } from '@/types';
import { Form, Head, router, useForm, usePage } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import { computed, ref } from 'vue';
import EmojiPicker from 'vue3-emoji-picker';
import 'vue3-emoji-picker/css';
const { workspace, channel } = defineProps<{
    workspace: Workspace;
    channel: Channel;
    availableUsers: ResourceUser[];
}>();

const page = usePage();
const auth = computed(() => page.props.auth);

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

const showEmojiPicker = ref(false);

const form = useForm({
    content: '',
    channel_id: channel.id,
});

useEcho(`channels.${channel.id}`, 'MessageSent', () => {
    router.visit('', {
        preserveState: true,
        preserveScroll: true,
    });
});

function onSelectEmoji(emoji: { i: string }) {
    form.content = emoji.i;
    form.post(store().url, {
        onSuccess: () => {
            form.reset('content');
        },
    });
    toggleEmojiPicker();
}

function toggleEmojiPicker() {
    showEmojiPicker.value = !showEmojiPicker.value;
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex max-h-screen flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex gap-5">
                <h1 class="text-2xl">{{ channel.name }}</h1>
                <Dialog>
                    <DialogTrigger>
                        <Button variant="ghost" class="cursor-pointer"
                            >👤</Button
                        >
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader class="space-y-3">
                            <DialogTitle>Users</DialogTitle>
                        </DialogHeader>
                        <h2 class="text-lg font-medium">Channel Users</h2>
                        <ul>
                            <li v-for="user in channel.users" :key="user.id">
                                {{ user.name }} - {{ user.email }}
                                <Form
                                    :action="
                                        ChannelUserController.destroy({
                                            channel: channel.id,
                                            user: user.id,
                                        })
                                    "
                                    method="delete"
                                    class="ml-2 inline-block"
                                    v-if="
                                        user.id !== channel.owner_id &&
                                        auth.user.id === channel.owner_id
                                    "
                                >
                                    <Button type="submit" class="cursor-pointer"
                                        >Remove</Button
                                    >
                                </Form>
                            </li>
                        </ul>
                        <div v-if="availableUsers.length > 0">
                            <h2 class="text-lg font-medium">Available Users</h2>

                            <ul>
                                <li
                                    v-for="availableUser in availableUsers"
                                    :key="availableUser.id"
                                >
                                    {{ availableUser.name }} -
                                    {{ availableUser.email }}
                                    <Form
                                        :action="
                                            ChannelUserController.add(
                                                channel.id,
                                            )
                                        "
                                        method="post"
                                        :transform="
                                            (data) => ({
                                                ...data,
                                                channel: channel.id,
                                            })
                                        "
                                        class="ml-2 inline-block"
                                    >
                                        <input
                                            type="hidden"
                                            name="user_id"
                                            :value="availableUser.id"
                                        />
                                        <Button
                                            type="submit"
                                            class="cursor-pointer"
                                            >Add</Button
                                        >
                                    </Form>
                                </li>
                            </ul>
                        </div>
                    </DialogContent>
                </Dialog>
            </div>
            <div
                class="flex h-1/10 items-center gap-4 rounded bg-gray-800/50 p-4"
            >
                <div>
                    <button
                        @click="toggleEmojiPicker"
                        class="cursor-pointer text-2xl"
                    >
                        {{ showEmojiPicker ? '⬇️' : '😀' }}
                    </button>
                    <EmojiPicker
                        v-if="showEmojiPicker"
                        :native="true"
                        @select="onSelectEmoji"
                        theme="dark"
                        class="absolute"
                    />
                    <form>
                        <input v.model="form.content" readonly hidden />
                    </form>
                </div>
            </div>
            <div
                class="flex h-7/10 flex-col-reverse overflow-auto rounded bg-gray-800/50 p-4"
            >
                <Message
                    v-for="message in channel.messages"
                    :key="message.id"
                    :message="message"
                />
            </div>
        </div>
    </AppLayout>
</template>
