<script setup lang="ts">
import { ref } from 'vue'
import { IPagination } from '@/saas/api/types/IPagination'
import { IResp } from '@/saas/api/collections/crud/show'
import { actions } from '@/saas/globals/datagrid/actions'
import api from '@/saas/api'
import PageHeading from '@/saas/components/layout/PageHeading.vue'
import Datagrid from '@/saas/components/datagrid-v2/Datagrid.vue'

const props = defineProps<{ tableName: string }>()
const emits = defineEmits<{ (e: 'onLoadingChange', status: boolean): void }>()

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

function handleShowEditMdl() {
    console.log('show')
}

</script>

<template>
    <div class="h-100" v-show="!loading">
        <PageHeading :breadcrumb="['Nastavení', tableName]" @onRefresh="() => datagrid.refresh()" />
        <Datagrid
            ref="datagrid"
            class="mt-5"
            :key="tableName"
            :loadFunction="loadFunction"
            :rowActions="actions.row"
            :bulkActions="actions.bulk"
            :defaultItemsPerPage="15"
            emptyDataMessage="Tato kolekce je prázdná."
        />
    </div>
</template>