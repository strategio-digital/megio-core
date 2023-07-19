/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
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