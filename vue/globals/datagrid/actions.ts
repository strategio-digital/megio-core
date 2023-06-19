/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import IDgActions from '@/saas/components/datagrid-v2/types/IDgActions'

const actions: IDgActions = {
    bulk: [
        {
            type: 'remove',
            label: 'Odstranit'
        },
        // {
        //     type: 'revoke',
        //     label: 'Odhlásit'
        // }
    ],
    row: [
        {
            type: 'remove',
            label: 'Odstranit'
        },
        // {
        //     type: 'revoke',
        //     label: 'Odhlásit'
        // }
    ]
}

export default actions