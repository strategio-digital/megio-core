/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import { Router } from 'vue-router'
import ICollectionSummary from '@/saas/components/collection/types/ICollectionSummary'

const summaries = (router: Router): ICollectionSummary[] => {
    return [
        // {
        //     collectionName: 'admin',
        //     onFirstColumnClick: (collection: string, row: IRow) => router.push({
        //         name: 'saas.view.settings.admins'
        //         //params: { id: row.id }
        //     })
        // }
    ]
}

export default summaries