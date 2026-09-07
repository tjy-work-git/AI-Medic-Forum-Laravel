<template>
    <div class="flex flex-row gap-4">

        <div class="flex flex-col w-full gap-4">
            <Deferred data="data">
                <template #fallback>
                    <div class="flex justify-center items-center min-h-screen">
                        <ProgressSpinner />
                    </div>
                </template>

                <Card>
                    <template #header>
                        <div class="flex flex-row justify-between items-center">
                            <h1 class="text-4xl font-bold m-4">Edit Details</h1>
                            <Button severity="secondary" @click="goBack()" class="mx-6" rounded>
                                <template #icon>
                                    <ArrowLeft />
                                </template>
                            </Button>
                        </div>
                    </template>
                    <template #content>
                        <form id="update-form" @submit.prevent="onSubmit" method="post">
                            <!-- Profile -->
                            <div class="flex flex-col gap-2">
                                <h3 class="text-xl font-bold">Profile Details</h3>
                                <div class="flex flex-col">
                                    <label for="username">Username<span style="color: red;">*</span> </label>
                                    <InputText v-model="form.username" required />
                                </div>
                                <div class="flex flex-col">
                                    <label for="bio">Bio</label>
                                    <Textarea v-model="form.bio" />
                                </div>
                                <div class="flex flex-col">
                                    <label for="gender">Gender<span style="color: red;">*</span></label>
                                    <Select class="w-full border rounded" v-model="form.gender" required
                                        :options="[{ label: 'Male', value: 'Male' }, { label: 'Female', value: 'Female' }]"
                                        optionLabel="label" optionValue="value" />
                                </div>

                                <!-- Security -->
                                <h3 class="text-xl font-bold">Security Details</h3>
                                <div class="py-2">
                                    <Checkbox v-model="form.email_change" binary />
                                    <label for="email_change"> Enable email change </label>
                                </div>
                                <div class="flex flex-col">
                                    <label for="email">Email<span style="color: red;">*</span> </label>
                                    <InputText :disabled="!form.email_change" v-model="form.email" required />
                                </div>
                                <div class="py-2">
                                    <Checkbox v-model="form.password_change" binary />
                                    <label for="password_change"> Enable password change </label>
                                </div>
                                <div class="flex flex-col">
                                    <label for="email">Password<span style="color: red;">*</span> </label>
                                    <InputText :disabled="!form.password_change" v-model="form.password" min="8" required />
                                </div>
                                <!-- If a checkbox is enabled then these fields is enabled -->
                                <div class="flex flex-col">
                                    <label for="c_password">Confirm Password<span style="color: red;">*</span> </label>
                                    <InputText :disabled="!form.password_change" v-model="form.c_password" min="8"
                                        required />
                                </div>
                                <div class="py-2">
                                    <Checkbox
                                        v-tooltip.top="{ value: 'Enable authentication will force an extra login steps that requires a 6-digit one-time password sent to your email.' }"
                                        v-model="form.enable_auth" binary />
                                    <label for="enable_auth">
                                        Enable Authentication </label>
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

            <Dialog v-model:visible="deleteVisible" modal header="Delete Account" :style="{ width: '25rem' }">
                <div class="py-4">
                    <p>Are you sure you want to delete your account? Once deleted, you no longer have access to this account.</p>
                    <br>
                    <p>The following data may still persist after you have deleted your account: </p>
                    <ul class="list-disc pl-10">
                        <li>Your post and comment history</li>
                        <li>Your upvotes</li>
                    </ul>
                    <br>
                    <p>If you wish to proceed, please enter your full email address below: </p>
                </div>
                
                <form id="delete-form" @submit.prevent="deleteAccount" method="post">
                    <InputText class="w-full" v-model="deleteForm.delete_email" />
                </form>

                <template #footer>
                    <Button label="No" icon="pi pi-times" @click="deleteVisible = false" severity="secondary" />
                    <Button label="Yes" icon="pi pi-check" type="submit" form="delete-form" severity="danger" />
                </template>
            </Dialog>
        </div>
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
import InputText from 'primevue/inputtext'
import ProgressSpinner from 'primevue/progressspinner'
import Select from 'primevue/select'
import Textarea from 'primevue/textarea'

// Primevue Icons
import ArrowLeft from '@primeicons/vue/arrow-left'

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