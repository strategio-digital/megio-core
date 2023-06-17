<script lang="ts" setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Layout from '@/saas/components/Layout.vue'
import CollectionNav from '@/saas/components/navbar/CollectionNav.vue'
import CollectionDatagrid from '@/saas/components/collection/CollectionDatagrid.vue'
import { COLLECTION_EMPTY_ROUTE } from '@/saas/components/navbar/types/Constants'

// TODO: hide ids (add default columnName)
// TODO: render Collection navbar by DB
// TODO: show loading

const route = useRoute()
const router = useRouter()
const tableName = ref(COLLECTION_EMPTY_ROUTE)

watch(() => route.params.name, () => {
    const routeName = route.params.name.toString()
    tableName.value = routeName

    if (routeName === COLLECTION_EMPTY_ROUTE) {
        router.push({ name: 'Collections', params: { name: 'lead' } })
    }
})

onMounted(async () => {
    const routeName = route.params.name.toString()
    tableName.value = routeName

    if (routeName === COLLECTION_EMPTY_ROUTE) {
        router.push({ name: 'Collections', params: { name: 'lead' } })
    }
})
</script>

<template>
    <Layout>
        <template v-slot:default>
            <div class="pa-7">
                <div v-if="tableName !== COLLECTION_EMPTY_ROUTE">
                    <CollectionDatagrid :key="tableName" :table-name="tableName" />
                </div>
                <div v-else>
                    <v-breadcrumbs :items="['Kolekce']" class="pa-0" style="font-size: 1.4rem" />
                    <p class="mt-3">Zatím nebyla vytvořena žádná databázová tabulka / entita.</p>
                </div>
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