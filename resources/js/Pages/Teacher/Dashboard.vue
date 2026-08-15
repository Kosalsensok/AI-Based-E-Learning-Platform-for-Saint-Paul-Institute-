<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, Head } from '@inertiajs/vue3'
import TeacherLayout from '@/Layouts/TeacherLayout.vue'
import { useTheme } from '@/composables/useTheme'
import { i18n } from '@/Services/i18n'

const { isDark } = useTheme()
const currentLang = computed(() => i18n.locale.value)

const coursesIcon = '/images/dashboard/courses-flaticon.svg'
const studentsIcon = '/images/dashboard/students-flaticon.svg'
const tasksIcon = '/images/dashboard/tasks-flaticon.svg'
const earningsIcon = '/images/dashboard/earnings-flaticon.svg'
const analyticsIcon = '/images/dashboard/analytics-flaticon.svg'
const bellIcon = '/images/dashboard/bell-flaticon.svg'
const videoIcon = '/images/dashboard/video-flaticon.svg'
const quizIcon = '/images/dashboard/quiz-flaticon.svg'
const discussionIcon = '/images/dashboard/discussion-flaticon.svg'
const paymentIcon = '/images/dashboard/payment-flaticon.svg'
const enrollmentIcon = '/images/dashboard/enrollment-flaticon.svg'

const performanceIcon = '/images/dashboard/performance-flaticon.svg'
const lightningIcon = '/images/dashboard/lightning-flaticon.svg'
const createCourseIcon = '/images/dashboard/create-course.svg'
const uploadContentIcon = '/images/dashboard/upload-content.svg'
const addQuizIcon = '/images/dashboard/add-quiz.svg'
const announcementIcon = '/images/dashboard/announcement.svg'

const getActivityIcon = (type: string) => {
  switch (type) {
    case 'video': return videoIcon
    case 'quiz': return quizIcon
    case 'discussion': return discussionIcon
    case 'payment': return paymentIcon
    case 'enrollment': return enrollmentIcon
    default: return coursesIcon
  }
}

const getQuickActionIcon = (title: string) => {
  const t = (title || '').toLowerCase()
  if (t.includes('create') || t.includes('បង្កើត')) return createCourseIcon
  if (t.includes('upload') || t.includes('បង្ហោះ')) return uploadContentIcon
  if (t.includes('quiz') || t.includes('សំណួរ')) return addQuizIcon
  if (t.includes('announcement') || t.includes('ជូនដំណឹង')) return announcementIcon
  return createCourseIcon
}

interface ActivityItem {
  id: string
  type: 'payment' | 'enrollment' | 'video' | 'quiz' | 'discussion'
  title: string
  subtitle: string
  time: string
  badge: string
  badgeClass: string
  icon: string
  iconBg: string
}

interface ChartSeries {
  labels: string[]
  completion: number[]
  active_students: number[]
  earnings: number[]
}

const props = defineProps<{
  teacherInfo: {
    name: string
    title: string
    department: string
    status: string
  }
  stats: {
    total_courses: number
    total_students: number
    active_students: number
    pending_tasks: number
    total_earnings: number
  }
  weeklyChart: ChartSeries
  monthlyChart: ChartSeries
  recentActivities: ActivityItem[]
  quickActions: Array<{
    title: string
    desc: string
    href: string
    icon: string
    color: string
    gradient: string
  }>
  recentCourses?: Array<any>
  coursePerformance?: Array<{
    course: string
    students: number
    progress: number
  }>
}>()

// State
const timeframe = ref<'weekly' | 'monthly'>('weekly')
const selectedMetric = ref<'completion' | 'active_students' | 'earnings'>('completion')
const activityFilter = ref<string>('all')

const teacherDisplayName = computed(() => {
  const raw = props.teacherInfo?.name || 'Teacher Sophea'
  if (raw.toLowerCase() === 'sophea teacher') return 'Teacher Sophea'
  return raw
})

// Dynamic Chart Data Calculations
const currentChartData = computed(() => {
  const chart = timeframe.value === 'weekly' ? props.weeklyChart : props.monthlyChart
  const data = chart[selectedMetric.value] || []
  const labels = chart.labels || []

  const maxVal = Math.max(...data, 10)
  const minVal = 0

  // Generate SVG Points for smooth area path
  const width = 600
  const height = 180
  const padding = 20

  const points = data.map((val, idx) => {
    const x = padding + (idx / (data.length - 1)) * (width - 2 * padding)
    const y = height - padding - ((val - minVal) / (maxVal - minVal)) * (height - 2 * padding)
    return { x, y, val, label: labels[idx] }
  })

  // Smooth Bezier Curve Command
  let pathD = ''
  if (points.length > 0) {
    pathD = `M ${points[0].x} ${points[0].y}`
    for (let i = 0; i < points.length - 1; i++) {
      const curr = points[i]
      const next = points[i + 1]
      const mx = (curr.x + next.x) / 2
      pathD += ` C ${mx} ${curr.y}, ${mx} ${next.y}, ${next.x} ${next.y}`
    }
  }

  const areaD = pathD
    ? `${pathD} L ${points[points.length - 1].x} ${height - padding} L ${points[0].x} ${height - padding} Z`
    : ''

  return { points, pathD, areaD, maxVal, data, labels }
})

// Activity Filtering with Guaranteed Chronological Sorting (Newest to Oldest)
const filteredActivities = computed(() => {
  let list = props.recentActivities || []
  if (activityFilter.value !== 'all') {
    list = list.filter(a => a.type === activityFilter.value)
  }
  return [...list].sort((a: any, b: any) => (b.raw_timestamp || 0) - (a.raw_timestamp || 0))
})

const formatCurrency = (val: number) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val || 0)
}

const formatActivityTitle = (title: string) => {
  if (!title) return ''
  return title
    .replace(/\b(mengy)\b/gi, 'Mengy')
    .replace(/\b(chan dara)\b/gi, 'Chan Dara')
    .replace(/\b(sok dara)\b/gi, 'Sok Dara')
    .replace(/\b([a-z])/g, char => char.toUpperCase())
    .replace(/(\bIn\b|\bFor\b|\bFrom\b|\bTo\b|\bOf\b)/g, w => w.toLowerCase())
}

const formatActivityTime = (timeStr: string) => {
  if (!timeStr) return ''
  if (currentLang.value === 'en') {
    return timeStr
      .replace(/(\d+)\s*ថ្ងៃមុន/g, '$1 day ago')
      .replace(/(\d+)\s*ម៉ោងមុន/g, '$1 hour ago')
      .replace(/(\d+)\s*នាទីមុន/g, '$1 min ago')
      .replace(/មុននេះបន្តិច|ទើបតែឥឡូវនេះ/g, 'Just now')
  } else {
    return timeStr
      .replace(/(\d+)\s*days?\s*ago/gi, '$1 ថ្ងៃមុន')
      .replace(/(\d+)\s*hours?\s*ago/gi, '$1 ម៉ោងមុន')
      .replace(/(\d+)\s*mins?\s*ago/gi, '$1 នាទីមុន')
      .replace(/(\d+)\s*minutes?\s*ago/gi, '$1 នាទីមុន')
      .replace(/just now/gi, 'មុននេះបន្តិច')
      .replace(/yesterday/gi, 'ម្សិលមិញ')
  }
}
</script>

<template>
  <Head title="Teacher Dashboard — E.LMS" />
  <TeacherLayout title="Dashboard">
    <div class="space-y-6 font-sans pb-8 transition-colors duration-200">
      
      <!-- Top Hero Banner / Welcome Card -->
      <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 transition-all duration-300 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white shadow-2xl border border-indigo-500/20 dark:from-[#0F172A] dark:via-[#131738] dark:to-[#0F172A] dark:border-slate-800/60">
        <!-- Background Ambient Glow -->
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute left-1/3 -bottom-20 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
          <!-- Left Section: Status & Welcome Text -->
          <div class="space-y-3 max-w-2xl">
            <!-- Status Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 backdrop-blur-md">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
              <span>
                {{ currentLang === 'km' ? 'អនឡាញ' : 'Online' }} • {{ currentLang === 'km' ? 'មហាវិទ្យាល័យវិទ្យាសាស្ត្រកុំព្យូទ័រ' : (teacherInfo?.department || 'Faculty of Computing') }}
              </span>
            </div>
            
            <!-- Title with Waving Hand Animation -->
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white flex items-center gap-2 flex-wrap">
              <span>{{ currentLang === 'km' ? `សូមស្វាគមន៍ការត្រឡប់មកវិញ, ${teacherDisplayName}` : `Welcome back, ${teacherDisplayName}` }}</span>
              <span class="inline-block animate-waving-hand select-none text-2xl sm:text-3xl">👋</span>
            </h1>
            
            <!-- Description -->
            <p class="text-xs sm:text-sm text-slate-300 dark:text-slate-400 font-normal leading-relaxed">
              {{ currentLang === 'km' 
                ? 'គ្រប់គ្រងវគ្គសិក្សា តាមដានវឌ្ឍនភាពសិស្ស និងពិនិត្យមើលការទូទាត់ប្រាក់ ABA ប្រចាំខែរបស់អ្នកនៅទីនេះ។' 
                : 'Manage your courses, track student learning progress, and monitor your monthly ABA payouts in one unified hub.' 
              }}
            </p>
          </div>

          <!-- Right Section: Action Buttons with Tactile Micro-Interactions & Enhanced Contrast -->
          <div class="flex items-center gap-3 shrink-0 flex-wrap sm:flex-nowrap">
            <!-- Primary CTA Button -->
            <Link
              href="/teacher/courses/create"
              class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-white bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 shadow-lg shadow-cyan-500/25 border border-cyan-300/30 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
              <span>{{ currentLang === 'km' ? 'បង្កើតវគ្គសិក្សា' : 'Create Course' }}</span>
            </Link>

            <!-- Secondary CTA Button (Enhanced Contrast & Border Legibility) -->
            <Link
              href="/teacher/content"
              class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-white bg-white/20 hover:bg-white/30 border-2 border-white/40 hover:border-white/70 backdrop-blur-md shadow-lg transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
            >
              <svg class="w-4 h-4 text-cyan-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
              <span>{{ currentLang === 'km' ? 'បញ្ចូលមាតិកា' : 'Upload Content' }}</span>
            </Link>
          </div>
        </div>
      </div>

      <!-- 4 Overview Stat Cards Grid (Explicit labels, High Legibility, Flaticon-style 3D Icons) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
        
        <!-- Card 1: Active Courses (Explicit Label & Subtitle) -->
        <Link
          href="/teacher/courses"
          class="group bg-white dark:bg-slate-900/60 hover:bg-slate-50 dark:hover:bg-slate-900/90 p-5 sm:p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 hover:border-cyan-500/50 dark:hover:border-cyan-500/40 backdrop-blur-xl transition-all duration-300 shadow-sm dark:shadow-xl flex items-center justify-between cursor-pointer"
        >
          <div class="space-y-1.5 flex-1 min-w-0">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
              {{ currentLang === 'km' ? '📚 វគ្គសិក្សាសកម្ម' : '📚 ACTIVE COURSES' }}
            </span>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors font-mono py-0.5">
              {{ stats.total_courses }}
            </div>
            <div class="inline-flex items-center gap-1.5 text-xs sm:text-[13px] font-semibold text-cyan-600 dark:text-cyan-400 leading-normal pt-1">
              <span class="w-2 h-2 rounded-full bg-cyan-500 dark:bg-cyan-400 shrink-0"></span>
              <span class="truncate">{{ currentLang === 'km' ? `${stats.total_courses} វគ្គសិក្សាសកម្ម (Active Courses)` : `${stats.total_courses} Active Courses` }}</span>
            </div>
          </div>
          <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-cyan-500/10 dark:bg-cyan-500/15 border border-cyan-500/20 flex items-center justify-center p-2 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shrink-0 ml-2 shadow-sm">
            <img :src="coursesIcon" alt="Courses" class="w-full h-full object-contain filter drop-shadow-sm" />
          </div>
        </Link>

        <!-- Card 2: Active Students (Accessible 13px Subtitle & Flaticon Icon) -->
        <Link
          href="/teacher/students"
          class="group bg-white dark:bg-slate-900/60 hover:bg-slate-50 dark:hover:bg-slate-900/90 p-5 sm:p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 hover:border-indigo-500/50 dark:hover:border-indigo-500/40 backdrop-blur-xl transition-all duration-300 shadow-sm dark:shadow-xl flex items-center justify-between cursor-pointer"
        >
          <div class="space-y-1.5 flex-1 min-w-0">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
              {{ currentLang === 'km' ? '👥 សិស្សសកម្ម' : '👥 ACTIVE STUDENTS' }}
            </span>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors font-mono py-0.5">
              {{ stats.active_students }}
            </div>
            <div class="inline-flex items-center gap-1.5 text-xs sm:text-[13px] font-semibold text-indigo-600 dark:text-indigo-400 leading-normal pt-1">
              <span class="w-2 h-2 rounded-full bg-indigo-500 dark:bg-indigo-400 shrink-0"></span>
              <span class="truncate">
                {{ currentLang === 'km' ? `ក្នុងចំណោមសិស្សសរុប ${stats.total_students} នាក់` : `Of ${stats.total_students} Total Enrolled` }}
              </span>
            </div>
          </div>
          <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/15 border border-indigo-500/20 flex items-center justify-center p-2 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shrink-0 ml-2 shadow-sm">
            <img :src="studentsIcon" alt="Students" class="w-full h-full object-contain filter drop-shadow-sm" />
          </div>
        </Link>

        <!-- Card 3: Pending Tasks (Clarified Submissions to Grade Count & Flaticon Icon) -->
        <Link
          href="/teacher/assessment?tab=quizzes"
          class="group bg-white dark:bg-slate-900/60 hover:bg-slate-50 dark:hover:bg-slate-900/90 p-5 sm:p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 hover:border-amber-500/50 dark:hover:border-amber-500/40 backdrop-blur-xl transition-all duration-300 shadow-sm dark:shadow-xl flex items-center justify-between cursor-pointer"
        >
          <div class="space-y-1.5 flex-1 min-w-0">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
              {{ currentLang === 'km' ? '⏳ កិច្ចការត្រូវដាក់ពិន្ទុ' : '⏳ PENDING TASKS' }}
            </span>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors font-mono py-0.5">
              {{ stats.pending_tasks }}
            </div>
            <div class="inline-flex items-center gap-1.5 text-xs sm:text-[13px] font-semibold text-amber-600 dark:text-amber-400 leading-normal pt-1">
              <span class="w-2 h-2 rounded-full bg-amber-500 dark:bg-amber-400 animate-ping shrink-0"></span>
              <span class="truncate">{{ currentLang === 'km' ? `${stats.pending_tasks} កិច្ចការសិស្សត្រូវដាក់ពិន្ទុ` : `${stats.pending_tasks} Submissions to Grade` }}</span>
            </div>
          </div>
          <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-amber-500/10 dark:bg-amber-500/15 border border-amber-500/20 flex items-center justify-center p-2 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shrink-0 ml-2 shadow-sm">
            <img :src="tasksIcon" alt="Pending Tasks" class="w-full h-full object-contain filter drop-shadow-sm" />
          </div>
        </Link>

        <!-- Card 4: Total Earnings (High Contrast & Flaticon Icon) -->
        <Link
          href="/teacher/earnings"
          class="group bg-white dark:bg-slate-900/60 hover:bg-slate-50 dark:hover:bg-slate-900/90 p-5 sm:p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 hover:border-emerald-500/50 dark:hover:border-emerald-500/40 backdrop-blur-xl transition-all duration-300 shadow-sm dark:shadow-xl flex items-center justify-between cursor-pointer"
        >
          <div class="space-y-1.5 flex-1 min-w-0">
            <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block whitespace-nowrap">
              {{ currentLang === 'km' ? '💵 ចំណូលសរុប (ខែនេះ)' : '💵 EARNINGS (THIS MONTH)' }}
            </span>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors font-mono tracking-tight py-0.5">
              {{ formatCurrency(stats.total_earnings) }}
            </div>
            <div class="inline-flex items-center gap-1.5 text-xs sm:text-[13px] font-semibold text-emerald-600 dark:text-emerald-400 leading-normal pt-1">
              <span class="truncate">{{ currentLang === 'km' ? '↑ +14.5% ធៀបខែមុន' : '↑ +14.5% vs Last Month' }}</span>
            </div>
          </div>
          <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/15 border border-emerald-500/20 flex items-center justify-center p-2 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shrink-0 ml-2 shadow-sm">
            <img :src="earningsIcon" alt="Earnings" class="w-full h-full object-contain filter drop-shadow-sm" />
          </div>
        </Link>

      </div>

      <!-- Section: Charts & Recent Activities Feed -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2-Columns: Progress Analytics & Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900/60 p-5 md:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 backdrop-blur-xl space-y-5 shadow-sm dark:shadow-xl">
          
          <!-- Chart Header: Top Row with Title & Timeframe Switcher on the Right -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800/80 pb-4">
            <div class="space-y-1">
              <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-xl bg-indigo-500/10 dark:bg-indigo-500/20 p-1 flex items-center justify-center shrink-0 border border-indigo-500/20 shadow-xs">
                  <img :src="analyticsIcon" alt="Analytics" class="w-full h-full object-contain filter drop-shadow-xs" />
                </div>
                <span>{{ currentLang === 'km' ? 'ការវិភាគវឌ្ឍនភាព និងការបញ្ចប់' : 'Progress & Completion Analytics' }}</span>
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-sans">
                {{ currentLang === 'km' 
                  ? 'ការវិភាគវឌ្ឍនភាពសិក្សារបស់សិស្ស និន្នាការបញ្ចប់មេរៀន និងប្រាក់ចំណូល' 
                  : 'Student learning progress analysis, completion trends, and earnings breakdown' 
                }}
              </p>
            </div>

            <!-- Timeframe Filter Toggle (Right Aligned to reduce clutter) -->
            <div class="inline-flex rounded-xl bg-slate-100 dark:bg-slate-950 p-1 border border-slate-200 dark:border-slate-800 text-xs font-sans self-start sm:self-center shrink-0">
              <button
                @click="timeframe = 'weekly'"
                :class="[timeframe === 'weekly' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white', 'px-3 py-1.5 rounded-lg transition-all cursor-pointer']"
              >
                {{ currentLang === 'km' ? 'ប្រចាំសប្ដាហ៍' : 'Weekly' }}
              </button>
              <button
                @click="timeframe = 'monthly'"
                :class="[timeframe === 'monthly' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white', 'px-3 py-1.5 rounded-lg transition-all cursor-pointer']"
              >
                {{ currentLang === 'km' ? 'ប្រចាំខែ' : 'Monthly' }}
              </button>
            </div>
          </div>

          <!-- Secondary Row: Metric Selector Pills (Left Aligned for clean visual hierarchy) -->
          <div class="flex items-center justify-between flex-wrap gap-2 pt-0.5">
            <div class="inline-flex rounded-xl bg-slate-100 dark:bg-slate-950 p-1 border border-slate-200 dark:border-slate-800 text-xs">
              <button
                @click="selectedMetric = 'completion'"
                :class="[selectedMetric === 'completion' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white', 'px-3 py-1.5 rounded-lg transition-all cursor-pointer']"
              >
                {{ currentLang === 'km' ? 'ភាគរយបញ្ចប់ (%)' : 'Completion %' }}
              </button>
              <button
                @click="selectedMetric = 'active_students'"
                :class="[selectedMetric === 'active_students' ? 'bg-cyan-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white', 'px-3 py-1.5 rounded-lg transition-all cursor-pointer']"
              >
                {{ currentLang === 'km' ? 'ចំនួនសិស្ស' : 'Students' }}
              </button>
              <button
                @click="selectedMetric = 'earnings'"
                :class="[selectedMetric === 'earnings' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white', 'px-3 py-1.5 rounded-lg transition-all cursor-pointer']"
              >
                {{ currentLang === 'km' ? 'ប្រាក់ចំណូល ($)' : 'Earnings ($)' }}
              </button>
            </div>

            <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 hidden sm:inline-flex items-center gap-1.5 font-mono">
              <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
              {{ selectedMetric === 'completion' ? 'Target: 80%+' : selectedMetric === 'active_students' ? 'Enrolled Active' : 'USD ($) ABA Payout' }}
            </span>
          </div>

          <!-- Dynamic SVG Curved Area Chart -->
          <div class="relative w-full overflow-hidden pt-2">
            <svg viewBox="0 0 600 180" class="w-full h-48 sm:h-56 overflow-visible">
              <defs>
                <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#6366f1" stop-opacity="0.35" />
                  <stop offset="100%" stop-color="#6366f1" stop-opacity="0.0" />
                </linearGradient>
              </defs>

              <!-- Grid Horizontal Lines -->
              <line x1="20" y1="20" x2="580" y2="20" :stroke="isDark ? '#1e293b' : '#e2e8f0'" stroke-dasharray="4 4" />
              <line x1="20" y1="60" x2="580" y2="60" :stroke="isDark ? '#1e293b' : '#e2e8f0'" stroke-dasharray="4 4" />
              <line x1="20" y1="100" x2="580" y2="100" :stroke="isDark ? '#1e293b' : '#e2e8f0'" stroke-dasharray="4 4" />
              <line x1="20" y1="140" x2="580" y2="140" :stroke="isDark ? '#1e293b' : '#e2e8f0'" stroke-dasharray="4 4" />

              <!-- Area Fill -->
              <path :d="currentChartData.areaD" fill="url(#chartGradient)" />

              <!-- Smooth Curve Line -->
              <path :d="currentChartData.pathD" fill="none" stroke="#6366f1" stroke-width="3" stroke-linecap="round" />

              <!-- Data Points with Glow -->
              <g v-for="(p, i) in currentChartData.points" :key="i">
                <circle :cx="p.x" :cy="p.y" r="5" :class="['fill-indigo-500', isDark ? 'stroke-slate-900' : 'stroke-white', 'stroke-2 hover:r-7 transition-all duration-200 cursor-pointer shadow-md']" />
                <text :x="p.x" y="175" text-anchor="middle" :class="[isDark ? 'fill-slate-400' : 'fill-slate-500', 'text-[10px] font-mono font-medium']">{{ p.label }}</text>
              </g>
            </svg>
          </div>

          <!-- Bottom Legend & Summary -->
          <div class="grid grid-cols-3 gap-3 p-3 bg-slate-50 dark:bg-slate-950/80 rounded-2xl border border-slate-200/80 dark:border-slate-800 text-xs text-center font-sans">
            <div>
              <span class="text-slate-500 dark:text-slate-400 block text-[10px]">{{ currentLang === 'km' ? 'តម្លៃខ្ពស់បំផុត' : 'Peak Value' }}</span>
              <span class="text-slate-900 dark:text-white font-bold font-mono">
                {{ selectedMetric === 'earnings' ? formatCurrency(currentChartData.maxVal) : currentChartData.maxVal + (selectedMetric === 'completion' ? '%' : (currentLang === 'km' ? ' នាក់' : ' Students')) }}
              </span>
            </div>
            <div>
              <span class="text-slate-500 dark:text-slate-400 block text-[10px]">{{ currentLang === 'km' ? 'រយៈពេល' : 'Period' }}</span>
              <span class="text-cyan-600 dark:text-cyan-400 font-bold uppercase">{{ timeframe === 'weekly' ? (currentLang === 'km' ? 'សប្ដាហ៍' : 'Weekly') : (currentLang === 'km' ? 'ខែ' : 'Monthly') }}</span>
            </div>
            <div>
              <span class="text-slate-500 dark:text-slate-400 block text-[10px]">{{ currentLang === 'km' ? 'ស្ថានភាព' : 'Status' }}</span>
              <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ currentLang === 'km' ? 'កំណើនថេរ ↑' : 'Consistent Growth ↑' }}</span>
            </div>
          </div>

        </div>

        <!-- Right 1-Column: Recent Activities Feed (Chronologically Ordered, Flaticon-style 3D Icons) -->
        <div class="bg-white dark:bg-slate-900/60 p-5 md:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 backdrop-blur-xl space-y-4 shadow-sm dark:shadow-xl flex flex-col">
          
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-3">
            <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2.5">
              <div class="w-7 h-7 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 p-1 flex items-center justify-center shrink-0 border border-amber-500/20 shadow-xs">
                <img :src="bellIcon" alt="Notifications" class="w-full h-full object-contain filter drop-shadow-xs" />
              </div>
              <span>{{ currentLang === 'km' ? 'សកម្មភាពថ្មីៗ' : 'Recent Activities' }}</span>
            </h3>
            <span class="text-[11px] font-semibold text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 cursor-pointer">
              {{ currentLang === 'km' ? 'ផ្សាយបន្តផ្ទាល់' : 'Live Feed' }}
            </span>
          </div>

          <!-- Activity Filter Pills -->
          <div class="flex flex-wrap items-center gap-1.5 text-[11px] font-semibold">
            <button
              @click="activityFilter = 'all'"
              :class="[activityFilter === 'all' ? 'bg-cyan-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200', 'px-2.5 py-1 rounded-lg transition-all cursor-pointer']"
            >
              {{ currentLang === 'km' ? 'ទាំងអស់' : 'All' }}
            </button>
            <button
              @click="activityFilter = 'enrollment'"
              :class="[activityFilter === 'enrollment' ? 'bg-cyan-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200', 'px-2.5 py-1 rounded-lg transition-all cursor-pointer']"
            >
              {{ currentLang === 'km' ? 'ការចុះឈ្មោះ' : 'Enrollments' }}
            </button>
            <button
              @click="activityFilter = 'payment'"
              :class="[activityFilter === 'payment' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200', 'px-2.5 py-1 rounded-lg transition-all cursor-pointer']"
            >
              {{ currentLang === 'km' ? 'ការទូទាត់' : 'Payments' }}
            </button>
            <button
              @click="activityFilter = 'quiz'"
              :class="[activityFilter === 'quiz' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200', 'px-2.5 py-1 rounded-lg transition-all cursor-pointer']"
            >
              {{ currentLang === 'km' ? 'កម្រងសំណួរ' : 'Quizzes' }}
            </button>
          </div>

          <!-- Activity Feed Stream List with Universal Slim Scrollbar -->
          <div class="space-y-3 flex-1 overflow-y-auto max-h-[340px] custom-scrollbar-slim pr-1.5">
            <div
              v-for="act in filteredActivities"
              :key="act.id"
              class="group p-3 rounded-2xl bg-slate-50/90 dark:bg-slate-950/80 border border-slate-200/80 dark:border-slate-800/80 hover:border-indigo-500/40 dark:hover:border-indigo-500/40 transition-all duration-200 flex items-start gap-3"
            >
              <div class="w-10 h-10 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 p-1.5 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:rotate-3 transition-all duration-200 shadow-xs mt-0.5">
                <img :src="getActivityIcon(act.type)" :alt="act.type" class="w-full h-full object-contain filter drop-shadow-xs" />
              </div>
              <div class="flex-1 min-w-0 text-xs space-y-1">
                <div class="flex items-center justify-between gap-1.5">
                  <p class="font-bold text-slate-900 dark:text-slate-100 truncate group-hover:text-indigo-600 dark:group-hover:text-cyan-400 transition-colors">
                    {{ formatActivityTitle(act.title) || (act.type === 'enrollment' ? 'New Student Enrolled' : 'Recent Course Activity') }}
                  </p>
                  <span :class="[act.badgeClass, 'px-2 py-0.5 text-[9px] font-bold rounded-md shrink-0']">{{ act.badge }}</span>
                </div>
                <p class="text-[11.5px] text-slate-600 dark:text-slate-300 truncate leading-relaxed font-sans">{{ act.subtitle || 'General update' }}</p>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 pt-0.5">
                  <i class="pi pi-clock text-[11px] text-indigo-500 dark:text-indigo-400 font-bold"></i>
                  <span class="font-mono">{{ formatActivityTime(act.time) }}</span>
                </div>
              </div>
            </div>

            <div v-if="filteredActivities.length === 0" class="py-8 text-center text-xs text-slate-400 dark:text-slate-500 font-sans">
              {{ currentLang === 'km' ? 'មិនមានសកម្មភាពថ្មីៗសម្រាប់ប្រភេទនេះទេ។' : 'No recent activities found for this filter.' }}
            </div>
          </div>

        </div>

      </div>

      <!-- Section: Course Performance Table (Accurate Data Visualization & Flaticon Icon) -->
      <div class="bg-white dark:bg-slate-900/60 p-5 md:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 backdrop-blur-xl space-y-4 shadow-sm dark:shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800/80 pb-3">
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2.5">
              <div class="w-7 h-7 rounded-xl bg-cyan-500/10 dark:bg-cyan-500/20 p-1 flex items-center justify-center shrink-0 border border-cyan-500/20 shadow-xs">
                <img :src="performanceIcon" alt="Performance" class="w-full h-full object-contain filter drop-shadow-xs" />
              </div>
              <span>{{ currentLang === 'km' ? 'ប្រសិទ្ធភាពវគ្គសិក្សា' : 'Course Performance' }}</span>
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed font-sans">
              {{ currentLang === 'km' 
                ? 'ទិដ្ឋភាពទូទៅនៃការចុះឈ្មោះសិស្ស និងវឌ្ឍនភាពបញ្ចប់តាមមុខវិជ្ជា' 
                : 'Overview of student enrollment and completion progress per course' 
              }}
            </p>
          </div>
          <Link href="/teacher/courses" class="text-xs font-semibold text-indigo-600 dark:text-cyan-400 hover:text-indigo-700 dark:hover:text-cyan-300 inline-flex items-center gap-1 group">
            <span>{{ currentLang === 'km' ? 'មើលវគ្គសិក្សាទាំងអស់' : 'View All Courses' }}</span>
            <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
          </Link>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="bg-slate-50/80 dark:bg-slate-950/80 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-800">
                <th class="py-3 px-4">{{ currentLang === 'km' ? 'វគ្គសិក្សា' : 'Course' }}</th>
                <th class="py-3 px-4 text-center">{{ currentLang === 'km' ? 'សិស្ស' : 'Students' }}</th>
                <th class="py-3 px-4 w-2/5">{{ currentLang === 'km' ? 'វឌ្ឍនភាព' : 'Progress' }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-sans">
              <tr
                v-for="cp in (coursePerformance || [
                  { course: 'C Programming', students: 45, progress: 72 },
                  { course: 'Database Systems', students: 38, progress: 65 },
                  { course: 'Web Development', students: 30, progress: 81 },
                  { course: 'Computer Networks', students: 22, progress: 58 }
                ])"
                :key="cp.course"
                class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors cursor-pointer"
              >
                <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-white flex items-center gap-2.5">
                  <span class="w-2 h-2 rounded-full bg-cyan-500 dark:bg-cyan-400 shrink-0"></span>
                  <span>{{ cp.course }}</span>
                </td>
                <td class="py-3.5 px-4 text-center font-mono font-bold text-indigo-600 dark:text-purple-400">{{ cp.students }}</td>
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-3">
                    <div class="flex-1 bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden border border-slate-200/60 dark:border-slate-700/60">
                      <div
                        class="h-full bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-500 rounded-full transition-all duration-700 ease-out shadow-xs"
                        :style="{ width: `${cp.progress}%` }"
                      ></div>
                    </div>
                    <span class="font-mono font-bold text-slate-800 dark:text-slate-200 text-xs w-10 text-right">{{ cp.progress }}%</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Quick Actions Grid (Flaticon 3D Icons & Tactile Feedback) -->
      <div class="space-y-3">
        <h3 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider flex items-center gap-2">
          <div class="w-6 h-6 rounded-lg bg-amber-500/10 dark:bg-amber-500/20 p-1 flex items-center justify-center shrink-0 border border-amber-500/20 shadow-xs">
            <img :src="lightningIcon" alt="Quick Actions" class="w-full h-full object-contain filter drop-shadow-xs" />
          </div>
          <span>{{ currentLang === 'km' ? 'សកម្មភាពរហ័ស' : 'Quick Actions' }}</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <Link
            v-for="qa in quickActions"
            :key="qa.title"
            :href="qa.href"
            class="group p-4 bg-white dark:bg-slate-900/60 hover:bg-slate-50 dark:hover:bg-slate-900/90 rounded-2xl border border-slate-200/80 dark:border-slate-800 hover:border-indigo-500/40 backdrop-blur-xl transition-all duration-300 shadow-sm dark:shadow-md hover:shadow-lg hover:-translate-y-1 active:scale-[0.98] flex items-center gap-3.5 cursor-pointer"
          >
            <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800 p-2 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-xs">
              <img :src="getQuickActionIcon(qa.title)" :alt="qa.title" class="w-full h-full object-contain filter drop-shadow-xs" />
            </div>
            <div class="min-w-0 flex-1">
              <h4 class="font-bold text-slate-800 dark:text-white text-xs group-hover:text-indigo-600 dark:group-hover:text-cyan-300 transition-colors truncate">{{ qa.title }}</h4>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5 leading-relaxed font-sans">{{ qa.desc }}</p>
            </div>
          </Link>
        </div>
      </div>

    </div>
  </TeacherLayout>
</template>
