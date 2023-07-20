<template>
    <v-list density="comfortable">
        <v-list-item
            :to="{ name: 'saas.view.application' }"
            title="Aplikace"
            value="application"
            prepend-icon="mdi-home-edit-outline"
        />

        <v-divider class="mt-3 mb-3" />

        <v-list-item
            :to="{ name: 'saas.view.settings.admins' }"
            title="Administrátoři"
            value="admins"
            prepend-icon="mdi-shield-account-outline"
        />

        <v-list-item
            :to="{ name: 'saas.view.settings.roles' }"
            title="Role a oprávnění"
            value="roles"
            prepend-icon="mdi-shield-account-variant-outline"
        />

        <v-divider class="mt-3 mb-3" />

        <v-list-item :to="{ name: 'saas.view.settings.storage' }" title="Úložiště souborů" value="storage" prepend-icon="mdi-server" />
        <v-list-item :to="{ name: 'saas.view.settings.emails' }" title="E-maily" value="emails" prepend-icon="mdi-email-sync-outline" />
    </v-list>
</template>