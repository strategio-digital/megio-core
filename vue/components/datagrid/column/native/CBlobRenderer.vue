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
    {{ result }}
</template>