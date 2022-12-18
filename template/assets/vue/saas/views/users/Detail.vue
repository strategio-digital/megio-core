<script lang="ts" setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { IUser } from '@/saas/api/types/IUser'
import Layout from '@/saas/components/Layout.vue'
import api from '@/saas/api'

const route = useRoute()
const loading = ref(true)
const item = ref<IUser>()

async function refresh() {
    loading.value = true
    const resp = await api.collections.showOne('user', { id: route.params.id as string })
    item.value = { ...resp.data } as IUser
    loading.value = false
}

onMounted(() => refresh())
</script>

<template>
    <Layout :loading="loading">
        <div class="pa-7">
            <div v-if="item" class="d-flex justify-space-between align-center">
                <v-breadcrumbs
                    :items="[{ title: 'Uživatelé', to: { name: 'Users' } }, item.email]"
                    class="pa-0"
                    style="font-size: 1.4rem"
                />
            </div>

            <div class="mt-5">
                <pre>{{ item }}</pre>
            </div>
        </div>
    </Layout>
</template>