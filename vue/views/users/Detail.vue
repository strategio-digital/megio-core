<script lang="ts" setup>
import { onMounted, ref } from 'vue'
import { IUser } from '@/plugins/api/types/IUser'
import { useRoute, useRouter } from 'vue-router'
import api from '@/plugins/api'
import Layout from '@/components/Layout.vue'

const router = useRouter()
const route = useRoute()
const loading = ref(true)
const item = ref<IUser>()

const refresh = async () => {
    loading.value = true
    const resp = await api.collections.showOne('user', { id: route.params.id as string })
    item.value = { ...resp.data } as IUser
    loading.value = false
}

onMounted(() => refresh())
</script>

<template>
    <Layout :loading="loading">
        <div v-if="item" class="d-flex justify-space-between align-center">
            <v-breadcrumbs
                :items="[{ title: 'Uživatelé', to: { name: 'Users'  } }, item.email]"
                class="pa-0"
                style="font-size: 1.4rem"
            />

            <div class="d-flex ms-3">
                <v-btn variant="tonal" prepend-icon="mdi-content-save-outline" class="ms-3">
                    Uložit
                </v-btn>
            </div>
        </div>

<!--        <v-form validate-on="blur" v-model="valid" ref="form" @submit.prevent="onSubmit">-->
<!--            <h1>Přihlášení</h1>-->
<!--            <v-text-field label="E-mail" v-model="data.email" :rules="schema.email" />-->
<!--        </v-form>-->

    </Layout>
</template>