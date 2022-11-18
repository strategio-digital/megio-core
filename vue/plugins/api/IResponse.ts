/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

export interface IResponse {
    data?: any,
    success: boolean
    errors: string[]
}