<script setup lang="ts">
import { IRow } from '@/saas/api/types/IRow'
import { ISchema } from '@/saas/api/types/ISchema'
import { ISchemaProp } from '@/saas/api/types/ISchemaProp'

const props = defineProps<{
    value: any
    columnIndex: number
    columnSchema: ISchemaProp
    tableSchema: ISchema
    row: IRow
}>()

</script>

<template>
    <span>{{ value }}</span>
</template>