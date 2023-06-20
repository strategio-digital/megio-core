<script setup lang="ts">
import { ref, onUpdated, onMounted, inject, computed } from 'vue'
import { useRouter } from 'vue-router'
import { IRow } from '@/saas/api/types/IRow'
import { IPagination } from '@/saas/api/types/IPagination'
import { IResp } from '@/saas/api/collections/crud/show'
import IDatagridAction from '@/saas/components/datagrid/types/IDatagridAction'
import IDatagridSettings from '@/saas/components/datagrid/types/IDatagridSettings'
import RowAction from '@/saas/components/datagrid/action/RowAction.vue'
import BulkAction from '@/saas/components/datagrid/action/BulkAction.vue'

defineExpose({ refresh })

const props = defineProps<{
    rowActions: IDatagridAction[]
    bulkActions: IDatagridAction[]
    defaultItemsPerPage: number
    emptyDataMessage: string
    loadFunction: (pagination: IPagination) => Promise<IResp>
    allowActionsFiltering?: boolean
}>()

const emits = defineEmits<{
    (e: 'onRowAction', row: IRow, type: string): void
    (e: 'onBulkAction', rows: IRow[], type: string): void
    (e: 'onFirstColumnClick', row: IRow): void
    (e: 'onPaginationChange', pagination: IPagination): void
    (e: 'onAcceptModalSucceeded'): void
}>()


const router = useRouter()
const modals: IDatagridSettings['modals'] | undefined = inject('datagrid-modals')
const columns: IDatagridSettings['columns'] | undefined = inject('datagrid-columns')

const modal = ref<string | null>(null)
const selected = ref<IRow[]>([])
const multiselectChecked = ref<boolean>(false)
const data = ref<IResp['data']>({
    items: [],
    pagination: {
        currentPage: 1,
        itemsPerPage: props.defaultItemsPerPage,
        lastPage: 0,
        itemsCountAll: 0
    }
})

const allowedBulkActions = computed(() => filterAllowedActions(props.bulkActions))
const allowedRowActions = computed(() => filterAllowedActions(props.rowActions))

async function refresh(newPagination: IPagination | null = null) {
    if (! newPagination) {
        selected.value = []
    }

    newPagination = newPagination || data.value.pagination
    const resp = await props.loadFunction(newPagination)
    if (resp.success) {
        data.value = resp.data
    }
}

async function onPaginationChange(newPage: number) {
    await refresh({ ...data.value.pagination, currentPage: newPage })
    emits('onPaginationChange', data.value.pagination)
}

async function onItemsPerPageChange(newItems: number) {
    await refresh({
        ...data.value.pagination,
        currentPage: 1,
        itemsPerPage: newItems
    })
}

async function onAcceptModalSucceeded() {
    modal.value = null
    selected.value = []
    multiselectChecked.value = false

    const prevData = data.value
    await refresh(data.value.pagination)

    if (prevData.pagination.currentPage > data.value.pagination.lastPage && prevData.pagination.currentPage !== 1) {
        await onPaginationChange(data.value.pagination.lastPage)
    }

    emits('onAcceptModalSucceeded')
}

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
    emits('onFirstColumnClick', row)
}

function onModalCancel() {
    modal.value = null
}

function onUnselectAll() {
    selected.value = []
}

function onSelectAll() {
    // Add items
    if (multiselectChecked.value === false) {
        const newItems = data.value.items.filter(item => ! selected.value.includes(item))
        selected.value.push(...newItems)
    }

    // Remove items
    if (multiselectChecked.value === true) {
        const ids = data.value.items.map(item => item.id)
        selected.value = selected.value.filter(sel => ! ids.includes(sel.id))
    }
}

function resolveMultiselect() {
    const ids = data.value.items.map(item => item.id)
    const items = selected.value.filter(item => ids.includes(item.id))
    multiselectChecked.value = ids.length === items.length && items.length !== 0
}

function filterAllowedActions(actions: IDatagridAction[]) : IDatagridAction[] {
    if (!props.allowActionsFiltering) {
        return actions
    }

    const currentPath = router.currentRoute.value.fullPath

    return actions.map(action => {
        return {
            ...action,
            show: action.showOn.filter(show => currentPath.startsWith(show)).length !== 0
        }
    }).filter(action => action.show)
}

onMounted(() => refresh(data.value.pagination))

onUpdated(() => resolveMultiselect())
</script>

<template>
    <div>
        <!-- dynamic rendered modals -->
        <component
            v-if="data.schema && modals"
            v-for="m in modals" :key="m.onAction"
            :is="m.component"
            :collection="data.schema.meta.table"
            :open="modal === m.onAction"
            :rows="selected"
            @onCancel="onModalCancel"
            @onAccept="onAcceptModalSucceeded"
        />

        <!-- table -->
        <v-table density="default" hover v-if="data.items.length && data.schema">
            <thead>
            <tr class="text-no-wrap">
                <!-- bulk actions -->
                <th class="d-flex align-center">
                    <!-- select all -->
                    <div class="me-2">
                        <v-checkbox
                            v-model="multiselectChecked"
                            @click="onSelectAll"
                            color="primary"
                            class="d-flex"
                        />
                    </div>

                    <!-- bulk actions -->
                    <v-menu>
                        <template v-slot:activator="{ props }">
                            <v-btn
                                v-bind="props"
                                rounded="xl"
                                size="small"
                                :variant="selected.length ? 'tonal' : 'plain'"
                                :disabled="!selected.length"
                            >
                                <v-icon icon="mdi-chevron-down" />
                                <span>{{ selected.length }}</span>
                            </v-btn>
                        </template>
                        <v-list>
                            <BulkAction
                                v-for="action in allowedBulkActions"
                                :v-key="action.name"
                                :bulkAction="action"
                                :count="selected.length"
                                @onBulkAction="onBulkAction"
                            />
                            <BulkAction
                                :bulk-action="{ label: 'Zrušit označení', name: '', showOn:[] }"
                                :count="selected.length"
                                @onBulkAction="onUnselectAll"
                            />
                        </v-list>
                    </v-menu>
                </th>

                <!-- dynamic column names -->
                <th :key="col.name" v-for="col in data.schema.props" class="text-start">
                    {{ col.name.toUpperCase() }}
                </th>

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
                                        @update:model-value="onItemsPerPageChange"
                                        :model-value="data.pagination.itemsPerPage"
                                        :items="[10, 15, 25, 50, 100, 250, 500]"
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
            <tr v-for="item in data.items">
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
                    v-for="(col, colIdx) in data.schema.props"
                    :key="col.name + '_' + item.id"
                >
                    <!--- TODO: COLUMN RENDERER -->
                    <a href="#" v-if="colIdx === 0" @click.prevent="onRowClick(item)">
                        <div>{{ item[col.name] }}</div>
                    </a>
                    <div v-else>{{ item[col.name] }}</div>
                </td>

                <!-- row actions -->
                <td class="text-right text-no-wrap">
                    <v-menu>
                        <template v-slot:activator="{ props }">
                            <v-btn icon="mdi-chevron-down" v-bind="props" size="small" variant="plain"></v-btn>
                        </template>
                        <v-list>
                            <RowAction
                                v-for="action in allowedRowActions"
                                :v-key="action.name"
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

        <div v-if="!data.items.length" class="d-flex justify-center align-center">
            <div class="border-0 border-t border-dashed w-100 py-5 mt-5 text-center">
                {{ emptyDataMessage }}
            </div>
        </div>

        <!-- pagination -->
        <v-pagination
            @update:model-value="onPaginationChange"
            v-if="data.items.length"
            :length="data.pagination.lastPage"
            :total-visible="6"
            class="mt-5"
        />
    </div>
</template>