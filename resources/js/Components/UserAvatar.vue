<template>
    <Avatar :image="user?.user_photo ?? 'https://placehold.co/100x100'" shape="circle" :size="size"/>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Avatar from 'primevue/avatar'

const page = usePage()
const user = computed(() => page.props.auth.user)
const props = defineProps({ size: String})
</script>