/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import { IResponse } from '@/saas/api/types/IResponse'
import { IResource } from '@/saas/api/resources/types/IResource'
import { IGroupedResourcesWithRoles } from '@/saas/api/resources/types/IGroupedResourcesWithRoles'
import { IResourceDiff } from '@/saas/api/resources/types/IResourceDiff'
import api from '@/saas/api'

export interface IResp extends IResponse {
    data: {
        roles: string[],
        resources: IResource[],
        grouped_resources_with_roles: IGroupedResourcesWithRoles[],
        resources_diff: IResourceDiff
    }
}

const updateRole = async (roleId: string, resourceId: string, enable: boolean): Promise<IResp> => {
    const resp = await api.fetch(`saas/resources/update-role`, {
        method: 'POST',
        body: JSON.stringify({
            role_id: roleId,
            resource_id: resourceId,
            enable
        })
    })

    return { ...resp, data: resp.data }
}

export default updateRole