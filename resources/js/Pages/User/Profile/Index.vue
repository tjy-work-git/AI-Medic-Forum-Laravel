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
                        <div class="flex flex-row w-full">
                            <UserAvatar class="mx-4" :size="150" :img="profile_data.user_photo" />
                            <div class="flex flex-col mx-10 gap-2">
                                <h3 class="text-2xl font-bold">
                                    {{ profile_data.username }}
                                    <span v-tooltip.right="{ value: 'Joined on ' + profile_data.created_at }" class="ml-2">
                                        <Shield v-if="profile_data.role == 'Admin'"/>
                                        <User v-else/>
                                    </span>
                                </h3>
                                <p>{{ profile_data.gender }}</p>
                                <div class="flex flex-wrap gap-2">
                                    <Badge :value="profile_data.post_upvotes + ' Post Upvotes Received'" size="xlarge" severity="secondary"/>
                                    <Badge :value="profile_data.comment_upvotes + ' Comment Upvotes Received'" size="xlarge" severity="secondary"/>
                                    <Badge :value="profile_data.total_upvotes + ' Total Upvotes Received'" size="xlarge" severity="secondary"/>
                                </div>
                                <Link v-if="profile_data.user_id === current_user.user_id" href="/user/profile/edit">
                                    <Button label="Edit Profile" size="small">
                                        <template #icon>
                                            <UserEdit />
                                        </template>
                                    </Button>
                                </Link>
                            </div>
                        </div>
                        <p class="m-5">{{ profile_data.bio ?? 'No bio information.' }}</p>
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

import Badge from 'primevue/badge'
import Button from 'primevue/button'
import Card from 'primevue/card'
import ProgressSpinner from 'primevue/progressspinner'

import Shield from '@primeicons/vue/shield'
import User from '@primeicons/vue/user'
import UserEdit from '@primeicons/vue/user-edit'

import PostHistory from '@/Components/PostHistory.vue'
import ProfileSidebar from '@/Components/ProfileSidebar.vue'
import UserAvatar from '@/Components/UserAvatar.vue'

const page = usePage()
const current_user = computed(() => page.props.auth?.current_user)
const props = defineProps({ profile_data: Object })
</script>