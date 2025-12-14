<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { store } from '@/routes/message';
import { view } from '@/routes/workspace';
import { Channel, Workspace, type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
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

// to do - type emoiji
function onSelectEmoji(emoji: any) {
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
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <h1 class="text-2xl">{{ channel.name }}</h1>
            <div class="flex h-1/10 items-center gap-4 rounded bg-gray-800 p-4">
                <div>
                    <button @click="toggleEmojiPicker">
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
            <div class="h-8/10 rounded bg-gray-800 p-4">
                <p v-for="message in channel.messages">
                    {{ message.content }} - {{ message.user }} at
                    {{ message.created_at }}
                </p>
            </div>
        </div>
    </AppLayout>
</template>
