<script setup lang="ts">
import { store } from '@/routes/channel';
import { Form } from '@inertiajs/vue3';
import Button from '../ui/button/Button.vue';
import { Card } from '../ui/card';
import Input from '../ui/input/Input.vue';

defineProps<{
    workspace: {
        id: number;
    };
}>();
</script>

<template>
    <Card class="w-100 p-4">
        <h2 class="text-lg">Add a channel</h2>
        <Form
            class="flex flex-col gap-2"
            :action="store()"
            method="POST"
            resetOnSuccess
            :transform="(data) => ({ ...data, workspace_id: workspace.id })"
            #default="{ errors }"
        >
            <Input id="name" type="text" name="name" placeholder="Name" />
            <p v-if="errors?.name">{{ errors?.name }}</p>
            <Button type="submit" class="cursor-pointer">Add</Button>
        </Form>
    </Card>
</template>
