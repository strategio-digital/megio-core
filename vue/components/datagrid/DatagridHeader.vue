<script lang="ts" setup>
defineProps<{
    breadcrumbItems: string[],
    refresh: Function
}>()
</script>

<template>
    <div class="d-flex justify-space-between align-center">
        <v-breadcrumbs :items="breadcrumbItems" class="pa-0" style="font-size: 1.4rem" />

        <div class="d-flex ms-3">
            <v-tooltip location="top" text="Obnovit data" offset="-5">
                <template v-slot:activator="{ props }">
                    <v-btn
                        v-bind="props"
                        variant="plain"
                        icon="mdi-refresh"
                        size="small"
                        color=""
                        @click="refresh"
                    ></v-btn>
                </template>
            </v-tooltip>

            <v-tooltip location="top" text="Upravit kolekci" offset="-5">
                <template v-slot:activator="{ props }">
                    <v-btn v-bind="props" variant="plain" icon="mdi-cog" size="small" color=""></v-btn>
                </template>
            </v-tooltip>

            <v-btn variant="tonal" prepend-icon="mdi-plus" class="ms-3">
                Přidat
            </v-btn>
        </div>
    </div>
</template>