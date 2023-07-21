/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import api from '@/saas/api'
import { IResponse } from '@/saas/api/types/IResponse'

export type Role = {
    id: string
    name: string
    enabled: boolean
}

export type Resource = {
    id: string
    name: string
    type: string
}

export type GroupedResourcesWithRoles = {
    [key: string]: Resource & {
        roles: Role[]
    }
}

interface IResp extends IResponse {
    data: {
        roles: string[],
        resources: Resource[],
        grouped_resources_with_roles: GroupedResourcesWithRoles[],
    }
}

const show = async (): Promise<IResp> => {
    const resp = await api.fetch(`saas/resources/show`, { method: 'POST' })
    return { ...resp, data: resp.data }
}

export default show