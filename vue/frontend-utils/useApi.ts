/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

export const useApi = (apiUri: string | null = null) => {

    const defaultEndpoint = window.location.host.includes('localhost')
        ? 'https://demo.contentio.app/api'
        : 'https://strategio.contentio.app/api'

    const apiEndpoint = apiUri ? apiUri : defaultEndpoint

    const fetchApi = async (uri: string, options: RequestInit): Promise<{
        data: any,
        errors: [],
        success: boolean
    }> => {
        const info: RequestInit = {
            ...options,
            headers: {
                ...options?.headers,
                'Content-Type': 'application/json'
            }
        }

        const resp = await fetch(apiEndpoint + uri, info)
        const json = await resp.json()

        return {
            success: resp.ok,
            data: json,
            errors: json.errors ? json.errors : []
        }
    }

    return {
        fetchApi
    }
}
