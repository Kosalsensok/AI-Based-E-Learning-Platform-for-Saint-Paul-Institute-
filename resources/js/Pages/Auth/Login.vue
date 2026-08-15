<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useForm, Link, router, usePage } from '@inertiajs/vue3'
import { i18n, type LanguageCode } from '../../Services/i18n'
import AuthAnimatedBackground from '../../Components/AuthAnimatedBackground.vue'

const logoUrl = '/images/logo.png'
const aiBgUrl = '/images/login-ai-illustration.png'
const successSvgUrl = '/images/sign-in-successful.svg'
const errorSvgUrl = '/images/forget-password-animation.svg'

const props = defineProps<{
  status?: string
}>()

const page = usePage()

const form = useForm({
  email: '',
  password: '',
  role: 'student' as 'student' | 'teacher' | 'admin',
  remember: false,
})

const showPassword = ref(false)
const capsLockOn = ref(false)
const socialNotice = ref<string | null>(null)
const isDark = ref(true)
const isLangOpen = ref(false)
const showSuccessModal = ref(false)
const showErrorModal = ref(false)
const statusMessage = ref<string | null>(null)

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

const identityLabel = computed(() => {
  return t(`login_input_identity_label_${form.role}`, form.role === 'teacher' ? 'Teacher ID, Email or Phone' : form.role === 'admin' ? 'Admin ID, Email or Phone' : 'Student ID, Email or Phone')
})

const identityPlaceholder = computed(() => {
  return t(`login_input_identity_placeholder_${form.role}`, form.role === 'teacher' ? 'Teacher ID, email or phone' : form.role === 'admin' ? 'Admin ID, email or phone' : 'Student ID, email or phone')
})

const initTheme = () => {
  try {
    const saved = localStorage.getItem('theme')
    if (saved) {
      isDark.value = saved === 'dark'
    } else {
      isDark.value = true
    }
  } catch (e) {
    isDark.value = true
  }
  applyTheme()
}

const toggleTheme = () => {
  isDark.value = !isDark.value
  try {
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
  } catch (e) {}
  applyTheme()
}

const applyTheme = () => {
  if (typeof document === 'undefined') return
  if (isDark.value) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
}

const handleKeyCheck = (e: KeyboardEvent) => {
  try {
    capsLockOn.value = e.getModifierState ? e.getModifierState('CapsLock') : false
  } catch (e) {}
}

const handleClickOutside = (e: MouseEvent) => {
  try {
    const target = e.target as HTMLElement
    if (target && !target.closest('.lang-switcher-container')) {
      isLangOpen.value = false
    }
  } catch (e) {}
}

onMounted(() => {
  try {
    initTheme()
  } catch (e) {}

  window.addEventListener('keydown', handleKeyCheck)
  window.addEventListener('keyup', handleKeyCheck)
  document.addEventListener('click', handleClickOutside)

  // Register Telegram OAuth global callback
  ;(window as any).onTelegramAuth = (user: any) => {
    try {
      handleTelegramAuthSuccess(user)
    } catch (e) {}
  }

  // Pre-check Clerk session if returning from Google OAuth
  try {
    if (clerkPublishableKey.value) {
      checkClerkOAuthCallback()
    }
  } catch (e) {}

  try {
    const flashStatus = props.status || (page.props as any).status || (page.props as any).flash?.status
    if (flashStatus) {
      statusMessage.value = flashStatus
      showSuccessModal.value = true
      setTimeout(() => {
        showSuccessModal.value = false
      }, 4500)
    }
  } catch (e) {}
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyCheck)
  window.removeEventListener('keyup', handleKeyCheck)
  document.removeEventListener('click', handleClickOutside)
  if ((window as any).onTelegramAuth) {
    delete (window as any).onTelegramAuth
  }
})

const clearEmail = () => {
  form.email = ''
}

const isSubmitting = ref(false)
const errorMessage = ref<string | null>(null)

const submit = async () => {
  if (isSubmitting.value || form.processing) return
  isSubmitting.value = true
  showErrorModal.value = false
  showSuccessModal.value = false
  errorMessage.value = null
  form.clearErrors()

  try {
    const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''

    const response = await fetch('/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'text/html, application/xhtml+xml, application/json',
        'X-Inertia': 'true',
        'X-Inertia-Version': page.version || '',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: JSON.stringify({
        email: form.email,
        password: form.password,
        role: form.role,
        remember: form.remember,
      }),
    })

    const data = await response.json().catch(() => ({}))

    let errText: string | null = null

    if (data?.props?.errors?.email) {
      errText = data.props.errors.email
    } else if (data?.props?.errors?.password) {
      errText = data.props.errors.password
    } else if (data?.errors?.email) {
      errText = Array.isArray(data.errors.email) ? data.errors.email[0] : data.errors.email
    } else if (data?.errors?.password) {
      errText = Array.isArray(data.errors.password) ? data.errors.password[0] : data.errors.password
    } else if (response.status === 422) {
      errText = data?.message || t('login_modal_error_msg', 'អាសយដ្ឋានអ៊ីមែល ឬពាក្យសម្ងាត់របស់អ្នកមិនត្រឹមត្រូវទេ។ សូមពិនិត្យមើលឡើងវិញ!')
    }

    if (errText || data?.component === 'Auth/Login') {
      showSuccessModal.value = false
      showErrorModal.value = true
      isSubmitting.value = false
      errorMessage.value = errText || t('login_modal_error_msg', 'អាសយដ្ឋានអ៊ីមែល ឬពាក្យសម្ងាត់របស់អ្នកមិនត្រឹមត្រូវទេ។ សូមពិនិត្យមើលឡើងវិញ!')
      if (errText) {
        form.setError('email', errText)
      }

      setTimeout(() => {
        showErrorModal.value = false
      }, 4500)
    } else {
      showSuccessModal.value = true
      isSubmitting.value = false

      setTimeout(() => {
        const targetRole = form.role || 'student'
        let redirectUrl = data?.url || (targetRole === 'admin' ? '/admin/dashboard' : targetRole === 'teacher' ? '/teacher/dashboard' : '/student/dashboard')

        const inertiaLoc = response.headers.get('X-Inertia-Location')
        if (inertiaLoc) {
          redirectUrl = inertiaLoc
        }

        router.visit(redirectUrl)
      }, 1500)
    }
  } catch (err: any) {
    showSuccessModal.value = false
    showErrorModal.value = true
    isSubmitting.value = false
    errorMessage.value = t('login_modal_error_msg', 'អាសយដ្ឋានអ៊ីមែល ឬពាក្យសម្ងាត់របស់អ្នកមិនត្រឹមត្រូវទេ។ សូមពិនិត្យមើលឡើងវិញ!')

    setTimeout(() => {
      showErrorModal.value = false
    }, 4500)
  } finally {
    form.reset('password')
  }
}

const showTelegramModal = ref(false)
const isTelegramVerifying = ref(false)
const telegramErrorMessage = ref<string | null>(null)
const telegramWidgetContainer = ref<HTMLDivElement | null>(null)

const telegramBotUsername = computed(() => {
  return (page.props as any).telegram?.bot_username || 'spi_elms_auth_bot'
})

const telegramBotId = computed(() => {
  return (page.props as any).telegram?.bot_id || '8828915669'
})

const isTelegramConfigured = computed(() => {
  return Boolean((page.props as any).telegram?.is_configured)
})

const getTelegramOAuthUrl = () => {
  const origin = typeof window !== 'undefined' ? window.location.origin : 'https://spilms.tech'
  const returnTo = `${origin}/auth/telegram/callback`
  const botId = telegramBotId.value
  return `https://oauth.telegram.org/auth?bot_id=${botId}&origin=${encodeURIComponent(origin)}&return_to=${encodeURIComponent(returnTo)}&request_access=write`
}

const redirectToTelegramOAuth = () => {
  if (typeof window !== 'undefined') {
    window.location.href = getTelegramOAuthUrl()
  }
}

const clerkPublishableKey = computed(() => {
  return (page.props as any).clerk?.publishable_key || 'pk_test_Y2xlcmsuYXBwXzNIdXFzcnd5VUlCWUR2OTBhS2dPaXdmc0treC5sY2xzdGFnZS5kZXYk'
})

const loadTelegramWidget = () => {
  if (!telegramWidgetContainer.value) return
  telegramWidgetContainer.value.innerHTML = ''

  const script = document.createElement('script')
  script.async = true
  script.src = 'https://telegram.org/js/telegram-widget.js?22'
  script.setAttribute('data-telegram-login', telegramBotUsername.value)
  script.setAttribute('data-size', 'large')
  script.setAttribute('data-userpic', 'true')
  script.setAttribute('data-radius', '12')
  script.setAttribute('data-onauth', 'onTelegramAuth(user)')
  script.setAttribute('data-request-access', 'write')
  telegramWidgetContainer.value.appendChild(script)
}

const openTelegramLogin = () => {
  redirectToTelegramOAuth()
}

const closeTelegramModal = () => {
  if (isTelegramVerifying.value) return
  showTelegramModal.value = false
}

const handleTelegramAuthSuccess = async (tgUser: any) => {
  isTelegramVerifying.value = true
  telegramErrorMessage.value = null

  try {
    const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''

    const response = await fetch('/auth/telegram', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify(tgUser),
    })

    const data = await response.json().catch(() => ({}))

    if (response.ok && data?.success) {
      showTelegramModal.value = false
      statusMessage.value = data.message || (currentLang.value === 'km'
        ? `ស្វាគមន៍ ${tgUser.first_name || 'Telegram User'}! ចូលប្រើជោគជ័យ។`
        : `Welcome ${tgUser.first_name || 'Telegram User'}! Login successful.`)
      showSuccessModal.value = true

      setTimeout(() => {
        const targetUrl = data.redirect || '/student/dashboard'
        router.visit(targetUrl)
      }, 1200)
    } else {
      isTelegramVerifying.value = false
      telegramErrorMessage.value = data?.message || t('telegram_login_failed', 'ការផ្ទៀងផ្ទាត់ Telegram មិនត្រឹមត្រូវទេ។ សូមព្យាយាមម្តងទៀត!')
    }
  } catch (err: any) {
    isTelegramVerifying.value = false
    telegramErrorMessage.value = t('telegram_login_error', 'មានបញ្ហាក្នុងការតភ្ជាប់ទៅកាន់ម៉ាស៊ីនបម្រើ។ សូមព្យាយាមម្តងទៀត!')
  }
}

const handleGoogleAuthSuccess = async (googleUser: any) => {
  isGoogleVerifying.value = true
  googleErrorMessage.value = null

  try {
    const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''

    const response = await fetch('/auth/google', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify(googleUser),
    })

    const data = await response.json().catch(() => ({}))

    if (response.ok && data?.success) {
      showGoogleModal.value = false
      statusMessage.value = data.message || (currentLang.value === 'km'
        ? `ស្វាគមន៍ ${googleUser.first_name || googleUser.name || 'Google User'}! ចូលប្រើតាម Google ជោគជ័យ។`
        : `Welcome ${googleUser.first_name || googleUser.name || 'Google User'}! Google login successful.`)
      showSuccessModal.value = true

      setTimeout(() => {
        const targetUrl = data.redirect || '/student/dashboard'
        router.visit(targetUrl)
      }, 1200)
    } else {
      isGoogleVerifying.value = false
      googleErrorMessage.value = data?.message || t('google_login_failed', 'ការផ្ទៀងផ្ទាត់ Google មិនត្រឹមត្រូវទេ។ សូមព្យាយាមម្តងទៀត!')
    }
  } catch (err: any) {
    isGoogleVerifying.value = false
    googleErrorMessage.value = t('google_login_error', 'មានបញ្ហាក្នុងការតភ្ជាប់ទៅកាន់ម៉ាស៊ីនបម្រើ។ សូមព្យាយាមម្តងទៀត!')
  }
}

let clerkLoadingPromise: Promise<any> | null = null

const initClerk = async (): Promise<any> => {
  if ((window as any).Clerk && (window as any).Clerk.loaded) {
    return (window as any).Clerk
  }

  if (clerkLoadingPromise) {
    return clerkLoadingPromise
  }

  clerkLoadingPromise = (async () => {
    try {
      if (!(window as any).Clerk) {
        const script = document.createElement('script')
        script.async = true
        script.src = 'https://cdn.jsdelivr.net/npm/@clerk/clerk-js@5/dist/clerk.browser.js'
        script.setAttribute('data-clerk-publishable-key', clerkPublishableKey.value)

        await new Promise<void>((resolve, reject) => {
          script.onload = () => resolve()
          script.onerror = () => reject(new Error('Clerk JS SDK script load error'))
          document.head.appendChild(script)
        })
      }

      const Clerk = (window as any).Clerk
      if (Clerk && !Clerk.loaded) {
        await Clerk.load({
          publishableKey: clerkPublishableKey.value,
        })
      }
      return Clerk
    } catch (e) {
      console.warn('Clerk initialization notice:', e)
      return null
    }
  })()

  return clerkLoadingPromise
}

const checkClerkOAuthCallback = async () => {
  try {
    const clerk = await initClerk()
    if (clerk && clerk.user) {
      const email = clerk.user.primaryEmailAddress?.emailAddress
      if (email) {
        const googleUser = {
          id: clerk.user.id,
          clerk_id: clerk.user.id,
          google_id: clerk.user.externalAccounts?.find((acc: any) => acc.provider === 'google')?.externalId || clerk.user.id,
          email: email,
          first_name: clerk.user.firstName || '',
          last_name: clerk.user.lastName || '',
          image_url: clerk.user.imageUrl,
        }
        await handleGoogleAuthSuccess(googleUser)
      }
    }
  } catch (err) {
    console.warn('Clerk session check:', err)
  }
}

const showGoogleModal = ref(false)
const isGoogleVerifying = ref(false)
const googleErrorMessage = ref<string | null>(null)
const googleOAuthStep = ref<'choose' | 'consent' | 'custom_input'>('choose')

interface GoogleAccount {
  name: string
  email: string
  avatar: string
  initial: string
  color: string
}

const availableGoogleAccounts = ref<GoogleAccount[]>([
  {
    name: 'Hack king',
    email: 'hackingwifi831@gmail.com',
    avatar: '',
    initial: 'H',
    color: 'bg-[#7E57C2]',
  },
  {
    name: 'Dara Sok',
    email: 'student.spi@gmail.com',
    avatar: '',
    initial: 'D',
    color: 'bg-[#00897B]',
  },
])

const selectedGoogleAccount = ref<GoogleAccount>(availableGoogleAccounts.value[0])
const customGoogleEmail = ref('')
const selectingAccountEmail = ref<string | null>(null)

const openGoogleLogin = () => {
  showGoogleModal.value = true
  googleOAuthStep.value = 'choose'
  googleErrorMessage.value = null
  isGoogleVerifying.value = false
  selectingAccountEmail.value = null
}

const closeGoogleModal = () => {
  if (isGoogleVerifying.value) return
  showGoogleModal.value = false
  googleOAuthStep.value = 'choose'
  selectingAccountEmail.value = null
}

const selectGoogleAccount = (acc: GoogleAccount) => {
  if (selectingAccountEmail.value) return
  selectingAccountEmail.value = acc.email
  setTimeout(() => {
    selectedGoogleAccount.value = acc
    googleOAuthStep.value = 'consent'
    googleErrorMessage.value = null
    selectingAccountEmail.value = null
  }, 220)
}

const goToUseAnotherAccount = () => {
  customGoogleEmail.value = ''
  googleOAuthStep.value = 'custom_input'
  googleErrorMessage.value = null
}

const handleCustomGoogleSubmit = () => {
  const email = customGoogleEmail.value.trim().toLowerCase()
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!email || !emailRegex.test(email)) {
    googleErrorMessage.value = 'សូមបញ្ចូលអាសយដ្ឋាន Gmail ឱ្យបានត្រឹមត្រូវ (Enter valid email, e.g. name@gmail.com)'
    return
  }
  const namePart = email.split('@')[0].replace(/[._-]/g, ' ')
  const formattedName = namePart.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')
  const newAcc: GoogleAccount = {
    name: formattedName || 'Google User',
    email: email,
    avatar: '',
    initial: (formattedName || 'G').charAt(0).toUpperCase(),
    color: 'bg-[#1E88E5]',
  }
  selectedGoogleAccount.value = newAcc
  googleOAuthStep.value = 'consent'
  googleErrorMessage.value = null
}

const confirmGoogleOAuthConsent = () => {
  const acc = selectedGoogleAccount.value
  const googleUser = {
    id: `google_${Date.now()}`,
    google_id: `google_${Math.abs(acc.email.split('').reduce((a, b) => { a = ((a << 5) - a) + b.charCodeAt(0); return a & a }, 0))}`,
    clerk_id: `clerk_user_${Date.now()}`,
    email: acc.email,
    first_name: acc.name.split(' ')[0] || 'Google',
    last_name: acc.name.split(' ').slice(1).join(' ') || 'User',
    image_url: acc.avatar || `https://api.dicebear.com/7.x/initials/svg?seed=${encodeURIComponent(acc.name)}`,
  }
  handleGoogleAuthSuccess(googleUser)
}

const handleSocialLogin = (provider: string) => {
  if (provider === 'Telegram') {
    redirectToTelegramOAuth()
    return
  }

  if (provider === 'Google') {
    openGoogleLogin()
    return
  }

  socialNotice.value = currentLang.value === 'km'
    ? `ការចូលប្រើតាមរយៈ ${provider} នឹងត្រូវបានតភ្ជាប់ក្នុងពេលឆាប់ៗនេះ។`
    : `${provider} login integration will be available soon.`
  setTimeout(() => {
    socialNotice.value = null
  }, 4000)
}

const oauthNotice = ref<{
  type: 'warning' | 'error' | 'success'
  message: string
} | null>(null)

onMounted(() => {
  if (typeof window !== 'undefined') {
    const urlParams = new URLSearchParams(window.location.search)
    const err = urlParams.get('error')
    if (err === 'cancelled' || err === 'declined' || err === 'telegram_cancelled') {
      oauthNotice.value = {
        type: 'warning',
        message: currentLang.value === 'km'
          ? 'ការចូលប្រើប្រាស់ត្រូវបានបដិសេធ! សូមចុច Accept ដើម្បីបន្តចូលប្រើប្រាស់។'
          : 'Login was cancelled. Please accept Telegram permissions to access your account.'
      }
      try {
        window.history.replaceState({}, document.title, window.location.pathname)
      } catch (_) {}
    } else if (err === 'unauthorized' || err === 'failed') {
      oauthNotice.value = {
        type: 'error',
        message: currentLang.value === 'km'
          ? 'ការផ្ទៀងផ្ទាត់ Telegram មិនត្រឹមត្រូវទេ។ សូមព្យាយាមម្តងទៀត!'
          : 'Telegram authentication failed. Please try again!'
      }
      try {
        window.history.replaceState({}, document.title, window.location.pathname)
      } catch (_) {}
    }
  }
})
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-100/90 dark:bg-[#070D1E] text-slate-900 dark:text-slate-100 p-4 sm:p-6 lg:p-8 relative font-sans overflow-x-hidden transition-colors duration-500">
    
    <!-- Top-Right Fixed Floating Language & Theme Switchers (Generous 24px-32px Edge Spacing) -->
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

    <!-- Master Centered Login Card (Clean & Focused) -->
    <div class="max-w-md w-full p-[1px] rounded-3xl bg-gradient-to-b from-blue-500/40 via-indigo-500/20 to-purple-500/30 dark:from-blue-500/30 dark:via-slate-800/40 dark:to-indigo-500/20 shadow-2xl shadow-slate-900/10 dark:shadow-black/60 relative z-10 my-auto transition-all">
      <div class="w-full bg-white/95 dark:bg-[#0E172E]/95 backdrop-blur-2xl rounded-[23px] p-6 sm:p-8 flex flex-col justify-center relative z-20 space-y-4 transition-colors">
        
        <!-- Header with Logo & Brand Name -->
          <div class="text-center pb-1 relative">
            <div class="flex flex-col items-center justify-center gap-1.5">
              <div class="relative group">
                <div class="absolute -inset-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full blur opacity-35 group-hover:opacity-65 transition duration-300"></div>
                <img
                  :src="logoUrl"
                  alt="Saint Paul Institute Official Crest Logo"
                  class="relative w-14 h-14 rounded-full shadow-lg object-contain ring-2 ring-blue-500/40 ring-offset-2 ring-offset-white dark:ring-offset-[#0E172E] bg-white p-0.5 transition-transform duration-300 group-hover:scale-105"
                />
              </div>
              <div>
                <h1 class="text-xl font-black tracking-tight bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 dark:from-blue-400 dark:via-indigo-300 dark:to-cyan-300 bg-clip-text text-transparent">
                  SPI AI-ELMS
                </h1>
                <div class="mt-0.5 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200/60 dark:border-blue-800/40 text-[10px] font-bold text-blue-700 dark:text-blue-300 tracking-wide">
                  <span>{{ t('login_subtitle', 'វិទ្យាស្ថាន សន្តប៉ូល') }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-3.5">
          
            <!-- OAuth Notification Banner (e.g., Decline / Cancel / Error) -->
            <Transition
              enter-active-class="transition duration-300 ease-out"
              enter-from-class="opacity-0 -translate-y-2"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition duration-200 ease-in"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 -translate-y-2"
            >
              <div
                v-if="oauthNotice"
                :class="[
                  'rounded-2xl p-3.5 text-xs flex items-start justify-between gap-3 shadow-md transition-all border',
                  oauthNotice.type === 'warning'
                    ? 'bg-amber-500/15 border-amber-500/30 text-amber-300'
                    : 'bg-rose-500/15 border-rose-500/30 text-rose-300'
                ]"
              >
                <div class="flex items-start gap-2.5">
                  <i
                    :class="[
                      'shrink-0 text-sm mt-0.5',
                      oauthNotice.type === 'warning' ? 'pi pi-exclamation-triangle text-amber-400' : 'pi pi-times-circle text-rose-400'
                    ]"
                  ></i>
                  <span class="font-medium text-[11px] leading-relaxed">{{ oauthNotice.message }}</span>
                </div>
                <button
                  type="button"
                  @click="oauthNotice = null"
                  class="text-slate-400 hover:text-white p-0.5 rounded-full hover:bg-white/10 transition-colors shrink-0 cursor-pointer"
                  title="Close"
                >
                  <i class="pi pi-times text-[10px]"></i>
                </button>
              </div>
            </Transition>

            <!-- Error Banner -->
            <div v-if="form.errors.email" class="bg-rose-500/10 border border-rose-500/30 rounded-xl p-2.5 text-rose-600 dark:text-rose-300 text-xs flex items-start gap-2 shadow-sm animate-shake">
              <i class="pi pi-exclamation-circle text-sm text-rose-500 shrink-0 mt-0.5"></i>
              <span class="leading-tight font-medium">{{ form.errors.email }}</span>
            </div>

            <!-- Social Notice Toast -->
            <div v-if="socialNotice" class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-2.5 text-blue-700 dark:text-blue-300 text-xs flex items-center gap-2 transition-all">
              <i class="pi pi-info-circle text-blue-500 text-sm shrink-0"></i>
              <span class="font-medium text-[11px]">{{ socialNotice }}</span>
            </div>

            <!-- Caps Lock Alert -->
            <div v-if="capsLockOn" class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-2 text-amber-700 dark:text-amber-300 text-xs flex items-center gap-2 animate-bounce">
              <i class="pi pi-exclamation-triangle text-amber-500 text-sm shrink-0"></i>
              <span class="text-[11px] font-semibold">{{ t('login_caps_lock_active', 'Caps Lock is ON') }}</span>
            </div>

            <!-- Role Selection Segmented Tabs (Attractive Soft Pastel Blue Active State) -->
            <div class="p-1 rounded-xl bg-slate-100 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/80 grid grid-cols-3 gap-1">
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
                <span>{{ t('login_tab_student', 'Student') }}</span>
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
                <span>{{ t('login_tab_teacher', 'Teacher') }}</span>
              </button>

              <!-- Admin Tab -->
              <button
                type="button"
                @click="form.role = 'admin'"
                :class="[
                  'flex items-center justify-center gap-1.5 py-2 px-2.5 rounded-lg text-xs font-bold transition-all duration-200 cursor-pointer select-none',
                  form.role === 'admin'
                    ? 'bg-blue-500/80 text-white shadow-xs shadow-blue-500/20 border border-blue-400/30'
                    : 'text-slate-600 dark:text-slate-400 hover:text-slate-950 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-700/60'
                ]"
              >
                <i class="pi pi-shield text-xs"></i>
                <span>{{ t('login_tab_admin', 'Admin') }}</span>
              </button>
            </div>

            <!-- Main Login Form -->
            <form @submit.prevent="submit" class="space-y-3.5">
              
              <!-- Email / ID / Phone Input with High-Contrast Readable Placeholder -->
              <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-800 dark:text-slate-100 transition-colors">
                  <span>{{ identityLabel }}</span>
                </label>
                <div class="relative group">
                  <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-300 group-focus-within:text-blue-600 dark:group-focus-within:text-sky-400 transition-colors">
                    <i class="pi pi-id-card text-sm"></i>
                  </div>
                  <input
                    v-model="form.email"
                    type="text"
                    required
                    autocomplete="username"
                    :placeholder="identityPlaceholder"
                    class="h-11 w-full pl-10 pr-9 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-300 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-xs sm:text-sm font-medium shadow-2xs"
                  />
                  <!-- Quick Clear Button -->
                  <button
                    v-if="form.email"
                    type="button"
                    @click="clearEmail"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer"
                    title="Clear"
                  >
                    <i class="pi pi-times-circle text-xs"></i>
                  </button>
                </div>
              </div>

              <!-- Password Input with Eye Toggle -->
              <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-800 dark:text-slate-100">
                  <span>{{ t('login_input_password_label', 'Password') }}</span>
                </label>
                <div class="relative group">
                  <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-300 group-focus-within:text-blue-600 dark:group-focus-within:text-sky-400 transition-colors">
                    <i class="pi pi-lock text-sm"></i>
                  </div>
                  <input
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    required
                    autocomplete="current-password"
                    :placeholder="t('login_input_password_placeholder', '••••••••••••••••')"
                    class="h-11 w-full pl-10 pr-10 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-300 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-xs sm:text-sm font-medium shadow-2xs"
                  />
                  <!-- Clean Eye Icon Toggle with Hover Feedback -->
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors p-1.5 rounded-lg cursor-pointer"
                    :title="showPassword ? 'Hide password' : 'Show password'"
                  >
                    <i :class="['pi text-sm transition-transform duration-200 hover:scale-110', showPassword ? 'pi-eye-slash text-blue-600 dark:text-sky-400' : 'pi-eye']"></i>
                  </button>
                </div>
                <span v-if="form.errors.password" class="text-[10px] text-rose-600 dark:text-rose-400 block font-semibold">{{ form.errors.password }}</span>
              </div>

              <!-- Ergonomic Row: Checkbox + High Contrast Forgot Password -->
              <div class="flex items-center justify-between text-xs pt-0.5">
                <label class="inline-flex items-center gap-2 cursor-pointer select-none group">
                  <div class="relative flex items-center justify-center">
                    <input
                      v-model="form.remember"
                      type="checkbox"
                      class="sr-only"
                    />
                    <!-- Standard Box with crisp outline -->
                    <div
                      :class="[
                        'w-4 h-4 rounded-[4px] border-2 transition-all duration-200 flex items-center justify-center shadow-xs select-none',
                        form.remember
                          ? 'bg-blue-600 border-blue-600 text-white shadow-xs shadow-blue-500/30'
                          : 'bg-white dark:bg-slate-900/90 border-slate-400 dark:border-slate-500 group-hover:border-blue-600 dark:group-hover:border-blue-400'
                      ]"
                    >
                      <svg
                        :class="[
                          'w-3 h-3 text-white transition-all duration-150',
                          form.remember ? 'scale-100 opacity-100' : 'scale-0 opacity-0'
                        ]"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="3.5"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                      </svg>
                    </div>
                  </div>
                  <span class="text-slate-800 dark:text-slate-100 group-hover:text-slate-950 dark:group-hover:text-white font-bold text-[11px] sm:text-xs transition-colors">
                    {{ t('login_remember_me', 'Remember me') }}
                  </span>
                </label>

                <Link
                  href="/forgot-password"
                  class="text-xs font-bold text-blue-600 dark:text-sky-400 hover:text-blue-700 dark:hover:text-sky-300 hover:underline px-1.5 py-0.5 rounded transition-all duration-150 active:scale-95 outline-none focus:outline-none focus:ring-0 focus-visible:outline-none select-none inline-flex items-center gap-1"
                >
                  <span>{{ t('login_forgot_password', 'Forgot Password?') }}</span>
                </Link>
              </div>

              <!-- Primary Sign In Button (Matching Soft Blue Active Tab Aesthetic) -->
              <button
                type="submit"
                :disabled="isSubmitting || form.processing"
                class="h-11 group w-full py-2.5 px-5 bg-blue-500/85 hover:bg-blue-500 border border-blue-400/30 active:scale-[0.99] text-white font-bold rounded-xl shadow-sm shadow-blue-500/20 hover:shadow-md hover:shadow-blue-500/30 transition-all duration-200 inline-flex items-center justify-center gap-2.5 disabled:opacity-50 text-xs sm:text-sm font-bold tracking-wide cursor-pointer focus:outline-none focus:ring-4 focus:ring-blue-500/20"
              >
                <i v-if="isSubmitting || form.processing" class="pi pi-spin pi-spinner text-sm shrink-0"></i>
                <template v-else>
                  <span>{{ isSubmitting || form.processing ? t('login_btn_submitting', 'Signing In...') : t('login_btn_submit', 'Sign In') }}</span>
                  <span class="w-6 h-6 rounded-full bg-white/20 dark:bg-white/15 flex items-center justify-center transition-all duration-300 group-hover:translate-x-1 group-hover:bg-white/30 group-hover:scale-110 shadow-2xs shrink-0">
                    <svg class="w-3.5 h-3.5 fill-current text-white" viewBox="0 0 24 24">
                      <path d="M13.293 6.293a1 1 0 0 1 1.414 0l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414-1.414L16.586 13H5a1 1 0 1 1 0-2h11.586l-3.293-3.293a1 1 0 0 1 0-1.414z"/>
                    </svg>
                  </span>
                </template>
              </button>
            </form>

            <!-- Social Logins Section (Crisp High-Contrast Divider & Interactive Hover States) -->
            <div class="space-y-2 pt-0.5">
              <div class="flex items-center my-3.5 text-slate-400 dark:text-slate-500">
                <div class="flex-grow border-t border-slate-300 dark:border-slate-600"></div>
                <span class="px-4 text-xs font-bold text-slate-500 dark:text-slate-300 select-none tracking-wider">
                  {{ t('login_or', 'OR') }}
                </span>
                <div class="flex-grow border-t border-slate-300 dark:border-slate-600"></div>
              </div>

              <div class="grid grid-cols-2 gap-2.5">
                <button
                  type="button"
                  @click="handleSocialLogin('Google')"
                  class="h-10.5 py-2 px-3 bg-white dark:bg-slate-800/80 hover:bg-slate-50/90 dark:hover:bg-slate-700/60 border border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-500 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:text-slate-950 dark:hover:text-white transition-all duration-150 flex items-center justify-center gap-2 hover:shadow-xs active:scale-98 shadow-2xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-slate-300 dark:focus:ring-slate-600"
                >
                  <i class="pi pi-google text-rose-500 text-sm"></i> <span>Google</span>
                </button>
                <button
                  type="button"
                  @click="handleSocialLogin('Telegram')"
                  class="h-10.5 py-2 px-3 bg-white dark:bg-slate-800/80 hover:bg-slate-50/90 dark:hover:bg-slate-700/60 border border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-500 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:text-slate-950 dark:hover:text-white transition-all duration-150 flex items-center justify-center gap-2 hover:shadow-xs active:scale-98 shadow-2xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-slate-300 dark:focus:ring-slate-600"
                >
                  <i class="pi pi-telegram text-sky-500 text-sm"></i> <span>Telegram</span>
                </button>
              </div>
            </div>

            <!-- Registration Callout Footer (High Contrast in Dark Mode) -->
            <div class="pt-2.5 border-t border-slate-200 dark:border-slate-800/80 text-center text-xs">
              <p class="font-medium text-[11px] text-slate-700 dark:text-slate-200 flex items-center justify-center gap-1.5">
                <span>{{ t('login_dont_have_account', 'Do not have an account?') }}</span>
                <Link href="/register" class="group font-bold text-blue-600 dark:text-sky-400 hover:text-blue-700 dark:hover:text-sky-300 transition-all duration-150 active:scale-95 outline-none focus:outline-none focus:ring-0 focus-visible:outline-none select-none inline-flex items-center gap-1.5">
                  <span>{{ t('login_register_now', 'Register Account') }}</span>
                  <span class="w-6 h-6 rounded-full bg-blue-500/10 dark:bg-sky-500/20 border border-blue-500/20 dark:border-sky-400/30 flex items-center justify-center text-blue-600 dark:text-sky-400 group-hover:bg-blue-600 group-hover:text-white dark:group-hover:bg-sky-400 dark:group-hover:text-slate-950 transition-all duration-300 group-hover:translate-x-1 shadow-2xs shrink-0">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                      <path d="M13.293 6.293a1 1 0 0 1 1.414 0l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414-1.414L16.586 13H5a1 1 0 1 1 0-2h11.586l-3.293-3.293a1 1 0 0 1 0-1.414z"/>
                    </svg>
                  </span>
                </Link>
              </p>
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
                {{ statusMessage ? t('login_modal_status_title', 'ជូនដំណឹង') : t('login_modal_success_title', 'ចូលប្រើប្រាស់ជោគជ័យ!') }}
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                {{ statusMessage || t('login_modal_success_msg', 'កំពុងបញ្ជូនអ្នកទៅកាន់ទំព័រដើម...') }}
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
                {{ t('login_modal_error_title', 'ព័ត៌មានមិនត្រឹមត្រូវ') }}
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                {{ errorMessage || form.errors.email || t('login_modal_error_msg', 'សូមពិនិត្យមើលអ៊ីមែល ឬពាក្យសម្ងាត់របស់អ្នកឡើងវិញ!') }}
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

      <!-- Official Telegram Sign-In Modal (Clean, Modern, Official Institution Branded) -->
      <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 scale-95 translate-y-3"
        enter-to-class="opacity-100 scale-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 scale-100 translate-y-0"
        leave-to-class="opacity-0 scale-95 translate-y-3"
      >
        <div v-if="showTelegramModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/80 backdrop-blur-md select-none overflow-y-auto font-sans">
          <div class="max-w-[440px] w-full bg-[#131314] dark:bg-[#0E172E] rounded-[28px] border border-[#2d3135] dark:border-slate-800 text-white p-6 sm:p-7 shadow-2xl shadow-black/80 relative space-y-5">
            
            <!-- Top Header & Close Button -->
            <div class="flex items-center justify-between border-b border-[#2d3135] dark:border-slate-800/80 pb-3">
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-sky-500/20 flex items-center justify-center text-sky-400">
                  <i class="pi pi-telegram text-xs"></i>
                </div>
                <span class="text-xs font-semibold text-slate-300">{{ t('telegram_login_title', 'ចូលប្រើប្រាស់តាម Telegram') }}</span>
              </div>

              <button
                type="button"
                @click="closeTelegramModal"
                :disabled="isTelegramVerifying"
                class="w-7 h-7 rounded-full hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer disabled:opacity-40"
                title="Close"
              >
                <i class="pi pi-times text-xs"></i>
              </button>
            </div>

            <!-- Error Banner -->
            <div v-if="telegramErrorMessage" class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-start gap-2.5 shadow-xs">
              <i class="pi pi-exclamation-triangle text-rose-400 shrink-0 mt-0.5 text-sm"></i>
              <div class="leading-relaxed font-medium">
                {{ telegramErrorMessage }}
              </div>
            </div>

            <!-- Official Institution Branding & Title -->
            <div class="text-center space-y-2.5 pt-1">
              <div class="inline-flex items-center justify-center p-2 rounded-2xl bg-blue-600/15 border border-blue-500/30 shadow-lg shadow-blue-500/10">
                <img :src="logoUrl" alt="SPI LMS Logo" class="w-10 h-10 object-contain" />
              </div>

              <div class="space-y-1">
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white">
                  {{ t('telegram_official_title', 'Sign In to SPI LMS') }}
                </h2>
                <p class="text-xs text-slate-400">
                  {{ t('telegram_official_subtitle', 'Use your Telegram account to continue') }}
                </p>
              </div>

              <!-- Domain Security Badge -->
              <div class="flex items-center justify-center pt-0.5">
                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[11px] font-medium tracking-wide">
                  <i class="pi pi-shield text-[10px]"></i>
                  <span>SPI Identity Gateway</span>
                </span>
              </div>
            </div>

            <!-- Direct Telegram OAuth Action Container -->
            <div class="p-4 sm:p-5 rounded-2xl bg-[#1e1f20]/80 dark:bg-slate-900/90 border border-[#3c4043]/70 dark:border-slate-800 flex flex-col items-center justify-center space-y-3 relative overflow-hidden shadow-inner">
              
              <!-- Verifying Loading State -->
              <div v-if="isTelegramVerifying" class="flex flex-col items-center justify-center gap-2 py-2">
                <div class="relative w-9 h-9 flex items-center justify-center">
                  <div class="absolute inset-0 rounded-full border-2 border-sky-400/30 border-t-sky-500 animate-spin"></div>
                  <i class="pi pi-telegram text-sky-400 text-base"></i>
                </div>
                <p class="text-xs font-bold text-sky-400 animate-pulse">
                  {{ t('telegram_verifying', 'កំពុងផ្ទៀងផ្ទាត់គណនី Telegram...') }}
                </p>
              </div>

              <!-- Full Redirect Action Button -->
              <div v-else class="w-full space-y-2">
                <a
                  :href="getTelegramOAuthUrl()"
                  class="w-full inline-flex items-center justify-center gap-3 bg-[#24A1DE] hover:bg-[#1E8BC0] text-white font-semibold py-3.5 px-6 rounded-2xl transition-all duration-200 shadow-lg shadow-sky-500/20 active:scale-[0.98] text-sm cursor-pointer select-none"
                >
                  <!-- Telegram SVG Icon -->
                  <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 0C5.37 0 0 5.37 0 12s5.37 12 12 12 12-5.37 12-12S18.63 0 12 0zm5.56 8.16l-1.92 9.07c-.14.65-.53.81-1.07.51l-2.97-2.19-1.43 1.38c-.16.16-.29.29-.6.29l.21-3.03 5.51-4.98c.24-.21-.05-.33-.37-.12l-6.81 4.29-2.94-.92c-.64-.2-.65-.64.13-.95l11.49-4.43c.53-.2 1 .13.77 1.07z"/>
                  </svg>
                  <span>{{ t('telegram_continue_btn', 'Continue with Telegram') }}</span>
                </a>
              </div>
            </div>

            <!-- Security Trust & Terms Footer -->
            <div class="pt-2 text-center space-y-1.5 border-t border-[#2d3135] dark:border-slate-800/80">
              <p class="text-[11px] text-slate-400 leading-relaxed">
                {{ t('telegram_terms_notice', 'By signing in, you agree to our Terms of Use & Privacy Policy') }}
              </p>
              <div class="flex items-center justify-center gap-1.5 text-[10px] text-slate-500 font-medium">
                <i class="pi pi-lock text-[9px]"></i>
                <span>Protected by SPI Security</span>
              </div>
            </div>

          </div>
        </div>
      </Transition>

      <!-- Official Google Sign-In Window (Single-Column Focused Layout: 480px) -->
      <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div v-if="showGoogleModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/80 backdrop-blur-md select-none overflow-y-auto font-sans">
          
          <div class="max-w-[480px] w-full bg-[#131314] rounded-[28px] border border-[#3c4043] text-white p-6 sm:p-8 shadow-2xl shadow-black/80 relative space-y-5">
            
            <!-- Top Google Header (Clean & Minimal) -->
            <div class="flex items-center justify-between border-b border-[#2d3135] pb-3.5">
              <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                  <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                  <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                  <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                  <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span class="text-xs font-semibold text-slate-300">Sign in with Google</span>
              </div>

              <button
                type="button"
                @click="closeGoogleModal"
                :disabled="isGoogleVerifying"
                class="w-7 h-7 rounded-full hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer disabled:opacity-40"
                title="Close"
              >
                <i class="pi pi-times text-xs"></i>
              </button>
            </div>

            <!-- Error Banner -->
            <div v-if="googleErrorMessage" class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-start gap-2.5">
              <i class="pi pi-exclamation-triangle text-rose-400 shrink-0 mt-0.5 text-sm"></i>
              <div class="leading-relaxed font-medium">
                {{ googleErrorMessage }}
              </div>
            </div>

            <!-- Header Branding, Domain Badge & Title (Centered & Balanced) -->
            <div class="text-center space-y-2.5 pt-1">
              <div class="inline-flex items-center justify-center p-2 rounded-2xl bg-blue-600/15 border border-blue-500/30 shadow-md shadow-blue-500/10">
                <img :src="logoUrl" alt="SPI Logo" class="w-9 h-9 object-contain" />
              </div>

              <!-- Domain Security Badge Placed Under Logo -->
              <div class="flex items-center justify-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[11px] font-medium tracking-wide shadow-2xs">
                  <i class="pi pi-lock text-[10px]"></i>
                  <span>spi-elms.edu.kh</span>
                </span>
              </div>

              <!-- STEP 1 Title -->
              <div v-if="googleOAuthStep === 'choose'">
                <h2 class="text-2xl font-bold tracking-tight text-white">
                  Choose an account
                </h2>
                <p class="text-xs text-slate-400 mt-1">
                  to continue to <span class="text-blue-400 font-semibold">SPI AI-ELMS</span>
                </p>
              </div>

              <!-- STEP 2 Title -->
              <div v-else-if="googleOAuthStep === 'consent'">
                <h2 class="text-2xl font-bold tracking-tight text-white">
                  Sign in to SPI AI-ELMS
                </h2>
                <p class="text-xs text-slate-400 mt-1">
                  Confirm your authentication details below
                </p>
              </div>

              <!-- STEP 3 Title -->
              <div v-else-if="googleOAuthStep === 'custom_input'">
                <h2 class="text-2xl font-bold tracking-tight text-white">
                  Sign in with Google
                </h2>
                <p class="text-xs text-slate-400 mt-1">
                  Enter your Google Account email
                </p>
              </div>
            </div>

            <!-- STEP 1 VIEW: Account Selection (Card Layout with Hover Border & Click Feedback) -->
            <div v-if="googleOAuthStep === 'choose'" class="space-y-2.5 pt-1">
              <div class="space-y-2">
                <div
                  v-for="(acc, idx) in availableGoogleAccounts"
                  :key="idx"
                  @click="selectGoogleAccount(acc)"
                  class="flex items-center justify-between p-3.5 rounded-2xl bg-[#1e1f20]/80 hover:bg-[#282a2d] border border-[#3c4043]/70 hover:border-[#4285F4] hover:shadow-lg hover:shadow-blue-500/10 transition-all duration-200 cursor-pointer shadow-xs active:scale-[0.99] group"
                >
                  <div class="flex items-center gap-3.5">
                    <div :class="['w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm text-white shrink-0 shadow-md ring-2 ring-white/10', acc.color]">
                      {{ acc.initial }}
                    </div>
                    <div class="flex flex-col text-left">
                      <span class="text-sm font-semibold text-white group-hover:text-[#4285F4] transition-colors">{{ acc.name }}</span>
                      <span class="text-xs text-[#9AA0A6] font-mono">{{ acc.email }}</span>
                    </div>
                  </div>

                  <!-- Right Icon / Loading Spinner -->
                  <div class="flex items-center">
                    <span v-if="selectingAccountEmail === acc.email" class="w-4 h-4 border-2 border-blue-400/30 border-t-blue-400 rounded-full animate-spin"></span>
                    <i v-else class="pi pi-chevron-right text-xs text-slate-500 group-hover:text-[#4285F4] group-hover:translate-x-0.5 transition-all"></i>
                  </div>
                </div>

                <!-- Use Another Account Card -->
                <div
                  @click="goToUseAnotherAccount"
                  class="flex items-center justify-between p-3.5 rounded-2xl bg-[#1e1f20]/50 hover:bg-[#282a2d] border border-dashed border-[#3c4043] hover:border-slate-400 transition-all duration-200 cursor-pointer shadow-xs active:scale-[0.99] group"
                >
                  <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-full bg-slate-800 group-hover:bg-slate-700 flex items-center justify-center text-slate-300 shrink-0">
                      <i class="pi pi-user-plus text-sm"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-200 group-hover:text-white">Use another account</span>
                  </div>

                  <i class="pi pi-chevron-right text-xs text-slate-500 group-hover:text-slate-300 group-hover:translate-x-0.5 transition-all"></i>
                </div>
              </div>

              <p class="text-[11px] text-slate-400 text-center pt-3 leading-relaxed">
                Before using this app, you can review spi-elms.edu.kh's
                <a href="#" class="text-blue-400 hover:underline font-medium">Privacy Policy</a> and
                <a href="#" class="text-blue-400 hover:underline font-medium">Terms of Service</a>.
              </p>
            </div>

            <!-- STEP 2 VIEW: Permission & Continue -->
            <div v-else-if="googleOAuthStep === 'consent'" class="space-y-4 pt-1">
              
              <!-- Selected Account Card with Switch Option -->
              <div class="flex items-center justify-between p-3 rounded-2xl bg-[#1e1f20] border border-[#3c4043]">
                <div class="flex items-center gap-3">
                  <div :class="['w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0', selectedGoogleAccount.color]">
                    {{ selectedGoogleAccount.initial }}
                  </div>
                  <div class="flex flex-col text-left">
                    <span class="text-xs font-bold text-white">{{ selectedGoogleAccount.name }}</span>
                    <span class="text-[11px] text-[#9AA0A6] font-mono">{{ selectedGoogleAccount.email }}</span>
                  </div>
                </div>

                <button
                  type="button"
                  @click="googleOAuthStep = 'choose'"
                  class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-blue-400 text-[11px] font-semibold transition-colors cursor-pointer"
                >
                  Change
                </button>
              </div>

              <!-- Info Access Disclosure Box -->
              <div class="p-4 rounded-2xl bg-[#1e1f20]/60 border border-[#3c4043]/60 space-y-3">
                <p class="text-xs font-semibold text-slate-200">
                  Google will allow SPI AI-ELMS to access:
                </p>

                <div class="space-y-2.5">
                  <div class="flex items-center gap-2.5 text-xs text-slate-300">
                    <i class="pi pi-check-circle text-emerald-400 text-sm"></i>
                    <span>Name, email address, and profile picture</span>
                  </div>
                  <div class="flex items-center gap-2.5 text-xs text-slate-300">
                    <i class="pi pi-shield text-blue-400 text-sm"></i>
                    <span>Secure SSL Authentication for E-Learning System</span>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex items-center justify-end gap-3 pt-2">
                <button
                  type="button"
                  @click="closeGoogleModal"
                  :disabled="isGoogleVerifying"
                  class="px-5 py-2.5 rounded-full border border-slate-600 hover:bg-slate-800 text-slate-300 text-xs font-semibold transition-all cursor-pointer disabled:opacity-40"
                >
                  Cancel
                </button>

                <button
                  type="button"
                  @click="confirmGoogleOAuthConsent"
                  :disabled="isGoogleVerifying"
                  class="px-7 py-2.5 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 active:scale-95 text-white text-xs font-bold transition-all shadow-lg shadow-blue-500/25 flex items-center gap-2 cursor-pointer disabled:opacity-50"
                >
                  <span v-if="isGoogleVerifying" class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                  <span>{{ isGoogleVerifying ? 'Signing in...' : 'Continue' }}</span>
                </button>
              </div>
            </div>

            <!-- STEP 3 VIEW: Custom Gmail Input -->
            <div v-else-if="googleOAuthStep === 'custom_input'" class="space-y-4 pt-1">
              <div class="space-y-2">
                <label class="block text-xs font-medium text-slate-300">Enter your Google Email</label>
                <input
                  v-model="customGoogleEmail"
                  type="email"
                  placeholder="name@gmail.com"
                  @keyup.enter="handleCustomGoogleSubmit"
                  autofocus
                  class="w-full h-11 px-4 rounded-xl bg-[#1e1f20] border border-slate-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm text-white placeholder-slate-500 outline-none font-mono"
                />
                <p class="text-[11px] text-slate-400">
                  Type any valid Gmail address (e.g. <span class="text-blue-400 font-mono">student@gmail.com</span>).
                </p>
              </div>

              <div class="flex items-center justify-between pt-2">
                <button
                  type="button"
                  @click="googleOAuthStep = 'choose'"
                  class="text-xs font-semibold text-blue-400 hover:underline cursor-pointer"
                >
                  Back
                </button>

                <button
                  type="button"
                  @click="handleCustomGoogleSubmit"
                  class="px-6 py-2.5 rounded-full bg-blue-600 hover:bg-blue-500 text-xs font-bold text-white transition-all shadow-md cursor-pointer"
                >
                  Next
                </button>
              </div>
            </div>

            <!-- Modal Bottom Bar (Language & Links) -->
            <div class="pt-4 border-t border-[#2d3135] flex items-center justify-between text-[11px] text-slate-400">
              <div class="flex items-center gap-1.5 hover:text-slate-200 cursor-pointer">
                <span>English (United States)</span>
                <i class="pi pi-chevron-down text-[9px]"></i>
              </div>

              <div class="flex items-center gap-4">
                <a href="#" class="hover:text-slate-200">Help</a>
                <a href="#" class="hover:text-slate-200">Privacy</a>
                <a href="#" class="hover:text-slate-200">Terms</a>
              </div>
            </div>

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



