/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
import { IDateTime } from '@/saas/api/types/IDateTime'

export interface IRow {
    id: string
    createdAt: IDateTime
    updatedAt: IDateTime
}