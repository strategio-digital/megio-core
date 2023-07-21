<script setup lang="ts">
import { ISchema } from '@/saas/api/collections/types/ISchema'
import { IRow } from '@/saas/api/collections/types/IRow'
import { ISchemaProp } from '@/saas/api/collections/types/ISchemaProp'

defineProps<{
    value: any
    columnIndex: number
    columnSchema: ISchemaProp
    tableSchema: ISchema
    row: IRow
}>()
</script>

<template>
    <strong class="text-red">@unknown_column_type: {{ columnSchema.type }}</strong>
</template>