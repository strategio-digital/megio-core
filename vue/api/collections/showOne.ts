/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import api from '@/saas/api'
import { IResponse } from '@/saas/api/types/IResponse'
import { IShowOneParams } from '@/saas/api/types/IShowOneParams'
import { IRow } from '@/saas/api/types/IRow'

interface IResp extends IResponse {
    data: IRow|any
}

const showOne = async (tableName: string, params: IShowOneParams): Promise<IResp> => {
    const resp = await api.fetch(`/crud/show-one`, {
        method: 'POST',
        body: JSON.stringify({
            table: tableName,
            id: params.id
        })
    })

    return { ...resp, data: resp.data }
}

export default showOne