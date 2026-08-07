<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const props = defineProps<{
  course: any,
  progress: any,
  enrollment: any
}>()

const activeLesson = ref<any>(null)

// Flatten lessons to find first or next
const allLessons = computed(() => {
  let lessons: any[] = []
  if (props.course.modules) {
    props.course.modules.forEach((mod: any) => {
      if (mod.lessons) {
        lessons = [...lessons, ...mod.lessons]
      }
    })
  }
  return lessons
})

// Init first lesson
if (allLessons.value.length > 0) {
  // Find first uncompleted or just first
  const firstUncompleted = allLessons.value.find((l: any) => !props.progress[l.id] || props.progress[l.id].percent < 100)
  activeLesson.value = firstUncompleted || allLessons.value[0]
}

const selectLesson = (lesson: any) => {
  activeLesson.value = lesson
}

const isCompleted = (lessonId: number) => {
  return props.progress[lessonId] && props.progress[lessonId].percent >= 90
}

const markAsCompleted = () => {
  if (!activeLesson.value) return
  
  fetch(route('student.learn.progress', activeLesson.value.id), {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': (document.head.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
    },
    body: JSON.stringify({ percent: 100, seconds: 0 })
  }).then(res => res.json()).then(data => {
    if (data.ok) {
      if (!props.progress[activeLesson.value.id]) {
        props.progress[activeLesson.value.id] = {}
      }
      props.progress[activeLesson.value.id].percent = 100
      
      // Auto advance to next lesson
      const currentIndex = allLessons.value.findIndex(l => l.id === activeLesson.value.id)
      if (currentIndex !== -1 && currentIndex < allLessons.value.length - 1) {
        activeLesson.value = allLessons.value[currentIndex + 1]
      }
    }
  })
}
</script>

<template>
  <StudentLayout :title="course.title">
    <div class="flex flex-col md:flex-row h-[calc(100vh-64px)] overflow-hidden -m-6">
      
      <!-- Sidebar (Curriculum) -->
      <div class="w-full md:w-80 bg-white dark:bg-gray-800 border-r dark:border-gray-700 flex flex-col h-full shrink-0">
        <div class="p-4 border-b dark:border-gray-700 shrink-0">
          <button @click="router.get(route('student.courses'))" class="text-sm text-gray-500 hover:text-primary mb-2 flex items-center gap-1">
            <i class="pi pi-arrow-left text-xs"></i> Back to Courses
          </button>
          <h2 class="font-bold line-clamp-2 leading-tight">{{ course.title }}</h2>
        </div>
        
        <div class="flex-1 overflow-y-auto">
          <div v-for="(mod, index) in course.modules" :key="mod.id" class="border-b dark:border-gray-700">
            <div class="bg-gray-50 dark:bg-gray-900 p-3 text-sm font-bold sticky top-0 z-10">
              Module {{ Number(index) + 1 }}: {{ mod.title }}
            </div>
            
            <ul class="divide-y divide-gray-100 dark:divide-gray-800">
              <li v-for="lesson in mod.lessons" :key="lesson.id" 
                @click="selectLesson(lesson)"
                class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors flex items-start gap-3"
                :class="{
                  'bg-primary/5 dark:bg-primary/10 border-l-4 border-primary': activeLesson?.id === lesson.id,
                  'border-l-4 border-transparent': activeLesson?.id !== lesson.id
                }">
                
                <div class="mt-0.5 shrink-0">
                  <i v-if="isCompleted(lesson.id)" class="pi pi-check-circle text-green-500"></i>
                  <i v-else-if="lesson.type === 'video'" class="pi pi-video text-gray-400"></i>
                  <i v-else-if="lesson.type === 'pdf'" class="pi pi-file-pdf text-gray-400"></i>
                  <i v-else class="pi pi-file text-gray-400"></i>
                </div>
                
                <div class="flex-1 min-w-0">
                  <div class="text-sm font-medium leading-tight" :class="{'text-primary': activeLesson?.id === lesson.id}">{{ lesson.title }}</div>
                  <div class="text-xs text-gray-500 mt-1 uppercase">{{ lesson.type }}</div>
                </div>
              </li>
              <li v-if="!mod.lessons || mod.lessons.length === 0" class="p-3 text-xs text-gray-500 text-center">
                Empty module
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="flex-1 flex flex-col bg-gray-50 dark:bg-gray-900 overflow-y-auto h-full">
        
        <div v-if="activeLesson" class="max-w-4xl mx-auto w-full p-4 md:p-8 flex flex-col min-h-full">
          
          <h1 class="text-2xl font-bold mb-6">{{ activeLesson.title }}</h1>
          
          <!-- Content Display based on type -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden mb-6 flex-1 flex flex-col">
            
            <template v-if="activeLesson.type === 'video'">
              <div v-if="activeLesson.video_url" class="aspect-video bg-black flex items-center justify-center">
                <iframe 
                  class="w-full h-full"
                  :src="activeLesson.video_url" 
                  frameborder="0" 
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                  allowfullscreen>
                </iframe>
              </div>
              <div v-else class="aspect-video bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500">
                No video URL provided.
              </div>
            </template>
            
            <template v-else-if="activeLesson.type === 'pdf'">
              <div v-if="activeLesson.file_path" class="flex-1 flex flex-col items-center justify-center p-12 bg-gray-100 dark:bg-gray-800 border-b">
                <i class="pi pi-file-pdf text-6xl text-red-500 mb-4"></i>
                <h3 class="font-bold text-lg mb-4">PDF Document</h3>
                <a :href="`/storage/${activeLesson.file_path}`" target="_blank" class="bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-primary/90 flex items-center gap-2">
                  <i class="pi pi-download"></i> Download PDF
                </a>
              </div>
              <div v-else class="p-8 text-center text-gray-500">No PDF file attached.</div>
            </template>

            <!-- Text Content -->
            <div v-if="activeLesson.content" class="p-6 md:p-8 prose dark:prose-invert max-w-none">
              <div v-html="activeLesson.content"></div>
            </div>
            
          </div>
          
          <!-- Completion Action -->
          <div class="flex justify-end mt-auto shrink-0 mb-8">
            <button 
              @click="markAsCompleted" 
              class="px-8 py-3 rounded-lg font-bold transition-colors flex items-center gap-2"
              :class="isCompleted(activeLesson.id) ? 'bg-green-100 text-green-700' : 'bg-primary text-white hover:bg-primary/90'"
            >
              <i class="pi" :class="isCompleted(activeLesson.id) ? 'pi-check' : 'pi-circle'"></i>
              {{ isCompleted(activeLesson.id) ? 'Completed' : 'Mark as Complete' }}
            </button>
          </div>
          
        </div>
        
        <div v-else class="flex-1 flex flex-col items-center justify-center text-gray-500 p-8 text-center">
          <i class="pi pi-book text-6xl mb-4 text-gray-300"></i>
          <h2 class="text-xl font-bold">Ready to learn?</h2>
          <p>Select a lesson from the curriculum sidebar to start.</p>
        </div>
        
      </div>
      
    </div>
  </StudentLayout>
</template>
