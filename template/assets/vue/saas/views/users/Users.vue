<script lang="ts" setup>
import { ref } from 'vue'
import { storeToRefs } from 'pinia'
import Layout from '@/saas/components/Layout.vue'
import useDatagrid from '@/saas/composables/datagrid/useDatagrid'
import DatagridHeader from '@/saas/components/datagrid/DatagridHeader.vue'
import DatagridTable from '@/saas/components/datagrid/DatagridTable.vue'
import Modal from '@/saas/components/modal/Modal.vue'
import useDatagridModal from '@/saas/composables/datagrid/useDatagridModal'
import useUserModal from '@/saas/composables/modal/useUserModal'
import SideModal from '@/saas/components/modal/SideModal.vue'
import CollectionForm from '@/saas/components/form/CollectionForm.vue'

const { store, refresh } = useDatagrid('user', 10)
const { items, page, selectedItems, selectedItem, loading } = storeToRefs(store)
const { mdlRemove, mdlBulkRemove, remove, bulkRemove } = useDatagridModal('user', refresh, selectedItem, selectedItems)
const { mdlRevoke, mdlBulkRevoke, revoke, bulkRevoke } = useUserModal(refresh, selectedItem, selectedItems)

const editingItemId = ref<string | null>(null)

function handleShowEditMdl(id: string) {
    editingItemId.value = id
}

function handleCloseEditMdl() {
    editingItemId.value = null
}
</script>

<template>
    <Layout :loading="loading">
        <SideModal :opened="editingItemId !== null" @close="handleCloseEditMdl">
            <CollectionForm v-if="editingItemId" :id="editingItemId" collection="user" @close="handleCloseEditMdl" />
        </SideModal>

        <Modal v-bind="mdlRemove" @accept="remove" title="Odstranit uživatele">
            Opravdu si přejete trvale odstranit uživatele
            <span class="font-weight-bold">{{ selectedItem.email }}</span>?
        </Modal>

        <Modal v-bind="mdlRevoke" @accept="revoke" title="Odhlásit uživatele">
            Opravdu si přejete odhlásit uživatele <span class="font-weight-bold">{{ selectedItem.email }}</span>?
        </Modal>

        <Modal v-bind="mdlBulkRemove" @accept="bulkRemove" :title="`Odstranit uživatele (${selectedItems.length}x)`">
            <div class="mb-5">Opravdu si přejete trvale odstranit tyto uživatele?</div>
            <v-chip class="me-2 mb-2" size="small" v-for="email in selectedItems.map(item => item.email)">
                {{ email }}
            </v-chip>
        </Modal>

        <Modal v-bind="mdlBulkRevoke" @accept="bulkRevoke" :title="`Odhlásit uživatele (${selectedItems.length}x)`">
            <div class="mb-5">Opravdu si přejete odhlásit tyto uživatele?</div>
            <v-chip class="me-2 mb-2" size="small" v-for="email in selectedItems.map(item => item.email)">
                {{ email }}
            </v-chip>
        </Modal>

        <div class="pa-7">
            <DatagridHeader :breadcrumb-items="['Uživatelé']" :refresh="refresh" />
            <DatagridTable
                :columns="[
                    { name: 'Role', key: 'role', type: 'string'},
                    { name: 'E-mail', key: 'email', type: 'email'},
                    { name: 'Poslední přihlášení', key: 'lastLogin', type: 'datetime'},
                    { name: 'Expirace tokenu', key: 'loginExpiration', type: 'datetime'},
                    { name: 'Poslední úprava', key: 'updatedAt', type: 'datetime'},
                    { name: 'Registrace', key: 'createdAt', type: 'datetime'},
                ]"
                :batch-actions="[
                    { title: 'Odhlásit uživatele', handler: () => mdlBulkRevoke.toggleOpen('show') },
                    { title: 'Trvale odstranit', handler: () => mdlBulkRemove.toggleOpen('show') }
                ]"
                :row-actions="[
                    { title: 'Odhlásit uživatele', handler: () => mdlRevoke.toggleOpen('show') },
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
        </div>
    </Layout>
</template>