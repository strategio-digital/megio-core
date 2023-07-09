/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

export interface IAuthUser {
    bearer_token: string
    token_id: string
    id: string
    email: string
    roles: string[]
    resources: string[]|null
}