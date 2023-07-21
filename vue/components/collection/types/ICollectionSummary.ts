/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
import { IRow } from '@/saas/api/collections/types/IRow'

export default interface ICollectionSummary {
    collectionName: string;
    onFirstColumnClick: (collection: string, row: IRow) => void;
}