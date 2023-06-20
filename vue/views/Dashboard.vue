<script lang="ts" setup>
import Layout from '@/saas/components/layout/Layout.vue'
</script>

<template>
    <Layout>
        <template v-slot:default>
            <div class="pa-7">
                <v-breadcrumbs :items="['Přehled']" class="pa-0" style="font-size: 1.4rem"/>
                <p class="mt-3">Vytvořte si vlastní dashboard a zaregistrujte ho ve vlastním vue-routeru (./assets/saas.ts).</p>
            </div>
        </template>
    </Layout>
</template>