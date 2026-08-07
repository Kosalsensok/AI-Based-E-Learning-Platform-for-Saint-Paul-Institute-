<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import PreTest from './PreTest.vue'
import Practice from './Practice.vue'
import PostTest from './PostTest.vue'
import Assignments from './Assignments.vue'
import History from './History.vue'
import Scores from './Scores.vue'

const props = defineProps<{
  activeTab?: string
}>()

const currentTab = ref(props.activeTab || 'pretest')

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tabParam = urlParams.get('tab')
  if (tabParam && ['pretest', 'practice', 'posttest', 'assignments', 'history', 'scores'].includes(tabParam)) {
    currentTab.value = tabParam
  }
})

watch(() => props.activeTab, (newTab) => {
  if (newTab) currentTab.value = newTab
})
</script>

<template>
  <div>
    <PreTest v-if="currentTab === 'pretest'" />
    <Practice v-else-if="currentTab === 'practice'" />
    <PostTest v-else-if="currentTab === 'posttest'" />
    <Assignments v-else-if="currentTab === 'assignments'" />
    <History v-else-if="currentTab === 'history'" />
    <Scores v-else-if="currentTab === 'scores'" />
    <PreTest v-else />
  </div>
</template>
