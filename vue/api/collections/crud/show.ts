/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import api from '@/saas/api'
import { IResponse } from '@/saas/api/types/IResponse'
import { IShowParams } from '@/saas/api/collections/types/IShowParams'
import { IRow } from '@/saas/api/collections/types/IRow'
import { IPagination } from '@/saas/api/collections/types/IPagination'
import { ISchema } from '@/saas/api/collections/types/ISchema'

export interface IResp extends IResponse {
    data: {
        pagination: IPagination
        items: IRow[]
        schema?: ISchema
    }
}

const show = async (params: IShowParams): Promise<IResp> => {
    const resp = await api.fetch(`saas/collections/show`, {
        method: 'POST',
        body: JSON.stringify(params)
    })

    return { ...resp, data: resp.data }
}

export default show