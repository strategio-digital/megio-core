/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

// SaaS Admin
import { createApp } from 'vue'
import App from '@/saas/App.vue'
import { createSaas } from '@/saas/createSaas'
//import routes from '@/saas/router/routes'

// routes.push( {
//     path: '/users/:id',
//     name: 'UserDetail',
//     component: () => import(/* webpackChunkName: "users" */ '@/saas/views/users/Detail.vue')
// })

const saas = createSaas({ /*routes*/ });
createApp(App).use(saas).mount('#app-saas')