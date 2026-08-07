<script setup lang="ts">
import ContentDeliveryModuleIndex from '../ContentDeliveryModule/Index.vue'

const props = withDefaults(defineProps<{
  tab?: string
  stats?: any
  contentItems?: Array<any>
  courseModules?: Array<any>
  offlinePackages?: Array<any>
  courses?: Array<any>
  teachers?: Array<any>
  majors?: Array<any>
}>(), {
  tab: 'library',
  stats: () => ({}),
  contentItems: () => [],
  courseModules: () => [],
  offlinePackages: () => [],
  courses: () => [],
  teachers: () => [],
  majors: () => [],
})
</script>

<template>
  <ContentDeliveryModuleIndex
    :tab="props.tab"
    :stats="props.stats"
    :contentItems="props.contentItems"
    :courseModules="props.courseModules"
    :offlinePackages="props.offlinePackages"
    :courses="props.courses"
    :teachers="props.teachers"
    :majors="props.majors"
  />
</template>
