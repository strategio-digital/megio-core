/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import { ref } from 'vue'
import { defineStore } from 'pinia'
import { IRow } from '@/api/types/IRow'

export const useDatagridStore = defineStore('datagrid', () => {
    const loading = ref(true)
    const page = ref({ currentPage: 1, lastPage: 1 })

    const items = ref<IRow[]>([])
    const selectedItems = ref<IRow[]>([])
    const checkedAll = ref(false)

    return {
        loading,
        page,
        items,
        selectedItems,
        checkedAll
    }
})