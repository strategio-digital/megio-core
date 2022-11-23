/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import api from '@/api'
import { IResponse } from '@/api/types/IResponse'

interface IResp extends IResponse {
    data: {
        message: string
    }
}

const remove = async (collection: string, ids: string[]): Promise<IResp> => {
    const resp = await api.fetch(`/${collection}/delete`, {
        method: 'DELETE',
        body: JSON.stringify({ ids })
    })

    return { ...resp, data: resp.data }
}

export default remove