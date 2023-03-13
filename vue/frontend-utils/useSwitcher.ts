/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

export const useSwitcher = (el: HTMLDivElement): void => {
    const buttons: HTMLButtonElement[] = Array.from(el.querySelectorAll('[data-switcher-btn]'))
    const targets: HTMLDivElement[] = Array.from(el.querySelectorAll('[data-switcher-wrapper] > div'))

    buttons.forEach((button) => button.addEventListener('click', () => {
        const target = button.dataset.switcherBtn as string
        const targetEl = el.querySelector(`[id="${target}"]`) as HTMLDivElement

        buttons.map((b) => b.classList.remove('active'))
        button.classList.add('active')

        targets.map((t) => t.classList.remove('active'))
        targetEl.classList.add('active')
    }))
}
