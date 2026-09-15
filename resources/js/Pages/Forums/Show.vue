<template>
    <!-- Floating comment button - triggers comment dialog-->
    <Button v-if="current_user" rounded size="xlarge" @click="visible = true"
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

    <template v-if="post == null">
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

            <ForumContentCard :data="post" type="post" />
        </div>

        <Divider align="left" class="py-6">
            <h2 class="text-2xl font-bold">Comments <Badge :value="comments?.total" severity="secondary"/></h2>
        </Divider>

        <!-- Comment Section : Defer to load later -->
        <Deferred data="comments">
            <template #fallback>
                <ProgressSpinner />
            </template>

            <template v-if="comments?.total == 0">
                <p class="flex justify-center items-center">No comments yet.</p>
            </template>

            <div v-else class="flex flex-col gap-4">
                <template v-for="comment in comments.data" :key="comment.comment_id">
                    <!-- Content -->
                    <ForumContentCard :data="comment" type="comment" />
                </template>
            </div>
        </Deferred>
    </template>
</template>

<script setup>
// Libraries
import { computed, ref, watch } from 'vue'
import { useForm, usePage, Deferred } from '@inertiajs/vue3'

// Primevue
import Badge from 'primevue/badge'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import Divider from 'primevue/divider'
import FileUpload from 'primevue/fileupload'
import Textarea from 'primevue/textarea'
import ProgressSpinner from 'primevue/progressspinner'

// Primevue Icons
import Bookmark from '@primeicons/vue/bookmark'
import BookmarkFill from '@primeicons/vue/bookmark-fill' // will use for bookmarked content, wip
import Comment from '@primeicons/vue/comment'

// Custom Imports
import ForumContentCard from '@/Components/ForumContentCard.vue'

const visible = ref(false)
const props = defineProps({ post: Object, comments: Object })
const page = usePage()
const current_user = computed(() => page.props.auth?.current_user)

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
        }
    })
}

watch(() => props.post, (post) => {
    if (post) {
        form.post_id = post.post_id ?? null
    }
}, { immediate: true })
</script>