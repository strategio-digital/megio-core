/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import { IResponse } from '@/plugins/api/types/IResponse'
import { IShowAllParams } from '@/plugins/api/types/IShowAllParams'
import api from '@/plugins/api'
import { IRow } from '@/plugins/api/types/IRow'

interface IResp extends IResponse {
    data: {
        currentPage: number
        itemsCountAll: number
        itemsPerPage: number
        lastPage: number
        items: IRow[]
    }
}

const showAll = async (collection: string, params: IShowAllParams): Promise<IResp> => {
    const resp = await api.fetch(`/${collection}/show-all`, {
        method: 'POST',
        body: JSON.stringify({
            currentPage: params.currentPage,
            itemsPerPage: params.itemsPerPage
        })
    })

    return { ...resp, data: resp.data }
}

export default showAll