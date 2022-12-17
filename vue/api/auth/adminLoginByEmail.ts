/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import api from '@/saas/api'
import { IResponse } from '@/saas/api/types/IResponse'
import { IAuthUser } from '@/saas/api/types/IAuthUser'

interface IResp extends IResponse {
    data: IAuthUser
}

const adminLoginByEmail = async (email: string, password: string): Promise<IResp> => {
    const resp = await api.fetch('/auth/email', {
        method: 'POST',
        body: JSON.stringify({ email, password })
    })

    if (resp.success && resp.data.user_role === 'admin') {
        localStorage.setItem('strategio_saas_user', JSON.stringify(resp.data))
    }

    return { ...resp, data: resp.data }
}

export default adminLoginByEmail