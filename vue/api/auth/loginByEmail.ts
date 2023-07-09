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

const loginByEmail = async (email: string, password: string, source: string): Promise<IResp> => {
    const resp = await api.fetch('saas/auth/email', {
        method: 'POST',
        body: JSON.stringify({ source, email, password })
    })

    if (resp.success && (resp.data.roles.includes('admin') || resp.data.resources?.length !== 0)) {
        localStorage.setItem('strategio_saas_user', JSON.stringify(resp.data))
    }

    return { ...resp, data: resp.data }
}

export default loginByEmail