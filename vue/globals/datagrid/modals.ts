/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

// TODO: move into providers
import RemoveModal from '@/saas/components/datagrid-v2/modal/RemoveModal.vue'
import { IDgModal } from '@/saas/components/datagrid-v2/types/IDgModal'

export const modals: IDgModal[] = [
    {
        actionEvent: 'remove',
        component: RemoveModal
    }
]