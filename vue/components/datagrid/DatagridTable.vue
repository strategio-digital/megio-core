<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { IDatagridAction } from '@/saas/components/datagrid/types/IDatagridAction'
import { IDatagridColumn } from '@/saas/components/datagrid/types/IDatagridColumn'
import { useDatagridStore } from '@/saas/composables/datagrid/useDatagridStore'
import dateHelper from '@/saas/helpers/dateHelper'

defineProps<{
    columns: IDatagridColumn[]
    batchActions: IDatagridAction[],
    rowActions: IDatagridAction[],
}>()

const emit = defineEmits<{
    (e: 'goToDetail', id: string): void
}>()

const store = useDatagridStore()
const { checkedAll, items, selectedItems, selectedItem } = storeToRefs(store)
const { toCzDateTime } = dateHelper()

function checkAll() {
    selectedItems.value = checkedAll.value ? [] : items.value
}

function goToDetail(id: string) {
    emit('goToDetail', id)
}
</script>

<template>
    <v-table v-if="items.length" density="default" hover class="mt-5">
        <thead>
        <tr class="text-no-wrap">
            <th style="width: 90px">
                <div class="d-flex align-center">
                    <v-checkbox @click="checkAll" v-model="checkedAll" color="primary" class="d-flex" />

                    <v-btn
                        icon="mdi-dots-vertical"
                        variant="plain"
                        size="small"
                        id="menu-activator"
                        :disabled="selectedItems.length === 0"
                    />

                    <v-menu activator="#menu-activator">
                        <v-list>
                            <v-list-item v-for="(action, i) in batchActions" :key="i" :value="i" @click="action.handler">
                                <v-list-item-title>
                                    {{ action.title }} ({{ selectedItems.length }}x)
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </div>
            </th>
            <th>ID</th>
            <th v-for="col in columns" :key="col.key" class="text-left">{{ col.name }}</th>
            <th class="text-right"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="item in items" :key="item.id" class="text-no-wrap">
            <td>
                <v-checkbox v-model="selectedItems" :value="item" :value-comparator="(a, b) => a.id === b.id" color="primary" class="d-flex"></v-checkbox>
            </td>
            <td>
                <v-chip size="small" color="" style="cursor: pointer" @click="goToDetail(item.id)">
                    {{ item.id }}
                </v-chip>
            </td>
            <td v-for="col in columns" :key="col.key">
                <div v-if="item[col.key]">
                    <a v-if="col.type === 'email'" target="_blank" :href="`mailto:${item[col.key]}`">
                        {{ item[col.key] }}
                    </a>

                    <a v-else-if="col.type === 'phone'" target="_blank" :href="`tel:${item[col.key]}`">
                        {{ item[col.key] }}
                    </a>

                    <span v-else-if="col.type === 'datetime'">{{ toCzDateTime(item[col.key]) }}</span>

                    <span v-else>{{ item[col.key] }}</span>
                </div>
                <div v-else>-</div>
            </td>
            <td class="text-right">
                <v-menu>
                    <template v-slot:activator="{ props }">
                        <v-btn icon="mdi-dots-vertical" v-bind="props" size="small" variant="plain"></v-btn>
                    </template>

                    <v-list>
                        <v-list-item v-for="(action, i) in rowActions" :key="i" :value="i" @click="(selectedItem = item) && action.handler()">
                            <v-list-item-title>
                                {{ action.title }}
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
                <v-btn icon="mdi-arrow-right" variant="plain" size="small" @click="goToDetail(item.id)" />
            </td>
        </tr>
        </tbody>
    </v-table>
</template>