/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import api from '@/saas/api'
import { IResponse } from '@/saas/api/types/IResponse'

interface IResp extends IResponse {
    data: {
        items: string[]
    }
}

const navbar = async (): Promise<IResp> => {
    const resp = await api.fetch(`saas/metadata/navbar`, { method: 'POST' })
    return { ...resp, data: resp.data }
}

export default navbar