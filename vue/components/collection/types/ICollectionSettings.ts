/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
import { Router } from 'vue-router'

import ICollectionSummary from '@/saas/components/collection/types/ICollectionSummary'

export default interface ICollectionSettings {
    summaries: (router: Router) => ICollectionSummary[]
}