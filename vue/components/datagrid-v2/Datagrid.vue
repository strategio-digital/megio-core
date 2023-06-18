<script setup lang="ts">
import { ref, watch } from 'vue'
import { IRow } from '@/saas/api/types/IRow'
import { IDgAction } from '@/saas/components/datagrid-v2/types/IDgAction'
import { IPagination } from '@/saas/api/types/IPagination'
import { ISchema } from '@/saas/api/types/ISchema'
import RowAction from '@/saas/components/datagrid-v2/action/RowAction.vue'
import BulkAction from '@/saas/components/datagrid-v2/action/BulkAction.vue'
import { modals } from '@/saas/globals/datagrid/modals'

const props = defineProps<{
    items: IRow[],
    pagination: IPagination,
    schema: ISchema,
    rowActions: IDgAction[],
    bulkActions: IDgAction[]
}>()

const emits = defineEmits<{
    (e: 'onRowClick', row: IRow): void
    (e: 'onRowAction', row: IRow, type: string): void
    (e: 'onBulkAction', rows: IRow[], type: string): void
    (e: 'onPaginationChange', pagination: IPagination): void
    (e: 'onAcceptModalSucceeded'): void
}>()

const modal = ref<string | null>(null)
const selected = ref<IRow[]>([])
const multiselectChecked = ref<boolean>(false)

function onRowAction(row: IRow, type: string) {
    modal.value = type
    selected.value = [row]
    emits('onRowAction', row, type)
}

function onBulkAction(type: string) {
    modal.value = type
    emits('onBulkAction', selected.value, type)
}

function onRowClick(row: IRow) {
    emits('onRowClick', row)
}

function onAcceptModalSucceeded() {
    modal.value = null
    selected.value = []
    emits('onAcceptModalSucceeded')
}

function onModalCancel() {
    modal.value = null
}

watch(() => props.pagination.currentPage, () => {
    emits('onPaginationChange', props.pagination)
})

watch(() => props.pagination.itemsPerPage, () => {
    if (props.pagination.currentPage === 1) {
        emits('onPaginationChange', props.pagination)
    } else {
        props.pagination.currentPage = 1
    }
})

watch(() => multiselectChecked.value, () => {
    if (multiselectChecked.value && selected.value.length < props.items.length) {
        selected.value = props.items
    } else {
        selected.value = []
    }
})
</script>

<template>
    <div>
        <!-- dynamic rendered modals -->
        <component
            v-for="m in modals" :key="m.actionEvent"
            :is="m.component"
            :collection="schema.meta.table"
            :open="modal === m.actionEvent"
            :rows="selected"
            @onCancel="onModalCancel"
            @onAccept="onAcceptModalSucceeded"
        />

        <v-table density="default" hover>
            <thead>
            <tr class="text-no-wrap">
                <!-- bulk actions -->
                <th style="width: 100px" class="d-flex align-center">
                    <!-- select all -->
                    <v-checkbox v-model="multiselectChecked" color="primary" class="d-flex" />

                    <!-- bulk actions -->
                    <v-menu>
                        <template v-slot:activator="{ props }">
                            <v-btn
                                icon="mdi-dots-vertical"
                                v-bind="props"
                                size="small"
                                variant="plain"
                                :disabled="!selected.length"
                            ></v-btn>
                        </template>
                        <v-list>
                            <BulkAction
                                v-for="action in bulkActions"
                                :v-key="action.type"
                                :bulkAction="action"
                                :count="selected.length"
                                @onBulkAction="onBulkAction"
                            />
                        </v-list>
                    </v-menu>
                </th>

                <!-- dynamic column names -->
                <th :key="col.name" v-for="col in schema.props">{{ col.name.toUpperCase() }}</th>

                <!-- datagrid settings -->
                <th class="text-right">
                    <v-menu :close-on-content-click="false">
                        <template v-slot:activator="{ props }">
                            <v-btn
                                icon="mdi-dots-vertical"
                                v-bind="props"
                                size="small"
                                variant="plain"
                            ></v-btn>
                        </template>

                        <!-- change items per page -->
                        <v-card min-width="300">
                            <v-list>
                                <v-list-item>
                                    <v-select
                                        class="my-2"
                                        hide-details
                                        v-model="pagination.itemsPerPage"
                                        :items="[10, 15, 25, 50, 100, 250, 500, 1000]"
                                        variant="outlined"
                                        density="compact"
                                        label="Počet položek na stránku"
                                    ></v-select>
                                </v-list-item>
                            </v-list>
                        </v-card>
                    </v-menu>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in items">
                <!-- checkbox for bulk actions -->
                <td>
                    <v-checkbox
                        v-model="selected"
                        :value="item"
                        :value-comparator="(a, b) => a.id === b.id"
                        color="primary"
                        class="d-flex"
                    ></v-checkbox>
                </td>

                <!-- dynamic column values -->
                <td
                    class="text-no-wrap"
                    v-for="col in schema.props"
                    :key="col.name + '_' + item.id"
                    @click="onRowClick(item)"
                >
                    <div v-if="item[col.name]">{{ item[col.name] }}</div>
                    <div v-else>-</div>
                </td>

                <!-- row actions -->
                <td class="text-right text-no-wrap">
                    <v-menu>
                        <template v-slot:activator="{ props }">
                            <v-btn icon="mdi-dots-vertical" v-bind="props" size="small" variant="plain"></v-btn>
                        </template>
                        <v-list>
                            <RowAction
                                v-for="action in rowActions"
                                :v-key="action.type"
                                :row="item"
                                :rowAction="action"
                                @onRowAction="onRowAction"
                            />
                        </v-list>
                    </v-menu>
                    <v-btn icon="mdi-arrow-right" variant="plain" size="small" @click="onRowClick(item)" />
                </td>
            </tr>
            </tbody>
        </v-table>

        <v-pagination
            v-model="pagination.currentPage"
            :length="pagination.lastPage"
            :total-visible="5"
            class="mt-5"
        />
    </div>
</template>