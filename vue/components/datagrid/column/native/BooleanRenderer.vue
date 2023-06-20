<script setup lang="ts">
import { IRow } from '@/saas/api/types/IRow'
import { ISchema } from '@/saas/api/types/ISchema'
import { ISchemaProp } from '@/saas/api/types/ISchemaProp'

defineProps<{
    value: any
    columnIndex: number
    columnSchema: ISchemaProp
    tableSchema: ISchema
    row: IRow
}>()
</script>

<template>
    <div class="text-center">
        <v-icon icon="mdi-check-circle-outline" color="green" v-if="value === true"/>
        <v-icon icon="mdi-close-circle-outline" color="red" v-if="value === false"/>
    </div>
</template>