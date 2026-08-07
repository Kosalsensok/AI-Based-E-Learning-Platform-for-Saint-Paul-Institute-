<script setup lang="ts">
import TeacherLayout from '@/Layouts/TeacherLayout.vue'
import { ref, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import ModuleCompletion from './ModuleCompletion.vue'
import LearningTime from './LearningTime.vue'
import WeeklyReport from './WeeklyReport.vue'
import ModuleDetailModal from './ModuleDetailModal.vue'
import StudentTimeModal from './StudentTimeModal.vue'
import ExportReportModal from './ExportReportModal.vue'

const props = defineProps<{
  courses: Array<any>
  topStats?: {
    avg_completion: number
    avg_time: string
    active_students: number
    at_risk_students: number
    this_week_engagement: number
    trend: string
  }
  moduleCompletionOverview?: any
  modulesData?: Array<any>
  heatmapData?: Array<any>
  learningTimeOverview?: any
  studentTimeList?: Array<any>
  weeklyChartDays?: Array<any>
  timeDistribution?: Array<any>
  weeklyReport?: any
}>()

// Active Tab state
const activeTab = ref<'modules' | 'time' | 'weekly'>('modules')

// Language state
const lang = ref<'km' | 'en'>('km')
const toggleLang = () => {
  lang.value = lang.value === 'km' ? 'en' : 'km'
}

// Controls State
const selectedCourse = ref(props.courses?.[0]?.id || 1)
const selectedPeriod = ref('This Month')

// Modal States
const selectedModule = ref<any>(null)
const showModuleModal = ref(false)

const selectedStudent = ref<any>(null)
const showStudentModal = ref(false)

const showExportModal = ref(false)

// Toast notification state
const toastMessage = ref('')
const showToast = ref(false)

const triggerToast = (msg: string) => {
  toastMessage.value = msg
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 4000)
}

// Handle URL query string tab param if present
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tabParam = urlParams.get('tab')
  if (tabParam === 'time') activeTab.value = 'time'
  else if (tabParam === 'weekly') activeTab.value = 'weekly'
  else if (tabParam === 'modules') activeTab.value = 'modules'
})

// Tab Switch handler
const setTab = (tab: 'modules' | 'time' | 'weekly') => {
  activeTab.value = tab
  const url = new URL(window.location.href)
  url.searchParams.set('tab', tab)
  window.history.pushState({}, '', url.toString())
}

// Event Handlers
const openModuleDetail = (m: any) => {
  selectedModule.value = m
  showModuleModal.value = true
}

const openStudentDetail = (s: any) => {
  selectedStudent.value = s
  showStudentModal.value = true
}

const handleModuleAction = (actionType: string, mod: any) => {
  showModuleModal.value = false
  if (actionType === 'add_video') {
    triggerToast(`Navigating to upload video for ${mod?.name || 'module'}...`)
    router.get('/teacher/content?tab=videos')
  } else if (actionType === 'create_quiz') {
    triggerToast(`Creating new practice quiz for ${mod?.name || 'module'}...`)
    router.get('/teacher/quizzes?tab=practice')
  } else if (actionType === 'announcement') {
    triggerToast(`Opening announcements to alert students about ${mod?.name || 'module'}...`)
    router.get('/teacher/discussions?tab=announcements')
  }
}

const handleSendReminder = (student: any) => {
  triggerToast(`Notification reminder sent to ${student?.student || student?.student_name || 'student'}!`)
}

const handleRecommendContent = (student: any) => {
  showStudentModal.value = false
  triggerToast(`Recommended review content sent to ${student?.student}!`)
}

const handleExportReport = (config: any) => {
  showExportModal.value = false
  triggerToast(`Weekly Report exported successfully in ${config.format.toUpperCase()} (${config.language === 'km' ? 'Khmer' : 'English'})! Download starting...`)
}

const handleTeacherAction = (actionText: string) => {
  if (actionText.includes('video')) {
    router.get('/teacher/content?tab=videos')
  } else if (actionText.includes('quiz')) {
    router.get('/teacher/quizzes?tab=practice')
  } else if (actionText.includes('reminder')) {
    triggerToast('Bulk reminder notification sent to inactive students!')
  } else if (actionText.includes('review')) {
    router.get('/teacher/calendar?tab=schedule')
  } else {
    triggerToast(`Action initiated: ${actionText}`)
  }
}
</script>

<template>
  <TeacherLayout title="Progress Tracking Module">
    <div class="space-y-6">
      <!-- Toast Alert Banner -->
      <div
        v-if="showToast"
        class="fixed bottom-5 right-5 z-50 bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-3 animate-bounce"
      >
        <span class="text-xl">✅</span>
        <span class="text-xs font-extrabold">{{ toastMessage }}</span>
      </div>

      <!-- Top Header & Language Toggle -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2 tracking-tight">
              <span>📈 Progress Tracking Module</span>
            </h1>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
              Teacher Panel
            </span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            {{ lang === 'km' ? 'តាមដានការរៀនរបស់និស្សិតជាលក្ខណៈសរុប និងជាលម្អិត (Module Completion, Learning Time, Weekly Report)' : 'Monitor student performance, course completion, learning hours, and weekly class reports.' }}
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button
            @click="toggleLang"
            class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-xs font-extrabold text-slate-700 dark:text-white transition flex items-center gap-1.5"
          >
            <span>{{ lang === 'km' ? '🇰🇭 ភាសាខ្មែរ' : '🇬🇧 English' }}</span>
          </button>
        </div>
      </div>

      <!-- Top Stats Bar -->
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-5 rounded-3xl shadow-lg border border-slate-800">
        <div class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider mb-2">Class Overview Top Stats</div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-center">
          <div class="p-2.5 rounded-2xl bg-white/5 border border-white/10">
            <span class="text-[10px] text-slate-400 font-bold uppercase block">📦 Avg Completion</span>
            <span class="text-lg font-black text-emerald-400 mt-0.5 block">{{ topStats?.avg_completion || 72 }}%</span>
          </div>

          <div class="p-2.5 rounded-2xl bg-white/5 border border-white/10">
            <span class="text-[10px] text-slate-400 font-bold uppercase block">⏱️ Avg Time</span>
            <span class="text-lg font-black text-blue-400 mt-0.5 block">{{ topStats?.avg_time || '19h 30m' }}</span>
          </div>

          <div class="p-2.5 rounded-2xl bg-white/5 border border-white/10">
            <span class="text-[10px] text-slate-400 font-bold uppercase block">🟢 Active</span>
            <span class="text-lg font-black text-teal-400 mt-0.5 block">{{ topStats?.active_students || 95 }}</span>
          </div>

          <div class="p-2.5 rounded-2xl bg-white/5 border border-white/10">
            <span class="text-[10px] text-slate-400 font-bold uppercase block">🔴 At-Risk</span>
            <span class="text-lg font-black text-rose-400 mt-0.5 block">{{ topStats?.at_risk_students || 12 }}</span>
          </div>

          <div class="p-2.5 rounded-2xl bg-white/5 border border-white/10">
            <span class="text-[10px] text-slate-400 font-bold uppercase block">📅 Engagement</span>
            <span class="text-lg font-black text-purple-400 mt-0.5 block">{{ topStats?.this_week_engagement || 78 }}%</span>
          </div>

          <div class="p-2.5 rounded-2xl bg-white/5 border border-white/10">
            <span class="text-[10px] text-slate-400 font-bold uppercase block">📈 Trend</span>
            <span class="text-lg font-black text-amber-400 mt-0.5 block">{{ topStats?.trend || '+6%' }}</span>
          </div>
        </div>
      </div>

      <!-- Controls & Filter Toolbar -->
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Selectors -->
        <div class="flex flex-wrap items-center gap-3 text-xs">
          <div class="flex items-center gap-2">
            <span class="font-extrabold text-slate-500">Course:</span>
            <select
              v-model="selectedCourse"
              class="px-3 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500"
            >
              <option v-for="c in courses" :key="c.id" :value="c.id">
                📚 {{ c.title }}
              </option>
            </select>
          </div>

          <div class="flex items-center gap-2">
            <span class="font-extrabold text-slate-500">Period:</span>
            <select
              v-model="selectedPeriod"
              class="px-3 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500"
            >
              <option value="This Week">This Week</option>
              <option value="This Month">This Month</option>
              <option value="All Time">All Time</option>
            </select>
          </div>
        </div>

        <!-- Section Navigation Tabs -->
        <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-gray-700 p-1.5 rounded-2xl text-xs font-bold self-start sm:self-auto">
          <button
            @click="setTab('modules')"
            :class="['px-4 py-2 rounded-xl transition flex items-center gap-1.5', activeTab === 'modules' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm font-extrabold' : 'text-slate-600 dark:text-slate-300']"
          >
            <span>📦</span>
            <span>Module Completion</span>
          </button>

          <button
            @click="setTab('time')"
            :class="['px-4 py-2 rounded-xl transition flex items-center gap-1.5', activeTab === 'time' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm font-extrabold' : 'text-slate-600 dark:text-slate-300']"
          >
            <span>⏱️</span>
            <span>Learning Time</span>
          </button>

          <button
            @click="setTab('weekly')"
            :class="['px-4 py-2 rounded-xl transition flex items-center gap-1.5', activeTab === 'weekly' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm font-extrabold' : 'text-slate-600 dark:text-slate-300']"
          >
            <span>📅</span>
            <span>Weekly Report</span>
          </button>
        </div>
      </div>

      <!-- ACTIVE TAB RENDERER -->
      <div class="transition-all duration-300">
        <ModuleCompletion
          v-if="activeTab === 'modules'"
          :overview="moduleCompletionOverview!"
          :modules="modulesData!"
          :heatmap="heatmapData!"
          @selectModule="openModuleDetail"
          @action="handleModuleAction"
        />

        <LearningTime
          v-if="activeTab === 'time'"
          :overview="learningTimeOverview!"
          :studentList="studentTimeList!"
          :chartDays="weeklyChartDays!"
          :distribution="timeDistribution!"
          @selectStudent="openStudentDetail"
          @sendReminder="handleSendReminder"
        />

        <WeeklyReport
          v-if="activeTab === 'weekly'"
          :report="weeklyReport!"
          @openExport="showExportModal = true"
          @triggerAction="handleTeacherAction"
        />
      </div>

      <!-- MODALS -->
      <ModuleDetailModal
        :show="showModuleModal"
        :module="selectedModule"
        @close="showModuleModal = false"
        @action="handleModuleAction"
      />

      <StudentTimeModal
        :show="showStudentModal"
        :student="selectedStudent"
        @close="showStudentModal = false"
        @encourage="handleSendReminder"
        @recommend="handleRecommendContent"
      />

      <ExportReportModal
        :show="showExportModal"
        :weekRange="weeklyReport?.week_range || 'May 19 to May 25, 2025'"
        @close="showExportModal = false"
        @export="handleExportReport"
      />
    </div>
  </TeacherLayout>
</template>
