import { createRouter as create, createWebHistory, RouteRecordRaw } from 'vue-router'
import api from '@/saas/api'
import { hasResource } from '@/saas/api/auth/currentUser'
import { useToast } from '@/saas/components/toast/useToast'

const createRouter = (routes: RouteRecordRaw[], routeRoot: string) => {
    const router = create({ history: createWebHistory(routeRoot), routes })
    const toast = useToast()

    router.beforeEach((to, from, next) => {
        const user = api.auth.currentUser()
        const routeTo = to.name?.toString()

        // skip route if routeTo is 401
        if (user && routeTo === 'saas.view.401') {
            return next()
        }

        // redirect to dashboard if route does not exist
        if (!routeTo) {
            toast.add('You are trying to access non-existent route (404)', 'error')
            return next({ name: 'saas.view.dashboard' })
        }

        // redirect to login page if user is not logged in
        if (user && ['saas.view.login', 'saas.view.admin.login'].includes(routeTo)) {
            return next({ name: 'saas.view.dashboard' })
        }

        // redirect to dashboard if user is logged in
        if (! user && ! ['saas.view.login', 'saas.view.admin.login'].includes(routeTo)) {
            return next({ name: 'saas.view.login' })
        }

        // redirect to 401 page if user is logged in but does not have the required resource
        if (user && ! hasResource(routeTo)) {
            const message = `This view-resource '${routeTo}' is not allowed for current user`
            toast.add(message, 'error', null)
            return next({ name: 'saas.view.401' })
        }

        return next()
    })

    return router
}

export default createRouter
