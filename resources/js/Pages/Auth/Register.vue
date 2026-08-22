<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { i18n, type LanguageCode } from '../../Services/i18n'
import AuthAnimatedBackground from '../../Components/AuthAnimatedBackground.vue'

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
const isLangOpen = ref(false)

const showSuccessModal = ref(false)
const showErrorModal = ref(false)
const successTitle = ref('')
const successMessage = ref('')
const errorTitle = ref('')
const errorMessage = ref('')

const languages = [
  { code: 'km' as LanguageCode, name: 'ភាសាខ្មែរ', label: 'ខ្មែរ', short: 'KH', flagUrl: '/images/flags/km.svg' },
  { code: 'en' as LanguageCode, name: 'English', label: 'English', short: 'EN', flagUrl: '/images/flags/en.svg' },
]

const currentLang = computed(() => i18n.locale.value)

const selectLanguage = (code: LanguageCode) => {
  i18n.setLanguage(code)
  isLangOpen.value = false
}

const t = (key: string, defaultText?: string) => {
  return i18n.t(key, defaultText)
}

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

const handleClickOutside = (e: MouseEvent) => {
  const target = e.target as HTMLElement
  if (!target.closest('.lang-switcher-container')) {
    isLangOpen.value = false
  }
}

onMounted(() => {
  initTheme()
  document.addEventListener('click', handleClickOutside)
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
  return (
    form.name.trim() !== '' &&
    form.email.includes('@') &&
    form.phone.trim() !== '' &&
    isPasswordValid.value &&
    form.password === form.password_confirmation &&
    form.terms
  )
})

const isStep2Valid = computed(() => {
  if (form.role === 'teacher') {
    return form.invitation_code.trim() !== '' && form.major_id !== null
  }
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

    const step1Fields = ['name', 'name_kh', 'email', 'phone', 'password', 'password_confirmation', 'terms']
    const step2Fields = ['major_id', 'study_type', 'invitation_code']

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
  showErrorModal.value = false
  showSuccessModal.value = false
  form.post('/register', {
    onSuccess: () => {
      showSuccessModal.value = true
      successTitle.value = t('register_modal_success_title', 'ចុះឈ្មោះជោគជ័យ!')
      successMessage.value = t('register_modal_success_msg', 'គណនីរបស់អ្នកត្រូវបានបង្កើតដោយជោគជ័យ! កំពុងចូលប្រព័ន្ធ...')
    },
    onError: (errors) => {
      showErrorModal.value = true
      errorTitle.value = t('register_modal_error_title', 'ព័ត៌មានមិនត្រឹមត្រូវ')
      const firstKey = Object.keys(errors)[0]
      errorMessage.value = errors[firstKey] || t('register_modal_error_msg', 'សូមពិនិត្យមើលព័ត៌មាននៃការចុះឈ្មោះរបស់អ្នកឡើងវិញ!')
      setTimeout(() => {
        showErrorModal.value = false
      }, 4500)
    },
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-100/90 dark:bg-[#070D1E] text-slate-900 dark:text-slate-100 p-4 sm:p-6 lg:p-8 relative font-sans overflow-x-hidden transition-colors duration-500">
    
    <!-- Top-Right Fixed Floating Language & Theme Switchers -->
    <div class="fixed top-6 right-6 sm:top-7 sm:right-7 lg:top-8 lg:right-8 z-50 flex items-center gap-3">
      
      <!-- Language Switcher Pill -->
      <div class="relative lang-switcher-container">
        <button
          type="button"
          @click.stop="isLangOpen = !isLangOpen"
          class="group px-3.5 py-2 rounded-full bg-white/95 dark:bg-slate-800/90 backdrop-blur-md hover:bg-white dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 transition-all duration-200 border border-slate-300/90 dark:border-slate-700/60 shadow-md shadow-slate-900/5 dark:shadow-black/20 flex items-center gap-2 text-xs font-semibold cursor-pointer select-none hover:scale-105 active:scale-95 focus:outline-none"
          :title="currentLang === 'km' ? 'ប្តូរភាសា / Change Language' : 'Change Language / ប្តូរភាសា'"
        >
          <img
            :src="languages.find(l => l.code === currentLang)?.flagUrl || '/images/flags/km.svg'"
            :alt="currentLang"
            class="w-4 h-4 rounded-full object-cover shrink-0 ring-1 ring-slate-300 dark:ring-slate-600"
          />
          <span class="text-[11px] font-bold tracking-wide">
            {{ currentLang === 'km' ? 'KH' : 'EN' }}
          </span>
          <i :class="['pi pi-chevron-down text-[10px] text-slate-500 dark:text-slate-400 transition-transform duration-200', isLangOpen ? 'rotate-180 text-blue-600' : '']"></i>
        </button>

        <!-- Dropdown Menu -->
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
            class="absolute right-0 mt-2 w-44 rounded-2xl bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl border border-slate-200/90 dark:border-slate-700/80 shadow-xl shadow-black/10 dark:shadow-black/40 py-1.5 z-50 overflow-hidden"
          >
            <button
              v-for="lang in languages"
              :key="lang.code"
              type="button"
              @click="selectLanguage(lang.code)"
              :class="[
                'w-full flex items-center justify-between px-3.5 py-2.5 text-xs font-semibold transition-colors cursor-pointer select-none',
                currentLang === lang.code
                  ? 'bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400'
                  : 'text-slate-800 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60 hover:text-slate-950 dark:hover:text-white'
              ]"
            >
              <span class="flex items-center gap-2.5">
                <img :src="lang.flagUrl" :alt="lang.name" class="w-4 h-4 rounded-full object-cover shrink-0 shadow-xs" />
                <span>{{ lang.name }}</span>
              </span>
              <i v-if="currentLang === lang.code" class="pi pi-check text-xs text-blue-600 dark:text-blue-400 font-bold shrink-0"></i>
            </button>
          </div>
        </Transition>
      </div>

      <!-- Theme Switcher Pill -->
      <button
        type="button"
        @click="toggleTheme"
        class="group px-3.5 py-2 rounded-full bg-white/95 dark:bg-[#11131a]/90 backdrop-blur-md hover:bg-white dark:hover:bg-[#181a24] text-slate-800 dark:text-slate-200 transition-all duration-200 border border-slate-300/90 dark:border-white/10 shadow-md shadow-slate-900/5 dark:shadow-black/40 flex items-center gap-2 text-xs font-semibold cursor-pointer select-none hover:scale-105 active:scale-95"
        :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
      >
        <div class="relative w-4 h-4 flex items-center justify-center">
          <i :class="['pi text-xs transition-transform duration-300 group-hover:rotate-45', isDark ? 'pi-sun text-amber-400' : 'pi-moon text-indigo-500']"></i>
        </div>
        <span class="text-[11px] font-bold">{{ isDark ? t('theme_light', 'Light Mode') : t('theme_dark', 'Dark Mode') }}</span>
      </button>
    </div>

    <!-- Manus AI Style Interactive Dot-Matrix Canvas Background -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0 select-none">
      <AuthAnimatedBackground />
    </div>

    <!-- Master Centered Register Card (Clean, Obsidian Glassmorphism) -->
    <div class="max-w-lg w-full max-h-[90vh] p-[1px] rounded-3xl bg-gradient-to-b from-blue-500/40 via-indigo-500/20 to-purple-500/30 dark:from-white/20 dark:via-white/5 dark:to-white/10 shadow-2xl shadow-slate-900/10 dark:shadow-[0_0_60px_-15px_rgba(0,0,0,0.9)] relative z-10 my-auto transition-all flex flex-col">
      <div class="w-full bg-white/95 dark:bg-[#0c0d12]/95 backdrop-blur-2xl rounded-[23px] p-6 sm:p-7 flex flex-col justify-center relative z-20 space-y-3.5 transition-colors overflow-y-auto max-h-[90vh] custom-scrollbar">
        
        <!-- Header with Logo & Brand Name -->
        <div class="text-center pb-1 relative">
          <div class="flex flex-col items-center justify-center gap-1.5">
            <div class="relative group">
              <div class="absolute -inset-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full blur opacity-35 group-hover:opacity-65 transition duration-300"></div>
              <img
                :src="logoUrl"
                alt="Saint Paul Institute Logo"
                class="relative w-14 h-14 rounded-full shadow-lg object-contain ring-2 ring-blue-500/40 ring-offset-2 ring-offset-white dark:ring-offset-[#0c0d12] bg-white p-0.5 transition-transform duration-300 group-hover:scale-105"
              />
            </div>
            <div>
              <h1 class="text-xl font-black tracking-tight bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 dark:from-blue-400 dark:via-indigo-300 dark:to-cyan-300 bg-clip-text text-transparent">
                SPI AI-ELMS
              </h1>
              <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-0.5 tracking-wide">
                {{ t('register_title', 'បង្កើតគណនីថ្មី') }}
              </p>
            </div>
          </div>
        </div>

        <div class="space-y-3.5">

          <!-- Clean Step Progress Header -->
          <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-100/90 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 text-xs select-none">
            <div class="flex items-center gap-2">
              <span class="w-5 h-5 rounded-full bg-blue-500/80 text-white font-black text-[10px] flex items-center justify-center shadow-xs">
                {{ step }}
              </span>
              <span class="text-slate-900 dark:text-slate-100 font-bold text-xs">
                {{ step === 1 ? t('register_step1_header', 'Step 1 of 3: Account Info') : step === 2 ? t('register_step2_header', 'Step 2 of 3: Academic Details') : t('register_step3_header', 'Step 3 of 3: Verification') }}
              </span>
            </div>

            <!-- Progress Indicator Pills -->
            <div class="flex items-center gap-1.5">
              <button type="button" @click="goToStep(1)" :class="['w-6 h-1.5 rounded-full transition-all duration-300', step >= 1 ? 'bg-blue-500/80' : 'bg-slate-300 dark:bg-slate-700']"></button>
              <button type="button" @click="goToStep(2)" :disabled="!isStep1Valid" :class="['w-6 h-1.5 rounded-full transition-all duration-300 disabled:opacity-40', step >= 2 ? 'bg-blue-500/80' : 'bg-slate-300 dark:bg-slate-700']"></button>
              <button type="button" @click="goToStep(3)" :disabled="!isStep1Valid || !isStep2Valid" :class="['w-6 h-1.5 rounded-full transition-all duration-300 disabled:opacity-40', step >= 3 ? 'bg-blue-500/80' : 'bg-slate-300 dark:bg-slate-700']"></button>
            </div>
          </div>

          <!-- Role Selection Segmented Tabs (Attractive Soft Pastel Blue Active State) -->
          <div class="p-1 rounded-xl bg-slate-100 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/80 grid grid-cols-2 gap-1">
            <!-- Student Tab -->
            <button
              type="button"
              @click="form.role = 'student'"
              :class="[
                'flex items-center justify-center gap-1.5 py-2 px-2.5 rounded-lg text-xs font-bold transition-all duration-200 cursor-pointer select-none',
                form.role === 'student'
                  ? 'bg-blue-500/80 text-white shadow-xs shadow-blue-500/20 border border-blue-400/30'
                  : 'text-slate-600 dark:text-slate-400 hover:text-slate-950 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-700/60'
              ]"
            >
              <i class="pi pi-graduation-cap text-xs"></i>
              <span>{{ t('register_tab_student', 'Student') }}</span>
            </button>

            <!-- Teacher Tab -->
            <button
              type="button"
              @click="form.role = 'teacher'"
              :class="[
                'flex items-center justify-center gap-1.5 py-2 px-2.5 rounded-lg text-xs font-bold transition-all duration-200 cursor-pointer select-none',
                form.role === 'teacher'
                  ? 'bg-blue-500/80 text-white shadow-xs shadow-blue-500/20 border border-blue-400/30'
                  : 'text-slate-600 dark:text-slate-400 hover:text-slate-950 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-700/60'
              ]"
            >
              <i class="pi pi-book text-xs"></i>
              <span>{{ t('register_tab_teacher', 'Teacher') }}</span>
            </button>
          </div>

          <!-- Global Validation Error Alert -->
          <div v-if="Object.keys(form.errors).length > 0" class="bg-rose-500/10 border border-rose-500/30 rounded-xl p-2.5 text-rose-600 dark:text-rose-300 text-xs space-y-1 animate-shake">
            <div class="font-bold flex items-center gap-1.5 text-rose-500 text-[11px]">
              <i class="pi pi-exclamation-circle text-sm"></i> Please fix the validation errors:
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-slate-700 dark:text-slate-300 font-medium text-[11px]">
              <li v-for="(err, key) in form.errors" :key="key">{{ err }}</li>
            </ul>
          </div>

          <form @submit.prevent="submit" class="space-y-3.5">
            
            <!-- STEP 1: ACCOUNT INFORMATION -->
            <div v-if="step === 1" class="space-y-3">
              
              <!-- Names Grid -->
              <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                  <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                    {{ t('register_name_kh_label', 'Full Name (Khmer)') }} *
                  </label>
                  <input
                    v-model="form.name_kh"
                    type="text"
                    :placeholder="t('register_name_kh_placeholder', 'e.g. ចាន់ ដារ៉ា')"
                    class="h-11 w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 text-xs sm:text-sm font-medium focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-2xs font-khmer"
                  />
                </div>

                <div class="space-y-1">
                  <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                    {{ t('register_name_en_label', 'Full Name (Latin/English)') }} *
                  </label>
                  <input
                    v-model="form.name"
                    type="text"
                    required
                    :placeholder="t('register_name_en_placeholder', 'e.g. Chan Dara')"
                    class="h-11 w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 text-xs sm:text-sm font-medium focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-2xs"
                  />
                </div>
              </div>

              <!-- Contact Grid -->
              <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                  <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                    {{ t('register_email_label', 'Email Address') }} *
                  </label>
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    :placeholder="t('register_email_placeholder', 'e.g. name@domain.com')"
                    class="h-11 w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 text-xs sm:text-sm font-medium focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-2xs"
                  />
                </div>

                <div class="space-y-1">
                  <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                    {{ t('register_phone_label', 'Phone Number') }} *
                  </label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    required
                    :placeholder="t('register_phone_placeholder', 'e.g. +855 12 345 678')"
                    class="h-11 w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 text-xs sm:text-sm font-medium focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-2xs"
                  />
                </div>
              </div>

              <!-- Passwords Grid -->
              <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                  <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                    {{ t('register_password_label', 'Password') }} *
                  </label>
                  <div class="relative">
                    <input
                      v-model="form.password"
                      :type="showPassword ? 'text' : 'password'"
                      required
                      :placeholder="t('register_password_placeholder', '••••••••••••••••')"
                      class="h-11 w-full pl-3.5 pr-8 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 text-xs sm:text-sm font-medium focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-2xs"
                    />
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-2.5 top-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                      <i :class="['pi text-xs', showPassword ? 'pi-eye-slash text-blue-600' : 'pi-eye']"></i>
                    </button>
                  </div>
                </div>

                <div class="space-y-1">
                  <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                    {{ t('register_confirm_password_label', 'Confirm Password') }} *
                  </label>
                  <input
                    v-model="form.password_confirmation"
                    :type="showPassword ? 'text' : 'password'"
                    required
                    :placeholder="t('register_password_placeholder', '••••••••••••••••')"
                    class="h-11 w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 text-xs sm:text-sm font-medium focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-2xs"
                  />
                </div>
              </div>

              <!-- Password Validation Helper Hint Text -->
              <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium pt-0.5 flex items-center gap-1.5 select-none">
                <i class="pi pi-info-circle text-[11px] text-blue-500 shrink-0"></i>
                <span>{{ t('register_password_hint', 'Must be at least 8 characters with numbers & letters') }}</span>
              </p>

              <!-- Terms Checkbox (Full Label Interaction Wrap) -->
              <div class="pt-1">
                <label class="flex items-center space-x-2.5 cursor-pointer select-none group">
                  <input
                    v-model="form.terms"
                    type="checkbox"
                    required
                    class="rounded border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-blue-600 focus:ring-blue-500 h-4 w-4 cursor-pointer"
                  />
                  <span class="text-xs text-slate-700 dark:text-slate-300 font-medium group-hover:text-blue-600 dark:group-hover:text-sky-400 transition-colors">
                    {{ t('register_terms_label', 'I agree to the Terms of Service & Privacy Policy') }}
                  </span>
                </label>
                <div class="flex items-center gap-2 mt-1 ml-6.5 text-[11px] text-slate-500 dark:text-slate-400">
                  <a href="/terms" target="_blank" class="hover:underline text-blue-600 dark:text-sky-400 font-medium">Terms of Service</a>
                  <span>•</span>
                  <a href="/privacy" target="_blank" class="hover:underline text-blue-600 dark:text-sky-400 font-medium">Privacy Policy</a>
                </div>
              </div>

              <!-- Primary CTA Button (Matching Soft Blue Active Tab Aesthetic) -->
              <button
                type="button"
                @click="nextStep"
                :disabled="!isStep1Valid"
                class="h-11 group w-full py-2.5 px-5 bg-blue-500/85 hover:bg-blue-500 border border-blue-400/30 active:scale-[0.99] text-white font-bold rounded-xl shadow-sm shadow-blue-500/20 hover:shadow-md hover:shadow-blue-500/30 transition-all duration-200 inline-flex items-center justify-center gap-2.5 disabled:opacity-50 text-xs sm:text-sm font-bold tracking-wide cursor-pointer focus:outline-none focus:ring-4 focus:ring-blue-500/20"
              >
                <span>{{ t('register_btn_next_step2', 'Continue to Step 2') }}</span>
                <span class="w-6 h-6 rounded-full bg-white/20 dark:bg-white/15 flex items-center justify-center transition-all duration-300 group-hover:translate-x-1 group-hover:bg-white/30 group-hover:scale-110 shadow-2xs shrink-0">
                  <svg class="w-3.5 h-3.5 fill-current text-white" viewBox="0 0 24 24">
                    <path d="M13.293 6.293a1 1 0 0 1 1.414 0l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414-1.414L16.586 13H5a1 1 0 1 1 0-2h11.586l-3.293-3.293a1 1 0 0 1 0-1.414z"/>
                  </svg>
                </span>
              </button>
            </div>

            <!-- STEP 2: ACADEMIC SELECTION -->
            <div v-if="step === 2" class="space-y-3.5">
              
              <!-- TEACHER ROLE: Clean Dynamic Form -->
              <template v-if="form.role === 'teacher'">
                <!-- Teacher Invitation Code Input -->
                <div class="space-y-1">
                  <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                    {{ t('register_invite_label', 'Teacher Invitation Code') }} *
                  </label>
                  <input
                    v-model="form.invitation_code"
                    type="text"
                    required
                    placeholder="TEACHER-2026-INVITE"
                    class="h-11 w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white font-mono text-xs sm:text-sm font-medium focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-2xs"
                  />
                  <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium pt-0.5 flex items-center gap-1.5 select-none">
                    <i class="pi pi-info-circle text-[11px] text-blue-500 shrink-0"></i>
                    <span>{{ t('register_invite_hint', 'Required to verify faculty authorization') }}</span>
                  </p>
                </div>

                <!-- Department / Faculty Selection -->
                <div class="space-y-1">
                  <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                    {{ t('register_dept_label', 'Department / Faculty') }} *
                  </label>
                  <select
                    v-model="form.major_id"
                    required
                    class="h-11 w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-xs sm:text-sm font-medium shadow-2xs"
                  >
                    <option :value="null" disabled>-- Select Department / Faculty --</option>
                    <option v-for="m in props.majors" :key="m.id" :value="m.id">
                      {{ m.name }} {{ m.name_kh ? `(${m.name_kh})` : '' }}
                    </option>
                  </select>
                </div>
              </template>

              <!-- STUDENT ROLE: Major, Auto-filled Card & Study Type -->
              <template v-else>
                <!-- Major Dropdown -->
                <div class="space-y-1">
                  <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Select Major *</label>
                  <select
                    v-model="form.major_id"
                    required
                    class="h-11 w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-xs sm:text-sm font-medium shadow-2xs"
                  >
                    <option :value="null" disabled>-- Choose Your Major --</option>
                    <option v-for="m in props.majors" :key="m.id" :value="m.id">
                      {{ m.name }} {{ m.name_kh ? `(${m.name_kh})` : '' }}
                    </option>
                  </select>
                </div>

                <!-- Read-Only Auto-filled Academic Information Card -->
                <div class="bg-slate-50 dark:bg-slate-800/60 border border-slate-200/90 dark:border-slate-700/80 rounded-xl p-3.5 space-y-2 text-xs transition-all shadow-2xs">
                  <div class="font-bold text-slate-800 dark:text-slate-200 text-xs flex items-center gap-1.5 pb-1 border-b border-slate-200/80 dark:border-slate-700/60">
                    <i class="pi pi-building text-blue-600 dark:text-sky-400 text-xs shrink-0"></i>
                    <span>🏫 Academic Information (Auto-filled)</span>
                  </div>
                  <div class="space-y-1 pt-0.5 text-xs">
                    <div class="flex items-center gap-1.5">
                      <span class="text-slate-500 dark:text-slate-400 font-semibold">• Faculty:</span>
                      <span class="font-bold text-blue-600 dark:text-sky-400">{{ facultyName }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                      <span class="text-slate-500 dark:text-slate-400 font-semibold">• Department:</span>
                      <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ departmentName }}</span>
                    </div>
                  </div>
                </div>

                <!-- Study Type Radio Buttons (Uniform Primary Blue Palette) -->
                <div class="space-y-1.5">
                  <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Study Type *</label>
                  <div class="grid grid-cols-2 gap-2.5">
                    <label :class="[
                      'p-3 rounded-xl border cursor-pointer transition-all duration-200 flex items-center justify-center gap-2 text-xs font-bold select-none',
                      form.study_type === 'online'
                        ? 'bg-blue-500/10 border-blue-500/60 text-blue-600 dark:text-sky-400 dark:border-blue-500/50 shadow-2xs'
                        : 'bg-slate-50 dark:bg-slate-800/60 border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-slate-400'
                    ]">
                      <input type="radio" v-model="form.study_type" value="online" class="sr-only" />
                      <i class="pi pi-globe text-sm text-blue-600 dark:text-sky-400"></i>
                      <span>🌐 Online Learning</span>
                    </label>

                    <label :class="[
                      'p-3 rounded-xl border cursor-pointer transition-all duration-200 flex items-center justify-center gap-2 text-xs font-bold select-none',
                      form.study_type === 'on_campus'
                        ? 'bg-blue-500/10 border-blue-500/60 text-blue-600 dark:text-sky-400 dark:border-blue-500/50 shadow-2xs'
                        : 'bg-slate-50 dark:bg-slate-800/60 border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-slate-400'
                    ]">
                      <input type="radio" v-model="form.study_type" value="on_campus" class="sr-only" />
                      <i class="pi pi-building text-sm text-blue-600 dark:text-sky-400"></i>
                      <span>🏢 On-Campus</span>
                    </label>
                  </div>
                </div>
              </template>

              <!-- Step 2 Navigation Buttons -->
              <div class="grid grid-cols-2 gap-2.5 pt-1">
                <button
                  type="button"
                  @click="prevStep"
                  class="h-11 bg-slate-200/90 dark:bg-slate-800/90 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold rounded-xl border border-slate-300/80 dark:border-slate-700/60 transition text-xs flex items-center justify-center gap-1.5 cursor-pointer"
                >
                  <i class="pi pi-arrow-left text-xs"></i>
                  <span>{{ t('register_btn_back', 'Back') }}</span>
                </button>

                <button
                  v-if="form.role === 'student'"
                  type="button"
                  @click="nextStep"
                  :disabled="!isStep2Valid"
                  class="h-11 group py-2.5 px-4 bg-blue-500/85 hover:bg-blue-500 border border-blue-400/30 active:scale-[0.99] text-white font-bold rounded-xl shadow-sm shadow-blue-500/20 hover:shadow-md hover:shadow-blue-500/30 transition-all duration-200 inline-flex items-center justify-center gap-2 disabled:opacity-50 text-xs font-bold tracking-wide cursor-pointer focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                  <span>{{ t('register_btn_next_step3', 'Continue to Step 3') }}</span>
                  <span class="w-5 h-5 rounded-full bg-white/20 dark:bg-white/15 flex items-center justify-center transition-all duration-300 group-hover:translate-x-1 group-hover:bg-white/30 shrink-0">
                    <svg class="w-3 h-3 fill-current text-white" viewBox="0 0 24 24">
                      <path d="M13.293 6.293a1 1 0 0 1 1.414 0l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414-1.414L16.586 13H5a1 1 0 1 1 0-2h11.586l-3.293-3.293a1 1 0 0 1 0-1.414z"/>
                    </svg>
                  </span>
                </button>

                <button
                  v-else
                  type="submit"
                  :disabled="form.processing || !isStep2Valid"
                  class="h-11 group py-2.5 px-4 bg-blue-500/85 hover:bg-blue-500 border border-blue-400/30 active:scale-[0.99] text-white font-bold rounded-xl shadow-sm shadow-blue-500/20 hover:shadow-md hover:shadow-blue-500/30 transition-all duration-200 inline-flex items-center justify-center gap-2 disabled:opacity-50 text-xs font-bold tracking-wide cursor-pointer focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                  <i v-if="form.processing" class="pi pi-spin pi-spinner text-xs"></i>
                  <span>{{ form.processing ? t('register_btn_submitting', 'Registering...') : t('register_btn_submit', 'Complete Registration') }}</span>
                  <span v-if="!form.processing" class="w-5 h-5 rounded-full bg-white/20 dark:bg-white/15 flex items-center justify-center transition-all duration-300 group-hover:translate-x-1 group-hover:bg-white/30 shrink-0">
                    <svg class="w-3 h-3 fill-current text-white" viewBox="0 0 24 24">
                      <path d="M13.293 6.293a1 1 0 0 1 1.414 0l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414-1.414L16.586 13H5a1 1 0 1 1 0-2h11.586l-3.293-3.293a1 1 0 0 1 0-1.414z"/>
                    </svg>
                  </span>
                </button>
              </div>

            </div>

            <!-- STEP 3: PAYMENT VERIFICATION (STUDENT ONLY) -->
            <div v-if="step === 3 && form.role === 'student'" class="space-y-3.5">
              
              <!-- Fee Summary Box -->
              <div class="bg-slate-100/90 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 rounded-xl p-3 space-y-1.5 text-xs">
                <div class="font-bold text-slate-900 dark:text-white text-xs flex items-center justify-between">
                  <span>Tuition & Registration Fee:</span>
                  <span class="font-mono text-emerald-600 dark:text-emerald-400 font-bold">$324.00 (ABA Special)</span>
                </div>
              </div>

              <!-- Payment Method Choice -->
              <div class="grid grid-cols-2 gap-2">
                <label :class="[
                  'p-2.5 rounded-xl border cursor-pointer transition flex items-center justify-center gap-2 text-xs font-bold select-none',
                  form.payment_method === 'aba'
                    ? 'bg-blue-500/10 border-blue-500/60 text-blue-600 dark:text-sky-400 dark:border-blue-500/50'
                    : 'bg-slate-50 dark:bg-slate-800/60 border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-400'
                ]">
                  <input type="radio" v-model="form.payment_method" value="aba" class="sr-only" />
                  <i class="pi pi-qrcode text-xs"></i>
                  <span>ABA Mobile QR</span>
                </label>

                <label :class="[
                  'p-2.5 rounded-xl border cursor-pointer transition flex items-center justify-center gap-2 text-xs font-bold select-none',
                  form.payment_method === 'cash'
                    ? 'bg-emerald-500/10 border-emerald-500/60 text-emerald-600 dark:text-emerald-400 dark:border-emerald-500/50'
                    : 'bg-slate-50 dark:bg-slate-800/60 border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-400'
                ]">
                  <input type="radio" v-model="form.payment_method" value="cash" class="sr-only" />
                  <i class="pi pi-money-bill text-xs"></i>
                  <span>Cash at Campus</span>
                </label>
              </div>

              <!-- Step 3 Navigation -->
              <div class="grid grid-cols-2 gap-2.5 pt-1">
                <button
                  type="button"
                  @click="prevStep"
                  class="h-11 bg-slate-200/90 dark:bg-slate-800/90 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold rounded-xl border border-slate-300/80 dark:border-slate-700/60 transition text-xs flex items-center justify-center gap-1.5 cursor-pointer"
                >
                  <i class="pi pi-arrow-left text-xs"></i>
                  <span>{{ t('register_btn_back', 'Back') }}</span>
                </button>

                <button
                  type="submit"
                  :disabled="form.processing"
                  class="h-11 group py-2.5 px-4 bg-emerald-500/85 hover:bg-emerald-500 border border-emerald-400/30 active:scale-[0.99] text-white font-bold rounded-xl shadow-sm shadow-emerald-500/20 hover:shadow-md hover:shadow-emerald-500/30 transition-all duration-200 inline-flex items-center justify-center gap-2 disabled:opacity-50 text-xs font-bold tracking-wide cursor-pointer focus:outline-none focus:ring-4 focus:ring-emerald-500/20"
                >
                  <i v-if="form.processing" class="pi pi-spin pi-spinner text-xs"></i>
                  <span>{{ form.processing ? t('register_btn_submitting', 'Processing...') : t('register_btn_submit', 'Complete Registration') }}</span>
                  <span v-if="!form.processing" class="w-5 h-5 rounded-full bg-white/20 dark:bg-white/15 flex items-center justify-center transition-all duration-300 group-hover:translate-x-1 group-hover:bg-white/30 shrink-0">
                    <svg class="w-3 h-3 fill-current text-white" viewBox="0 0 24 24">
                      <path d="M13.293 6.293a1 1 0 0 1 1.414 0l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414-1.414L16.586 13H5a1 1 0 1 1 0-2h11.586l-3.293-3.293a1 1 0 0 1 0-1.414z"/>
                    </svg>
                  </span>
                </button>
              </div>

            </div>

          </form>

          <!-- Navigation Footer: Clean Single Link Back to Sign In (No redundant Forgot Password link) -->
          <div class="pt-3.5 pb-1 border-t border-slate-200 dark:border-slate-800/80 flex items-center justify-between text-xs font-medium">
            <span class="text-slate-600 dark:text-slate-400">
              {{ t('register_already_have_account', 'Already have an account?') }}
            </span>
            <Link href="/login" class="group text-blue-600 dark:text-sky-400 hover:text-blue-700 dark:hover:text-sky-300 font-bold inline-flex items-center gap-2 transition-all duration-150 active:scale-95 outline-none focus:outline-none focus:ring-0 focus-visible:outline-none select-none">
              <span>{{ t('register_back_to_login', 'Sign In') }}</span>
              <span class="w-6 h-6 rounded-full bg-blue-500/10 dark:bg-sky-500/20 border border-blue-500/20 dark:border-sky-400/30 flex items-center justify-center text-blue-600 dark:text-sky-400 group-hover:bg-blue-600 group-hover:text-white dark:group-hover:bg-sky-400 dark:group-hover:text-slate-950 transition-all duration-300 group-hover:translate-x-1 shadow-2xs shrink-0">
                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                  <path d="M13.293 6.293a1 1 0 0 1 1.414 0l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414-1.414L16.586 13H5a1 1 0 1 1 0-2h11.586l-3.293-3.293a1 1 0 0 1 0-1.414z"/>
                </svg>
              </span>
            </Link>
          </div>

        </div>
      </div>
    </div>

    <!-- Compact Success Alert Modal (Checkmark Icon) -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 scale-90 translate-y-2"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-90 translate-y-2"
    >
      <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/40 backdrop-blur-sm select-none">
        <div class="max-w-xs w-full bg-white dark:bg-[#0E172E] rounded-3xl p-6 shadow-2xl border border-emerald-500/30 text-center flex flex-col items-center space-y-3.5 transform transition-all">
          <!-- Icon with glowing ring -->
          <div class="relative flex items-center justify-center">
            <div class="absolute -inset-2 bg-gradient-to-r from-emerald-500/30 to-teal-500/30 rounded-full blur-md animate-pulse"></div>
            <div class="relative w-14 h-14 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-lg shadow-emerald-500/25 ring-4 ring-white dark:ring-[#0E172E]">
              <svg class="w-7 h-7 text-white stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" class="checkmark-path" d="M4.5 12.75l6 6 9-13.5"/>
              </svg>
            </div>
          </div>
          
          <div class="space-y-1">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
              {{ successTitle || t('register_modal_success_title', 'ចុះឈ្មោះជោគជ័យ!') }}
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
              {{ successMessage || t('register_modal_success_msg', 'គណនីរបស់អ្នកត្រូវបានបង្កើតដោយជោគជ័យ! កំពុងចូលប្រព័ន្ធ...') }}
            </p>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Compact Error Alert Modal (Question Mark Icon) -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 scale-90 translate-y-2"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-90 translate-y-2"
    >
      <div v-if="showErrorModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/40 backdrop-blur-sm select-none">
        <div class="max-w-xs w-full bg-white dark:bg-[#0E172E] rounded-3xl p-6 shadow-2xl border border-rose-500/30 text-center flex flex-col items-center space-y-3.5 transform transition-all">
          <!-- Icon with glowing ring -->
          <div class="relative flex items-center justify-center">
            <div class="absolute -inset-2 bg-gradient-to-r from-rose-500/30 to-amber-500/30 rounded-full blur-md animate-pulse"></div>
            <div class="relative w-14 h-14 rounded-full bg-gradient-to-br from-amber-500 via-rose-500 to-rose-600 text-white flex items-center justify-center shadow-lg shadow-rose-500/25 ring-4 ring-white dark:ring-[#0E172E]">
              <span class="text-3xl font-black font-sans leading-none">?</span>
            </div>
          </div>
          
          <div class="space-y-1">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
              {{ errorTitle || t('register_modal_error_title', 'ព័ត៌មានមិនត្រឹមត្រូវ') }}
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
              {{ errorMessage || t('register_modal_error_msg', 'សូមពិនិត្យមើលព័ត៌មាននៃការចុះឈ្មោះរបស់អ្នកឡើងវិញ!') }}
            </p>
          </div>

          <button
            type="button"
            @click="showErrorModal = false"
            class="w-full py-2 px-4 rounded-xl bg-rose-50 dark:bg-rose-950/50 hover:bg-rose-100 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800/40 text-rose-700 dark:text-rose-300 text-xs font-bold transition-all duration-150 cursor-pointer active:scale-95"
          >
            {{ t('login_modal_close', 'យល់ព្រម') }}
          </button>
        </div>
      </div>
    </Transition>

  </div>
</template>

<style scoped>
.checkmark-path {
  stroke-dasharray: 50;
  stroke-dashoffset: 50;
  animation: checkmarkDraw 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

@keyframes checkmarkDraw {
  0% {
    stroke-dashoffset: 50;
    opacity: 0;
  }
  100% {
    stroke-dashoffset: 0;
    opacity: 1;
  }
}

@keyframes floatSlow {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-22px) rotate(4deg); }
}

@keyframes floatReverse {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(22px) rotate(-4deg); }
}

@keyframes aurora {
  0% { transform: translate(0%, 0%) rotate(0deg); }
  50% { transform: translate(8%, 8%) rotate(180deg); }
  100% { transform: translate(0%, 0%) rotate(360deg); }
}

.animate-float-slow {
  animation: floatSlow 8s ease-in-out infinite;
}
.animate-float-reverse {
  animation: floatReverse 11s ease-in-out infinite;
}
.animate-aurora {
  animation: aurora 25s linear infinite;
}
.font-khmer {
  font-family: 'Kantumruy Pro', 'Kantumruy', 'Siemreap', 'Noto Sans Khmer', system-ui, sans-serif;
}
</style>
