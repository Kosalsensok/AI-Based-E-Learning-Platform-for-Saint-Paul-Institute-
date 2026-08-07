<script setup lang="ts">
import { computed } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const props = defineProps<{
  progress: any[]
}>()

// Group progress by course
const courseProgress = computed(() => {
  const groups: Record<number, any> = {}
  
  props.progress.forEach(p => {
    if (!p.lesson || !p.lesson.course_id) return
    
    const cid = p.lesson.course_id
    if (!groups[cid]) {
      groups[cid] = {
        course: p.lesson.course,
        lessonsCompleted: 0,
        totalTime: 0,
        progressList: []
      }
    }
    
    groups[cid].progressList.push(p)
    groups[cid].totalTime += p.seconds_watched || 0
    if (p.percent >= 90) {
      groups[cid].lessonsCompleted++
    }
  })
  
  return Object.values(groups)
})

const formatTime = (seconds: number) => {
  const h = Math.floor(seconds / 3600)
  const m = Math.floor((seconds % 3600) / 60)
  if (h > 0) return `${h}h ${m}m`
  return `${m}m`
}
</script>

<template>
  <StudentLayout title="My Progress">
    <div class="p-6">
      <div class="mb-6">
        <h1 class="text-2xl font-bold">Learning Progress</h1>
        <p class="text-sm text-gray-500">Track your completed lessons and study time</p>
      </div>

      <div v-if="courseProgress.length === 0" class="bg-white dark:bg-gray-800 rounded-xl shadow p-12 text-center text-gray-500">
        <i class="pi pi-chart-line text-6xl mb-4 text-gray-300"></i>
        <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">No Progress Yet</h3>
        <p>Start learning some courses to see your progress here.</p>
      </div>

      <div v-else class="grid grid-cols-1 gap-6">
        <div v-for="cp in courseProgress" :key="cp.course.id" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
          <div class="flex flex-col md:flex-row gap-6 items-center">
            
            <img v-if="cp.course.thumbnail" :src="`/storage/${cp.course.thumbnail}`" class="w-full md:w-48 h-32 object-cover rounded-lg shrink-0" />
            <div v-else class="w-full md:w-48 h-32 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center shrink-0">
              <i class="pi pi-image text-3xl text-gray-400"></i>
            </div>
            
            <div class="flex-1 w-full">
              <h2 class="text-xl font-bold mb-2">{{ cp.course.title }}</h2>
              
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-lg border dark:border-gray-700">
                  <div class="text-xs text-gray-500">Lessons Completed</div>
                  <div class="font-bold text-lg text-primary">{{ cp.lessonsCompleted }}</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-lg border dark:border-gray-700">
                  <div class="text-xs text-gray-500">Time Spent</div>
                  <div class="font-bold text-lg text-[#005E82]">{{ formatTime(cp.totalTime) }}</div>
                </div>
              </div>
              
              <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mt-2">
                 <!-- Just showing a dummy bar if we don't know total lessons -->
                <div class="bg-primary h-2.5 rounded-full" :style="{ width: Math.min((cp.lessonsCompleted / (cp.progressList.length || 1)) * 100, 100) + '%' }"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </StudentLayout>
</template>
