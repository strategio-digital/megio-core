<script lang="ts" setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/saas/api'

const router = useRouter()
const loading = ref(false)
const valid = ref()
const alert = ref()

const schema = ref({
    email: [
        (v: string) => !! v || 'E-mail je povinný',
        (v: string) => /.+@.+/.test(v) || 'E-mail není validní '
    ],
    password: [
        (v: string) => !! v || 'Heslo je povinné'
    ]
})

const data = ref({ email: '', password: '' })

async function onSubmit() {
    if (valid.value) {
        loading.value = true
        alert.value = ''

        const resp = await api.auth.loginByEmail(data.value.email, data.value.password)

        if (resp.success) {
            if (!['admin', 'backend-user'].includes(resp.data.user_role)) {
                alert.value = 'Nedostatečné oprávnění.'
            } else {
                router.push({ name: 'Dashboard' })
            }
        } else {
            console.error(resp.errors)
            alert.value = 'Nesprávné přihlašovácací údaje.'
        }

        loading.value = false
    }
}
</script>

<template>
    <div class="d-flex justify-center align-center w-100 h-100 bg-gradient-blue">
        <div class="w-100 position-relative" style="max-width: 450px">
            <div class="text-center position-absolute" style="right: 20px; bottom: -30px">
                <img src="@/saas/assets/img/strategio.svg" height="100" width="100" alt="Strategio SaaS">
            </div>
            <div class="w-100 pa-10" style="border-radius: .3rem; background-color: rgba(255,255,255,0.97)">
                <v-form validate-on="blur" v-model="valid" ref="form" @submit.prevent="onSubmit">
                    <h1>Přihlášení</h1>
                    <v-text-field label="E-mail" v-model="data.email" :rules="schema.email" />
                    <v-text-field type="password" label="Heslo" v-model="data.password" :rules="schema.password" />

                    <v-alert v-if="alert" color="error" icon="$info" class="mb-5">
                        {{ alert }}
                    </v-alert>

                    <v-btn type="submit" size="large" color="warning" :loading="loading" :disabled="loading">
                        Přihlásit se
                    </v-btn>
                </v-form>
            </div>
        </div>
    </div>
</template>