/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
import { IRole } from '@/saas/api/resources/types/IRole'
import { IResource } from '@/saas/api/resources/types/IResource'
import { IGroupedResourcesWithRoles } from '@/saas/api/resources/types/IGroupedResourcesWithRoles'
import { IResourceDiff } from '@/saas/api/resources/types/IResourceDiff'

export interface IResponseData {
    roles: IRole[],
    resources: IResource[],
    grouped_resources_with_roles: IGroupedResourcesWithRoles[],
    resources_diff: IResourceDiff
}