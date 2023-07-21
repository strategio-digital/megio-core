<script setup lang="ts">
import { IRow } from '@/saas/api/collections/types/IRow'
import { ISchema } from '@/saas/api/collections/types/ISchema'
import { ISchemaProp } from '@/saas/api/collections/types/ISchemaProp'

const props = defineProps<{
    value: any
    columnIndex: number
    columnSchema: ISchemaProp
    tableSchema: ISchema
    row: IRow
}>()

</script>

<template>
    <span v-if="value">{{ value }}</span>
    <v-icon v-else icon="mdi-minus" color="grey" size="sm"/>
</template>