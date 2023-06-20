<script setup lang="ts">
import IDatagridAction from '@/saas/components/datagrid/types/IDatagridAction'

defineProps<{ bulkAction: IDatagridAction, count: number }>()

const emits = defineEmits<{
    (e: 'onBulkAction', type: string): void
}>()
</script>

<template>
    <v-list-item :key="bulkAction.type" @click="emits('onBulkAction', bulkAction.type)">
        <v-list-item-title>
            {{ bulkAction.label }} ({{ count }}x)
        </v-list-item-title>
    </v-list-item>
</template>

<style scoped>

</style>