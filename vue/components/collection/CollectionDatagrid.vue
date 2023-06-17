<script setup lang="ts">
import { watch } from 'vue'
import { storeToRefs } from 'pinia'
import DatagridTable from '@/saas/components/datagrid/DatagridTable.vue'
import DatagridHeader from '@/saas/components/datagrid/DatagridHeader.vue'
import useDatagrid from '@/saas/composables/datagrid/useDatagrid'
import useDatagridModal from '@/saas/composables/datagrid/useDatagridModal'
import Modal from '@/saas/components/modal/Modal.vue'

const props = defineProps<{ tableName: string }>()
const emits = defineEmits<{ (e: 'onLoadingChange', status: boolean): void }>()

const { store, refresh } = useDatagrid(props.tableName, 15)
const { items, page, selectedItems, selectedItem, loading } = storeToRefs(store)
const {
    mdlRemove,
    mdlBulkRemove,
    remove,
    bulkRemove
} = useDatagridModal(props.tableName, refresh, selectedItem, selectedItems)

watch(() => loading.value, () => {
    emits('onLoadingChange', loading.value)
})

function handleShowEditMdl() {
    console.log('show')
}

</script>

<template>
    <div v-if="!loading" class="h-100">
        <Modal v-bind="mdlRemove" @accept="remove" title="Odstranit záznam">
            Opravdu si přejete trvale odstranit tento záznam?
            <span class="font-weight-bold">{{ selectedItem.id }}</span>?
        </Modal>

        <Modal v-bind="mdlBulkRemove" @accept="bulkRemove" :title="`Odstranit záznam (${selectedItems.length}x)`">
            <div class="mb-5">Opravdu si přejete trvale odstranit tyto záznamy?</div>
            <v-chip class="me-2 mb-2" size="small" v-for="email in selectedItems.map(item => item.id)">
                {{ email }}
            </v-chip>
        </Modal>

        <DatagridHeader :breadcrumb-items="['Kolekce', tableName]" :refresh="refresh" />
        <DatagridTable
            :columns="[
            { name: 'Upraveno', key: 'updatedAt', type: 'datetime'},
            { name: 'Vytvořeno', key: 'createdAt', type: 'datetime'},
            { name: 'Obsah', key: 'content', type: 'string'},
            //{ name: 'E-mail', key: 'email', type: 'email'},
        ]"
            :batch-actions="[
            { title: 'Trvale odstranit', handler: () => mdlBulkRemove.toggleOpen('show') }
        ]"
            :row-actions="[
            { title: 'Trvale odstranit', handler: () => mdlRemove.toggleOpen('show') }
        ]"
            @goToDetail="handleShowEditMdl"
        />
        <v-pagination
            v-if="items.length"
            v-model="page.currentPage"
            :length="page.lastPage"
            :total-visible="5"
            class="mt-5"
        />
        <div v-if="!items.length" class="d-flex justify-center align-center">
            <div class="border-0 border-t border-dashed w-100 py-5 mt-5 text-center">
                Tato kolekce je prázná.
            </div>
        </div>
    </div>
</template>