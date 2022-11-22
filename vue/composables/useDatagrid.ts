/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import { onMounted, ref, watch, watchEffect } from 'vue'
import api from '@/plugins/api'
import { IRow } from '@/plugins/api/types/IRow'

const useDatagrid = (collectionName: string) => {
    const loading = ref(true)
    const page = ref({ currentPage: 1, lastPage: 1 })
    const items = ref<IRow[]>([])

    const selectedItems = ref<IRow[]>([])
    const selected = ref(false)

    const selectAll = () => selectedItems.value = selected.value ? [] : items.value

    const removeSelected = () => {
        console.log(selectedItems.value)
    }

    const removeOne = (item: IRow) => {
        console.log(item)
    }

    const refresh = async (): Promise<any> => {
        loading.value = true
        items.value = []
        selectedItems.value = []

        let resp = await api.collections.showAll(collectionName, { currentPage: page.value.currentPage, itemsPerPage: 15 })

        page.value = { currentPage: resp.data.currentPage, lastPage: resp.data.lastPage }

        if (resp.data.lastPage < page.value.currentPage) {
            page.value.currentPage = resp.data.lastPage
            return await refresh()
        }

        items.value = resp.data.items
        loading.value = false
    }

    watch(() => page.value.currentPage, () => refresh())

    watchEffect(() => {
        if (items.value.length !== 0) {
            selected.value = selectedItems.value.length === items.value.length
        }
    })

    onMounted(() => refresh())

    return {
        refresh,
        selectAll,
        removeOne,
        removeSelected,
        page,
        selectedItems,
        items,
        loading,
        selected
    }
}

export default useDatagrid