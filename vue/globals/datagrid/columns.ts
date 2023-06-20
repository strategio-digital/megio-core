/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

import StringRenderer from '@/saas/components/datagrid/column/native/StringRenderer.vue'
import BooleanRenderer from '@/saas/components/datagrid/column/native/BooleanRenderer.vue'
import UnknownRenderer from '@/saas/components/datagrid/column/native/UnknownRenderer.vue'
import DateTimeRenderer from '@/saas/components/datagrid/column/native/DateTimeRenderer.vue'
import NumberRenderer from '@/saas/components/datagrid/column/native/NumberRenderer.vue'
import CBlobRenderer from '@/saas/components/datagrid/column/native/CBlobRenderer.vue'
import IDatagridSettings from '@/saas/components/datagrid/types/IDatagridSettings'

const columns: IDatagridSettings['columns'] = [
    {
        types: ['@unknown', 'blob'],
        component: UnknownRenderer
    },
    {
        types: ['boolean'],
        component: BooleanRenderer
    },
    {
        types: ['guid', 'string', 'text', 'json', 'decimal', 'bigint'],
        component: StringRenderer
    },
    {
        types: ['datetime', 'datetimez', 'date', 'time'],
        component: DateTimeRenderer
    },
    {
        types: ['object', 'array', 'simple_array', 'json_array'],
        component: CBlobRenderer
    },
    {
        types: ['integer', 'smallint', 'float'],
        component: NumberRenderer
    }
]

export default columns