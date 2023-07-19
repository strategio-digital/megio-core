/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import { RouteLocationNamedRaw } from 'vue-router'

export default interface INavbarItem {
    title: string,
    activePrefix: string,
    icon: string,
    route: RouteLocationNamedRaw,
}