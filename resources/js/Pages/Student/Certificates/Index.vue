<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import MyCertificates from './MyCertificates.vue'
import DownloadShare from './DownloadShare.vue'
import VerifyCertificate from './VerifyCertificate.vue'

const props = defineProps<{
  activeTab?: string
}>()

const currentTab = ref(props.activeTab || 'my-certificates')

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tabParam = urlParams.get('tab')
  if (tabParam && ['my-certificates', 'my-certs', 'download', 'download-share', 'verify'].includes(tabParam)) {
    if (tabParam === 'my-certs') currentTab.value = 'my-certificates'
    else if (tabParam === 'download') currentTab.value = 'download-share'
    else currentTab.value = tabParam
  }
})

watch(() => props.activeTab, (newTab) => {
  if (newTab) {
    if (newTab === 'my-certs') currentTab.value = 'my-certificates'
    else if (newTab === 'download') currentTab.value = 'download-share'
    else currentTab.value = newTab
  }
})
</script>

<template>
  <div>
    <MyCertificates v-if="currentTab === 'my-certificates' || currentTab === 'my-certs'" />
    <DownloadShare v-else-if="currentTab === 'download-share' || currentTab === 'download'" />
    <VerifyCertificate v-else-if="currentTab === 'verify'" />
    <MyCertificates v-else />
  </div>
</template>
