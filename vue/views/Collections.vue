<script lang="ts" setup>
import Layout from '@/components/Layout.vue'
import CollectionNav from '@/components/nav/CollectionNav.vue'
</script>

<template>
    <Layout>
        <template v-slot:default>
            <v-breadcrumbs :items="['Kolekce', 'Aktuality']" class="pa-0" style="font-size: 1.4rem"></v-breadcrumbs>
            <p class="mt-5">Todo: ...</p>
        </template>

        <template v-slot:navigation>
            <CollectionNav/>
            <v-btn variant="tonal" class="w-100">
                Přidat kolekci
            </v-btn>
        </template>
    </Layout>
</template>