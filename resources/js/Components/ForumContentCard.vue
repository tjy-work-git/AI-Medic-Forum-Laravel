<template>
    <!-- Report Dialog -->
    <Dialog v-model:visible="reportDialogVisible" class="w-1/2" modal>
        <template #header>
            <h2 class="text-2xl font-bold">{{ type == 'post' ? 'Report post' : 'Report comment' }}</h2>
        </template>
        <!-- TODO: implement form -->
        <!--
        <form @submit.prevent="onSubmit()">
            <Textarea v-model="data.description" class="w-full" rows="10" autoResize placeholder="Say something..." />
            <div class="flex flex-col">
                <label for="img">Add Image (optional):</label>
                <FileUpload v-model="form.img" chooselabel="Browse" accept="image/*" />
            </div>
            <div class="flex justify-end gap-2 my-4">
                <Button severity="secondary" @click="reportDialogVisible = false">Cancel</Button>
                <Button type="submit" label="Submit" />
            </div>
        </form>
        -->
    </Dialog>

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
                <p class="mb-2">{{ data.description }}</p>
            </template>
            <template #footer>
                <Divider />
                <ButtonGroup>
                    <Button v-tooltip.top="{ value: 'Upvote' }">
                        <ThumbsUp />
                        {{ data.upvotes }}
                    </Button>
                    <Button v-tooltip.top="{ value: 'Report' }" @click="reportDialogVisible = true">
                        <Flag />
                    </Button>
                    <template v-if="current_user.user_id === data.user_id">
                        <Button v-tooltip.top="{ value: 'Edit' }" @click="selectEdit(data)">
                            <PenLine />
                        </Button>
                        <Button v-tooltip.top="{ value: 'Delete' }" @click="confirmDelete()">
                            <Trash />
                        </Button>
                    </template>
                </ButtonGroup>
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
import Textarea from 'primevue/textarea'

import ThumbsUp from '@primeicons/vue/thumbs-up'
import ThumbsUpFill from '@primeicons/vue/thumbs-up-fill' // will use for upvoted content, wip
import PenLine from '@primeicons/vue/pen-line'
import Flag from '@primeicons/vue/flag'
import Trash from '@primeicons/vue/trash'

import UserAvatar from '@/Components/UserAvatar.vue'

const confirm = useConfirm()
const page = usePage()
const selectedEdit = ref()
const reportDialogVisible = ref(false)
const editDialogVisible = ref(false)
const current_user = computed(() => page.props.auth?.current_user)
const props = defineProps({ data: Object, type: String })
const emit = defineEmits(['deleted'])

const editForm = useForm({
    description: ''
})

const selectEdit = (data) => {
    editForm.description = data.description
    editDialogVisible.value = true
}

const onEditSubmit = () => {
    const updateUrl = computed(() => {
        switch (props.type) {
            case 'post':
                return `/forum/post/${props.data.post_id}/update`
            case 'comment':
                return `/forum/comment/${props.data.comment_id}/update`
        }
    })

    router.post(updateUrl.value, editForm, {
        preserveScroll: true,
        onSuccess: () => {
            emit('updated')
            editDialogVisible.value = false
        }
    })
}

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
        accept: () => {
            const deleteUrl = computed(() => {
                switch (props.type) {
                    case 'post':
                        return `/forum/post/${props.data.post_id}/delete`
                    case 'comment':
                        return `/forum/comment/${props.data.comment_id}/delete`
                }
            })
            router.post(deleteUrl.value, {}, {
                preserveScroll: true,
                onSuccess: () => {
                    emit('deleted')
                }
            })
        },
    });
};
</script>