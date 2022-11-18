/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import api from '@/plugins/api'
import { IResponse } from '@/plugins/api/IResponse'
import { IUser } from '@/plugins/api/IUser'

interface IResp extends IResponse {
    data: IUser
}

const adminLoginByEmail = async (email: string, password: string): Promise<IResp> => {
    const resp = await api.fetch('/user/login/email', {
        method: 'POST',
        body: JSON.stringify({ email, password })
    })

    if (resp.success && resp.data.user_role === 'admin') {
        localStorage.setItem('strategio_saas_user', JSON.stringify(resp.data))
    }

    return { ...resp, data: resp.data }
}

export default adminLoginByEmail