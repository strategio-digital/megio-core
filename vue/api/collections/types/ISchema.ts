/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
import { ISchemaProp } from '@/saas/api/collections/types/ISchemaProp'

export interface ISchema {
    meta: {
        table: string,
        invisible: string[]
    }
    props: ISchemaProp[]
}