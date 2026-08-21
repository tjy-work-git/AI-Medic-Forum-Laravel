<template>
    <Toast />
    <HeaderLayout v-if="showHeader" />
    <slot />
    <FooterLayout/>
</template>

<script setup>
import { watch, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useToast } from 'primevue/usetoast'
import Toast from 'primevue/toast'
import HeaderLayout from '@/Layouts/HeaderLayout.vue'
import FooterLayout from '@/Layouts/FooterLayout.vue'

const page = usePage();
const toast = useToast()
const showHeader = computed(() => {
    return page.props.showHeader !== false
})

watch(
    () => page.props.errors,
    (errors) => {
        if (errors && Object.keys(errors).length > 0) {
            Object.values(errors).forEach(msg => {
                toast.add({ severity: 'error', summary: 'Validation Error', detail: msg, life: 3000 })
            })
        }
        // errors.value = null
    },
    { immediate: true }
)

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            toast.add({ severity: 'success', summary: 'Success', detail: flash.success, life: 3000 })
        }
        if (flash?.error) {
            toast.add({ severity: 'error', summary: 'Error', detail: flash.error, life: 3000 })
        }
        // flash.value = null
    },
    { immediate: true }
)
</script>