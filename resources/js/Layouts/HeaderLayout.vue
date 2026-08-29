<template>
    <header :style="{ backgroundImage: `url(${bgUrl})` }">
        <Link href="/">
            <img :src="logoUrl" alt="WeDoCare - AI Assisted Forums" class="h-24">
        </Link>
    </header>
    <Menubar :model="items" class="flex flex-row bg-black gap-4 p-2 m-4 text-white">
        <template #item="{ item }">
            <Link :href="item.url">
                {{ item.label }}
            </Link>
        </template>
        <template #end>
            <Link v-if="!current_user" href="/user/login">Login</Link>
            <UserAvatar v-else class="cursor-pointer" @click="toggle" :user="current_user" />
            <Popover ref="op">
                <p>Welcome, {{ current_user.username }}</p>
                <Link href="/user/profile">Profile</Link>
                <Button class="w-full my-2" severity="danger" @click="logout()" label="Logout"/>
            </Popover>
        </template>
    </Menubar>
</template>

<script setup>
import { computed, ref } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import UserAvatar from '@/Components/UserAvatar.vue'
import { router } from '@inertiajs/vue3'
import Button from 'primevue/button'
import Menubar from 'primevue/menubar'
import Popover from 'primevue/popover'
import logoUrl from '../../images/static/wedocare_logo.png'
import bgUrl from '../../images/static/bg.jpg'

const page = usePage()
const op = ref();
const current_user = computed(() => page.props.auth?.current_user)

const items = [
    { label: 'Home', url: '/' },
    { label: 'Forum', url: '/forum' },
    { label: 'Bookmark', url: '/forum/bookmark', visible: current_user?.role === 'user' },
    { label: 'Feedback', url: '/feedback', visible: current_user?.role === 'user' },
    { label: 'Report', url: '/report', visible: current_user?.role === 'user' },
    { label: 'About Us', url: '/about-us' },
]

const toggle = (event) => {
    op.value.toggle(event);
}

const logout = () => {
    router.post('/action/logout')
}
</script>