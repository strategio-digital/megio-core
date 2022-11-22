import App from './App.vue'
import router from './router'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createVuetify } from 'vuetify'
import { vuetifyOptions } from '@/plugins/vuetify'

import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import './style.scss'

const pinia = createPinia()
const vuetify = createVuetify(vuetifyOptions)

createApp(App).use(router).use(pinia).use(vuetify).mount('#app')
