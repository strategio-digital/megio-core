<script lang="ts" setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { COLLECTION_EMPTY_ROUTE } from '@/saas/components/navbar/types/Constants'
import api from '@/saas/api'
import Layout from '@/saas/components/Layout.vue'
import CollectionDatagrid from '@/saas/components/collection/CollectionDatagrid.vue'

const route = useRoute()
const router = useRouter()

const loading = ref(true)
const navbarLoading = ref(true)
const collections = ref<string[]>([])
const tableName = ref<string>(COLLECTION_EMPTY_ROUTE)

function isActive(routeName: string): boolean {
    return routeName === tableName.value
}

function handleLoading(status: boolean) {
    loading.value = status
}

watch(() => route.params.name, () => {
    const routeName = route.params.name.toString()

    if (routeName === COLLECTION_EMPTY_ROUTE && collections.value.length !== 0) {
        tableName.value = collections.value[0]
    } else {
        tableName.value = routeName
    }
})

onMounted(async () => {
    const navbar = await api.collections.meta.navbar()
    const items = navbar.data.items

    collections.value = items
    navbarLoading.value = false

    if (items.length === 0) {
        loading.value = false
    }

    const routeName = route.params.name.toString()

    if (routeName === COLLECTION_EMPTY_ROUTE && collections.value.length !== 0) {
        tableName.value = items[0]
    } else {
        tableName.value = routeName
    }
})
</script>

<template>
    <Layout :loading="loading">
        <template v-slot:default>
            <div class="pa-7 h-100">
                <CollectionDatagrid
                    v-if="collections.length !== 0"
                    :key="tableName"
                    :table-name="tableName"
                    @onLoadingChange="handleLoading"
                />
                <div v-if="!loading && collections.length === 0">
                    <v-breadcrumbs :items="['Kolekce']" class="pa-0" style="font-size: 1.4rem" />
                    <p class="mt-3">Zatím nebyla vytvořena žádná kolekce.</p>
                </div>
            </div>
        </template>

        <template v-slot:navigation>
            <v-list density="comfortable">
                <v-list-item
                    v-for="name in collections"
                    :title="name"
                    :value="name"
                    :to="{ name: 'Collections', params: { name: name }}"
                    :active="isActive(name)"
                    prepend-icon="mdi-folder-outline"
                />
            </v-list>

            <v-btn v-if="!navbarLoading" variant="tonal" class="w-100">
                Přidat kolekci
            </v-btn>

            <div
                class="d-flex justify-center align-center"
                style="height: calc(100% - 2rem)"
                v-if="navbarLoading"
            >
                <v-progress-circular indeterminate :size="30" :width="3" />
            </div>
        </template>
    </Layout>
</template>