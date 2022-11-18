<script lang="ts" setup>
import api from '@/plugins/api'

const showLoginForm = () => {
    api.fetch()
}
</script>

<template>
    <div style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%">
        <div style="max-width: 450px; width: 100%; background-color: rgba(255, 255, 255, .05); padding: 3rem; border-radius: .3rem">
            <h1>Přihlášení</h1>
            <button class="btn" style="margin-top: 2rem" @click="showLoginForm">
                Přihlásit se
            </button>
        </div>
    </div>
</template>