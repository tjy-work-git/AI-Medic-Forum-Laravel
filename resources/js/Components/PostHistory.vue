<template>
    <Card class="min-h-screen">
        <template #title>
            <h1 class="text-4xl font-bold m-4">Post History</h1>
        </template>
        <template #content>
            <template v-if="loading" class="flex justify-center items-center min-h-screen">
                <ProgressSpinner />
            </template>

            <template v-else-if="posts.length > 0">
                <template v-for="post in posts">
                    <Link :href="'/forum/post/' + post.post_id">
                        <Card class="my-2">
                            <template #title>
                                <h2 class="text-xl font-bold">{{ post.title }}</h2>
                            </template>
                            <template #content>
                                <p class="mb-2">{{ post.description.substring(0, 100) + "..." }}</p>
                            </template>
                            <template #footer>
                                <div class="flex flex-wrap justify-between">
                                    <p><Clock /> {{ post.created_at }} </p>
                                    <Badge :severity="current_user ? 'secondary' : 'primary'" size="large"
                                        :value="post.upvote_count + ' Upvotes'">
                                    </Badge>
                                </div>
                            </template>
                        </Card>
                    </Link>
                </template>
            </template>

            <template v-else>
                <p class="flex justify-center items-center">No posts history found.</p>
            </template>
        </template>
    </Card>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { Link } from '@inertiajs/vue3'

import Badge from 'primevue/badge'
import Card from 'primevue/card'
import ProgressSpinner from 'primevue/progressspinner'

import Clock from '@primeicons/vue/clock'

const props = defineProps({
    id: Number
});

const posts = ref([])
const loading = ref(true)

onMounted(async () => {
    try {
        const response = await fetch(`/user/posts/${props.id}`)
        if (response.ok) {
            posts.value = await response.json()
        }
    } catch (error) {
        console.error('Failed to fetch user posts:', error)
    } finally {
        loading.value = false
    }
})
</script>