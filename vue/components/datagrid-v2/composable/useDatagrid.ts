/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import { ref } from 'vue'
import { IRow } from '@/saas/api/types/IRow'
import { IPagination } from '@/saas/api/types/IPagination'

export const useDatagrid = (refresh: () => void) => {

    const pagination = ref<IPagination>()

    function rowClick(row: IRow) {
        console.log(row.id)
    }

    function rowAction(row: IRow, type: string) {
        console.log(row.id, type)
        refresh()
    }

    function bulkAction(rows: IRow[], type: string) {
        console.log(rows.map(row => row.id), type)
        refresh()
    }

    function paginationChange(newPagination: IPagination) {
        pagination.value = newPagination
        refresh()
    }

    return {
        pagination,
        rowClick,
        rowAction,
        bulkAction,
        paginationChange
    }
}