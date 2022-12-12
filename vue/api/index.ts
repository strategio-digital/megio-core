/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import { IResponse } from '@/api/types/IResponse'
import adminLoginByEmail from '@/api/auth/adminLoginByEmail'
import loginByEmail from '@/api/auth/loginByEmail'
import logout from '@/api/auth/logout'
import currentUser from '@/api/auth/currentUser'
import show from '@/api/collections/show'
import showOne from '@/api/collections/showOne'
import remove from '@/api/collections/remove'
import revokeToken from '@/api/auth/revokeToken'

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

    if(user && resp.status === 401) {
        logout()
        window.location.href = import.meta.env.MODE === 'production' ? '/admin' : '/'
    }

    return {
        success: resp.ok,
        data: json,
        errors: json.errors ? json.errors : []
    }
}

export default {
    fetch: fetchApi,
    collections: {
        show,
        showOne,
        remove
    },
    auth: {
        currentUser,
        adminLoginByEmail,
        loginByEmail,
        logout,
        revokeToken
    }
}