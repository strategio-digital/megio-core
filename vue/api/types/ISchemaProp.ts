/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

export interface ISchemaProp {
    name: string
    // TODO: https://www.doctrine-project.org/projects/doctrine-orm/en/2.15/reference/basic-mapping.html#doctrine-mapping-types
    type: 'string' | 'text' | 'json' | 'bool' | 'DateTime'
    nullable: boolean
    maxLength: null | number
}