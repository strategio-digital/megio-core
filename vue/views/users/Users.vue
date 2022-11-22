<script lang="ts" setup>
import Layout from '@/components/Layout.vue'
import useDatagrid from '@/composables/useDatagrid'
import DatagridHeader from '@/components/datagrid/DatagridHeader.vue'
import DatagridTable from '@/components/datagrid/DatagridTable.vue'
import { IRow } from '@/plugins/api/types/IRow'

const {
    refresh,
    loading,
    page,
    items,
    selected,
    selectedItems,
    selectAll,
    removeOne,
    removeSelected
} = useDatagrid('user')

const revokeSelected = () => {
    console.log(selectedItems.value)
}

const revokeOne = (item: IRow) => {
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
            :items="items"
            :remove-one="removeOne"
            :remove-selected="removeSelected"
            :selected="selected"
            :selected-items="selectedItems"
            :select-all="selectAll"
            :batchActions="[{ title: 'Odhlásit uživatele', handler: revokeSelected }]"
            :rowActions="[{ title: 'Odhlásit uživatele', handler: revokeOne }]"
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