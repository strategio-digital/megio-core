<script setup lang="ts">
import { IRow } from '@/saas/api/types/IRow'
import { ISchema } from '@/saas/api/types/ISchema'

defineProps<{
    value: string
    columnIndex: number
    columnName: string
    schema: ISchema
    row: IRow
}>()
</script>

<template>
    {{ value }}
</template>