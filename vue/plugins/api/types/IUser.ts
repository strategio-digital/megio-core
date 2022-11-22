/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
import { IRow } from '@/plugins/api/types/IRow'

export interface IUser extends IRow {
    email: string
    role: string
}