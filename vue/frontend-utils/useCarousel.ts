/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

export type Options = {
    autoPlay: { speed: number, enabled: boolean }
}

export const useCarousel = (el: HTMLDivElement, options: Options) => {
    const items = Array.from(el.querySelectorAll('[data-carousel="item"]')) as HTMLDivElement[]
    const next = el.querySelector('[data-carousel="next"]') as HTMLButtonElement
    const prev = el.querySelector('[data-carousel="prev"]') as HTMLButtonElement
    const counter = el.querySelector('[data-carousel="counter"]') as HTMLElement

    let currentIndex = 0
    let autoPlayInterval: number | null = null

    function create(): void {
        next.addEventListener('click', () => {
            handleNext()
            restartAutoplay()
        })

        prev.addEventListener('click', () => {
            handlePrev()
            restartAutoplay()
        })

        items.map(el => hideItem(el))
        showItem(items[0])

        restartAutoplay()
        updateCounter()
    }

    function handleNext(): void {
        const nextIndex = currentIndex === items.length - 1 ? 0 : currentIndex + 1
        hideItem(items[currentIndex])
        showItem(items[nextIndex])
        currentIndex = nextIndex
        updateCounter()
    }

    function handlePrev(): void {
        const prevIndex = currentIndex === 0 ? items.length - 1 : currentIndex - 1
        hideItem(items[currentIndex])
        showItem(items[prevIndex])
        currentIndex = prevIndex
        updateCounter()
    }

    function updateCounter(): void {
        counter.innerText = (currentIndex + 1) + ' / ' + items.length
    }

    function hideItem(item: HTMLDivElement): void {
        item.classList.add('carousel-hide')
        item.classList.remove('carousel-show')
    }

    function showItem(item: HTMLDivElement): void {
        item.classList.remove('carousel-hide')
        item.classList.add('carousel-show')
    }

    function restartAutoplay(): void {
        if (options.autoPlay.enabled) {
            if (autoPlayInterval) {
                clearInterval(autoPlayInterval)
            }

            autoPlayInterval = setInterval(handleNext, options.autoPlay.speed)
        }
    }

    return {
        create,
        handleNext,
        handlePrev,
        getStats: () => {
            return {
                currentIndex
            }
        }
    }
}
