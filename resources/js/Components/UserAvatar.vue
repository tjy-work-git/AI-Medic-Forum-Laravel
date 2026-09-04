<template>
    <Avatar
        :image="avatarImage"
        shape="circle"
        :pt="{
            root: { style: { width: resolvedSize, height: resolvedSize } },
            image: { style: { width: '100%', height: '100%', objectFit: 'cover' } }
        }"
    />
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Avatar from 'primevue/avatar'

const page = usePage()

const props = defineProps({
    size: String,
    size_value: Number,
    user: Object,
    img: String,
})

const resolvedSize = computed(() => {
    const val = props.size ?? props.size_value ?? 40
    return typeof val === 'number' ? `${val}px` : (val.endsWith('px') ? val : `${val}px`)
})

const avatarImage = computed(() => {
    return props.img ?? props.user?.user_photo ?? page.props.auth.user?.user_photo ?? 'https://placehold.co/100x100'
})
</script>
