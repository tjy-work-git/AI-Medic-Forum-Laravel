<template>
    <Card class="min-h-screen">
        <template #title>
            <h1 class="text-4xl font-bold m-4">Post History</h1>
        </template>
        <template #content>
            <div v-if="loading" class="flex justify-center items-center min-h-screen">
                <ProgressSpinner />
            </div>

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
                                    <p>Posted on {{ post.created_at }}</p>
                                    <p>{{ post.upvotes }} Upvotes</p>
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
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3'

import Card from 'primevue/card';

import ProgressSpinner from 'primevue/progressspinner';

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