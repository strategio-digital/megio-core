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

const revokeToken = async (user_ids: string[]): Promise<IResp> => {
    const resp = await api.fetch(`/auth/revoke-token`, {
        method: 'POST',
        body: JSON.stringify({ user_ids })
    })

    return { ...resp, data: resp.data }
}

export default revokeToken