<script setup lang="ts">
import Message from '@/components/Message.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { store } from '@/routes/message';
import { view } from '@/routes/workspace';
import { Channel, Workspace, type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import { ref } from 'vue';
import EmojiPicker from 'vue3-emoji-picker';
import 'vue3-emoji-picker/css';
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

const showEmojiPicker = ref(false);

const form = useForm({
    content: '',
    channel_id: channel.id,
});

useEcho(`channels.${channel.id}`, 'MessageSent', (e) => {
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
            <h1 class="text-2xl">{{ channel.name }}</h1>
            <div class="flex h-1/10 items-center gap-4 rounded bg-gray-800 p-4">
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
                class="flex h-7/10 flex-col-reverse overflow-auto rounded bg-gray-800 p-4"
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
