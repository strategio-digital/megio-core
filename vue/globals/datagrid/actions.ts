/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import IDatagridSettings from '@/saas/components/datagrid/types/IDatagridSettings'

const actions: IDatagridSettings['actions'] = {
    bulk: [
        {
            type: 'remove',
            label: 'Odstranit',
            showOn: ['/collections', '/settings/admins'],
        },
        {
            type: 'revoke',
            label: 'Odhlásit',
            showOn: [
                '/settings/admins'
            ]
        }
    ],
    row: [
        {
            type: 'remove',
            label: 'Odstranit',
            showOn: ['/collections', '/settings/admins']
        },
        {
            type: 'revoke',
            label: 'Odhlásit',
            showOn: [
                '/settings/admins'
            ]
        }
    ]
}

export default actions