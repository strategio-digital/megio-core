<template>
    <v-list density="comfortable">
        <v-list-item
            title="Reference"
            value="reference"
            prepend-icon="mdi-folder-outline"
            :to="{ name: 'Collections', params: { name: 'reference' }}"
            :active="false"
        />
        <v-list-item
            title="Leady"
            value="lead"
            prepend-icon="mdi-folder-outline"
            :to="{ name: 'Collections', params: { name: 'lead' }}"
            :active="false"
        />
        <v-list-item
            title="Newsletter"
            value="newsletter"
            prepend-icon="mdi-folder-outline"
            :to="{ name: 'Collections', params: { name: 'newsletter' }}"
            :active="false"
        />
    </v-list>
</template>