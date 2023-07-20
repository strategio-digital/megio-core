<script lang="ts" setup>
import { hasAnyOfResources, hasResource } from '@/saas/api/auth/currentUser'
</script>
<template>
    <v-list density="comfortable">
        <v-list-item
            v-if="hasResource('saas.view.application')"
            :to="{ name: 'saas.view.application' }"
            title="Aplikace"
            value="application"
            prepend-icon="mdi-home-edit-outline"
        />

        <v-divider class="mt-3 mb-3" v-if="hasAnyOfResources(['saas.view.admins', 'saas.view.roles'])" />

        <v-list-item
            v-if="hasResource('saas.view.admins')"
            :to="{ name: 'saas.view.settings.admins' }"
            title="Administrátoři"
            value="admins"
            prepend-icon="mdi-shield-account-outline"
        />

        <v-list-item
            v-if="hasResource('saas.view.roles')"
            :to="{ name: 'saas.view.settings.roles' }"
            title="Role a oprávnění"
            value="roles"
            prepend-icon="mdi-shield-account-variant-outline"
        />

        <v-divider class="mt-3 mb-3" v-if="hasAnyOfResources(['saas.view.storage', 'saas.view.emails'])" />

        <v-list-item
            v-if="hasResource('saas.view.storage')"
            :to="{ name: 'saas.view.settings.storage' }"
            title="Úložiště souborů"
            value="storage"
            prepend-icon="mdi-server"
        />

        <v-list-item
            v-if="hasResource('saas.view.emails')"
            :to="{ name: 'saas.view.settings.emails' }"
            title="E-maily"
            value="emails"
            prepend-icon="mdi-email-sync-outline"
        />
    </v-list>
</template>