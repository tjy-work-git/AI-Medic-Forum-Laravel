<template>
  <div class="flex flex-col justify-center items-center min-h-screen">
    <Card class="container border max-w-100">
      <template #title>
        Login
      </template>
      <template #subtitle>
        Login in to WeDoCare to engage with other users.
      </template>
      <template #content>
        <form id="login-form" @submit.prevent="onSubmit" method="post">
          <div class="flex flex-col gap-2">
            <div class="flex flex-col">
              <label for="email">Email<span style="color: red;">*</span> </label>
              <InputText v-model="form.email" required />
            </div>

            <div class="flex flex-col">
              <label for="password">Password<span style="color: red;">*</span> </label>
              <InputText v-model="form.password" required />
            </div>

            <div class="py-2">
              <Checkbox v-model="form.remember" binary />
              <label for="remember"> Remember me when I visit this website </label>
            </div>


          </div>
        </form>
      </template>
      <template #footer>
        <Button class="w-full my-4" type="submit" form="login-form" label="Login" />
        <div class="flex justify-between">
          <Link href="/user/register">No Account? Register here</Link>
          <Link href="/user/forgot-password" class="float-right">Forgot password?</Link>
        </div>
      </template>
    </Card>
    <Link class="text-red py-4" @click="goBack()">Go Back</Link>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'

import Button from "primevue/button"
import Card from 'primevue/card'
import Checkbox from 'primevue/checkbox'
import InputText from 'primevue/inputtext'


const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const onSubmit = () => {
  form.post('/action/login')
}

const goBack = () => {
  window.history.back()
}
</script>
