<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

const logoUrl = '/images/logo.png'

const form = useForm({
  email: '',
  password: '',
  role: '' as '' | 'student' | 'teacher' | 'admin',
  remember: false,
})

const showPassword = ref(false)
const socialNotice = ref<string | null>(null)
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

const selectRole = (roleVal: 'student' | 'teacher' | 'admin') => {
  if (form.role === roleVal) {
    form.role = ''
  } else {
    form.role = roleVal
  }
}

const submit = () => {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  })
}

const handleSocialLogin = (provider: string) => {
  socialNotice.value = `ការចូលប្រើតាមរយៈ ${provider} នឹងត្រូវបានតភ្ជាប់ក្នុងពេលឆាប់ៗនេះ។`
  setTimeout(() => {
    socialNotice.value = null
  }, 4000)
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
    <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-indigo-500/10 dark:bg-indigo-600/5 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Glassmorphism Card Container -->
    <div class="max-w-[500px] w-full p-[1px] rounded-2xl bg-gradient-to-b from-blue-500/30 via-slate-200 to-slate-300 dark:from-blue-500/25 dark:via-[#1C2951] dark:to-slate-800/40 shadow-2xl shadow-slate-300/60 dark:shadow-blue-950/40 relative z-10 my-auto transition-all">
      <div class="w-full bg-white/95 dark:bg-[#111A36]/95 backdrop-blur-xl rounded-[15px] overflow-hidden transition-colors">
        
        <!-- Sleek Header -->
        <div class="bg-slate-50/90 dark:bg-[#0E1630]/90 border-b border-slate-200 dark:border-[#1C2951] px-7 py-4.5 text-center space-y-2 relative">
          <!-- Soft Accent Light Line -->
          <div class="absolute top-0 left-1/4 right-1/4 h-[1px] bg-gradient-to-r from-transparent via-blue-500/40 dark:via-blue-400/30 to-transparent"></div>

          <div class="flex flex-col items-center justify-center gap-2 pb-0.5">
            <img
              :src="logoUrl"
              alt="E-LMS Logo"
              class="w-16 h-16 rounded-full shadow-md object-cover ring-2 ring-blue-500/30 ring-offset-2 ring-offset-slate-50 dark:ring-offset-[#0E1630]"
            />
            <span class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">E.LMS</span>
          </div>
          <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-widest">
            Learning Management System
          </p>
        </div>

        <div class="p-7 sm:p-8 space-y-4">
        
          <!-- Global Error Alert -->
          <div v-if="form.errors.email" class="bg-red-500/10 border border-red-500/30 rounded-xl p-3 text-red-600 dark:text-red-300 text-xs flex items-start gap-2 shadow-sm">
            <i class="pi pi-exclamation-circle text-sm text-red-500 shrink-0 mt-0.5"></i>
            <span class="leading-tight font-medium text-[11px]">{{ form.errors.email }}</span>
          </div>

          <!-- Social Notice -->
          <div v-if="socialNotice" class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-2.5 text-blue-700 dark:text-blue-300 text-xs flex items-center gap-2">
            <i class="pi pi-info-circle text-blue-500 text-sm"></i>
            <span class="text-[11px]">{{ socialNotice }}</span>
          </div>

          <!-- Login Form -->
          <form @submit.prevent="submit" class="space-y-3">
            
            <!-- Email / Student ID / Phone Input -->
            <div class="space-y-1">
              <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 inline-flex items-center gap-1.5">
                <i class="pi pi-user text-blue-500 text-xs shrink-0"></i> <span>Email, Student ID, or Phone</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                  <i class="pi pi-id-card text-xs shrink-0"></i>
                </div>
                <input
                  v-model="form.email"
                  type="text"
                  required
                  autocomplete="username"
                  placeholder="email@domain.com, STU-2026-..., or 012..."
                  class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700/80 bg-white dark:bg-slate-800/60 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-medium"
                />
              </div>
            </div>

            <!-- Password Input -->
            <div class="space-y-1">
              <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 inline-flex items-center gap-1.5">
                <i class="pi pi-key text-blue-500 text-xs shrink-0"></i> <span>Password</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                  <i class="pi pi-lock text-xs shrink-0"></i>
                </div>
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  autocomplete="current-password"
                  placeholder="••••••••••••••••"
                  class="w-full pl-9 pr-16 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700/80 bg-white dark:bg-slate-800/60 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-medium"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-2 top-1.5 bottom-1.5 px-2 rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700/60 dark:hover:bg-slate-700 text-[10px] text-slate-700 dark:text-slate-300 font-medium transition inline-flex items-center gap-1 border border-slate-300/80 dark:border-slate-600/40"
                >
                  <i :class="['pi text-[10px] shrink-0', showPassword ? 'pi-eye-slash' : 'pi-eye']"></i>
                  <span class="select-none">{{ showPassword ? 'Hide' : 'Show' }}</span>
                </button>
              </div>
              <span v-if="form.errors.password" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ form.errors.password }}</span>
            </div>

            <!-- Role Select Pills -->
            <div class="bg-slate-100 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 rounded-xl p-2.5 space-y-1.5">
              <div class="flex items-center justify-between">
                <label class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 inline-flex items-center gap-1.5">
                  <i class="pi pi-user-edit text-blue-500 text-xs shrink-0"></i> <span>Login As <span class="text-slate-400 dark:text-slate-500 font-normal lowercase">(Optional)</span></span>
                </label>
                <button
                  v-if="form.role"
                  type="button"
                  @click="form.role = ''"
                  class="text-[9px] text-blue-600 dark:text-blue-400 hover:text-blue-500 font-medium"
                >
                  Clear
                </button>
              </div>
              <div class="grid grid-cols-3 gap-1.5 text-xs">
                <button
                  type="button"
                  @click="selectRole('student')"
                  :class="[
                    'inline-flex items-center justify-center gap-1.5 py-1.5 px-1.5 rounded-lg cursor-pointer border transition text-center select-none font-semibold text-[11px]',
                    form.role === 'student'
                      ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                      : 'bg-white dark:bg-slate-800/60 border-slate-200 dark:border-slate-700/60 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
                  ]"
                >
                  <i class="pi pi-user text-xs shrink-0"></i>
                  <span>Student</span>
                </button>

                <button
                  type="button"
                  @click="selectRole('teacher')"
                  :class="[
                    'inline-flex items-center justify-center gap-1.5 py-1.5 px-1.5 rounded-lg cursor-pointer border transition text-center select-none font-semibold text-[11px]',
                    form.role === 'teacher'
                      ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm'
                      : 'bg-white dark:bg-slate-800/60 border-slate-200 dark:border-slate-700/60 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
                  ]"
                >
                  <i class="pi pi-id-card text-xs shrink-0"></i>
                  <span>Teacher</span>
                </button>

                <button
                  type="button"
                  @click="selectRole('admin')"
                  :class="[
                    'inline-flex items-center justify-center gap-1.5 py-1.5 px-1.5 rounded-lg cursor-pointer border transition text-center select-none font-semibold text-[11px]',
                    form.role === 'admin'
                      ? 'bg-purple-600 text-white border-purple-600 shadow-sm'
                      : 'bg-white dark:bg-slate-800/60 border-slate-200 dark:border-slate-700/60 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
                  ]"
                >
                  <i class="pi pi-shield text-xs shrink-0"></i>
                  <span>Admin</span>
                </button>
              </div>
              <span v-if="form.errors.role" class="text-[10px] text-red-500 dark:text-red-400 block font-medium">{{ form.errors.role }}</span>
            </div>

            <!-- Remember me -->
            <div class="flex items-center justify-between text-xs pt-0.5">
              <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input
                  v-model="form.remember"
                  type="checkbox"
                  class="rounded border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-blue-600 focus:ring-blue-500 h-3.5 w-3.5 cursor-pointer shrink-0"
                />
                <span class="text-slate-700 dark:text-slate-300 font-medium text-[11px]">Remember me</span>
              </label>
            </div>

            <!-- Login Submit Button -->
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-bold rounded-xl shadow-md transition inline-flex items-center justify-center gap-2 disabled:opacity-50 text-xs uppercase tracking-wider cursor-pointer"
            >
              <i v-if="form.processing" class="pi pi-spin pi-spinner text-xs shrink-0"></i>
              <i v-else class="pi pi-sign-in text-xs shrink-0"></i>
              <span>{{ form.processing ? 'Signing In...' : 'LOGIN' }}</span>
            </button>
          </form>

          <!-- Forgot & Register Links Row -->
          <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 pt-2.5 border-t border-slate-200 dark:border-slate-800/80 px-1 font-medium">
            <Link href="/forgot-password" class="hover:text-blue-600 dark:hover:text-blue-400 transition inline-flex items-center gap-1.5">
              <i class="pi pi-question-circle text-xs shrink-0"></i> <span>Forgot Password?</span>
            </Link>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <Link href="/register" class="text-blue-600 dark:text-blue-400 font-semibold hover:text-blue-500 transition inline-flex items-center gap-1.5">
              <i class="pi pi-user-plus text-xs shrink-0"></i> <span>Register Account</span>
            </Link>
          </div>

          <!-- Social Logins Section -->
          <div class="space-y-2 pt-0.5">
            <div class="relative text-center">
              <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200 dark:border-slate-800"></div></div>
              <span class="relative bg-white dark:bg-[#111A36] px-2.5 text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">OR</span>
            </div>

            <div class="grid grid-cols-2 gap-2">
              <button
                type="button"
                @click="handleSocialLogin('Google')"
                class="py-2 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800/60 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700/60 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 transition flex items-center justify-center gap-2 shadow-sm cursor-pointer"
              >
                <i class="pi pi-google text-red-500 text-xs"></i> Google
              </button>
              <button
                type="button"
                @click="handleSocialLogin('Telegram')"
                class="py-2 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800/60 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700/60 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 transition flex items-center justify-center gap-2 shadow-sm cursor-pointer"
              >
                <i class="pi pi-telegram text-sky-500 text-xs"></i> Telegram
              </button>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>
</template>
