<script setup lang="ts">
import WorkspaceUserController from '@/actions/App/Http/Controllers/WorkspaceUserController';
import { Workspace } from '@/types';
import { Form } from '@inertiajs/vue3';
import Button from '../ui/button/Button.vue';
import Card from '../ui/card/Card.vue';
import Input from '../ui/input/Input.vue';
defineProps<{
    workspace: Workspace;
}>();
</script>

<template>
    <Card class="w-100 p-4">
        <h3 class="text-lg">Add user</h3>
        <Form
            class="flex flex-col gap-2"
            :action="
                WorkspaceUserController.add({
                    workspace: workspace.id,
                })
            "
            method="POST"
            resetOnSuccess
            #default="{ errors }"
        >
            <Input type="text" placeholder="User's email" name="email" />
            <p v-if="errors?.email">{{ errors?.email }}</p>
            <Button
                type="submit"
                class="cursor-pointer bg-blue-500 p-2 text-white hover:bg-blue-600"
                >Submit</Button
            >
        </Form>
    </Card>
</template>
