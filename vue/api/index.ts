/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import { IResponse } from '@/saas/api/types/IResponse'
import loginByEmail from '@/saas/api/auth/loginByEmail'
import logout from '@/saas/api/auth/logout'
import currentUser from '@/saas/api/auth/currentUser'
import show from '@/saas/api/collections/crud/show'
import showOne from '@/saas/api/collections/crud/showOne'
import remove from '@/saas/api/collections/crud/remove'
import revokeToken from '@/saas/api/auth/revokeToken'
import navbar from '@/saas/api/collections/meta/navbar'

const endpoint = (import.meta as any).env.DEV ? 'http://localhost:8090/' : '/'

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

    // TODO: do not logout, but show error alert
    // if(user && resp.status === 401) {
    //     logout()
    //     window.location.href = '/'
    // }

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
    metadata: {
        navbar
    },
    auth: {
        currentUser,
        loginByEmail,
        logout,
        revokeToken
    },
}