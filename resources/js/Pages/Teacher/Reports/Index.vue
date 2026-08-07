<script setup lang="ts">
import TeacherLayout from '@/Layouts/TeacherLayout.vue'
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentReport from './StudentReport.vue'
import CourseReport from './CourseReport.vue'
import QuizReport from './QuizReport.vue'
import StudentDetailReportModal from './StudentDetailReportModal.vue'
import ReportExportCenterModal from './ReportExportCenterModal.vue'

const props = defineProps<{
  courses: Array<any>
  studentReportStats?: any
  studentList?: Array<any>
  courseReportData?: any
  quizReportData?: any
}>()

// Active Tab state
const activeTab = ref<'student' | 'course' | 'quiz'>('student')

// Language state
const lang = ref<'km' | 'en'>('km')
const toggleLang = () => {
  lang.value = lang.value === 'km' ? 'en' : 'km'
}

// Modal States
const selectedStudent = ref<any>(null)
const showStudentDetailModal = ref(false)

const showExportCenterModal = ref(false)
const exportCenterType = ref<'student' | 'course' | 'quiz'>('student')

// Toast State
const toastMessage = ref('')
const showToast = ref(false)

const triggerToast = (msg: string) => {
  toastMessage.value = msg
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 4000)
}

// URL tab param reader
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tabParam = urlParams.get('tab')
  if (tabParam === 'course') activeTab.value = 'course'
  else if (tabParam === 'quiz') activeTab.value = 'quiz'
  else if (tabParam === 'student') activeTab.value = 'student'
})

// Tab switcher
const setTab = (tab: 'student' | 'course' | 'quiz') => {
  activeTab.value = tab
  const url = new URL(window.location.href)
  url.searchParams.set('tab', tab)
  window.history.pushState({}, '', url.toString())
}

// Handlers
const openStudentDetail = (student: any) => {
  selectedStudent.value = student
  showStudentDetailModal.value = true
}

const openExportCenter = (type?: 'student' | 'course' | 'quiz') => {
  exportCenterType.value = type || activeTab.value
  showExportCenterModal.value = true
}

const handleGenerateReport = (config: any) => {
  showExportCenterModal.value = false
  const rName = config.reportType === 'student' ? 'Student Report' : config.reportType === 'course' ? 'Course Report' : 'Quiz Report'
  const langText = config.language === 'km' ? 'Khmer' : config.language === 'en' ? 'English' : 'Bilingual'
  triggerToast(`Generated ${rName} in ${config.format.toUpperCase()} (${langText}) successfully! Starting download...`)
}

const handleExportStudentPdf = (student: any) => {
  triggerToast(`Exporting PDF Report for ${student.name}...`)
}

const handleExportStudentExcel = (student: any) => {
  triggerToast(`Exporting Excel Report for ${student.name}...`)
}

const handleEmailStudent = (student: any) => {
  triggerToast(`Performance report emailed to ${student.name}!`)
}

const handleActionTrigger = (actionType: string) => {
  if (actionType === 'practice_quiz') {
    router.get('/teacher/quizzes?tab=practice')
  } else if (actionType === 'recap_video') {
    router.get('/teacher/content?tab=videos')
  } else if (actionType === 'live_review') {
    router.get('/teacher/calendar?tab=schedule')
  } else {
    triggerToast(`Action initiated: ${actionType}`)
  }
}
</script>

<template>
  <TeacherLayout title="Reports Module">
    <div class="space-y-6">
      <!-- Toast Alert Banner -->
      <div
        v-if="showToast"
        class="fixed bottom-5 right-5 z-50 bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-3 animate-bounce"
      >
        <span class="text-xl">✅</span>
        <span class="text-xs font-extrabold">{{ toastMessage }}</span>
      </div>

      <!-- Top Banner & Export Center Action Bar -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2 tracking-tight">
              <span>📊 Reports Module</span>
            </h1>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300">
              Teacher Analytics
            </span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            {{ lang === 'km' ? 'គ្រប់គ្រង និងទាញយករបាយការណ៍សរុបទាំងអស់ (Student Report, Course Report, Quiz Report)' : 'Generate, analyze, and export complete reports for students, courses, and quiz performance.' }}
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button
            @click="toggleLang"
            class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-xs font-extrabold text-slate-700 dark:text-white transition flex items-center gap-1.5"
          >
            <span>{{ lang === 'km' ? '🇰🇭 ភាសាខ្មែរ' : '🇬🇧 English' }}</span>
          </button>

          <button
            @click="openExportCenter()"
            class="px-4 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-black text-xs shadow-md flex items-center gap-1.5 transition"
          >
            <span>📤</span> Report Export Center
          </button>
        </div>
      </div>

      <!-- Tab Navigation Toolbar -->
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs font-extrabold text-slate-500">
          <span>Module Sections:</span>
        </div>

        <div class="flex items-center gap-2 bg-slate-100 dark:bg-gray-700 p-1.5 rounded-2xl text-xs font-bold w-full sm:w-auto overflow-x-auto">
          <button
            @click="setTab('student')"
            :class="['px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap', activeTab === 'student' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm font-black' : 'text-slate-600 dark:text-slate-300']"
          >
            <span>👨‍🎓</span>
            <span>Student Report</span>
          </button>

          <button
            @click="setTab('course')"
            :class="['px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap', activeTab === 'course' ? 'bg-white dark:bg-gray-800 text-indigo-600 shadow-sm font-black' : 'text-slate-600 dark:text-slate-300']"
          >
            <span>📚</span>
            <span>Course Report</span>
          </button>

          <button
            @click="setTab('quiz')"
            :class="['px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap', activeTab === 'quiz' ? 'bg-white dark:bg-gray-800 text-purple-600 shadow-sm font-black' : 'text-slate-600 dark:text-slate-300']"
          >
            <span>📝</span>
            <span>Quiz Report</span>
          </button>
        </div>
      </div>

      <!-- TAB RENDERER -->
      <div class="transition-all duration-300">
        <StudentReport
          v-if="activeTab === 'student'"
          :stats="studentReportStats!"
          :students="studentList!"
          @viewStudent="openStudentDetail"
          @exportStudentPdf="handleExportStudentPdf"
          @exportStudentExcel="handleExportStudentExcel"
          @emailStudent="handleEmailStudent"
        />

        <CourseReport
          v-if="activeTab === 'course'"
          :report="courseReportData!"
          :courses="courses!"
          @exportPdf="openExportCenter('course')"
          @exportExcel="openExportCenter('course')"
          @openAction="handleActionTrigger"
        />

        <QuizReport
          v-if="activeTab === 'quiz'"
          :report="quizReportData!"
          :courses="courses!"
          @exportPdf="openExportCenter('quiz')"
          @exportExcel="openExportCenter('quiz')"
          @openAction="handleActionTrigger"
        />
      </div>

      <!-- MODALS -->
      <StudentDetailReportModal
        :show="showStudentDetailModal"
        :student="selectedStudent"
        @close="showStudentDetailModal = false"
        @downloadPdf="handleExportStudentPdf"
        @exportExcel="handleExportStudentExcel"
        @sendEmail="handleEmailStudent"
      />

      <ReportExportCenterModal
        :show="showExportCenterModal"
        :defaultReportType="exportCenterType"
        @close="showExportCenterModal = false"
        @generate="handleGenerateReport"
      />
    </div>
  </TeacherLayout>
</template>
