<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, useForm, Head, Link } from '@inertiajs/vue3'
import TeacherLayout from '@/Layouts/TeacherLayout.vue'

interface Lesson {
  id: number
  title: string
  type: string
  duration_seconds?: number
  content_url?: string
  order: number
}

interface Module {
  id: number
  title: string
  order: number
  lessons: Lesson[]
}

interface Course {
  id: number
  title: string
  code: string
  description?: string
  status: string
  learning_mode: string
  is_paid: boolean
  price?: number
  discount_price?: number
  major?: { name: string; department?: { faculty?: { name: string } } }
  modules?: Module[]
  quizzes?: any[]
  enrollments?: any[]
  created_at?: string
}

interface ChecklistItem {
  key: string
  label: string
  completed: boolean
  detail?: string
}

const props = defineProps<{
  course: Course
  currentTab?: string
  completionPercentage?: number
  checklist?: ChecklistItem[]
}>()

const activeTab = ref(props.currentTab || 'overview')
const moduleList = ref<Module[]>(props.course.modules || [])

// Dynamic checklist calculated from active moduleList
const setupChecklist = computed(() => {
  const modulesCount = moduleList.value.length
  const lessonsCount = moduleList.value.reduce((acc: number, m: Module) => acc + (m.lessons?.length || 0), 0)
  const quizzesCount = (props.course.quizzes?.length || 0) + moduleList.value.reduce((acc: number, m: Module) => acc + (m.lessons?.filter((l: Lesson) => l.type === 'quiz').length || 0), 0)

  return [
    { key: 'basic_info', label: 'Basic Information Complete', completed: !!(props.course.title && props.course.code) },
    { key: 'academic_info', label: 'Academic Information Complete', completed: !!props.course.major },
    { key: 'learning_mode', label: 'Learning Mode Selected', completed: !!props.course.learning_mode },
    { key: 'fee_aba', label: 'Course Fee & ABA Configured', completed: true },
    { key: 'modules', label: 'Course Modules Created', completed: modulesCount > 0, detail: `${modulesCount} modules` },
    { key: 'lessons', label: 'Lessons Added to Modules', completed: lessonsCount > 0, detail: `${lessonsCount} lessons` },
    { key: 'quizzes', label: 'Quiz & Assessment Created', completed: quizzesCount > 0, detail: `${quizzesCount} quizzes` },
    { key: 'cert_rule', label: 'Certificate Issuance Rule Confirmed', completed: true },
  ]
})

const setActiveTab = (tabId: string) => {
  activeTab.value = tabId
  const url = new URL(window.location.href)
  url.searchParams.set('tab', tabId)
  window.history.pushState({}, '', url.toString())
}

const calcCompletion = computed(() => {
  if (typeof props.completionPercentage === 'number') return props.completionPercentage
  const done = setupChecklist.value.filter(i => i.completed).length
  return Math.round((done / setupChecklist.value.length) * 100)
})

// Expanded module state
const expandedModules = ref<Record<number, boolean>>({})
moduleList.value.forEach(m => { expandedModules.value[m.id] = true })

const toggleModuleExpand = (id: number) => {
  expandedModules.value[id] = !expandedModules.value[id]
}

// ── Modals & Forms ─────────────────────────────────────────────
const showModuleModal = ref(false)
const showLessonModal = ref(false)
const showAiOutlineModal = ref(false)
const showFeedbackModal = ref(false)
const showFeeModal = ref(false)

const activeModuleForLesson = ref<Module | null>(null)
const selectedStudentForFeedback = ref<any>(null)

const moduleForm = useForm({ title: '' })
const lessonForm = useForm({
  title: '',
  type: 'video',
  duration_seconds: 900,
  video: null as File | null,
  file: null as File | null,
})
const feedbackForm = useForm({ feedback: '' })
const settingsForm = useForm({
  learning_mode: props.course.learning_mode || 'instructor_led',
  is_paid: props.course.is_paid ?? true,
  price: props.course.price || 30.00,
  discount_price: props.course.discount_price || 25.00,
})

// Add Module
const handleAddModule = () => {
  moduleForm.post(`/teacher/courses/${props.course.id}/modules`, {
    onSuccess: () => {
      showModuleModal.value = false
      moduleForm.reset()
    }
  })
}

// Add Lesson
const openAddLessonModal = (m: Module) => {
  activeModuleForLesson.value = m
  showLessonModal.value = true
}

const handleAddLesson = () => {
  if (!activeModuleForLesson.value) return
  lessonForm.post(`/teacher/modules/${activeModuleForLesson.value.id}/lessons`, {
    forceFormData: true,
    onSuccess: () => {
      showLessonModal.value = false
      lessonForm.reset()
    }
  })
}

// AI Outline Generator
const aiPrompt = ref('Generate 4 comprehensive modules with lessons for ' + props.course.title)
const isGeneratingAi = ref(false)

const handleGenerateAiOutline = () => {
  isGeneratingAi.value = true
  setTimeout(() => {
    isGeneratingAi.value = false
    showAiOutlineModal.value = false

    // Mock inserting generated modules
    const newModules: Module[] = [
      {
        id: Date.now() + 1,
        title: 'Module 1: Introduction & Fundamentals',
        order: 1,
        lessons: [
          { id: Date.now() + 10, title: '🎬 Lesson 1: Overview & Syntax Basics', type: 'video', duration_seconds: 900, order: 1 },
          { id: Date.now() + 11, title: '📄 Lesson 2: Getting Started Guide (PDF)', type: 'pdf', order: 2 },
          { id: Date.now() + 12, title: '📝 Module 1 Quiz: Core Concepts', type: 'quiz', order: 3 }
        ]
      },
      {
        id: Date.now() + 2,
        title: 'Module 2: Control Flow & Operations',
        order: 2,
        lessons: [
          { id: Date.now() + 20, title: '🎬 Lesson 1: Conditional Logic', type: 'video', duration_seconds: 1200, order: 1 },
          { id: Date.now() + 21, title: '💻 Coding Lab: Practice Exercises', type: 'coding_lab', order: 2 },
          { id: Date.now() + 22, title: '📊 Presentation Slides Chapter 2', type: 'slides', order: 3 }
        ]
      }
    ]
    moduleList.value.push(...newModules)
  }, 1200)
}

// Helper icons & badge maps
const getLessonIcon = (type: string) => {
  switch (type) {
    case 'video': return '🎬'
    case 'pdf': return '📄'
    case 'slides': return '📊'
    case 'quiz': return '📝'
    case 'coding_lab': return '💻'
    case 'ai_english': return '🇬🇧'
    default: return '📁'
  }
}

const formatDuration = (s?: number) => {
  if (!s) return '--:--'
  const m = Math.floor(s / 60)
  const sec = s % 60
  return `${m}:${sec.toString().padStart(2, '0')}`
}

// Submitting course for approval
const submitCourseApproval = () => {
  router.post(`/teacher/courses/${props.course.id}/submit`)
}

// Simulated Enrolled Students
const sampleStudents = ref([
  { id: 1, name: 'Chan Dara', email: 'dara@example.com', avatar: 'https://ui-avatars.com/api/?name=Chan+Dara', progress: 85, score: 92, at_risk: false, payment: '$25.00 via ABA' },
  { id: 2, name: 'Keo Sokha', email: 'sokha@example.com', avatar: 'https://ui-avatars.com/api/?name=Keo+Sokha', progress: 40, score: 58, at_risk: true, payment: '$25.00 via ABA' },
  { id: 3, name: 'Vannak Nita', email: 'nita@example.com', avatar: 'https://ui-avatars.com/api/?name=Vannak+Nita', progress: 100, score: 98, at_risk: false, payment: '$25.00 via ABA' },
  { id: 4, name: 'Srey Roth', email: 'roth@example.com', avatar: 'https://ui-avatars.com/api/?name=Srey+Roth', progress: 20, score: 45, at_risk: true, payment: '$25.00 via ABA' },
])

const toggleAtRisk = (student: any) => {
  student.at_risk = !student.at_risk
}

const openFeedback = (student: any) => {
  selectedStudentForFeedback.value = student
  showFeedbackModal.value = true
}

const sendStudentFeedback = () => {
  showFeedbackModal.value = false
  feedbackForm.reset()
}

// Update settings
const saveSettings = () => {
  settingsForm.put(`/teacher/courses/${props.course.id}`)
}
</script>

<template>
  <TeacherLayout :title="`Course Workspace: ${course.title}`">
    <Head :title="`Workspace - ${course.title}`" />

    <div class="max-w-7xl mx-auto space-y-6">
      
      <!-- ─── TOP HEADER CARD (Course Workspace Header) ─── -->
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 shadow-2xl border border-indigo-500/20 relative overflow-hidden text-white">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
          <div class="space-y-3 max-w-3xl">
            <div class="flex flex-wrap items-center gap-2 text-xs">
              <span class="px-2.5 py-1 rounded-md font-mono font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                {{ course.code }}
              </span>
              <span class="px-2.5 py-1 rounded-md font-semibold bg-slate-800 text-slate-300">
                {{ course.major?.name || 'General Major' }}
              </span>
              <span class="px-2.5 py-1 rounded-md font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 capitalize">
                {{ (course.learning_mode || 'instructor_led').replace('_', ' ') }}
              </span>
              <span class="px-2.5 py-1 rounded-md font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                {{ course.is_paid ? `$${Number(course.price || 25).toFixed(2)} via ABA` : 'Free Course' }}
              </span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
              {{ course.title }}
            </h1>

            <p class="text-xs sm:text-sm text-slate-300 line-clamp-2">
              {{ course.description || 'Course Workspace for syllabus design, lesson uploads, student tracking, and fee management.' }}
            </p>
          </div>

          <!-- Status & Setup Progress Bar -->
          <div class="bg-slate-950/70 p-4 rounded-2xl border border-slate-800 space-y-3 min-w-[280px]">
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-slate-400">Course Status</span>
              <span 
                class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                :class="course.status === 'published' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30'"
              >
                {{ course.status }}
              </span>
            </div>

            <div>
              <div class="flex items-center justify-between text-xs font-bold mb-1">
                <span class="text-slate-300">Setup Completion</span>
                <span class="text-indigo-400 font-mono">{{ calcCompletion }}%</span>
              </div>
              <div class="w-full h-2.5 bg-slate-800 rounded-full overflow-hidden">
                <div 
                  class="h-full bg-gradient-to-r from-indigo-500 to-emerald-400 rounded-full transition-all duration-500" 
                  :style="{ width: `${calcCompletion}%` }"
                ></div>
              </div>
            </div>

            <div class="pt-1 flex items-center gap-2">
              <button 
                v-if="course.status === 'draft'"
                @click="submitCourseApproval"
                class="w-full py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs rounded-xl transition shadow-md shadow-emerald-600/30 cursor-pointer"
              >
                Submit for Approval
              </button>
              <Link 
                :href="`/student/browse/${course.id}`" 
                target="_blank"
                class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-center font-semibold text-xs rounded-xl transition cursor-pointer"
              >
                Preview Course ↗
              </Link>
            </div>
          </div>
        </div>

        <!-- WORKSPACE NAVIGATION TABS -->
        <div class="flex items-center gap-2 overflow-x-auto pt-6 mt-6 border-t border-slate-800/80 custom-scrollbar">
          <button
            v-for="tab in [
              { id: 'overview', name: 'Overview', icon: '📌' },
              { id: 'curriculum', name: 'Curriculum Builder', icon: '📚' },
              { id: 'assessment', name: 'Assessment & Quizzes', icon: '📝' },
              { id: 'students', name: 'Students & Progress', icon: '👥' },
              { id: 'payments', name: 'Payments & Revenue', icon: '💳' },
              { id: 'settings', name: 'Settings (Mode & Fee)', icon: '⚙️' },
            ]"
            :key="tab.id"
            @click="setActiveTab(tab.id)"
            class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer"
            :class="activeTab === tab.id 
              ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' 
              : 'bg-slate-800/60 hover:bg-slate-800 text-slate-300'"
          >
            <span>{{ tab.icon }}</span>
            <span>{{ tab.name }}</span>
          </button>
        </div>
      </div>

      <!-- ─── TAB 1: OVERVIEW ─── -->
      <div v-if="activeTab === 'overview'" class="space-y-6">
        
        <!-- Course Setup Checklist -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-md border dark:border-slate-800 space-y-6">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b dark:border-slate-800 pb-4">
            <div>
              <h2 class="text-lg font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                <span>Course Setup Checklist</span>
              </h2>
              <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                Complete all requirements to submit your course for admin approval and publish to students.
              </p>
            </div>
            <button 
              @click="setActiveTab('curriculum')"
              class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition shadow-md shadow-indigo-600/30 flex items-center gap-2 cursor-pointer self-start sm:self-auto"
            >
              <span>Go to Curriculum</span>
              <span>→</span>
            </button>
          </div>

          <!-- Checklist Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div 
              v-for="item in setupChecklist" 
              :key="item.key"
              class="p-4 rounded-2xl border transition flex items-center justify-between gap-3"
              :class="item.completed 
                ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-300' 
                : 'bg-amber-50/50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800/40 text-amber-900 dark:text-amber-300'"
            >
              <div class="flex items-center gap-3">
                <span class="text-lg font-bold">
                  {{ item.completed ? '✅' : '❌' }}
                </span>
                <div>
                  <p class="text-xs font-bold">{{ item.label }}</p>
                  <p v-if="item.detail" class="text-[11px] opacity-75 font-mono">{{ item.detail }}</p>
                </div>
              </div>

              <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-md uppercase" :class="item.completed ? 'bg-emerald-200 dark:bg-emerald-800/50 text-emerald-800 dark:text-emerald-200' : 'bg-amber-200 dark:bg-amber-800/50 text-amber-800 dark:text-amber-200'">
                {{ item.completed ? 'Ready' : 'Missing' }}
              </span>
            </div>
          </div>

          <!-- Summary Bar -->
          <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs font-semibold">
            <div class="flex items-center gap-2 text-gray-700 dark:text-slate-200">
              <span class="text-indigo-500">ℹ️</span>
              <span>Setup Completion Progress: <strong>{{ calcCompletion }}%</strong></span>
            </div>

            <div class="text-gray-500 dark:text-slate-400">
              Need assistance? Use <span class="text-indigo-400 font-bold">🤖 Generate Outline with AI</span> in Curriculum.
            </div>
          </div>
        </div>

        <!-- Performance / Quick Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow border dark:border-slate-800 space-y-2">
            <span class="text-xs text-gray-500 dark:text-slate-400">Enrolled Students</span>
            <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400">40 / 50</p>
            <p class="text-[10px] text-emerald-500 font-semibold">80% Capacity filled</p>
          </div>

          <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow border dark:border-slate-800 space-y-2">
            <span class="text-xs text-gray-500 dark:text-slate-400">Average Student Progress</span>
            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">72%</p>
            <p class="text-[10px] text-gray-400 font-semibold">32 students on track</p>
          </div>

          <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow border dark:border-slate-800 space-y-2">
            <span class="text-xs text-gray-500 dark:text-slate-400">Total Course Revenue</span>
            <p class="text-2xl font-black text-amber-600 dark:text-amber-400">$900.00</p>
            <p class="text-[10px] text-amber-500 font-semibold">via ABA KHQR PayWay</p>
          </div>

          <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow border dark:border-slate-800 space-y-2">
            <span class="text-xs text-gray-500 dark:text-slate-400">Next Live Class</span>
            <p class="text-base font-black text-blue-600 dark:text-blue-400">Tomorrow 08:00 AM</p>
            <p class="text-[10px] text-gray-400 font-semibold">Room IT101-A</p>
          </div>
        </div>

      </div>

      <!-- ─── TAB 2: CURRICULUM BUILDER ─── -->
      <div v-else-if="activeTab === 'curriculum'" class="space-y-6">
        
        <!-- Curriculum Header Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl shadow-md border dark:border-slate-800">
          <div>
            <h2 class="text-lg font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
              <span>Curriculum Builder</span>
            </h2>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
              Add modules, upload videos, PDFs, slides, quizzes, and coding labs for this course.
            </p>
          </div>

          <div class="flex items-center gap-2">
            <button
              @click="showAiOutlineModal = true"
              class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs rounded-xl transition shadow-md shadow-indigo-600/30 flex items-center gap-1.5 cursor-pointer"
            >
              <span>🤖 Generate Outline with AI</span>
            </button>
            <button
              @click="showModuleModal = true"
              class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition shadow-md shadow-indigo-600/30 flex items-center gap-1.5 cursor-pointer"
            >
              <span>+ Add Module</span>
            </button>
          </div>
        </div>

        <!-- Modules List Accordion -->
        <div class="space-y-4">
          <div 
            v-for="(module, mIdx) in moduleList" 
            :key="module.id"
            class="bg-white dark:bg-slate-900 rounded-3xl border dark:border-slate-800 shadow-md overflow-hidden"
          >
            <!-- Module Header -->
            <div 
              @click="toggleModuleExpand(module.id)"
              class="p-5 bg-slate-50 dark:bg-slate-800/50 border-b dark:border-slate-800 flex items-center justify-between gap-4 cursor-pointer select-none"
            >
              <div class="flex items-center gap-3">
                <button type="button" class="text-gray-400 hover:text-white text-sm font-bold">
                  {{ expandedModules[module.id] ? '▼' : '▶' }}
                </button>
                <div>
                  <h3 class="font-extrabold text-sm text-gray-900 dark:text-white">
                    {{ module.title }}
                  </h3>
                  <p class="text-[11px] text-gray-500 dark:text-slate-400 font-mono mt-0.5">
                    Module {{ mIdx + 1 }} • {{ module.lessons?.length || 0 }} lessons & materials
                  </p>
                </div>
              </div>

              <div class="flex items-center gap-2" @click.stop>
                <button 
                  @click="openAddLessonModal(module)"
                  class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg transition"
                >
                  + Add Lesson
                </button>
                <button 
                  @click="router.delete(`/teacher/modules/${module.id}`)"
                  class="px-2.5 py-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 text-xs rounded-lg font-semibold transition"
                >
                  Delete
                </button>
              </div>
            </div>

            <!-- Lessons Items -->
            <div v-if="expandedModules[module.id]" class="divide-y dark:divide-slate-800">
              <div 
                v-for="lesson in module.lessons || []" 
                :key="lesson.id"
                class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition flex items-center justify-between gap-4 text-xs"
              >
                <div class="flex items-center gap-3">
                  <span class="text-base">{{ getLessonIcon(lesson.type) }}</span>
                  <div>
                    <p class="font-bold text-gray-900 dark:text-slate-100">{{ lesson.title }}</p>
                    <div class="flex items-center gap-2 text-[10px] text-gray-400 mt-0.5 font-mono">
                      <span class="uppercase font-bold text-indigo-400">{{ lesson.type }}</span>
                      <span v-if="lesson.duration_seconds">• {{ formatDuration(lesson.duration_seconds) }}</span>
                    </div>
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <button 
                    @click="router.delete(`/teacher/lessons/${lesson.id}`)"
                    class="px-2.5 py-1 text-slate-400 hover:text-red-500 text-xs rounded transition"
                  >
                    Remove
                  </button>
                </div>
              </div>

              <div v-if="!module.lessons || module.lessons.length === 0" class="p-8 text-center text-xs text-gray-400 italic">
                No lessons or content added to this module yet. Click "+ Add Lesson" above to upload Videos, PDFs, Slides, or Quizzes.
              </div>
            </div>
          </div>

          <div v-if="moduleList.length === 0" class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-dashed dark:border-slate-800 space-y-3">
            <p class="text-base font-bold text-gray-600 dark:text-slate-300">No modules created yet</p>
            <p class="text-xs text-gray-400 max-w-md mx-auto">Click "+ Add Module" to start structuring your syllabus, or use AI Outline Generator to automatically generate modules.</p>
            <button 
              @click="showAiOutlineModal = true"
              class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 cursor-pointer"
            >
              🤖 Generate Syllabus with AI
            </button>
          </div>
        </div>

      </div>

      <!-- ─── TAB 3: ASSESSMENT ─── -->
      <div v-else-if="activeTab === 'assessment'" class="space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-md border dark:border-slate-800 space-y-4">
          <h2 class="text-lg font-extrabold text-gray-900 dark:text-white">Assessment & Quiz Management</h2>
          <p class="text-xs text-gray-500 dark:text-slate-400">Configure Pre-Test, Practice Quizzes, Post-Test, and Coding Lab assessments for this course.</p>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
            <div class="p-4 rounded-2xl border dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 space-y-2">
              <span class="text-xs font-bold text-indigo-400">Module 1 Quiz</span>
              <p class="text-xs text-slate-300">10 Multiple Choice Questions</p>
              <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-500/20 text-emerald-400">Pass Score: 60%</span>
            </div>
            <div class="p-4 rounded-2xl border dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 space-y-2">
              <span class="text-xs font-bold text-indigo-400">Practice Quiz: Variables</span>
              <p class="text-xs text-slate-300">5 Exercises</p>
              <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded bg-blue-500/20 text-blue-400">Unlimited Retakes</span>
            </div>
            <div class="p-4 rounded-2xl border dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 space-y-2">
              <span class="text-xs font-bold text-amber-400">Final Post-Test</span>
              <p class="text-xs text-slate-300">Comprehensive Exam</p>
              <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded bg-purple-500/20 text-purple-400">Required for Certificate</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── TAB 4: STUDENTS ─── -->
      <div v-else-if="activeTab === 'students'" class="space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-md border dark:border-slate-800 space-y-4">
          <div class="flex items-center justify-between border-b dark:border-slate-800 pb-4">
            <div>
              <h2 class="text-lg font-extrabold text-gray-900 dark:text-white">Enrolled Students (40 / 50)</h2>
              <p class="text-xs text-gray-500 dark:text-slate-400">Monitor student progress, quiz scores, feedback, and at-risk flags.</p>
            </div>
          </div>

          <div class="divide-y dark:divide-slate-800">
            <div v-for="std in sampleStudents" :key="std.id" class="py-3 flex items-center justify-between gap-4 text-xs">
              <div class="flex items-center gap-3">
                <img :src="std.avatar" class="w-9 h-9 rounded-full" />
                <div>
                  <p class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span>{{ std.name }}</span>
                    <span v-if="std.at_risk" class="px-2 py-0.5 text-[10px] bg-red-500/20 text-red-400 rounded font-bold">At-Risk ⚠️</span>
                  </p>
                  <p class="text-[10px] text-gray-400 font-mono">{{ std.email }} • {{ std.payment }}</p>
                </div>
              </div>

              <div class="flex items-center gap-6">
                <div class="text-right">
                  <p class="font-bold text-slate-200">Progress: {{ std.progress }}%</p>
                  <p class="text-[10px] text-emerald-400 font-mono">Quiz Avg: {{ std.score }}%</p>
                </div>

                <div class="flex items-center gap-2">
                  <button @click="openFeedback(std)" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs rounded-lg font-medium">Feedback</button>
                  <button @click="toggleAtRisk(std)" class="px-3 py-1.5 text-xs rounded-lg font-semibold" :class="std.at_risk ? 'bg-emerald-600 text-white' : 'bg-red-500/20 text-red-400 border border-red-500/30'">
                    {{ std.at_risk ? 'Clear Flag' : 'Flag At-Risk' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── TAB 5: PAYMENTS & REVENUE ─── -->
      <div v-else-if="activeTab === 'payments'" class="space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-md border dark:border-slate-800 space-y-6">
          <div>
            <h2 class="text-lg font-extrabold text-gray-900 dark:text-white">ABA Payment & Revenue Breakdown</h2>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Course fee: $25.00 via ABA KHQR PayWay (90% Teacher Earning / 10% Institute Fee)</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
              <span class="text-[11px] text-slate-400">Total Revenue Collected</span>
              <p class="text-xl font-bold text-emerald-400">$1,000.00</p>
            </div>
            <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
              <span class="text-[11px] text-slate-400">Institute Platform Fee (10%)</span>
              <p class="text-xl font-bold text-slate-400">$100.00</p>
            </div>
            <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
              <span class="text-[11px] text-slate-400">Net Teacher Earning (90%)</span>
              <p class="text-xl font-bold text-indigo-400">$900.00</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── TAB 6: SETTINGS (MODE & FEE) ─── -->
      <div v-else-if="activeTab === 'settings'" class="space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-md border dark:border-slate-800 space-y-6">
          <div class="border-b dark:border-slate-800 pb-4">
            <h2 class="text-lg font-extrabold text-gray-900 dark:text-white">Course Settings (Mode & Fee)</h2>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Configure Learning Mode (Instructor-Led / Self-Paced) and Fee Pricing via ABA KHQR.</p>
          </div>

          <form @submit.prevent="saveSettings" class="space-y-6">
            <!-- Mode Selection -->
            <div class="space-y-3">
              <label class="block text-xs font-bold text-slate-300">Learning Mode</label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label 
                  class="p-4 rounded-2xl border dark:border-slate-800 cursor-pointer transition flex items-start gap-3"
                  :class="settingsForm.learning_mode === 'instructor_led' ? 'bg-indigo-950/40 border-indigo-500' : 'bg-slate-950'"
                >
                  <input type="radio" v-model="settingsForm.learning_mode" value="instructor_led" class="mt-1" />
                  <div>
                    <p class="text-xs font-bold text-white">○ Instructor-Led Mode</p>
                    <p class="text-[11px] text-slate-400 mt-1">Includes live classes, fixed schedule, attendance tracking, and teacher interaction.</p>
                  </div>
                </label>

                <label 
                  class="p-4 rounded-2xl border dark:border-slate-800 cursor-pointer transition flex items-start gap-3"
                  :class="settingsForm.learning_mode === 'self_paced' ? 'bg-indigo-950/40 border-indigo-500' : 'bg-slate-950'"
                >
                  <input type="radio" v-model="settingsForm.learning_mode" value="self_paced" class="mt-1" />
                  <div>
                    <p class="text-xs font-bold text-white">○ Self-Paced Mode</p>
                    <p class="text-[11px] text-slate-400 mt-1">Self-learning anytime, AI recommendations, auto quiz evaluation, no required live class.</p>
                  </div>
                </label>
              </div>
            </div>

            <!-- Fee & ABA -->
            <div class="space-y-3 pt-2">
              <label class="block text-xs font-bold text-slate-300">Course Pricing & ABA Gateway</label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-[11px] text-slate-400 mb-1">Standard Price ($)</label>
                  <input v-model.number="settingsForm.price" type="number" step="0.01" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white" />
                </div>
                <div>
                  <label class="block text-[11px] text-slate-400 mb-1">Discount Price ($)</label>
                  <input v-model.number="settingsForm.discount_price" type="number" step="0.01" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white" />
                </div>
              </div>
            </div>

            <div class="pt-4 flex justify-end">
              <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 cursor-pointer">
                Save Course Settings
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>

    <!-- ─── MODAL: Add Module ─── -->
    <div v-if="showModuleModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border dark:border-slate-800">
        <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Add New Module</h3>
        <form @submit.prevent="handleAddModule" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 mb-1">Module Title</label>
            <input v-model="moduleForm.title" type="text" required placeholder="e.g. Module 1: Introduction to C" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border dark:border-slate-800 rounded-xl text-xs text-gray-900 dark:text-white" />
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="showModuleModal = false" class="px-4 py-2 border rounded-xl text-xs font-semibold">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/30">Save Module</button>
          </div>
        </form>
      </div>
    </div>

    <!-- ─── MODAL: Add Lesson ─── -->
    <div v-if="showLessonModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border dark:border-slate-800">
        <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Add Lesson to "{{ activeModuleForLesson?.title }}"</h3>
        <form @submit.prevent="handleAddLesson" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 mb-1">Lesson Title</label>
            <input v-model="lessonForm.title" type="text" required placeholder="e.g. Lesson 1: What is C?" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border dark:border-slate-800 rounded-xl text-xs text-gray-900 dark:text-white" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 mb-1">Content Delivery Type</label>
            <select v-model="lessonForm.type" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border dark:border-slate-800 rounded-xl text-xs text-gray-900 dark:text-white">
              <option value="video">🎬 Video (MP4)</option>
              <option value="pdf">📄 PDF Document</option>
              <option value="slides">📊 Presentation Slides</option>
              <option value="quiz">📝 Quiz / Assessment</option>
              <option value="coding_lab">💻 IT Coding Lab</option>
              <option value="ai_english">🇬🇧 English AI Exercise</option>
              <option value="notes">📁 Reading Notes & Downloads</option>
            </select>
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="showLessonModal = false" class="px-4 py-2 border rounded-xl text-xs font-semibold">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/30">Save Lesson</button>
          </div>
        </form>
      </div>
    </div>

    <!-- ─── MODAL: AI Outline Generator ─── -->
    <div v-if="showAiOutlineModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl border dark:border-slate-800">
        <h3 class="text-base font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
          <span>🤖 AI Syllabus & Outline Generator</span>
        </h3>
        <p class="text-xs text-gray-500 dark:text-slate-400">Describe your course topic, and Gemini AI will automatically create structured modules with videos, PDFs, and quizzes.</p>

        <textarea v-model="aiPrompt" rows="3" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border dark:border-slate-800 rounded-xl text-xs text-gray-900 dark:text-white"></textarea>

        <div class="flex justify-end gap-2 pt-2">
          <button type="button" @click="showAiOutlineModal = false" class="px-4 py-2 border rounded-xl text-xs font-semibold">Cancel</button>
          <button 
            type="button" 
            @click="handleGenerateAiOutline" 
            :disabled="isGeneratingAi"
            class="px-5 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/30 flex items-center gap-2 cursor-pointer"
          >
            <span v-if="isGeneratingAi">Generating...</span>
            <span v-else>Generate & Add Modules</span>
          </button>
        </div>
      </div>
    </div>

    <!-- ─── MODAL: Feedback ─── -->
    <div v-if="showFeedbackModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border dark:border-slate-800">
        <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Send Feedback to {{ selectedStudentForFeedback?.name }}</h3>
        <textarea v-model="feedbackForm.feedback" rows="3" placeholder="Enter personalized feedback..." class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border dark:border-slate-800 rounded-xl text-xs text-gray-900 dark:text-white"></textarea>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" @click="showFeedbackModal = false" class="px-4 py-2 border rounded-xl text-xs font-semibold">Cancel</button>
          <button type="button" @click="sendStudentFeedback" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold">Send Feedback</button>
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
