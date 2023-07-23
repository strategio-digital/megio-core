/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import { useRouter } from 'vue-router'
import { useToast } from '@/saas/components/toast/useToast'
import api from '@/saas/api'
import { hasRole } from '@/saas/api/auth/currentUser'

export const useLogout = () => {
    const router = useRouter()
    const toast = useToast();

    async function logout() {
        const name = hasRole('admin') ? 'saas.view.admin.login' : 'saas.view.login'
        api.auth.logout()
        await router.push({ name })
        toast.add('Právě jste se úspěšně odhlásili', 'warning')
    }

    return {
        logout
    }
}