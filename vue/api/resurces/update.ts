/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import { IResponse } from '@/saas/api/types/IResponse'
import api from '@/saas/api'
import { IResource } from '@/saas/api/resurces/types/IResource'
import { IGroupedResourcesWithRoles } from '@/saas/api/resurces/types/IGroupedResourcesWithRoles'

export interface IResp extends IResponse {
    data: {
        roles: string[],
        resources: IResource[],
        grouped_resources_with_roles: IGroupedResourcesWithRoles[],
    }
}

const updateViewResources = async (addResources: string[], removeResources: string[]): Promise<IResp> => {
    const resp = await api.fetch(`saas/resources/update-view-resources`, {
        method: 'POST',
        body: JSON.stringify({
            addResources,
            removeResources
        })
    })

    return { ...resp, data: resp.data }
}

export default updateViewResources