/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
import IFormSelectItem from '@/saas/composables/form/IFormSelectItem'

export default interface IFormField {
    type: 'text' | 'select' | 'datetime',
    key: string,
    label: string
    disabled?: boolean
    value?: any
    items?: IFormSelectItem[]
}