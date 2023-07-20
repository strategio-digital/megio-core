/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import { useRouter } from 'vue-router'
import { useToast } from '@/saas/components/toast/useToast'
import api from '@/saas/api'

export const useLogout = () => {
    const router = useRouter()
    const toast = useToast();

    async function logout() {
        api.auth.logout()
        await router.push({ name: 'saas.view.login' })
        toast.add('Právě jste se úspěšně odhlásili', 'warning')
    }

    return {
        logout
    }
}