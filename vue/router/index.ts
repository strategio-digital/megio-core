import { createRouter as create, createWebHistory, RouteRecordRaw } from 'vue-router'
import api from '@/saas/api'

const createRouter = (routes: RouteRecordRaw[], routeRoot: string) => {

    const router = create({
        history: createWebHistory(routeRoot),
        routes
    })

    router.beforeEach((to, from, next) => {
        const user = api.auth.currentUser()

        if (! user && (to.name !== 'Login' && to.name !== 'AdminLogin')) {
            next({ name: 'Login' })
        } else if (user && (to.name === 'Login' || to.name === 'AdminLogin')) {
            next({ name: 'Dashboard' })
        } else if (! to.name) {
            next({ name: 'Dashboard' })
        } else {
            next()
        }
    })

    return router
}

export default createRouter
