<script setup lang="ts">
import { ref } from 'vue'
import vite from '@/assets/img/vite.svg'
import vue from '@/assets/img/vue.svg'

const props = defineProps<{ msg: string }>()
const images = ref([
    { src: vite, href: 'https://vitejs.dev', alt: props.msg },
    { src: vue, href: 'https://vuejs.org', alt: props.msg }
])

function openLink(href: string) {
    window.open(href, '_blank')
}
</script>

<template>
    <a v-for="image in images" href="#" target="_blank" @click.prevent="openLink(image.href)">
        <img :src="image.src" class="logo" :alt="image.alt" />
    </a>
</template>