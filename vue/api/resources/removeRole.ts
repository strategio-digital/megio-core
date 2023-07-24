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
        message: string
    }
}

const removeRole = async (roleId: string): Promise<IResp> => {
    const resp = await api.fetch(`saas/resources/delete-role`, {
        method: 'DELETE',
        body: JSON.stringify({ id: roleId })
    })

    return { ...resp, data: resp.data }
}

export default removeRole