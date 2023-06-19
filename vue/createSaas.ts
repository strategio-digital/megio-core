/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
import { App } from 'vue'
import { createPinia } from 'pinia'
import { createVuetify } from 'vuetify'
import { RouteRecordRaw } from 'vue-router'
import { vuetifyOptions } from '@/saas/plugins/vuetify'
import createRouter from '@/saas/router'
import INavbar from '@/saas/components/navbar/types/INavbar'
import IDgModal from '@/saas/components/datagrid-v2/types/IDgModal'
import IDgActions from '@/saas/components/datagrid-v2/types/IDgActions'

import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import '@/saas/style.scss'

type SaasOptions = {
    routeRoot: string
    routes: RouteRecordRaw[]
    navbar: INavbar,
    actions: IDgActions
    modals: IDgModal[]
}

export const createSaas = (options: SaasOptions) => {
    const pinia = createPinia()
    const vuetify = createVuetify(vuetifyOptions)
    const router = createRouter(options.routes, options.routeRoot)

    return {
        install: (app: App) => {
            app.provide('navbar', options.navbar)
            app.provide('actions', options.actions)
            app.provide('modals', options.modals)
            app.use(vuetify)
            app.use(pinia)
            app.use(router)
        }
    }
}