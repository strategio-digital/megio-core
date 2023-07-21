/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
import { IRow } from '@/saas/api/collections/types/IRow'

export interface IUser extends IRow {
    email: string
    role: string
}