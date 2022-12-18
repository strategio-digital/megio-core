<script lang="ts" setup>
import Layout from '@/saas/components/Layout.vue'
import CollectionNav from '@/saas/components/navbar/CollectionNav.vue'
</script>

<template>
    <Layout>
        <template v-slot:default>
            <div class="pa-7">
                <v-breadcrumbs :items="['Kolekce', 'Aktuality']" class="pa-0" style="font-size: 1.4rem"/>
                <p class="mt-5">Todo: ...</p>
            </div>
        </template>

        <template v-slot:navigation>
            <CollectionNav />
            <v-btn variant="tonal" class="w-100">
                Přidat kolekci
            </v-btn>
        </template>
    </Layout>
</template>