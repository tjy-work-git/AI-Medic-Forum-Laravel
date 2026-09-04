<template>
  <div class="flex flex-col justify-center items-center min-h-screen">
    <Card class="container border max-w-100">
      <template #title>
        Register Account
      </template>
      <template #subtitle>
        Create an account for WeDoCare here.
      </template>
      <template #content>
        <form id="register-form" @submit.prevent="onSubmit" method="post">
          <div class="flex flex-col gap-2">
            <div class="flex flex-col">
              <label for="uname">Username<span style="color: red;">*</span></label>
              <InputText v-model="form.username" required />
            </div>

            <div class="flex flex-col">
              <label for="email">Email<span style="color: red;">*</span></label>
              <InputText v-model="form.email" required />
            </div>

            <div class="flex flex-col">
              <label for="password">Password<span style="color: red;">*</span></label>
              <InputText v-model="form.password" required />
            </div>

            <div class="flex flex-col">
              <label for="c_password">Re-confirm Password<span style="color: red;">*</span></label>
              <InputText v-model="form.c_password" min="8" required />
            </div>

            <div class="flex flex-col">
              <label for="gender">Gender<span style="color: red;">*</span></label>
              <Select class="w-full border rounded" v-model="form.gender" required
                :options="[{ label: 'Male', value: 'Male' }, { label: 'Female', value: 'Female' }]" optionLabel="label"
                optionValue="value" />
            </div>
          </div>
        </form>
      </template>
      <template #footer>
        <Button class="w-full my-4" type="submit" form="register-form" label="Register" />
      </template>
    </Card>
    <Link class="text-red py-4" @click="goBack()">Go Back</Link>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import Button from "primevue/button"
import Card from 'primevue/card'
import InputText from 'primevue/inputtext'
import Select from 'primevue/select'

const form = useForm({
  username: '',
  email: '',
  password: '',
  c_password: '',
  gender: '',
})

const onSubmit = () => {
  form.post('/action/register')
}

const goBack = () => {
  window.history.back()
}
</script>
