/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
import { App } from 'vue'
import { createVuetify } from 'vuetify'
import { RouteRecordRaw } from 'vue-router'
import { vuetifyOptions } from '@/saas/plugins/vuetify'
import INavbarSettings from '@/saas/components/navbar/types/INavbarSettings'
import ICollectionSettings from '@/saas/components/collection/types/ICollectionSettings'
import IDatagridSettings from '@/saas/components/datagrid/types/IDatagridSettings'
import createRouter from '@/saas/router'
import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import '@/saas/style.scss'

export type SaasOptions = {
    root: string
    routes: RouteRecordRaw[]
    navbar: INavbarSettings,
    datagrid: IDatagridSettings
    collection: ICollectionSettings
}

export const createSaas = (options: SaasOptions) => {
    const vuetify = createVuetify(vuetifyOptions)
    const router = createRouter(options.routes, options.root)

    return {
        install: (app: App) => {
            app.provide('navbar', options.navbar)
            app.provide('datagrid-actions', options.datagrid.actions)
            app.provide('datagrid-modals', options.datagrid.modals)
            app.provide('datagrid-columns', options.datagrid.columns)
            app.provide('collection-summaries', options.collection.summaries(router))
            app.use(vuetify)
            app.use(router)
        }
    }
}