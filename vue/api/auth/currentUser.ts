/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
import { IAuthUser } from '@/saas/api/types/IAuthUser'

const currentUser = (): IAuthUser | null => {
    const data = localStorage.getItem('strategio_saas_user')
    return data ? JSON.parse(data) : null
}

export default currentUser