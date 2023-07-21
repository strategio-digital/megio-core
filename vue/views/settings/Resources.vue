<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from '@/saas/components/toast/useToast'
import { IResource } from '@/saas/api/resurces/types/IResource'
import { IGroupedResourcesWithRoles } from '@/saas/api/resurces/types/IGroupedResourcesWithRoles'
import { IResp as IRespShow } from '@/saas/api/resurces/show'
import { IResp as IRespUpdate } from '@/saas/api/resurces/update'
import Layout from '@/saas/components/layout/Layout.vue'
import SettingNav from '@/saas/components/navbar/SettingNav.vue'
import api from '@/saas/api'

const router = useRouter()
const toast = useToast();

const loading = ref(true)
const resources = ref<IResource[]>([])
const roles = ref<string[]>([])
const groupedResourcesWithRoles = ref<IGroupedResourcesWithRoles[]>([])
const routes = ref<string[]>(router.getRoutes().map(route => route.name as string))

const resourcesToAdd = computed((): string[] => {
    const dbResources = resources.value.map(resource => resource.name)
    return routes.value.filter(name => ! dbResources.includes(name))
})

const resourcesToRemove = computed((): string[] => {
    return resources.value.filter(resource =>
        ! routes.value.includes(resource.name) && resource.type === 'router.vue')
    .map(value => value.name)
})

const badgeText = computed((): string | null => {
    let result = ''
    if (resourcesToAdd.value.length) result += '+' + resourcesToAdd.value.length
    if (resourcesToAdd.value.length && resourcesToRemove.value.length) result += ' | '
    if (resourcesToRemove.value.length) result += '-' + resourcesToRemove.value.length
    return result === '' ? null : result
})

function unwrapResponse(resp: IRespShow|IRespUpdate) {
    groupedResourcesWithRoles.value = resp.data.grouped_resources_with_roles
    resources.value = resp.data.resources
    roles.value = resp.data.roles
}

async function updateResources() {
    loading.value = true;
    const resp = await api.resources.updateViewResources(resourcesToAdd.value, resourcesToRemove.value)
    if(resp.success) unwrapResponse(resp);
    loading.value = false;
}

onMounted(async () => {
    const resp = await api.resources.show()
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
                        <v-btn v-if="badgeText" @click="updateResources" variant="tonal" color="red"  class="ms-3">
                            <v-badge
                                :content="badgeText"
                                color="red"
                                offset-y="-22"
                                offset-x="12"
                            />
                            <span>Aktualizovat</span>
                        </v-btn>
                    </div>
                </div>

                <v-alert v-if="badgeText" color="red" variant="tonal" border="start" icon="$warning" class="mt-5">
                    Upravili jste položky ve vue-routeru. Je tedy potřeba aktualizovat vaše
                    view-resources. To provedete kliknutím na tlačítko "aktualizovat".
                    <div class="mt-3 text-green" v-if="resourcesToAdd.length">
                        <span class="font-weight-bold">[+{{ resourcesToAdd.length }}]: </span>
                        {{ resourcesToAdd.join(' | ') }}
                    </div>

                    <div class="mt-3 text-red" v-if="resourcesToRemove.length">
                        <span class="font-weight-bold">[-{{ resourcesToRemove.length }}]: </span>
                        {{ resourcesToRemove.join(' | ') }}
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
                                <v-checkbox class="d-flex justify-center" color="primary" :model-value="role.enabled" />
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