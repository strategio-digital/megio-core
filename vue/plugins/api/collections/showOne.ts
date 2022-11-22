/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import { IResponse } from '@/plugins/api/types/IResponse'
import { IShowOneParams } from '@/plugins/api/types/IShowOneParams'
import api from '@/plugins/api'
import { IRow } from '@/plugins/api/types/IRow'

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