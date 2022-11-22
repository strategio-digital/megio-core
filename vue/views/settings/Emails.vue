<script lang="ts" setup>
import Layout from '@/components/Layout.vue'
import SettingNav from '@/components/nav/SettingNav.vue'
</script>

<template>
    <Layout>
        <template v-slot:default>
            <v-breadcrumbs :items="['Nastavení', 'E-maily']" class="pa-0" style="font-size: 1.4rem"></v-breadcrumbs>
            <p class="mt-5">Todo: ...</p>
        </template>

        <template v-slot:navigation>
            <SettingNav/>
        </template>
    </Layout>
</template>