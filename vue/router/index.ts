import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

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
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})

// router.beforeEach((to, from, next) => {
//     const { client } = usePb()
//
//     if (to.name === 'InvoiceDetailPublic') {
//         next()
//     } else if (! client.authStore.model && to.name !== 'Login') {
//         next({ name: 'Login' })
//     } else if (client.authStore.model && to.name === 'Login') {
//         next({ name: 'Invoices', params: { page: 1 } })
//     } else {
//         next()
//     }
// })

export default router
