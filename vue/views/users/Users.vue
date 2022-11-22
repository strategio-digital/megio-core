<script lang="ts" setup>
import Layout from '@/components/Layout.vue'
import useDatagrid from '@/composables/useDatagrid'
import DatagridHeader from '@/components/datagrid/DatagridHeader.vue'
import DatagridTable from '@/components/datagrid/DatagridTable.vue'
import { IRow } from '@/api/types/IRow'
import { storeToRefs } from 'pinia'

const { store, refresh } = useDatagrid('user')
const { items, page, selectedItems, loading } = storeToRefs(store)

function revokeSelected() {
    console.log(selectedItems.value)
}

function revokeOne (item: IRow) {
    console.log(item)
}

function removeSelected() {
    console.log(selectedItems.value)
}

function removeOne(item: IRow) {
    console.log(item)
}
</script>

<template>
    <Layout :loading="loading">
        <DatagridHeader :breadcrumb-items="['Uživatelé']" :refresh="refresh" />

        <DatagridTable
            :columns="[
                { name: 'Role', key: 'role', type: 'string'},
                { name: 'E-mail', key: 'email', type: 'email'}
            ]"
            :batch-actions="[
                { title: 'Odhlásit uživatele', handler: revokeSelected },
                { title: 'Trvale odstranit', handler: removeSelected }
            ]"
            :row-actions="[
                { title: 'Odhlásit uživatele', handler: revokeOne },
                { title: 'Trvale odstranit', handler: removeOne }
            ]"
            route-detail-name="UserDetail"
        />

        <v-pagination
            v-if="items.length"
            v-model="page.currentPage"
            :length="page.lastPage"
            :total-visible="5"
            class="mt-5"
        />
    </Layout>
</template>