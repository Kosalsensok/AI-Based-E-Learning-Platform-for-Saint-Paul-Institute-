<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const props = defineProps<{
  course: any,
  enrolled: any
}>()

const formatCurrency = (value: number) => {
  if (value === 0 || value === null) return 'Free';
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
}

const handleEnroll = () => {
  if (props.course.price > 0) {
    // Redirect to payment upload page for paid courses
    router.get(route('student.payments.create', props.course.id))
  } else {
    // Directly enroll for free courses
    router.post(route('student.enroll', props.course.id))
  }
}
</script>

<template>
  <StudentLayout :title="course.title">
    <div class="max-w-5xl mx-auto p-6">
      
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden flex flex-col md:flex-row mb-8 border border-gray-100 dark:border-gray-700">
        <!-- Thumbnail -->
        <div class="md:w-1/3 bg-gray-200 dark:bg-gray-700 relative min-h-[250px]">
          <img v-if="course.thumbnail" :src="`/storage/${course.thumbnail}`" class="absolute inset-0 w-full h-full object-cover" />
          <div v-else class="absolute inset-0 flex items-center justify-center text-gray-400">No Image</div>
        </div>
        
        <!-- Details -->
        <div class="p-8 md:w-2/3 flex flex-col justify-center">
          <div class="text-sm font-bold text-primary mb-2 uppercase tracking-wide">
            {{ course.major?.name || 'General Course' }}
          </div>
          <h1 class="text-3xl font-bold mb-4">{{ course.title }}</h1>
          <p class="text-gray-600 dark:text-gray-400 mb-6 line-clamp-3">
            {{ course.description || 'No description provided.' }}
          </p>
          
          <div class="flex items-center justify-between mt-auto">
            <div class="flex items-center gap-3">
              <img :src="course.teacher?.profile_photo_url || `https://ui-avatars.com/api/?name=${course.teacher?.name}`" class="w-10 h-10 rounded-full" />
              <div>
                <div class="text-sm text-gray-500">Instructor</div>
                <div class="font-bold">{{ course.teacher?.name || 'Unknown' }}</div>
              </div>
            </div>
            
            <div class="text-right">
              <div class="text-2xl font-bold text-emerald-600 mb-2">{{ formatCurrency(course.price) }}</div>
              
              <template v-if="enrolled">
                <button v-if="enrolled.status === 'active'" @click="router.get(route('student.learn', course.id))" class="bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-primary/90 w-full">
                  Go to Course
                </button>
                <button v-else-if="enrolled.status === 'pending_payment'" disabled class="bg-yellow-500 text-white px-6 py-2 rounded-lg font-bold opacity-80 cursor-not-allowed w-full">
                  Verifying Payment...
                </button>
              </template>
              <template v-else>
                <button @click="handleEnroll" class="bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-primary/90 w-full">
                  Enroll Now
                </button>
              </template>
            </div>
          </div>
        </div>
      </div>

      <!-- Curriculum -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
        <h2 class="text-2xl font-bold mb-6">Course Curriculum</h2>
        
        <div v-if="!course.modules || course.modules.length === 0" class="text-gray-500 italic">
          Curriculum is currently empty.
        </div>
        
        <div v-else class="space-y-4">
          <div v-for="mod in course.modules" :key="mod.id" class="border dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="bg-gray-50 dark:bg-gray-900 p-4 font-bold border-b dark:border-gray-700 flex justify-between">
              <span>{{ mod.title }}</span>
              <span class="text-sm text-gray-500 font-normal">{{ mod.lessons?.length || 0 }} lessons</span>
            </div>
            <div class="p-0">
              <ul class="divide-y dark:divide-gray-700">
                <li v-for="lesson in mod.lessons" :key="lesson.id" class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                  <div class="flex items-center gap-3">
                    <i class="pi" :class="{
                      'pi-video text-blue-500': lesson.type === 'video',
                      'pi-file-pdf text-red-500': lesson.type === 'pdf',
                      'pi-file text-gray-500': lesson.type === 'reading'
                    }"></i>
                    <span>{{ lesson.title }}</span>
                  </div>
                  <span v-if="lesson.is_free_preview" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded font-bold">Preview</span>
                </li>
                <li v-if="!mod.lessons || mod.lessons.length === 0" class="p-4 text-gray-500 text-sm">No lessons yet.</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </StudentLayout>
</template>
