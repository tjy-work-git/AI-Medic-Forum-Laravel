<template>
    <Card class="min-h-screen">
        <template #title>Post History</template>
        <template #content>
            <div v-if="loading" class="flex justify-center items-center min-h-screen">
                <ProgressSpinner />
            </div>

            <template v-else-if="posts.length > 0">
                <div class="flex flex-row gap-2" v-for="post in posts">
                    <p>{{ post.title }}</p>
                    <p>{{ post.upvotes }} upvotes</p>
                    <Divider />
                </div>
            </template>

            <template v-else>
                <p class="flex justify-center items-center">No posts history found.</p>
            </template>
        </template>
    </Card>
</template>

<script setup>
import { ref, onMounted } from 'vue';

import Card from 'primevue/card';
import Divider from 'primevue/divider';

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