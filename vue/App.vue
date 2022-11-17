<script setup lang="ts">
import HelloWorld from './components/HelloWorld.vue'
import { onMounted } from 'vue'
import api from './plugins/api'

const showLoginForm = () => {
    api.fetch()
}

</script>

<template>
    <div style="display: flex; align-items: center; justify-content: center; height: 100%">
        <div style="text-align: center">
            <a href="https://strategio.digital" target="_blank">
                <img src="./assets/strategio.svg" alt="Strategio SaaS" width="100" height="100" />
            </a>
            <HelloWorld msg="Strategio SaaS" />
            <button class="btn" style="margin-top: 2rem" @click="showLoginForm">
                Přihlásit se
            </button>
        </div>
    </div>
</template>

<style>
html, body, #app {
    height: 100%;
    width: 100%;
    margin: 0;
}
body {
    font-family: "Apple SD Gothic Neo";
    font-size: 16px;
    background: rgb(19,24,129);
    background: radial-gradient(circle, rgba(19,24,129,1) 30%, rgba(11,15,87,1) 70%);
    color: white;
}
h1, h2, h3, h4, h5 {
    font-family: 'Montserrat', sans-serif;
}
.btn {
    padding: 1rem 2rem;
    margin: 0;
    font-weight: bold;
    background-color: #ffffff;
    border: 1px solid #ffffff;
    border-radius: .5rem;
    color: #000000;
    font-size: 1rem;
    cursor: pointer;
    transform: translateY(0);
    transition: 150ms ease all;
}
.btn:hover {
    background-color: #f68f26;
    border-color: #f68f26;
    transform: translateY(3px);
}
</style>