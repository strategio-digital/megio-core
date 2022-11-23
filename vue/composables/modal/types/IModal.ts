/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
import { Ref } from 'vue'

export interface IModal {
    loading: Ref,
    open: Ref,
    toggleOpen: Function
    toggleLoading: Function
}
