<script lang="ts" setup>
import { ref, inject } from 'vue'
import { IResp } from '@/saas/api/collections/crud/show'
import { IRow } from '@/saas/api/types/IRow'
import { IPagination } from '@/saas/api/types/IPagination'
import Layout from '@/saas/components/layout/Layout.vue'
import SettingNav from '@/saas/components/navbar/SettingNav.vue'
import PageHeading from '@/saas/components/layout/PageHeading.vue'
import Datagrid from '@/saas/components/datagrid/Datagrid.vue'
import IDatagridSettings from '@/saas/components/datagrid/types/IDatagridSettings'
import api from '@/saas/api'

const collection = 'admin'
const actions: IDatagridSettings['actions'] | undefined = inject('datagrid-actions')
const loading = ref<boolean>(true)
const datagrid = ref()

async function loadFunction(newPagination: IPagination): Promise<IResp> {
    loading.value = true

    const resp = await api.collections.show({
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

function handleFirstColumnClick(row: IRow) {
    console.log('go to detail on admin', row.id)
}
</script>

<template>
    <Layout :loading="loading">
        <template v-slot:default>
            <div class="pa-7">
                <PageHeading :breadcrumb="['Administrátoři']" @onRefresh="() => datagrid.refresh()" />
                <Datagrid
                    v-if="actions"
                    ref="datagrid"
                    class="mt-5"
                    :key="collection"
                    :loadFunction="loadFunction"
                    :rowActions="actions.row"
                    :bulkActions="actions.bulk"
                    :allowActionsFiltering="true"
                    :defaultItemsPerPage="15"
                    emptyDataMessage="Zatím nebyl přidán žádný administrátor"
                    @onFirstColumnClick="handleFirstColumnClick"
                />
            </div>
        </template>

        <template v-slot:navigation>
            <SettingNav />
        </template>
    </Layout>
</template>