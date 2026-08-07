<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import TeacherLayout from '@/Layouts/TeacherLayout.vue'

// Import the 4 Sub-module Components cleanly organized inside resources/js/Pages/Teacher/Content/
import VideosTab from './Videos.vue'
import PdfsTab from './Pdfs.vue'
import SlidesTab from './Slides.vue'
import ModulesChaptersTab from './ModulesChapters.vue'

const props = defineProps<{
  courses: Array<any>
  videos: Array<any>
  pdfs: Array<any>
  slides: Array<any>
  stats?: {
    videos_count: number
    pdfs_count: number
    slides_count: number
    chapters_count: number
    storage_used: string
    processing_count: number
  }
}>()

const activeTab = ref<'videos' | 'pdfs' | 'slides' | 'modules'>('videos')
const selectedCourseId = ref<number | null>(null)

const selectedCourse = computed(() => {
  if (!selectedCourseId.value) return null
  return props.courses?.find(c => c.id == selectedCourseId.value) || null
})

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tab = urlParams.get('tab')
  const courseIdParam = urlParams.get('course_id')

  if (tab === 'videos' || tab === 'pdfs' || tab === 'slides' || tab === 'modules') {
    activeTab.value = tab
  }
  if (courseIdParam) {
    selectedCourseId.value = Number(courseIdParam)
  } else if (props.courses && props.courses.length > 0) {
    selectedCourseId.value = props.courses[0].id
  }
})

const changeTab = (tab: 'videos' | 'pdfs' | 'slides' | 'modules') => {
  activeTab.value = tab
  const url = new URL(window.location.href)
  url.searchParams.set('tab', tab)
  if (selectedCourseId.value) {
    url.searchParams.set('course_id', String(selectedCourseId.value))
  }
  window.history.pushState({}, '', url.toString())
}
</script>

<template>
  <TeacherLayout title="Content Delivery Module">
    <div class="space-y-6 max-w-7xl mx-auto pb-12">
      <!-- MAIN MODULE HEADER -->
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
        <!-- Background Glow Accents -->
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-12 left-1/3 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl"></div>

        <div class="relative z-10 space-y-6">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-5">
            <div>
              <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight flex items-center gap-3">
                <span class="p-2.5 bg-blue-600/30 rounded-2xl border border-blue-400/30">📦</span>
                <span>Content Delivery Module</span>
              </h1>
              <p class="text-xs md:text-sm text-slate-300 mt-1">
                Manage learning materials, organize chapters, add subtitles, and publish lessons for students.
              </p>
            </div>

            <!-- Storage Indicator Badge -->
            <div class="bg-slate-800/80 backdrop-blur border border-slate-700 p-3.5 rounded-2xl text-xs space-y-1.5 min-w-[220px]">
              <div class="flex justify-between font-semibold">
                <span class="text-slate-300">Cloud Storage Used</span>
                <span class="text-blue-400 font-bold">{{ props.stats?.storage_used || '18.6GB / 100GB' }}</span>
              </div>
              <div class="w-full bg-slate-700 h-2 rounded-full overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-400 h-full w-[18.6%] rounded-full shadow"></div>
              </div>
              <p class="text-[10px] text-slate-400 text-right">Processing: {{ props.stats?.processing_count || 2 }} files</p>
            </div>
          </div>

          <!-- TOP STATS CARDS BAR -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4">
            <div
              @click="changeTab('videos')"
              :class="[
                'p-4 rounded-2xl border transition-all cursor-pointer flex items-center gap-3.5',
                activeTab === 'videos'
                  ? 'bg-blue-600/20 border-blue-500/60 ring-2 ring-blue-500/30 shadow-lg'
                  : 'bg-slate-800/50 border-slate-700/60 hover:bg-slate-800'
              ]"
            >
              <div class="w-10 h-10 rounded-xl bg-blue-500/20 border border-blue-500/40 text-blue-400 flex items-center justify-center text-xl font-bold">
                🎬
              </div>
              <div>
                <p class="text-[11px] text-slate-400 font-bold uppercase">Videos</p>
                <p class="text-lg font-extrabold text-white">{{ props.stats?.videos_count || 48 }}</p>
              </div>
            </div>

            <div
              @click="changeTab('pdfs')"
              :class="[
                'p-4 rounded-2xl border transition-all cursor-pointer flex items-center gap-3.5',
                activeTab === 'pdfs'
                  ? 'bg-rose-600/20 border-rose-500/60 ring-2 ring-rose-500/30 shadow-lg'
                  : 'bg-slate-800/50 border-slate-700/60 hover:bg-slate-800'
              ]"
            >
              <div class="w-10 h-10 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-xl font-bold">
                📄
              </div>
              <div>
                <p class="text-[11px] text-slate-400 font-bold uppercase">PDFs</p>
                <p class="text-lg font-extrabold text-white">{{ props.stats?.pdfs_count || 62 }}</p>
              </div>
            </div>

            <div
              @click="changeTab('slides')"
              :class="[
                'p-4 rounded-2xl border transition-all cursor-pointer flex items-center gap-3.5',
                activeTab === 'slides'
                  ? 'bg-amber-600/20 border-amber-500/60 ring-2 ring-amber-500/30 shadow-lg'
                  : 'bg-slate-800/50 border-slate-700/60 hover:bg-slate-800'
              ]"
            >
              <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center text-xl font-bold">
                📊
              </div>
              <div>
                <p class="text-[11px] text-slate-400 font-bold uppercase">Slides</p>
                <p class="text-lg font-extrabold text-white">{{ props.stats?.slides_count || 35 }}</p>
              </div>
            </div>

            <div
              @click="changeTab('modules')"
              :class="[
                'p-4 rounded-2xl border transition-all cursor-pointer flex items-center gap-3.5',
                activeTab === 'modules'
                  ? 'bg-emerald-600/20 border-emerald-500/60 ring-2 ring-emerald-500/30 shadow-lg'
                  : 'bg-slate-800/50 border-slate-700/60 hover:bg-slate-800'
              ]"
            >
              <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 flex items-center justify-center text-xl font-bold">
                📁
              </div>
              <div>
                <p class="text-[11px] text-slate-400 font-bold uppercase">Chapters</p>
                <p class="text-lg font-extrabold text-white">{{ props.stats?.chapters_count || 24 }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- COURSE SCOPE SELECTOR BANNER -->
      <div class="bg-slate-800/90 rounded-2xl p-5 border border-indigo-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xl">
        <div class="space-y-1">
          <div class="flex items-center gap-2 text-xs font-bold text-indigo-400">
            <span>Content Delivery Module</span>
            <span>/</span>
            <span class="capitalize">{{ activeTab }}</span>
          </div>
          <p class="text-sm font-extrabold text-white flex items-center gap-2">
            <span>Scope Content to Specific Course:</span>
          </p>
        </div>

        <div class="flex items-center gap-3">
          <select 
            v-model="selectedCourseId" 
            class="px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-semibold min-w-[240px]"
          >
            <option :value="null">-- Select a Course First --</option>
            <option v-for="c in props.courses" :key="c.id" :value="c.id">
              {{ c.title }} ({{ c.code }})
            </option>
          </select>

          <a 
            v-if="selectedCourseId"
            :href="`/teacher/courses/${selectedCourseId}/workspace?tab=curriculum`"
            class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition whitespace-nowrap"
          >
            Go to Course Builder →
          </a>
        </div>
      </div>

      <!-- MAIN CONTENT DELIVERY TABS -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-1.5 border border-slate-200/80 dark:border-gray-700 shadow-sm flex flex-wrap gap-1">
        <button
          @click="changeTab('videos')"
          :class="[
            'px-5 py-3 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2',
            activeTab === 'videos'
              ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/20'
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>🎬 Videos</span>
        </button>

        <button
          @click="changeTab('pdfs')"
          :class="[
            'px-5 py-3 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2',
            activeTab === 'pdfs'
              ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white shadow-md shadow-red-500/20'
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>📄 PDFs</span>
        </button>

        <button
          @click="changeTab('slides')"
          :class="[
            'px-5 py-3 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2',
            activeTab === 'slides'
              ? 'bg-gradient-to-r from-amber-500 to-indigo-600 text-white shadow-md shadow-amber-500/20'
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>📊 Slides</span>
        </button>

        <button
          @click="changeTab('modules')"
          :class="[
            'px-5 py-3 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2',
            activeTab === 'modules'
              ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-500/20'
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>📁 Modules & Chapters</span>
        </button>
      </div>

      <!-- SUB-MODULE CONTENT TAB PANELS -->
      <div>
        <VideosTab v-if="activeTab === 'videos'" :courses="props.courses" :videos="props.videos" />
        <PdfsTab v-if="activeTab === 'pdfs'" :courses="props.courses" :pdfs="props.pdfs" />
        <SlidesTab v-if="activeTab === 'slides'" :courses="props.courses" :slides="props.slides" />
        <ModulesChaptersTab v-if="activeTab === 'modules'" :courses="props.courses" />
      </div>
    </div>
  </TeacherLayout>
</template>
