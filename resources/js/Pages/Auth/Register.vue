<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

const logoUrl = '/images/logo.png'

interface Major {
  id: number
  name: string
  name_kh?: string
  department?: {
    id: number
    name: string
    faculty?: {
      id: number
      name: string
    }
  }
}

const props = defineProps<{
  majors?: Major[]
}>()

const step = ref<1 | 2 | 3>(1)
const maxStepReached = ref<number>(1)
const isDark = ref(true)

const initTheme = () => {
  const saved = localStorage.getItem('theme')
  if (saved) {
    isDark.value = saved === 'dark'
  } else {
    isDark.value = true
  }
  applyTheme()
}

const toggleTheme = () => {
  isDark.value = !isDark.value
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
  applyTheme()
}

const applyTheme = () => {
  if (isDark.value) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
}

onMounted(() => {
  initTheme()
})

const form = useForm({
  name: '',
  name_kh: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  role: 'student' as 'student' | 'teacher',
  invitation_code: '',
  major_id: null as number | null,
  study_type: 'on_campus' as 'on_campus' | 'online',
  payment_method: 'aba' as 'aba' | 'cash',
  receipt: null as File | null,
  payment_confirmed: false,
  terms: false,
})

const showPassword = ref(false)
const receiptPreview = ref<string | null>(null)

// Fallback mappings for major details
const majorDetailsMap: Record<string, { faculty: string; department: string }> = {
  'IT & Networking': {
    faculty: 'Faculty of Science & Technology',
    department: 'Dept of IT & Networking',
  },
  'Tourism Management': {
    faculty: 'Faculty of Tourism & Hospitality',
    department: 'Dept of Tourism & Hospitality',
  },
  'English Literature': {
    faculty: 'Faculty of Arts & Humanities',
    department: 'Dept of English Literature',
  },
  'Agronomy': {
    faculty: 'Faculty of Agriculture',
    department: 'Dept of Agronomy & Crop Science',
  },
  'Social Work': {
    faculty: 'Faculty of Social Sciences',
    department: 'Dept of Social Work',
  },
}

// Student ID preview
const studentIdDisplay = computed(() => {
  return 'STU-2026-' + Math.floor(10000 + Math.random() * 90000)
})

// Selected Major Details
const selectedMajorObj = computed(() => {
  if (!form.major_id || !props.majors) return null
  return props.majors.find(m => m.id === form.major_id) || null
})

const facultyName = computed(() => {
  if (!selectedMajorObj.value) return 'Select a major first'
  if (selectedMajorObj.value.department?.faculty?.name) {
    return selectedMajorObj.value.department.faculty.name
  }
  return majorDetailsMap[selectedMajorObj.value.name]?.faculty || 'Faculty of Applied Sciences'
})

const departmentName = computed(() => {
  if (!selectedMajorObj.value) return 'Select a major first'
  if (selectedMajorObj.value.department?.name) {
    return selectedMajorObj.value.department.name
  }
  return majorDetailsMap[selectedMajorObj.value.name]?.department || 'Academic Department'
})

const isPasswordValid = computed(() => {
  return form.password.length >= 8
})

const isStep1Valid = computed(() => {
  const basicValid = (
    form.name.trim() !== '' &&
    form.email.includes('@') &&
    form.phone.trim() !== '' &&
    isPasswordValid.value &&
    form.password === form.password_confirmation &&
    form.terms
  )

  if (form.role === 'teacher') {
    return basicValid && form.invitation_code.trim() !== ''
  }

  return basicValid
})

const isStep2Valid = computed(() => {
  return form.major_id !== null && form.study_type !== null
})

const goToStep = (targetStep: 1 | 2 | 3) => {
  if (targetStep === 1) {
    step.value = 1
  } else if (targetStep === 2 && isStep1Valid.value) {
    step.value = 2
    if (maxStepReached.value < 2) maxStepReached.value = 2
  } else if (targetStep === 3 && isStep1Valid.value && isStep2Valid.value) {
    step.value = 3
    if (maxStepReached.value < 3) maxStepReached.value = 3
  }
}

const nextStep = () => {
  if (step.value === 1 && isStep1Valid.value) {
    step.value = 2
    if (maxStepReached.value < 2) maxStepReached.value = 2
  } else if (step.value === 2 && isStep2Valid.value) {
    step.value = 3
    if (maxStepReached.value < 3) maxStepReached.value = 3
  }
}

const prevStep = () => {
  if (step.value > 1) {
    step.value = (step.value - 1) as 1 | 2 | 3
  }
}

// Auto-navigate on backend validation errors
watch(
  () => form.errors,
  (errors) => {
    if (!errors || Object.keys(errors).length === 0) return

    const step1Fields = ['name', 'name_kh', 'email', 'phone', 'password', 'password_confirmation', 'terms', 'invitation_code']
    const step2Fields = ['major_id', 'study_type']

    if (Object.keys(errors).some((key) => step1Fields.includes(key))) {
      step.value = 1
    } else if (Object.keys(errors).some((key) => step2Fields.includes(key))) {
      step.value = 2
    }
  },
  { deep: true }
)

const handleFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    form.receipt = file
    receiptPreview.value = URL.createObjectURL(file)
  }
}

const submit = () => {
  form.post('/register', {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-100 dark:bg-[#0B132B] text-slate-900 dark:text-slate-100 px-3 py-4 relative font-sans overflow-hidden transition-colors duration-300">
    
    <!-- Top-Right Fixed Screen Theme Switcher (As requested) -->
    <div class="fixed top-4 right-4 sm:top-6 sm:right-6 z-50">
      <button
        type="button"
        @click="toggleTheme"
        class="p-2 sm:px-3 sm:py-2 rounded-xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-md hover:bg-white dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition border border-slate-300/80 dark:border-slate-700/60 shadow-lg flex items-center gap-2 text-xs font-semibold cursor-pointer select-none"
        :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
      >
        <i :class="['pi text-sm', isDark ? 'pi-sun text-amber-400' : 'pi-moon text-indigo-500']"></i>
        <span class="hidden sm:inline">{{ isDark ? 'Light Mode' : 'Dark Mode' }}</span>
      </button>
    </div>

    <!-- Background Pattern & Ambient Glows -->
    <div class="absolute inset-0 bg-[radial-gradient(#CBD5E1_1px,transparent_1px)] dark:bg-[radial-gradient(#1E2951_1px,transparent_1px)] [background-size:24px_24px] opacity-30 dark:opacity-15 pointer-events-none"></div>
    <div class="absolute -top-32 -right-32 w-80 h-80 bg-blue-500/10 dark:bg-blue-600/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-indigo-500/10 dark:bg-indigo-600/5 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Glassmorphism Card Container -->
    <div class="max-w-[500px] w-full p-[1px] rounded-2xl bg-gradient-to-b from-blue-500/30 via-slate-200 to-slate-300 dark:from-blue-500/25 dark:via-[#1C2951] dark:to-slate-800/40 shadow-2xl shadow-slate-300/60 dark:shadow-blue-950/40 relative z-10 my-auto transition-all">
      <div class="w-full bg-white/95 dark:bg-[#111A36]/95 backdrop-blur-xl rounded-[15px] overflow-hidden transition-colors">
        
        <!-- Sleek Header -->
        <div class="bg-slate-50/90 dark:bg-[#0E1630]/90 border-b border-slate-200 dark:border-[#1C2951] px-6 py-4 text-center space-y-1.5 relative">
          <!-- Soft Accent Light Line -->
          <div class="absolute top-0 left-1/4 right-1/4 h-[1px] bg-gradient-to-r from-transparent via-blue-500/40 dark:via-blue-400/30 to-transparent"></div>

          <div class="flex flex-col items-center justify-center gap-2 pb-0.5">
            <img
              :src="logoUrl"
              alt="E-LMS Logo"
              class="w-16 h-16 rounded-full shadow-md object-cover ring-2 ring-blue-500/30 ring-offset-2 ring-offset-slate-50 dark:ring-offset-[#0E1630]"
            />
            <span class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">CREATE NEW ACCOUNT</span>
          </div>
        
          <div class="flex items-center justify-between text-[11px] font-semibold text-blue-600 dark:text-blue-400 max-w-sm mx-auto px-1">
            <span>STEP {{ step }} OF 3</span>
            <span class="text-slate-500 dark:text-slate-400 font-normal">
              {{ step === 1 ? 'ACCOUNT INFO' : step === 2 ? 'ACADEMICS' : 'PAYMENT' }}
            </span>
          </div>

          <!-- Progress Pills -->
          <div class="grid grid-cols-3 gap-1.5 pt-0.5 max-w-sm mx-auto">
            <button
              type="button"
              @click="goToStep(1)"
              :class="['h-1.5 rounded-full transition-all duration-300', step >= 1 ? 'bg-blue-600 shadow-sm shadow-blue-500/50' : 'bg-slate-200 dark:bg-[#1C2951]']"
            ></button>
            <button
              type="button"
              @click="goToStep(2)"
              :disabled="!isStep1Valid"
              :class="['h-1.5 rounded-full transition-all duration-300 disabled:opacity-40', step >= 2 ? 'bg-blue-600 shadow-sm shadow-blue-500/50' : 'bg-slate-200 dark:bg-[#1C2951]']"
            ></button>
            <button
              type="button"
              @click="goToStep(3)"
              :disabled="!isStep1Valid || !isStep2Valid"
              :class="['h-1.5 rounded-full transition-all duration-300 disabled:opacity-40', step >= 3 ? 'bg-blue-600 shadow-sm shadow-blue-500/50' : 'bg-slate-200 dark:bg-[#1C2951]']"
            ></button>
          </div>
        </div>

        <div class="p-5 sm:p-6 space-y-3.5">

          <!-- Role Selector Pills -->
          <div class="bg-slate-100 dark:bg-[#0A1024] p-1 rounded-xl border border-slate-200 dark:border-[#1C2951] grid grid-cols-2 gap-1 text-xs">
            <button
              type="button"
              @click="form.role = 'student'"
              :class="[
                'py-1.5 px-2 rounded-lg font-bold transition flex items-center justify-center gap-1.5 select-none text-[11px]',
                form.role === 'student' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
              ]"
            >
              <i class="pi pi-user text-xs"></i>
              <span>Student (និស្សិត)</span>
            </button>

            <button
              type="button"
              @click="form.role = 'teacher'"
              :class="[
                'py-1.5 px-2 rounded-lg font-bold transition flex items-center justify-center gap-1.5 select-none text-[11px]',
                form.role === 'teacher' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
              ]"
            >
              <i class="pi pi-id-card text-xs"></i>
              <span>Teacher (លោកគ្រូ/អ្នកគ្រូ)</span>
            </button>
          </div>

          <!-- Single-line Subtle Hint for Student Mode -->
          <div v-if="form.role === 'student'" class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 px-1 pt-0.5">
            <span class="flex items-center gap-1">
              <i class="pi pi-info-circle text-blue-500 text-xs"></i>
              <span>ចុះឈ្មោះសម្រាប់ <b>Student (និស្សិត)</b></span>
            </span>
            <button type="button" @click="form.role = 'teacher'" class="text-sky-600 dark:text-sky-400 hover:text-sky-500 font-semibold text-[11px]">
              ចុះឈ្មោះ Teacher?
            </button>
          </div>

          <!-- Soft Cyan Alert Box for Teacher Mode -->
          <div v-else class="bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-800/40 rounded-xl p-3 text-[11px] text-cyan-800 dark:text-cyan-200 space-y-2 shadow-sm">
            <div class="flex items-start gap-2">
              <i class="pi pi-shield text-sm text-cyan-600 dark:text-cyan-400 shrink-0 mt-0.5"></i>
              <span class="leading-tight font-medium">
                Account សម្រាប់ <b>Teacher</b> ត្រូវប្រើ <b>Teacher Invitation Code</b> (ឧ. <code>TEACHER-2026-INVITE</code>)។
              </span>
            </div>
            
            <div class="space-y-1 pt-1.5 border-t border-cyan-200 dark:border-cyan-800/30">
              <label class="block text-[10px] font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-300 flex items-center gap-1">
                <i class="pi pi-key text-[10px]"></i> Teacher Invitation Code *
              </label>
              <input
                v-model="form.invitation_code"
                type="text"
                required
                placeholder="TEACHER-2026-INVITE"
                class="w-full px-3 py-1.5 rounded-lg border border-cyan-300 dark:border-cyan-600/40 bg-white dark:bg-[#0B132B] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-cyan-500 font-mono text-xs font-medium"
              />
              <span v-if="form.errors.invitation_code" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ form.errors.invitation_code }}</span>
            </div>
          </div>

          <!-- Global Error Banner -->
          <div v-if="Object.keys(form.errors).length > 0" class="bg-red-500/10 border border-red-500/30 rounded-xl p-2.5 text-red-600 dark:text-red-300 text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5 text-red-500 dark:text-red-400 text-[11px]">
              <i class="pi pi-exclamation-circle text-sm"></i> Please fix errors:
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-slate-700 dark:text-slate-300 font-medium text-[11px]">
              <li v-for="(err, key) in form.errors" :key="key">{{ err }}</li>
            </ul>
          </div>

          <form @submit.prevent="submit" class="space-y-3">
            
            <!-- STEP 1: ACCOUNT INFORMATION -->
            <div v-if="step === 1" class="space-y-3">
              
              <!-- Row 1: Full Name KH | Full Name EN -->
              <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                  <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider inline-flex items-center gap-1.5">
                    <i class="pi pi-user text-blue-500 text-xs shrink-0"></i> <span>Full Name (KH)</span>
                  </label>
                  <input
                    v-model="form.name_kh"
                    type="text"
                    placeholder="ចាន់ ដារា"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-[#1C2951] bg-white dark:bg-[#0A1024]/80 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-medium"
                  />
                  <span v-if="form.errors.name_kh" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ form.errors.name_kh }}</span>
                </div>

                <div class="space-y-1">
                  <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider inline-flex items-center gap-1.5">
                    <i class="pi pi-user text-blue-500 text-xs shrink-0"></i> <span>Full Name (EN) *</span>
                  </label>
                  <input
                    v-model="form.name"
                    type="text"
                    required
                    placeholder="Chan Dara"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-[#1C2951] bg-white dark:bg-[#0A1024]/80 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-medium"
                  />
                  <span v-if="form.errors.name" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ form.errors.name }}</span>
                </div>
              </div>

              <!-- Row 2: Email | Phone Number -->
              <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                  <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider inline-flex items-center gap-1.5">
                    <i class="pi pi-envelope text-blue-500 text-xs shrink-0"></i> <span>Email *</span>
                  </label>
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    placeholder="chan.dara@student.edu"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-[#1C2951] bg-white dark:bg-[#0A1024]/80 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-medium"
                  />
                  <span v-if="form.errors.email" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ form.errors.email }}</span>
                </div>

                <div class="space-y-1">
                  <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider inline-flex items-center gap-1.5">
                    <i class="pi pi-phone text-blue-500 text-xs shrink-0"></i> <span>Phone Number *</span>
                  </label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    required
                    placeholder="+855 12 345 678"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-[#1C2951] bg-white dark:bg-[#0A1024]/80 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-medium"
                  />
                  <span v-if="form.errors.phone" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ form.errors.phone }}</span>
                </div>
              </div>

              <!-- Row 3: Password | Confirm Password -->
              <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                  <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider inline-flex items-center gap-1.5">
                    <i class="pi pi-lock text-blue-500 text-xs shrink-0"></i> <span>Password *</span>
                  </label>
                  <div class="relative">
                    <input
                      v-model="form.password"
                      :type="showPassword ? 'text' : 'password'"
                      required
                      placeholder="••••••••••••••••"
                      class="w-full pl-3 pr-9 py-2.5 rounded-xl border border-slate-300 dark:border-[#1C2951] bg-white dark:bg-[#0A1024]/80 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-medium"
                    />
                    <button
                      type="button"
                      @click="showPassword = !showPassword"
                      class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-xs"
                    >
                      <i :class="['pi text-[11px] shrink-0', showPassword ? 'pi-eye-slash' : 'pi-eye']"></i>
                    </button>
                  </div>
                  <span v-if="form.errors.password" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ form.errors.password }}</span>
                </div>

                <div class="space-y-1">
                  <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider inline-flex items-center gap-1.5">
                    <i class="pi pi-lock text-blue-500 text-xs shrink-0"></i> <span>Confirm Password *</span>
                  </label>
                  <input
                    v-model="form.password_confirmation"
                    :type="showPassword ? 'text' : 'password'"
                    required
                    placeholder="••••••••••••••••"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-[#1C2951] bg-white dark:bg-[#0A1024]/80 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-medium"
                  />
                  <span v-if="form.password && form.password_confirmation && form.password !== form.password_confirmation" class="text-[10px] text-red-500 dark:text-red-400 block flex items-center gap-1 font-medium">
                    <i class="pi pi-exclamation-triangle"></i> Passwords do not match
                  </span>
                  <span v-if="form.errors.password_confirmation" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ form.errors.password_confirmation }}</span>
                </div>
              </div>

              <!-- Terms Checkbox -->
              <div class="pt-0.5">
                <label class="flex items-center space-x-2 cursor-pointer select-none">
                  <input
                    v-model="form.terms"
                    type="checkbox"
                    required
                    class="rounded border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-[#0A1024] text-blue-600 focus:ring-blue-500 h-3.5 w-3.5 cursor-pointer"
                  />
                  <span class="text-[11px] text-slate-700 dark:text-slate-300 font-medium">
                    I agree to Terms of Service & Privacy Policy
                  </span>
                </label>
                <span v-if="form.errors.terms" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ form.errors.terms }}</span>
              </div>

              <button
                type="button"
                @click="nextStep"
                :disabled="!isStep1Valid"
                class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-md transition flex items-center justify-center gap-1.5 disabled:opacity-50 uppercase tracking-wider text-xs cursor-pointer"
              >
                <span>Continue to Step 2</span>
                <i class="pi pi-arrow-right text-[11px]"></i>
              </button>
            </div>

            <!-- STEP 2: ACADEMIC SELECTION -->
            <div v-if="step === 2" class="space-y-3">
              
              <!-- Major Dropdown -->
              <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1">
                  <i class="pi pi-graduation-cap text-blue-500 text-[10px]"></i> Select Major *
                </label>
                <select
                  v-model="form.major_id"
                  required
                  class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-[#1C2951] bg-white dark:bg-[#0A1024] text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 transition text-xs font-medium"
                >
                  <option :value="null" disabled>-- Choose Your Major --</option>
                  <option v-for="m in props.majors" :key="m.id" :value="m.id">
                    {{ m.name }} {{ m.name_kh ? `(${m.name_kh})` : '' }}
                  </option>
                </select>
                <span v-if="form.errors.major_id" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ form.errors.major_id }}</span>
              </div>

              <!-- Auto-filled Faculty & Department Box -->
              <div class="bg-slate-50 dark:bg-[#0A1024]/70 border border-slate-200 dark:border-[#1C2951] rounded-xl p-3 space-y-2 text-xs">
                <div>
                  <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block flex items-center gap-1">
                    <i class="pi pi-building text-blue-500"></i> Faculty (Auto-fill)
                  </span>
                  <span class="text-xs font-bold text-blue-600 dark:text-blue-300">{{ facultyName }}</span>
                </div>
                <div class="border-t border-slate-200 dark:border-[#1C2951] pt-1.5">
                  <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block flex items-center gap-1">
                    <i class="pi pi-sitemap text-indigo-500"></i> Department (Auto-fill)
                  </span>
                  <span class="text-xs font-bold text-indigo-600 dark:text-indigo-300">{{ departmentName }}</span>
                </div>
              </div>

              <!-- Study Type Choices -->
              <div class="space-y-1.5">
                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1">
                  <i class="pi pi-book text-blue-500 text-[10px]"></i> Study Type *
                </label>
                <div class="grid grid-cols-2 gap-2">
                  <label :class="[
                    'p-2.5 rounded-xl border cursor-pointer transition flex items-center gap-2 text-xs font-semibold select-none',
                    form.study_type === 'online'
                      ? 'bg-blue-500/10 dark:bg-blue-600/20 border-blue-500 text-blue-600 dark:text-blue-300'
                      : 'bg-slate-50 dark:bg-[#0A1024]/80 border-slate-200 dark:border-[#1C2951] text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
                  ]">
                    <input type="radio" v-model="form.study_type" value="online" class="sr-only" />
                    <i class="pi pi-globe text-xs text-blue-500"></i>
                    <span>Online / Distance</span>
                  </label>

                  <label :class="[
                    'p-2.5 rounded-xl border cursor-pointer transition flex items-center gap-2 text-xs font-semibold select-none',
                    form.study_type === 'on_campus'
                      ? 'bg-emerald-500/10 dark:bg-emerald-600/20 border-emerald-500 text-emerald-600 dark:text-emerald-300'
                      : 'bg-slate-50 dark:bg-[#0A1024]/80 border-slate-200 dark:border-[#1C2951] text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
                  ]">
                    <input type="radio" v-model="form.study_type" value="on_campus" class="sr-only" />
                    <i class="pi pi-building text-xs text-emerald-500"></i>
                    <span>On-Campus</span>
                  </label>
                </div>
                <span v-if="form.errors.study_type" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ form.errors.study_type }}</span>
              </div>

              <!-- Auto-generated Student ID Badge -->
              <div v-if="form.role === 'student'" class="bg-slate-100 dark:bg-[#0B132B] border border-slate-200 dark:border-[#1C2951] rounded-xl p-2.5 flex items-center justify-between">
                <span class="text-[11px] text-slate-600 dark:text-slate-400 font-semibold uppercase tracking-wider flex items-center gap-1">
                  <i class="pi pi-id-card text-blue-500"></i> Student ID (Auto):
                </span>
                <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-950/80 px-2 py-0.5 rounded-md border border-emerald-300 dark:border-emerald-800/50">
                  {{ studentIdDisplay }}
                </span>
              </div>

              <!-- Step 2 Navigation Buttons -->
              <div class="grid grid-cols-2 gap-2.5 pt-1">
                <button
                  type="button"
                  @click="prevStep"
                  class="py-2.5 bg-slate-200 dark:bg-[#0A1024] hover:bg-slate-300 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold rounded-xl border border-slate-300 dark:border-[#1C2951] transition uppercase tracking-wider text-xs flex items-center justify-center gap-1 cursor-pointer"
                >
                  <i class="pi pi-arrow-left text-[10px]"></i>
                  <span>Back</span>
                </button>

                <button
                  v-if="form.role === 'student'"
                  type="button"
                  @click="nextStep"
                  :disabled="!isStep2Valid"
                  class="py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-md transition uppercase tracking-wider text-xs flex items-center justify-center gap-1 disabled:opacity-50 cursor-pointer"
                >
                  <span>Continue to Step 3</span>
                  <i class="pi pi-arrow-right text-[10px]"></i>
                </button>

                <button
                  v-else
                  type="submit"
                  :disabled="form.processing || !isStep2Valid"
                  class="py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-md transition uppercase tracking-wider text-xs flex items-center justify-center gap-1.5 disabled:opacity-50 cursor-pointer"
                >
                  <i v-if="form.processing" class="pi pi-spin pi-spinner text-xs"></i>
                  <i v-else class="pi pi-check-circle text-xs"></i>
                  <span>{{ form.processing ? 'Registering...' : 'REGISTER TEACHER' }}</span>
                </button>
              </div>

            </div>

            <!-- STEP 3: PAYMENT VERIFICATION (STUDENT ONLY) -->
            <div v-if="step === 3 && form.role === 'student'" class="space-y-3">
              
              <!-- Summary Box -->
              <div class="bg-slate-50 dark:bg-[#0A1024]/70 border border-slate-200 dark:border-[#1C2951] rounded-xl p-3 space-y-1.5 text-xs">
                <div class="font-bold text-slate-800 dark:text-slate-200 text-xs flex items-center gap-1">
                  <i class="pi pi-list text-blue-500"></i> Summary:
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 text-slate-600 dark:text-slate-300 font-medium text-[11px]">
                  <div>• <b>Student:</b> {{ form.name }}</div>
                  <div>• <b>Major:</b> {{ selectedMajorObj?.name || 'Selected' }}</div>
                  <div>• <b>Type:</b> {{ form.study_type === 'online' ? 'Online' : 'On-Campus' }}</div>
                </div>
              </div>

              <!-- Fee Breakdown -->
              <div class="bg-slate-100 dark:bg-[#0B132B] border border-slate-200 dark:border-[#1C2951] rounded-xl p-3 space-y-1.5 text-[11px]">
                <div class="font-bold text-slate-800 dark:text-slate-200 text-xs flex items-center gap-1 mb-0.5">
                  <i class="pi pi-wallet text-blue-500"></i> Fees:
                </div>
                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400 font-medium">
                  <span>• Registration Fee:</span>
                  <span class="font-mono">$10.00</span>
                </div>
                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400 font-medium">
                  <span>• First Semester Tuition:</span>
                  <span class="font-mono">$350.00</span>
                </div>
                <div class="border-t border-slate-200 dark:border-[#1C2951] pt-1.5 flex items-center justify-between font-bold text-slate-900 dark:text-slate-100 text-xs">
                  <span>• Subtotal:</span>
                  <span class="font-mono text-emerald-600 dark:text-emerald-400">
                    {{ form.payment_method === 'aba' ? '$324.00 (10% Off ABA Special)' : '$360.00' }}
                  </span>
                </div>
              </div>

              <!-- Payment Method Choice -->
              <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1">
                  <i class="pi pi-credit-card text-blue-500 text-[10px]"></i> Payment Method:
                </label>
                <div class="grid grid-cols-2 gap-2">
                  <label :class="[
                    'p-2.5 rounded-xl border cursor-pointer transition flex items-center gap-2 text-xs font-semibold select-none',
                    form.payment_method === 'aba'
                      ? 'bg-blue-500/10 dark:bg-blue-600/20 border-blue-500 text-blue-600 dark:text-blue-300'
                      : 'bg-slate-50 dark:bg-[#0A1024]/80 border-slate-200 dark:border-[#1C2951] text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
                  ]">
                    <input type="radio" v-model="form.payment_method" value="aba" class="sr-only" />
                    <i class="pi pi-qrcode text-blue-500 text-xs"></i>
                    <span>ABA Bank (10% Off)</span>
                  </label>

                  <label :class="[
                    'p-2.5 rounded-xl border cursor-pointer transition flex items-center gap-2 text-xs font-semibold select-none',
                    form.payment_method === 'cash'
                      ? 'bg-emerald-500/10 dark:bg-emerald-600/20 border-emerald-500 text-emerald-600 dark:text-emerald-300'
                      : 'bg-slate-50 dark:bg-[#0A1024]/80 border-slate-200 dark:border-[#1C2951] text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
                  ]">
                    <input type="radio" v-model="form.payment_method" value="cash" class="sr-only" />
                    <i class="pi pi-money-bill text-emerald-500 text-xs"></i>
                    <span>Cash at Campus</span>
                  </label>
                </div>
              </div>

              <!-- ABA Payment QR Code Box -->
              <div v-if="form.payment_method === 'aba'" class="bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800/40 rounded-xl p-3 text-center space-y-1.5">
                <div class="text-[11px] font-bold text-blue-700 dark:text-blue-300 uppercase tracking-wider flex items-center justify-center gap-1">
                  <i class="pi pi-qrcode text-blue-500"></i> ABA PAYMENT QR CODE
                </div>
                <div class="w-24 h-24 mx-auto bg-white rounded-xl p-1.5 flex items-center justify-center border border-blue-400 shadow-md">
                  <div class="w-full h-full border-2 border-slate-900 bg-slate-900 rounded-lg flex flex-col items-center justify-center p-1 text-center">
                    <i class="pi pi-mobile text-blue-400 text-base mb-0.5"></i>
                    <span class="text-[8px] font-mono text-emerald-400 font-bold">ABA PAY QR</span>
                    <span class="text-[7px] text-slate-400">$324.00</span>
                  </div>
                </div>
                <div class="text-[10px] text-slate-600 dark:text-slate-300 font-medium">
                  Scan with ABA Mobile App | <span class="font-mono text-blue-600 dark:text-blue-300 font-bold">Acc: 000 123 456 789</span>
                </div>
              </div>

              <!-- Receipt Upload Field -->
              <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1">
                  <i class="pi pi-upload text-blue-500 text-[10px]"></i> Upload Receipt (Optional):
                </label>
                <input
                  type="file"
                  accept="image/*,application/pdf"
                  @change="handleFileChange"
                  class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-slate-200 dark:file:bg-[#0A1024] file:text-blue-600 dark:file:text-blue-400 hover:file:bg-slate-300 cursor-pointer"
                />
                <span v-if="form.errors.receipt" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ form.errors.receipt }}</span>
                <div v-if="receiptPreview" class="mt-1">
                  <img :src="receiptPreview" class="max-h-20 rounded-lg border border-slate-300 dark:border-[#1C2951]" />
                </div>
              </div>

              <!-- Payment Confirmation Checkbox -->
              <div>
                <label class="flex items-center space-x-2 cursor-pointer select-none">
                  <input
                    v-model="form.payment_confirmed"
                    type="checkbox"
                    class="rounded border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-[#0A1024] text-blue-600 focus:ring-blue-500 h-3.5 w-3.5"
                  />
                  <span class="text-[11px] text-slate-700 dark:text-slate-300 font-medium">
                    I have completed the payment ({{ form.payment_method === 'aba' ? '$324.00 via ABA' : '$360.00 Cash' }})
                  </span>
                </label>
              </div>

              <!-- Step 3 Navigation -->
              <div class="grid grid-cols-2 gap-2.5 pt-1">
                <button
                  type="button"
                  @click="prevStep"
                  class="py-2.5 bg-slate-200 dark:bg-[#0A1024] hover:bg-slate-300 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold rounded-xl border border-slate-300 dark:border-[#1C2951] transition uppercase tracking-wider text-xs flex items-center justify-center gap-1 cursor-pointer"
                >
                  <i class="pi pi-arrow-left text-[10px]"></i>
                  <span>Back</span>
                </button>

                <button
                  type="submit"
                  :disabled="form.processing"
                  class="py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-md transition uppercase tracking-wider text-xs flex items-center justify-center gap-1.5 disabled:opacity-50 cursor-pointer"
                >
                  <i v-if="form.processing" class="pi pi-spin pi-spinner text-xs"></i>
                  <i v-else class="pi pi-check-circle text-xs"></i>
                  <span>{{ form.processing ? 'Processing...' : 'COMPLETE' }}</span>
                </button>
              </div>

            </div>

          </form>

          <div class="text-[11px] text-slate-500 dark:text-slate-400 border-t border-slate-200 dark:border-[#1C2951] pt-2.5 text-center font-medium">
            Already have an account? 
            <Link href="/login" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 font-semibold ml-1 transition-colors">
              Login here
            </Link>
          </div>

        </div>

      </div>
    </div>
  </div>
</template>
