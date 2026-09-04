<template>
    <Card class="w-1/6">
        <template #content>
            <div class="flex flex-col gap-4">
                <Link href="/user/profile">
                    Profile
                </Link>
                <Link href="/user/post-history">
                    Post History
                </Link>
                <Link href="/user/comment-history">
                    Comment History
                </Link>
                <Link href="/user/logout">
                    Logout
                </Link>
            </div>
        </template>
    </Card>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import Card from 'primevue/card'
import Menubar from 'primevue/menubar' // reserve for mobile later
</script>