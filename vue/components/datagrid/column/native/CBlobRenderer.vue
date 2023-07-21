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

function getResult() {
    const text = Object.values(props.value).join(', ')
    if (text.length > 25) {
        return text.substring(0, 25) + '...'
    }
    return text
}

const result = getResult()

</script>

<template>
    <span v-if="result">{{ result }}</span>
    <v-icon v-else icon="mdi-minus" color="grey" size="sm"/>
</template>