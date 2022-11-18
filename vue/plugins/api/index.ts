/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import { IResponse } from '@/plugins/api/IResponse'
import adminLoginByEmail from '@/plugins/api/auth/adminLoginByEmail'
import loginByEmail from '@/plugins/api/auth/loginByEmail'
import logout from '@/plugins/api/auth/logout'
import current from '@/plugins/api/user/current'

const endpoint = import.meta.env.DEV ? 'http://localhost:8090/api' : '/api'

const fetchApi = async (uri: string, options: RequestInit): Promise<IResponse> => {
    const url = endpoint + uri
    const info: RequestInit = {
        ...options,
        headers: {
            ...options?.headers,
            'Content-Type': 'application/json'
        }
    }

    const user = current()

    if (user) {
        info.headers = { ...info.headers, 'Authorization': `Bearer ${user.bearer_token}` }
    }

    const resp = await fetch(url, info)
    const json = await resp.json()

    return {
        success: resp.ok,
        data: json,
        errors: json.messages ? json.messages : []
    }
}

export default {
    fetch: fetchApi,
    user: {
        current
    },
    auth: {
        adminLoginByEmail,
        loginByEmail,
        logout
    }
}