<template>
    <div class="flex flex-row gap-4">
        <ProfileSidebar />

        <div class="flex flex-col w-full gap-4">
            <Deferred data="data">
                <template #fallback>
                    <div class="flex justify-center items-center min-h-screen">
                        <ProgressSpinner />
                    </div>
                </template>

                <Card>
                    <template #header>
                        <h1 class="text-4xl font-bold m-4">Edit Details</h1>
                    </template>
                    <template #content>
                        <form @submit.prevent="onSubmit" method="post">
                            <!-- Profile -->
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
                            <Button class="w-full my-4" type="submit" label="Update" />
                        </form>
                    </template>
                </Card>
            </Deferred>
        </div>
    </div>
</template>

<script setup>
import { watch } from 'vue'
import { useForm, Deferred } from '@inertiajs/vue3'

import Button from 'primevue/button'
import Card from 'primevue/card'
import Checkbox from 'primevue/checkbox'
import InputText from 'primevue/inputtext'
import ProgressSpinner from 'primevue/progressspinner'
import Select from 'primevue/select'
import Textarea from 'primevue/textarea'

import ProfileSidebar from '@/Components/ProfileSidebar.vue'

const props = defineProps({ data: Object })

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

const onSubmit = () => {
    form.post('/user/profile/update') // change later
}

watch(() => props.data, (data) => {
    if (data) {
        form.username = data.username ?? ''
        form.bio = data.bio ?? ''
        form.gender = data.gender ?? ''
        form.email = data.email ?? ''
        form.enable_auth = data.enable_auth ?? false
    }
}, { immediate: true })
</script>