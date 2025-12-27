<script setup lang="ts">
import WorkspaceUserController from '@/actions/App/Http/Controllers/WorkspaceUserController';
import { Workspace } from '@/types';

defineProps<{
    workspace: Workspace;
}>();
</script>
<template>
    <ul>
        <li v-for="value in workspace.users" :key="value.id">
            {{ value.name }} - {{ value.email }}
            <Form
                v-if="workspace.owner_id !== value.id"
                :action="
                    WorkspaceUserController.destroy({
                        workspace: workspace.id,
                        user: value.id,
                    })
                "
                method="DELETE"
                class="inline"
            >
                <button
                    type="submit"
                    class="ml-2 cursor-pointer rounded bg-red-500 p-1 text-white"
                >
                    Remove
                </button>
            </Form>
        </li>
    </ul>
</template>
