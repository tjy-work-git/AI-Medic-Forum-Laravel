<template>
    <!-- Floating comment button - triggers comment dialog-->
    <Button rounded size="xlarge" @click="visible = true"
        style="position: fixed; bottom: 60px; right: 30px; z-index: 100;">
        <template #icon>
            <Comment />
        </template>
    </Button>

    <!-- Comment Dialog -->
    <Dialog v-model:visible="visible" class="w-1/2" modal>
        <template #header>
            <h2 class="text-2xl font-bold">Leave a comment</h2>
        </template>
        <form @submit.prevent="onSubmit()">
            <Textarea v-model="form.description" class="w-full" rows="10" autoResize placeholder="Say something..." />
            <div class="flex flex-col">
                <label for="img">Add Image (optional):</label>
                <FileUpload v-model="form.img" chooselabel="Browse" accept="image/*" />
            </div>
            <div class="flex justify-end gap-2 my-4">
                <Button severity="secondary" @click="visible = false">Cancel</Button>
                <Button type="submit" label="Comment" />
            </div>
        </form>
    </Dialog>

    <template v-if="post.length == 0">
        <p class="flex justify-center items-center">This post does not exist</p>
    </template>

    <template v-else>
        <!-- Post Section -->
        <div class="flex flex-col gap-2">
            <div class="flex flex-wrap items-center">
                <h1 class="text-4xl font-bold my-6">{{ post.title }}</h1>
                <Button class="mx-6" variant="text" rounded>
                    <template #icon>
                        <Bookmark v-tooltip.bottom="{ value: 'Bookmark' }"/>
                    </template>
                </Button>
            </div>

            <div class="flex flex-row gap-10">
                <!-- Poster info card -->
                <UserInfoCard :data="post" />

                <!-- Content -->
                <Card class="w-full ">
                    <template #content>
                        <p class="mb-2">{{ post.description }}</p>
                    </template>
                </Card>
            </div>
        </div>

        <Divider align="left" class="py-6">
            <h2 class="text-2xl font-bold">Comments <Badge :value="comments.total" severity="secondary"/></h2>
        </Divider>

        <!-- Comment Section -->
            <template v-if="loading" class="flex justify-center items-center min-h-screen">
                <ProgressSpinner />
            </template>

            <template v-else-if="comments.data.length > 0">
                <template v-for="comment in comments.data" :key="comment.comment_id">
                    <div class="flex flex-row gap-10 mb-4">
                        <!-- Commenter info card -->
                        <UserInfoCard :data="comment" />

                        <!-- Content -->
                        <Card class="w-full">
                            <template #content>
                                <p class="mb-2">{{ comment.description }}</p>
                            </template>
                        </Card>
                    </div>
                </template>
            </template>

            <template v-else>
                <p class="flex justify-center items-center">No comments yet.</p>
            </template>
    </template>
</template>

<script setup>
// Libraries
import { onMounted, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

// Primevue
import Badge from 'primevue/badge'
import Button from 'primevue/button'
import Card from 'primevue/card'
import Dialog from 'primevue/dialog'
import Divider from 'primevue/divider'
import FileUpload from 'primevue/fileupload'
import Textarea from 'primevue/textarea';
import ProgressSpinner from 'primevue/progressspinner';

// Primevue Icons
import Bookmark from '@primeicons/vue/bookmark'
import BookmarkFill from '@primeicons/vue/bookmark-fill' // will use for bookmarked content, wip
import Comment from '@primeicons/vue/comment';

// Custom Imports
import UserInfoCard from '@/Components/UserInfoCard.vue'

const visible = ref(false);
const props = defineProps({ post: Object })
const comments = ref([])
const loading = ref(true)

const form = useForm({
    description: null,
    img: null,
    post_id: null,
})

const onSubmit = () => {
    form.post('/forum/comment/store', {
        preserveScroll: true,
        onSuccess: () => {
            visible.value = false
            form.reset('description', 'img')
            fetchComments()
        }
    })
}

const fetchComments = async () => {
    try {
        const response = await fetch(`/forum/post/${props.post.post_id}/comments`)
        if (response.ok) {
            comments.value = await response.json()
        }
    } catch (error) {
        console.error('Failed to fetch comments:', error)
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchComments()
})

watch(() => props.post, (post) => {
    if (post) {
        form.post_id = post.post_id ?? null
    }
}, { immediate: true })
</script>