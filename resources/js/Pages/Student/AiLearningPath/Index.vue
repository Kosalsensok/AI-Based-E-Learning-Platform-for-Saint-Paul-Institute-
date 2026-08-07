<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import RecommendedLesson from './RecommendedLesson.vue'
import ReviewLesson from './ReviewLesson.vue'
import WeakTopics from './WeakTopics.vue'
import NextModule from './NextModule.vue'

const props = defineProps<{
  activeTab?: string
}>()

const currentTab = ref(props.activeTab || 'recommended')

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tabParam = urlParams.get('tab')
  if (tabParam && ['recommended', 'review', 'weak-topics', 'next-module'].includes(tabParam)) {
    currentTab.value = tabParam
  }
})

watch(() => props.activeTab, (newTab) => {
  if (newTab) currentTab.value = newTab
})
</script>

<template>
  <div>
    <RecommendedLesson v-if="currentTab === 'recommended'" />
    <ReviewLesson v-else-if="currentTab === 'review'" />
    <WeakTopics v-else-if="currentTab === 'weak-topics'" />
    <NextModule v-else-if="currentTab === 'next-module'" />
    <RecommendedLesson v-else />
  </div>
</template>
