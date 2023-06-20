<script lang="ts" setup>
import Layout from '@/saas/components/layout/Layout.vue'
import SettingNav from '@/saas/components/navbar/SettingNav.vue'
</script>

<template>
    <Layout>
        <template v-slot:default>
            <div class="pa-7">
                <v-breadcrumbs :items="['Nastavení', 'Úložiště souborů']" class="pa-0" style="font-size: 1.4rem" />
                <p class="mt-5">Todo: ...</p>
            </div>
        </template>

        <template v-slot:navigation>
            <SettingNav />
        </template>
    </Layout>
</template>