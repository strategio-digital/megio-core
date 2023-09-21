/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
import { useApi } from '@/saas/frontend-utils/useApi'

type Params = {
    path: string,
    method: string,
    quality: number,
    width: number | null
    height: number | null
}

interface IOnCreated {
    (params: Params, src: string): void
}

export const useThumbnail = (onCreated?: IOnCreated, apiUri: string | null = null) => {

    const api = useApi(apiUri)

    const requestQueue: string[] = []

    function replaceDomain(src: string): string {
        return src.replace(/^http[s]?:\/\/.+?\//, '')
    }

    function replacePath(src: string): string {
        return src.replace(replaceDomain(src), '');
    }

    function extractParams(src: string): Params | null {
        // example: _develop_contentio_app/article/56/test--thumb-[SHRINK_ONLY-80-100x100].jpeg

        const regex = /(.*)\-\-thumb\[([A-Z\_]+)\-([0-9]{1,3})\-([0-9]*)x([0-9]*)\](.*)/
        const path = replaceDomain(src)
        const groups = regex.exec(path) as string[]

        if (groups === null) {
            return null
        }

        const thumbPath = (groups[1] + groups[6])
        const width = isNaN(parseInt(groups[4])) ? null : parseInt(groups[4])
        const height = isNaN(parseInt(groups[5])) ? null : parseInt(groups[5])

        return {
            path: thumbPath,
            method: groups[2],
            quality: parseInt(groups[3]),
            width,
            height
        }
    }

    function getImagesBySameSrc(src: string): HTMLImageElement[] {
        const nodes: NodeListOf<HTMLImageElement> = document.querySelectorAll('img[data-thumb]')
        return Array.from(nodes).filter(node => node.src === src)
    }

    async function getThumbnail(src: string, params: Params): Promise<void> {
        const sameImages = getImagesBySameSrc(src)

        try {
            const resp = await api.fetchApi('/image/create', {
                method: 'POST',
                body: JSON.stringify(params)
            })

            if (! resp.success) return

            sameImages.map(node => node.src = replacePath(src) + resp.data.path)
        } catch (e) {
            console.error(e)
        }

        if (onCreated) onCreated(params, src)
    }


    function registerEvents() {
        document.querySelectorAll('img[data-thumb]').forEach((node) => {
            node.addEventListener('error', async (e) => {
                const target = e.target as HTMLImageElement
                if (target.tagName !== 'IMG') return

                if (requestQueue.filter(src => target.src === src).length !== 0) return
                requestQueue.push(target.src)

                const params = extractParams(target.src)
                if (! params) return

                await getThumbnail(target.src, params)
            })
        })
    }

    return {
        registerEvents
    }
}