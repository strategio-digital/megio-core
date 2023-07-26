/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import { IResponse } from '@/saas/api/types/IResponse'
import { IResponseData } from '@/saas/api/resources/types/IResponseData'
import api from '@/saas/api'

export interface IResp extends IResponse {
    data: IResponseData
}

const show = async (viewResources: string[] | null = null): Promise<IResp> => {
    const resp = await api.fetch(`saas/resources/show`, {
        method: 'POST',
        body: JSON.stringify({
            view_resources: viewResources,
            make_view_diff: true
        })
    })
    return { ...resp, data: resp.data }
}

export default show