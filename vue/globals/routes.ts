/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
import { RouteRecordRaw } from 'vue-router'

const routes: Array<RouteRecordRaw> = [
    {
        path: '/',
        name: 'saas.view.login',
        props: { source: 'user', title: 'Přihlášení' },
        meta: { auth: false },
        component: () => import(/* webpackChunkName: "public" */ '@/saas/views/public/Login.vue')
    },
    {
        path: '/admin',
        name: 'saas.view.admin.login',
        props: { source: 'admin', title: 'Admin přihlášení' },
        meta: { auth: false },
        component: () => import(/* webpackChunkName: "public" */ '@/saas/views/public/Login.vue')
    },
    {
        path: '/dashboard',
        name: 'saas.view.dashboard',
        component: () => import(/* webpackChunkName: "dashboard" */ '@/saas/views/Dashboard.vue')
    },
    {
        path: '/collections/:name',
        name: 'saas.view.collections',
        component: () => import(/* webpackChunkName: "collections" */ '@/saas/views/Collections.vue')
    },
    {
        path: '/settings',
        name: 'saas.view.settings.application',
        meta: { inResources: false },
        component: () => import(/* webpackChunkName: "settings" */ '@/saas/views/settings/Application.vue')
    },
    {
        path: '/settings/resources',
        name: 'saas.view.settings.resources',
        meta: { inResources: false },
        component: () => import(/* webpackChunkName: "settings" */ '@/saas/views/settings/Resources.vue')
    },
    {
        path: '/settings/admins',
        name: 'saas.view.settings.admins',
        meta: { inResources: false },
        component: () => import(/* webpackChunkName: "settings" */ '@/saas/views/settings/Admins.vue')
    },
    {
        path: '/settings/emails',
        name: 'saas.view.settings.emails',
        meta: { inResources: false },
        component: () => import(/* webpackChunkName: "settings" */ '@/saas/views/settings/Emails.vue')
    },
    {
        path: '/settings/storage',
        name: 'saas.view.settings.storage',
        meta: { inResources: false },
        component: () => import(/* webpackChunkName: "settings" */ '@/saas/views/settings/Storage.vue')
    },
    {
        path: '/401',
        name: 'saas.view.401',
        meta: { inResources: false },
        component: () => import(/* webpackChunkName: "errors" */ '@/saas/views/error/401.vue')
    }
]

export default routes