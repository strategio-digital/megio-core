/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
import { toast } from 'vuetify-sonner'

export const useToast = () => {
    function add(message: string, color: 'error' | 'success' | 'warning', timeout: number | null = 7000) {
        toast(message, {
            duration: timeout === null ? Number.POSITIVE_INFINITY : timeout,
            cardProps: { color },
            action: {
                label: 'Close',
                buttonProps: {
                    color: 'white'
                },
                onClick() {}
            }
        })
    }

    return { add }
}