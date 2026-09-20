<template>
    <h1 class="text-4xl font-bold m-4">Create New Post</h1>
    <Card>
        <template #content>
            <form id="form" @submit.prevent="onSubmit()" method="post">
                <div class="flex flex-col gap-2">
                    <div class="flex flex-col">
                    <label for="title">Title<span style="color: red;">*</span></label>
                    <InputText v-model="form.title" placeholder="Enter your title..." style="width: 100%;" required />
                    </div>

                    <div class="flex flex-col">
                        <label for="description">Description<span style="color: red;">*</span> </label>
                        <Textarea v-model="form.description" placeholder="Enter your description..." rows="5" style="width: 100%; resize:none;" required />
                    </div>

                    <div class="flex flex-col py-2 gap-2">
                        <label for="post_photo"><b>Add Image</b> <span class="text-sm">(optional)</span></label>
                        <FileUpload @select="(event) => form.post_photo = event.files[0]" mode="basic" chooselabel="Browse" accept="image/*" />
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <Button type="submit" label="Create Post" form="form" class="float-right">
                <template #icon>
                    <Plus />
                </template>
            </Button>
        </template>
    </Card>
</template>

<script setup>
// Libraries
import { useForm } from '@inertiajs/vue3'

// Primevue
import Button from 'primevue/button'
import Card from 'primevue/card'
import FileUpload from 'primevue/fileupload'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'

// Primevue Icons
import Plus from '@primeicons/vue/plus'

const form = useForm({
    title: '',
    description: '',
    post_photo: null
})

const onSubmit = () => {
    form.post('/forum/post/store')
}
</script>

