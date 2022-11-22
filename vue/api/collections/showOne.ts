/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import api from '@/api'
import { IResponse } from '@/api/types/IResponse'
import { IShowOneParams } from '@/api/types/IShowOneParams'
import { IRow } from '@/api/types/IRow'

interface IResp extends IResponse {
    data: IRow
}

const showOne = async (collection: string, params: IShowOneParams): Promise<IResp> => {
    const resp = await api.fetch(`/${collection}/show-one`, {
        method: 'POST',
        body: JSON.stringify({
            id: params.id
        })
    })

    return { ...resp, data: resp.data }
}

export default showOne