/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import IDgAction from '@/saas/components/datagrid-v2/types/IDgAction'

export default interface IDgActions {
    bulk: IDgAction[],
    row: IDgAction[]
}