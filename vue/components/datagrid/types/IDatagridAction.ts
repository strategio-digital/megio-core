/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

export default interface IDatagridAction {
    type: string;
    label: string;
    showOn: string[]
}