import { createRouter as create, createWebHistory, RouteRecordRaw } from 'vue-router'
import api from '@/saas/api'

const createRouter = (routes: RouteRecordRaw[]) => {

    const router = create({
        history: createWebHistory('/admin'),
        routes
    })

    router.beforeEach((to, from, next) => {
        const user = api.auth.currentUser()

        if (! user && to.name === 'Intro') {
            next()
        } else if (! user && to.name !== 'Login') {
            next({ name: 'Login' })
        } else if (user && (to.name === 'Login' || to.name === 'Intro')) {
            next({ name: 'Collections' })
        } else {
            next()
        }
    })

    return router
}

export default createRouter
