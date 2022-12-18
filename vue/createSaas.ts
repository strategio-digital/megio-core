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
import routes from '@/saas/globals/routes'
import navbar from '@/saas/globals/navbar'

import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import '@/saas/style.scss'

type SaasOptions = {
    routes?: RouteRecordRaw[]
    navbar?: INavbar
}

export const createSaas = (options: SaasOptions) => {
    const pinia = createPinia()
    const vuetify = createVuetify(vuetifyOptions)
    const router = createRouter(options.routes || routes)

    return {
        install: (app: App) => {
            app.provide('navbar', options.navbar || navbar)
            app.use(vuetify)
            app.use(pinia)
            app.use(router)
        }
    }
}