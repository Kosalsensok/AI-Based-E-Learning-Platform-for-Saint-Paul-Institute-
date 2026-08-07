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

const certProgress = ref(100)
const certQuizScore = ref(60)

const form = useForm({
  title: 'C Programming Basics',
  code: 'IT101',
  language: 'English / Khmer',
  level: 'Beginner',
  category: 'Programming',
  short_description: 'Learn the fundamentals of C programming, memory management, pointers, and algorithms from basic syntax to structured software development.',
  thumbnail: '/images/courses/c-programming.jpg',
  outcomes: [
    'Write and test basic C programs using standard libraries',
    'Understand pointers, memory allocation, and data structures',
    'Build modular functions and debug syntax errors effectively'
  ],
  prerequisites: 'Basic computer literacy and problem-solving skills',

  // Step 2: Academic Info
  faculty_id: null,
  department_id: null,
  major_id: props.majors?.[0]?.id || null,
  subject: 'Computer Science & Software Engineering',
  academic_year: '2025-2026',
  academic_term: 'Semester 1',
  assigned_teacher: 'Dr. Sophea Teacher (Read Only)',

  // Step 3: Learning Mode
  learning_mode: 'instructor_led', // 'instructor_led' | 'self_paced'
  
  // Instructor-Led fields
  class_section: 'IT101-A',
  start_date: '2025-06-10',
  end_date: '2025-09-10',
  study_days: ['Mon', 'Wed', 'Fri'],
  start_time: '08:00 AM',
  end_time: '10:00 AM',
  class_type: 'Live Online',
  room_or_link: 'https://zoom.us/j/9876543210',
  capacity: 40,
  attendance_required: true,
  live_reminder: true,

  // Self-Paced fields
  enrollment_type: 'Open Enrollment',
  access_days: 90,
  start_anytime: true,
  lesson_unlock: 'Sequential',
  ai_recommendation: true,
  automatic_quiz: true,
  teacher_support: 'Discussion / Message',

  // Step 4: Fee & ABA
  pricing_type: 'paid', // 'free' | 'paid'
  price: 30.00,
  currency: 'USD',
  discount_price: 25.00,
  discount_end_date: '2025-06-30',
  payment_method: 'ABA PayWay',
  refund_policy: 'Standard Course Refund Policy (7 Days)',

  // Step 5: Enrollment & Access
  enrollment_start_date: '2025-06-01',
  enrollment_end_date: '2025-06-30',
  approval_required: false,
  allow_late_enrollment: false,
  certificate_available: true,
  certificate_requirement: 'Progress 100% + Quiz Score 60%'
})

const effectivePrice = computed(() => {
  if (form.pricing_type === 'free') return 0
  if (form.discount_price !== null && form.discount_price !== undefined && form.discount_price > 0 && form.discount_price < form.price) {
    return form.discount_price
  }
  return form.price || 0
})

const platformFee = computed(() => {
  if (form.pricing_type === 'free') return 0
  return Number((effectivePrice.value * 0.10).toFixed(2))
})

const estimatedEarning = computed(() => {
  if (form.pricing_type === 'free') return 0
  return Math.max(0, effectivePrice.value - platformFee.value)
})

const stepError = ref<string | null>(null)

const validateCurrentStep = (): boolean => {
  stepError.value = null
  if (!form.title || !form.title.trim()) {
    stepError.value = 'Please enter a Course Title before continuing.'
    currentStep.value = 1
    return false
  }
  if (!form.code || !form.code.trim()) {
    stepError.value = 'Please enter a Course Code before continuing.'
    currentStep.value = 1
    return false
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
  if (currentStep.value < 6) {
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

const addOutcome = () => {
  form.outcomes.push('')
}

const removeOutcome = (index: number) => {
  form.outcomes.splice(index, 1)
}

const moveOutcomeUp = (index: number) => {
  if (index > 0) {
    const temp = form.outcomes[index]
    form.outcomes[index] = form.outcomes[index - 1]
    form.outcomes[index - 1] = temp
  }
}

const moveOutcomeDown = (index: number) => {
  if (index < form.outcomes.length - 1) {
    const temp = form.outcomes[index]
    form.outcomes[index] = form.outcomes[index + 1]
    form.outcomes[index + 1] = temp
  }
}

const updateCertRequirement = () => {
  form.certificate_requirement = `Progress ${certProgress.value}% + Quiz Score ${certQuizScore.value}%`
}

const saveAsDraft = () => {
  if (!validateCurrentStep()) return
  form.transform((data) => ({
    ...data,
    is_paid: data.pricing_type === 'paid',
    price: data.pricing_type === 'paid' ? (data.discount_price && data.discount_price > 0 && data.discount_price < data.price ? data.discount_price : data.price) : 0,
    description: data.short_description,
    status: 'draft',
    redirect_action: 'draft'
  })).post(route('teacher.courses.store'))
}

const saveAndGoToBuilder = () => {
  if (!validateCurrentStep()) return
  form.transform((data) => ({
    ...data,
    is_paid: data.pricing_type === 'paid',
    price: data.pricing_type === 'paid' ? (data.discount_price && data.discount_price > 0 && data.discount_price < data.price ? data.discount_price : data.price) : 0,
    description: data.short_description,
    status: 'draft',
    redirect_action: 'workspace'
  })).post(route('teacher.courses.store'))
}

const submitForApproval = () => {
  if (!validateCurrentStep()) return
  form.transform((data) => ({
    ...data,
    is_paid: data.pricing_type === 'paid',
    price: data.pricing_type === 'paid' ? (data.discount_price && data.discount_price > 0 && data.discount_price < data.price ? data.discount_price : data.price) : 0,
    description: data.short_description,
    status: 'pending_approval',
    redirect_action: 'submit'
  })).post(route('teacher.courses.store'))
}

const stepsList = [
  { step: 1, label: 'Basic Info', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
  { step: 2, label: 'Academic', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
  { step: 3, label: 'Learning Mode', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
  { step: 4, label: 'Course Fee & ABA', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
  { step: 5, label: 'Access Rules', icon: 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z' },
  { step: 6, label: 'Review & Submit', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }
]
</script>

<template>
  <TeacherLayout title="Create New Course">
    <Head title="Create New Course — Course Wizard" />

    <div class="space-y-6 max-w-6xl mx-auto pb-16">
      <!-- Breadcrumb Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-800/80 p-5 rounded-2xl border border-slate-700/60 shadow-xl backdrop-blur-xl">
        <div>
          <div class="flex items-center gap-2 text-xs text-indigo-400 font-semibold mb-1">
            <span>Teacher Panel</span>
            <span>/</span>
            <Link href="/teacher/courses" class="hover:underline">My Courses</Link>
            <span>/</span>
            <span class="text-slate-200">Course Creation Wizard</span>
          </div>
          <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
            <span>Create New Course</span>
            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Step {{ currentStep }} of 6</span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">Set up your course step-by-step with basic details, academic structure, mode, ABA fee, and access parameters.</p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
          <button
            @click="router.get(route('teacher.courses.index'))"
            type="button"
            class="px-3.5 py-2 text-xs font-semibold text-slate-300 hover:text-white bg-slate-700/60 hover:bg-slate-700 border border-slate-600 rounded-xl transition-all"
          >
            Cancel
          </button>
          <!-- Hide top Save as Draft button at Step 6 to eliminate duplicate header buttons -->
          <button
            v-if="currentStep < 6"
            @click="saveAsDraft"
            type="button"
            class="px-4 py-2 text-xs font-semibold text-indigo-300 hover:text-white bg-indigo-950/60 hover:bg-indigo-900/80 border border-indigo-500/30 rounded-xl transition-all flex items-center gap-1.5 shadow-sm"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            Save as Draft
          </button>
        </div>
      </div>

      <!-- Stepper Header Indicator Bar (Clickable for completed steps) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-lg overflow-x-auto custom-scrollbar">
        <div class="flex items-center justify-between min-w-[700px]">
          <template v-for="(item, idx) in stepsList" :key="item.step">
            <div
              @click="goToStep(item.step)"
              :class="[
                currentStep === item.step
                  ? 'text-indigo-400 font-bold border-b-2 border-indigo-500 pb-1 cursor-pointer'
                  : item.step <= maxReachedStep
                    ? 'text-emerald-400 font-semibold cursor-pointer hover:text-emerald-300'
                    : 'text-slate-500 font-medium cursor-not-allowed opacity-60',
                'flex items-center gap-2 text-xs transition-all select-none'
              ]"
              :title="item.step <= maxReachedStep ? `Jump to Step ${item.step}` : `Complete previous steps first`"
            >
              <div :class="[
                currentStep === item.step ? 'bg-indigo-600 text-white ring-4 ring-indigo-500/20' :
                item.step <= maxReachedStep ? 'bg-emerald-500 text-slate-950 font-bold hover:scale-105' : 'bg-slate-700 text-slate-400',
                'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 transition-all'
              ]">
                <span v-if="currentStep > item.step || (item.step <= maxReachedStep && currentStep !== item.step)">✓</span>
                <span v-else>{{ item.step }}</span>
              </div>
              <span class="whitespace-nowrap">{{ item.label }}</span>
            </div>

            <div v-if="idx < stepsList.length - 1" class="h-0.5 w-8 bg-slate-700 shrink-0"></div>
          </template>
        </div>
      </div>

      <!-- Validation Error Alert Banner -->
      <div v-if="stepError || Object.keys(form.errors).length > 0" class="p-4 bg-rose-950/50 border border-rose-500/50 rounded-2xl text-rose-200 text-xs font-semibold flex items-center justify-between shadow-lg">
        <div class="flex items-center gap-2">
          <span class="text-rose-400 text-base">⚠️</span>
          <span>{{ stepError || Object.values(form.errors)[0] }}</span>
        </div>
        <button @click="stepError = null; form.clearErrors()" type="button" class="text-rose-400 hover:text-white font-bold px-2 py-0.5 rounded-lg hover:bg-rose-900/40">✕</button>
      </div>

      <!-- Step Content Box -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-6 sm:p-8 shadow-xl backdrop-blur-xl">

        <!-- Step 1: Basic Information -->
        <div v-if="currentStep === 1" class="space-y-6">
          <div class="border-b border-slate-700/80 pb-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
              <span>Step 1 — Basic Course Information</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">Provide course title, code, level, description, thumbnail and key learning objectives.</p>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Course Title <span class="text-rose-500">*</span></label>
              <input
                v-model="form.title"
                type="text"
                placeholder="e.g. C Programming Basics"
                class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Course Code <span class="text-rose-500">*</span></label>
                <input
                  v-model="form.code"
                  type="text"
                  placeholder="e.g. IT101"
                  class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs uppercase font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Course Language <span class="text-rose-500">*</span></label>
                <select
                  v-model="form.language"
                  class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
                  <option value="English / Khmer">English & Khmer (Bilingual)</option>
                  <option value="Khmer">Khmer Only (ភាសាខ្មែរ)</option>
                  <option value="English">English Only</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Course Level <span class="text-rose-500">*</span></label>
                <select
                  v-model="form.level"
                  class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
                  <option value="Beginner">Beginner (កម្រិតដំបូង)</option>
                  <option value="Intermediate">Intermediate (កម្រិតមធ្យម)</option>
                  <option value="Advanced">Advanced (កម្រិតខ្ពស់)</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Course Category <span class="text-rose-500">*</span></label>
                <select
                  v-model="form.category"
                  class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
                  <option value="Programming">Programming & Software Development</option>
                  <option value="Networking">Networking & Cybersecurity</option>
                  <option value="Web Development">Web & Mobile Application Development</option>
                  <option value="Database Systems">Database Systems & Data Analytics</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Short Description <span class="text-rose-500">*</span></label>
              <textarea
                v-model="form.short_description"
                rows="3"
                placeholder="Brief summary of what this course covers..."
                class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              ></textarea>
              <p class="text-[10px] text-slate-500 text-right mt-0.5">{{ form.short_description.length }}/300 characters</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Course Thumbnail Image</label>
              <div class="border-2 border-dashed border-slate-700 hover:border-indigo-500 rounded-2xl p-4 text-center bg-slate-900/50 transition-all cursor-pointer">
                <div class="flex flex-col items-center justify-center gap-1.5">
                  <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  <p class="text-xs font-semibold text-slate-200">Drag & Drop course cover image or <span class="text-indigo-400 underline">Browse File</span></p>
                  <p class="text-[10px] text-slate-500">Supports JPG, PNG, WebP (16:9 ratio, max 5MB)</p>
                </div>
              </div>
            </div>

            <!-- Learning Outcomes List (with ⋮⋮ Drag Handle & Up/Down re-ordering) -->
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="text-xs font-semibold text-slate-300">Learning Outcomes (អ្វីដែលសិស្សទទួលបាន)</label>
                <button @click="addOutcome" type="button" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold">+ Add Outcome</button>
              </div>
              <div class="space-y-2">
                <div v-for="(out, idx) in form.outcomes" :key="idx" class="flex items-center gap-2 group bg-slate-900/60 p-1.5 rounded-xl border border-slate-700/60">
                  <!-- Drag Handle Icon ⋮⋮ & Quick Re-order controls -->
                  <div class="flex items-center gap-1 text-slate-500 pl-1 shrink-0">
                    <svg class="w-4 h-4 cursor-grab text-slate-400 group-hover:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8 9h.01M8 15h.01M12 9h.01M12 15h.01M16 9h.01M16 15h.01"/>
                    </svg>
                    <div class="flex flex-col text-[9px] leading-none">
                      <button @click="moveOutcomeUp(idx)" type="button" :disabled="idx === 0" class="hover:text-indigo-400 disabled:opacity-20 text-slate-400" title="Move Up">▲</button>
                      <button @click="moveOutcomeDown(idx)" type="button" :disabled="idx === form.outcomes.length - 1" class="hover:text-indigo-400 disabled:opacity-20 text-slate-400" title="Move Down">▼</button>
                    </div>
                  </div>
                  <span class="text-xs font-bold text-slate-400 shrink-0">{{ idx + 1 }}.</span>
                  <input
                    v-model="form.outcomes[idx]"
                    type="text"
                    placeholder="Enter learning outcome..."
                    class="w-full px-3 py-1.5 bg-slate-950 border border-slate-700 rounded-lg text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                  />
                  <button @click="removeOutcome(idx)" type="button" class="p-1 text-slate-500 hover:text-rose-400 shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 2: Academic Information -->
        <div v-else-if="currentStep === 2" class="space-y-6">
          <div class="border-b border-slate-700/80 pb-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
              <span>Step 2 — Academic Information</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">Associate this course with your assigned Faculty, Department, Major, and Academic Year.</p>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Faculty <span class="text-rose-500">*</span></label>
              <select
                v-model="form.faculty_id"
                class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
                <option :value="null">Faculty of Science & Technology (មហាវិទ្យាល័យវិទ្យាសាស្ត្រ និងបច្ចេកវិទ្យា)</option>
                <option value="2">Faculty of Tourism & Hospitality (មហាវិទ្យាល័យទេសចរណ៍ និងបដិសណ្ឋារកិច្ច)</option>
                <option value="3">Faculty of Arts, Humanities & Languages (មហាវិទ្យាល័យអក្សរសាស្ត្រ)</option>
                <option value="4">Faculty of Agriculture & Environment (មហាវិទ្យាល័យកសិកម្ម)</option>
                <option value="5">Faculty of Social Sciences (មហាវិទ្យាល័យវិទ្យាសាស្ត្រសង្គម)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Department <span class="text-rose-500">*</span></label>
              <select
                v-model="form.department_id"
                class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
                <option :value="null">Department of Computer Science & IT (ដេប៉ាតឺម៉ង់វិទ្យាសាស្ត្រកុំព្យូទ័រ)</option>
                <option value="2">Department of Telecommunication & Networking</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Major <span class="text-rose-500">*</span></label>
              <select
                v-model="form.major_id"
                class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
                <option v-for="m in props.majors" :key="m.id" :value="m.id">{{ m.name }} ({{ m.name_kh }})</option>
                <option v-if="!props.majors || props.majors.length === 0" :value="1">IT & Networking (បច្ចេកវិទ្យាព័ត៌មាន)</option>
              </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Academic Year <span class="text-rose-500">*</span></label>
                <select
                  v-model="form.academic_year"
                  class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
                  <option value="2025-2026">2025–2026 (Active)</option>
                  <option value="2026-2027">2026–2027</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Academic Term / Semester <span class="text-rose-500">*</span></label>
                <select
                  v-model="form.academic_term"
                  class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
                  <option value="Semester 1">Semester 1 (ឆមាសទី ១)</option>
                  <option value="Semester 2">Semester 2 (ឆមាសទី ២)</option>
                </select>
              </div>
            </div>

            <!-- Distinct Read-Only Disabled State for Assigned Teacher Owner -->
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1 flex items-center justify-between">
                <span>Assigned Teacher Owner</span>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-800 text-slate-400 border border-slate-700 flex items-center gap-1">
                  <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                  Read-Only System Field
                </span>
              </label>
              <div class="w-full px-3.5 py-2.5 bg-slate-950/40 border border-slate-700/40 rounded-xl text-slate-400 text-xs font-semibold flex items-center gap-2 cursor-not-allowed opacity-75 select-none">
                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>{{ form.assigned_teacher }}</span>
              </div>
              <p class="text-[10px] text-slate-500 mt-1">Teachers can only manage courses created by or assigned to themselves by Admin.</p>
            </div>
          </div>
        </div>

        <!-- Step 3: Learning Mode -->
        <div v-else-if="currentStep === 3" class="space-y-6">
          <div class="border-b border-slate-700/80 pb-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
              <span>Step 3 — Choose Learning Mode</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">Select whether this course is Instructor-Led (with live schedule & sections) or Self-Paced (learn anytime with AI support).</p>
          </div>

          <!-- Radio Cards for Mode (Consistent 'Select' / 'Selected' UX pattern) -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
              @click="form.learning_mode = 'instructor_led'"
              :class="[
                form.learning_mode === 'instructor_led'
                  ? 'border-indigo-500 bg-indigo-950/40 ring-2 ring-indigo-500/20'
                  : 'border-slate-700 bg-slate-900/60 hover:border-slate-600',
                'p-5 rounded-2xl border transition-all cursor-pointer'
              ]"
            >
              <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">👨‍🏫</span>
                <span :class="[form.learning_mode === 'instructor_led' ? 'bg-indigo-600 text-white' : 'bg-slate-700 text-slate-400', 'px-2.5 py-0.5 text-[10px] font-bold rounded-full']">
                  {{ form.learning_mode === 'instructor_led' ? 'Selected' : 'Select' }}
                </span>
              </div>
              <h3 class="font-bold text-sm text-white">Instructor-Led (រៀនជាមួយគ្រូ)</h3>
              <p class="text-xs text-slate-400 mt-1 mb-3">Scheduled classes with Section/Class roster, attendance tracking, and live sessions.</p>
              <ul class="text-[11px] text-slate-300 space-y-1">
                <li>✓ Defined schedule & timetable</li>
                <li>✓ Class / Section assignment (e.g. IT101-A)</li>
                <li>✓ Mandatory attendance tracking</li>
                <li>✓ Live Online or Physical Room</li>
              </ul>
            </div>

            <div
              @click="form.learning_mode = 'self_paced'"
              :class="[
                form.learning_mode === 'self_paced'
                  ? 'border-purple-500 bg-purple-950/40 ring-2 ring-purple-500/20'
                  : 'border-slate-700 bg-slate-900/60 hover:border-slate-600',
                'p-5 rounded-2xl border transition-all cursor-pointer'
              ]"
            >
              <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">🧑‍💻</span>
                <span :class="[form.learning_mode === 'self_paced' ? 'bg-purple-600 text-white' : 'bg-slate-700 text-slate-400', 'px-2.5 py-0.5 text-[10px] font-bold rounded-full']">
                  {{ form.learning_mode === 'self_paced' ? 'Selected' : 'Select' }}
                </span>
              </div>
              <h3 class="font-bold text-sm text-white">Self-Paced (រៀនខ្លួនឯង)</h3>
              <p class="text-xs text-slate-400 mt-1 mb-3">Flexible learning anytime, anywhere with AI learning path recommendations and automated quizzes.</p>
              <ul class="text-[11px] text-slate-300 space-y-1">
                <li>✓ Open enrollment & start anytime</li>
                <li>✓ AI recommended learning pace</li>
                <li>✓ Automated progress tracking</li>
                <li>✓ On-demand Q&A discussion support</li>
              </ul>
            </div>
          </div>

          <!-- Mode Specific Configurations -->
          <div v-if="form.learning_mode === 'instructor_led'" class="bg-slate-900/80 p-5 rounded-2xl border border-indigo-500/20 space-y-4">
            <h4 class="text-xs font-bold text-indigo-300 uppercase tracking-wider">Instructor-Led Class Configuration</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Class / Section Code</label>
                <input v-model="form.class_section" type="text" placeholder="e.g. IT101-A" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Student Capacity</label>
                <input v-model.number="form.capacity" type="number" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Start Date</label>
                <input v-model="form.start_date" type="date" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">End Date</label>
                <input v-model="form.end_date" type="date" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Class Type</label>
                <select v-model="form.class_type" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white">
                  <option value="Live Online">Live Online (Zoom / Teams)</option>
                  <option value="Physical Room">Physical Classroom</option>
                  <option value="Blended">Hybrid / Blended</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Room No. / Zoom URL</label>
                <input v-model="form.room_or_link" type="text" placeholder="e.g. Room A-203 or Meeting Link" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white" />
              </div>
            </div>
          </div>

          <div v-else class="bg-slate-900/80 p-5 rounded-2xl border border-purple-500/20 space-y-4">
            <h4 class="text-xs font-bold text-purple-300 uppercase tracking-wider">Self-Paced Course Configuration</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Course Access Duration (Days)</label>
                <input v-model.number="form.access_days" type="number" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Lesson Unlock Order</label>
                <select v-model="form.lesson_unlock" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white">
                  <option value="Sequential">Sequential (រៀនតាមលំដាប់)</option>
                  <option value="All Available">All Unlocked Immediately</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 4: Course Fee & ABA -->
        <div v-else-if="currentStep === 4" class="space-y-6">
          <div class="border-b border-slate-700/80 pb-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
              <span>Step 4 — Course Fee & ABA Payment</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">Configure pricing tier, student discounts, and ABA PayWay integration settings.</p>
          </div>

          <div class="space-y-4">
            <!-- Pricing Type Toggle -->
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-2">Pricing Type</label>
              <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-200">
                  <input type="radio" value="free" v-model="form.pricing_type" class="text-indigo-600 focus:ring-indigo-500" />
                  <span>Free Course ($0.00)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-200">
                  <input type="radio" value="paid" v-model="form.pricing_type" class="text-indigo-600 focus:ring-indigo-500" />
                  <span>Paid Course (ABA PayWay)</span>
                </label>
              </div>
            </div>

            <div v-if="form.pricing_type === 'paid'" class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-300 mb-1">Standard Course Price ($ USD) <span class="text-rose-500">*</span></label>
                  <input
                    v-model.number="form.price"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="30.00"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500"
                  />
                </div>

                <div>
                  <label class="block text-xs font-semibold text-slate-300 mb-1 flex items-center justify-between">
                    <span>Discount Promotional Price ($ USD)</span>
                    <span class="text-[10px] text-slate-400 font-normal">Optional / ទុកទំនេរប្រសិនបើគ្មានការបញ្ចុះតម្លៃ</span>
                  </label>
                  <input
                    v-model.number="form.discount_price"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="25.00"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-500"
                  />
                  <p class="text-[10px] text-slate-400 mt-1">Leave blank if no discount applies (or equal to Standard Price).</p>
                </div>
              </div>

              <!-- Payment Gateway & Financial Summary Card (Live Calculation based on Discounted Price) -->
              <div class="p-4 rounded-2xl bg-gradient-to-r from-emerald-950/30 to-slate-900 border border-emerald-500/30 shadow-md space-y-3">
                <div class="flex items-center justify-between border-b border-emerald-500/20 pb-2">
                  <span class="text-xs font-bold text-emerald-300 flex items-center gap-2">
                    <span>💳 Payment Gateway:</span>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px]">ABA PayWay — Active</span>
                  </span>
                  <span class="text-[10px] text-slate-400">USD & KHR DeepLink</span>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                  <div class="bg-slate-900/60 p-2.5 rounded-xl border border-slate-800">
                    <p class="text-[10px] text-slate-400">Student Pays</p>
                    <p class="font-bold text-white text-sm">${{ Number(effectivePrice).toFixed(2) }}</p>
                    <p v-if="form.discount_price && form.discount_price < form.price" class="text-[9px] text-emerald-400 font-medium">Promo price (${{ Number(form.price || 0).toFixed(2) }} reg)</p>
                  </div>
                  <div class="bg-slate-900/60 p-2.5 rounded-xl border border-slate-800">
                    <p class="text-[10px] text-slate-400">Platform Fee (10%)</p>
                    <p class="font-bold text-slate-400 text-sm">${{ Number(platformFee).toFixed(2) }}</p>
                  </div>
                  <div class="bg-slate-900/60 p-2.5 rounded-xl border border-slate-800">
                    <p class="text-[10px] text-emerald-400 font-semibold">Teacher Earning (90%)</p>
                    <p class="font-bold text-emerald-400 text-sm">${{ Number(estimatedEarning).toFixed(2) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 5: Enrollment & Access Rules -->
        <div v-else-if="currentStep === 5" class="space-y-6">
          <div class="border-b border-slate-700/80 pb-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
              <span>Step 5 — Enrollment & Access Rules</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">Configure course enrollment dates, maximum seat limits, and completion certificate requirements.</p>
          </div>

          <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Enrollment Start Date</label>
                <input v-model="form.enrollment_start_date" type="date" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs" />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Enrollment End Date</label>
                <input v-model="form.enrollment_end_date" type="date" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 text-xs" />
              </div>
            </div>

            <div class="flex items-center justify-between p-3.5 bg-slate-900/80 rounded-xl border border-slate-700/60">
              <div>
                <p class="text-xs font-semibold text-white">Certificate Availability</p>
                <p class="text-[10px] text-slate-400">Automatically issue certificate upon course completion</p>
              </div>
              <input type="checkbox" v-model="form.certificate_available" class="w-4 h-4 text-indigo-600 rounded" />
            </div>

            <!-- Customizable Certificate Issuance Requirement -->
            <div v-if="form.certificate_available" class="bg-slate-900/80 p-4 rounded-xl border border-slate-700/60 space-y-3">
              <label class="block text-xs font-semibold text-slate-300">Certificate Issuance Requirement Settings</label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label class="block text-[11px] text-slate-400 mb-1">Min Required Progress (%)</label>
                  <select v-model.number="certProgress" @change="updateCertRequirement" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white">
                    <option :value="100">100% Course Completion (System Default)</option>
                    <option :value="90">90% Completion</option>
                    <option :value="80">80% Completion</option>
                  </select>
                </div>
                <div>
                  <label class="block text-[11px] text-slate-400 mb-1">Min Quiz Passing Score (%)</label>
                  <select v-model.number="certQuizScore" @change="updateCertRequirement" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white">
                    <option :value="60">60% Minimum Score (System Default)</option>
                    <option :value="50">50% Minimum Score</option>
                    <option :value="70">70% Minimum Score</option>
                    <option :value="80">80% Minimum Score</option>
                  </select>
                </div>
              </div>
              <p class="text-[10px] text-indigo-300 font-mono">Current Requirement Rule: {{ form.certificate_requirement }}</p>
            </div>
          </div>
        </div>

        <!-- Step 6: Review & Submit -->
        <div v-else-if="currentStep === 6" class="space-y-6">
          <div class="border-b border-slate-700/80 pb-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
              <span>Step 6 — Review & Submit Course</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">Review your course summary and submission checklist before publishing or sending for Admin approval.</p>
          </div>

          <!-- Course Summary Preview Card -->
          <div class="p-5 bg-gradient-to-r from-slate-900 to-indigo-950/40 rounded-2xl border border-indigo-500/30 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-700/80 pb-3">
              <div>
                <span class="text-xs font-mono font-bold text-indigo-400 uppercase tracking-wider">{{ form.code }}</span>
                <h3 class="text-base font-bold text-white">{{ form.title }}</h3>
              </div>
              <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 self-start sm:self-auto">
                Ready for Approval
              </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
              <div>
                <p class="text-slate-400 text-[10px]">Learning Mode</p>
                <p class="font-semibold text-slate-200 capitalize">{{ form.learning_mode.replace('_', ' ') }}</p>
              </div>
              <div>
                <p class="text-slate-400 text-[10px]">Price Fee</p>
                <p class="font-semibold text-emerald-400">{{ form.pricing_type === 'free' ? 'Free' : `$${Number(effectivePrice).toFixed(2)} via ABA` }}</p>
              </div>
              <div>
                <p class="text-slate-400 text-[10px]">Capacity</p>
                <p class="font-semibold text-slate-200">{{ form.capacity }} Students</p>
              </div>
              <div>
                <p class="text-slate-400 text-[10px]">Academic Year</p>
                <p class="font-semibold text-slate-200">{{ form.academic_year }}</p>
              </div>
            </div>
          </div>

          <!-- Completion Checklist -->
          <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/60 space-y-2 text-xs">
            <h4 class="font-bold text-slate-200">Completion Checklist</h4>
            <div class="flex items-center gap-2 text-emerald-400 font-medium"><span>✅ Basic Information complete</span></div>
            <div class="flex items-center gap-2 text-emerald-400 font-medium"><span>✅ Academic Information complete</span></div>
            <div class="flex items-center gap-2 text-emerald-400 font-medium"><span>✅ Learning Mode configured</span></div>
            <div class="flex items-center gap-2 text-emerald-400 font-medium"><span>✅ Course Fee & ABA PayWay configured</span></div>
            <div class="flex items-center gap-2 text-amber-400 font-medium"><span>⚠️ Course Modules & Lessons can be added in Course Builder after saving draft.</span></div>
          </div>
        </div>

        <!-- Wizard Navigation Controls (Bottom Bar) -->
        <div class="flex items-center justify-between border-t border-slate-700/80 pt-6 mt-6">
          <button
            v-if="currentStep > 1"
            @click="prevStep"
            type="button"
            class="px-4 py-2 text-xs font-semibold text-slate-300 hover:text-white bg-slate-700/60 hover:bg-slate-700 rounded-xl transition-all flex items-center gap-1 cursor-pointer"
          >
            ← Back
          </button>
          <div v-else></div>

          <div class="flex items-center gap-2">
            <button
              v-if="currentStep < 6"
              @click="nextStep"
              type="button"
              class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all flex items-center gap-1 shadow-md shadow-indigo-600/30 cursor-pointer"
            >
              Save & Continue →
            </button>
            <template v-else>
              <button
                @click="saveAsDraft"
                type="button"
                class="px-4 py-2.5 text-xs font-semibold text-slate-300 hover:text-white bg-slate-700/60 hover:bg-slate-700 rounded-xl transition-all cursor-pointer"
              >
                Save as Draft
              </button>
              <button
                @click="saveAndGoToBuilder"
                type="button"
                class="px-5 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 rounded-xl transition-all shadow-lg shadow-indigo-600/30 flex items-center gap-1.5 cursor-pointer ring-2 ring-indigo-400/30"
              >
                <span>⭐ Save & Go to Course Builder</span>
              </button>
              <button
                @click="submitForApproval"
                type="button"
                class="px-5 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 rounded-xl transition-all shadow-lg shadow-emerald-600/30 cursor-pointer"
              >
                Submit for Approval
              </button>
            </template>
          </div>
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
