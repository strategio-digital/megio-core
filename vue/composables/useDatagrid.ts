/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import { onMounted, watch, watchEffect } from 'vue'
import { storeToRefs } from 'pinia'
import { useDatagridStore } from '@/composables/useDatagridStore'
import api from '@/api'

const useDatagrid = (collectionName: string) => {
    const store = useDatagridStore()
    const { checkedAll, loading, selectedItems, items, page } = storeToRefs(store)

    async function refresh(): Promise<any> {
        loading.value = true
        items.value = []
        selectedItems.value = []

        const resp = await api.collections.showAll(collectionName, {
            currentPage: page.value.currentPage,
            itemsPerPage: 15
        })

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
            checkedAll.value = selectedItems.value.length === items.value.length
        }
    })

    onMounted(() => refresh())

    return { store, refresh }
}

export default useDatagrid