/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import loginByEmail from '@/saas/api/auth/loginByEmail'
import logout from '@/saas/api/auth/logout'
import currentUser from '@/saas/api/auth/currentUser'
import show from '@/saas/api/collections/crud/show'
import showOne from '@/saas/api/collections/crud/showOne'
import remove from '@/saas/api/collections/crud/remove'
import revokeToken from '@/saas/api/auth/revokeToken'
import navbar from '@/saas/api/collections/meta/navbar'
import showResources from '@/saas/api/resources/show'
import updateResources from '@/saas/api/resources/update'
import updateRole from '@/saas/api/resources/updateRole'
import removeRole from '@/saas/api/resources/removeRole'
import createRole from '@/saas/api/resources/createRole'

import { IResponse } from '@/saas/api/types/IResponse'
import { useToast } from '@/saas/components/toast/useToast'

const endpoint = (import.meta as any).env.DEV ? 'http://localhost:8090/' : '/'
const toast = useToast()

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

    if (resp.status < 200 || resp.status > 299) {
        json.errors.map((message: string) => toast.add(message, 'error'))
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
        remove,
        navbar
    },
    auth: {
        currentUser,
        loginByEmail,
        logout,
        revokeToken
    },
    resources: {
        show: showResources,
        update: updateResources,
        createRole,
        updateRole,
        removeRole,
    }
}