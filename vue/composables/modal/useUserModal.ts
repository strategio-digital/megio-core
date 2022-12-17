/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import { Ref } from 'vue'
import useModal from '@/saas/composables/modal/useModal'
import api from '@/saas/api'

const useUserModal = (refresh: Function, selectedItem: Ref, selectedItems: Ref<any[]>) => {
    const mdlRevoke = useModal()
    const mdlBulkRevoke = useModal()

    async function revoke() {
        mdlRevoke.toggleLoading('show')
        await api.auth.revokeToken([selectedItem.value.id])
        mdlRevoke.toggleOpen('hide')
        await refresh()
    }

    async function bulkRevoke() {
        mdlBulkRevoke.toggleLoading('show')
        await api.auth.revokeToken(selectedItems.value.map((item) => item.id))
        mdlBulkRevoke.toggleOpen('hide')
        await refresh()
    }

    return {
        mdlRevoke,
        mdlBulkRevoke,
        revoke,
        bulkRevoke
    }
}

export default useUserModal