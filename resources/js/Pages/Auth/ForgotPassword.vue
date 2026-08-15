<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useForm, usePage, Link } from '@inertiajs/vue3'
import { i18n, type LanguageCode } from '../../Services/i18n'
import AuthAnimatedBackground from '../../Components/AuthAnimatedBackground.vue'

const props = defineProps<{
  status?: string
}>()

const page = usePage()
const logoUrl = '/images/logo.png'

const step = ref<1 | 2>(1)
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
  if (props.status || page.props.status) {
    showSuccessModal.value = true
    successTitle.value = t('forgot_modal_success_title', 'ផ្ញើកូដជោគជ័យ!')
    successMessage.value = props.status || (page.props.status as string)
    setTimeout(() => {
      showSuccessModal.value = false
    }, 4000)
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Step 1 Form
const requestForm = useForm({
  email: '',
})

// Step 2 Form
const resetForm = useForm({
  email: '',
  code: '',
  password: '',
  password_confirmation: '',
})

const showPassword = ref(false)
const codeDigits = ref(['', '', '', '', '', ''])
const digitInputs = ref<HTMLInputElement[]>([])

const clearEmail = () => {
  requestForm.email = ''
}

const handleCodeInput = (index: number, e: Event) => {
  const input = e.target as HTMLInputElement
  const val = input.value

  if (val.length > 1) {
    const chars = val.replace(/\D/g, '').split('').slice(0, 6)
    chars.forEach((char, i) => {
      if (i < 6) codeDigits.value[i] = char
    })
    resetForm.code = codeDigits.value.join('')
    if (chars.length > 0 && digitInputs.value[Math.min(chars.length - 1, 5)]) {
      digitInputs.value[Math.min(chars.length - 1, 5)].focus()
    }
    return
  }

  codeDigits.value[index] = val
  resetForm.code = codeDigits.value.join('')

  if (val && index < 5 && digitInputs.value[index + 1]) {
    digitInputs.value[index + 1].focus()
  }
}

const handleCodeKeyDown = (index: number, e: KeyboardEvent) => {
  if (e.key === 'Backspace' && !codeDigits.value[index] && index > 0 && digitInputs.value[index - 1]) {
    digitInputs.value[index - 1].focus()
  }
}

const handleCodePaste = (e: ClipboardEvent) => {
  e.preventDefault()
  const pasted = e.clipboardData?.getData('text') || ''
  const digits = pasted.replace(/\D/g, '').split('').slice(0, 6)
  digits.forEach((digit, i) => {
    codeDigits.value[i] = digit
  })
  resetForm.code = codeDigits.value.join('')
  if (digits.length > 0 && digitInputs.value[Math.min(digits.length - 1, 5)]) {
    digitInputs.value[Math.min(digits.length - 1, 5)].focus()
  }
}

const onRequestCode = () => {
  showErrorModal.value = false
  showSuccessModal.value = false
  requestForm.post('/forgot-password', {
    onSuccess: (pageRes: any) => {
      resetForm.email = requestForm.email
      step.value = 2
      showSuccessModal.value = true
      successTitle.value = t('forgot_modal_success_title', 'ផ្ញើកូដជោគជ័យ!')
      successMessage.value = (pageRes?.props?.status as string) || props.status || (page?.props?.status as string) || t('forgot_modal_success_msg', 'កូដផ្ទៀងផ្ទាត់ 6 ខ្ទង់ ត្រូវបានផ្ញើទៅកាន់ព័ត៌មានរបស់អ្នករួចរាល់ហើយ!')
      setTimeout(() => {
        showSuccessModal.value = false
      }, 4000)
    },
    onError: (errors) => {
      showErrorModal.value = true
      errorTitle.value = t('forgot_modal_error_title', 'ព័ត៌មានមិនត្រឹមត្រូវ')
      errorMessage.value = errors.email || errors.code || errors.password || t('forgot_modal_error_msg', 'សូមពិនិត្យមើលអ៊ីមែល ឬលេខទូរស័ព្ទរបស់អ្នកឡើងវិញ!')
      setTimeout(() => {
        showErrorModal.value = false
      }, 4500)
    },
  })
}

const onResetPassword = () => {
  showErrorModal.value = false
  showSuccessModal.value = false
  resetForm.post('/reset-password', {
    onFinish: () => resetForm.reset('password', 'password_confirmation'),
    onError: (errors) => {
      showErrorModal.value = true
      errorTitle.value = t('forgot_modal_error_title', 'ព័ត៌មានមិនត្រឹមត្រូវ')
      errorMessage.value = errors.code || errors.email || errors.password || t('forgot_modal_error_msg', 'កូដផ្ទៀងផ្ទាត់មិនត្រឹមត្រូវទេ! សូមពិនិត្យកូដ 6 ខ្ទង់ម្តងទៀត។')
      setTimeout(() => {
        showErrorModal.value = false
      }, 4500)
    },
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
        class="group px-3.5 py-2 rounded-full bg-white/95 dark:bg-slate-800/90 backdrop-blur-md hover:bg-white dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 transition-all duration-200 border border-slate-300/90 dark:border-slate-700/60 shadow-md shadow-slate-900/5 dark:shadow-black/20 flex items-center gap-2 text-xs font-semibold cursor-pointer select-none hover:scale-105 active:scale-95"
        :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
      >
        <div class="relative w-4 h-4 flex items-center justify-center">
          <i :class="['pi text-xs transition-transform duration-300 group-hover:rotate-45', isDark ? 'pi-sun text-amber-500' : 'pi-moon text-indigo-500']"></i>
        </div>
        <span class="text-[11px] font-bold">{{ isDark ? t('theme_light', 'Light Mode') : t('theme_dark', 'Dark Mode') }}</span>
      </button>
    </div>

    <!-- Interactive 3D Three.js Animated AI Background & Ambient Glow -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0 select-none">
      <AuthAnimatedBackground />

      <!-- Animated Aurora Gradient Wave -->
      <div class="absolute -inset-[100%] opacity-40 dark:opacity-30 animate-aurora bg-gradient-to-r from-blue-600/30 via-indigo-500/30 via-purple-500/30 via-sky-400/30 to-blue-600/30 blur-3xl"></div>

      <!-- Tech Dot Grid Matrix Overlay -->
      <div class="absolute inset-0 bg-[radial-gradient(#3b82f6_1.2px,transparent_1.2px)] dark:bg-[radial-gradient(#38bdf8_1.2px,transparent_1.2px)] [background-size:32px_32px] opacity-30 dark:opacity-20"></div>

      <!-- Glowing Animated Orbs -->
      <div class="absolute -top-32 -left-32 w-[550px] h-[550px] bg-blue-500/25 dark:bg-blue-600/30 rounded-full blur-[130px] animate-float-slow"></div>
      <div class="absolute -bottom-32 -right-32 w-[600px] h-[600px] bg-indigo-500/25 dark:bg-indigo-600/30 rounded-full blur-[140px] animate-float-reverse"></div>
    </div>

    <!-- Master Centered Forgot Password Card (Clean & Focused Layout) -->
    <div class="max-w-md w-full p-[1px] rounded-3xl bg-gradient-to-b from-blue-500/40 via-indigo-500/20 to-purple-500/30 dark:from-blue-500/30 dark:via-slate-800/40 dark:to-indigo-500/20 shadow-2xl shadow-slate-900/10 dark:shadow-black/60 relative z-10 my-auto transition-all">
      <div class="w-full bg-white/95 dark:bg-[#0E172E]/95 backdrop-blur-2xl rounded-[23px] p-6 sm:p-8 flex flex-col justify-center relative z-20 space-y-4 transition-colors">
        
        <!-- Header with Logo & Brand Name -->
        <div class="text-center pb-1 relative">
          <div class="flex flex-col items-center justify-center gap-1.5">
            <div class="relative group">
              <div class="absolute -inset-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full blur opacity-35 group-hover:opacity-65 transition duration-300"></div>
              <img
                :src="logoUrl"
                alt="Saint Paul Institute Logo"
                class="relative w-14 h-14 rounded-full shadow-lg object-contain ring-2 ring-blue-500/40 ring-offset-2 ring-offset-white dark:ring-offset-[#0E172E] bg-white p-0.5 transition-transform duration-300 group-hover:scale-105"
              />
            </div>
            <div>
              <h1 class="text-xl font-black tracking-tight bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 dark:from-blue-400 dark:via-indigo-300 dark:to-cyan-300 bg-clip-text text-transparent">
                SPI AI-ELMS
              </h1>
              <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 mt-0.5 tracking-wide">
                {{ t('forgot_subtitle', 'វិទ្យាស្ថាន សន្តប៉ូល') }}
              </p>
            </div>
          </div>
        </div>

        <div class="space-y-4">

          <!-- Clean Modern Step Progress Bar -->
          <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-100/90 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 text-xs select-none">
            <div class="flex items-center gap-2">
              <span :class="['w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black text-white transition-colors shadow-xs', step === 1 ? 'bg-blue-600 ring-2 ring-blue-500/30' : 'bg-emerald-500']">
                <i v-if="step > 1" class="pi pi-check text-[9px]"></i>
                <span v-else>1</span>
              </span>
              <span class="text-slate-900 dark:text-slate-100 font-bold text-xs">
                {{ step === 1 ? t('forgot_step1_label', 'Step 1 of 2: Request OTP Code') : t('forgot_step2_label', 'Step 2 of 2: Enter OTP & New Password') }}
              </span>
            </div>
            <div class="flex items-center gap-1.5">
              <div :class="['w-7 h-1.5 rounded-full transition-all duration-300', step >= 1 ? 'bg-blue-600' : 'bg-slate-300 dark:bg-slate-700']"></div>
              <div :class="['w-7 h-1.5 rounded-full transition-all duration-300', step === 2 ? 'bg-blue-600' : 'bg-slate-300 dark:bg-slate-700']"></div>
            </div>
          </div>
        
          <!-- Status Message Banner -->
          <div v-if="props.status || page.props.status" class="bg-emerald-500/10 border border-emerald-500/30 rounded-xl p-2.5 text-emerald-700 dark:text-emerald-300 text-xs flex items-start gap-2 shadow-xs">
            <i class="pi pi-check-circle text-sm text-emerald-500 shrink-0 mt-0.5"></i>
            <span class="leading-tight font-medium text-[11px]">{{ props.status || page.props.status }}</span>
          </div>

          <!-- STEP 1 FORM: REQUEST OTP -->
          <form v-if="step === 1" @submit.prevent="onRequestCode" class="space-y-3.5">
            
            <!-- Global Error Alert -->
            <div v-if="requestForm.errors.email" class="bg-rose-500/10 border border-rose-500/30 rounded-xl p-2.5 text-rose-600 dark:text-rose-300 text-xs flex items-start gap-2 shadow-xs animate-shake">
              <i class="pi pi-exclamation-circle text-sm text-rose-500 shrink-0 mt-0.5"></i>
              <span class="leading-tight font-medium">{{ requestForm.errors.email }}</span>
            </div>

            <!-- Input Email / ID / Phone -->
            <div class="space-y-1">
              <label class="block text-xs font-bold text-slate-900 dark:text-slate-100">
                <span>{{ t('forgot_input_email_label', 'Email, Phone, or Account ID') }}</span>
              </label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-300 group-focus-within:text-blue-600 dark:group-focus-within:text-sky-400 transition-colors">
                  <i class="pi pi-id-card text-sm"></i>
                </div>
                <input
                  v-model="requestForm.email"
                  type="text"
                  required
                  autocomplete="username"
                  :placeholder="t('forgot_input_email_placeholder', 'e.g. user@domain.com or ID/Phone')"
                  class="h-11 w-full pl-10 pr-9 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-xs sm:text-sm font-medium shadow-2xs"
                />
                <!-- Quick Clear Button -->
                <button
                  v-if="requestForm.email"
                  type="button"
                  @click="clearEmail"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer"
                  title="Clear"
                >
                  <i class="pi pi-times-circle text-xs"></i>
                </button>
              </div>
            </div>

            <!-- Submit Button Step 1 -->
            <button
              type="submit"
              :disabled="requestForm.processing"
              class="h-11 group w-full py-2.5 px-5 bg-blue-600 hover:bg-blue-500 border border-blue-500/30 active:scale-[0.99] text-white font-bold rounded-xl shadow-sm shadow-blue-500/20 hover:shadow-md hover:shadow-blue-500/30 transition-all duration-200 inline-flex items-center justify-center gap-2.5 disabled:opacity-50 text-xs sm:text-sm tracking-wide cursor-pointer focus:outline-none focus:ring-4 focus:ring-blue-500/20"
            >
              <i v-if="requestForm.processing" class="pi pi-spin pi-spinner text-sm shrink-0"></i>
              <template v-else>
                <span>{{ requestForm.processing ? t('forgot_btn_sending', 'Sending Code...') : t('forgot_btn_send_otp', 'Send Verification Code') }}</span>
                <span class="w-6 h-6 rounded-full bg-white/20 dark:bg-white/15 flex items-center justify-center transition-all duration-300 group-hover:translate-x-1 group-hover:bg-white/30 group-hover:scale-110 shadow-2xs shrink-0">
                  <svg class="w-3.5 h-3.5 fill-current text-white" viewBox="0 0 24 24">
                    <path d="M13.293 6.293a1 1 0 0 1 1.414 0l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414-1.414L16.586 13H5a1 1 0 1 1 0-2h11.586l-3.293-3.293a1 1 0 0 1 0-1.414z"/>
                  </svg>
                </span>
              </template>
            </button>
          </form>

          <!-- STEP 2 FORM: ENTER OTP & NEW PASSWORD -->
          <form v-else @submit.prevent="onResetPassword" class="space-y-3.5">

            <!-- Global Error Alert -->
            <div v-if="resetForm.errors.email || resetForm.errors.code || resetForm.errors.password" class="bg-rose-500/10 border border-rose-500/30 rounded-xl p-2.5 text-rose-600 dark:text-rose-300 text-xs flex items-start gap-2 shadow-xs animate-shake">
              <i class="pi pi-exclamation-circle text-sm text-rose-500 shrink-0 mt-0.5"></i>
              <span class="leading-tight font-medium">{{ resetForm.errors.email || resetForm.errors.code || resetForm.errors.password }}</span>
            </div>

            <!-- Account Identifier Readonly Input -->
            <div class="space-y-1">
              <label class="block text-xs font-bold text-slate-900 dark:text-slate-100">
                <span>{{ t('forgot_account_label', 'Account Identifier') }}</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-300">
                  <i class="pi pi-user text-sm"></i>
                </div>
                <input
                  v-model="resetForm.email"
                  type="text"
                  required
                  readonly
                  class="h-11 w-full pl-10 pr-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700/80 bg-slate-100 dark:bg-slate-900/80 text-slate-700 dark:text-slate-300 transition text-xs font-medium cursor-not-allowed"
                />
              </div>
            </div>

            <!-- 6-Digit OTP Code Inputs -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-900 dark:text-slate-100">
                <span>{{ t('forgot_otp_label', 'Enter 6-Digit OTP Code') }}</span>
              </label>
              <div class="grid grid-cols-6 gap-1.5" @paste="handleCodePaste">
                <input
                  v-for="(digit, idx) in 6"
                  :key="idx"
                  ref="digitInputs"
                  v-model="codeDigits[idx]"
                  type="text"
                  maxlength="1"
                  inputmode="numeric"
                  @input="handleCodeInput(idx, $event)"
                  @keydown="handleCodeKeyDown(idx, $event)"
                  class="w-full h-11 py-2 text-center text-sm font-bold font-mono rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-2xs"
                />
              </div>
            </div>

            <!-- New Password Input -->
            <div class="space-y-1">
              <label class="block text-xs font-bold text-slate-900 dark:text-slate-100">
                <span>{{ t('forgot_new_password_label', 'New Password') }}</span>
              </label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-300 group-focus-within:text-blue-600 dark:group-focus-within:text-sky-400 transition-colors">
                  <i class="pi pi-lock text-sm"></i>
                </div>
                <input
                  v-model="resetForm.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  autocomplete="new-password"
                  :placeholder="t('login_input_password_placeholder', '••••••••••••••••')"
                  class="h-11 w-full pl-10 pr-10 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-xs sm:text-sm font-medium shadow-2xs"
                />
                <!-- Eye Toggle Button -->
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors p-1.5 rounded-lg cursor-pointer"
                  :title="showPassword ? 'Hide password' : 'Show password'"
                >
                  <i :class="['pi text-sm transition-transform duration-200 hover:scale-110', showPassword ? 'pi-eye-slash text-blue-600 dark:text-sky-400' : 'pi-eye']"></i>
                </button>
              </div>
            </div>

            <!-- Confirm Password Input -->
            <div class="space-y-1">
              <label class="block text-xs font-bold text-slate-900 dark:text-slate-100">
                <span>{{ t('forgot_confirm_password_label', 'Confirm New Password') }}</span>
              </label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-300 group-focus-within:text-blue-600 dark:group-focus-within:text-sky-400 transition-colors">
                  <i class="pi pi-lock text-sm"></i>
                </div>
                <input
                  v-model="resetForm.password_confirmation"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  autocomplete="new-password"
                  :placeholder="t('login_input_password_placeholder', '••••••••••••••••')"
                  class="h-11 w-full pl-10 pr-10 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-xs sm:text-sm font-medium shadow-2xs"
                />
              </div>
              <span v-if="resetForm.password && resetForm.password_confirmation && resetForm.password !== resetForm.password_confirmation" class="text-[10px] text-rose-600 dark:text-rose-400 block font-semibold inline-flex items-center gap-1">
                <i class="pi pi-exclamation-triangle shrink-0"></i> <span>{{ t('forgot_pwd_mismatch', 'Passwords do not match') }}</span>
              </span>
            </div>

            <!-- Submit Button Step 2 -->
            <button
              type="submit"
              :disabled="resetForm.processing || (resetForm.password !== resetForm.password_confirmation && resetForm.password_confirmation.length > 0)"
              class="h-11 group w-full py-2.5 px-5 bg-emerald-600 hover:bg-emerald-500 border border-emerald-500/30 active:scale-[0.99] text-white font-bold rounded-xl shadow-sm shadow-emerald-500/20 hover:shadow-md hover:shadow-emerald-500/30 transition-all duration-200 inline-flex items-center justify-center gap-2.5 disabled:opacity-50 text-xs sm:text-sm font-bold tracking-wide cursor-pointer focus:outline-none focus:ring-4 focus:ring-emerald-500/20"
            >
              <i v-if="resetForm.processing" class="pi pi-spin pi-spinner text-sm shrink-0"></i>
              <template v-else>
                <span>{{ resetForm.processing ? t('forgot_btn_updating', 'Updating Password...') : t('forgot_btn_reset', 'Reset Password & Login') }}</span>
                <span class="w-6 h-6 rounded-full bg-white/20 dark:bg-white/15 flex items-center justify-center transition-all duration-300 group-hover:scale-110 group-hover:bg-white/30 shadow-2xs shrink-0">
                  <svg class="w-3.5 h-3.5 fill-current text-white" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                  </svg>
                </span>
              </template>
            </button>
          </form>

          <!-- Navigation Footer: Back to Sign In & Register -->
          <div class="pt-3.5 pb-1 border-t border-slate-200 dark:border-slate-800/80 flex items-center justify-between text-xs font-medium">
            <Link href="/login" class="group text-blue-600 dark:text-sky-400 hover:text-blue-700 dark:hover:text-sky-300 font-bold inline-flex items-center gap-2 transition-all duration-150 active:scale-95 outline-none focus:outline-none focus:ring-0 focus-visible:outline-none select-none">
              <span class="w-6 h-6 rounded-full bg-blue-500/10 dark:bg-sky-500/20 border border-blue-500/20 dark:border-sky-400/30 flex items-center justify-center text-blue-600 dark:text-sky-400 group-hover:bg-blue-600 group-hover:text-white dark:group-hover:bg-sky-400 dark:group-hover:text-slate-950 transition-all duration-300 group-hover:-translate-x-1 shadow-2xs shrink-0">
                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                  <path d="M10.707 17.707a1 1 0 0 1-1.414 0l-5-5a1 1 0 0 1 0-1.414l5-5a1 1 0 0 1 1.414 1.414L7.414 11H19a1 1 0 1 1 0 2H7.414l3.293 3.293a1 1 0 0 1 0 1.414z"/>
                </svg>
              </span>
              <span>{{ t('forgot_back_to_login', 'Sign In') }}</span>
            </Link>
            <Link href="/register" class="group text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-sky-400 font-bold inline-flex items-center gap-2 transition-all duration-150 active:scale-95 outline-none focus:outline-none focus:ring-0 focus-visible:outline-none select-none">
              <span>{{ t('login_register_now', 'Register Account') }}</span>
              <span class="w-6 h-6 rounded-full bg-slate-200/80 dark:bg-slate-800 border border-slate-300/80 dark:border-slate-700 flex items-center justify-center text-slate-700 dark:text-slate-200 group-hover:bg-blue-600 group-hover:text-white dark:group-hover:bg-sky-400 dark:group-hover:text-slate-950 group-hover:border-blue-600 dark:group-hover:border-sky-400 transition-all duration-300 group-hover:translate-x-1 shadow-2xs shrink-0">
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
              {{ successTitle || t('forgot_modal_success_title', 'ផ្ញើកូដជោគជ័យ!') }}
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
              {{ successMessage || t('forgot_modal_success_msg', 'កូដផ្ទៀងផ្ទាត់ 6 ខ្ទង់ ត្រូវបានផ្ញើទៅកាន់ព័ត៌មានរបស់អ្នករួចរាល់ហើយ!') }}
            </p>
          </div>

          <button
            type="button"
            @click="showSuccessModal = false"
            class="w-full py-2 px-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold transition-all duration-150 cursor-pointer active:scale-95"
          >
            {{ t('login_modal_close', 'យល់ព្រម') }}
          </button>
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
              {{ errorTitle || t('forgot_modal_error_title', 'ព័ត៌មានមិនត្រឹមត្រូវ') }}
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
              {{ errorMessage || requestForm.errors.email || resetForm.errors.code || t('forgot_modal_error_msg', 'សូមពិនិត្យមើលឡើងវិញ!') }}
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

@keyframes spinSlow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes spinReverse {
  from { transform: rotate(360deg); }
  to { transform: rotate(0deg); }
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
.animate-spin-slow {
  animation: spinSlow 30s linear infinite;
}
.animate-spin-reverse {
  animation: spinReverse 35s linear infinite;
}
.animate-aurora {
  animation: aurora 25s linear infinite;
}
.animate-pulse-slow {
  animation: pulse 5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
