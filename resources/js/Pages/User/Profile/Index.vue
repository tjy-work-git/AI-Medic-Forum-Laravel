<template>
    <div class="flex flex-row gap-4">
        <ProfileSidebar />

        <div class="flex flex-col w-full gap-4">
            <Deferred data="profile_data">
                <template #fallback>
                    <div class="flex justify-center items-center min-h-screen">
                        <ProgressSpinner />
                    </div>
                </template>

                <Card>
                    <template #header>
                        <h1 class="text-4xl font-bold m-4">Profile</h1>
                    </template>
                    <template #content>
                        <div class="flex flex-row m-4 gap-4">
                            <UserAvatar :size="'xlarge'" :user="profile_data" />
                            <div class="flex flex-col">
                                <h3 class="text-2xl font-bold">{{ profile_data.username }}</h3>
                                <p class="font-bold">{{ profile_data.role }}</p>
                                <p>{{ profile_data.gender }}</p>
                                <p>Joined on {{ profile_data.created_at }}</p>
                                <p><b>{{ profile_data.total_upvotes }} upvotes from contribution</b></p>
                                <i>{{ profile_data.bio ?? 'This user have not left anything here yet...' }}</i>
                                <Link v-if="profile_data.user_id === current_user.user_id" href="/user/profile/edit">
                                    <Button label="Edit" />
                                </Link>
                            </div>
                        </div>
                    </template>
                </Card>

                <PostHistory :id="profile_data.user_id" />
            </Deferred>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, Deferred, Link } from '@inertiajs/vue3'

import Button from 'primevue/button'
import Card from 'primevue/card'
import ProgressSpinner from 'primevue/progressspinner'

import PostHistory from '@/Components/PostHistory.vue'
import ProfileSidebar from '@/Components/ProfileSidebar.vue'
import UserAvatar from '@/Components/UserAvatar.vue'

const page = usePage()
const current_user = computed(() => page.props.auth?.current_user)
const props = defineProps({ profile_data: Object })
</script>