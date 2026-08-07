<script setup lang="ts">
import TeacherLayout from '@/Layouts/TeacherLayout.vue'
import { ref, computed, onMounted } from 'vue'

// Import Sub-Components from Schedule, Deadlines, and Student View
import ScheduleDashboard from './Schedule/ScheduleDashboard.vue'
import ScheduleFormModal from './Schedule/ScheduleFormModal.vue'
import LiveClassLobbyModal from './Schedule/LiveClassLobbyModal.vue'

import DeadlinesDashboard from './Deadlines/DeadlinesDashboard.vue'
import DeadlineFormModal from './Deadlines/DeadlineFormModal.vue'
import OverdueHandlingModal from './Deadlines/OverdueHandlingModal.vue'

import StudentViewModal from './StudentViewModal.vue'

const props = defineProps<{
  teacher?: any
  courses: Array<any>
  modules: Array<any>
  majors: Array<any>
  schedules: Array<any>
  deadlines: Array<any>
  overdueStudents: Array<any>
}>()

// Active Tab ('calendar' | 'schedule' | 'deadlines')
const activeTab = ref<'calendar' | 'schedule' | 'deadlines'>('calendar')

// Calendar View Mode ('month' | 'week' | 'list')
const viewMode = ref<'month' | 'week' | 'list'>('month')

// Selected Month & Year
const currentMonth = ref(4) // 0-indexed, May = 4
const currentYear = ref(2025)
const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']

// Filter selections
const filterCourse = ref<string | number>('all')
const filterType = ref('all')

// Modals State
const showScheduleModal = ref(false)
const scheduleToEdit = ref<any>(null)

const showLobbyModal = ref(false)
const selectedLobbySchedule = ref<any>(null)

const showDeadlineModal = ref(false)
const deadlineToEdit = ref<any>(null)

const showOverdueModal = ref(false)
const selectedOverdueDeadline = ref<any>(null)

const showStudentPreview = ref(false)

// Check URL query parameters for active tab on mount
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tabParam = urlParams.get('tab')
  if (tabParam === 'schedule') {
    activeTab.value = 'schedule'
  } else if (tabParam === 'deadlines') {
    activeTab.value = 'deadlines'
  }
})

// Helper methods for Month Navigation
const prevMonth = () => {
  if (currentMonth.value === 0) {
    currentMonth.value = 11
    currentYear.value--
  } else {
    currentMonth.value--
  }
}

const nextMonth = () => {
  if (currentMonth.value === 11) {
    currentMonth.value = 0
    currentYear.value++
  } else {
    currentMonth.value++
  }
}

const resetToToday = () => {
  currentMonth.value = 4
  currentYear.value = 2025
}

// Modal Trigger Functions
const openCreateSchedule = (type?: string) => {
  scheduleToEdit.value = type ? { type } : null
  showScheduleModal.value = true
}

const openEditSchedule = (scheduleItem: any) => {
  scheduleToEdit.value = scheduleItem
  showScheduleModal.value = true
}

const openLobby = (scheduleItem: any) => {
  selectedLobbySchedule.value = scheduleItem
  showLobbyModal.value = true
}

const openCreateDeadline = () => {
  deadlineToEdit.value = null
  showDeadlineModal.value = true
}

const openEditDeadline = (deadlineItem: any) => {
  deadlineToEdit.value = deadlineItem
  showDeadlineModal.value = true
}

const openOverdue = (deadlineItem: any) => {
  selectedOverdueDeadline.value = deadlineItem || (props.deadlines[0] ?? {})
  showOverdueModal.value = true
}

// Color Badge Resolver
const getEventBadgeStyle = (type: string) => {
  switch (type) {
    case 'live_class':
      return 'bg-blue-600 text-white border-blue-700'
    case 'quiz':
      return 'bg-emerald-600 text-white border-emerald-700'
    case 'post_test':
    case 'exam':
      return 'bg-red-600 text-white border-red-700 font-bold'
    case 'assignment':
      return 'bg-orange-600 text-white border-orange-700'
    case 'qa_session':
    case 'office_hour':
      return 'bg-purple-600 text-white border-purple-700'
    case 'payment':
      return 'bg-yellow-500 text-slate-900 border-yellow-600 font-bold'
    default:
      return 'bg-slate-500 text-white border-slate-600'
  }
}
</script>

<template>
  <TeacherLayout title="Calendar & Deadlines Command Center">
    <div class="space-y-6">
      <!-- TOP HEADER & MAIN MODULE NAVIGATION TABS -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2">
              <span class="text-2xl">📅</span>
              <h1 class="text-xl font-black text-slate-800 dark:text-white leading-tight">
                CALENDAR MODULE — TEACHER PANEL
              </h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">
              Command Center របស់គ្រូ - ដឹងថាថ្ងៃនេះត្រូវបង្រៀនអ្វី មាន Quiz អ្វីត្រូវបិទ មាន Assignment អ្វីត្រូវដាក់ពិន្ទុ
            </p>
          </div>

          <div class="flex items-center gap-2">
            <button
              @click="showStudentPreview = true"
              class="px-4 py-2.5 rounded-2xl bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 dark:hover:bg-gray-600 text-slate-800 dark:text-slate-100 text-xs font-bold transition flex items-center gap-2 shadow-sm"
            >
              <span>👨‍🎓 Student View Preview</span>
            </button>

            <button
              @click="openCreateSchedule('live_class')"
              class="px-4 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-md shadow-blue-500/20 flex items-center gap-2"
            >
              <span>➕ Add Schedule</span>
            </button>
          </div>
        </div>

        <!-- TABS BAR -->
        <div class="flex border-b border-slate-200 dark:border-gray-700 pt-2 gap-2">
          <button
            @click="activeTab = 'calendar'"
            :class="activeTab === 'calendar' ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-slate-500 font-medium hover:text-slate-800 dark:hover:text-white'"
            class="pb-3 px-4 text-xs transition flex items-center gap-2"
          >
            <span>🗓️ MY CALENDAR (Main View)</span>
          </button>

          <button
            @click="activeTab = 'schedule'"
            :class="activeTab === 'schedule' ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-slate-500 font-medium hover:text-slate-800 dark:hover:text-white'"
            class="pb-3 px-4 text-xs transition flex items-center gap-2"
          >
            <span>📌 Schedule (កាលវិភាគបង្រៀន)</span>
            <span class="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 font-bold text-[10px]">{{ schedules.length }}</span>
          </button>

          <button
            @click="activeTab = 'deadlines'"
            :class="activeTab === 'deadlines' ? 'border-b-2 border-orange-600 text-orange-600 font-bold' : 'text-slate-500 font-medium hover:text-slate-800 dark:hover:text-white'"
            class="pb-3 px-4 text-xs transition flex items-center gap-2"
          >
            <span>⏰ Deadlines (កាលបរិច្ឆេទផុតកំណត់)</span>
            <span class="px-2 py-0.5 rounded-full bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-300 font-bold text-[10px]">{{ deadlines.length }}</span>
          </button>
        </div>

        <!-- COLOR LEGEND BAR -->
        <div class="p-3.5 bg-slate-50 dark:bg-gray-700/50 rounded-2xl border border-slate-200/80 dark:border-gray-700 space-y-2">
          <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">🎨 COLOR LEGEND — Event Classification</div>
          <div class="flex flex-wrap items-center gap-3 text-[11px] font-bold">
            <span class="px-2.5 py-1 rounded-full bg-blue-600 text-white shadow-xs">🟦 BLUE = Live Class / Teacher-Led</span>
            <span class="px-2.5 py-1 rounded-full bg-emerald-600 text-white shadow-xs">🟩 GREEN = Practice Quiz / Pre-Test</span>
            <span class="px-2.5 py-1 rounded-full bg-red-600 text-white shadow-xs">🟥 RED = Post-Test / Final Exam (សំខាន់)</span>
            <span class="px-2.5 py-1 rounded-full bg-orange-600 text-white shadow-xs">🟧 ORANGE = Assignment Deadline</span>
            <span class="px-2.5 py-1 rounded-full bg-purple-600 text-white shadow-xs">🟪 PURPLE = Q&A / Office Hour</span>
            <span class="px-2.5 py-1 rounded-full bg-yellow-500 text-slate-900 shadow-xs">🟨 YELLOW = Payment Deadline</span>
            <span class="px-2.5 py-1 rounded-full bg-slate-600 text-white shadow-xs">⬜ GRAY = Meeting / General</span>
          </div>
        </div>
      </div>

      <!-- TAB 1: CALENDAR MAIN VIEW -->
      <div v-if="activeTab === 'calendar'" class="space-y-6">
        <!-- Main Calendar Card -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-5">
          <!-- Calendar Header Controls -->
          <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-2 border-b border-slate-100 dark:border-gray-700">
            <div class="flex items-center gap-3">
              <h2 class="text-lg font-black text-slate-800 dark:text-white">
                📅 MY CALENDAR — {{ monthNames[currentMonth] }} {{ currentYear }}
              </h2>
              <span class="px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-bold text-xs">
                👨‍🏫 {{ teacher?.name || 'Mr. Sophea' }}
              </span>
            </div>

            <!-- Month Controls & Views & Filters -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
              <!-- Navigation -->
              <div class="flex items-center bg-slate-100 dark:bg-gray-700 p-1 rounded-xl font-bold">
                <button @click="prevMonth" class="px-2.5 py-1 rounded-lg hover:bg-white dark:hover:bg-gray-600 transition">◀ Month</button>
                <button @click="resetToToday" class="px-3 py-1 rounded-lg hover:bg-white dark:hover:bg-gray-600 transition">Today</button>
                <button @click="nextMonth" class="px-2.5 py-1 rounded-lg hover:bg-white dark:hover:bg-gray-600 transition">Month ▶</button>
              </div>

              <!-- View Toggles -->
              <div class="flex bg-slate-100 dark:bg-gray-700 p-1 rounded-xl font-bold">
                <button
                  @click="viewMode = 'month'"
                  :class="viewMode === 'month' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300'"
                  class="px-3 py-1 rounded-lg transition"
                >
                  Month
                </button>

                <button
                  @click="viewMode = 'week'"
                  :class="viewMode === 'week' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300'"
                  class="px-3 py-1 rounded-lg transition"
                >
                  Week
                </button>

                <button
                  @click="viewMode = 'list'"
                  :class="viewMode === 'list' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300'"
                  class="px-3 py-1 rounded-lg transition"
                >
                  List
                </button>
              </div>

              <!-- Filters -->
              <select v-model="filterCourse" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-medium">
                <option value="all">Course: All Courses</option>
                <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
              </select>

              <select v-model="filterType" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-medium">
                <option value="all">Type: All Types</option>
                <option value="live_class">🟦 Live Class</option>
                <option value="quiz">🟩 Quiz</option>
                <option value="post_test">🟥 Post-Test</option>
                <option value="assignment">🟧 Assignment</option>
              </select>
            </div>
          </div>

          <!-- MONTH VIEW GRID -->
          <div v-if="viewMode === 'month'" class="space-y-2">
            <!-- Day Names -->
            <div class="grid grid-cols-7 gap-2 text-center text-xs font-black text-slate-400 py-1 uppercase tracking-wider">
              <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
            </div>

            <!-- Calendar Days (May 2025 Layout demo) -->
            <div class="grid grid-cols-7 gap-2 text-xs font-bold text-slate-700 dark:text-slate-200">
              <!-- Empty leading offset cells for May 2025 starting Thursday -->
              <div v-for="offset in 4" :key="'offset-' + offset" class="h-28 p-2 rounded-2xl bg-slate-50/50 dark:bg-gray-800/30 border border-slate-100 dark:border-gray-800 opacity-40">
                <span class="text-slate-300 dark:text-slate-600 font-medium">{{ 26 + offset }}</span>
              </div>

              <!-- Month Days 1 to 31 -->
              <div
                v-for="day in 31"
                :key="day"
                class="h-28 p-2 rounded-2xl border transition flex flex-col justify-between hover:border-blue-500 hover:shadow-md cursor-pointer"
                :class="[
                  day === 15 ? 'bg-blue-50/90 dark:bg-blue-950/50 border-blue-500 ring-2 ring-blue-400/50 font-black' : 'bg-white dark:bg-gray-800 border-slate-200/80 dark:border-gray-700'
                ]"
              >
                <div class="flex items-center justify-between">
                  <span :class="day === 15 ? 'w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold' : ''">
                    {{ day }}
                  </span>
                  <span v-if="day === 15" class="text-[9px] font-black text-blue-600 dark:text-blue-300 uppercase">Today</span>
                </div>

                <!-- Event Badges on specific dates -->
                <div class="space-y-1 overflow-hidden">
                  <!-- May 8 -->
                  <template v-if="day === 8">
                    <div class="p-1 rounded-lg bg-blue-600 text-white text-[9px] font-bold truncate shadow-xs">
                      🟦 Live: C Functions
                    </div>
                    <div class="p-1 rounded-lg bg-emerald-600 text-white text-[9px] font-bold truncate shadow-xs">
                      🟩 Quiz Module 2
                    </div>
                  </template>

                  <!-- May 12 -->
                  <template v-if="day === 12">
                    <div class="p-1 rounded-lg bg-orange-600 text-white text-[9px] font-bold truncate shadow-xs">
                      🟧 Assign Due
                    </div>
                  </template>

                  <!-- May 13 -->
                  <template v-if="day === 13">
                    <div class="p-1 rounded-lg bg-red-600 text-white text-[9px] font-bold truncate shadow-xs">
                      🟥 Post-Test Mod 1
                    </div>
                  </template>

                  <!-- May 15 (Today) -->
                  <template v-if="day === 15">
                    <div @click.stop="openLobby(schedules[0])" class="p-1 rounded-lg bg-blue-600 text-white text-[9px] font-bold truncate shadow-xs hover:scale-105 transition">
                      🟦 Live: C Func (2PM)
                    </div>
                    <div class="p-1 rounded-lg bg-emerald-600 text-white text-[9px] font-bold truncate shadow-xs">
                      🟩 Quiz Module 2
                    </div>
                  </template>

                  <!-- May 16 -->
                  <template v-if="day === 16">
                    <div class="p-1 rounded-lg bg-red-600 text-white text-[9px] font-bold truncate shadow-xs">
                      🟥 Post-Test Due
                    </div>
                  </template>

                  <!-- May 18 -->
                  <template v-if="day === 18">
                    <div class="p-1 rounded-lg bg-orange-600 text-white text-[9px] font-bold truncate shadow-xs">
                      🟧 Assignment 1
                    </div>
                  </template>

                  <!-- May 20 -->
                  <template v-if="day === 20">
                    <div class="p-1 rounded-lg bg-yellow-500 text-slate-900 text-[9px] font-bold truncate shadow-xs">
                      🟨 ABA Payment
                    </div>
                  </template>

                  <!-- May 22 -->
                  <template v-if="day === 22">
                    <div class="p-1 rounded-lg bg-purple-600 text-white text-[9px] font-bold truncate shadow-xs">
                      🟪 Q&A Pointers
                    </div>
                  </template>
                </div>
              </div>
            </div>
          </div>

          <!-- LIST VIEW fallback -->
          <div v-else class="space-y-3 text-xs">
            <div v-for="sched in schedules" :key="sched.id" class="p-4 rounded-2xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700/40 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="text-xl">🟦</span>
                <div>
                  <h4 class="font-bold text-slate-800 dark:text-white text-sm">{{ sched.title }}</h4>
                  <p class="text-slate-400">Course: {{ sched.course?.title || 'C Programming Basics' }}</p>
                </div>
              </div>
              <div class="text-right font-bold text-blue-600">
                <div>{{ sched.start_at ? new Date(sched.start_at).toLocaleDateString() : 'May 15, 2025' }}</div>
                <div class="text-[11px]">02:00 PM - 04:00 PM</div>
              </div>
            </div>
          </div>
        </div>

        <!-- DUAL COLUMN BOTTOM PANELS: TODAY'S AGENDA & UPCOMING DEADLINES -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- TODAY'S AGENDA PANEL -->
          <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
              <div>
                <h3 class="text-base font-black text-slate-800 dark:text-white flex items-center gap-2">
                  <span>📌 TODAY'S AGENDA</span>
                </h3>
                <p class="text-xs text-blue-600 dark:text-blue-400 font-bold">May 15, 2025</p>
              </div>

              <button
                @click="openCreateSchedule()"
                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm"
              >
                [➕ Add Schedule]
              </button>
            </div>

            <!-- Agenda Items -->
            <div class="space-y-3 text-xs">
              <!-- Item 1: Practice Quiz -->
              <div class="p-3.5 rounded-2xl border border-emerald-200 dark:border-emerald-900/50 bg-emerald-50/50 dark:bg-emerald-950/30 space-y-1">
                <div class="flex items-center justify-between font-bold">
                  <span class="text-emerald-800 dark:text-emerald-300 font-black">🟩 10:00 AM Quiz Module 2</span>
                  <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-200 text-emerald-800 font-bold">Practice Quiz</span>
                </div>
                <p class="text-slate-600 dark:text-slate-300 font-medium">C Programming Basics · Online Assessment</p>
              </div>

              <!-- Item 2: Live Class -->
              <div class="p-3.5 rounded-2xl border border-blue-200 dark:border-blue-900/50 bg-blue-50/50 dark:bg-blue-950/30 space-y-2">
                <div class="flex items-center justify-between font-bold">
                  <span class="text-blue-800 dark:text-blue-300 font-black">🟦 02:00 PM Live: C Functions</span>
                  <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-600 text-white font-bold">Instructor-Led</span>
                </div>
                <p class="text-slate-600 dark:text-slate-300 font-medium">Deep Dive into Parameters, Return Types & Recursion</p>
                <div class="flex items-center justify-between pt-1">
                  <span class="text-[11px] text-slate-400 font-mono">Zoom: https://zoom.us/j/123-456-789</span>
                  <button @click="openLobby(schedules[0])" class="px-3.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-xs transition">
                    Join →
                  </button>
                </div>
              </div>

              <!-- Item 3: Assignment Deadline -->
              <div class="p-3.5 rounded-2xl border border-orange-200 dark:border-orange-900/50 bg-orange-50/50 dark:bg-orange-950/30 space-y-1">
                <div class="flex items-center justify-between font-bold">
                  <span class="text-orange-800 dark:text-orange-300 font-black">🟧 11:59 PM Assign Due</span>
                  <span class="px-2 py-0.5 rounded-full text-[10px] bg-red-100 text-red-700 font-bold">⏳ 6h left</span>
                </div>
                <p class="text-slate-600 dark:text-slate-300 font-medium">First C Program · 48/60 Submitted</p>
              </div>
            </div>
          </div>

          <!-- UPCOMING DEADLINES PANEL -->
          <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
              <div>
                <h3 class="text-base font-black text-slate-800 dark:text-white flex items-center gap-2">
                  <span>⏰ UPCOMING DEADLINES</span>
                </h3>
                <p class="text-xs text-slate-400">Next 7 Days</p>
              </div>

              <button
                @click="activeTab = 'deadlines'"
                class="px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition shadow-sm"
              >
                [View All Deadlines →]
              </button>
            </div>

            <!-- Deadlines List -->
            <div class="space-y-3 text-xs">
              <!-- Item 1: Post-Test -->
              <div class="p-3.5 rounded-2xl border border-red-200 dark:border-red-900/50 bg-red-50/50 dark:bg-red-950/30 space-y-2">
                <div class="flex items-center justify-between font-bold text-red-700 dark:text-red-300">
                  <span class="font-black">🔴 May 16 - Post-Test Module 1 Due 11:59 PM</span>
                  <span class="text-[11px] px-2 py-0.5 rounded-full bg-red-100 text-red-800 font-bold">40 At-Risk</span>
                </div>
                <div class="flex items-center justify-between text-slate-600 dark:text-slate-300 font-semibold">
                  <span>120 Students - 80 Submitted</span>
                  <button @click="openOverdue(deadlines[0])" class="text-red-600 dark:text-red-400 underline hover:text-red-800 font-bold">
                    View Overdue Students →
                  </button>
                </div>
              </div>

              <!-- Item 2: Assignment -->
              <div class="p-3.5 rounded-2xl border border-orange-200 dark:border-orange-900/50 bg-orange-50/50 dark:bg-orange-950/30 space-y-1.5">
                <div class="flex items-center justify-between font-bold text-orange-800 dark:text-orange-300">
                  <span class="font-black">🟧 May 18 - Assignment: First C Program</span>
                  <span>11:59 PM</span>
                </div>
                <div class="text-slate-600 dark:text-slate-300 font-semibold">
                  48/60 Submitted (80%)
                </div>
              </div>

              <!-- Item 3: Payment -->
              <div class="p-3.5 rounded-2xl border border-yellow-200 dark:border-yellow-900/50 bg-yellow-50/50 dark:bg-yellow-950/30 space-y-1.5">
                <div class="flex items-center justify-between font-bold text-yellow-800 dark:text-yellow-300">
                  <span class="font-black">🟨 May 20 - ABA Payment Deadline</span>
                  <span class="px-2 py-0.5 rounded-full bg-yellow-200 text-yellow-900 text-[10px]">45 Pending</span>
                </div>
                <div class="text-slate-600 dark:text-slate-300 font-semibold">
                  Course Continuation Fee
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 2: SCHEDULE MANAGEMENT (📌 Schedule) -->
      <div v-if="activeTab === 'schedule'">
        <ScheduleDashboard
          :schedules="schedules"
          :courses="courses"
          @openCreate="openCreateSchedule"
          @openEdit="openEditSchedule"
          @openLobby="openLobby"
        />
      </div>

      <!-- TAB 3: DEADLINES MANAGEMENT (⏰ Deadlines) -->
      <div v-if="activeTab === 'deadlines'">
        <DeadlinesDashboard
          :deadlines="deadlines"
          :courses="courses"
          @openCreate="openCreateDeadline"
          @openEdit="openEditDeadline"
          @openOverdue="openOverdue"
        />
      </div>

      <!-- MODALS -->
      <!-- Create / Edit Schedule Modal -->
      <ScheduleFormModal
        :show="showScheduleModal"
        :courses="courses"
        :modules="modules"
        :majors="majors"
        :scheduleToEdit="scheduleToEdit"
        @close="showScheduleModal = false"
      />

      <!-- Live Class Lobby Modal -->
      <LiveClassLobbyModal
        :show="showLobbyModal"
        :schedule="selectedLobbySchedule"
        @close="showLobbyModal = false"
      />

      <!-- Create / Edit Deadline Modal -->
      <DeadlineFormModal
        :show="showDeadlineModal"
        :courses="courses"
        :deadlineToEdit="deadlineToEdit"
        @close="showDeadlineModal = false"
      />

      <!-- Overdue Handling Modal -->
      <OverdueHandlingModal
        :show="showOverdueModal"
        :deadline="selectedOverdueDeadline"
        :students="overdueStudents"
        @close="showOverdueModal = false"
      />

      <!-- Student View Preview Modal -->
      <StudentViewModal
        :show="showStudentPreview"
        @close="showStudentPreview = false"
      />
    </div>
  </TeacherLayout>
</template>
