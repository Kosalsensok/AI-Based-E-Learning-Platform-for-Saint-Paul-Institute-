<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import EnrolledCourses from './EnrolledCourses.vue'
import CurrentCourse from './CurrentCourse.vue'
import CompletedCourses from './CompletedCourses.vue'
import BrowseCatalog from './BrowseCatalog.vue'
import Wishlist from './Wishlist.vue'

const props = defineProps<{
  activeTab?: string
  enrollments?: any[]
}>()

const currentTab = ref(props.activeTab || 'enrolled')

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tabParam = urlParams.get('tab')
  if (tabParam && ['enrolled', 'current', 'completed', 'browse', 'wishlist'].includes(tabParam)) {
    currentTab.value = tabParam
  }
})

watch(() => props.activeTab, (newTab) => {
  if (newTab) currentTab.value = newTab
})
</script>

<template>
  <div>
    <!-- Render Sub-Component based on current Tab -->
    <EnrolledCourses v-if="currentTab === 'enrolled'" :enrollments="enrollments" />
    <CurrentCourse v-else-if="currentTab === 'current'" />
    <CompletedCourses v-else-if="currentTab === 'completed'" />
    <BrowseCatalog v-else-if="currentTab === 'browse'" />
    <Wishlist v-else-if="currentTab === 'wishlist'" />
    <EnrolledCourses v-else :enrollments="enrollments" />
  </div>
</template>
