/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import { Ref } from 'vue'
import useModal from '@/saas/composables/modal/useModal'
import api from '@/saas/api'

const useDatagridModal = (collectionName: string, refresh: Function, selectedItem: Ref, selectedItems: Ref<any[]>) => {
    const mdlRemove = useModal()
    const mdlBulkRemove = useModal()

    async function remove() {
        mdlRemove.toggleLoading('show')
        await api.collections.remove(collectionName, [selectedItem.value.id])
        mdlRemove.toggleOpen('hide')
        await refresh()
    }

    async function bulkRemove() {
        mdlBulkRemove.toggleLoading('show')
        await api.collections.remove(collectionName, selectedItems.value.map((item) => item.id))
        mdlBulkRemove.toggleOpen('hide')
        await refresh()
    }

    return {
        mdlRemove,
        mdlBulkRemove,
        remove,
        bulkRemove
    }
}

export default useDatagridModal