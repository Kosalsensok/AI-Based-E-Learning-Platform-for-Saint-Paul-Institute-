<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, useForm, Head, Link } from '@inertiajs/vue3'
import TeacherLayout from '@/Layouts/TeacherLayout.vue'

const props = defineProps<{
  majors?: Array<any>
  departments?: Array<any>
  faculties?: Array<any>
}>()

const currentStep = ref(1)
const maxReachedStep = ref(1)
const stepError = ref<string | null>(null)

// 5 Official Majors of Saint Paul Institute
const defaultMajors = [
  { id: 1, name: 'IT & Networking', code: 'IT' },
  { id: 2, name: 'Tourism Management', code: 'TM' },
  { id: 3, name: 'English Literature', code: 'ENG' },
  { id: 4, name: 'Agronomy', code: 'AGR' },
  { id: 5, name: 'Social Work', code: 'SW' }
]

const availableMajors = computed(() => {
  if (props.majors && props.majors.length > 0) return props.majors
  return defaultMajors
})

// Step 2 Modules & Lessons State
interface Lesson {
  id: number
  title: string
  duration: string
  type: 'video' | 'document' | 'quiz'
  video_url?: string
}

interface Module {
  id: number
  title: string
  lessons: Lesson[]
}

const modules = ref<Module[]>([
  {
    id: 1,
    title: 'Module 1: Introduction & Fundamentals',
    lessons: [
      { id: 1, title: 'Lesson 1.1: Course Overview & Learning Roadmap', duration: '15 mins', type: 'video' },
      { id: 2, title: 'Lesson 1.2: Environment Setup & Prerequisites', duration: '20 mins', type: 'video' }
    ]
  },
  {
    id: 2,
    title: 'Module 2: Core Concepts & Practice',
    lessons: [
      { id: 3, title: 'Lesson 2.1: Foundations & Architecture Deep Dive', duration: '25 mins', type: 'video' }
    ]
  }
])

const newModuleTitle = ref('')
const showAddModuleModal = ref(false)

const addModule = () => {
  if (newModuleTitle.value.trim()) {
    modules.value.push({
      id: Date.now(),
      title: newModuleTitle.value.trim(),
      lessons: []
    })
    newModuleTitle.value = ''
    showAddModuleModal.value = false
  }
}

const removeModule = (index: number) => {
  modules.value.splice(index, 1)
}

const addLessonToModule = (moduleIndex: number) => {
  const lessonNum = modules.value[moduleIndex].lessons.length + 1
  modules.value[moduleIndex].lessons.push({
    id: Date.now(),
    title: `Lesson ${moduleIndex + 1}.${lessonNum}: New Topic`,
    duration: '15 mins',
    type: 'video'
  })
}

const removeLesson = (moduleIndex: number, lessonIndex: number) => {
  modules.value[moduleIndex].lessons.splice(lessonIndex, 1)
}

const totalLessonCount = computed(() => {
  return modules.value.reduce((acc, m) => acc + m.lessons.length, 0)
})

// Form State
const form = useForm({
  // Step 1: Basic Info
  title: 'Full-Stack Web Development with Laravel & Vue',
  code: 'IT-DEV-101',
  major_id: props.majors?.[0]?.id || 1,
  learning_mode: 'instructor_led', // 'instructor_led' | 'self_paced'
  short_description: 'Comprehensive curriculum covering backend API development, reactive frontend UI, database design, and cloud deployment for Saint Paul Institute students.',
  
  // Step 2: Content
  modules_data: [] as Module[],

  // Step 3: Media
  thumbnail: 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=600&q=80',
  intro_video_url: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
  
  // Step 4: Pricing
  pricing_type: 'paid', // 'free' | 'paid'
  price: 30.00,
  currency: 'USD',

  // Status & Actions
  status: 'draft'
})

// Live Revenue Split Calculator (ABA PayWay): Teacher share 70%, Platform share 30%
const effectivePrice = computed(() => {
  if (form.pricing_type === 'free') return 0
  return Number(form.price) || 0
})

const teacherShareAmount = computed(() => {
  if (form.pricing_type === 'free') return 0
  return Number((effectivePrice.value * 0.70).toFixed(2))
})

const platformShareAmount = computed(() => {
  if (form.pricing_type === 'free') return 0
  return Number((effectivePrice.value * 0.30).toFixed(2))
})

// Stepper Configuration: 5 Steps
const stepsList = [
  { step: 1, label: 'Basic Info', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
  { step: 2, label: 'Content', icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' },
  { step: 3, label: 'Media', icon: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' },
  { step: 4, label: 'Pricing', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
  { step: 5, label: 'Review', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }
]

// Validation Logic per step
const validateCurrentStep = (): boolean => {
  stepError.value = null
  
  if (currentStep.value === 1) {
    if (!form.title || !form.title.trim()) {
      stepError.value = 'សូមបញ្ចូលចំណងជើងវគ្គសិក្សា (Please enter Course Title).'
      return false
    }
    if (!form.major_id) {
      stepError.value = 'សូមជ្រើសរើសមុខវិជ្ជា (Please select a Major).'
      return false
    }
    if (!form.short_description || !form.short_description.trim()) {
      stepError.value = 'សូមបញ្ចូលការពិពណ៌នាសង្ខេប (Please enter Short Description).'
      return false
    }
  }

  if (currentStep.value === 2) {
    if (modules.value.length === 0) {
      stepError.value = 'សូមបន្ថែមយ៉ាងហោចណាស់ 1 Module (Please add at least 1 Module).'
      return false
    }
  }

  if (currentStep.value === 4) {
    if (form.pricing_type === 'paid' && (!form.price || form.price <= 0)) {
      stepError.value = 'សូមបញ្ចូលតម្លៃវគ្គសិក្សាត្រឹមត្រូវ (Please enter valid course price).'
      return false
    }
  }

  return true
}

const goToStep = (stepNum: number) => {
  if (stepNum <= maxReachedStep.value) {
    stepError.value = null
    currentStep.value = stepNum
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const nextStep = () => {
  if (!validateCurrentStep()) return
  if (currentStep.value < 5) {
    currentStep.value++
    maxReachedStep.value = Math.max(maxReachedStep.value, currentStep.value)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const prevStep = () => {
  stepError.value = null
  if (currentStep.value > 1) {
    currentStep.value--
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const getSelectedMajorName = computed(() => {
  const found = availableMajors.value.find((m: any) => m.id == form.major_id)
  return found?.name || 'IT & Networking'
})

// Submission Actions
const saveAsDraft = () => {
  form.modules_data = modules.value
  form.transform((data) => ({
    ...data,
    is_paid: data.pricing_type === 'paid',
    price: data.pricing_type === 'paid' ? data.price : 0,
    description: data.short_description,
    status: 'draft',
    redirect_action: 'draft'
  })).post(route('teacher.courses.store'), {
    onError: (err) => {
      stepError.value = Object.values(err)[0] as string
    }
  })
}

const submitForApproval = () => {
  if (!validateCurrentStep()) return
  form.modules_data = modules.value
  form.transform((data) => ({
    ...data,
    is_paid: data.pricing_type === 'paid',
    price: data.pricing_type === 'paid' ? data.price : 0,
    description: data.short_description,
    status: 'pending',
    redirect_action: 'submit'
  })).post(route('teacher.courses.store'), {
    onError: (err) => {
      stepError.value = Object.values(err)[0] as string
    }
  })
}

// Media Selection Presets
const thumbnailPresets = [
  'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=600&q=80',
  'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=600&q=80',
  'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=600&q=80',
  'https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?auto=format&fit=crop&w=600&q=80'
]
</script>

<template>
  <TeacherLayout title="Create New Course">
    <Head title="Create New Course — Course Creation Wizard" />

    <div class="space-y-6 max-w-5xl mx-auto pb-16">
      
      <!-- Top Page Header Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-xl backdrop-blur-xl">
        <div>
          <div class="flex items-center gap-2 text-xs text-indigo-400 font-semibold mb-1">
            <span>Teacher Panel</span>
            <span>/</span>
            <Link href="/teacher/courses" class="hover:underline">My Courses</Link>
            <span>/</span>
            <span class="text-slate-200">Create New Course</span>
          </div>
          <h1 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
            <span>Create New Course Wizard</span>
            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Step {{ currentStep }} of 5</span>
          </h1>
          <p class="text-xs text-slate-400 mt-0.5">Saint Paul Institute — 5-Step Multi-Stage Course Setup Wizard</p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
          <button
            @click="router.get(route('teacher.courses.index'))"
            type="button"
            class="px-3.5 py-2 text-xs font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-all cursor-pointer"
          >
            Cancel
          </button>
          <button
            @click="saveAsDraft"
            type="button"
            class="px-4 py-2 text-xs font-semibold text-indigo-300 hover:text-white bg-indigo-950/60 hover:bg-indigo-900/80 border border-indigo-500/30 rounded-xl transition-all flex items-center gap-1.5 shadow-sm cursor-pointer"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            <span>Save as Draft</span>
          </button>
        </div>
      </div>

      <!-- 🎨 Stepper Component (Top of Page): 5 circular step indicators connected by a line -->
      <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
        <div class="flex items-center justify-between relative max-w-3xl mx-auto">
          
          <!-- Connecting background line -->
          <div class="absolute top-4 left-6 right-6 h-0.5 bg-slate-800 -z-0"></div>
          
          <template v-for="(item, idx) in stepsList" :key="item.step">
            <div
              @click="goToStep(item.step)"
              class="flex flex-col items-center relative z-10 select-none group cursor-pointer"
              :class="item.step <= maxReachedStep ? 'cursor-pointer' : 'cursor-not-allowed opacity-60'"
            >
              <!-- Step Circle -->
              <div
                :class="[
                  currentStep === item.step
                    ? 'bg-indigo-600 text-white ring-4 ring-indigo-500/30 shadow-lg shadow-indigo-600/40 scale-110'
                    : currentStep > item.step || (item.step <= maxReachedStep && currentStep !== item.step)
                      ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-md'
                      : 'bg-slate-800 text-slate-400 border border-slate-700',
                  'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300'
                ]"
              >
                <span v-if="currentStep > item.step || (item.step < currentStep && item.step <= maxReachedStep)">✓</span>
                <span v-else>{{ item.step }}</span>
              </div>

              <!-- Label below each circle (11px, muted for inactive, primary for active) -->
              <span
                :class="[
                  currentStep === item.step
                    ? 'text-indigo-400 font-bold'
                    : item.step <= maxReachedStep
                      ? 'text-slate-200 font-medium'
                      : 'text-slate-500',
                  'text-[11px] mt-2 whitespace-nowrap'
                ]"
              >
                {{ item.label }}
              </span>
            </div>
          </template>

        </div>
      </div>

      <!-- Validation Error Alert Banner -->
      <div v-if="stepError || Object.keys(form.errors).length > 0" class="p-4 bg-rose-950/60 border border-rose-500/50 rounded-2xl text-rose-200 text-xs font-semibold flex items-center justify-between shadow-lg">
        <div class="flex items-center gap-2">
          <span class="text-rose-400 text-base">⚠️</span>
          <span>{{ stepError || Object.values(form.errors)[0] }}</span>
        </div>
        <button @click="stepError = null; form.clearErrors()" type="button" class="text-rose-400 hover:text-white font-bold px-2 py-0.5 rounded-lg hover:bg-rose-900/40 cursor-pointer">✕</button>
      </div>

      <!-- Step Content Area -->
      <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl backdrop-blur-xl">

        <!-- ═══════════════ STEP 1 — BASIC INFO ═══════════════ -->
        <div v-if="currentStep === 1" class="space-y-5">
          <div class="border-b border-slate-800 pb-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
              <span>Step 1 — Basic Course Information</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Enter the core details, official major, learning mode, and short course description.</p>
          </div>

          <div class="space-y-4">
            <!-- ចំណងជើងវគ្គសិក្សា (Title) -->
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">
                ចំណងជើងវគ្គសិក្សា (Course Title) <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.title"
                type="text"
                placeholder="e.g. Full-Stack Web Development with Laravel & Vue"
                class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- មុខវិជ្ជា (Major) — Dropdown 5 ជម្រើស -->
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">
                  មុខវិជ្ជា (Major) <span class="text-rose-500">*</span>
                </label>
                <select
                  v-model="form.major_id"
                  class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none cursor-pointer"
                >
                  <option v-for="m in availableMajors" :key="m.id" :value="m.id">
                    {{ m.name }}
                  </option>
                </select>
                <p class="text-[10px] text-slate-500 mt-1">Official Saint Paul Institute academic disciplines</p>
              </div>

              <!-- របៀបសិក្សា (Mode) — Dropdown Instructor-led / Self-paced -->
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">
                  របៀបសិក្សា (Learning Mode) <span class="text-rose-500">*</span>
                </label>
                <select
                  v-model="form.learning_mode"
                  class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none cursor-pointer"
                >
                  <option value="instructor_led">👨‍🏫 Instructor-Led (Live Scheduled Classes)</option>
                  <option value="self_paced">🧑‍💻 Self-Paced (Open Enrollment & Flexible Study)</option>
                </select>
                <p class="text-[10px] text-slate-500 mt-1">Defines attendance requirements & timetable structure</p>
              </div>
            </div>

            <!-- Course Code -->
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">
                Course Code <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.code"
                type="text"
                placeholder="e.g. IT-DEV-101"
                class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 text-xs font-mono uppercase focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              />
            </div>

            <!-- ការពិពណ៌នាសង្ខេប (Description) — Textarea 3 rows -->
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">
                ការពិពណ៌នាសង្ខេប (Course Description) <span class="text-rose-500">*</span>
              </label>
              <textarea
                v-model="form.short_description"
                rows="3"
                placeholder="សរសេរការពិពណ៌នាសង្ខេបអំពីគោលបំណង និងខ្លឹមសារសំខាន់ៗនៃវគ្គសិក្សានេះ..."
                class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              ></textarea>
              <p class="text-[10px] text-slate-500 text-right mt-0.5">{{ form.short_description.length }}/300 characters</p>
            </div>
          </div>
        </div>

        <!-- ═══════════════ STEP 2 — CONTENT ═══════════════ -->
        <div v-else-if="currentStep === 2" class="space-y-5">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div>
              <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span>Step 2 — Course Content Structure</span>
              </h2>
              <p class="text-xs text-slate-400 mt-0.5">Build curriculum modules and attach lessons with video & materials.</p>
            </div>

            <!-- "Add Module" button (secondary, outline) -->
            <button
              @click="showAddModuleModal = true"
              type="button"
              class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-indigo-300 hover:text-white text-xs font-bold rounded-xl border border-indigo-500/30 transition-all flex items-center gap-1.5 cursor-pointer shadow-sm"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
              <span>Add Module</span>
            </button>
          </div>

          <!-- Video Upload Dropzone (Dashed border, Cloudinary Integration) -->
          <div class="border-2 border-dashed border-slate-700 hover:border-indigo-500 rounded-2xl p-4 text-center bg-slate-950/60 transition-all cursor-pointer">
            <div class="flex flex-col items-center justify-center gap-1.5">
              <div class="w-10 h-10 rounded-xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
              </div>
              <p class="text-xs font-bold text-slate-200">Video Upload Dropzone (Cloudinary Cloud Storage)</p>
              <p class="text-[10px] text-slate-400">Drag & drop course lecture videos (MP4, MKV, WebM up to 2GB per lesson) or click to browse</p>
            </div>
          </div>

          <!-- List of Module cards (folder icon + name + lesson count) -->
          <div class="space-y-3 pt-2">
            <div
              v-for="(mod, mIdx) in modules"
              :key="mod.id"
              class="bg-slate-950 border border-slate-800 rounded-2xl p-4 space-y-3 shadow-md"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-xl bg-amber-500/15 text-amber-400 border border-amber-500/30 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                  </div>
                  <div>
                    <h3 class="text-xs font-bold text-white">{{ mod.title }}</h3>
                    <p class="text-[10px] text-slate-400">{{ mod.lessons.length }} Lessons configured</p>
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <button
                    @click="addLessonToModule(mIdx)"
                    type="button"
                    class="px-2.5 py-1 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white rounded-lg text-[10px] font-bold border border-indigo-500/30 transition-colors cursor-pointer"
                  >
                    + Add Lesson
                  </button>
                  <button
                    @click="removeModule(mIdx)"
                    type="button"
                    class="p-1 text-slate-500 hover:text-rose-400 rounded-lg transition-colors cursor-pointer"
                    title="Delete Module"
                  >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </div>

              <!-- Lessons inside Module -->
              <div v-if="mod.lessons.length > 0" class="pl-4 space-y-2 border-l-2 border-slate-800 ml-3">
                <div
                  v-for="(les, lIdx) in mod.lessons"
                  :key="les.id"
                  class="flex items-center justify-between p-2 bg-slate-900/80 rounded-xl border border-slate-800/80 text-xs"
                >
                  <div class="flex items-center gap-2 flex-1">
                    <span class="text-indigo-400 font-bold">🎬</span>
                    <input
                      v-model="les.title"
                      type="text"
                      class="flex-1 bg-transparent text-slate-200 text-xs focus:outline-none border-b border-transparent focus:border-indigo-500"
                    />
                  </div>
                  <div class="flex items-center gap-2 text-[10px] text-slate-400">
                    <span class="bg-slate-950 px-2 py-0.5 rounded border border-slate-800">{{ les.duration }}</span>
                    <button @click="removeLesson(mIdx, lIdx)" class="text-slate-500 hover:text-rose-400 cursor-pointer">✕</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ═══════════════ STEP 3 — MEDIA ═══════════════ -->
        <div v-else-if="currentStep === 3" class="space-y-5">
          <div class="border-b border-slate-800 pb-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
              <span>Step 3 — Media & Course Assets</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Upload course thumbnail poster and promotional intro video preview.</p>
          </div>

          <div class="space-y-5">
            <!-- Thumbnail dropzone (drag & drop or click to browse) -->
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                Course Thumbnail Poster Image (Cloudinary)
              </label>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2 border-2 border-dashed border-slate-700 hover:border-indigo-500 rounded-2xl p-6 text-center bg-slate-950/60 transition-all cursor-pointer flex flex-col items-center justify-center">
                  <svg class="w-10 h-10 text-indigo-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  <p class="text-xs font-bold text-slate-200">Drag & Drop Thumbnail Image or <span class="text-indigo-400 underline">Browse File</span></p>
                  <p class="text-[10px] text-slate-400 mt-1">Recommended: 1280x720 (16:9), PNG or JPG under 5MB</p>
                </div>

                <!-- Preview Box -->
                <div class="bg-slate-950 rounded-2xl border border-slate-800 p-2.5 flex flex-col justify-between">
                  <div class="aspect-video rounded-xl overflow-hidden bg-slate-900 border border-slate-800">
                    <img :src="form.thumbnail" class="w-full h-full object-cover" alt="Thumbnail Preview" />
                  </div>
                  <p class="text-[10px] text-center text-slate-400 mt-1">Active Poster Preview</p>
                </div>
              </div>

              <!-- Quick Presets -->
              <div class="mt-3">
                <p class="text-[11px] text-slate-400 mb-1.5">Or choose from sample course thumbnails:</p>
                <div class="flex gap-2">
                  <img
                    v-for="(img, idx) in thumbnailPresets"
                    :key="idx"
                    :src="img"
                    @click="form.thumbnail = img"
                    :class="[form.thumbnail === img ? 'ring-2 ring-indigo-500 scale-105' : 'opacity-60 hover:opacity-100', 'w-16 h-10 rounded-lg object-cover cursor-pointer transition-all border border-slate-700']"
                  />
                </div>
              </div>
            </div>

            <!-- Intro video dropzone -->
            <div class="pt-3 border-t border-slate-800">
              <label class="block text-xs font-semibold text-slate-300 mb-1">
                Promotional Intro Video URL / Cloudinary Link
              </label>
              <input
                v-model="form.intro_video_url"
                type="text"
                placeholder="https://www.youtube.com/watch?v=... or Cloudinary Video Link"
                class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              />
              <p class="text-[10px] text-slate-500 mt-1">Free 2-minute introductory preview for prospective students</p>
            </div>
          </div>
        </div>

        <!-- ═══════════════ STEP 4 — PRICING ═══════════════ -->
        <div v-else-if="currentStep === 4" class="space-y-5">
          <div class="border-b border-slate-800 pb-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
              <span>Step 4 — Course Pricing & ABA PayWay Integration</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Set course fee and preview real-time automated revenue split (ABA PayWay / KHQR).</p>
          </div>

          <div class="space-y-5">
            <!-- Free vs Paid Toggle -->
            <div class="flex items-center gap-3 bg-slate-950 p-2 rounded-2xl border border-slate-800 max-w-sm">
              <button
                @click="form.pricing_type = 'paid'"
                :class="[
                  form.pricing_type === 'paid' ? 'bg-emerald-600 text-white font-bold shadow-md' : 'text-slate-400 hover:text-white',
                  'flex-1 py-2 text-xs rounded-xl transition-all cursor-pointer'
                ]"
                type="button"
              >
                💳 Paid Course (ABA)
              </button>
              <button
                @click="form.pricing_type = 'free'"
                :class="[
                  form.pricing_type === 'free' ? 'bg-indigo-600 text-white font-bold shadow-md' : 'text-slate-400 hover:text-white',
                  'flex-1 py-2 text-xs rounded-xl transition-all cursor-pointer'
                ]"
                type="button"
              >
                🎁 Free Course ($0)
              </button>
            </div>

            <!-- Price input (USD, number type) -->
            <div v-if="form.pricing_type === 'paid'" class="space-y-4">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">
                  Course Fee ($ USD) <span class="text-rose-500">*</span>
                </label>
                <div class="relative max-w-xs">
                  <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">$</span>
                  <input
                    v-model.number="form.price"
                    type="number"
                    min="1"
                    step="0.50"
                    placeholder="30.00"
                    class="w-full pl-8 pr-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 text-sm font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                  />
                </div>
              </div>

              <!-- 📊 Live Revenue-Split Calculator (ABA PayWay) -->
              <div class="p-5 bg-slate-950 border border-emerald-500/30 rounded-2xl space-y-3 shadow-lg">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                  <span class="text-xs font-bold text-emerald-300 flex items-center gap-1.5">
                    <span>🏦 ABA PayWay Revenue Split Breakdown</span>
                  </span>
                  <span class="text-xs font-extrabold text-white">${{ effectivePrice.toFixed(2) }} USD</span>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-1">
                  <div class="bg-slate-900 p-3 rounded-xl border border-emerald-500/20 text-center">
                    <p class="text-[11px] text-emerald-400 font-semibold">Teacher Share (70%)</p>
                    <p class="text-lg font-black text-emerald-300 mt-0.5">${{ teacherShareAmount.toFixed(2) }}</p>
                    <p class="text-[9px] text-slate-400 mt-0.5">Payout directly to Instructor</p>
                  </div>
                  <div class="bg-slate-900 p-3 rounded-xl border border-slate-800 text-center">
                    <p class="text-[11px] text-slate-400 font-semibold">Platform Share (30%)</p>
                    <p class="text-lg font-black text-slate-300 mt-0.5">${{ platformShareAmount.toFixed(2) }}</p>
                    <p class="text-[9px] text-slate-400 mt-0.5">Institute Hosting & Maintenance</p>
                  </div>
                </div>

                <p class="text-[10px] text-slate-400 italic text-center">
                  *Revenue split updates live automatically per Saint Paul Institute ABA PayWay configuration.
                </p>
              </div>
            </div>

            <div v-else class="p-4 bg-indigo-950/30 border border-indigo-500/30 rounded-2xl text-xs text-indigo-200">
              ℹ️ Free courses are accessible to all enrolled Saint Paul Institute students without payment gateway checkout.
            </div>
          </div>
        </div>

        <!-- ═══════════════ STEP 5 — REVIEW ═══════════════ -->
        <div v-else-if="currentStep === 5" class="space-y-5">
          <div class="border-b border-slate-800 pb-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
              <span>Step 5 — Final Review & Submission</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Review your course summary card before saving as draft or submitting for Admin review.</p>
          </div>

          <!-- Summary Card -->
          <div class="bg-slate-950 border border-slate-800 rounded-2xl p-5 space-y-4 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
              <div>
                <span class="font-mono text-[10px] text-indigo-400 bg-indigo-950/80 px-2 py-0.5 rounded border border-indigo-500/30">{{ form.code }}</span>
                <h3 class="text-base font-bold text-white mt-1">{{ form.title }}</h3>
                <p class="text-xs text-slate-400">{{ getSelectedMajorName }}</p>
              </div>
              <div class="text-right">
                <span class="text-sm font-bold text-emerald-400 px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 rounded-xl">
                  {{ form.pricing_type === 'paid' ? `$${effectivePrice.toFixed(2)} USD` : 'Free ($0)' }}
                </span>
              </div>
            </div>

            <!-- Key Info Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
              <div class="bg-slate-900 p-2.5 rounded-xl border border-slate-800">
                <span class="text-[10px] text-slate-400 block">Learning Mode</span>
                <span class="font-bold text-slate-200 capitalize">{{ form.learning_mode === 'self_paced' ? 'Self-Paced' : 'Instructor-Led' }}</span>
              </div>
              <div class="bg-slate-900 p-2.5 rounded-xl border border-slate-800">
                <span class="text-[10px] text-slate-400 block">Total Modules</span>
                <span class="font-bold text-indigo-300">{{ modules.length }} Modules</span>
              </div>
              <div class="bg-slate-900 p-2.5 rounded-xl border border-slate-800">
                <span class="text-[10px] text-slate-400 block">Total Lessons</span>
                <span class="font-bold text-indigo-300">{{ totalLessonCount }} Lessons</span>
              </div>
              <div class="bg-slate-900 p-2.5 rounded-xl border border-slate-800">
                <span class="text-[10px] text-slate-400 block">Teacher Share</span>
                <span class="font-bold text-emerald-400">{{ form.pricing_type === 'paid' ? `$${teacherShareAmount.toFixed(2)} (70%)` : '$0.00' }}</span>
              </div>
            </div>

            <!-- Description -->
            <div class="text-xs text-slate-300 bg-slate-900/60 p-3 rounded-xl border border-slate-800/80">
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Course Description</p>
              <p class="leading-relaxed">{{ form.short_description }}</p>
            </div>
          </div>

          <!-- Two Final Actions -->
          <div class="p-4 bg-slate-950/80 border border-slate-800 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-3">
            <div>
              <p class="text-xs font-bold text-white">Ready to publish your course curriculum?</p>
              <p class="text-[11px] text-slate-400">You can save as Draft to continue editing later, or Submit for Admin Approval now.</p>
            </div>
            <div class="flex items-center gap-2.5 w-full sm:w-auto">
              <button
                @click="saveAsDraft"
                type="button"
                class="flex-1 sm:flex-initial px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs rounded-xl border border-slate-700 transition-all cursor-pointer"
              >
                Save as Draft
              </button>
              <button
                @click="submitForApproval"
                type="button"
                class="flex-1 sm:flex-initial px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition-all cursor-pointer flex items-center justify-center gap-1.5"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Submit for Approval</span>
              </button>
            </div>
          </div>
        </div>

      </div>

      <!-- 🎨 Navigation Footer (All Steps) -->
      <div class="flex items-center justify-between bg-slate-900/90 border border-slate-800 p-4 rounded-2xl shadow-xl">
        <!-- Left: "ថយក្រោយ" (Back) — hidden on step 1 -->
        <div>
          <button
            v-if="currentStep > 1"
            @click="prevStep"
            type="button"
            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5 cursor-pointer"
          >
            <span>←</span>
            <span>ថយក្រោយ (Back)</span>
          </button>
        </div>

        <!-- Right: "រក្សាទុកជា Draft" (secondary) + "បន្ទាប់ / ដាក់ស្នើសុំអនុម័ត" (accent) -->
        <div class="flex items-center gap-2.5">
          <button
            @click="saveAsDraft"
            type="button"
            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold text-xs rounded-xl border border-slate-700 transition-colors cursor-pointer"
          >
            រក្សាទុកជា Draft
          </button>

          <button
            v-if="currentStep < 5"
            @click="nextStep"
            type="button"
            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md hover:shadow-indigo-600/30 transition-all flex items-center gap-1.5 cursor-pointer"
          >
            <span>បន្ទាប់ (Next)</span>
            <span>→</span>
          </button>

          <button
            v-else
            @click="submitForApproval"
            type="button"
            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md hover:shadow-emerald-600/30 transition-all flex items-center gap-1.5 cursor-pointer"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>ដាក់ស្នើសុំអនុម័ត (Submit)</span>
          </button>
        </div>
      </div>

    </div>

    <!-- Modal for Adding New Module -->
    <div v-if="showAddModuleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
      <div class="bg-slate-900 border border-slate-700 rounded-2xl max-w-md w-full p-5 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
          <h3 class="text-sm font-bold text-white">Add Curriculum Module</h3>
          <button @click="showAddModuleModal = false" class="text-slate-400 hover:text-white cursor-pointer">✕</button>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-300 mb-1">Module Title</label>
          <input
            v-model="newModuleTitle"
            type="text"
            placeholder="e.g. Module 3: Database Design & Eloquent ORM"
            class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            @keyup.enter="addModule"
          />
        </div>
        <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
          <button @click="showAddModuleModal = false" class="px-3.5 py-1.5 bg-slate-800 text-slate-300 text-xs rounded-xl font-semibold cursor-pointer">Cancel</button>
          <button @click="addModule" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl cursor-pointer">Add Module</button>
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
