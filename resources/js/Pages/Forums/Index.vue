<template>
    <!-- Sticky add post button -->
    <Link href="/forum/post/create" form="searchForm" style="position: fixed; bottom: 60px; right: 30px; z-index: 100;">
        <Button rounded size="xlarge">
            <template #icon>
                <Plus />
            </template>
        </Button>
    </Link>

    <h1 class="text-4xl font-bold m-4">Forums</h1>
    <div class="flex flex-row gap-2">
        <Card class="w-1/4">
            <template #title>
                <h2>Search</h2>
            </template>
            <template #content>
                <form @submit.prevent="searchForm.get('/forum')">
                    <div class="flex flex-col gap-2">
                        <InputText class="w-full" v-model="searchForm.search" placeholder="Search keywords..." />
                        <Button label="Search">
                            <template #icon>
                                <Search />
                            </template>
                        </Button>
                    </div>
                    <div class="flex flex-col my-4 gap-2">
                        <div>
                            <label for="searchType">Search by </label>
                            <Select v-model="searchForm.searchType"
                                class="w-full"
                                :options="[{ label: 'Post', value: 'post' }, { label: 'User', value: 'user' }]"
                                optionLabel="label" optionValue="value" />
                        </div>
                        <div>
                            <label for="sortType">Sorting </label>
                            <Select v-model="searchForm.sortType"
                                class="w-full"
                                :options="[{ label: 'Latest', value: 'latest' }, { label: 'Alphabetically', value: 'alphabet' }, { label: 'Upvotes', value: 'upvote' }]"
                                optionLabel="label" optionValue="value" />
                        </div>
                        <div>
                            <label for="sortOrder">Order </label>
                            <Select v-model="searchForm.sortOrder"
                                class="w-full"
                                :options="[{ label: 'Descending', value: 'desc' }, { label: 'Ascending', value: 'asc' }]"
                                optionLabel="label" optionValue="value" />
                        </div>
                    </div>
                </form>
            </template>
        </Card>
        <div class="w-full mb-100px">
            <Deferred data="posts">
                <template #fallback>
                    <ProgressSpinner />
                </template>

                <template #default>
                    <template v-if="posts.data.length == 0">
                        <p class="flex justify-center items-center">No posts found</p>
                    </template>

                    <template v-else>
                        <template v-for="post in posts.data" :key="post.post_id">
                            <Link :href="'/forum/post/' + post.post_id">
                                <Card class="mb-4">
                                    <template #title>
                                        <h2 class="text-xl font-bold">{{ post.title }}</h2>
                                    </template>
                                    <template #content>
                                        <p class="mb-2">{{ post.description.substring(0, 100) + "..." }}</p>
                                    </template>
                                    <template #footer>
                                        <div class="flex flex-wrap justify-between">
                                            <p>{{ post.username ?? "[deleted]" }} on {{ post.created_at }}</p>
                                            <p>{{ post.upvotes }} Upvotes</p>
                                        </div>
                                    </template>
                                </Card>
                            </Link>
                        </template>
                    </template>
                </template>
            </Deferred>
        </div>
    </div>
</template>

<script setup>
// Libraries
import { Link, Deferred, useForm } from '@inertiajs/vue3'

// Primevue
import Button from 'primevue/button'
import Card from 'primevue/card'
import InputText from 'primevue/inputtext';
import ProgressSpinner from 'primevue/progressspinner';
import Select from 'primevue/select';

// Primevue Icons
import Plus from '@primeicons/vue/plus'
import Search from '@primeicons/vue/search'

const props = defineProps({ posts: Object })

const searchForm = useForm({
    search: '',
    searchType: 'post',
    sortType: 'latest',
    sortOrder: 'desc',
})
</script>