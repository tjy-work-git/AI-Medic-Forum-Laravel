<template>
    <!-- Floating comment button - triggers comment dialog-->
    <Button v-if="current_user" rounded @click="commentDiagVisible = true"
        style="position: fixed; bottom: 30px; right: 30px; z-index: 100;">
        <template #icon>
            <Comment />
        </template>
    </Button>

    <!-- Floating summary button -->
    <Button v-if="current_user" rounded @click="summary ? summaryResultDiagVisible = true : summaryDiagVisible = true"
        variant="outlined" style="position: fixed; bottom: 90px; right: 30px; z-index: 100;">
        <template #icon>
            <Sparkles />
        </template>
    </Button>

    <!-- Comment Dialog -->
    <Dialog v-model:visible="commentDiagVisible" class="w-1/2" modal>
        <template #header>
            <h2 class="text-2xl font-bold">Leave a comment</h2>
        </template>
        <form @submit.prevent="onCommentSubmit()">
            <Textarea v-model="commentForm.description" class="w-full" rows="10" autoResize
                placeholder="Say something..." />
            <div class="flex flex-col py-2 gap-2">
                <label for="comment_photo"><b>Add Image</b> <span class="text-sm">(optional)</span></label>
                <FileUpload @select="(event) => commentForm.comment_photo = event.files[0]" mode="basic"
                    chooseLabel="Browse" accept="image/*" />
            </div>
            <div class="flex justify-end gap-2 my-4">
                <Button severity="secondary" @click="commentDiagVisible = false">Cancel</Button>
                <Button type="submit" :disabled="isLoading" :loading="isLoading" label="Comment" />
            </div>
        </form>
    </Dialog>

    <!-- Summary Dialog -->
    <Dialog v-model:visible="summaryDiagVisible" class="w-1/2" modal>
        <template #header>
            <h2 class="text-2xl font-bold">Summary</h2>
        </template>
        <form @submit.prevent="onSummarySubmit()">
            <p>Select your summary preference</p>
            <Select v-model="summaryForm.preference" class="w-full" :options="[
                { label: 'General (simple and brief)', value: '1' },
                { label: 'Abstractive (more detailed)', value: '2' },
                { label: 'Extractive (main points)', value: '3' },
            ]" optionLabel="label" optionValue="value" />
            <div class="flex justify-end gap-2 my-4">
                <Button type="submit" :disabled="isLoading" :loading="isLoading" label="Summarize" />
            </div>
        </form>
    </Dialog>

    <!-- Summarized Content Dialog - triggered if summary is received -->
    <Dialog v-model:visible="summaryResultDiagVisible" class="w-1/2" modal>
        <template #header>
            <h2 class="text-2xl font-bold">Summary</h2>
        </template>
        <div class="w-full" v-html="summary"></div>
        <div class="flex justify-end gap-2 my-4">
            <Button type="button" label="Summarize again"
                @click="summaryResultDiagVisible = false; summaryDiagVisible = true" />
        </div>
    </Dialog>

    <!-- Edit Dialog (shared singleton - lifted from ForumContentCard) -->
    <Dialog v-model:visible="editDialogVisible" class="w-1/2" modal>
        <template #header>
            <h2 class="text-2xl font-bold">{{ selectedCard?.type === 'post' ? 'Edit post' : 'Edit comment' }}</h2>
        </template>
        <form @submit.prevent="onEditSubmit()">
            <Textarea v-model="editForm.description" class="w-full" rows="10" autoResize />
            <div class="flex justify-end gap-2 my-4">
                <Button severity="secondary" @click="editDialogVisible = false">Cancel</Button>
                <Button type="submit" label="Save" />
            </div>
        </form>
    </Dialog>

    <!-- Report Dialog (shared singleton - lifted from ForumContentCard) -->
    <Dialog v-model:visible="reportDialogVisible" class="w-1/2" modal>
        <template #header>
            <h2 class="text-2xl font-bold">{{ selectedCard?.type === 'post' ? 'Report post' : 'Report comment' }}</h2>
        </template>
        <form @submit.prevent="onReportSubmit()">
            <p>Provide a reason...</p>
            <Select v-model="reportForm.reason" class="w-full" :options="[
                { label: 'Spam', value: 'spam' },
                { label: 'Profanity', value: 'profanity' },
                { label: 'False Information', value: 'false-information' },
                { label: 'Other', value: 'other' },
            ]" optionLabel="label" optionValue="value" />
            <div class="flex justify-end gap-2 my-4">
                <Button severity="secondary" @click="reportDialogVisible = false">Cancel</Button>
                <Button type="submit" severity="danger" label="Report" />
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
                <Button class="mx-6" variant="outlined" :disabled="isLoading" :loading="isLoading" rounded
                    @click="onBookmarkSubmit">
                    <template #icon>
                        <BookmarkFill v-if="post.has_bookmarked == 1" v-tooltip.bottom="{ value: 'Remove Bookmark' }" />
                        <Bookmark v-else v-tooltip.bottom="{ value: 'Bookmark' }" />
                    </template>
                </Button>
                <h1 class="text-4xl font-bold my-6">{{ post.title }}</h1>
            </div>
        </div>

        <ForumContentCard :data="post" type="post"
            @updated="handleUpdated"
            @edit-requested="handleEditRequested"
            @report-requested="handleReportRequested" />

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
                <ForumContentCard :data="comment" type="comment"
                    @deleted="handleDeleted"
                    @updated="handleUpdated"
                    @edit-requested="handleEditRequested"
                    @report-requested="handleReportRequested" />
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
import Select from 'primevue/select'
import Textarea from 'primevue/textarea'
import ProgressSpinner from 'primevue/progressspinner'

// Primevue Icons
import Bookmark from '@primeicons/vue/bookmark'
import BookmarkFill from '@primeicons/vue/bookmark-fill'
import Comment from '@primeicons/vue/comment'
import Sparkles from '@primeicons/vue/sparkles'

// Custom Imports
import ForumContentCard from '@/Components/ForumContentCard.vue'

const page = usePage()
const commentDiagVisible = ref(false)
const summaryDiagVisible = ref(false)
const summaryResultDiagVisible = ref(false)
const editDialogVisible = ref(false)
const reportDialogVisible = ref(false)
const isLoading = ref(false)
const post = ref(null)
const comments = ref([])
const summary = ref('')
const selectedCard = ref(null) // tracks which card triggered edit/report
const props = defineProps({ post_data: Object, comments_data: Object })
const current_user = computed(() => page.props.auth?.current_user)

const commentForm = useForm({
    description: null,
    comment_photo: null,
    post_id: null,
})

const summaryForm = useForm({
    preference: '1'
})

const editForm = useForm({
    description: ''
})

const reportForm = useForm({
    reason: '',
    opt_description: '',
})

// Immediate delete
const handleDeleted = (commentId) => {
    comments.value = comments.value.filter(c => c.comment_id !== commentId)
    if (props.comments_data?.total) props.comments_data.total--
}

// Immediate update (handles both post and comment)
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

// Open shared edit dialog for a card
const handleEditRequested = ({ data, type }) => {
    selectedCard.value = { data, type }
    editForm.description = data.description
    editDialogVisible.value = true
}

// Open shared report dialog for a card
const handleReportRequested = ({ data, type }) => {
    selectedCard.value = { data, type }
    reportDialogVisible.value = true
}

const onCommentSubmit = () => {
    isLoading.value = true
    commentForm.post('/forum/comment/store', {
        preserveScroll: true,
        onSuccess: () => {
            commentDiagVisible.value = false
            commentForm.reset('description', 'comment_photo')
        },
        onFinish: () => {
            isLoading.value = false
        }
    })
}

const onBookmarkSubmit = () => {
    router.post(`/forum/post/${post.value.post_id}/bookmark`), {
        preserveScroll: true,
        only: ['flash'],
    }
}

const onEditSubmit = () => {
    if (!selectedCard.value) return
    const { data, type } = selectedCard.value
    const url = type === 'post'
        ? `/forum/post/${data.post_id}/update`
        : `/forum/comment/${data.comment_id}/update`

    router.post(url, editForm, {
        preserveScroll: true,
        only: ['flash'],
        onSuccess: () => {
            handleUpdated({
                type,
                post_id: type === 'post' ? data.post_id : null,
                comment_id: type === 'comment' ? data.comment_id : null,
                description: editForm.description
            })
            editDialogVisible.value = false
        }
    })
}

const onReportSubmit = () => {
    if (!selectedCard.value) return
    const { data, type } = selectedCard.value
    const url = type === 'post'
        ? `/forum/post/${data.post_id}/report`
        : `/forum/comment/${data.comment_id}/report`

    router.post(url, reportForm, {
        preserveScroll: true,
        only: ['flash'],
        onSuccess: () => {
            reportForm.reset()
            reportDialogVisible.value = false
        }
    })
}

const onSummarySubmit = async () => {
    isLoading.value = true
    try {
        const response = await fetch(`/forum/summarize/${post.value.post_id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(summaryForm.data())
        })

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }

        const data = await response.json()
        summary.value = data.summary
        summaryDiagVisible.value = false
        summaryResultDiagVisible.value = true
    } catch (error) {
        console.error('Fetch request failed:', error)
    } finally {
        isLoading.value = false
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