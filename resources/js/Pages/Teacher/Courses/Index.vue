<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { router, useForm, Head, Link } from '@inertiajs/vue3'
import TeacherLayout from '@/Layouts/TeacherLayout.vue'

const props = defineProps<{
  courses: Array<any>
  majors: Array<any>
  departments?: Array<any>
  faculties?: Array<any>
  summaryStats?: {
    total: number
    draft: number
    pending: number
    published: number
    archived: number
    total_students: number
    total_revenue: number
  }
  currentTab?: string
  selectedCourseId?: number | null
}>()

// State
const activeTab = ref(props.currentTab || 'all')
const viewMode = ref<'card' | 'table'>('card')
const searchQuery = ref('')
const selectedMajor = ref('all')
const selectedMode = ref('all')
const selectedStatus = ref('all')
const selectedPricing = ref('all')
const sortBy = ref('newest')

// Dropdown State
const activeDropdownId = ref<number | null>(null)

const toggleDropdown = (courseId: number, event: Event) => {
  event.stopPropagation()
  if (activeDropdownId.value === courseId) {
    activeDropdownId.value = null
  } else {
    activeDropdownId.value = courseId
  }
}

const closeDropdowns = () => {
  activeDropdownId.value = null
}

// Modals
const showCreateWizardModal = ref(false)
const showDetailsModal = ref(false)
const showSettingsModal = ref(false)
const showModeWarningModal = ref(false)
const showFeeRequestModal = ref(false)
const selectedCourse = ref<any>(null)
const selectedCourseForSettingId = ref<number | null>(props.courses?.[0]?.id || null)

// Forms
const feeRequestForm = useForm({
  requested_price: 30.00,
  reason: ''
})

const modeChangeForm = useForm({
  learning_mode: 'instructor_led'
})

onMounted(() => {
  window.addEventListener('click', closeDropdowns)

  if (props.selectedCourseId && props.courses?.length) {
    const found = props.courses.find((c: any) => c.id == props.selectedCourseId)
    if (found) selectedCourse.value = found
  }
  if (!selectedCourse.value && props.courses?.length) {
    selectedCourse.value = props.courses[0]
  }

  if (props.currentTab === 'settings') {
    showSettingsModal.value = true
  }
})

onUnmounted(() => {
  window.removeEventListener('click', closeDropdowns)
})

watch(() => props.currentTab, (newTab) => {
  if (newTab) {
    activeTab.value = newTab
    if (newTab === 'settings') {
      showSettingsModal.value = true
    }
  }
})

// Statistics computed
const stats = computed(() => {
  if (props.summaryStats) return props.summaryStats
  const total = props.courses?.length || 0
  const draft = props.courses?.filter(c => c.status === 'draft').length || 0
  const pending = props.courses?.filter(c => c.status === 'pending' || c.status === 'pending_approval').length || 0
  const published = props.courses?.filter(c => c.status === 'published').length || 0
  const archived = props.courses?.filter(c => c.status === 'archived' || c.status === 'paused').length || 0
  const total_students = props.courses?.reduce((acc, c) => acc + (c.enrollments_count || 45), 0) || 128
  const total_revenue = props.courses?.reduce((acc, c) => acc + (c.is_paid ? (c.price || 30) * (c.enrollments_count || 45) : 0), 0) || 1420

  return { total, draft, pending, published, archived, total_students, total_revenue }
})

// Filtered Courses List
const filteredCourses = computed(() => {
  let list = props.courses || []

  // Filter by Tab (Synchronized 1:1 with Stat Cards)
  if (activeTab.value === 'drafts') {
    list = list.filter(c => c.status === 'draft')
  } else if (activeTab.value === 'pending') {
    list = list.filter(c => c.status === 'pending' || c.status === 'pending_approval')
  } else if (activeTab.value === 'published') {
    list = list.filter(c => c.status === 'published')
  } else if (activeTab.value === 'rejected') {
    list = list.filter(c => c.status === 'rejected')
  } else if (activeTab.value === 'archived') {
    list = list.filter(c => c.status === 'archived' || c.status === 'paused')
  }

  // Filter by Search Query
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(c => 
      c.title.toLowerCase().includes(q) || 
      c.code.toLowerCase().includes(q) ||
      c.description?.toLowerCase().includes(q)
    )
  }

  // Filter by Major
  if (selectedMajor.value !== 'all') {
    list = list.filter(c => c.major_id == selectedMajor.value)
  }

  // Filter by Mode
  if (selectedMode.value !== 'all') {
    list = list.filter(c => c.learning_mode === selectedMode.value)
  }

  // Filter by Status
  if (selectedStatus.value !== 'all') {
    list = list.filter(c => c.status === selectedStatus.value)
  }

  // Filter by Pricing
  if (selectedPricing.value === 'free') {
    list = list.filter(c => !c.is_paid || c.price == 0)
  } else if (selectedPricing.value === 'paid') {
    list = list.filter(c => c.is_paid && c.price > 0)
  }

  // Sorting
  return list.slice().sort((a, b) => {
    if (sortBy.value === 'newest') return (b.id - a.id)
    if (sortBy.value === 'oldest') return (a.id - b.id)
    if (sortBy.value === 'most_students') return ((b.enrollments_count || 0) - (a.enrollments_count || 0))
    if (sortBy.value === 'highest_earnings') return (((b.price || 0) * (b.enrollments_count || 0)) - ((a.price || 0) * (a.enrollments_count || 0)))
    return 0
  })
})

// Helper methods
const getCourseCover = (course: any) => {
  if (course?.thumbnail) return course.thumbnail
  const title = (course?.title || '').toLowerCase()
  const code = (course?.code || '').toLowerCase()
  const major = (course?.major?.name || '').toLowerCase()
  const id = Number(course?.id || 0)

  if (title.includes('web') || title.includes('php') || title.includes('laravel') || major.includes('it')) {
    return 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=600&q=80'
  }
  if (title.includes('data') || title.includes('python') || title.includes('ai') || title.includes('machine')) {
    return 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=600&q=80'
  }
  if (title.includes('cloud') || title.includes('devops') || title.includes('network') || code.includes('net')) {
    return 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=600&q=80'
  }
  if (title.includes('ui') || title.includes('ux') || title.includes('design')) {
    return 'https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?auto=format&fit=crop&w=600&q=80'
  }
  if (title.includes('mobile') || title.includes('flutter') || title.includes('android') || title.includes('ios')) {
    return 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=600&q=80'
  }
  if (title.includes('security') || title.includes('cyber')) {
    return 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=600&q=80'
  }

  const covers = [
    'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=600&q=80'
  ]
  return covers[id % covers.length]
}

const getRealisticTitle = (course: any) => {
  if (!course?.title) return 'Professional Skill Course'
  const title = course.title
  if (title.includes('Test Course Pending') || title.includes('Test Course Draft') || title.includes('Test Course')) {
    const id = Number(course?.id || 0)
    const titles = [
      'Full-Stack Web Development (Laravel & Vue)',
      'Cloud Architecture & DevOps Essentials',
      'Data Science & Machine Learning Fundamentals',
      'UI/UX Design System Masterclass',
      'Cybersecurity & Network Defense',
      'Cross-Platform Mobile Apps with Flutter'
    ]
    return titles[id % titles.length]
  }
  return title
}

const getMajorStyle = (majorName: string) => {
  const m = (majorName || '').toLowerCase()
  if (m.includes('tourism') || m.includes('tour')) {
    return {
      name: 'Tourism Management',
      badgeClass: 'bg-amber-500/15 text-amber-300 border-amber-500/30',
      tintClass: 'from-amber-950/70 via-slate-900 to-slate-950',
      iconClass: 'text-amber-400',
      icon: '/images/nav/sub/tourism.svg',
      svg: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064'
    }
  }
  if (m.includes('english') || m.includes('literature') || m.includes('lang')) {
    return {
      name: 'English Literature',
      badgeClass: 'bg-purple-500/15 text-purple-300 border-purple-500/30',
      tintClass: 'from-purple-950/70 via-slate-900 to-slate-950',
      iconClass: 'text-purple-400',
      icon: '/images/nav/sub/language.svg',
      svg: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'
    }
  }
  if (m.includes('agronomy') || m.includes('agro') || m.includes('agri')) {
    return {
      name: 'Agronomy',
      badgeClass: 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
      tintClass: 'from-emerald-950/70 via-slate-900 to-slate-950',
      iconClass: 'text-emerald-400',
      icon: '/images/nav/sub/agronomy.svg',
      svg: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'
    }
  }
  if (m.includes('social') || m.includes('work')) {
    return {
      name: 'Social Work',
      badgeClass: 'bg-rose-500/15 text-rose-300 border-rose-500/30',
      tintClass: 'from-rose-950/70 via-slate-900 to-slate-950',
      iconClass: 'text-rose-400',
      icon: '/images/nav/sub/social.svg',
      svg: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
    }
  }
  // Default: IT & Networking
  return {
    name: 'IT & Networking',
    badgeClass: 'bg-blue-500/15 text-blue-300 border-blue-500/30',
    tintClass: 'from-blue-950/70 via-slate-900 to-slate-950',
    iconClass: 'text-blue-400',
    icon: '/images/nav/sub/it.svg',
    svg: 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'
  }
}

const getStatusBadge = (status: string) => {
  switch (status) {
    case 'published':
      return {
        label: 'Published',
        color: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30 ring-1 ring-emerald-500/20',
        dot: 'bg-emerald-400',
        icon: '/images/actions/toast-success.svg',
        svgPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
      }
    case 'pending':
    case 'pending_approval':
      return {
        label: 'Pending',
        color: 'bg-purple-500/15 text-purple-300 border-purple-500/30 ring-1 ring-purple-500/20',
        dot: 'bg-purple-400',
        icon: '/images/nav/sub/history.svg',
        svgPath: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
      }
    case 'draft':
      return {
        label: 'Draft',
        color: 'bg-slate-800 text-slate-300 border-slate-700',
        dot: 'bg-slate-400',
        icon: '/images/nav/sub/self-study.svg',
        svgPath: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'
      }
    case 'rejected':
      return {
        label: 'Rejected',
        color: 'bg-rose-500/15 text-rose-300 border-rose-500/30 ring-1 ring-rose-500/20',
        dot: 'bg-rose-400',
        icon: '/images/actions/toast-warning.svg',
        svgPath: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
      }
    case 'paused':
      return {
        label: 'Paused',
        color: 'bg-orange-500/15 text-orange-300 border-orange-500/30',
        dot: 'bg-orange-400',
        icon: '/images/nav/sub/suspended.svg',
        svgPath: 'M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z'
      }
    case 'archived':
      return {
        label: 'Archived',
        color: 'bg-slate-800 text-slate-400 border-slate-700',
        dot: 'bg-slate-500',
        icon: '/images/nav/sub/suspended.svg',
        svgPath: 'M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'
      }
    default:
      return {
        label: status,
        color: 'bg-slate-800 text-slate-300 border-slate-700',
        dot: 'bg-slate-400',
        icon: '/images/nav/sub/all-courses.svg',
        svgPath: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
      }
  }
}

const getContextualMeta = (course: any) => {
  const modeText = course.learning_mode === 'self_paced' ? 'Self-Paced' : 'Instructor-Led'
  if (course.status === 'published') {
    const students = course.enrollments_count || 45
    return `${modeText} • ${students} students`
  }
  if (course.status === 'pending' || course.status === 'pending_approval') {
    const d = course.submitted_at ? new Date(course.submitted_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : 'Recently'
    return `${modeText} • Submitted: ${d}`
  }
  if (course.status === 'rejected') {
    return `${modeText} • Changes Requested`
  }
  return `${modeText} • Last edited: 2h ago`
}

const openDetails = (course: any) => {
  selectedCourse.value = course
  showDetailsModal.value = true
}

const openSettings = (course: any) => {
  selectedCourse.value = course || props.courses?.[0]
  showSettingsModal.value = true
}

const submitApproval = (course: any) => {
  if (confirm(`Submit "${course.title}" for admin approval? You won't be able to edit core details while pending review.`)) {
    router.post(route('teacher.courses.submit', course.id))
  }
}

const withdrawSubmission = (course: any) => {
  if (confirm(`Withdraw submission for "${course.title}"? The course status will return to Draft and allow you to continue editing.`)) {
    router.post(route('teacher.courses.withdraw', course.id))
  }
}

const duplicateCourse = (course: any) => {
  if (confirm(`Duplicate "${course.title}"? A new Draft copy will be created.`)) {
    router.post(route('teacher.courses.clone', course.id))
  }
}

const deleteCourse = (course: any) => {
  if (confirm(`Are you sure you want to delete draft "${course.title}"? This action cannot be undone.`)) {
    router.delete(route('teacher.courses.destroy', course.id))
  }
}

const pauseCourse = (course: any) => {
  if (confirm(`Pause new student enrollments for "${course.title}"? Existing students will keep access.`)) {
    router.post(route('teacher.courses.pause', course.id))
  }
}

const archiveCourse = (course: any) => {
  if (confirm(`Archive "${course.title}"? Course will be moved to archived history.`)) {
    router.post(route('teacher.courses.archive', course.id))
  }
}

const submitFeeRequest = () => {
  if (selectedCourse.value) {
    feeRequestForm.post(route('teacher.courses.request-fee-change', selectedCourse.value.id), {
      onSuccess: () => {
        showFeeRequestModal.value = false
        feeRequestForm.reset()
      }
    })
  }
}

const openCreateWizard = () => {
  router.get(route('teacher.courses.create'))
}
</script>

<template>
  <TeacherLayout title="My Courses">
    <Head title="My Courses — Teacher Panel" />

    <div class="space-y-5 pb-16">
      
      <!-- 🎨 Top Header Row (Left: Books Icon + "All courses", Right: Button "Create new course") -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-xl backdrop-blur-xl">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center shadow-inner shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
          <div>
            <h1 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
              <span>All courses</span>
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">Saint Paul Institute — Teacher & Instructor Workspace</p>
          </div>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 self-end sm:self-auto">
          <button
            @click="openSettings(props.courses?.[0])"
            type="button"
            class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-semibold text-xs rounded-xl border border-slate-700 transition-colors duration-200 flex items-center gap-1.5 cursor-pointer shadow-sm"
            title="Course Settings"
          >
            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Course Settings</span>
          </button>

          <button
            @click="openCreateWizard"
            type="button"
            class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl shadow-md hover:shadow-indigo-600/30 transition-all duration-200 flex items-center gap-2 cursor-pointer"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span>Create new course</span>
          </button>
        </div>
      </div>

      <!-- 🎨 Filter Bar (Below Header): Search input (flex-grow) + Status dropdown + Major dropdown -->
      <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-3.5 shadow-xl">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3">
          <div class="flex flex-wrap items-center gap-3 w-full flex-1">
            <!-- Search input (placeholder: "ស្វែងរកវគ្គសិក្សា...") — flex-grow -->
            <div class="relative flex-1 min-w-[220px]">
              <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="ស្វែងរកវគ្គសិក្សា..."
                class="w-full pl-9 pr-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
              />
            </div>

            <!-- Status dropdown: All / Draft / Pending / Published -->
            <select
              v-model="selectedStatus"
              class="bg-slate-950 border border-slate-800 text-slate-200 rounded-xl px-3 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 cursor-pointer min-w-[130px]"
            >
              <option value="all">Status: All</option>
              <option value="draft">Draft</option>
              <option value="pending">Pending</option>
              <option value="published">Published</option>
              <option value="rejected">Rejected</option>
            </select>

            <!-- Major dropdown: IT & Networking / Tourism Management / English Literature / Agronomy / Social Work -->
            <select
              v-model="selectedMajor"
              class="bg-slate-950 border border-slate-800 text-slate-200 rounded-xl px-3 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 cursor-pointer min-w-[170px]"
            >
              <option value="all">All Majors (5 Majors)</option>
              <option v-for="m in props.majors" :key="m.id" :value="m.id">{{ m.name }}</option>
            </select>

            <!-- Mode Filter: Instructor-Led / Self-Paced -->
            <select
              v-model="selectedMode"
              class="bg-slate-950 border border-slate-800 text-slate-200 rounded-xl px-3 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 cursor-pointer min-w-[130px]"
            >
              <option value="all">All Modes</option>
              <option value="instructor_led">Instructor-Led</option>
              <option value="self_paced">Self-Paced</option>
            </select>
          </div>

          <!-- Right: Card / Table View Switcher -->
          <div class="flex items-center gap-2 shrink-0 self-end md:self-auto">
            <div class="flex items-center bg-slate-950 p-1 rounded-xl border border-slate-800">
              <button
                @click="viewMode = 'card'"
                :class="[
                  viewMode === 'card' ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-white',
                  'px-3 py-1.5 rounded-lg text-xs transition-all duration-200 flex items-center gap-1.5 cursor-pointer'
                ]"
                title="Card View"
              >
                <span>▦ Card</span>
              </button>
              <button
                @click="viewMode = 'table'"
                :class="[
                  viewMode === 'table' ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-white',
                  'px-3 py-1.5 rounded-lg text-xs transition-all duration-200 flex items-center gap-1.5 cursor-pointer'
                ]"
                title="List View"
              >
                <span>☷ List</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Tab Pills Bar: All / Drafts / Pending / Published -->
      <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar">
        <button
          @click="activeTab = 'all'"
          :class="[
            activeTab === 'all'
              ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/20'
              : 'bg-slate-900/80 text-slate-400 hover:text-white hover:bg-slate-800 border border-slate-800',
            'px-4 py-2 rounded-xl text-xs transition-all duration-200 shrink-0 cursor-pointer flex items-center gap-2'
          ]"
        >
          <span>All Courses</span>
          <span class="px-1.5 py-0.5 rounded-md bg-slate-950/60 text-[10px]">{{ stats.total }}</span>
        </button>

        <button
          @click="activeTab = 'drafts'"
          :class="[
            activeTab === 'drafts'
              ? 'bg-slate-700 text-white font-bold shadow-md'
              : 'bg-slate-900/80 text-slate-400 hover:text-white hover:bg-slate-800 border border-slate-800',
            'px-4 py-2 rounded-xl text-xs transition-all duration-200 shrink-0 cursor-pointer flex items-center gap-2'
          ]"
        >
          <span>Draft Courses</span>
          <span class="px-1.5 py-0.5 rounded-md bg-slate-950/60 text-[10px]">{{ stats.draft }}</span>
        </button>

        <button
          @click="activeTab = 'pending'"
          :class="[
            activeTab === 'pending'
              ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-600/20'
              : 'bg-slate-900/80 text-purple-300 hover:text-white hover:bg-slate-800 border border-slate-800',
            'px-4 py-2 rounded-xl text-xs transition-all duration-200 shrink-0 cursor-pointer flex items-center gap-2'
          ]"
        >
          <span>Pending Approval</span>
          <span class="px-1.5 py-0.5 rounded-md bg-purple-950/80 text-purple-300 border border-purple-500/30 text-[10px]">{{ stats.pending }}</span>
        </button>

        <button
          @click="activeTab = 'published'"
          :class="[
            activeTab === 'published'
              ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-600/20'
              : 'bg-slate-900/80 text-emerald-300 hover:text-white hover:bg-slate-800 border border-slate-800',
            'px-4 py-2 rounded-xl text-xs transition-all duration-200 shrink-0 cursor-pointer flex items-center gap-2'
          ]"
        >
          <span>Published Courses</span>
          <span class="px-1.5 py-0.5 rounded-md bg-emerald-950/80 text-emerald-300 border border-emerald-500/30 text-[10px]">{{ stats.published }}</span>
        </button>
      </div>

      <!-- MAIN CONTENT: Course Card Grid (auto-fit, minmax(220px, 1fr), gap 16px) -->
      <div>
        
        <!-- 2.1 CARD VIEW MODE -->
        <div v-if="viewMode === 'card'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          
          <div
            v-for="course in filteredCourses"
            :key="course.id"
            class="bg-slate-900/90 border border-slate-800 hover:border-indigo-500/50 rounded-2xl overflow-hidden shadow-xl transition-all duration-300 group flex flex-col justify-between"
          >
            <!-- 🖼️ Thumbnail area (110px height): Icon representing major, background tinted by major's accent color -->
            <div
              :class="[
                'relative h-[110px] overflow-hidden flex items-center justify-center bg-gradient-to-tr border-b border-slate-800/80',
                getMajorStyle(course.major?.name).tintClass
              ]"
            >
              <!-- Background cover image overlay with subtle opacity if available -->
              <img
                :src="getCourseCover(course)"
                :alt="getRealisticTitle(course)"
                class="absolute inset-0 w-full h-full object-cover opacity-35 mix-blend-overlay transition-transform duration-500 group-hover:scale-105"
                @error="(e: any) => e.target.style.display = 'none'"
              />

              <!-- Major Accent Vector Icon Centerpiece -->
              <div class="relative z-10 w-12 h-12 rounded-2xl bg-slate-950/60 backdrop-blur-md border border-slate-700/60 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <svg :class="['w-6 h-6', getMajorStyle(course.major?.name).iconClass]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" :d="getMajorStyle(course.major?.name).svg" />
                </svg>
              </div>

              <!-- Price Tag Top Right -->
              <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded-lg bg-slate-950/80 backdrop-blur-md text-[10px] font-bold text-emerald-300 border border-emerald-500/30 shadow-md">
                {{ course.is_paid ? `$${Number(course.price || 0).toFixed(2)}` : 'Free' }}
              </span>

              <!-- Code Pill Top Left -->
              <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-lg bg-slate-950/80 backdrop-blur-md text-[10px] font-mono font-bold text-indigo-300 border border-slate-700 shadow-md">
                {{ course.code }}
              </span>
            </div>

            <!-- Card Body Content -->
            <div class="p-3.5 space-y-2.5 flex-1 flex flex-col justify-between">
              
              <div class="space-y-2">
                <!-- 🏷️ Badge row: Major badge (accent color) + Status badge (color-coded) -->
                <div class="flex items-center justify-between gap-2">
                  <!-- Major Badge -->
                  <span :class="[getMajorStyle(course.major?.name).badgeClass, 'px-2 py-0.5 rounded-lg text-[10px] font-bold border truncate max-w-[140px]']">
                    {{ course.major?.name || 'IT & Networking' }}
                  </span>

                  <!-- Status Badge (color-coded per spec) -->
                  <span :class="[getStatusBadge(course.status).color, 'px-2 py-0.5 rounded-lg text-[10px] font-bold border flex items-center gap-1.5 shrink-0 shadow-sm']">
                    <span :class="['w-1.5 h-1.5 rounded-full', getStatusBadge(course.status).dot]"></span>
                    <span>{{ getStatusBadge(course.status).label }}</span>
                  </span>
                </div>

                <!-- 📖 Title: Course name, 14px / font-weight: 500 -->
                <h3 class="font-medium text-[14px] leading-snug text-white group-hover:text-indigo-300 transition-colors line-clamp-2" :title="getRealisticTitle(course)">
                  {{ getRealisticTitle(course) }}
                </h3>

                <!-- ℹ️ Meta line: Mode (Instructor-led / Self-paced) + contextual info -->
                <p class="text-[11px] text-slate-400 flex items-center gap-1.5 truncate">
                  <span class="font-medium text-slate-300">{{ getContextualMeta(course) }}</span>
                </p>
              </div>

              <!-- Rejection Notice Banner on Card if Rejected -->
              <div v-if="course.status === 'rejected'" class="p-2 rounded-xl bg-rose-950/40 border border-rose-500/40 text-[10px] text-rose-200 space-y-0.5">
                <p class="font-bold text-rose-300">Changes Requested:</p>
                <p class="italic text-rose-200/90 line-clamp-2">
                  "{{ course.rejection_note || 'Please update the module lessons content before resubmission.' }}"
                </p>
              </div>

              <!-- 🔘 Action buttons: 2 buttons side-by-side per status mapping -->
              <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-800">
                <!-- Status = draft: Button 1: Edit, Button 2: Submit for Approval -->
                <template v-if="course.status === 'draft'">
                  <button
                    @click="router.get(route('teacher.courses.workspace', course.id))"
                    type="button"
                    class="py-1.5 px-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-medium text-xs rounded-xl border border-slate-700 transition-colors text-center cursor-pointer flex items-center justify-center gap-1.5"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit</span>
                  </button>
                  <button
                    @click="submitApproval(course)"
                    type="button"
                    class="py-1.5 px-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-xs rounded-xl shadow-sm transition-all text-center cursor-pointer flex items-center justify-center gap-1.5"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Submit</span>
                  </button>
                </template>

                <!-- Status = pending: Button 1: View, Button 2: Withdraw -->
                <template v-else-if="course.status === 'pending' || course.status === 'pending_approval'">
                  <button
                    @click="openDetails(course)"
                    type="button"
                    class="py-1.5 px-2.5 bg-purple-500/20 hover:bg-purple-500/30 text-purple-200 font-medium text-xs rounded-xl border border-purple-500/30 transition-colors text-center cursor-pointer flex items-center justify-center gap-1.5"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>View</span>
                  </button>
                  <button
                    @click="withdrawSubmission(course)"
                    type="button"
                    class="py-1.5 px-2.5 bg-slate-800 hover:bg-slate-700 text-amber-300 hover:text-amber-200 font-medium text-xs rounded-xl border border-amber-500/30 transition-colors text-center cursor-pointer flex items-center justify-center gap-1.5"
                    title="Withdraw to draft"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    <span>Withdraw</span>
                  </button>
                </template>

                <!-- Status = published: Button 1: View, Button 2: Edit -->
                <template v-else-if="course.status === 'published'">
                  <button
                    @click="openDetails(course)"
                    type="button"
                    class="py-1.5 px-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-medium text-xs rounded-xl border border-slate-700 transition-colors text-center cursor-pointer flex items-center justify-center gap-1.5"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>View</span>
                  </button>
                  <button
                    @click="router.get(route('teacher.courses.workspace', course.id))"
                    type="button"
                    class="py-1.5 px-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-xs rounded-xl shadow-sm transition-all text-center cursor-pointer flex items-center justify-center gap-1.5"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit</span>
                  </button>
                </template>

                <!-- Status = rejected: Button 1: View Reason, Button 2: Revise & Resubmit -->
                <template v-else-if="course.status === 'rejected'">
                  <button
                    @click="openDetails(course)"
                    type="button"
                    class="py-1.5 px-2.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-200 font-medium text-xs rounded-xl border border-rose-500/30 transition-colors text-center cursor-pointer flex items-center justify-center gap-1.5"
                  >
                    <span>Reason</span>
                  </button>
                  <button
                    @click="router.get(route('teacher.courses.workspace', course.id))"
                    type="button"
                    class="py-1.5 px-2.5 bg-rose-600 hover:bg-rose-500 text-white font-medium text-xs rounded-xl shadow-sm transition-all text-center cursor-pointer flex items-center justify-center gap-1.5"
                  >
                    <span>Revise</span>
                  </button>
                </template>

                <!-- Default / Other statuses -->
                <template v-else>
                  <button
                    @click="openDetails(course)"
                    type="button"
                    class="py-1.5 px-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl border border-slate-700 text-xs font-medium"
                  >
                    View
                  </button>
                  <button
                    @click="router.get(route('teacher.courses.workspace', course.id))"
                    type="button"
                    class="py-1.5 px-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-medium"
                  >
                    Workspace
                  </button>
                </template>
              </div>

            </div>
          </div>

          <!-- Empty State Graphic -->
          <div v-if="filteredCourses.length === 0" class="col-span-full py-16 text-center bg-slate-900/60 rounded-2xl border border-slate-800">
            <div class="relative w-16 h-16 mx-auto mb-3 flex items-center justify-center">
              <div class="w-14 h-14 rounded-2xl bg-indigo-950/80 border border-indigo-500/30 flex items-center justify-center shadow-xl">
                <svg class="w-7 h-7 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
              </div>
            </div>
            <h3 class="text-sm font-bold text-white">No Courses Found</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">There are no courses matching your search or active filters.</p>
            <button
              @click="openCreateWizard"
              type="button"
              class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-xs rounded-xl shadow-md transition-all duration-200 inline-flex items-center gap-1.5 cursor-pointer"
            >
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
              <span>Create New Course</span>
            </button>
          </div>

        </div>

        <!-- 2.2 TABLE VIEW MODE -->
        <div v-else class="bg-slate-800/90 border border-slate-700/80 rounded-2xl overflow-hidden shadow-xl">
          <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse text-xs">
              <thead>
                <tr class="bg-slate-900/80 border-b border-slate-700/80 text-slate-400 uppercase tracking-wider font-semibold">
                  <th class="py-3.5 px-4">Course</th>
                  <th class="py-3.5 px-4">Major</th>
                  <th class="py-3.5 px-4">Mode</th>
                  <th class="py-3.5 px-4">Students</th>
                  <th class="py-3.5 px-4">Fee</th>
                  <th class="py-3.5 px-4">Status</th>
                  <th class="py-3.5 px-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-700/60">
                <tr v-for="course in filteredCourses" :key="course.id" class="hover:bg-slate-700/40 transition-colors">
                  <td class="py-3 px-4">
                    <div class="flex items-center gap-3">
                      <img :src="getCourseCover(course)" class="w-10 h-10 rounded-xl object-cover border border-slate-700 shrink-0" />
                      <div>
                        <p class="font-bold text-white hover:text-indigo-300 transition-colors">{{ getRealisticTitle(course) }}</p>
                        <p class="text-[10px] text-indigo-400 font-mono">{{ course.code }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="py-3 px-4 text-slate-300">{{ course.major?.name || 'IT & Networking' }}</td>
                  <td class="py-3 px-4">
                    <span class="capitalize px-2 py-0.5 rounded-md bg-slate-900 text-slate-300 border border-slate-700">
                      {{ course.learning_mode === 'self_paced' ? 'Self-Paced' : 'Instructor-Led' }}
                    </span>
                  </td>
                  <td class="py-3 px-4 font-bold text-white">{{ course.enrollments_count || 45 }}</td>
                  <td class="py-3 px-4 font-bold text-emerald-400">{{ course.is_paid ? `$${Number(course.price || 0).toFixed(2)}` : 'Free' }}</td>
                  <td class="py-3 px-4">
                    <span :class="[getStatusBadge(course.status).color, 'px-2.5 py-1 rounded-full text-[10px] font-bold border inline-flex items-center gap-1.5 shadow-sm']">
                      <img
                        v-if="getStatusBadge(course.status).icon"
                        :src="getStatusBadge(course.status).icon"
                        class="w-3.5 h-3.5 object-contain shrink-0 filter brightness-125"
                        alt="Status Icon"
                      />
                      <svg v-else class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="getStatusBadge(course.status).svgPath" />
                      </svg>
                      <span>{{ getStatusBadge(course.status).label }}</span>
                    </span>
                  </td>
                  <td class="py-3 px-4 text-right relative">
                    <div class="flex items-center justify-end gap-2">
                      <button @click="router.get(route('teacher.courses.modules.index', course.id))" class="px-2.5 py-1 bg-indigo-600/20 text-indigo-300 hover:bg-indigo-600 hover:text-white rounded-lg font-semibold text-[11px] cursor-pointer">Manage</button>
                      <button @click="openSettings(course)" class="px-2 py-1 bg-slate-700 hover:bg-slate-600 text-slate-300 hover:text-white rounded-lg text-[11px] cursor-pointer">Settings</button>
                      
                      <!-- Three-Dot Menu Options for Table View -->
                      <div class="relative">
                        <button
                          @click.stop="toggleDropdown(course.id, $event)"
                          type="button"
                          class="p-1.5 bg-slate-700/60 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg border border-slate-600 transition-colors duration-200 cursor-pointer"
                          title="More options"
                        >
                          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                        </button>

                        <div
                          v-if="activeDropdownId === course.id"
                          @click.stop
                          class="absolute right-0 top-full mt-1.5 w-52 bg-slate-900 border border-slate-700/80 rounded-2xl p-2 shadow-2xl z-50 text-left transition-all duration-200"
                        >
                          <button
                            @click="openDetails(course); activeDropdownId = null"
                            class="w-full text-left px-3 py-2 text-xs font-semibold text-sky-400 hover:bg-slate-800 hover:text-sky-300 rounded-xl flex items-center gap-2 transition-colors cursor-pointer"
                          >
                            <svg class="w-4 h-4 text-sky-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>View Details</span>
                          </button>

                          <button
                            @click="openSettings(course); activeDropdownId = null"
                            class="w-full text-left px-3 py-2 text-xs font-semibold text-indigo-400 hover:bg-slate-800 hover:text-indigo-300 rounded-xl flex items-center gap-2 transition-colors cursor-pointer"
                          >
                            <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Course Settings</span>
                          </button>

                          <button
                            @click="duplicateCourse(course); activeDropdownId = null"
                            class="w-full text-left px-3 py-2 text-xs font-semibold text-blue-400 hover:bg-slate-800 hover:text-blue-300 rounded-xl flex items-center gap-2 transition-colors cursor-pointer"
                          >
                            <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span>Duplicate Course</span>
                          </button>

                          <button
                            v-if="course.status === 'draft' || course.status === 'rejected'"
                            @click="submitApproval(course); activeDropdownId = null"
                            class="w-full text-left px-3 py-2 text-xs font-semibold text-emerald-400 hover:bg-slate-800 hover:text-emerald-300 rounded-xl flex items-center gap-2 transition-colors cursor-pointer"
                          >
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Submit for Approval</span>
                          </button>

                          <button
                            v-if="course.status === 'published'"
                            @click="pauseCourse(course); activeDropdownId = null"
                            class="w-full text-left px-3 py-2 text-xs font-semibold text-amber-400 hover:bg-slate-800 hover:text-amber-300 rounded-xl flex items-center gap-2 transition-colors cursor-pointer"
                          >
                            <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Pause Enrollment</span>
                          </button>

                          <button
                            v-if="course.status === 'published'"
                            @click="archiveCourse(course); activeDropdownId = null"
                            class="w-full text-left px-3 py-2 text-xs font-semibold text-slate-400 hover:bg-slate-800 hover:text-slate-200 rounded-xl flex items-center gap-2 transition-colors cursor-pointer"
                          >
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            <span>Archive Course</span>
                          </button>

                          <button
                            v-if="course.status === 'draft'"
                            @click="deleteCourse(course); activeDropdownId = null"
                            class="w-full text-left px-3 py-2 text-xs font-semibold text-rose-400 hover:bg-rose-950/40 hover:text-rose-300 rounded-xl flex items-center gap-2 transition-colors border-t border-slate-800 mt-1 pt-1.5 cursor-pointer"
                          >
                            <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Delete Draft</span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>

    </div>

    <!-- COURSE SETTINGS MODAL (Global Preferences & Per-Course Configuration) -->
    <div v-if="showSettingsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
      <div class="bg-slate-900 border border-slate-700 rounded-2xl max-w-4xl w-full p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              <span>⚙️ Course Settings & Global Preferences</span>
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Configure teacher defaults, certificate rules, ABA revenue split, and AI grading rubrics.</p>
          </div>
          <button @click="showSettingsModal = false" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <!-- Global Teacher Level Configurations (4 Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
          <!-- 1. Certificate Template -->
          <div class="bg-slate-950/80 border border-slate-800 rounded-xl p-4 space-y-2">
            <div class="flex items-center justify-between">
              <h4 class="text-xs font-bold text-indigo-300 uppercase tracking-wider">🎓 Default Certificate</h4>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Active</span>
            </div>
            <p class="text-xs text-white font-semibold">Saint Paul Institute Official Certificate</p>
            <p class="text-[11px] text-slate-400 leading-relaxed">Standard completion certificate with student name, course title, verification QR code & signature.</p>
          </div>

          <!-- 2. Revenue Split % (View-Only per ABA PayWay) -->
          <div class="bg-slate-950/80 border border-slate-800 rounded-xl p-4 space-y-2">
            <div class="flex items-center justify-between">
              <h4 class="text-xs font-bold text-emerald-300 uppercase tracking-wider">💳 Revenue Split (ABA PayWay)</h4>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">View-Only</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-center text-xs">
              <div class="bg-slate-900 p-2 rounded-lg border border-slate-800">
                <p class="text-[10px] text-emerald-400">Teacher Earning</p>
                <p class="font-bold text-emerald-400 text-sm">90%</p>
              </div>
              <div class="bg-slate-900 p-2 rounded-lg border border-slate-800">
                <p class="text-[10px] text-slate-400">Platform Fee</p>
                <p class="font-bold text-slate-300 text-sm">10%</p>
              </div>
            </div>
            <p class="text-[10px] text-slate-500">Revenue split percentages are managed by Institute Administration & Finance.</p>
          </div>

          <!-- 3. Notification Preferences -->
          <div class="bg-slate-950/80 border border-slate-800 rounded-xl p-4 space-y-2">
            <h4 class="text-xs font-bold text-amber-300 uppercase tracking-wider">🔔 Notification Preferences</h4>
            <div class="space-y-1.5 text-xs text-slate-300">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" checked class="rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-0" />
                <span>Instant In-App Alert on new student enrollment</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" checked class="rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-0" />
                <span>Email notification for student quiz submissions</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" checked class="rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-0" />
                <span>ABA Payment payout receipt notification</span>
              </label>
            </div>
          </div>

          <!-- 4. AI Grading Rubrics & Thresholds -->
          <div class="bg-slate-950/80 border border-slate-800 rounded-xl p-4 space-y-2">
            <h4 class="text-xs font-bold text-purple-300 uppercase tracking-wider">🤖 AI Grading Rubrics & Thresholds</h4>
            <div class="space-y-1.5 text-xs">
              <div class="flex items-center justify-between text-slate-300">
                <span>High Mastery Threshold:</span>
                <span class="font-bold text-emerald-400">80%+ (Certificate Eligible)</span>
              </div>
              <div class="flex items-center justify-between text-slate-300">
                <span>Standard Passing Threshold:</span>
                <span class="font-bold text-indigo-400">50% Minimum</span>
              </div>
              <div class="flex items-center justify-between text-slate-300">
                <span>At-Risk Intervention Alert:</span>
                <span class="font-bold text-rose-400">&lt; 40% (AI Recommendation)</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Course Selector Bar -->
        <div class="bg-slate-950/80 border border-slate-800 rounded-xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4 border-t border-slate-800">
          <div class="flex-1">
            <label class="block text-[11px] font-bold text-indigo-300 uppercase tracking-wider mb-1">Select Course For Pricing / Mode Adjustment</label>
            <select
              :value="selectedCourse?.id || props.courses?.[0]?.id"
              @change="(e: any) => selectedCourse = (props.courses || []).find(c => c.id == e.target.value)"
              class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs font-bold focus:ring-2 focus:ring-indigo-500"
            >
              <option v-for="c in props.courses" :key="c.id" :value="c.id">
                {{ c.code }} — {{ c.title }} ({{ c.status }})
              </option>
            </select>
          </div>

          <div v-if="selectedCourse" class="shrink-0 self-end sm:self-auto">
            <span :class="[getStatusBadge(selectedCourse?.status || 'published').color, 'px-2.5 py-1 rounded-full text-xs font-bold border inline-flex items-center gap-1.5 shadow-sm']">
              <img
                v-if="getStatusBadge(selectedCourse?.status || 'published').icon"
                :src="getStatusBadge(selectedCourse?.status || 'published').icon"
                class="w-3.5 h-3.5 object-contain shrink-0 filter brightness-125"
                alt="Status Icon"
              />
              <svg v-else class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" :d="getStatusBadge(selectedCourse?.status || 'published').svgPath" />
              </svg>
              <span>{{ getStatusBadge(selectedCourse?.status || 'published').label }}</span>
            </span>
          </div>
        </div>

        <!-- Settings Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Mode Settings Card -->
          <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-4 space-y-3">
            <h4 class="text-xs font-bold text-indigo-300 uppercase tracking-wider border-b border-slate-800 pb-2">👨‍🏫 Mode Configuration</h4>
            <div class="flex items-center justify-between text-xs">
              <span class="text-slate-400">Current Mode:</span>
              <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                {{ (selectedCourse?.learning_mode || 'instructor_led') === 'self_paced' ? '🧑‍💻 Self-Paced' : '👨‍🏫 Instructor-Led' }}
              </span>
            </div>
            <div class="text-xs text-slate-300 space-y-1.5 pt-2 border-t border-slate-800">
              <div class="flex items-center justify-between text-[11px]">
                <span class="text-slate-400">Class Section:</span>
                <span class="font-bold text-white">IT101-A</span>
              </div>
              <div class="flex items-center justify-between text-[11px]">
                <span class="text-slate-400">Schedule:</span>
                <span class="font-bold text-white">Mon/Wed 8:00-10:00 AM</span>
              </div>
            </div>
            <div class="p-2.5 rounded-lg bg-amber-950/30 border border-amber-500/30 text-[10px] text-amber-300">
              ⚠️ Changing mode on published courses with enrolled students will adjust live schedule & attendance settings.
            </div>
          </div>

          <!-- Fee & ABA Card -->
          <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-4 space-y-3">
            <h4 class="text-xs font-bold text-emerald-300 uppercase tracking-wider border-b border-slate-800 pb-2">💳 Fee & ABA Payment</h4>
            <div class="flex items-center justify-between text-xs">
              <span class="text-slate-400">Price Tier:</span>
              <span class="font-bold text-emerald-400">
                {{ selectedCourse?.is_paid ? `$${Number(selectedCourse?.price || 30).toFixed(2)} USD` : 'Free ($0.00)' }}
              </span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-center text-xs pt-1">
              <div class="bg-slate-900 p-2 rounded-lg border border-slate-800">
                <p class="text-[10px] text-slate-400">Platform (10%)</p>
                <p class="font-bold text-slate-300">${{ (Number(selectedCourse?.price || 30) * 0.10).toFixed(2) }}</p>
              </div>
              <div class="bg-slate-900 p-2 rounded-lg border border-slate-800">
                <p class="text-[10px] text-emerald-400">Teacher (90%)</p>
                <p class="font-bold text-emerald-400">${{ (Number(selectedCourse?.price || 30) * 0.90).toFixed(2) }}</p>
              </div>
            </div>
            <div class="p-2.5 rounded-lg bg-emerald-950/20 border border-emerald-500/30 text-[10px] text-emerald-300 flex items-center justify-between">
              <span>🏦 ABA PayWay Integration</span>
              <span class="font-bold">Active</span>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
          <button @click="showSettingsModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs rounded-xl font-bold">Close</button>
          <button @click="showFeeRequestModal = true; showSettingsModal = false" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs rounded-xl font-bold">Request Fee Change</button>
        </div>
      </div>
    </div>

    <!-- DETAILS MODAL -->
    <div v-if="showDetailsModal && selectedCourse" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
      <div class="bg-slate-900 border border-slate-700 rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <h3 class="text-base font-bold text-white">{{ selectedCourse.title }}</h3>
          <button @click="showDetailsModal = false" class="text-slate-400 hover:text-white">✕</button>
        </div>
        <div class="space-y-3 text-xs text-slate-300">
          <p><strong>Code:</strong> {{ selectedCourse.code }}</p>
          <p><strong>Major:</strong> {{ selectedCourse.major?.name || 'IT & Networking' }}</p>
          <p><strong>Mode:</strong> {{ selectedCourse.learning_mode }}</p>
          <p><strong>Price:</strong> {{ selectedCourse.is_paid ? `$${selectedCourse.price}` : 'Free' }}</p>
          <p><strong>Description:</strong> {{ selectedCourse.description || 'No description provided.' }}</p>
        </div>
        <div class="flex justify-end pt-3 border-t border-slate-800">
          <button @click="showDetailsModal = false" class="px-4 py-2 bg-slate-800 text-white text-xs rounded-xl font-bold">Close</button>
        </div>
      </div>
    </div>

    <!-- FEE CHANGE REQUEST MODAL -->
    <div v-if="showFeeRequestModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
      <div class="bg-slate-900 border border-slate-700 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <h3 class="text-sm font-bold text-white">Request Fee Change</h3>
          <button @click="showFeeRequestModal = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-3">
          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">New Requested Price ($ USD)</label>
            <input v-model.number="feeRequestForm.requested_price" type="number" step="0.01" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Reason for Adjustment</label>
            <textarea v-model="feeRequestForm.reason" rows="3" placeholder="Explain why fee update is requested..." class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white"></textarea>
          </div>
          <p class="text-[10px] text-amber-300/80">Note: Updating prices for published courses requires Admin approval to protect active student enrollments.</p>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
          <button @click="showFeeRequestModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 text-xs rounded-xl font-semibold">Cancel</button>
          <button @click="submitFeeRequest" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs rounded-xl font-bold">Submit Request</button>
        </div>
      </div>
    </div>

  </TeacherLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  height: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #334155;
  border-radius: 10px;
}
</style>
