<script setup lang="ts">
import { IRow } from '@/saas/api/types/IRow'
import { IDgAction } from '@/saas/components/datagrid-v2/types/IDgAction'

defineProps<{ row: IRow, rowAction: IDgAction }>()

const emits = defineEmits<{
    (e: 'onRowAction', row: IRow, type: string): void
}>()

</script>

<template>
    <v-list-item @click="emits('onRowAction', row, rowAction.type)">
        <v-list-item-title>{{ rowAction.label }}</v-list-item-title>
    </v-list-item>
</template>
