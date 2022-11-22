/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import { IResponse } from '@/plugins/api/types/IResponse'
import adminLoginByEmail from '@/plugins/api/auth/adminLoginByEmail'
import loginByEmail from '@/plugins/api/auth/loginByEmail'
import logout from '@/plugins/api/auth/logout'
import currentUser from '@/plugins/api/auth/currentUser'
import showAll from '@/plugins/api/collections/showAll'
import showOne from '@/plugins/api/collections/showOne'

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

    const user = currentUser()

    if (user) {
        info.headers = { ...info.headers, 'Authorization': `Bearer ${user.bearer_token}` }
    }

    const resp = await fetch(url, info)
    const json = await resp.json()

    return {
        success: resp.ok,
        data: json,
        errors: json.errors ? json.errors : []
    }
}

export default {
    fetch: fetchApi,
    collections: {
        showAll,
        showOne
    },
    auth: {
        currentUser,
        adminLoginByEmail,
        loginByEmail,
        logout,
    }
}