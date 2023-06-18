/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

// TODO: move into providers
import { IDgAction } from '@/saas/components/datagrid-v2/types/IDgAction'

export const actions: { bulk: IDgAction[], row: IDgAction[] } = {
    bulk: [
        { type: 'remove', label: 'Odstranit' },
        //{ type: 'revoke', label: 'Odhlásit' }
    ],
    row: [
        { type: 'remove', label: 'Odstranit' },
        //{ type: 'revoke', label: 'Odhlásit' }
    ]
}