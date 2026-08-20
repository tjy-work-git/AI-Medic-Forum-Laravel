<template>
    <div class="container" style="width: 25%; padding-top: 100px;">
            <h1>Login</h1>
            <div class="container border">
                <form  @submit.prevent="onSubmit" method="post">
                    <label for="email">Email: </label><br>
                    <input v-model="form.email" type="text" maxlength="50" id="email" name="email" required/><br>

                    <label for="password">Password: </label><br>
                    <input v-model="form.password" type="password" maxlength="20" id="password" name="password" required/><br>

                    <input v-model="form.remember" type="checkbox" id="remember" name="remember" value="remember">
                    <label for="remember"> Remember me for a month</label><br>
                </form>
                <br>
                <a href="/register">No Account? Register here</a>
                <a href="/forgot_password" class="float-right">Forgot password?</a>
            </div>
            <a href="/" style="color: red;">Back</a>
        </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const onSubmit = () => {
  form.post('/action/login')
}
</script>
