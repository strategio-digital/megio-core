<script lang="ts" setup>
import { onMounted, ref } from 'vue'
import api from '@/saas/api'
import Layout from '@/saas/components/Layout.vue'
import SettingNav from '@/saas/components/navbar/SettingNav.vue'
import Datagrid from '@/saas/components/datagrid-v2/Datagrid.vue'
import PageHeading from '@/saas/components/layout/PageHeading.vue'
import { IResp } from '@/saas/api/collections/crud/show'
import { useDatagrid } from '@/saas/components/datagrid-v2/composable/useDatagrid'

const loading = ref<boolean>(true)
const data = ref<IResp['data']>()
const dg = useDatagrid(refresh)

async function refresh() {
    loading.value = true

    const resp = await api.collections.crud.show({
        table: 'admin',
        schema: true,
        currentPage: dg.pagination.value?.currentPage || 1,
        itemsPerPage: dg.pagination.value?.itemsPerPage || 15
    })

    if (resp.success) {
        data.value = resp.data
    }

    loading.value = false
}

onMounted(() => refresh())
</script>

<template>
    <Layout :loading="loading">
        <template v-slot:default>
            <div class="pa-7">
                <PageHeading :breadcrumb="['Nastavení', 'Administrátoři']" @onRefresh="refresh"/>

                <Datagrid
                    class="mt-5"
                    v-if="data && data.schema"
                    key="admin-datagrid"
                    :schema="data.schema"
                    :items="data.items"
                    :pagination="data.pagination"
                    :rowActions="[{ type: 'remove', label: 'Odstranit' }, { type: 'revoke', label: 'Odhlásit' }]"
                    :bulkActions="[{ type: 'remove', label: 'Odstranit' }, { type: 'revoke', label: 'Odhlásit' }]"
                    @onRowClick="dg.rowClick"
                    @onRowAction="dg.rowAction"
                    @onBulkAction="dg.bulkAction"
                    @onPaginationChange="dg.paginationChange"
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