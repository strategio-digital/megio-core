/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

export const useAntiSpam = (timeOutMs: number, message: string) => {
    let ready = false;

    function isReady() {
        return ready
    }

    setTimeout(() => ready = true, timeOutMs)

    return {
        isReady,
        message
    }
}