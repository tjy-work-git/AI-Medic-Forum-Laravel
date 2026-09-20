<template>
    <Avatar
        class="shrink-0"
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
    user: Object,
    img: String,
})

const resolvedSize = computed(() => {
    const val = props.size ?? 40
    return typeof val === 'number' ? `${val}px` : (val.endsWith('px') ? val : `${val}px`)
})

const avatarImage = computed(() => {
    const photo = props.img ?? props.user?.user_photo ?? page.props.auth?.current_user?.user_photo ?? page.props.auth?.user?.user_photo

    if (!photo) {
        return 'https://placehold.co/100x100'
    }

    if (photo.startsWith('http://') || photo.startsWith('https://')) {
        return photo
    }

    if (photo.startsWith('/storage/')) {
        return photo
    }

    return `/storage/${photo.replace(/^\/+/, '')}`
})
</script>
