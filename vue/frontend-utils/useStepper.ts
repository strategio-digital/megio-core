/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

export const useStepper = () => {
    const step2 = document.querySelector('[data-step="2"]') as HTMLDivElement
    const step3 = document.querySelector('[data-step="3"]') as HTMLDivElement

    function addClass(): void {
        step2.classList.add('active')
        step2.classList.add('active')
    }

    function removeClass(): void {
        step2.classList.remove('active')
        step3.classList.remove('active')
    }

    function registerEvents(): void {
        if (step2 && step3) {
            step3.addEventListener('mouseover', addClass)
            step3.addEventListener('click', addClass)
            step3.addEventListener('touchstart', addClass, { passive: true })

            step3.addEventListener('touchend', removeClass, { passive: true })
            step3.addEventListener('mouseleave', removeClass)
        }
    }

    return {
        registerEvents
    }
}