<script lang="ts" setup>
import Layout from '@/saas/components/Layout.vue'
</script>

<template>
    <Layout>
        <template v-slot:default>
            <div class="pa-7">
                <v-breadcrumbs :items="['Přehled']" class="pa-0" style="font-size: 1.4rem"/>
                <p class="mt-5">Vytvořte si vlastní dashboard a zaregistrujte ho do vlastního vue-routeru.</p>
            </div>
        </template>
    </Layout>
</template>