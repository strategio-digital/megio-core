/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */

declare global {
    interface Window {
        dataLayer: Array<object | string>,
    }
}

type cookieType = 'ad_storage' | 'analytics_storage' | 'necessary'

export const useAnalytics = () => {

    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' })

    function injectScript(gtmId: string) {

        const f = document.getElementsByTagName('script')[0] as HTMLScriptElement
        const j = document.createElement('script') as HTMLScriptElement

        j.async = true
        j.src = 'https://www.googletagmanager.com/gtm.js?id=' + gtmId
        f.parentNode?.insertBefore(j, f)
    }

    function gtag() {
        window.dataLayer.push(arguments)
    }

    function disableAllGtmCookies() {
        // @ts-ignore
        gtag('consent', 'default', {
            analytics_storage: 'denied',
            ad_storage: 'denied',
            personalization_storage: 'denied'
        })

        window.dataLayer.push({ event: 'consent-update' })
    }

    function enableGtmCookie(type: cookieType) {
        const text = '{"' + type + '": "granted"}'
        // @ts-ignore
        gtag('consent', 'update', JSON.parse(text))
        window.dataLayer.push({ event: 'consent-update' })
    }

    function disableGtmCookie(type: cookieType) {
        const text = '{"' + type + '": "denied"}'
        // @ts-ignore
        gtag('consent', 'update', JSON.parse(text))
        window.dataLayer.push({ event: 'consent-update' })
    }

    function trackLeadGenerate(eventLabel: string = 'contact') {
        window.dataLayer.push({
            event: 'eventTracking',
            eventCategory: 'form',
            eventAction: 'conversion',
            eventLabel: eventLabel
        })
    }

    function trackNewsletterSubscribe(eventLabel: string = 'newsletter') {
        window.dataLayer.push({
            event: 'eventTracking',
            eventCategory: 'form',
            eventAction: 'conversion',
            eventLabel: eventLabel
        })
    }

    return {
        injectScript,
        trackLeadGenerate,
        trackNewsletterSubscribe,
        enableGtmCookie,
        disableGtmCookie,
        disableAllGtmCookies
    }
}