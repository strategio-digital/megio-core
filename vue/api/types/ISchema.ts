/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
import { ISchemaProp } from '@/saas/api/types/ISchemaProp'

export interface ISchema {
    meta: {
        table: string
    }
    props: ISchemaProp[]
}