/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import api from '@/saas/api'
import { IResponse } from '@/saas/api/types/IResponse'
import { IRemoveParams } from '@/saas/api/types/IRemoveParams'

export interface IResp extends IResponse {
    data: {
        message: string
    }
}

const remove = async (params: IRemoveParams): Promise<IResp> => {
    const resp = await api.fetch(`/saas/crud/delete`, {
        method: 'DELETE',
        body: JSON.stringify(params)
    })

    return { ...resp, data: resp.data }
}

export default remove