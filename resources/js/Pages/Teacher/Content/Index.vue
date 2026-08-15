<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import TeacherLayout from '@/Layouts/TeacherLayout.vue'

// Import all 7 Sub-module Components
import VideosTab from './Videos.vue'
import PdfsTab from './Pdfs.vue'
import SlidesTab from './Slides.vue'
import ModulesChaptersTab from './ModulesChapters.vue'
import NotesTab from './Notes.vue'
import AiContentTab from './AiContent.vue'
import PracticeLabsTab from './PracticeLabs.vue'

const props = defineProps<{
  courses: Array<any>
  courseVideos?: Array<any>
  courseMaterials?: Array<any>
  aiContents?: Array<any>
  labIntegrations?: Array<any>
  videos?: Array<any>
  pdfs?: Array<any>
  slides?: Array<any>
  notes?: Array<any>
  labs?: Array<any>
  stats?: {
    videos_count: number
    pdfs_count: number
    slides_count: number
    notes_count: number
    labs_count: number
    ai_count?: number
    modules_count: number
    chapters_count: number
    storage_used: string
    processing_count: number
  }
  selectedCourseId?: number | null
}>()

type TabKey = 'videos' | 'pdfs' | 'slides' | 'modules' | 'notes' | 'ai-content' | 'coding-lab'

const activeTab = ref<TabKey>('videos')
const selectedCourseId = ref<number | null>(props.selectedCourseId || props.courses?.[0]?.id || null)

const selectedCourse = computed(() => {
  if (!selectedCourseId.value) return null
  return props.courses?.find(c => c.id == selectedCourseId.value) || null
})

// Tab navigation definitions matching the exact specification
const tabs = [
  { key: 'videos' as TabKey, label: 'Videos', icon: '🎥', iconClass: 'ti-video', countKey: 'videos_count', accent: 'blue' },
  { key: 'pdfs' as TabKey, label: 'PDFs', icon: '📄', iconClass: 'ti-file-type-pdf', countKey: 'pdfs_count', accent: 'rose' },
  { key: 'slides' as TabKey, label: 'Slides', icon: '📊', iconClass: 'ti-presentation', countKey: 'slides_count', accent: 'amber' },
  { key: 'modules' as TabKey, label: 'Modules & Chapters', icon: '📑', iconClass: 'ti-stack-2', countKey: 'chapters_count', accent: 'emerald' },
  { key: 'notes' as TabKey, label: 'Notes & Downloads', icon: '📙', iconClass: 'ti-file-download', countKey: 'notes_count', accent: 'indigo' },
  { key: 'ai-content' as TabKey, label: 'AI-Assisted Content', icon: '✨', tag: '🤖', iconClass: 'ti-sparkles', countKey: 'ai_count', accent: 'purple' },
  { key: 'coding-lab' as TabKey, label: 'Practice Lab', icon: '💻', tag: '💻', iconClass: 'ti-device-laptop', countKey: 'labs_count', accent: 'sky' },
]

const resolveTab = (raw: string | null): TabKey => {
  if (!raw) return 'videos'
  const clean = decodeURIComponent(raw).toLowerCase().trim().replace(/[\s_]+/g, '-')
  if (clean.includes('ai') || clean === 'ai-content' || clean === 'ai-assisted-content') return 'ai-content'
  if (clean.includes('lab') || clean === 'coding-lab' || clean === 'practice-lab') return 'coding-lab'
  if (clean.includes('video')) return 'videos'
  if (clean.includes('pdf')) return 'pdfs'
  if (clean.includes('slide')) return 'slides'
  if (clean.includes('module') || clean.includes('chapter')) return 'modules'
  if (clean.includes('note') || clean.includes('download')) return 'notes'
  return 'videos'
}

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tabParam = urlParams.get('tab')
  const courseIdParam = urlParams.get('course_id')
  const pathname = window.location.pathname

  if (tabParam) {
    activeTab.value = resolveTab(tabParam)
  } else {
    // Check if tab is in pathname e.g. /teacher/content/ai-content
    for (const t of tabs) {
      if (pathname.includes(t.key)) {
        activeTab.value = t.key
        break
      }
    }
  }

  if (courseIdParam) {
    selectedCourseId.value = Number(courseIdParam)
  }
})

const changeTab = (tabKey: TabKey) => {
  activeTab.value = tabKey
  const url = new URL(window.location.href)
  url.searchParams.set('tab', tabKey)
  if (selectedCourseId.value) {
    url.searchParams.set('course_id', String(selectedCourseId.value))
  }
  window.history.pushState({}, '', url.toString())
}

const onCourseChange = () => {
  const url = new URL(window.location.href)
  if (selectedCourseId.value) {
    url.searchParams.set('course_id', String(selectedCourseId.value))
  }
  window.history.pushState({}, '', url.toString())
}
</script>

<template>
  <TeacherLayout title="Content Delivery — Teacher Panel">
    <div class="space-y-6 max-w-7xl mx-auto pb-16">
      
      <!-- MAIN MODULE HERO BANNER -->
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden border border-indigo-900/50">
        <!-- Background Glow Accents -->
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-12 left-1/3 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 space-y-6">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-5">
            <div>
              <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold mb-2">
                <span>Saint Paul Institute</span>
                <span>•</span>
                <span>E-Learning Management System</span>
              </div>
              <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight flex items-center gap-3">
                <span class="p-2.5 bg-blue-600/30 rounded-2xl border border-blue-400/30">📁</span>
                <span>Content Delivery — Teacher Panel</span>
              </h1>
              <p class="text-xs md:text-sm text-slate-300 mt-1">
                Manage curriculum media, Cloudinary videos, PDFs, slides, modules hierarchy, AI content generation, and virtual practice labs.
              </p>
            </div>

            <!-- Storage Indicator Badge -->
            <div class="bg-slate-800/80 backdrop-blur border border-slate-700 p-3.5 rounded-2xl text-xs space-y-1.5 min-w-[220px]">
              <div class="flex justify-between font-semibold">
                <span class="text-slate-300">Cloud Storage</span>
                <span class="text-blue-400 font-bold">{{ props.stats?.storage_used || '18.6GB / 100GB' }}</span>
              </div>
              <div class="w-full bg-slate-700 h-2 rounded-full overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-400 h-full w-[18.6%] rounded-full shadow"></div>
              </div>
              <div class="flex items-center justify-between text-[10px] text-slate-400">
                <span>Cloudinary & S3 Ready</span>
                <span>Processing: {{ props.stats?.processing_count || 0 }} files</span>
              </div>
            </div>
          </div>

          <!-- TOP STATS CARDS BAR -->
          <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
            <div
              v-for="t in tabs"
              :key="t.key"
              @click="changeTab(t.key)"
              :class="[
                'p-3.5 rounded-2xl border transition-all cursor-pointer flex flex-col justify-between space-y-2',
                activeTab === t.key
                  ? 'bg-slate-800 border-indigo-500 ring-2 ring-indigo-500/30 shadow-lg scale-[1.02]'
                  : 'bg-slate-800/50 border-slate-700/60 hover:bg-slate-800'
              ]"
            >
              <div class="flex items-center justify-between">
                <span class="text-xl">{{ t.icon }}</span>
                <span v-if="t.tag" class="text-xs">{{ t.tag }}</span>
              </div>
              <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase truncate">{{ t.label }}</p>
                <p class="text-base font-extrabold text-white">
                  {{ (props.stats as any)?.[t.countKey] ?? (t.key === 'videos' ? 18 : t.key === 'pdfs' ? 24 : t.key === 'slides' ? 15 : t.key === 'modules' ? 24 : t.key === 'notes' ? 12 : t.key === 'ai-content' ? 6 : 8) }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- COURSE SCOPE SELECTOR BANNER -->
      <div class="bg-slate-800/90 rounded-2xl p-4 md:p-5 border border-indigo-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xl">
        <div class="space-y-1">
          <div class="flex items-center gap-2 text-xs font-bold text-indigo-400">
            <span>Content Delivery</span>
            <span>/</span>
            <span class="capitalize">{{ activeTab }}</span>
          </div>
          <p class="text-sm font-extrabold text-white flex items-center gap-2">
            <span>Filter by Target Course:</span>
          </p>
        </div>

        <div class="flex items-center gap-3">
          <select 
            v-model="selectedCourseId" 
            @change="onCourseChange"
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
            Course Builder →
          </a>
        </div>
      </div>

      <!-- HORIZONTAL SCROLLABLE TAB BAR (7 PILLS) -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-2 border border-slate-200 dark:border-gray-700 shadow-sm overflow-x-auto scrollbar-thin">
        <div class="flex items-center gap-2 min-w-max">
          <button
            v-for="t in tabs"
            :key="t.key"
            @click="changeTab(t.key)"
            :class="[
              'px-4 py-2.5 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2 cursor-pointer border',
              activeTab === t.key
                ? (
                    t.accent === 'blue' ? 'bg-blue-600/10 border-blue-500 text-blue-600 dark:text-blue-400 font-extrabold shadow-sm' :
                    t.accent === 'rose' ? 'bg-rose-600/10 border-rose-500 text-rose-600 dark:text-rose-400 font-extrabold shadow-sm' :
                    t.accent === 'amber' ? 'bg-amber-600/10 border-amber-500 text-amber-600 dark:text-amber-400 font-extrabold shadow-sm' :
                    t.accent === 'emerald' ? 'bg-emerald-600/10 border-emerald-500 text-emerald-600 dark:text-emerald-400 font-extrabold shadow-sm' :
                    t.accent === 'purple' ? 'bg-purple-600/10 border-purple-500 text-purple-600 dark:text-purple-400 font-extrabold shadow-sm' :
                    t.accent === 'sky' ? 'bg-sky-600/10 border-sky-500 text-sky-600 dark:text-sky-400 font-extrabold shadow-sm' :
                    'bg-indigo-600/10 border-indigo-500 text-indigo-600 dark:text-indigo-400 font-extrabold shadow-sm'
                  )
                : 'border-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700/60 font-medium'
            ]"
          >
            <span>{{ t.icon }}</span>
            <span>{{ t.label }}</span>
            <span v-if="t.tag" class="text-xs">{{ t.tag }}</span>
          </button>
        </div>
      </div>

      <!-- CONTENT PANELS (Switches dynamically based on active tab) -->
      <div class="transition-all duration-200">
        <!-- 1. VIDEOS -->
        <VideosTab 
          v-if="activeTab === 'videos'"
          :courses="props.courses"
          :course-videos="props.courseVideos"
          :videos="props.videos"
          :selected-course-id="selectedCourseId"
        />

        <!-- 2. PDFS -->
        <PdfsTab
          v-else-if="activeTab === 'pdfs'"
          :courses="props.courses"
          :course-materials="props.courseMaterials"
          :pdfs="props.pdfs"
          :selected-course-id="selectedCourseId"
        />

        <!-- 3. SLIDES -->
        <SlidesTab
          v-else-if="activeTab === 'slides'"
          :courses="props.courses"
          :course-materials="props.courseMaterials"
          :slides="props.slides"
          :selected-course-id="selectedCourseId"
        />

        <!-- 4. MODULES & CHAPTERS -->
        <ModulesChaptersTab
          v-else-if="activeTab === 'modules'"
          :courses="props.courses"
          :selected-course-id="selectedCourseId"
        />

        <!-- 5. NOTES & DOWNLOADS -->
        <NotesTab
          v-else-if="activeTab === 'notes'"
          :courses="props.courses"
          :course-materials="props.courseMaterials"
          :notes="props.notes"
          :selected-course-id="selectedCourseId"
        />

        <!-- 6. AI-ASSISTED CONTENT 🤖 -->
        <AiContentTab
          v-else-if="activeTab === 'ai-content'"
          :courses="props.courses"
          :ai-contents="props.aiContents"
          :selected-course-id="selectedCourseId"
        />

        <!-- 7. PRACTICE LAB 💻 -->
        <PracticeLabsTab
          v-else-if="activeTab === 'coding-lab'"
          :courses="props.courses"
          :lab-integrations="props.labIntegrations"
          :labs="props.labs"
          :selected-course-id="selectedCourseId"
        />
      </div>

    </div>
  </TeacherLayout>
</template>
