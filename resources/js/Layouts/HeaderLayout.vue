<template>
    <header :style="{ backgroundImage: `url(${bgUrl})` }">
        <a href="/">
            <img :src="logoUrl" alt="WeDoCare - AI Assisted Forums" class="w-12 h-12">
        </a>
        <nav class="flex flex-row bg-black gap-4 text-white">
            <ul><a href="/">Home</a></ul>
            <ul><a href="/forum">Forum</a></ul>
            <ul><a href="/forum/bookmark">Bookmark</a></ul>
            <ul v-if="(user && user.role === 'User')"><a href="feedback/create-feedback">Feedback</a></ul>
            <ul v-if="(user && user.role === 'Admin')"><a href="feedback/main.php">Feedbacks</a></ul>
            <ul v-if="(user && user.role === 'Admin')"><a href="report/main.php">Reports</a></ul>
            <ul><a href="/about-us">About Us</a></ul>
            <ul class="justify-end">
                <span v-if="user">Welcome, <a href="user/profile">{{ user.name }}</a></span>
                <span v-else><a href="/login" class="float-right">Login</a></span>
            </ul>
        </nav>
    </header>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import logoUrl from '../../images/static/wedocare_logo.png'
import bgUrl from '../../images/static/bg.jpg'

const page = usePage()
const user = computed(() => page.props.auth?.users)
</script>