<template>
    <!-- Edit Dialog -->
    <Card class="w-1/6 border">
        <template #header>
            <div class="flex justify-center m-10">
                <UserAvatar :size="100" :img="data.user_photo"/>
            </div>
        </template>
        <template #content>
            <Link :href="`/user/profile/${data.user_id}`">
                <p class="text-xl font-bold">{{ data.username ?? "[deleted]" }}</p>
            </Link>
            {{ data.created_at }}
        </template>
        <template #footer>
            <!-- Button Group : Up, Report, Edit Delete (later implement logic, do design) -->
            <div class="my-4 flex justify-center w-full">
                <!-- Non-OP actions first, then finally OP actions (Edit, Delete) -->
                <ButtonGroup>
                    <Button v-tooltip.top="{ value: 'Upvote' }">
                        <ThumbsUp />
                        {{ data.upvotes }}
                    </Button>
                    <Button v-tooltip.top="{ value: 'Report' }">
                        <Flag />
                    </Button>
                    <template v-if="current_user.user_id === data.user_id">
                        <Button v-tooltip.top="{ value: 'Edit' }">
                            <PenLine />
                        </Button>
                        <Button v-tooltip.top="{ value: 'Delete' }" @click="confirmDelete()">
                            <Trash />
                        </Button>
                    </template>
                </ButtonGroup>
            </div>
        </template>
    </Card>
</template>

<script setup>
// Libraries
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useConfirm } from "primevue/useconfirm"

// Primevue
import Button from 'primevue/button'
import ButtonGroup from 'primevue/buttongroup'
import Card from 'primevue/card'

// Primevue Icons
import ThumbsUp from '@primeicons/vue/thumbs-up'
import ThumbsUpFill from '@primeicons/vue/thumbs-up-fill' // will use for upvoted content, wip
import PenLine from '@primeicons/vue/pen-line'
import Flag from '@primeicons/vue/flag'
import Trash from '@primeicons/vue/trash'

// Custom Components
import UserAvatar from '@/Components/UserAvatar.vue'

const props = defineProps({ data: Object })
const confirm = useConfirm()
const page = usePage()
const current_user = computed(() => page.props.auth?.current_user)

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
            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Record deleted', life: 3000 }); // change later to submit a request
        },
    });
};
</script>