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
                <div class="flex flex-col gap-2">
                    <p class="font-bold text-l">Welcome, {{ current_user.username }}</p>
                    <Divider />
                    <Link href="/user/profile">
                        <Button class="w-full" label="Profile" severity="secondary">
                            <template #icon>
                                <User />
                            </template>
                        </Button>
                    </Link>
                    <Button class="w-full" severity="danger" @click="logout()" label="Logout">
                        <template #icon>
                            <SignOut />
                        </template>
                    </Button>
                </div>
            </Popover>
        </template>
    </Menubar>
</template>

<script setup>
import { computed, ref } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'

import Button from 'primevue/button'
import Divider from 'primevue/divider'
import Menubar from 'primevue/menubar'
import Popover from 'primevue/popover'

import User from '@primeicons/vue/user'
import SignOut from '@primeicons/vue/sign-out'

import UserAvatar from '@/Components/UserAvatar.vue'
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