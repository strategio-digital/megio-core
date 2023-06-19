/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

// Extended SaaS Admin panel
import { createApp } from 'vue'
import App from '@/saas/App.vue'
import { createSaas } from '@/saas/createSaas'

import navbar from '@/saas/globals/navbar'
import routes from '@/saas/globals/routes'
import actions from '@/saas/globals/datagrid/actions'
import modals from '@/saas/globals/datagrid/modals'

// Custom routes
// const exclude = ['Users']
// const customRoutes = routes.filter(route => !exclude.includes(route.name as string))
// customRoutes.push(
//     {
//         path: '/users',
//         name: 'Users',
//         component: () => import(/* webpackChunkName: "users" */ '@/assets/vue/saas/views/users/Users.vue')
//     },
//     {
//         path: '/users/:id',
//         name: 'UserDetail',
//         component: () => import(/* webpackChunkName: "users" */ '@/assets/vue/saas/views/users/Detail.vue')
//     }
// )

// Custom navbar
// navbar.items.push(
//     { title: 'Uživatelé', activePrefix: '/users', icon: 'mdi-account-multiple', route: { name:  'Users' } }
// )

const app: HTMLElement | null = document.getElementById('app-saas')

if (app) {
    const appPath = app.dataset.appPath as string

    const saas = createSaas({
        routeRoot: appPath,
        routes,
        navbar,
        actions,
        modals
    })

    createApp(App).use(saas).mount(app)
}