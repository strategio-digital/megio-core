/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

export interface IUser {
    user_id: string,
    bearer_token: string
    user_email: string,
    user_role: string,
}