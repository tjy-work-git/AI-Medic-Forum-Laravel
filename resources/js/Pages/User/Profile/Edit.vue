<template>
    <div class="w-full">
        <Deferred data="data">
            <template #fallback>
                <div class="flex justify-center items-center min-h-screen">
                    <ProgressSpinner />
                </div>
            </template>

            <Card>
                <template #header>
                    <div class="flex flex-row justify-between items-center p-4">
                        <h1 class="text-4xl font-bold">Edit Details</h1>
                        <Button severity="secondary" @click="goBack()" rounded>
                            <template #icon>
                                <ArrowLeft />
                            </template>
                        </Button>
                    </div>
                </template>
                <template #content>
                    <form id="update-form" @submit.prevent="onSubmit" method="post" class="space-y-6">
                        <!-- Profile Section -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-bold">Profile Details</h3>
                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- left avatar -->
                                <div class="flex flex-col items-center gap-4">
                                    <UserAvatar :user="props.data" :size="150" />
                                    <FileUpload 
                                        @select="(event) => form.user_photo = event.files[0]"
                                        mode="basic" 
                                        chooseLabel="Change" 
                                        accept="image/*" 
                                    />
                                </div>
                                <!-- right fields -->
                                <div class="flex-1 w-full space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-1">
                                            <label for="username">Username<span class="text-red-500">*</span></label>
                                            <InputText id="username" v-model="form.username" class="w-full" required />
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <label for="gender">Gender<span class="text-red-500">*</span></label>
                                            <Select 
                                                id="gender"
                                                class="w-full" 
                                                v-model="form.gender" 
                                                required
                                                :options="[{ label: 'Male', value: 'Male' }, { label: 'Female', value: 'Female' }]"
                                                optionLabel="label" 
                                                optionValue="value" 
                                            />
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <label for="bio">Bio</label>
                                        <Textarea id="bio" v-model="form.bio" placeholder="Leave something in your bio..." rows="4" class="w-full resize-none" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Section -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-bold">Security Details</h3>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <Checkbox inputId="email_change" v-model="form.email_change" binary />
                                    <label for="email_change" class="cursor-pointer">Enable email change</label>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label for="email">Email<span class="text-red-500">*</span></label>
                                    <InputText id="email" :disabled="!form.email_change" v-model="form.email" class="w-full" required />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <Checkbox inputId="password_change" v-model="form.password_change" binary />
                                    <label for="password_change" class="cursor-pointer">Enable password change</label>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col gap-1">
                                        <label for="password">Password<span class="text-red-500">*</span></label>
                                        <InputText 
                                            id="password" 
                                            type="password" 
                                            :disabled="!form.password_change" 
                                            v-model="form.password" 
                                            minlength="8" 
                                            class="w-full"
                                            required 
                                        />
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <label for="c_password">Confirm Password<span class="text-red-500">*</span></label>
                                        <InputText 
                                            id="c_password" 
                                            type="password" 
                                            :disabled="!form.password_change" 
                                            v-model="form.c_password" 
                                            minlength="8" 
                                            class="w-full"
                                            required 
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-2">
                                <Checkbox
                                    inputId="enable_auth"
                                    v-tooltip.top="{ value: 'Enable authentication will force an extra login step that requires a 6-digit one-time password sent to your email.' }"
                                    v-model="form.enable_auth" 
                                    binary 
                                />
                                <label for="enable_auth" class="cursor-pointer">Enable Authentication</label>
                            </div>
                        </div>
                    </form>
                </template>
                <template #footer>
                    <div class="flex flex-row justify-center items-center gap-4">
                        <Button class="w-full my-4" label="Delete Account" severity="danger" @click="deleteVisible = true" />
                        <Button class="w-full my-4" type="submit" form="update-form" label="Update" />
                    </div>
                </template>
            </Card>
        </Deferred>

        <Dialog v-model:visible="deleteVisible" modal header="Delete Account" class="w-full max-w-md">
            <div class="py-4 space-y-3">
                <p>Are you sure you want to delete your account? Once deleted, you no longer have access to this account.</p>
                <p class="font-medium">The following data may still persist after you have deleted your account:</p>
                <ul class="list-disc pl-6 space-y-1 text-sm">
                    <li>Your post and comment history</li>
                    <li>Your upvotes</li>
                </ul>
                <p>If you wish to proceed, please enter your full email address below:</p>
            </div>

            <form id="delete-form" @submit.prevent="deleteAccount" method="post">
                <InputText class="w-full" v-model="deleteForm.delete_email" placeholder="Enter your email" />
            </form>

            <template #footer>
                <Button label="No" icon="pi pi-times" @click="deleteVisible = false" severity="secondary" />
                <Button label="Yes" icon="pi pi-check" type="submit" form="delete-form" severity="danger" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
// Libraries
import { watch, ref } from 'vue'
import { useForm, Deferred } from '@inertiajs/vue3'

// Primevue
import Button from 'primevue/button'
import Card from 'primevue/card'
import Checkbox from 'primevue/checkbox'
import Dialog from 'primevue/dialog'
import FileUpload from 'primevue/fileupload'
import InputText from 'primevue/inputtext'
import ProgressSpinner from 'primevue/progressspinner'
import Select from 'primevue/select'
import Textarea from 'primevue/textarea'

// Primevue Icons
import ArrowLeft from '@primeicons/vue/arrow-left'

// Custom import
import UserAvatar from '@/Components/UserAvatar.vue'

const props = defineProps({ data: Object })
const deleteVisible = ref(false)

const form = useForm({
    username: '',
    bio: '',
    gender: '',
    email: '',
    password: '',
    c_password: '',
    email_change: false,
    password_change: false,
    enable_auth: false,
    user_photo: null,
})

const deleteForm = useForm({
    delete_email: '',
})

const onSubmit = () => {
    form.post('/user/profile/update')
}

const deleteAccount = () => {
    deleteForm.post('/user/profile/delete')
}

const goBack = () => {
    window.history.back()
}

watch(() => props.data, (data) => {
    if (data) {
        form.username = data.username ?? ''
        form.bio = data.bio ?? ''
        form.gender = data.gender ?? ''
        form.email = data.email ?? ''
        form.enable_auth = data.enabled_auth ?? false
    }
}, { immediate: true })
</script>