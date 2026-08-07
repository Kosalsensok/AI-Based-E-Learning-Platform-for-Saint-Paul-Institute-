<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import Overview from './Overview.vue'
import LearningTime from './LearningTime.vue'
import WeeklyProgress from './WeeklyProgress.vue'
import AchievementsBadges from './AchievementsBadges.vue'

const props = defineProps<{
  activeTab?: string
}>()

const currentTab = ref(props.activeTab || 'overview')

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tabParam = urlParams.get('tab')
  if (tabParam && ['overview', 'time', 'learning-time', 'weekly', 'achievements'].includes(tabParam)) {
    currentTab.value = tabParam === 'learning-time' ? 'time' : tabParam
  }
})

watch(() => props.activeTab, (newTab) => {
  if (newTab) currentTab.value = newTab === 'learning-time' ? 'time' : newTab
})
</script>

<template>
  <div>
    <Overview v-if="currentTab === 'overview'" />
    <LearningTime v-else-if="currentTab === 'time' || currentTab === 'learning-time'" />
    <WeeklyProgress v-else-if="currentTab === 'weekly'" />
    <AchievementsBadges v-else-if="currentTab === 'achievements'" />
    <Overview v-else />
  </div>
</template>
