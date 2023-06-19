<script lang="ts" setup>
import { ref } from 'vue'
import { IResp } from '@/saas/api/collections/crud/show'
import { IPagination } from '@/saas/api/types/IPagination'
import api from '@/saas/api'
import Layout from '@/saas/components/Layout.vue'
import SettingNav from '@/saas/components/navbar/SettingNav.vue'
import Datagrid from '@/saas/components/datagrid-v2/Datagrid.vue'
import PageHeading from '@/saas/components/layout/PageHeading.vue'
import { actions } from '@/saas/globals/datagrid/actions'

const collection = 'admin'
const loading = ref<boolean>(true)
const datagrid = ref()

async function loadFunction(newPagination: IPagination): Promise<IResp> {
    loading.value = true

    const resp = await api.collections.crud.show({
        table: collection,
        schema: true,
        currentPage: newPagination.currentPage,
        itemsPerPage: newPagination.itemsPerPage,
        orderBy: [
            { col: 'createdAt', desc: true },
            { col: 'id', desc: true }
        ]
    })

    loading.value = false

    return resp
}
</script>

<template>
    <Layout :loading="loading">
        <template v-slot:default>
            <div class="pa-7">
                <PageHeading :breadcrumb="['Nastavení', 'Administrátoři']" @onRefresh="() => datagrid.refresh()" />
                <Datagrid
                    ref="datagrid"
                    class="mt-5"
                    :key="collection"
                    :loadFunction="loadFunction"
                    :rowActions="actions.row"
                    :bulkActions="actions.bulk"
                    :defaultItemsPerPage="15"
                    emptyDataMessage="Zatím nebyl přidán žádný administrátor"
                />
            </div>
        </template>

        <template v-slot:navigation>
            <SettingNav />
        </template>
    </Layout>
</template>