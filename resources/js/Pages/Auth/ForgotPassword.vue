<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useForm, usePage, Link } from '@inertiajs/vue3'

const props = defineProps<{
  status?: string
}>()

const page = usePage()
const logoUrl = '/images/logo.png'

const step = ref<1 | 2>(1)
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
  requestForm.post('/forgot-password', {
    onSuccess: () => {
      resetForm.email = requestForm.email
      step.value = 2
    },
  })
}

const onResetPassword = () => {
  resetForm.post('/reset-password', {
    onFinish: () => resetForm.reset('password', 'password_confirmation'),
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
    <div class="absolute -top-32 -left-32 w-80 h-80 bg-blue-500/10 dark:bg-blue-600/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-purple-500/10 dark:bg-purple-600/5 rounded-full blur-3xl pointer-events-none"></div>

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
            <span class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">RESET PASSWORD</span>
          </div>
          <p class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-widest">
            Step {{ step }} of 2 — {{ step === 1 ? 'REQUEST CODE' : 'NEW PASSWORD' }}
          </p>
        </div>

        <div class="p-6 sm:p-7 space-y-3.5">

          <!-- Status Message Banner -->
          <div v-if="props.status || page.props.status" class="bg-emerald-500/10 border border-emerald-500/30 rounded-xl p-3 text-emerald-700 dark:text-emerald-300 text-xs flex items-start gap-2 shadow-sm">
            <i class="pi pi-check-circle text-sm text-emerald-500 shrink-0 mt-0.5"></i>
            <span class="leading-tight font-medium text-[11px]">{{ props.status || page.props.status }}</span>
          </div>

          <!-- STEP 1: REQUEST VERIFICATION CODE -->
          <form v-if="step === 1" @submit.prevent="onRequestCode" class="space-y-3">
            <div class="space-y-1">
              <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 inline-flex items-center gap-1.5">
                <i class="pi pi-user text-blue-500 text-xs shrink-0"></i> <span>Email, Student ID, or Phone</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                  <i class="pi pi-id-card text-xs shrink-0"></i>
                </div>
                <input
                  v-model="requestForm.email"
                  type="text"
                  required
                  placeholder="email@domain.com, STU-2026-..., or 012..."
                  class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700/80 bg-white dark:bg-slate-800/60 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-medium"
                />
              </div>
              <span v-if="requestForm.errors.email" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ requestForm.errors.email }}</span>
            </div>

            <button
              type="submit"
              :disabled="requestForm.processing"
              class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-md transition inline-flex items-center justify-center gap-2 disabled:opacity-50 text-xs uppercase tracking-wider cursor-pointer"
            >
              <i v-if="requestForm.processing" class="pi pi-spin pi-spinner text-xs shrink-0"></i>
              <i v-else class="pi pi-send text-xs shrink-0"></i>
              <span>{{ requestForm.processing ? 'Sending...' : 'SEND OTP CODE' }}</span>
            </button>
          </form>

          <!-- STEP 2: ENTER OTP & NEW PASSWORD -->
          <form v-else @submit.prevent="onResetPassword" class="space-y-3">
            
            <div class="space-y-1">
              <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 inline-flex items-center gap-1.5">
                <i class="pi pi-user text-blue-500 text-xs shrink-0"></i> <span>Account Identifier</span>
              </label>
              <input
                v-model="resetForm.email"
                type="text"
                required
                readonly
                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700/80 bg-slate-100 dark:bg-slate-900/80 text-slate-700 dark:text-slate-300 transition text-xs font-medium cursor-not-allowed"
              />
              <span v-if="resetForm.errors.email" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ resetForm.errors.email }}</span>
            </div>

            <!-- OTP Code Inputs -->
            <div class="space-y-1">
              <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 inline-flex items-center gap-1.5">
                <i class="pi pi-key text-blue-500 text-xs shrink-0"></i> <span>Enter 6-Digit OTP Code *</span>
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
                  class="w-full py-2.5 text-center text-sm font-bold font-mono rounded-xl border border-slate-300 dark:border-slate-700/80 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                />
              </div>
              <span v-if="resetForm.errors.code" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ resetForm.errors.code }}</span>
            </div>

            <!-- New Passwords -->
            <div class="space-y-1">
              <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 inline-flex items-center gap-1.5">
                <i class="pi pi-lock text-blue-500 text-xs shrink-0"></i> <span>New Password *</span>
              </label>
              <div class="relative">
                <input
                  v-model="resetForm.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  placeholder="••••••••••••••••"
                  class="w-full pl-3 pr-16 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700/80 bg-white dark:bg-slate-800/60 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-medium"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-2 top-1.5 bottom-1.5 px-2 rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700/60 dark:hover:bg-slate-700 text-[10px] text-slate-700 dark:text-slate-300 font-medium transition inline-flex items-center gap-1 border border-slate-300/80 dark:border-slate-600/40"
                >
                  <i :class="['pi text-[10px] shrink-0', showPassword ? 'pi-eye-slash' : 'pi-eye']"></i>
                  <span>{{ showPassword ? 'Hide' : 'Show' }}</span>
                </button>
              </div>
              <span v-if="resetForm.errors.password" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ resetForm.errors.password }}</span>
            </div>

            <div class="space-y-1">
              <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 inline-flex items-center gap-1.5">
                <i class="pi pi-lock text-blue-500 text-xs shrink-0"></i> <span>Confirm New Password *</span>
              </label>
              <input
                v-model="resetForm.password_confirmation"
                :type="showPassword ? 'text' : 'password'"
                required
                placeholder="••••••••••••••••"
                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700/80 bg-white dark:bg-slate-800/60 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-medium"
              />
              <span v-if="resetForm.password && resetForm.password_confirmation && resetForm.password !== resetForm.password_confirmation" class="text-[10px] text-red-500 dark:text-red-400 block font-medium inline-flex items-center gap-1">
                <i class="pi pi-exclamation-triangle shrink-0"></i> <span>Passwords do not match</span>
              </span>
              <span v-if="resetForm.errors.password_confirmation" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ resetForm.errors.password_confirmation }}</span>
            </div>

            <button
              type="submit"
              :disabled="resetForm.processing"
              class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-md transition inline-flex items-center justify-center gap-2 disabled:opacity-50 text-xs uppercase tracking-wider cursor-pointer"
            >
              <i v-if="resetForm.processing" class="pi pi-spin pi-spinner text-xs shrink-0"></i>
              <i v-else class="pi pi-check-circle text-xs shrink-0"></i>
              <span>{{ resetForm.processing ? 'Updating...' : 'RESET PASSWORD & LOGIN' }}</span>
            </button>
          </form>

          <div class="text-xs text-slate-500 dark:text-slate-400 border-t border-slate-200 dark:border-slate-800/80 pt-2.5 text-center font-medium">
            <Link href="/login" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 inline-flex items-center justify-center gap-1.5 transition-colors">
              <i class="pi pi-arrow-left text-xs shrink-0"></i>
              <span>Back to Login</span>
            </Link>
          </div>

        </div>

      </div>
    </div>
  </div>
</template>
