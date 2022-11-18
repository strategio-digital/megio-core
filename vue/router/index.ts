import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import api from '@/plugins/api'

const routes: Array<RouteRecordRaw> = [
    {
        path: '/',
        name: 'Intro',
        component: () => import(/* webpackChunkName: "intro" */ '@/views/Intro.vue')
    },
    {
        path: '/login',
        name: 'Login',
        component: () => import(/* webpackChunkName: "login" */ '@/views/Login.vue')
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import(/* webpackChunkName: "dashboard" */ '@/views/Dashboard.vue')
    }
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})

router.beforeEach((to, from, next) => {
    const user = api.user.current()

    if (!user && to.name === 'Intro') {
       next()
    } else if (! user && to.name !== 'Login') {
        next({ name: 'Login' })
    } else if (user && (to.name === 'Login' || to.name === 'Intro')) {
        next({ name: 'Dashboard' })
    } else {
        next()
    }
})

export default router
