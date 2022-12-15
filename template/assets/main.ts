/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

// Static files
import '@/assets/img/strategio.svg'
import '@/assets/img/favicon.svg'
import '@/assets/img/favicon.png'

// Stylesheets
import '@/assets/scss/layout.scss'

// Typescript
import removeThis from '@/assets/ts/removeThis'

const message = removeThis().data()
console.log(message)

// Vue Js
import { createApp } from 'vue'
import App from '@/assets//vue/App.vue'
createApp(App).mount('#vue-app')