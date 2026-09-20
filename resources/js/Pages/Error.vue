<template>

    <Head>
        <title>{{ useStatusName( status ) }}</title>
    </Head>

    <div class="h-screen flex justify-center">
        <div class="flex items-center space-x-10">
            <Image v-bind:src="notFoundImg" alt="Image" width="200" />
            <div class="flex flex-col items-start space-y-2 text-primary-black">
                <h1 class="text-8xl text-bold">Oh no, not good!</h1>
                <h1 class="text-2xl" v-text="`${props.status} - ${useStatusName( status )}`" />
                <p class="text-md" v-text="props.message" />
                <Button class="w-full" severity="secondary" label="Go back..." @click="goBack()" />
            </div>
        </div>
    </div>
</template>

<script setup>
// Will change soon to fit what i want
import { Head } from '@inertiajs/vue3'

import Image from 'primevue/image'
import Button from 'primevue/button'

import { useStatusName} from '@/Composables/status.js';
import notFoundImg from '/resources/images/static/not_found_funne.png';

const props = defineProps( { status : Number, message : String } );

const goBack = () => {
  window.history.back()
}

</script>