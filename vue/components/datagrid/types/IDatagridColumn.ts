/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import { Component } from 'vue';

export default interface IDatagridColumn {
    types: string[]
    component: Component
}