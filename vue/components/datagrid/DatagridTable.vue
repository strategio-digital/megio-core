<script lang="ts" setup>
import { useRouter } from 'vue-router'
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { IDatagridAction } from '@/components/datagrid/types/IDatagridAction'
import { IDatagridColumn } from '@/components/datagrid/types/IDatagridColumn'
import { useDatagridStore } from '@/composables/useDatagridStore'
import dateHelper from '@/helpers/dateHelper'

const props = defineProps<{
    columns: IDatagridColumn[]
    batchActions: IDatagridAction[],
    rowActions: IDatagridAction[],
    routeDetailName: string
}>()

const router = useRouter()
const store = useDatagridStore()
const { checkedAll, items, selectedItems } = storeToRefs(store)
const { toCzDateTime } = dateHelper()

const itemsHydrated = computed(() => items.value.map(item => {
    return {
        ...item,
        createdAt: toCzDateTime(item.createdAt),
        updatedAt: toCzDateTime(item.updatedAt)
    }
}))

function checkAll() {
    selectedItems.value = checkedAll.value ? [] : items.value
}

function goToDetail(id: string) {
    router.push({ name: props.routeDetailName, params: { id } })
}

function colData(colName: string, item: any) {
    return item[colName]
}
</script>

<template>
    <v-table v-if="items.length" density="default" hover class="mt-5">
        <thead>
        <tr>
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
                            <v-list-item v-for="(action, i) in batchActions" :key="i" :value="i">
                                <v-list-item-title @click="action.handler">
                                    {{ action.title }} ({{ selectedItems.length }}x)
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </div>
            </th>
            <th>ID</th>
            <th v-for="col in columns" :key="col.key" class="text-left">{{ col.name }}</th>
            <th class="text-right">Vytvořeno</th>
            <th class="text-right">Upraveno</th>
            <th class="text-right"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="item in itemsHydrated" :key="item.id" class="text-no-wrap">
            <td>
                <v-checkbox v-model="selectedItems" :value="item" :value-comparator="(a, b) => a.id === b.id" color="primary" class="d-flex"></v-checkbox>
            </td>
            <td>
                <v-chip size="small" color="" style="cursor: pointer" @click="goToDetail(item.id)">
                    {{ item.id }}
                </v-chip>
            </td>
            <td v-for="col in columns" :key="col.key">
                <div v-if="colData(col.key, item)">
                    <a v-if="col.type === 'email'" target="_blank" :href="`mailto:${colData(col.key, item)}`">
                        {{ colData(col.key, item) }}
                    </a>

                    <a v-else-if="col.type === 'phone'" target="_blank" :href="`tel:${colData(col.key, item)}`">
                        {{ colData(col.key, item) }}
                    </a>

                    <span v-else>{{ colData(col.key, item) }}</span>
                </div>
                <div v-else>-</div>
            </td>
            <td class="text-right">{{ item.updatedAt }}</td>
            <td class="text-right">{{ item.updatedAt }}</td>
            <td class="text-right">
                <v-menu>
                    <template v-slot:activator="{ props }">
                        <v-btn icon="mdi-dots-vertical" v-bind="props" size="small" variant="plain"></v-btn>
                    </template>

                    <v-list>
                        <v-list-item v-for="(action, i) in rowActions" :key="i" :value="i">
                            <v-list-item-title @click="action.handler(item)">
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