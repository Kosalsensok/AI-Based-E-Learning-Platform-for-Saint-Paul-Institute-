<script setup lang="ts">
import MyCoursesHub from '@/Pages/Student/MyCourses/Index.vue'

defineProps<{
  enrollments?: any[]
  activeTab?: string
}>()
</script>

<template>
  <MyCoursesHub :enrollments="enrollments" :activeTab="activeTab" />
</template>
