/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import { IResponse } from '@/saas/api/types/IResponse'
import { IResource } from '@/saas/api/resurces/types/IResource'
import { IGroupedResourcesWithRoles } from '@/saas/api/resurces/types/IGroupedResourcesWithRoles'
import api from '@/saas/api'

export interface IResp extends IResponse {
    data: {
        roles: string[],
        resources: IResource[],
        grouped_resources_with_roles: IGroupedResourcesWithRoles[],
    }
}

const show = async (): Promise<IResp> => {
    const resp = await api.fetch(`saas/resources/show`, { method: 'POST' })
    return { ...resp, data: resp.data }
}

export default show