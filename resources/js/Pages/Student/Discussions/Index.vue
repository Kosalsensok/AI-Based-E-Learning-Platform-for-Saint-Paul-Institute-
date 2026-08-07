<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import AskQuestion from './AskQuestion.vue'
import MyQuestions from './MyQuestions.vue'
import CourseDiscussions from './CourseDiscussions.vue'
import CourseFaq from './CourseFaq.vue'
import SupportTicket from './SupportTicket.vue'

const props = defineProps<{
  activeTab?: string
}>()

const currentTab = ref(props.activeTab || 'ask')

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tabParam = urlParams.get('tab')
  if (tabParam && ['ask', 'my-questions', 'course-discussions', 'discussions', 'faq', 'tickets'].includes(tabParam)) {
    if (tabParam === 'discussions') currentTab.value = 'course-discussions'
    else currentTab.value = tabParam
  }
})

watch(() => props.activeTab, (newTab) => {
  if (newTab) {
    if (newTab === 'discussions') currentTab.value = 'course-discussions'
    else currentTab.value = newTab
  }
})
</script>

<template>
  <div>
    <AskQuestion v-if="currentTab === 'ask'" />
    <MyQuestions v-else-if="currentTab === 'my-questions'" />
    <CourseDiscussions v-else-if="currentTab === 'course-discussions' || currentTab === 'discussions'" />
    <CourseFaq v-else-if="currentTab === 'faq'" />
    <SupportTicket v-else-if="currentTab === 'tickets'" />
    <AskQuestion v-else />
  </div>
</template>
