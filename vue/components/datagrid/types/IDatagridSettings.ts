/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import IDatagridAction from '@/saas/components/datagrid/types/IDatagridAction'
import IDatagridModal from '@/saas/components/datagrid/types/IDatagridModal'

export default interface IDatagridSettings {
    modals: IDatagridModal[]
    actions: {
        row: IDatagridAction[],
        bulk: IDatagridAction[]
    }
}