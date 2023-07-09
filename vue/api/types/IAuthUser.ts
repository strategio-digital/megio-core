/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

export interface IAuthUser {
    bearer_token: string
    bearer_token_id: string
    user: {
        id: string
        email: string
        roles: string[]
        resources: string[]|null
    }
}