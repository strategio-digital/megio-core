<script lang="ts" setup>
import { useRouter } from 'vue-router'
import { computed, ref } from 'vue'
import { IDatagridAction } from '@/types/IDatagridAction'
import { IDatagridColumn } from '@/types/IDatagridColumn'
import dateHelper from '@/helpers/dateHelper'
import { IRow } from '@/plugins/api/types/IRow'

const props = defineProps<{
    columns: IDatagridColumn[]
    items: IRow[]
    removeOne: Function
    removeSelected: Function
    //selected: boolean
    //selectedItems: IUser[],
    selectAll: Function
    batchActions: IDatagridAction[],
    rowActions: IDatagridAction[],
    routeDetailName: string
}>()

const router = useRouter()
const { toCzDateTime } = dateHelper()

const selectedItems = ref<IRow[]>([])
const selected = ref(false)

const goToDetail = (id: string) => router.push({ name: props.routeDetailName, params: { id } })

const itemsHydrated = computed(() => {
    return props.items.map(item => {
        return {
            ...item,
            createdAt: toCzDateTime(item.createdAt),
            updatedAt: toCzDateTime(item.updatedAt),
        }
    })
})

const colData = (colName: string, item: any) => {
    return item[colName]
}
</script>

<template>
    <v-table v-if="items.length" density="default" hover class="mt-5">
        <thead>
        <tr>
            <th style="width: 90px">
                <div class="d-flex align-center">
                    <v-checkbox @click="selectAll" v-model="selected" color="primary" class="d-flex" />

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

                            <v-list-item value="revoke">
                                <v-list-item-title @click="removeSelected" class="text-red font-weight-bold">
                                    Trvale odstranit ({{ selectedItems.length }}x)
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
                <v-checkbox v-model="selectedItems" :value="item" color="primary" class="d-flex"></v-checkbox>
            </td>
            <td>
                <v-chip size="small" color="" style="cursor: pointer" @click="goToDetail(item.id)">
                    {{ item.id }}
                </v-chip>
            </td>
            <td v-for="col in columns" :key="col.key">
                <div v-if="colData(col.key, item)">
                    <a v-if="col.type === 'email'" target="_blank" :href="`mailto:${colData(col.key, item)}`">{{ colData(col.key, item) }}</a>
                    <a v-else-if="col.type === 'phone'" target="_blank" :href="`tel:${colData(col.key, item)}`">{{ colData(col.key, item) }}</a>
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

                        <v-list-item value="revoke">
                            <v-list-item-title @click="removeOne(item)" class="text-red font-weight-bold">
                                Trvale odstranit
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