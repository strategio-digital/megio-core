<script lang="ts" setup>
import { ref, useSlots } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api'

const props = defineProps<{ loading?: boolean }>()

const slots = useSlots()
const router = useRouter()
const route = useRoute()

const themeStorage = localStorage.getItem('strategio_saas_theme')
const theme = ref(themeStorage || 'dark')

function changeTheme() {
    theme.value = theme.value === 'light' ? 'dark' : 'light'
    localStorage.setItem('strategio_saas_theme', theme.value)
}

function logout() {
    api.auth.logout()
    router.push({ name: 'Intro' })
}
</script>

<template>
    <v-app :theme="theme">
        <v-navigation-drawer permanent rail rail-width="86">
            <router-link
                :to="{ name: 'Collections' }"
                class="ps-2 mb-4 mt-3 d-flex text-no-wrap align-center text-decoration-none"
            >
                <div
                    class="rounded-circle"
                    :class="{'bg-grey-lighten-2': theme === 'light'}"
                    :style="{'padding': (theme === 'light' ? '2px' : '0px')}"
                >
                    <img
                        src="@/assets/img/strategio.svg"
                        :width="theme === 'light' ? 34 : 38"
                        :height="theme === 'light' ? 34 : 38"
                        alt="Strategio Saas"
                        class="d-block"
                    >
                </div>
                <h4 class="ms-6 my-0 py-0 font-weight-bold" :class="`color-blue-${theme}`">Strategio SaaS</h4>
            </router-link>

            <v-list density="comfortable">
                <v-tooltip location="end" text="Kolekce" offset="-5">
                    <template v-slot:activator="{ props }">
                        <v-list-item
                            v-bind="props"
                            :active="route.path.startsWith('/collections')"
                            :to="{ name: 'Collections'}"
                            prepend-icon="mdi-database"
                            value="collections"
                            title="Kolekce"
                        />
                    </template>
                </v-tooltip>

                <v-tooltip location="end" text="Uživatelé" offset="-5">
                    <template v-slot:activator="{ props }">
                        <v-list-item
                            v-bind="props"
                            :active="route.path.startsWith('/users')"
                            :to="{ name: 'Users' }"
                            prepend-icon="mdi-account-multiple"
                            value="users"
                            title="Uživatelé"
                        />
                    </template>
                </v-tooltip>

                <v-tooltip location="end" text="Nastavení" offset="-5">
                    <template v-slot:activator="{ props }">
                        <v-list-item
                            v-bind="props"
                            :active="route.path.startsWith('/settings')"
                            :to="{ name: 'Application' }"
                            prepend-icon="mdi-hammer-screwdriver"
                            value="settings"
                            title="Nastavení"
                        />
                    </template>
                </v-tooltip>
            </v-list>

            <template v-slot:append>
                <v-list density="comfortable">
                    <v-tooltip location="end" :text="theme === 'light' ? 'Tmavý režim' : 'Světlý režim'" offset="-5">
                        <template v-slot:activator="{ props }">
                            <v-list-item
                                v-bind="props"
                                @click="changeTheme"
                                :prepend-icon="theme === 'light' ? 'mdi-weather-night' : 'mdi-weather-sunny'"
                                :title="theme === 'light' ? 'Tmavý režim' : 'Světlý režim'"
                                value="theme"
                            />
                        </template>
                    </v-tooltip>

                    <v-tooltip location="end" text="Odhlásit se" offset="-5">
                        <template v-slot:activator="{ props }">
                            <v-list-item
                                v-bind="props"
                                @click="logout"
                                prepend-icon="mdi-account-arrow-left"
                                title="Odhlásit se"
                                value="logout"
                            />
                        </template>
                    </v-tooltip>
                </v-list>
            </template>
        </v-navigation-drawer>

        <template v-if="slots.navigation">
            <v-navigation-drawer permanent>
                <slot name="navigation"></slot>
            </v-navigation-drawer>
        </template>

        <v-main>
            <div class="position-relative w-100 h-100">
                <div
                    :class="[theme, props.loading || 'invisible']"
                    style="z-index: 10"
                    class="position-absolute w-100 h-100 d-flex justify-center align-center bg-overlay"
                >
                    <v-progress-circular indeterminate :size="50" :width="5"/>
                </div>
                <slot><h1>...</h1></slot>
            </div>
        </v-main>
    </v-app>
</template>