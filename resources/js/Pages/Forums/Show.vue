<template>
    <!-- Floating comment button - triggers comment dialog-->
    <Button v-if="current_user" rounded @click="visible = true"
        style="position: fixed; bottom: 30px; right: 30px; z-index: 100;">
        <template #icon>
            <Comment />
        </template>
    </Button>

    <!-- Comment Dialog -->
    <Dialog v-model:visible="visible" class="w-1/2" modal>
        <template #header>
            <h2 class="text-2xl font-bold">Leave a comment</h2>
        </template>
        <form @submit.prevent="onCommentSubmit()">
            <Textarea v-model="commentForm.description" class="w-full" rows="10" autoResize placeholder="Say something..." />
            <div class="flex flex-col py-2 gap-2">
                <label for="comment_photo"><b>Add Image</b> <span class="text-sm">(optional)</span></label>
                <FileUpload @select="(event) => commentForm.comment_photo = event.files[0]" mode="basic" chooseLabel="Browse" accept="image/*" />
            </div>
            <div class="flex justify-end gap-2 my-4">
                <Button severity="secondary" @click="visible = false">Cancel</Button>
                <Button type="submit" label="Comment" />
            </div>
        </form>
    </Dialog>

    <!-- Main template -->
    <template v-if="post == null">
         <div class="flex flex-col gap-4 justify-center items-center min-h-[calc(100vh-16rem)]">
            <p class="text-2xl">This post does not exist</p>
         </div>
    </template>

    <template v-else>
        <!-- Post Section -->
        <div class="flex flex-col gap-2">
            <div class="flex flex-wrap items-center">
                <Button class="mx-6" variant="outlined" rounded @click="onBookmarkSubmit">
                    <template #icon>
                        <BookmarkFill v-if="post.has_bookmarked == 1" v-tooltip.bottom="{ value: 'Remove Bookmark' }"/>
                        <Bookmark v-else v-tooltip.bottom="{ value: 'Bookmark' }" />
                    </template>
                </Button>
                <h1 class="text-4xl font-bold my-6">{{ post.title }}</h1>
            </div>
        </div>

        <ForumContentCard :data="post" type="post" @updated="handleUpdated" />

        <div class="py-3">
            <Divider align="left">
                <h2 class="text-2xl font-bold">Comments
                    <Badge :value="comments?.total ?? 0" severity="secondary" />
                </h2>
            </Divider>
        </div>

        <!-- Comment Section : load after post render -->
        <template v-if="!comments">
            <ProgressSpinner />
        </template>

        <template v-else-if="comments.length == 0">
            <p class="flex justify-center items-center min-h-[calc(100vh-40rem)]">No comments yet.</p>
        </template>

        <div v-else class="flex flex-col gap-4">
            <template v-for="comment in comments" :key="comment.comment_id">
                <!-- Content -->
                <ForumContentCard :data="comment" type="comment" @deleted="handleDeleted" @updated="handleUpdated" />
            </template>
        </div>

        <div class="py-12">
            <Divider align="center">
                <h2 class="text-xl text-gray-400">
                    END OF DISCUSSION
                </h2>
            </Divider>
        </div>
    </template>
</template>

<script setup>
// Libraries
import { computed, ref, watch } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'

// Primevue
import Badge from 'primevue/badge'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import Divider from 'primevue/divider'
import FileUpload from 'primevue/fileupload'
import Image from 'primevue/image'
import Textarea from 'primevue/textarea'
import ProgressSpinner from 'primevue/progressspinner'

// Primevue Icons
import Bookmark from '@primeicons/vue/bookmark'
import BookmarkFill from '@primeicons/vue/bookmark-fill' // will use for bookmarked content, wip
import Comment from '@primeicons/vue/comment'

// Custom Imports
import ForumContentCard from '@/Components/ForumContentCard.vue'

const page = usePage()
const visible = ref(false)
const post = ref(null)
const comments = ref([])
const props = defineProps({ post_data: Object, comments_data: Object })
const current_user = computed(() => page.props.auth?.current_user)

const commentForm = useForm({
    description: null,
    comment_photo: null,
    post_id: null,
})

// Immediate delete
const handleDeleted = (commentId) => {
    comments.value = comments.value.filter(c => c.comment_id !== commentId)
    if (props.comments_data?.total) props.comments_data.total--
}

// Immediate update
const handleUpdated = (updated) => {
    if (updated.comment_id) {
        const comment = comments.value.find(c => c.comment_id === updated.comment_id)
        if (comment) {
            comment.description = updated.description
        }
    } else if (updated.post_id && post.value?.post_id === updated.post_id) {
        post.value.description = updated.description
    }
}

const onCommentSubmit = () => {
    commentForm.post('/forum/comment/store', {
        preserveScroll: true,
        onSuccess: () => {
            visible.value = false
            commentForm.reset('description', 'comment_photo')
        }
    })
}

const onBookmarkSubmit = () => {
    router.post(`/forum/post/${post.value.post_id}/bookmark`), {
        preserveScroll: true,
        only: ['flash']
    }
}

// Watch for post change + append post ID into comment form (so it knows what post it belong to)
watch(() => props.post_data, (post_data) => {
    if (post_data) {
        commentForm.post_id = post_data.post_id ?? null
        post.value = post_data
    }
}, { immediate: true })

// Watch for comment changes (update / delete / add)
watch(() => props.comments_data?.data, (comments_data) => {
    if (comments_data) {
        comments.value = [...comments_data]
    }
}, { immediate: true })

</script>