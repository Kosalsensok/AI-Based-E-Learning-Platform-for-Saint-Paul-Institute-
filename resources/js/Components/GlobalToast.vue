<script setup lang="ts">
import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Toast from 'primevue/toast'
import { useAppToast } from '@/composables/useAppToast'

const page = usePage<any>()
const appToast = useAppToast()

watch(
  () => page.props.flash,
  (flash) => {
    if (!flash) return
    if (flash.success) {
      appToast.success(flash.success)
    }
    if (flash.error) {
      appToast.error(flash.error)
    }
    if (flash.info) {
      appToast.info(flash.info)
    }
    if (flash.warning || flash.warn) {
      appToast.warn(flash.warning || flash.warn)
    }
    if (flash.status) {
      appToast.info(flash.status)
    }
  },
  { deep: true, immediate: true }
)

watch(
  () => page.props.status,
  (status) => {
    if (status && typeof status === 'string') {
      appToast.info(status)
    }
  },
  { immediate: true }
)
</script>

<template>
  <Toast position="top-right" class="z-[9999]" />
</template>
