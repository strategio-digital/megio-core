/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

// Extended SaaS Admin panel
import { createApp } from 'vue'
import App from '@/saas/App.vue'
import { createSaas } from '@/saas/createSaas'
// import routes from '@/saas/router/routes'
//
// const exclude = ['Users']
// const customRoutes = routes.filter(route => !exclude.includes(route.name as string))
//
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

const saas = createSaas({ /*routes: customRoutes*/ })
createApp(App).use(saas).mount('#app-saas')