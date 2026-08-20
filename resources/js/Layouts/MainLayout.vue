<template>
    <HeaderLayout v-if="showHeader" />
    <slot />
    <FooterLayout/>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import HeaderLayout from '@/Layouts/HeaderLayout.vue'
import FooterLayout from '@/Layouts/FooterLayout.vue'

const page = usePage();

const showHeader = computed(() => {
    return page.props.showHeader !== false
})

</script>