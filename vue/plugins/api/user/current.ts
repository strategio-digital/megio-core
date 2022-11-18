/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
import { IUser } from '@/plugins/api/IUser'

const current = (): IUser | null => {
    const data = localStorage.getItem('strategio_saas_user')
    return data ? JSON.parse(data) : null
}

export default current