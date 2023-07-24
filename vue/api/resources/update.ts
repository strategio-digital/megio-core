/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import { IResponse } from '@/saas/api/types/IResponse'
import { IResource } from '@/saas/api/resources/types/IResource'
import { IRole } from '@/saas/api/resources/types/IRole'
import { IGroupedResourcesWithRoles } from '@/saas/api/resources/types/IGroupedResourcesWithRoles'
import { IResourceDiff } from '@/saas/api/resources/types/IResourceDiff'
import api from '@/saas/api'

export interface IResp extends IResponse {
    data: {
        roles: IRole[],
        resources: IResource[],
        grouped_resources_with_roles: IGroupedResourcesWithRoles[],
        resources_diff: IResourceDiff
    }
}

const update = async (viewResources: string[]): Promise<IResp> => {
    const resp = await api.fetch(`saas/resources/update`, {
        method: 'POST',
        body: JSON.stringify({
            view_resources: viewResources,
            make_view_diff: true
        })
    })

    return { ...resp, data: resp.data }
}

export default update