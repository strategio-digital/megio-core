import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import api from '@/api'

const routes: Array<RouteRecordRaw> = [
    {
        path: '/',
        name: 'Intro',
        component: () => import(/* webpackChunkName: "public" */ '@/views/public/Intro.vue')
    },
    {
        path: '/login',
        name: 'Login',
        component: () => import(/* webpackChunkName: "public" */ '@/views/public/Login.vue')
    },
    {
        path: '/collections',
        name: 'Collections',
        component: () => import(/* webpackChunkName: "collections" */ '@/views/Collections.vue')
    },
    {
        path: '/users',
        name: 'Users',
        component: () => import(/* webpackChunkName: "users" */ '@/views/users/Users.vue')
    },
    {
        path: '/users/:id',
        name: 'UserDetail',
        component: () => import(/* webpackChunkName: "users" */ '@/views/users/Detail.vue')
    },
    {
        path: '/settings',
        name: 'Application',
        component: () => import(/* webpackChunkName: "settings" */ '@/views/settings/Application.vue')
    },
    {
        path: '/settings/roles',
        name: 'Roles',
        component: () => import(/* webpackChunkName: "settings" */ '@/views/settings/Roles.vue')
    },
    {
        path: '/settings/admins',
        name: 'Admins',
        component: () => import(/* webpackChunkName: "settings" */ '@/views/settings/Admins.vue')
    },
    {
        path: '/settings/emails',
        name: 'Emails',
        component: () => import(/* webpackChunkName: "settings" */ '@/views/settings/Emails.vue')
    },
    {
        path: '/settings/storage',
        name: 'Storage',
        component: () => import(/* webpackChunkName: "settings" */ '@/views/settings/Storage.vue')
    }
]

const router = createRouter({
    history: createWebHistory(import.meta.env.MODE === 'production' ? '/admin' : '/'),
    routes
})

router.beforeEach((to, from, next) => {
    const user = api.auth.currentUser()

    if (!user && to.name === 'Intro') {
       next()
    } else if (! user && to.name !== 'Login') {
        next({ name: 'Login' })
    } else if (user && (to.name === 'Login' || to.name === 'Intro')) {
        next({ name: 'Collections' })
    } else {
        next()
    }
})

export default router
