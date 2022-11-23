/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
import { ref } from 'vue'
import { IModal } from '@/composables/modal/types/IModal'

type TToggle = 'show' | 'hide'

const useModal = (show: boolean = false): IModal => {

    const loading = ref(false)
    const open = ref(show)

    function toggleOpen(toggle: TToggle) {
        loading.value = false
        open.value = toggle === 'show'
    }

    function toggleLoading(toggle: TToggle) {
        loading.value = toggle === 'show'
    }

    return { loading, open, toggleLoading, toggleOpen }
}

export default useModal