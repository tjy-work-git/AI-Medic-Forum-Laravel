<template>
    <header :style="{ backgroundImage: `url(${bgUrl})` }">
        <a href="/">
            <img :src="logoUrl" alt="WeDoCare - AI Assisted Forums" class="h-24">
        </a>
    </header>
    <Menubar :model="items" class="flex flex-row bg-black gap-4 p-2 m-4 text-white">
        <template #item="{ item }">
            <Link :href="item.url">
                {{ item.label }}
            </Link>
        </template>
        <template #end>
            <Link v-if="user" href="/user/profile"><span>Welcome, {{ user.username }}</span></Link>
            <Link v-else href="/user/login">Login</Link>
        </template>
    </Menubar>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import Menubar from 'primevue/menubar'
import logoUrl from '../../images/static/wedocare_logo.png'
import bgUrl from '../../images/static/bg.jpg'

const page = usePage()
const user = computed(() => page.props.auth?.users)

const items = [
    { label: 'Home', url: '/'},
    { label: 'Forum', url: '/forum'},
    { label: 'Bookmark', url: '/forum/bookmark', visible: user?.role === 'user'},
    { label: 'Feedback', url: '/feedback', visible: user?.role === 'user'},
    { label: 'Report', url: '/report', visible: user?.role === 'user'},
    { label: 'About Us', url: '/about-us'},
]
</script>