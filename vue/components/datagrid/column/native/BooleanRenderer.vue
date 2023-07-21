<script setup lang="ts">
import { IRow } from '@/saas/api/collections/types/IRow'
import { ISchema } from '@/saas/api/collections/types/ISchema'
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
    <div class="text-center">
        <div v-if="value === true || value === false">
            <v-icon icon="mdi-check-circle-outline" color="green" v-if="value === true"/>
            <v-icon icon="mdi-close-circle-outline" color="red" v-if="value === false"/>
        </div>
        <v-icon v-else icon="mdi-minus" color="grey" size="sm"/>
    </div>
</template>