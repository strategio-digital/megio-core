/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

import INavbar from '@/saas/components/navbar/types/INavbar'
import logo from '@/saas/assets/img/strategio.svg'

const navbar: INavbar = {
    brand: {
        title: 'Strategio SaaS',
        routeName: 'Dashboard',
        logo
    },
    items: [
        { title: 'Přehled', routeName: 'Dashboard', activePrefix: '/dashboard', icon: 'mdi-view-dashboard' },
        { title: 'Kolekce', routeName: 'Collections', activePrefix: '/collections', icon: 'mdi-database' },
        { title: 'Uživatelé', routeName: 'Users', activePrefix: '/users', icon: 'mdi-account-multiple' },
        { title: 'Nastavení', routeName: 'Application', activePrefix: '/settings', icon: 'mdi-hammer-screwdriver' }
    ]
}

export default navbar