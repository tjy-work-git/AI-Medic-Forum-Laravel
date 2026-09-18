<template>
    <!-- Edit Dialog -->
    <Dialog v-model:visible="editDialogVisible" class="w-1/2" modal>
        <template #header>
            <h2 class="text-2xl font-bold">{{ type == 'post' ? 'Edit post' : 'Edit comment' }}</h2>
        </template>
        <form @submit.prevent="onEditSubmit()">
            <Textarea v-model="editForm.description" class="w-full" rows="10" autoResize />
            <div class="flex justify-end gap-2 my-4">
                <Button severity="secondary" @click="editDialogVisible = false">Cancel</Button>
                <Button type="submit" label="Save" />
            </div>
        </form>
    </Dialog>

    <!-- Report Dialog -->
    <Dialog v-model:visible="reportDialogVisible" class="w-1/2" modal>
        <template #header>
            <h2 class="text-2xl font-bold">{{ type == 'post' ? 'Report post' : 'Report comment' }}</h2>
        </template>
        <form @submit.prevent="onSubmit()">
            <Textarea v-model="data.description" class="w-full" rows="10" autoResize placeholder="Say something..." />
            <p>Provide a reason...</p>
            <Select v-model="reportForm.searchType"
                class="w-full"
                :options="[{ label: 'Post', value: 'post' }, { label: 'User', value: 'user' }]"
                optionLabel="label" optionValue="value" />
            <div class="flex justify-end gap-2 my-4">
                <Button severity="secondary" @click="editDialogVisible = false">Cancel</Button>
                <Button type="submit" label="Save" />
            </div>
        </form>
    </Dialog>

    <div class="flex flex-row gap-10">
        <Card class="w-max-200 border">
            <template #header>
                <div class="flex justify-center m-10">
                    <UserAvatar :size="100" :img="data.user_photo" />
                </div>
            </template>
            <template #content>
                <Link :href="`/user/profile/${data.user_id}`">
                    <p class="text-xl font-bold">{{ data.username ?? "[deleted]" }}</p>
                </Link>
                {{ data.created_at }}
            </template>
        </Card>

        <Card class="w-full" :pt="{ body: 'h-full flex flex-col justify-between' }">
            <template #content>
                <p class="mb-2 w-full whitespace-pre-wrap break-words">{{ data.description }}</p>
            </template>
            <template #footer>
                <Divider />
                <template v-if="!current_user">
                    <!-- Simple upvote display -->
                    <div class="secondary">
                        <ThumbsUp />
                        {{ data.upvotes }}
                    </div>
                </template>
                <template v-else>
                    <!-- Left side buttons - general functions -->
                    <ButtonGroup class="float-left" @click="onUpvoteSubmit()">
                        <Button variant="text" v-tooltip.top="{ value: 'Upvote' }">
                            <ThumbsUpFill v-if="data.has_upvoted == 1" />
                            <ThumbsUp v-else />
                            {{ data.upvotes }}
                        </Button>
                        <Button variant="text" severity="danger" v-if="current_user?.user_id !== data.user_id" v-tooltip.top="{ value: 'Report' }" @click="reportDialogVisible = true">
                            <Flag />
                        </Button>
                    </ButtonGroup>
                    <!-- Right side buttons - for author of the content-->
                    <ButtonGroup class="float-right">
                        <template v-if="current_user?.user_id === data.user_id">
                            <Button variant="text" severity="secondary" v-tooltip.top="{ value: 'Edit' }" @click="selectEdit(data)">
                                <PenLine />
                            </Button>
                            <Button variant="text" severity="danger" v-tooltip.top="{ value: 'Delete' }" @click="confirmDelete()">
                                <Trash />
                            </Button>
                        </template>
                    </ButtonGroup>
                </template>
            </template>
        </Card>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useConfirm } from "primevue/useconfirm"
import { useForm, usePage, router, Link } from '@inertiajs/vue3'

import Button from 'primevue/button'
import ButtonGroup from 'primevue/buttongroup'
import Card from 'primevue/card'
import Dialog from 'primevue/dialog'
import Divider from 'primevue/divider'
import Select from 'primevue/select'
import Textarea from 'primevue/textarea'

import ThumbsUp from '@primeicons/vue/thumbs-up'
import ThumbsUpFill from '@primeicons/vue/thumbs-up-fill'
import PenLine from '@primeicons/vue/pen-line'
import Flag from '@primeicons/vue/flag'
import Trash from '@primeicons/vue/trash'

import UserAvatar from '@/Components/UserAvatar.vue'

const confirm = useConfirm()
const page = usePage()
const reportDialogVisible = ref(false)
const editDialogVisible = ref(false)
const actionURL = ref()
const current_user = computed(() => page.props.auth?.current_user)
const props = defineProps({ data: Object, type: String })
const emit = defineEmits(['deleted', 'updated', 'upvoted']) 

const editForm = useForm({
    description: ''
})

const selectEdit = (data) => {
    editForm.description = data.description
    editDialogVisible.value = true
}

const determineUrl = (type) => {
    switch (type) {
        case 'post':
            return {
                upvote: `/forum/post/${props.data.post_id}/upvote`,
                update: `/forum/post/${props.data.post_id}/update`,
                delete: `/forum/post/${props.data.post_id}/delete`
            }
        case 'comment':
            return {
                upvote: `/forum/comment/${props.data.comment_id}/upvote`,
                update: `/forum/comment/${props.data.comment_id}/update`,
                delete: `/forum/comment/${props.data.comment_id}/delete`
            }
    }
}

const onEditSubmit = () => {
    actionURL.value = determineUrl(props.type)
    router.post(actionURL.value.update, editForm, {
        preserveScroll: true,
        only: ['flash'],
        onSuccess: () => {
            emit('updated', {
                post_id: props.data.post_id ?? null,
                comment_id: props.data.comment_id ?? null,
                description: editForm.description
            })
            editDialogVisible.value = false
        }
    })
}

const onUpvoteSubmit = () => {
    actionURL.value = determineUrl(props.type)
    router.post(actionURL.value.upvote, {}, {
        preserveScroll: true,
        only: [],
        onSuccess: () => {
            emit('upvoted', {
                post_id: props.data.post_id ?? null,
                comment_id: props.data.comment_id ?? null,
            })
        }
    })
}

const onDeleteSubmit = () => {
    actionURL.value = determineUrl(props.type)
    router.post(actionURL.value.delete, {}, {
        preserveScroll: true,
        only: ['flash'],
        onSuccess: () => {
            // deleting post will wipe everything and redirect - so only comment need this for instant feedback
            emit('deleted', props.data.comment_id)
        }
    })
}

// For primevue confirm dialog
const confirmDelete = () => {
    confirm.require({
        message: 'Do you want to delete this content?',
        header: 'Danger Zone',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger'
        },
        accept: onDeleteSubmit
    });
};
</script>