<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import { i18n } from '@/Services/i18n'

const logoUrl = '/images/logo.png'
const actionBtnIcon = '/images/actions/action-button.svg'
const onlineIconUrl = '/images/nav/online.svg'
const offlineIconUrl = '/images/nav/offline.svg'

const props = defineProps<{ title?: string }>()

const page = usePage<any>()
const user = computed(() => page.props.auth?.user || {})

const isSidebarCollapsed = ref(false)
const sidebarOpen = ref(false)

const toggleSidebarCollapse = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value
}

// --- Top Navbar State & Dropdown Logic ---
const searchQuery = ref('')
const isSearchOpen = ref(false)
const isNotificationOpen = ref(false)
const isProfileOpen = ref(false)
const isQuickActionOpen = ref(false)
const isLangOpen = ref(false)
const isStatusOpen = ref(false)
const isFullscreen = ref(false)

const currentLang = computed(() => i18n.locale.value || 'km')

const isOnline = ref(typeof window !== 'undefined' ? window.navigator.onLine : true)
const manualStatusOverride = ref<boolean | null>(null)

const updateOnlineStatus = () => {
  if (manualStatusOverride.value !== null) {
    isOnline.value = manualStatusOverride.value
  } else {
    isOnline.value = window.navigator.onLine
  }
}

const setStatusMode = (online: boolean) => {
  manualStatusOverride.value = online
  isOnline.value = online
  isStatusOpen.value = false
}

const languages = [
  { code: 'km', name: '🇰🇭 ភាសាខ្មែរ', flagUrl: '/images/flags/km.svg' },
  { code: 'en', name: '🇬🇧 English', flagUrl: '/images/flags/en.svg' },
]

const selectLanguage = (code: string) => {
  if (i18n.setLanguage) {
    i18n.setLanguage(code as 'km' | 'en')
  }
  isLangOpen.value = false
}

const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen().catch(err => console.log(err))
    isFullscreen.value = true
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen()
      isFullscreen.value = false
    }
  }
}

const closeAllDropdowns = () => {
  isSearchOpen.value = false
  isNotificationOpen.value = false
  isProfileOpen.value = false
  isQuickActionOpen.value = false
  isLangOpen.value = false
  isStatusOpen.value = false
}

const toggleDropdown = (target: 'search' | 'notification' | 'profile' | 'quick' | 'lang' | 'status') => {
  const current = target === 'search' ? isSearchOpen.value
    : target === 'notification' ? isNotificationOpen.value
    : target === 'profile' ? isProfileOpen.value
    : target === 'quick' ? isQuickActionOpen.value
    : target === 'lang' ? isLangOpen.value
    : isStatusOpen.value

  closeAllDropdowns()

  if (target === 'search') isSearchOpen.value = !current
  else if (target === 'notification') isNotificationOpen.value = !current
  else if (target === 'profile') isProfileOpen.value = !current
  else if (target === 'quick') isQuickActionOpen.value = !current
  else if (target === 'lang') isLangOpen.value = !current
  else if (target === 'status') isStatusOpen.value = !current
}

const handleKeydown = (e: KeyboardEvent) => {
  if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
    e.preventDefault()
    toggleDropdown('search')
  } else if (e.key === 'Escape') {
    closeAllDropdowns()
  }
}

// Dynamic Breadcrumb Calculation
const currentBreadcrumb = computed(() => {
  const url = page.url
  if (url.startsWith('/student/dashboard')) return ['Student', 'Dashboard']
  if (url.startsWith('/student/browse')) return ['Student', 'Browse Catalog']
  if (url.startsWith('/student/courses')) return ['Student', 'My Courses']
  if (url.startsWith('/student/learn') || url.startsWith('/student/content')) return ['Student', 'Learning Content']
  if (url.startsWith('/student/quizzes') || url.startsWith('/student/quiz')) return ['Student', 'Quiz & Assessment']
  if (url.startsWith('/student/ai-path')) return ['Student', 'AI Learning Path']
  if (url.startsWith('/student/progress')) return ['Student', 'Progress Tracking']
  if (url.startsWith('/student/payments')) return ['Student', 'Payments (ABA)']
  if (url.startsWith('/student/certificates')) return ['Student', 'Certificates']
  if (url.startsWith('/student/discussions')) return ['Student', 'Discussion & Support']
  if (url.startsWith('/student/notifications')) return ['Student', 'Notifications']
  if (url.startsWith('/student/calendar')) return ['Student', 'Calendar']
  if (url.startsWith('/student/profile')) return ['Student', 'My Profile']
  return ['Student', 'Workspace']
})

const pageTitle = computed(() => {
  if (props.title) return props.title
  const crumb = currentBreadcrumb.value
  return crumb.length > 1 ? crumb[crumb.length - 1] : 'Student Portal'
})

// Quick Actions Dropdown items for Student
const quickActions = [
  { name: 'រៀនមេរៀនបន្ត (Continue Learning)', href: '/student/courses?tab=current', iconUrl: '/images/actions/add-course.svg' },
  { name: 'ស្វែងរក Course ថ្មី (Browse Catalog)', href: '/student/browse', iconUrl: '/images/nav/sub/overview.svg' },
  { name: 'ធ្វើ Quiz / Practice Test', href: '/student/quizzes?tab=practice', iconUrl: '/images/nav/quiz.svg' },
  { name: 'ទាញយក Certificate', href: '/student/certificates', iconUrl: '/images/nav/certificate.svg' },
  { name: 'សួរសំណួរទៅកាន់គ្រូ (Ask Teacher)', href: '/student/discussions?tab=ask', iconUrl: '/images/actions/announcement.svg' },
]

// Notifications Drawer Data for Student
const notifications = ref([
  {
    id: 1,
    title: 'ការរំលឹកបង់ប្រាក់ Payment Pending',
    desc: 'សូមទូទាត់ប្រាក់សម្រាប់ Database Systems ដើម្បីបើកមើលមេរៀន',
    time: '1 ម៉ោងមុន',
    type: 'payment',
    read: false,
    link: '/student/payments'
  },
  {
    id: 2,
    title: 'Quiz ថ្មីអាចធ្វើបានហើយ: Module 2 Practice',
    desc: 'គ្រូ Sophea បានបើក Practice Quiz សម្រាប់ C Programming',
    time: '2 ម៉ោងមុន',
    type: 'quiz',
    read: false,
    link: '/student/quizzes'
  },
  {
    id: 3,
    title: '🤖 AI Recommendation: មេរៀនថ្មីសម្រាប់អ្នក',
    desc: 'ផ្អែកលើពិន្ទុរបស់អ្នក សូមរៀនមេរៀន Arrays in C ឥឡូវនេះ',
    time: '5 ម៉ោងមុន',
    type: 'ai',
    read: true,
    link: '/student/ai-path'
  }
])

const unreadNotificationsCount = computed(() => {
  return notifications.value.filter(n => !n.read).length
})

const markAllAsRead = () => {
  notifications.value.forEach(n => n.read = true)
}

const markNotificationRead = (id: number) => {
  const item = notifications.value.find(n => n.id === id)
  if (item) item.read = true
}

// Global Command Search Links for Student
const searchableLinks = computed(() => [
  { name: 'Dashboard (ទិដ្ឋភាពសរុប)', category: 'General', href: '/student/dashboard', iconUrl: '/images/nav/dashboard.svg' },
  { name: 'My Courses (មុខវិជ្ជារបស់ខ្ញុំ)', category: 'Courses', href: '/student/courses', iconUrl: '/images/nav/courses.svg' },
  { name: 'Browse Course Catalog (ស្វែងរកមុខវិជ្ជា)', category: 'Courses', href: '/student/browse', iconUrl: '/images/nav/sub/all-courses.svg' },
  { name: 'Learning Content (មាតិកាសិក្សា)', category: 'Content', href: '/student/content', iconUrl: '/images/nav/content.svg' },
  { name: 'Quizzes & Practice Tests', category: 'Assessment', href: '/student/quizzes', iconUrl: '/images/nav/quiz.svg' },
  { name: 'AI Learning Path (ផ្លូវសិក្សា AI)', category: 'AI Path', href: '/student/ai-path', iconUrl: '/images/nav/ai.svg' },
  { name: 'Progress Tracking (វឌ្ឍនភាព)', category: 'Analytics', href: '/student/progress', iconUrl: '/images/nav/progress.svg' },
  { name: 'My Payments (ABA)', category: 'Finance', href: '/student/payments', iconUrl: '/images/nav/payment.svg' },
  { name: 'My Certificates (វិញ្ញាបនបត្រ)', category: 'Certificates', href: '/student/certificates', iconUrl: '/images/nav/certificate.svg' },
  { name: 'Discussion & Support (ការពិភាក្សា)', category: 'Support', href: '/student/discussions', iconUrl: '/images/nav/discussions.svg' },
  { name: 'Calendar & Deadlines', category: 'Calendar', href: '/student/calendar', iconUrl: '/images/actions/announcement.svg' },
  { name: 'My Profile (ព័ត៌មានផ្ទាល់ខ្លួន)', category: 'Account', href: '/student/profile', iconUrl: '/images/nav/sub/students.svg' },
])

const filteredSearchLinks = computed(() => {
  if (!searchQuery.value.trim()) return searchableLinks.value
  const q = searchQuery.value.toLowerCase()
  return searchableLinks.value.filter(l => l.name.toLowerCase().includes(q) || l.category.toLowerCase().includes(q))
})

// Navigation Structure Definition
interface NavSubItem {
  name: string
  khName: string
  href: string
  iconUrl?: string
  icon?: string
}

interface NavItem {
  key: string
  name: string
  khName: string
  href?: string
  iconUrl?: string
  icon?: string
  children?: NavSubItem[]
}

const expandedModules = ref<Record<string, boolean>>({
  courses: false,
  content: false,
  quizzes: false,
  aiPath: false,
  progress: false,
  payments: false,
  certificates: false,
  discussions: false,
  notificationsModule: false,
  calendar: false,
  profile: false,
})

const studentNav: NavItem[] = [
  {
    key: 'dashboard',
    name: 'Dashboard',
    khName: 'ផ្ទាំងព័ត៌មានដើម',
    href: '/student/dashboard',
    iconUrl: '/images/nav/dashboard.svg',
    icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
  },
  {
    key: 'courses',
    name: 'My Courses',
    khName: 'មុខវិជ្ជារបស់ខ្ញុំ',
    iconUrl: '/images/nav/courses.svg',
    icon: 'M12 14l9-5-9-5-9 5 9 5z',
    children: [
      { name: 'Enrolled Courses', khName: 'មុខវិជ្ជាបានចុះឈ្មោះ', href: '/student/my-courses/enrolled', iconUrl: '/images/nav/sub/all-courses.svg' },
      { name: 'Current Course', khName: 'មុខវិជ្ជាកំពុងរៀន', href: '/student/my-courses/current', iconUrl: '/images/nav/sub/teacher-led.svg' },
      { name: 'Completed Courses', khName: 'មុខវិជ្ជាបានរៀនចប់', href: '/student/my-courses/completed', iconUrl: '/images/nav/sub/roles.svg' },
      { name: 'Browse Catalog', khName: 'ស្វែងរកមុខវិជ្ជាថ្មី', href: '/student/my-courses/browse', iconUrl: '/images/nav/sub/overview.svg' },
      { name: 'Wishlist', khName: 'បញ្ជីប្រាថ្នា Wishlist', href: '/student/my-courses/wishlist', iconUrl: '/images/actions/action-button.svg' },
    ]
  },
  {
    key: 'content',
    name: 'Learning Content',
    khName: 'មាតិកាសិក្សា',
    iconUrl: '/images/nav/content.svg',
    icon: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
    children: [
      { name: 'Videos', khName: 'វីដេអូមេរៀន', href: '/student/learning-content/videos', iconUrl: '/images/nav/sub/teacher-led.svg' },
      { name: 'PDFs', khName: 'ឯកសារ PDF', href: '/student/learning-content/pdfs', iconUrl: '/images/nav/sub/policies.svg' },
      { name: 'Slides', khName: 'ស្លាយ Presentation', href: '/student/learning-content/slides', iconUrl: '/images/nav/sub/self-study.svg' },
      { name: 'Notes & Downloads', khName: 'កំណត់ចំណាំ & Downloads', href: '/student/learning-content/notes', iconUrl: '/images/nav/sub/import-export.svg' },
      { name: 'URL Links / Resources', khName: 'តំណភ្ជាប់ & ធនធាន', href: '/student/learning-content/links', iconUrl: '/images/nav/sub/subjects.svg' },
    ]
  },
  {
    key: 'quizzes',
    name: 'Quiz & Assessment',
    khName: 'ការប្រឡង & Quiz',
    iconUrl: '/images/nav/quiz.svg',
    icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
    children: [
      { name: 'Pre-Test', khName: 'Pre-Test (មុនរៀន)', href: '/student/quizzes/pre-test', iconUrl: '/images/nav/sub/semesters.svg' },
      { name: 'Practice Quiz', khName: 'Practice Quiz (អនុវត្ត)', href: '/student/quizzes/practice', iconUrl: '/images/nav/sub/subjects.svg' },
      { name: 'Post-Test', khName: 'Post-Test (ក្រោយរៀន)', href: '/student/quizzes/post-test', iconUrl: '/images/nav/sub/roles.svg' },
      { name: 'Assignments', khName: 'Assignments (កិច្ចការ)', href: '/student/quizzes/assignments', iconUrl: '/images/nav/sub/teacher-assignments.svg' },
      { name: 'Quiz History', khName: 'ប្រវត្តិប្រឡង', href: '/student/quizzes/history', iconUrl: '/images/nav/sub/history.svg' },
      { name: 'My Scores', khName: 'ពិន្ទុរបស់ខ្ញុំ', href: '/student/quizzes/scores', iconUrl: '/images/nav/analytics.svg' },
    ]
  },
  {
    key: 'aiPath',
    name: 'AI Learning Path',
    khName: 'ផ្លូវសិក្សា AI',
    iconUrl: '/images/nav/ai.svg',
    icon: 'M13 10V3L4 14h7v7l9-11h-7z',
    children: [
      { name: 'Recommended Lesson', khName: 'មេរៀន AI ណែនាំ', href: '/student/ai-path/recommended', iconUrl: '/images/nav/ai.svg' },
      { name: 'Review Lesson', khName: 'មេរៀនត្រូវរំលឹកឡើងវិញ', href: '/student/ai-path/review', iconUrl: '/images/nav/sub/overview.svg' },
      { name: 'Weak Topics', khName: 'ប្រធានបទខ្សោយ (Weak)', href: '/student/ai-path/weak-topics', iconUrl: '/images/nav/sub/failed.svg' },
      { name: 'Next Module', khName: 'មេជំពូកបន្ទាប់', href: '/student/ai-path/next-module', iconUrl: '/images/nav/sub/all-courses.svg' },
    ]
  },
  {
    key: 'progress',
    name: 'Progress Tracking',
    khName: 'តាមដានវឌ្ឍនភាព',
    iconUrl: '/images/nav/progress.svg',
    icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
    children: [
      { name: 'Progress Overview', khName: 'ទិដ្ឋភាពវឌ្ឍនភាព', href: '/student/progress/overview', iconUrl: '/images/nav/progress.svg' },
      { name: 'Learning Time', khName: 'រយៈពេលរៀន', href: '/student/progress/learning-time', iconUrl: '/images/nav/sub/history.svg' },
      { name: 'Weekly Progress', khName: 'វឌ្ឍនភាពសប្តាហ៍', href: '/student/progress/weekly', iconUrl: '/images/nav/analytics.svg' },
      { name: 'Achievements & Badges', khName: 'មេដាយ & សមិទ្ធផល', href: '/student/progress/achievements', iconUrl: '/images/nav/sub/roles.svg' },
    ]
  },
  {
    key: 'payments',
    name: 'Payments (ABA)',
    khName: 'ការបង់ប្រាក់ (ABA)',
    iconUrl: '/images/nav/payment.svg',
    icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    children: [
      { name: 'My Payments', khName: 'ការបង់ប្រាក់របស់ខ្ញុំ', href: '/student/payments/my-payments', iconUrl: '/images/nav/payment.svg' },
      { name: 'Pending Payments', khName: 'ការបង់ប្រាក់រង់ចាំ (Pending)', href: '/student/payments/pending', iconUrl: '/images/actions/payment.svg' },
      { name: 'Payment History', khName: 'ប្រវត្តិទូទាត់ប្រាក់', href: '/student/payments/history', iconUrl: '/images/nav/sub/history.svg' },
      { name: 'Receipts / Invoices', khName: 'វិក្កយបត្រ Receipts', href: '/student/payments/receipts', iconUrl: '/images/nav/sub/policies.svg' },
    ]
  },
  {
    key: 'certificates',
    name: 'Certificates',
    khName: 'វិញ្ញាបនបត្រ',
    iconUrl: '/images/nav/certificate.svg',
    icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
    children: [
      { name: 'My Certificates', khName: 'វិញ្ញាបនបត្ររបស់ខ្ញុំ', href: '/student/certificates/my-certificates', iconUrl: '/images/nav/certificate.svg' },
      { name: 'Download / Share', khName: 'ទាញយក & ចែករំលែក', href: '/student/certificates/download-share', iconUrl: '/images/nav/sub/import-export.svg' },
      { name: 'Verify Certificate', khName: 'ផ្ទៀងផ្ទាត់វិញ្ញាបនបត្រ', href: '/student/certificates/verify', iconUrl: '/images/actions/action-button.svg' },
    ]
  },
  {
    key: 'discussions',
    name: 'Discussion & Support',
    khName: 'ការពិភាក្សា & ជំនួយ',
    iconUrl: '/images/nav/discussions.svg',
    icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
    children: [
      { name: 'Ask Question', khName: 'សួរសំណួរទៅគ្រូ', href: '/student/discussions/ask', iconUrl: '/images/nav/sub/overview.svg' },
      { name: 'My Questions', khName: 'សំណួររបស់ខ្ញុំ', href: '/student/discussions/my-questions', iconUrl: '/images/nav/discussions.svg' },
      { name: 'Course Discussions', khName: 'ការពិភាក្សាប្រចាំមុខវិជ្ជា', href: '/student/discussions/course-discussions', iconUrl: '/images/nav/sub/subjects.svg' },
      { name: 'Course FAQ', khName: 'សំណួរញឹកញាប់ FAQ', href: '/student/discussions/faq', iconUrl: '/images/nav/sub/policies.svg' },
      { name: 'Support Ticket', khName: 'ផ្ញើ Support Ticket', href: '/student/discussions/tickets', iconUrl: '/images/actions/action-button.svg' },
    ]
  },
  {
    key: 'notificationsModule',
    name: 'Notifications',
    khName: 'ការជូនដំណឹង',
    iconUrl: '/images/nav/notification.svg',
    icon: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
    children: [
      { name: 'Announcements', khName: 'សេចក្តីប្រកាស', href: '/student/notifications/announcements', iconUrl: '/images/actions/announcement.svg' },
      { name: 'AI Alerts', khName: 'ការដាស់តឿនពី AI', href: '/student/notifications/ai-alerts', iconUrl: '/images/nav/ai.svg' },
      { name: 'Teacher Feedback', khName: 'មតិយោបល់ពីគ្រូ', href: '/student/notifications/feedback', iconUrl: '/images/nav/sub/students.svg' },
      { name: 'Payment Alerts', khName: 'ការជូនដំណឹងបង់ប្រាក់', href: '/student/notifications/payment-alerts', iconUrl: '/images/nav/payment.svg' },
    ]
  },
  {
    key: 'calendar',
    name: 'Calendar',
    khName: 'កាលវិភាគរៀន',
    iconUrl: '/images/actions/announcement.svg',
    icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    children: [
      { name: 'Live Class Schedule', khName: 'កាលវិភាគ Live Class', href: '/student/calendar/live-class', iconUrl: '/images/nav/sub/history.svg' },
      { name: 'Upcoming Quiz', khName: 'កាលបរិច្ឆេទ Quiz', href: '/student/calendar/upcoming-quiz', iconUrl: '/images/nav/quiz.svg' },
      { name: 'Assignment Due', khName: 'កាលកំណត់ Assignment', href: '/student/calendar/assignment-due', iconUrl: '/images/nav/sub/policies.svg' },
    ]
  },
  {
    key: 'profile',
    name: 'My Profile',
    khName: 'ព័ត៌មានផ្ទាល់ខ្លួន',
    iconUrl: '/images/nav/sub/students.svg',
    icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    children: [
      { name: 'Personal Information', khName: 'ព័ត៌មានផ្ទាល់ខ្លួន', href: '/student/profile?tab=personal', iconUrl: '/images/nav/sub/students.svg' },
      { name: 'My Major & Faculty', khName: 'ជំនាញ & មហាវិទ្យាល័យ', href: '/student/profile?tab=major', iconUrl: '/images/nav/sub/semesters.svg' },
      { name: 'Change Password', khName: 'ប្តូរកូដសម្ងាត់', href: '/student/profile?tab=password', iconUrl: '/images/nav/sub/policies.svg' },
      { name: 'Learning History', khName: 'ប្រវត្តិសិក្សា', href: '/student/profile?tab=history', iconUrl: '/images/nav/sub/history.svg' },
      { name: 'Notification Settings', khName: 'កំណត់ការជូនដំណឹង', href: '/student/profile?tab=settings', iconUrl: '/images/nav/settings.svg' },
    ]
  }
]

const isSubActive = (subHref: string) => {
  const currentUrl = page.url
  if (subHref.includes('?')) {
    const [path, query] = subHref.split('?')
    if (!currentUrl.startsWith(path)) return false
    return currentUrl.includes(query)
  }
  return currentUrl === subHref || (currentUrl.startsWith(subHref.split('?')[0]) && !subHref.includes('?'))
}

const isChildActive = (children?: NavSubItem[]) => {
  if (!children) return false
  return children.some(child => isSubActive(child.href))
}

const toggleModule = (key: string) => {
  expandedModules.value[key] = !expandedModules.value[key]
}

watch(
  () => page.url,
  () => {
    studentNav.forEach(item => {
      if (item.key && item.children && isChildActive(item.children)) {
        expandedModules.value[item.key] = true
      }
    })
  },
  { immediate: true }
)

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
  window.addEventListener('online', updateOnlineStatus)
  window.addEventListener('offline', updateOnlineStatus)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
  window.removeEventListener('online', updateOnlineStatus)
  window.removeEventListener('offline', updateOnlineStatus)
})

const logout = () => {
  router.post('/logout')
}

const onIconError = (e: Event) => {
  const target = e.target as HTMLImageElement
  if (target) {
    target.style.display = 'none'
    const parent = target.parentElement
    if (parent) {
      const fallbackSvg = parent.querySelector('svg')
      if (fallbackSvg) {
        fallbackSvg.classList.remove('hidden')
        fallbackSvg.style.display = 'block'
      }
    }
  }
}
</script>

<template>
  <Head :title="pageTitle" />
  <div class="min-h-screen bg-slate-900 text-slate-200 selection:bg-indigo-500/30">
    <!-- Desktop Sidebar (Fixed Positioning exactly like Admin & Teacher layouts) -->
    <aside :class="[isSidebarCollapsed ? 'w-20 overflow-visible' : 'w-72', 'fixed inset-y-0 left-0 z-50 hidden flex-col bg-slate-900/90 backdrop-blur-xl border-r border-slate-800 lg:flex transition-all duration-300']">
      <!-- Sidebar Header -->
      <div
        :class="[
          isSidebarCollapsed ? 'justify-center px-2' : 'justify-between px-4',
          'relative flex h-16 shrink-0 items-center border-b border-slate-800 transition-all duration-300 group/sidebar-header'
        ]"
      >
        <!-- Logo & Title Container -->
        <div
          @click="isSidebarCollapsed && toggleSidebarCollapse()"
          :class="[
            isSidebarCollapsed ? 'justify-center cursor-pointer' : '',
            'flex items-center gap-3 min-w-0 shrink-0 transition-all'
          ]"
        >
          <img
            :src="logoUrl"
            alt="E-LMS Logo"
            :class="[
              isSidebarCollapsed ? 'hover:scale-110' : '',
              'w-9 h-9 min-w-[36px] min-h-[36px] max-w-[36px] max-h-[36px] aspect-square rounded-full object-cover shadow-md shadow-indigo-500/20 ring-2 ring-indigo-500/30 shrink-0 transition-all duration-300'
            ]"
          />
          <div v-show="!isSidebarCollapsed" class="transition-opacity duration-200 min-w-0">
            <h1 class="text-base font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 via-cyan-300 to-sky-400 tracking-tight whitespace-nowrap">
              E-LMS Student
            </h1>
            <p class="text-[10px] text-slate-400 font-medium tracking-wide uppercase whitespace-nowrap">Student Panel</p>
          </div>
        </div>

        <!-- Collapse / Expand Toggle Button -->
        <button
          @click="toggleSidebarCollapse"
          type="button"
          :title="isSidebarCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
          :class="[
            isSidebarCollapsed
              ? 'absolute -right-3 top-1/2 -translate-y-1/2 bg-slate-800 text-indigo-400 border border-slate-700 shadow-md rounded-full p-1 hover:scale-110 hover:bg-slate-700 z-10'
              : 'p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 shrink-0',
            'transition-all duration-200 focus:outline-none'
          ]"
        >
          <svg
            :class="[isSidebarCollapsed ? 'rotate-180 w-3.5 h-3.5' : 'w-5 h-5', 'transition-transform duration-300']"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
          </svg>
        </button>
      </div>

      <!-- Navigation Tree -->
      <nav
        :class="[
          isSidebarCollapsed ? 'px-0 overflow-visible' : 'px-3 custom-scrollbar overflow-y-auto',
          'flex flex-1 flex-col py-4'
        ]"
      >
        <ul role="list" class="space-y-1 w-full">
          <li
            v-for="item in studentNav"
            :key="item.key"
            :class="isSidebarCollapsed ? 'relative group/flyout flex justify-center w-full' : 'relative'"
          >
            <!-- Direct Link (No Children) -->
            <Link
              v-if="!item.children || item.children.length === 0"
              :href="item.href!"
              :title="isSidebarCollapsed ? (currentLang === 'km' ? item.khName : item.name) : undefined"
              :class="[
                $page.url.startsWith(item.href!) 
                  ? 'bg-indigo-500/15 text-indigo-300 border border-indigo-500/30 font-semibold shadow-sm' 
                  : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border border-transparent font-medium',
                isSidebarCollapsed ? 'justify-center px-0 w-10 h-10 mx-auto' : 'px-3 w-full gap-x-3',
                'group flex items-center rounded-xl py-2.5 text-xs transition-all duration-200'
              ]"
            >
              <div class="relative flex items-center justify-center shrink-0">
                <img 
                  v-if="item.iconUrl"
                  :src="item.iconUrl" 
                  :alt="item.name"
                  @error="onIconError"
                  class="w-5 h-5 object-contain shrink-0 filter drop-shadow-sm transition-transform duration-200 group-hover:scale-110"
                />
                <svg 
                  :class="[
                    $page.url.startsWith(item.href!) ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300',
                    item.iconUrl ? 'hidden' : '',
                    'h-5 w-5 shrink-0 transition-colors'
                  ]"
                  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon || 'M4 6h16M4 12h16M4 18h16'" />
                </svg>
              </div>
              <span v-show="!isSidebarCollapsed" class="flex-1 truncate">
                {{ currentLang === 'km' ? item.khName : item.name }}
              </span>
            </Link>

            <!-- Collapsible Module with Submenu -->
            <div v-else class="space-y-1 w-full flex flex-col items-center">
              <button
                @click="toggleModule(item.key!)"
                type="button"
                :title="isSidebarCollapsed ? (currentLang === 'km' ? item.khName : item.name) : undefined"
                :class="[
                  isChildActive(item.children) 
                    ? 'bg-indigo-500/10 text-indigo-300 border border-indigo-500/20' 
                    : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border border-transparent',
                  isSidebarCollapsed ? 'justify-center px-0 w-10 h-10 mx-auto' : 'px-3 w-full justify-between',
                  'group flex items-center rounded-xl py-2.5 text-xs font-medium transition-all duration-200'
                ]"
              >
                <div :class="[isSidebarCollapsed ? 'justify-center w-full' : '', 'flex items-center gap-x-3 truncate']">
                  <div class="relative flex items-center justify-center shrink-0">
                    <img 
                      v-if="item.iconUrl"
                      :src="item.iconUrl" 
                      :alt="item.name"
                      @error="onIconError"
                      class="w-5 h-5 object-contain shrink-0 filter drop-shadow-sm transition-transform duration-200 group-hover:scale-110"
                    />
                    <svg 
                      :class="[
                        isChildActive(item.children) ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300',
                        item.iconUrl ? 'hidden' : '',
                        'h-5 w-5 shrink-0 transition-colors'
                      ]"
                      fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon || 'M4 6h16M4 12h16M4 18h16'" />
                    </svg>
                  </div>
                  <span v-show="!isSidebarCollapsed" class="truncate">
                    {{ currentLang === 'km' ? item.khName : item.name }}
                  </span>
                </div>

                <div v-show="!isSidebarCollapsed" class="flex items-center gap-1.5">
                  <svg
                    :class="[
                      expandedModules[item.key!] ? 'rotate-180 text-indigo-400' : 'text-slate-500 group-hover:text-slate-300',
                      'w-4 h-4 transition-transform duration-200 shrink-0 ml-1'
                    ]"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </button>

              <!-- Submenu Items Tree -->
              <div
                v-show="!isSidebarCollapsed && expandedModules[item.key!]"
                class="relative ml-6 pl-4 space-y-1 my-1.5 transition-all duration-300 border-l border-slate-800"
              >
                <div
                  v-for="sub in item.children"
                  :key="sub.href"
                  class="relative"
                >
                  <Link
                    :href="sub.href"
                    :class="[
                      isSubActive(sub.href)
                        ? 'bg-indigo-500/20 text-indigo-300 font-semibold border border-indigo-500/30'
                        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 border border-transparent',
                      'flex items-center gap-x-2.5 rounded-lg px-2.5 py-1.5 text-xs transition-all duration-200'
                    ]"
                  >
                    <img
                      v-if="sub.iconUrl"
                      :src="sub.iconUrl"
                      :alt="sub.name"
                      @error="onIconError"
                      class="w-4 h-4 object-contain shrink-0 filter drop-shadow-xs"
                    />
                    <span class="truncate">{{ currentLang === 'km' ? sub.khName : sub.name }}</span>
                  </Link>
                </div>
              </div>
            </div>

            <!-- Flyout Popover for Collapsed Sidebar Mode (Has Children) -->
            <div
              v-if="isSidebarCollapsed && item.children && item.children.length > 0"
              class="absolute left-full top-0 ml-3.5 w-64 opacity-0 pointer-events-none group-hover/flyout:opacity-100 group-hover/flyout:pointer-events-auto transition-all duration-200 ease-out translate-x-1 group-hover/flyout:translate-x-0 z-50"
            >
              <div class="relative bg-slate-900/95 backdrop-blur-xl border border-slate-700/80 rounded-2xl p-3 shadow-2xl ring-1 ring-slate-800/80">
                <div class="absolute -left-1.5 top-3.5 w-3 h-3 bg-slate-900 border-l border-b border-slate-700/80 rotate-45 z-10 pointer-events-none"></div>

                <div class="relative z-20 flex items-center justify-between px-2 py-1.5 mb-2 border-b border-slate-800 pb-2">
                  <div class="flex items-center gap-2 min-w-0">
                    <img v-if="item.iconUrl" :src="item.iconUrl" :alt="item.name" class="w-4 h-4 object-contain shrink-0" />
                    <span class="text-xs font-bold text-white truncate">{{ currentLang === 'km' ? item.khName : item.name }}</span>
                  </div>
                </div>

                <div class="relative z-20 space-y-1 max-h-[70vh] overflow-y-auto custom-scrollbar pr-1">
                  <Link
                    v-for="sub in item.children"
                    :key="sub.href"
                    :href="sub.href"
                    :class="[
                      isSubActive(sub.href)
                        ? 'bg-indigo-500/15 text-indigo-300 font-semibold border border-indigo-500/30'
                        : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/70 border border-transparent',
                      'flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs transition-all duration-150'
                    ]"
                  >
                    <img v-if="sub.iconUrl" :src="sub.iconUrl" :alt="sub.name" class="w-4 h-4 object-contain shrink-0" />
                    <span class="truncate">{{ currentLang === 'km' ? sub.khName : sub.name }}</span>
                  </Link>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </nav>

      <!-- Sidebar Footer User Card -->
      <div :class="[isSidebarCollapsed ? 'px-0 py-3' : 'p-3.5', 'mt-auto border-t border-slate-800 bg-slate-950/60']">
        <div :class="[isSidebarCollapsed ? 'flex-col justify-center items-center gap-2.5 w-full' : 'gap-3', 'flex items-center']">
          <div class="relative shrink-0">
            <img
              v-if="user.avatar"
              :src="user.avatar"
              class="w-9 h-9 rounded-full object-cover border border-indigo-500/30 shadow-md"
            />
            <div
              v-else
              class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-bold flex items-center justify-center text-sm shadow"
            >
              {{ user.name ? user.name.charAt(0) : 'S' }}
            </div>
            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full ring-2 ring-slate-900"></span>
          </div>

          <div v-show="!isSidebarCollapsed" class="flex-1 min-w-0">
            <p class="font-bold text-slate-100 text-xs truncate">{{ user.name || 'Chan Dara' }}</p>
            <p class="text-[10px] text-slate-400 truncate">{{ user.major || 'IT & Networking' }} • Student</p>
          </div>

          <button
            v-show="!isSidebarCollapsed"
            @click="logout"
            title="Log Out"
            class="p-2 text-slate-400 hover:text-red-400 hover:bg-slate-800 rounded-lg transition-colors shrink-0"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </button>
        </div>
      </div>
    </aside>

    <!-- Mobile Drawer Sidebar (Sliding Drawer on Mobile) -->
    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 bg-slate-950/80 z-50 lg:hidden backdrop-blur-sm transition-opacity"
    ></div>

    <aside
      :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        'fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 border-r border-slate-800 flex flex-col lg:hidden transition-transform duration-300 ease-in-out shadow-2xl'
      ]"
    >
      <div class="h-16 px-4 flex items-center justify-between border-b border-slate-800">
        <div class="flex items-center gap-3">
          <img :src="logoUrl" alt="E-LMS Logo" class="w-8 h-8 rounded-full object-cover ring-2 ring-indigo-500/30" />
          <span class="font-bold text-sm text-white">E-LMS Student</span>
        </div>
        <button @click="sidebarOpen = false" class="p-1 text-slate-400 hover:text-white">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto custom-scrollbar">
        <template v-for="item in studentNav" :key="item.key">
          <Link
            v-if="!item.children || item.children.length === 0"
            :href="item.href!"
            @click="sidebarOpen = false"
            :class="[
              $page.url.startsWith(item.href!) ? 'bg-indigo-500/15 text-indigo-300 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60',
              'flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs'
            ]"
          >
            <img v-if="item.iconUrl" :src="item.iconUrl" class="w-5 h-5 object-contain" />
            <span>{{ currentLang === 'km' ? item.khName : item.name }}</span>
          </Link>
          <div v-else class="space-y-1">
            <button
              @click="toggleModule(item.key!)"
              class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-medium"
            >
              <div class="flex items-center gap-3">
                <img v-if="item.iconUrl" :src="item.iconUrl" class="w-5 h-5 object-contain" />
                <span>{{ currentLang === 'km' ? item.khName : item.name }}</span>
              </div>
              <svg :class="[expandedModules[item.key!] ? 'rotate-180' : '', 'w-4 h-4 transition-transform']" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div v-show="expandedModules[item.key!]" class="pl-8 space-y-1 border-l border-slate-800 ml-4">
              <Link
                v-for="sub in item.children"
                :key="sub.href"
                :href="sub.href"
                @click="sidebarOpen = false"
                :class="[isSubActive(sub.href) ? 'text-indigo-300 font-semibold' : 'text-slate-400 hover:text-slate-200', 'block py-1.5 text-xs']"
              >
                {{ currentLang === 'km' ? sub.khName : sub.name }}
              </Link>
            </div>
          </div>
        </template>
      </nav>
    </aside>

    <!-- STICKY TOP NAVBAR (Dynamically padded to account for fixed sidebar) -->
    <header :class="[isSidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72', 'sticky top-0 z-40 bg-slate-900/90 backdrop-blur-xl border-b border-slate-800/80 transition-all duration-300']">
      <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8 gap-4">
        
        <!-- Left Side: Mobile Menu Toggle, Breadcrumb & Global Search Bar -->
        <div class="flex items-center gap-3.5 min-w-0">
          <button
            @click="sidebarOpen = !sidebarOpen"
            type="button"
            class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg focus:outline-none transition-colors cursor-pointer lg:hidden"
            title="Toggle Mobile Navigation"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          <!-- Breadcrumb -->
          <div class="hidden sm:flex items-center gap-2 text-xs font-medium truncate">
            <span class="text-slate-400 font-normal">Student</span>
            <svg class="w-3.5 h-3.5 text-slate-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="px-2.5 py-1 rounded-xl bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-semibold truncate shadow-xs">
              {{ currentBreadcrumb[1] }}
            </span>
          </div>

          <!-- Glassmorphic Search Bar -->
          <div class="relative hidden md:block">
            <button
              @click="toggleDropdown('search')"
              type="button"
              class="flex items-center gap-2.5 px-3.5 py-1.5 rounded-xl bg-slate-800/60 hover:bg-slate-800 text-slate-400 hover:text-slate-200 border border-slate-700/60 hover:border-indigo-500/40 text-xs transition-all w-56 lg:w-72 justify-between shadow-inner group"
            >
              <div class="flex items-center gap-2 truncate min-w-0 flex-1">
                <svg class="w-3.5 h-3.5 text-indigo-400 shrink-0 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span class="truncate text-slate-400 group-hover:text-slate-300">ស្វែងរក... (Global Search)</span>
              </div>
              <kbd class="hidden lg:inline-flex items-center shrink-0 whitespace-nowrap px-1.5 py-0.5 text-[10px] font-mono font-semibold text-slate-400 bg-slate-900/80 border border-slate-700/60 rounded shadow-xs leading-none">ctrl k</kbd>
            </button>
          </div>
        </div>

        <!-- Right Side: Quick Action Button, Online Status, Language Switcher, Fullscreen, Notifications, User Profile -->
        <div class="flex items-center gap-1.5 sm:gap-2">
          <!-- Mobile Search Trigger -->
          <button
            @click="toggleDropdown('search')"
            type="button"
            class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg md:hidden focus:outline-none transition-colors"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </button>

          <!-- Quick Action Dropdown (+ រៀនមេរៀន ˅) -->
          <div 
            class="relative" 
            @mouseenter="isQuickActionOpen = true" 
            @mouseleave="isQuickActionOpen = false"
          >
            <button
              @click="isQuickActionOpen = !isQuickActionOpen"
              type="button"
              class="h-8 px-2.5 bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 hover:border-slate-600 rounded-lg transition-all flex items-center gap-2 cursor-pointer group select-none focus:outline-none"
            >
              <img :src="actionBtnIcon" alt="Actions" class="w-4 h-4 shrink-0 group-hover:scale-105 transition-transform" />
              <span class="hidden sm:inline text-xs font-semibold text-slate-300 group-hover:text-white font-sans tracking-wide">+ ផ្លូវកាត់</span>
              <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-200 transition-transform duration-200" :class="isQuickActionOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <Transition
              enter-active-class="transition duration-150 ease-out"
              enter-from-class="opacity-0 -translate-y-1"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition duration-100 ease-in"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 -translate-y-1"
            >
              <div
                v-if="isQuickActionOpen"
                class="absolute right-0 mt-1.5 w-60 rounded-xl bg-slate-800/95 backdrop-blur-xl border border-slate-700/80 shadow-2xl py-1.5 z-50 overflow-hidden"
              >
                <div class="px-3.5 py-1.5 border-b border-slate-700/60 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                  ផ្លូវកាត់សិក្សា (Quick Actions)
                </div>
                <Link
                  v-for="act in quickActions"
                  :key="act.name"
                  :href="act.href"
                  @click="isQuickActionOpen = false"
                  class="flex items-center gap-2.5 px-3.5 py-2 text-xs text-slate-300 hover:text-white hover:bg-slate-700/60 transition-colors group/item"
                >
                  <img :src="act.iconUrl" :alt="act.name" class="w-4 h-4 shrink-0 group-hover/item:scale-110 transition-transform" />
                  <span class="font-medium">{{ act.name }}</span>
                </Link>
              </div>
            </Transition>
          </div>

          <!-- Online Status Badge Pill (🟢 Online ˅) -->
          <div 
            class="relative" 
            @mouseenter="isStatusOpen = true" 
            @mouseleave="isStatusOpen = false"
          >
            <button
              @click="toggleDropdown('status')"
              type="button"
              class="h-8 px-2.5 bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 hover:border-slate-600 rounded-lg transition-all flex items-center gap-2 cursor-pointer group select-none focus:outline-none"
            >
              <div class="relative flex items-center justify-center shrink-0">
                <img 
                  :src="isOnline ? onlineIconUrl : offlineIconUrl" 
                  :alt="isOnline ? 'Online' : 'Offline'"
                  class="w-4 h-4 object-contain shrink-0 group-hover:scale-110 transition-transform filter drop-shadow-xs" 
                />
                <span 
                  :class="[isOnline ? 'bg-emerald-500 shadow-emerald-500/50' : 'bg-rose-500 shadow-rose-500/50']"
                  class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full ring-2 ring-slate-900 shadow-xs animate-pulse"
                ></span>
              </div>
              <span 
                :class="[isOnline ? 'text-emerald-400' : 'text-rose-400']"
                class="hidden sm:inline text-xs font-semibold font-sans tracking-wide"
              >
                {{ isOnline ? 'Online' : 'Offline' }}
              </span>
              <svg 
                :class="[isStatusOpen ? 'rotate-180 text-slate-200' : 'text-slate-400']" 
                class="w-3.5 h-3.5 transition-transform duration-200" 
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>

            <!-- Status Dropdown Menu -->
            <Transition
              enter-active-class="transition duration-200 ease-out"
              enter-from-class="transform opacity-0 scale-95 -translate-y-1"
              enter-to-class="transform opacity-100 scale-100 translate-y-0"
              leave-active-class="transition duration-150 ease-in"
              leave-from-class="transform opacity-100 scale-100 translate-y-0"
              leave-to-class="transform opacity-0 scale-95 -translate-y-1"
            >
              <div
                v-if="isStatusOpen"
                class="absolute right-0 mt-1.5 w-56 rounded-xl bg-slate-800/95 backdrop-blur-xl border border-slate-700/80 shadow-2xl py-1.5 z-50 overflow-hidden"
              >
                <div class="px-3.5 py-1.5 border-b border-slate-700/60 flex items-center justify-between">
                  <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">ស្ថានភាពប្រព័ន្ធ (Status)</span>
                  <span 
                    :class="[isOnline ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border-rose-500/30']"
                    class="px-2 py-0.5 text-[9px] font-bold rounded-full border"
                  >
                    {{ isOnline ? 'Connected' : 'Disconnected' }}
                  </span>
                </div>

                <div class="p-1 space-y-1 text-xs">
                  <button
                    @click="setStatusMode(true)"
                    :class="[isOnline ? 'bg-emerald-500/15 text-emerald-300 font-semibold border-emerald-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-700/60 border-transparent']"
                    class="w-full flex items-center justify-between px-3 py-2 text-xs rounded-lg border transition-all cursor-pointer text-left"
                  >
                    <div class="flex items-center gap-2.5">
                      <img :src="onlineIconUrl" alt="Online" class="w-4 h-4 object-contain" />
                      <div>
                        <p class="font-medium text-xs text-white">អនឡាញ (Online)</p>
                        <p class="text-[10px] text-slate-400">ភ្ជាប់ប្រព័ន្ធធម្មតា</p>
                      </div>
                    </div>
                  </button>

                  <button
                    @click="setStatusMode(false)"
                    :class="[!isOnline ? 'bg-rose-500/15 text-rose-300 font-semibold border-rose-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-700/60 border-transparent']"
                    class="w-full flex items-center justify-between px-3 py-2 text-xs rounded-lg border transition-all cursor-pointer text-left"
                  >
                    <div class="flex items-center gap-2.5">
                      <img :src="offlineIconUrl" alt="Offline" class="w-4 h-4 object-contain" />
                      <div>
                        <p class="font-medium text-xs text-white">អូហ្វឡាញ (Offline)</p>
                        <p class="text-[10px] text-slate-400">ដាច់អ៊ីនធឺណិត / Offline</p>
                      </div>
                    </div>
                  </button>
                </div>
              </div>
            </Transition>
          </div>

          <!-- Language Switcher Pill (🇰🇭 KM ˅) -->
          <div 
            class="relative" 
            @mouseenter="isLangOpen = true" 
            @mouseleave="isLangOpen = false"
          >
            <button
              @click="isLangOpen = !isLangOpen"
              type="button"
              class="h-8 px-2.5 bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 hover:border-slate-600 rounded-lg transition-all flex items-center gap-2 cursor-pointer group select-none focus:outline-none"
            >
              <img 
                :src="languages.find(l => l.code === currentLang)?.flagUrl || '/images/flags/km.svg'" 
                :alt="currentLang"
                class="w-4 h-4 rounded-full object-cover shrink-0" 
              />
              <span class="uppercase text-xs font-semibold text-slate-300 group-hover:text-white font-sans tracking-wide">
                {{ currentLang === 'km' ? 'KM' : 'EN' }}
              </span>
              <svg 
                :class="[isLangOpen ? 'rotate-180 text-slate-200' : 'text-slate-400']" 
                class="w-3.5 h-3.5 transition-transform duration-200" 
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>

            <!-- Language Dropdown Menu -->
            <Transition
              enter-active-class="transition duration-200 ease-out"
              enter-from-class="transform opacity-0 scale-95 -translate-y-1"
              enter-to-class="transform opacity-100 scale-100 translate-y-0"
              leave-active-class="transition duration-150 ease-in"
              leave-from-class="transform opacity-100 scale-100 translate-y-0"
              leave-to-class="transform opacity-0 scale-95 -translate-y-1"
            >
              <div
                v-if="isLangOpen"
                class="absolute right-0 mt-1.5 w-40 rounded-xl bg-slate-800/95 backdrop-blur-xl border border-slate-700/80 shadow-2xl py-1.5 z-50 overflow-hidden"
              >
                <button
                  v-for="lang in languages"
                  :key="lang.code"
                  @click="selectLanguage(lang.code)"
                  :class="[
                    currentLang === lang.code ? 'bg-indigo-600/20 text-indigo-300 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-700/60'
                  ]"
                  class="w-full flex items-center justify-between px-3.5 py-2 text-xs transition-colors rounded-none cursor-pointer focus:outline-none"
                >
                  <span class="flex items-center gap-2.5">
                    <img :src="lang.flagUrl" :alt="lang.name" class="w-4 h-4 rounded-full object-cover shrink-0" />
                    <span>{{ lang.name }}</span>
                  </span>
                  <svg v-if="currentLang === lang.code" class="w-3.5 h-3.5 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                  </svg>
                </button>
              </div>
            </Transition>
          </div>

          <!-- Fullscreen Button (⛶) -->
          <button
            @click="toggleFullscreen"
            type="button"
            class="p-2 text-slate-400 hover:text-white hover:bg-slate-800 border border-transparent hover:border-slate-700/60 rounded-xl transition-all focus:outline-none"
            :title="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'"
          >
            <svg v-if="!isFullscreen" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
            </svg>
            <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 9L4 4m0 0l5 0m-5 0l0 5m11 0l5-5m0 0l-5 0m5 0l0 5m-5 11l5 5m0 0l-5 0m5 0l0-5m-11 0l-5 5m0 0l5 0m-5 0l0-5" />
            </svg>
          </button>

          <!-- Notifications Bell Dropdown (🔔) -->
          <div class="relative">
            <button
              @click="toggleDropdown('notification')"
              type="button"
              class="relative p-2 text-slate-400 hover:text-white hover:bg-slate-800 border border-transparent hover:border-slate-700/60 rounded-xl transition-all focus:outline-none"
              title="Notifications"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <span v-if="unreadNotificationsCount > 0" class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-slate-900 animate-pulse"></span>
            </button>

            <!-- Notifications Dropdown -->
            <div
              v-if="isNotificationOpen"
              class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-slate-800 border border-slate-700/80 shadow-2xl z-50 overflow-hidden"
            >
              <div class="px-4 py-3 border-b border-slate-700/80 flex items-center justify-between bg-slate-800/80">
                <div class="flex items-center gap-2">
                  <h3 class="text-xs font-bold text-white">ការជូនដំណឹង (Notifications)</h3>
                  <span v-if="unreadNotificationsCount > 0" class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-500/20 text-indigo-300">
                    {{ unreadNotificationsCount }} ថ្មី
                  </span>
                </div>
                <button
                  v-if="unreadNotificationsCount > 0"
                  @click="markAllAsRead"
                  class="text-[11px] text-indigo-400 hover:text-indigo-300 transition-colors font-medium"
                >
                  អានទាំងអស់
                </button>
              </div>

              <div class="max-h-80 overflow-y-auto custom-scrollbar divide-y divide-slate-700/40">
                <div
                  v-for="notif in notifications"
                  :key="notif.id"
                  @click="markNotificationRead(notif.id)"
                  :class="[notif.read ? 'bg-slate-800/30 opacity-70' : 'bg-slate-800 hover:bg-slate-700/50', 'p-3.5 transition-colors cursor-pointer block']"
                >
                  <Link :href="notif.link" @click="isNotificationOpen = false">
                    <div class="flex items-start gap-3">
                      <div :class="[
                        notif.type === 'payment' ? 'bg-emerald-500/20 text-emerald-400' :
                        notif.type === 'quiz' ? 'bg-amber-500/20 text-amber-400' : 'bg-indigo-500/20 text-indigo-400',
                        'p-2 rounded-xl shrink-0 mt-0.5'
                      ]">
                        <svg v-if="notif.type === 'payment'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v-2m0 0c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg v-else-if="notif.type === 'quiz'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                      </div>
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                          <p class="text-xs font-semibold text-slate-200 truncate">{{ notif.title }}</p>
                          <span class="text-[10px] text-slate-400 shrink-0 ml-1">{{ notif.time }}</span>
                        </div>
                        <p class="text-[11px] text-slate-400 line-clamp-2 mt-0.5">{{ notif.desc }}</p>
                      </div>
                    </div>
                  </Link>
                </div>
              </div>

              <div class="p-2 border-t border-slate-700/80 bg-slate-900/60 text-center">
                <Link
                  href="/student/notifications"
                  @click="isNotificationOpen = false"
                  class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors"
                >
                  មើលការជូនដំណឹងទាំងអស់ →
                </Link>
              </div>
            </div>
          </div>

          <!-- User Profile Dropdown Avatar (👤 Chan Dara ˅) -->
          <div class="relative ml-1">
            <button
              @click="toggleDropdown('profile')"
              type="button"
              class="flex items-center gap-2 p-1 rounded-xl hover:bg-slate-800/80 border border-transparent hover:border-slate-700/60 transition-all focus:outline-none group"
            >
              <div class="relative shrink-0">
                <img
                  :src="user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name || 'Chan Dara')}&background=6366f1&color=fff`"
                  alt="Student Profile"
                  class="w-7 h-7 rounded-full border border-slate-700 object-cover shadow-xs group-hover:border-indigo-500/50 transition-colors"
                />
                <span class="absolute bottom-0 right-0 w-2 h-2 bg-emerald-500 rounded-full ring-2 ring-slate-900"></span>
              </div>
              <span class="hidden md:inline text-xs font-semibold text-slate-200 group-hover:text-indigo-300 transition-colors truncate max-w-[130px]">
                {{ user.name || 'Chan Dara' }}
              </span>
              <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-200 transition-transform" :class="isProfileOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <!-- Profile Dropdown Menu Popup -->
            <div
              v-if="isProfileOpen"
              class="absolute right-0 mt-2 w-64 rounded-2xl bg-slate-800 border border-slate-700/80 shadow-2xl py-2 z-50 animate-in fade-in duration-150"
            >
              <div class="px-4 py-3 border-b border-slate-700/80 bg-gradient-to-r from-indigo-900/30 to-purple-900/30">
                <div class="flex items-center gap-3">
                  <img
                    :src="user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name || 'Chan Dara')}&background=6366f1&color=fff`"
                    alt="Student Avatar"
                    class="w-10 h-10 rounded-full border border-slate-700 object-cover shadow-md"
                  />
                  <div class="flex-1 min-w-0">
                    <h4 class="text-xs font-bold text-white truncate">{{ user.name || 'Chan Dara' }}</h4>
                    <p class="text-[11px] text-slate-400 truncate">{{ user.email || 'dara@example.com' }}</p>
                    <span class="inline-block mt-1 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                      {{ user.major || 'IT & Networking' }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="py-1 border-b border-slate-700/60 text-xs">
                <Link
                  href="/student/profile"
                  @click="isProfileOpen = false"
                  class="flex items-center gap-2.5 px-4 py-2 text-slate-300 hover:text-white hover:bg-slate-700/50 transition-colors"
                >
                  <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                  <span>My Profile (ព័ត៌មានផ្ទាល់ខ្លួន)</span>
                </Link>

                <Link
                  href="/student/payments"
                  @click="isProfileOpen = false"
                  class="flex items-center gap-2.5 px-4 py-2 text-slate-300 hover:text-white hover:bg-slate-700/50 transition-colors"
                >
                  <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                  <span>My Payments & ABA (ការបង់ប្រាក់)</span>
                </Link>

                <Link
                  href="/student/certificates"
                  @click="isProfileOpen = false"
                  class="flex items-center gap-2.5 px-4 py-2 text-slate-300 hover:text-white hover:bg-slate-700/50 transition-colors"
                >
                  <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                  <span>My Certificates (វិញ្ញាបនបត្រ)</span>
                </Link>
              </div>

              <div class="pt-1">
                <button
                  @click="logout"
                  class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-red-400 hover:bg-red-900/30 transition-colors font-bold text-left"
                >
                  <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                  <span>Log Out (ចាកចេញ)</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Global Command Search Modal (Ctrl + K Palette) -->
    <div
      v-if="isSearchOpen"
      class="fixed inset-0 z-50 flex items-start justify-center pt-16 px-4 bg-slate-950/80 backdrop-blur-md"
      @click.self="isSearchOpen = false"
    >
      <div class="bg-slate-900 rounded-2xl max-w-xl w-full border border-slate-700 shadow-2xl overflow-hidden transform transition-all">
        <div class="p-4 border-b border-slate-800 flex items-center gap-3">
          <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="ស្វែងរកក្នុងប្រព័ន្ធ Student Panel..."
            class="w-full bg-transparent border-none text-slate-100 focus:outline-none focus:ring-0 text-sm font-medium placeholder-slate-500"
            autoFocus
          />
          <kbd class="px-2 py-0.5 rounded bg-slate-800 text-slate-400 text-[10px] font-mono border border-slate-700">ESC</kbd>
        </div>

        <div class="max-h-80 overflow-y-auto p-2 space-y-1">
          <Link
            v-for="link in filteredSearchLinks"
            :key="link.name"
            :href="link.href"
            @click="isSearchOpen = false"
            class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-800/80 transition-colors text-xs group"
          >
            <div class="flex items-center gap-3">
              <img v-if="link.iconUrl" :src="link.iconUrl" class="w-4 h-4 object-contain" />
              <span class="font-semibold text-slate-200 group-hover:text-indigo-300">{{ link.name }}</span>
            </div>
            <span class="px-2 py-0.5 rounded-md bg-slate-800 text-slate-400 text-[10px] border border-slate-700">{{ link.category }}</span>
          </Link>

          <div v-if="filteredSearchLinks.length === 0" class="p-6 text-center text-slate-500 text-xs">
            មិនមានលទ្ធផលសម្រាប់ "{{ searchQuery }}"
          </div>
        </div>
      </div>
    </div>

    <!-- MAIN CONTENT AREA (Dynamically Padded for Fixed Sidebar Layout exactly like Admin & Teacher layouts) -->
    <main :class="[isSidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72', 'pt-3.5 sm:pt-4 pb-10 transition-all duration-300']">
      <div class="px-4 sm:px-6 lg:px-8">
        <slot />
      </div>
    </main>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #334155;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #475569;
}
</style>
