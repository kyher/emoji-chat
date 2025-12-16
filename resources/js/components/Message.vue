<script setup lang="ts">
import { Message } from '@/types';

defineProps<{
    message: Message;
}>();
</script>
<template>
    <p class="mb-2 flex w-full justify-between rounded bg-gray-900/20 p-2">
        <span>{{ message.content }}</span>
        <span class="text-sm text-gray-400 italic">
            {{ message.user }} -
            {{ message.created_at }}
        </span>
    </p>
</template>
