<script lang="ts" setup>
import { onMounted, ref } from 'vue'
import api from '@/saas/api'
import { IResp } from '@/saas/api/collections/crud/show'
import { IPagination } from '@/saas/api/types/IPagination'
import Layout from '@/saas/components/Layout.vue'
import SettingNav from '@/saas/components/navbar/SettingNav.vue'
import Datagrid from '@/saas/components/datagrid-v2/Datagrid.vue'
import PageHeading from '@/saas/components/layout/PageHeading.vue'
import { actions } from '@/saas/globals/datagrid/actions'

const collection = 'admin';

const loading = ref<boolean>(true)
const data = ref<IResp['data']>()
const pagination = ref({ currentPage: 1, itemsPerPage: 15 })

async function refresh() {
    loading.value = true

    const resp = await api.collections.crud.show({
        table: collection,
        schema: true,
        currentPage: pagination.value.currentPage,
        itemsPerPage: pagination.value.itemsPerPage
    })

    if (resp.success) {
        data.value = resp.data
    }

    loading.value = false
}

function handlePaginationChange(newPagination: IPagination) {
    pagination.value = {
        currentPage: newPagination.currentPage,
        itemsPerPage: newPagination.itemsPerPage
    }
    refresh()
}

onMounted(() => refresh())
</script>

<template>
    <Layout :loading="loading">
        <template v-slot:default>
            <div class="pa-7">
                <PageHeading :breadcrumb="['Nastavení', 'Administrátoři']" @onRefresh="refresh" />

                <Datagrid
                    class="mt-5"
                    v-if="data && data.schema"
                    :key="collection"
                    :schema="data.schema"
                    :items="data.items"
                    :pagination="data.pagination"
                    :rowActions="actions.row"
                    :bulkActions="actions.bulk"
                    @onPaginationChange="handlePaginationChange"
                    @onAcceptModalSucceeded="refresh"
                />

                <div v-else class="border-0 border-t border-dashed w-100 py-5 mt-5 text-center">
                    Tato kolekce je prázdná.
                </div>
            </div>
        </template>

        <template v-slot:navigation>
            <SettingNav />
        </template>
    </Layout>
</template>