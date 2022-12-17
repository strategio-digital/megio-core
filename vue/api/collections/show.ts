/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import api from '@/saas/api'
import { IResponse } from '@/saas/api/types/IResponse'
import { IShowParams } from '@/saas/api/types/IShowParams'
import { IRow } from '@/saas/api/types/IRow'

interface IResp extends IResponse {
    data: {
        currentPage: number
        itemsCountAll: number
        itemsPerPage: number
        lastPage: number
        items: IRow[]|any[]
    }
}

const show = async (collection: string, params: IShowParams): Promise<IResp> => {
    const resp = await api.fetch(`/${collection}/show`, {
        method: 'POST',
        body: JSON.stringify({
            currentPage: params.currentPage,
            itemsPerPage: params.itemsPerPage
        })
    })

    return { ...resp, data: resp.data }
}

export default show