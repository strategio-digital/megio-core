<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from '@/saas/components/toast/useToast'
import { IResource } from '@/saas/api/resources/types/IResource'
import { IGroupedResourcesWithRoles } from '@/saas/api/resources/types/IGroupedResourcesWithRoles'
import { IResp as IRespShow } from '@/saas/api/resources/show'
import { IResp as IRespUpdate } from '@/saas/api/resources/updateRole'
import { IRole } from '@/saas/api/resources/types/IRole'
import { IResourceDiff } from '@/saas/api/resources/types/IResourceDiff'
import Layout from '@/saas/components/layout/Layout.vue'
import SettingNav from '@/saas/components/navbar/SettingNav.vue'
import api from '@/saas/api'

const router = useRouter()
const toast = useToast()

const loading = ref(true)
const resources = ref<IResource[]>([])
const roles = ref<string[]>([])
const groupedResourcesWithRoles = ref<IGroupedResourcesWithRoles[]>([])
const resourceDiff = ref<IResourceDiff>()
const routes = ref<string[]>(router.getRoutes().map(route => route.name as string))

const badgeText = computed((): number => {
    return ((resourceDiff.value?.to_create.length || 0) + (resourceDiff.value?.to_remove.length || 0))
})

function unwrapResponse(resp: IRespShow | IRespUpdate) {
    groupedResourcesWithRoles.value = resp.data.grouped_resources_with_roles
    resourceDiff.value = resp.data.resources_diff
    resources.value = resp.data.resources
    roles.value = resp.data.roles
}

async function update() {
    loading.value = true
    const resp = await api.resources.update(routes.value)
    if (resp.success)  {
        unwrapResponse(resp)
        toast.add('Aktualizace resources proběhla úspěšně', 'success')
    }
    loading.value = false
}

async function updateRole(role: IRole, resource: IResource) {
    const resp = await api.resources.updateRole(role.id, resource.id, role.enabled)
    if (resp.success) {
        toast.add('Změna oprávnění proběhla úspěšně', 'success')
    }
}

onMounted(async () => {
    const resp = await api.resources.show(routes.value)
    if (resp.success) unwrapResponse(resp)
    loading.value = false
})
</script>

<template>
    <Layout :loading="loading">
        <template v-slot:default>
            <div class="pa-7">
                <div class="d-flex justify-space-between align-center">
                    <v-breadcrumbs :items="['Role a oprávnění']" class="pa-0" style="font-size: 1.4rem" />

                    <div class="d-flex ms-3">
                        <v-btn variant="tonal" prepend-icon="mdi-plus" class="ms-3">
                            Nová role
                        </v-btn>
                        <v-btn @click="update" variant="tonal" color="red" class="ms-3">
                            <v-badge
                                v-if="badgeText"
                                :content="badgeText"
                                color="red"
                                offset-y="-22"
                                offset-x="12"
                            />
                            <span>Aktualizovat</span>
                        </v-btn>
                    </div>
                </div>

                <v-alert color="red" variant="tonal" border="start" icon="$warning" class="mt-5" v-if="badgeText !== 0">
                    <div>
                        Upravili jste položky routeru. Je tedy potřeba aktualizovat vaše
                        resources. To provedete kliknutím na tlačítko "aktualizovat".
                    </div>

                    <div class="mt-3 text-green" v-if="resourceDiff?.to_create.length">
                        <span class="font-weight-bold">[+{{ resourceDiff?.to_create.length }}]: </span>
                        {{ resourceDiff?.to_create.join(' | ') }}
                    </div>

                    <div class="mt-3 text-red" v-if="resourceDiff?.to_remove.length">
                        <span class="font-weight-bold">[-{{ resourceDiff?.to_remove.length }}]: </span>
                        {{ resourceDiff?.to_remove.join(' | ') }}
                    </div>
                </v-alert>

                <div class="py-5 mt-5" v-for="(resources, groupName) in groupedResourcesWithRoles" :key="groupName">
                    <h2 class="mt-0 mb-0">{{ groupName }}</h2>
                    <v-table density="default" :hover="true">
                        <thead>
                        <tr>
                            <th></th>
                            <th>admin</th>
                            <th v-for="role in roles" :key="role">{{ role }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="text-body-2" v-for="resource in resources" :key="resource.id">
                            <td style="width: 100%">{{ resource.name }}</td>
                            <td class="text-center">
                                <v-checkbox
                                    class="d-flex justify-center"
                                    color="primary"
                                    :model-value="true"
                                    :disabled="true"
                                />
                            </td>
                            <td v-for="role in resource.roles" :key="role.id">
                                <v-checkbox
                                    class="d-flex justify-center"
                                    color="primary"
                                    v-model="role.enabled"
                                    @change="updateRole(role, resource)"
                                />
                            </td>
                        </tr>
                        </tbody>
                    </v-table>
                </div>
            </div>
        </template>

        <template v-slot:navigation>
            <SettingNav />
        </template>
    </Layout>
</template>