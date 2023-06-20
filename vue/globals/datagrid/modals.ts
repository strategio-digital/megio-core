/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import RemoveModal from '@/saas/components/datagrid/modal/RemoveModal.vue'
import IDatagridSettings from '@/saas/components/datagrid/types/IDatagridSettings'

const modals: IDatagridSettings['modals'] = [
    {
        onAction: 'remove',
        component: RemoveModal
    }
]

export default modals