<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import Videos from './Videos.vue'
import Pdfs from './Pdfs.vue'
import Slides from './Slides.vue'
import NotesDownloads from './NotesDownloads.vue'
import UrlResources from './UrlResources.vue'

const props = defineProps<{
  activeTab?: string
}>()

const currentTab = ref(props.activeTab || 'videos')

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tabParam = urlParams.get('tab')
  if (tabParam && ['videos', 'pdfs', 'slides', 'notes', 'links'].includes(tabParam)) {
    currentTab.value = tabParam
  }
})

watch(() => props.activeTab, (newTab) => {
  if (newTab) currentTab.value = newTab
})
</script>

<template>
  <div>
    <Videos v-if="currentTab === 'videos'" />
    <Pdfs v-else-if="currentTab === 'pdfs'" />
    <Slides v-else-if="currentTab === 'slides'" />
    <NotesDownloads v-else-if="currentTab === 'notes'" />
    <UrlResources v-else-if="currentTab === 'links'" />
    <Videos v-else />
  </div>
</template>
