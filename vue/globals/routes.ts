/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
import { RouteRecordRaw } from 'vue-router'

const routes: Array<RouteRecordRaw> = [
    {
        path: '/',
        name: 'Login',
        component: () => import(/* webpackChunkName: "public" */ '@/saas/views/public/Login.vue')
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import(/* webpackChunkName: "dashboard" */ '@/saas/views/Dashboard.vue')
    },
    {
        path: '/collections/:name',
        name: 'Collections',
        component: () => import(/* webpackChunkName: "collections" */ '@/saas/views/Collections.vue')
    },
    {
        path: '/users',
        name: 'Users',
        component: () => import(/* webpackChunkName: "users" */ '@/saas/views/users/Users.vue')
    },
    {
        path: '/settings',
        name: 'Application',
        component: () => import(/* webpackChunkName: "settings" */ '@/saas/views/settings/Application.vue')
    },
    {
        path: '/settings/roles',
        name: 'Roles',
        component: () => import(/* webpackChunkName: "settings" */ '@/saas/views/settings/Roles.vue')
    },
    {
        path: '/settings/admins',
        name: 'Admins',
        component: () => import(/* webpackChunkName: "settings" */ '@/saas/views/settings/Admins.vue')
    },
    {
        path: '/settings/emails',
        name: 'Emails',
        component: () => import(/* webpackChunkName: "settings" */ '@/saas/views/settings/Emails.vue')
    },
    {
        path: '/settings/storage',
        name: 'Storage',
        component: () => import(/* webpackChunkName: "settings" */ '@/saas/views/settings/Storage.vue')
    }
]

export default routes