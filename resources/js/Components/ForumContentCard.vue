<template>
    <div class="flex flex-row gap-10">
        <div class="w-full">
            <div class="flex flex-row">
                <div class="flex flex-col justify-center m-10 w-max-200">
                    <UserAvatar :size="100" :img="data.user_photo" />
                    <Link :href="`/user/profile/${data.user_id}`">
                        <p class="text-xl font-bold">{{ data.username ?? "[deleted]" }}</p>
                    </Link>
                    {{ data.created_at }}
                </div>
                <Card class="w-full border" :pt="{ body: 'h-full flex flex-col justify-between' }">
                    <template #content>
                        <p class="mb-2 w-full whitespace-pre-wrap break-words">{{ data.description }}</p>
                    </template>
                    <template #footer>
                        <div class="p-6 rounded-lg" v-if="data.post_photo || data.comment_photo">
                            <Image :src="`/storage/${data.post_photo ?? data.comment_photo}`" width="200" preview />
                        </div>
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
                            <ButtonGroup class="float-left">
                                <Button variant="text" v-tooltip.top="{ value: 'Upvote' }" @click="onUpvoteSubmit()">
                                    <ThumbsUpFill v-if="data.has_upvoted == 1" />
                                    <ThumbsUp v-else />
                                    {{ data.upvotes }}
                                </Button>
                                <Button variant="text" severity="danger" v-tooltip.top="{ value: 'Report' }"
                                    @click="emit('report-requested', { data: data, type: type })">
                                    <Flag />
                                </Button>
                            </ButtonGroup>
                            <!-- Right side buttons - for author of the content -->
                            <ButtonGroup class="float-right">
                                <template v-if="current_user?.user_id === data.user_id">
                                    <Button variant="text" severity="secondary" v-tooltip.top="{ value: 'Edit' }"
                                        @click="emit('edit-requested', { data: data, type: type })">
                                        <PenLine />
                                    </Button>
                                    <Button variant="text" severity="danger" v-tooltip.top="{ value: 'Delete' }"
                                        @click="confirmDelete()">
                                        <Trash />
                                    </Button>
                                </template>
                            </ButtonGroup>
                        </template>
                    </template>
                </Card>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useConfirm } from "primevue/useconfirm"
import { usePage, router, Link } from '@inertiajs/vue3'

import Button from 'primevue/button'
import ButtonGroup from 'primevue/buttongroup'
import Card from 'primevue/card'
import Divider from 'primevue/divider'
import Image from 'primevue/image'

import ThumbsUp from '@primeicons/vue/thumbs-up'
import ThumbsUpFill from '@primeicons/vue/thumbs-up-fill'
import PenLine from '@primeicons/vue/pen-line'
import Flag from '@primeicons/vue/flag'
import Trash from '@primeicons/vue/trash'

import UserAvatar from '@/Components/UserAvatar.vue'

const confirm = useConfirm()
const page = usePage()
const actionURL = ref()
const current_user = computed(() => page.props.auth?.current_user)
const props = defineProps({ data: Object, type: String })
const emit = defineEmits(['deleted', 'updated', 'upvoted', 'edit-requested', 'report-requested'])

const determineUrl = (type) => {
    switch (type) {
        case 'post':
            return {
                upvote: `/forum/post/${props.data.post_id}/upvote`,
                delete: `/forum/post/${props.data.post_id}/delete`,
            }
        case 'comment':
            return {
                upvote: `/forum/comment/${props.data.comment_id}/upvote`,
                delete: `/forum/comment/${props.data.comment_id}/delete`,
            }
    }
}

const onUpvoteSubmit = () => {
    actionURL.value = determineUrl(props.type)
    router.post(actionURL.value.upvote, {}, {
        preserveScroll: true,
        only: [],
        onSuccess: () => {
            emit('upvoted', {
                type: props.type,
                post_id: props.type === 'post' ? props.data.post_id : null,
                comment_id: props.type === 'comment' ? props.data.comment_id : null,
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