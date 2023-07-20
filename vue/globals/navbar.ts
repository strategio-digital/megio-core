/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import INavbarSettings from '@/saas/components/navbar/types/INavbarSettings'
import logo from '@/saas/assets/img/strategio.svg'
import { COLLECTION_EMPTY_ROUTE } from '@/saas/components/navbar/types/Constants'

const navbar: INavbarSettings = {
    brand: {
        title: 'Strategio SaaS',
        routeName: 'saas.view.dashboard',
        logo
    },
    items: [
        {
            title: 'Přehled',
            activePrefix: '/dashboard',
            icon: 'mdi-view-dashboard',
            route: {
                name: 'saas.view.dashboard'
            }
        },
        {
            title: 'Kolekce',
            activePrefix: '/collections',
            icon: 'mdi-database',
            route: {
                name: 'saas.view.collections',
                params: {
                    name: COLLECTION_EMPTY_ROUTE
                }
            }
        },
        {
            title: 'Nastavení',
            activePrefix: '/settings',
            icon: 'mdi-hammer-screwdriver',
            route: {
                name: 'saas.view.application'
            }
        }
    ]
}

export default navbar