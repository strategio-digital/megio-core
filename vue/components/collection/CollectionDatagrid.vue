<script setup lang="ts">
import { ref, inject } from 'vue'
import { IPagination } from '@/saas/api/types/IPagination'
import { IResp } from '@/saas/api/collections/crud/show'
import { IRow } from '@/saas/api/types/IRow'
import IDatagridSettings from '@/saas/components/datagrid-v2/types/IDatagridSettings'
import ICollectionSummary from '@/saas/components/collection/types/ICollectionSummary'
import PageHeading from '@/saas/components/layout/PageHeading.vue'
import Datagrid from '@/saas/components/datagrid-v2/Datagrid.vue'
import api from '@/saas/api'

const props = defineProps<{ tableName: string }>()
const emits = defineEmits<{ (e: 'onLoadingChange', status: boolean): void }>()

const actions: IDatagridSettings['actions'] | undefined = inject('datagrid-actions')
const summaries: ICollectionSummary[] | undefined = inject('collection-summaries')

const loading = ref<boolean>(true)
const datagrid = ref()

async function loadFunction(newPagination: IPagination): Promise<IResp> {
    loading.value = true
    emits('onLoadingChange', loading.value)

    const resp = await api.collections.crud.show({
        table: props.tableName,
        schema: true,
        currentPage: newPagination.currentPage,
        itemsPerPage: newPagination.itemsPerPage,
        orderBy: [
            { col: 'createdAt', desc: true },
            { col: 'id', desc: true }
        ]
    })

    loading.value = false
    emits('onLoadingChange', loading.value)

    return resp
}

function handleFirstColumnClick(row: IRow) {
    const custom = summaries?.filter(sum => sum.collectionName === props.tableName).shift()
    if (custom) {
        custom.onFirstColumnClick(props.tableName, row)
    } else {
        console.log('open sideModal by default')
    }
}

</script>

<template>
    <div class="h-100" v-show="!loading">
        <PageHeading :breadcrumb="['Nastavení', tableName]" @onRefresh="() => datagrid.refresh()" />
        <Datagrid
            v-if="actions"
            ref="datagrid"
            class="mt-5"
            :key="tableName"
            :loadFunction="loadFunction"
            :rowActions="actions.row"
            :bulkActions="actions.bulk"
            :defaultItemsPerPage="15"
            emptyDataMessage="Tato kolekce je prázdná."
            @onFirstColumnClick="handleFirstColumnClick"
        />
    </div>
</template>