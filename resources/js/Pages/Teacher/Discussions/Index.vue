<script setup lang="ts">
import TeacherLayout from '@/Layouts/TeacherLayout.vue'
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentQuestions from './StudentQuestions.vue'
import Announcements from './Announcements.vue'
import ReplyQuestionModal from './ReplyQuestionModal.vue'
import QuestionThreadModal from './QuestionThreadModal.vue'
import FaqBuilderModal from './FaqBuilderModal.vue'
import CreateAnnouncementModal from './CreateAnnouncementModal.vue'
import AnnouncementPreviewModal from './AnnouncementPreviewModal.vue'
import AnnouncementMetricsModal from './AnnouncementMetricsModal.vue'

const props = defineProps<{
  courses: Array<any>
  questionsStats?: any
  questions?: Array<any>
  faqs?: Array<any>
  announcements?: Array<any>
}>()

// Active Tab state
const activeTab = ref<'questions' | 'announcements'>('questions')

// Language state
const lang = ref<'km' | 'en'>('km')
const toggleLang = () => {
  lang.value = lang.value === 'km' ? 'en' : 'km'
}

// Toast state
const toastMessage = ref('')
const showToast = ref(false)

const triggerToast = (msg: string) => {
  toastMessage.value = msg
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 4000)
}

// Modal States
const showReplyModal = ref(false)
const selectedQuestionForReply = ref<any>(null)

const showThreadModal = ref(false)
const selectedQuestionForThread = ref<any>(null)

const showFaqModal = ref(false)

const showCreateAnnouncementModal = ref(false)

const showPreviewAnnouncementModal = ref(false)
const selectedAnnouncementForPreview = ref<any>(null)

const showMetricsAnnouncementModal = ref(false)
const selectedAnnouncementForMetrics = ref<any>(null)

// URL tab param reader
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tabParam = urlParams.get('tab')
  if (tabParam === 'announcements') activeTab.value = 'announcements'
  else if (tabParam === 'questions') activeTab.value = 'questions'
})

const setTab = (tab: 'questions' | 'announcements') => {
  activeTab.value = tab
  const url = new URL(window.location.href)
  url.searchParams.set('tab', tab)
  window.history.pushState({}, '', url.toString())
}

// Question Handlers
const handleOpenReply = (q: any) => {
  selectedQuestionForReply.value = q
  showReplyModal.value = true
}

const handleOpenThread = (q: any) => {
  selectedQuestionForThread.value = q
  showThreadModal.value = true
}

const handleSubmitReply = (payload: any) => {
  showReplyModal.value = false
  triggerToast(`Answer posted successfully to ${selectedQuestionForReply.value?.student_name}! Email & Push sent.`)
}

const handleMarkUrgent = (q: any) => {
  triggerToast(`Question from ${q.student_name} marked as URGENT SLA!`)
}

const handleReportSpam = (q: any) => {
  triggerToast(`Question from ${q.student_name} flagged for moderation.`)
}

const handleRemindUnanswered = () => {
  triggerToast(`Batch reminder emails sent for 3 unanswered student questions!`)
}

const handleExportQaLog = () => {
  triggerToast(`Exporting complete Q&A Log in CSV/PDF format...`)
}

const handleAddFaq = (payload: any) => {
  triggerToast(`Added new FAQ: "${payload.question}" to ${payload.course}!`)
}

// Announcement Handlers
const handleCreateAnnouncement = () => {
  showCreateAnnouncementModal.value = true
}

const handleSubmitAnnouncement = (form: any) => {
  showCreateAnnouncementModal.value = false
  triggerToast(`Announcement "${form.titleKh || form.titleEn}" published & broadcasted successfully!`)
}

const handlePreviewAnnouncement = (ann: any) => {
  selectedAnnouncementForPreview.value = ann
  showPreviewAnnouncementModal.value = true
}

const handleViewMetrics = (ann: any) => {
  selectedAnnouncementForMetrics.value = ann
  showMetricsAnnouncementModal.value = true
}

const handleTogglePin = (ann: any) => {
  ann.pinned = !ann.pinned
  triggerToast(`Announcement "${ann.title_kh || ann.title_en}" ${ann.pinned ? 'pinned to top' : 'unpinned'}!`)
}

const handleSendAgain = (ann: any) => {
  triggerToast(`Re-broadcasted Announcement notification to all student channels!`)
}
</script>

<template>
  <TeacherLayout title="Discussion & Announcements">
    <div class="space-y-6">
      <!-- Toast Alert Banner -->
      <div
        v-if="showToast"
        class="fixed bottom-5 right-5 z-50 bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-3 animate-bounce text-xs font-black"
      >
        <span class="text-xl">✅</span>
        <span>{{ toastMessage }}</span>
      </div>

      <!-- Top Header Banner -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2 tracking-tight">
              <span>💬 Discussions & Announcements</span>
            </h1>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
              Teacher Hub
            </span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            {{ lang === 'km' ? 'ឆ្លើយសំណួរសិស្សក្នុងមេរៀន បង្កើត Course FAQ និងផ្ញើសារប្រកាស (Announcements) ទៅកាន់ថ្នាក់រៀន' : 'Answer student chapter questions, maintain Course FAQs, and broadcast targeted announcements.' }}
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

      <!-- Sub-Tab Navigation Bar -->
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs font-extrabold text-slate-500">
          <span>Module Sections:</span>
        </div>

        <div class="flex items-center gap-2 bg-slate-100 dark:bg-gray-700 p-1.5 rounded-2xl text-xs font-bold w-full sm:w-auto overflow-x-auto">
          <button
            @click="setTab('questions')"
            :class="['px-5 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap', activeTab === 'questions' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm font-black' : 'text-slate-600 dark:text-slate-300']"
          >
            <span>❓</span>
            <span>Student Questions</span>
          </button>

          <button
            @click="setTab('announcements')"
            :class="['px-5 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap', activeTab === 'announcements' ? 'bg-white dark:bg-gray-800 text-indigo-600 shadow-sm font-black' : 'text-slate-600 dark:text-slate-300']"
          >
            <span>📢</span>
            <span>Announcements</span>
          </button>
        </div>
      </div>

      <!-- TAB RENDERER -->
      <div class="transition-all duration-300">
        <StudentQuestions
          v-if="activeTab === 'questions'"
          :questions="questions || []"
          :stats="questionsStats || {}"
          :courses="courses || []"
          @openReply="handleOpenReply"
          @openThread="handleOpenThread"
          @openFaqBuilder="showFaqModal = true"
          @markUrgent="handleMarkUrgent"
          @reportSpam="handleReportSpam"
          @remindUnanswered="handleRemindUnanswered"
          @exportLog="handleExportQaLog"
        />

        <Announcements
          v-if="activeTab === 'announcements'"
          :announcements="announcements || []"
          :courses="courses || []"
          @createAnnouncement="handleCreateAnnouncement"
          @previewAnnouncement="handlePreviewAnnouncement"
          @viewMetrics="handleViewMetrics"
          @togglePin="handleTogglePin"
          @sendAgain="handleSendAgain"
          @exportList="handleExportQaLog"
        />
      </div>

      <!-- MODALS -->
      <ReplyQuestionModal
        :show="showReplyModal"
        :question="selectedQuestionForReply"
        @close="showReplyModal = false"
        @submitReply="handleSubmitReply"
      />

      <QuestionThreadModal
        :show="showThreadModal"
        :question="selectedQuestionForThread"
        @close="showThreadModal = false"
        @openReply="handleOpenReply"
      />

      <FaqBuilderModal
        :show="showFaqModal"
        :faqs="faqs || []"
        @close="showFaqModal = false"
        @addFaq="handleAddFaq"
      />

      <CreateAnnouncementModal
        :show="showCreateAnnouncementModal"
        :courses="courses || []"
        @close="showCreateAnnouncementModal = false"
        @submit="handleSubmitAnnouncement"
        @preview="(data) => { handlePreviewAnnouncement(data) }"
      />

      <AnnouncementPreviewModal
        :show="showPreviewAnnouncementModal"
        :announcement="selectedAnnouncementForPreview"
        @close="showPreviewAnnouncementModal = false"
      />

      <AnnouncementMetricsModal
        :show="showMetricsAnnouncementModal"
        :announcement="selectedAnnouncementForMetrics"
        @close="showMetricsAnnouncementModal = false"
      />
    </div>
  </TeacherLayout>
</template>
