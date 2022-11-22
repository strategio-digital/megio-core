<script lang="ts" setup>
import Layout from '@/components/Layout.vue'
import UserNav from '@/components/nav/SettingNav.vue'
</script>

<template>
    <Layout>
        <template v-slot:default>
            <v-breadcrumbs :items="['Nastavení', 'Role a oprávnění']" class="pa-0" style="font-size: 1.4rem"></v-breadcrumbs>
            <p class="mt-5">Todo: ...</p>
        </template>

        <template v-slot:navigation>
            <UserNav/>
        </template>
    </Layout>
</template>