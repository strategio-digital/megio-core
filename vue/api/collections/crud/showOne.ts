/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import api from '@/saas/api'
import { IResponse } from '@/saas/api/types/IResponse'
import { IShowOneParams } from '@/saas/api/collections/types/IShowOneParams'
import { IRow } from '@/saas/api/collections/types/IRow'
import { ISchema } from '@/saas/api/collections/types/ISchema'

export interface IResp extends IResponse {
    data: IRow | any,
    schema?: ISchema
}

const showOne = async (params: IShowOneParams): Promise<IResp> => {
    const resp = await api.fetch(`saas/collections/show-one`, {
        method: 'POST',
        body: JSON.stringify(params)
    })

    return { ...resp, data: resp.data }
}

export default showOne