/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
import { IAuthUser } from '@/saas/api/types/IAuthUser'

const currentUser = (): IAuthUser | null => {
    const data = localStorage.getItem('strategio_saas_user')
    return data ? JSON.parse(data) : null
}

export const currentUserResources = (): string[] => {
    const user = currentUser()
    return user?.user.resources || []
}

export const currentUserRoles = () => {
    const user = currentUser()
    return user?.user.roles || []
}

export const hasRole = (role: string): boolean => {
    return currentUserRoles().includes(role)
}

export const hasResource = (resource: string): boolean => {
    if (currentUserRoles().includes('admin')) {
        return true
    }

    return currentUserResources().includes(resource)
}

export default currentUser