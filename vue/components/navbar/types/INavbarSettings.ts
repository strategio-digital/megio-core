/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
import INavbarItem from '@/saas/components/navbar/types/INavbarItem'

export default interface INavbarSettings {
    brand: {
        title: string
        routeName: string
        logo: string
    },
    items: INavbarItem[]
}