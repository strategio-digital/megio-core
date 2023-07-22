/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
import { IResource } from '@/saas/api/resources/types/IResource'
import { IRole } from '@/saas/api/resources/types/IRole'

export interface IGroupedResourcesWithRoles {
    [key: string]: IResource & {
        roles: IRole[]
    }
}