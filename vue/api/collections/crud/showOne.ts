/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import api from '@/saas/api'
import { IResponse } from '@/saas/api/types/IResponse'
import { IShowOneParams } from '@/saas/api/types/IShowOneParams'
import { IRow } from '@/saas/api/types/IRow'
import { ISchemaRow } from '@/saas/api/types/ISchemaRow'

export interface IResp extends IResponse {
    data: IRow | any,
    schema?: ISchemaRow[]
}

const showOne = async (params: IShowOneParams): Promise<IResp> => {
    const resp = await api.fetch(`/saas/crud/show-one`, {
        method: 'POST',
        body: JSON.stringify(params)
    })

    return { ...resp, data: resp.data }
}

export default showOne