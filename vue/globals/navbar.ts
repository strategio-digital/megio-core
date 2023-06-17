/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import INavbar from '@/saas/components/navbar/types/INavbar'
import logo from '@/saas/assets/img/strategio.svg'
import { COLLECTION_EMPTY_ROUTE } from '@/saas/components/navbar/types/Constants'

const navbar: INavbar = {
    brand: {
        title: 'Strategio SaaS',
        routeName: 'Dashboard',
        logo
    },
    items: [
        {
            title: 'Přehled',
            activePrefix: '/dashboard',
            icon: 'mdi-view-dashboard',
            route: {
                name: 'Dashboard'
            }
        },
        {
            title: 'Kolekce',
            activePrefix: '/collections',
            icon: 'mdi-database',
            route: {
                name: 'Collections',
                params: {
                    name: COLLECTION_EMPTY_ROUTE
                }
            }
        },
        {
            title: 'Uživatelé',
            activePrefix: '/users',
            icon: 'mdi-account-multiple',
            route: {
                name: 'Users'
            }
        },
        {
            title: 'Nastavení',
            activePrefix: '/settings',
            icon: 'mdi-hammer-screwdriver',
            route: {
                name: 'Application'
            }
        }
    ]
}

export default navbar