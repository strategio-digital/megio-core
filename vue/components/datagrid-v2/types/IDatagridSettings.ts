/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import IDatagridAction from '@/saas/components/datagrid-v2/types/IDatagridAction'
import IDatagridModal from '@/saas/components/datagrid-v2/types/IDatagridModal'

export default interface IDatagridSettings {
    modals: IDatagridModal[]
    actions: {
        row: IDatagridAction[],
        bulk: IDatagridAction[]
    }
}