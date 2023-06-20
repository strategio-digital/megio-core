<script setup lang="ts">
import { IRow } from '@/saas/api/types/IRow'
import { ISchema } from '@/saas/api/types/ISchema'
import { ISchemaProp } from '@/saas/api/types/ISchemaProp'
import { IDateTime } from '@/saas/api/types/IDateTime'
import DateHelper from '@/saas/helpers/dateHelper'

const props = defineProps<{
    value: any
    columnIndex: number
    columnSchema: ISchemaProp
    tableSchema: ISchema
    row: IRow
}>()

function getResult(): string {
    if (!props.value) {
        return ''
    }

    const dateTime: IDateTime = {
        date: props.value.date,
        timezone: props.value.timezone,
        timezone_type: props.value.timezone
    }

    // TODO: set formatter in PHP entity attribut
    return DateHelper().toCzDateTime(dateTime)
}

</script>

<template>
    {{ getResult() }}
</template>