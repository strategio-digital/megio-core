<script lang="ts" setup>

import { Ref } from 'vue'

const props = defineProps<{
    title: string,
    open: Ref<boolean>
    loading: Ref<boolean>,
    toggleOpen: Function
}>()

const emit = defineEmits<{
    (e: 'accept'): void
    (e: 'close'): void
}>()

function accept () {
    emit('accept')
}

function close () {
    props.toggleOpen('hide')
    emit('close')
}
</script>

<template>
    <v-dialog :model-value="open.value" :max-width="500" scrollable>
        <v-card>
            <v-card-title class="text-h5 mt-3 px-5 pt-5 pb-0">{{ title }}</v-card-title>
            <v-card-text class="py-3 px-5" style="max-height: 300px">
                <slot></slot>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn variant="tonal" color="" @click="close">
                    Zrušit
                </v-btn>
                <v-btn variant="tonal" color="error" :loading="loading.value" @click="accept">
                    Potvrdit
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>