/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import api from '@/saas/api'
import { IResponse } from '@/saas/api/types/IResponse'

interface IResp extends IResponse {
    data: {
        message: string
    }
}

const remove = async (tableName: string, ids: string[]): Promise<IResp> => {
    const resp = await api.fetch(`/saas/crud/delete`, {
        method: 'DELETE',
        body: JSON.stringify({
            table: tableName,
            ids
        })
    })

    return { ...resp, data: resp.data }
}

export default remove