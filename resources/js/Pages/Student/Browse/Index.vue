<script setup lang="ts">
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const props = defineProps<{
  courses: any,
  filters: any
}>()

const search = ref(props.filters.search || '')

// Search handler
const handleSearch = () => {
  router.get(route('student.browse'), { search: search.value }, { preserveState: true, preserveScroll: true })
}

const formatCurrency = (value: number) => {
  if (value === 0 || value === null) return 'Free';
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
}
</script>

<template>
  <StudentLayout title="Browse Courses">
    <div class="p-6">
      <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold">Browse Courses</h1>
          <p class="text-sm text-gray-500">Discover new skills and knowledge</p>
        </div>
        <div class="flex items-center gap-2">
          <input 
            v-model="search" 
            @keyup.enter="handleSearch"
            type="text" 
            placeholder="Search courses..." 
            class="px-4 py-2 border rounded-lg focus:ring-primary focus:border-primary border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800"
          />
          <button @click="handleSearch" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90">
            Search
          </button>
        </div>
      </div>

      <div v-if="courses.data.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="text-gray-500">No courses found matching your criteria.</div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div v-for="course in courses.data" :key="course.id" class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden hover:shadow-lg transition-shadow cursor-pointer border border-gray-100 dark:border-gray-700 flex flex-col" @click="router.get(route('student.browse.show', course.id))">
          
          <div class="h-48 bg-gray-200 dark:bg-gray-700 relative">
            <img v-if="course.thumbnail" :src="`/storage/${course.thumbnail}`" class="w-full h-full object-cover" />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
              No Image
            </div>
            <div class="absolute top-2 right-2 px-2 py-1 bg-white/90 dark:bg-black/90 backdrop-blur-sm rounded text-xs font-bold shadow">
              {{ formatCurrency(course.price) }}
            </div>
            <div v-if="course.learning_mode" class="absolute top-2 left-2 px-2 py-1 bg-blue-500 text-white rounded text-xs font-bold shadow uppercase">
              {{ course.learning_mode }}
            </div>
          </div>
          
          <div class="p-4 flex-1 flex flex-col">
            <div class="text-xs text-primary font-semibold mb-1" v-if="course.major">{{ course.major.name }}</div>
            <h3 class="font-bold text-lg mb-2 line-clamp-2 leading-tight flex-1">{{ course.title }}</h3>
            
            <div class="flex items-center gap-2 mt-4 text-sm text-gray-600 dark:text-gray-400">
              <img :src="course.teacher?.profile_photo_url || `https://ui-avatars.com/api/?name=${course.teacher?.name}`" class="w-6 h-6 rounded-full" />
              <span class="truncate">{{ course.teacher?.name || 'Unknown Instructor' }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="courses.links.length > 3" class="mt-8 flex justify-center">
        <div class="flex gap-1">
          <button v-for="link in courses.links" :key="link.label" 
            @click="link.url ? router.get(link.url) : null"
            :disabled="!link.url"
            class="px-3 py-1 rounded border text-sm"
            :class="{
              'bg-primary text-white border-primary': link.active,
              'hover:bg-gray-100 dark:hover:bg-gray-700 bg-white dark:bg-gray-800': !link.active && link.url,
              'opacity-50 cursor-not-allowed bg-gray-50 dark:bg-gray-900': !link.url
            }"
            v-html="link.label">
          </button>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>
