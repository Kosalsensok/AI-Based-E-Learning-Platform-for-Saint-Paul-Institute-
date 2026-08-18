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
  return 'https://oauth.telegram.org/auth?bot_id=8828915669&origin=https%3A%2F%2Fspilms.tech&return_to=https%3A%2F%2Fspilms.tech%2Fauth%2Ftelegram%2Fcallback&request_access=write'
}



const isAuthenticating = ref(false)
const authLoadingTitle = ref('')
const authLoadingSubtitle = ref('')
const isGoogleLoading = ref(false)
const isTelegramLoading = ref(false)

let activePopup: Window | null = null
let popupCheckTimer: any = null

const stopPopupTracking = () => {
  if (popupCheckTimer) {
    clearInterval(popupCheckTimer)
    popupCheckTimer = null
  }
  activePopup = null
}

const checkPopupClosed = () => {
  if (activePopup && activePopup.closed) {
    stopPopupTracking()
    // User closed the popup (clicked X) without completing authentication
    if (!isAuthenticating.value || (!authLoadingTitle.value.includes('ផ្ទៀងផ្ទាត់') && !authLoadingTitle.value.includes('Verifying'))) {
      isTelegramLoading.value = false
      isGoogleLoading.value = false
      isAuthenticating.value = false
    }
  }
}

const handleTelegramPostMessage = (event: MessageEvent) => {
  const origin = event.origin || ''
  const isAllowedOrigin = origin.includes('telegram.org') || origin.includes('spilms.tech') || (typeof window !== 'undefined' && origin === window.location.origin)
  if (!isAllowedOrigin) return

  if (event.data) {
    try {
      const data = typeof event.data === 'string' ? JSON.parse(event.data) : event.data
      if (data.event === 'auth_result') {
        stopPopupTracking()
        if (data.result === false) {
          // User clicked DECLINE on Telegram popup
          isAuthenticating.value = false
          isTelegramLoading.value = false
          oauthNotice.value = {
            type: 'warning',
            message: currentLang.value === 'km'
              ? 'លោកអ្នកបានបដិសេធការ Login! សូមចុច Accept ដើម្បីចូលប្រើប្រាស់។'
              : 'Login was cancelled. Please accept Telegram permissions to access your account.'
          }
        } else if (data.result && (data.result.id || typeof data.result === 'object')) {
          // User clicked ACCEPT on Telegram popup
          isAuthenticating.value = true
          isTelegramLoading.value = true
          authLoadingTitle.value = currentLang.value === 'km' ? 'កំពុងផ្ទៀងផ្ទាត់ Telegram...' : 'Verifying Telegram Account...'
          authLoadingSubtitle.value = currentLang.value === 'km' ? 'សូមរង់ចាំមួយភ្លែត ប្រព័ន្ធកំពុងរៀបចំ Dashboard ជូនលោកអ្នក' : 'Please wait a moment while setting up your dashboard...'
          handleTelegramAuthSuccess(data.result)
        }
      }
    } catch (e) {}
  }
}

const redirectToTelegramOAuth = () => {
  if (typeof window === 'undefined') return
  isTelegramLoading.value = true
  stopPopupTracking()

  const url = getTelegramOAuthUrl()
  const width = 550
  const height = 650
  const left = window.screenX + Math.max(0, (window.outerWidth - width) / 2)
  const top = window.screenY + Math.max(0, (window.outerHeight - height) / 2)

  const popup = window.open(
    url,
    'telegram_oauth',
    `width=${width},height=${height},left=${left},top=${top},status=0,toolbar=0,menubar=0,location=1`
  )

  if (!popup || popup.closed || typeof popup.closed === 'undefined') {
    // If popup was blocked by browser, fallback to direct page navigation
    isAuthenticating.value = true
    authLoadingTitle.value = currentLang.value === 'km' ? 'កំពុងតភ្ជាប់ទៅកាន់ Telegram...' : 'Connecting to Telegram...'
    authLoadingSubtitle.value = currentLang.value === 'km' ? 'សូមរង់ចាំមួយភ្លែត ប្រព័ន្ធកំពុងនាំអ្នកទៅកាន់ Telegram Login' : 'Please wait a moment while redirecting to Telegram...'
    setTimeout(() => {
      window.location.assign(url)
    }, 250)
  } else {
    activePopup = popup
    popupCheckTimer = setInterval(checkPopupClosed, 300)
  }
}

const handleTelegramAuthSuccess = async (tgUser: any) => {
  isAuthenticating.value = true
  isTelegramLoading.value = true
  authLoadingTitle.value = currentLang.value === 'km' ? 'កំពុងផ្ទៀងផ្ទាត់ Telegram...' : 'Verifying Telegram Account...'
  authLoadingSubtitle.value = currentLang.value === 'km' ? 'សូមរង់ចាំមួយភ្លែត ប្រព័ន្ធកំពុងរៀបចំ Dashboard ជូនលោកអ្នក' : 'Please wait a moment while setting up your dashboard...'

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

    const data = await response.json()
    if (data.success && data.redirect) {
      setTimeout(() => {
        window.location.href = data.redirect
      }, 600)
    } else {
      setTimeout(() => {
        window.location.href = '/student/dashboard'
      }, 600)
    }
  } catch (err: any) {
    try {
      const form = document.createElement('form')
      form.method = 'POST'
      form.action = '/auth/telegram'
      for (const key in tgUser) {
        if (Object.prototype.hasOwnProperty.call(tgUser, key) && tgUser[key] !== null && tgUser[key] !== undefined) {
          const input = document.createElement('input')
          input.type = 'hidden'
          input.name = key
          input.value = typeof tgUser[key] === 'object' ? JSON.stringify(tgUser[key]) : String(tgUser[key])
          form.appendChild(input)
        }
      }
      document.body.appendChild(form)
      form.submit()
    } catch (_) {
      isAuthenticating.value = false
      isTelegramLoading.value = false
      oauthNotice.value = {
        type: 'error',
        message: currentLang.value === 'km' ? 'មានបញ្ហាក្នុងការតភ្ជាប់ទៅកាន់ម៉ាស៊ីនបម្រើ។ សូមព្យាយាមម្តងទៀត!' : 'Connection error. Please try again!'
      }
    }
  }
}

const handleGoogleAuthSuccess = async (googleUser: any) => {
  isAuthenticating.value = true
  isGoogleLoading.value = true
  authLoadingTitle.value = currentLang.value === 'km' ? 'កំពុងផ្ទៀងផ្ទាត់គណនី Google...' : 'Verifying Google Account...'
  authLoadingSubtitle.value = currentLang.value === 'km' ? 'សូមរង់ចាំមួយភ្លែត ប្រព័ន្ធកំពុងរៀបចំ Dashboard ជូនលោកអ្នក' : 'Please wait a moment while setting up your dashboard...'

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
      statusMessage.value = data.message || (currentLang.value === 'km'
        ? `ស្វាគមន៍ ${googleUser.first_name || googleUser.name || 'Google User'}! ចូលប្រើតាម Google ជោគជ័យ។`
        : `Welcome ${googleUser.first_name || googleUser.name || 'Google User'}! Google login successful.`)
      showSuccessModal.value = true

      setTimeout(() => {
        const targetUrl = data.redirect || '/student/dashboard'
        router.visit(targetUrl)
      }, 1200)
    } else {
      isAuthenticating.value = false
      isGoogleLoading.value = false
      oauthNotice.value = {
        type: 'error',
        message: data?.message || t('google_login_failed', 'ការផ្ទៀងផ្ទាត់ Google មិនត្រឹមត្រូវទេ។ សូមព្យាយាមម្តងទៀត!')
      }
    }
  } catch (err: any) {
    isAuthenticating.value = false
    isGoogleLoading.value = false
    oauthNotice.value = {
      type: 'error',
      message: t('google_login_error', 'មានបញ្ហាក្នុងការតភ្ជាប់ទៅកាន់ម៉ាស៊ីនបម្រើ។ សូមព្យាយាមម្តងទៀត!')
    }
  }
}



const redirectToGoogleOAuth = () => {
  isGoogleLoading.value = true
  isAuthenticating.value = true
  authLoadingTitle.value = currentLang.value === 'km' ? 'កំពុងតភ្ជាប់ទៅកាន់ Google...' : 'Connecting to Google...'
  authLoadingSubtitle.value = currentLang.value === 'km' ? 'សូមរង់ចាំមួយភ្លែត ប្រព័ន្ធកំពុងនាំអ្នកទៅកាន់ផ្ទាំង Google Sign-In' : 'Please wait a moment while redirecting to Google Sign-In...'

  setTimeout(() => {
    window.location.assign('/auth/google/redirect')
  }, 350)
}

const handleSocialLogin = (provider: string) => {
  if (provider === 'Telegram') {
    redirectToTelegramOAuth()
    return
  }

  if (provider === 'Google') {
    redirectToGoogleOAuth()
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

    // Listen for Telegram OAuth PostMessage events (DECLINE or ACCEPT from popup)
    window.addEventListener('message', handleTelegramPostMessage)

    // Reset loading state if user closes popup (X) and returns to main window
    window.addEventListener('focus', checkPopupClosed)
    window.addEventListener('pageshow', () => {
      stopPopupTracking()
      isAuthenticating.value = false
      isGoogleLoading.value = false
      isTelegramLoading.value = false
    })

    // 1. Check for Telegram OAuth URL fragment (#tgAuthResult=...)
    if (window.location.hash && window.location.hash.includes('tgAuthResult=')) {
      try {
        const hashStr = window.location.hash.substring(1)
        window.history.replaceState(null, '', window.location.pathname)
        const params = new URLSearchParams(hashStr)
        const tgAuthResult = params.get('tgAuthResult')
        if (tgAuthResult) {
          let base64 = tgAuthResult.replace(/-/g, '+').replace(/_/g, '/')
          while (base64.length % 4 !== 0) base64 += '='
          const decoded = decodeURIComponent(escape(atob(base64)))
          const tgUser = JSON.parse(decoded)
          if (tgUser && tgUser.id) {
            handleTelegramAuthSuccess(tgUser)
            return
          }
        }
      } catch (e) {
        console.error('Telegram Auth Result Hash parsing error:', e)
      }
    }

    // 2. Check for query parameter errors and direct OAuth query returns
    const urlParams = new URLSearchParams(window.location.search)
    const err = urlParams.get('error')
    const status = urlParams.get('status')
    if (status === 'declined' || err === 'cancelled' || err === 'declined' || err === 'telegram_cancelled') {
      oauthNotice.value = {
        type: 'warning',
        message: currentLang.value === 'km'
          ? 'លោកអ្នកបានបដិសេធការ Login! សូមចុច Accept ដើម្បីចូលប្រើប្រាស់។'
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
    } else if (urlParams.get('id') && urlParams.get('hash')) {
      const tgUser: Record<string, string> = {}
      urlParams.forEach((val, key) => {
        tgUser[key] = val
      })
      try {
        window.history.replaceState({}, document.title, window.location.pathname)
      } catch (_) {}
      handleTelegramAuthSuccess(tgUser)
    }
  }
})

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('keydown', handleKeyCheck)
    window.removeEventListener('keyup', handleKeyCheck)
    document.removeEventListener('click', handleClickOutside)
    window.removeEventListener('message', handleTelegramPostMessage)
    window.removeEventListener('focus', checkPopupClosed)
    stopPopupTracking()
    if ((window as any).onTelegramAuth) {
      delete (window as any).onTelegramAuth
    }
  }
})
</script>

<template>
  <div class="min-h-[100dvh] w-full flex flex-col justify-between bg-slate-100/90 dark:bg-[#070D1E] text-slate-900 dark:text-slate-100 px-3 py-3 sm:px-6 sm:py-5 relative font-sans overflow-x-hidden select-none transition-colors duration-500">
    
    <!-- Top Header (Language & Theme Switchers) - Clean & Integrated into Page Flow -->
    <div class="w-full max-w-md mx-auto flex items-center justify-between z-30 pt-0.5 pb-1 sm:pb-2">
      <!-- Language Switcher Pill -->
      <div class="relative lang-switcher-container">
        <button
          type="button"
          @click.stop="isLangOpen = !isLangOpen"
          class="group px-3 py-1.5 rounded-full bg-white/90 dark:bg-slate-800/80 backdrop-blur-md hover:bg-white dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 transition-all duration-200 border border-slate-300/80 dark:border-slate-700/60 shadow-xs flex items-center gap-1.5 text-xs font-semibold cursor-pointer select-none active:scale-95 focus:outline-none"
          :title="currentLang === 'km' ? 'ប្តូរភាសា / Change Language' : 'Change Language / ប្តូរភាសា'"
        >
          <img
            :src="languages.find(l => l.code === currentLang)?.flagUrl || '/images/flags/km.svg'"
            :alt="currentLang"
            width="16"
            height="16"
            loading="eager"
            decoding="async"
            class="w-3.5 h-3.5 rounded-full object-cover shrink-0 ring-1 ring-slate-300 dark:ring-slate-600"
          />
          <span class="text-[11px] font-bold tracking-wide">
            {{ currentLang === 'km' ? 'KH' : 'EN' }}
          </span>
          <i :class="['pi pi-chevron-down text-[9px] text-slate-500 dark:text-slate-400 transition-transform duration-200', isLangOpen ? 'rotate-180 text-blue-600' : '']"></i>
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
            class="absolute left-0 mt-1.5 w-40 rounded-2xl bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl border border-slate-200/90 dark:border-slate-700/80 shadow-xl shadow-black/10 dark:shadow-black/40 py-1 z-50 overflow-hidden"
          >
            <button
              v-for="lang in languages"
              :key="lang.code"
              type="button"
              @click="selectLanguage(lang.code)"
              :class="[
                'w-full flex items-center justify-between px-3 py-2 text-xs font-semibold transition-colors cursor-pointer select-none',
                currentLang === lang.code
                  ? 'bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400'
                  : 'text-slate-800 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60 hover:text-slate-950 dark:hover:text-white'
              ]"
            >
              <span class="flex items-center gap-2">
                <img :src="lang.flagUrl" :alt="lang.name" width="16" height="16" loading="eager" decoding="async" class="w-3.5 h-3.5 rounded-full object-cover shrink-0" />
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
        class="group px-3 py-1.5 rounded-full bg-white/90 dark:bg-slate-800/80 backdrop-blur-md hover:bg-white dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 transition-all duration-200 border border-slate-300/80 dark:border-slate-700/60 shadow-xs flex items-center gap-1.5 text-xs font-semibold cursor-pointer select-none active:scale-95"
        :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
      >
        <div class="relative w-3.5 h-3.5 flex items-center justify-center">
          <i :class="['pi text-[11px] transition-transform duration-300 group-hover:rotate-45', isDark ? 'pi-sun text-amber-500' : 'pi-moon text-indigo-500']"></i>
        </div>
        <span class="text-[11px] font-bold">{{ isDark ? t('theme_light', 'Light Mode') : t('theme_dark', 'Dark Mode') }}</span>
      </button>
    </div>

    <!-- Interactive 3D Animated Background & Ambient Glow -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0 select-none">
      <AuthAnimatedBackground />
      <div class="absolute -inset-[100%] opacity-35 dark:opacity-25 animate-aurora bg-gradient-to-r from-blue-600/30 via-indigo-500/30 via-purple-500/30 via-sky-400/30 to-blue-600/30 blur-3xl"></div>
      <div class="absolute inset-0 bg-[radial-gradient(#3b82f6_1.2px,transparent_1.2px)] dark:bg-[radial-gradient(#38bdf8_1.2px,transparent_1.2px)] [background-size:32px_32px] opacity-25 dark:opacity-15"></div>
      <div class="absolute -top-24 -left-24 w-72 h-72 bg-blue-500/20 dark:bg-blue-600/25 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-indigo-500/20 dark:bg-indigo-600/25 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Main Login Card Area (Ergonomic & Centered) -->
    <div class="w-full max-w-md mx-auto my-auto z-10 space-y-3">
      
      <!-- Brand & School Logo -->
      <div v-if="!isAuthenticating" class="flex flex-col items-center text-center">
        <div class="relative group mb-1.5">
          <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full blur opacity-30 group-hover:opacity-60 transition duration-300"></div>
          <img
            :src="logoUrl"
            alt="Saint Paul Institute Official Crest Logo"
            width="52"
            height="52"
            fetchpriority="high"
            decoding="async"
            class="relative w-12 h-12 sm:w-13 sm:h-13 rounded-full shadow-md object-contain ring-2 ring-blue-500/30 ring-offset-2 ring-offset-white dark:ring-offset-[#070D1E] bg-white p-0.5"
          />
        </div>
        <span class="px-2.5 py-0.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-700 dark:text-blue-300 text-[11px] font-bold tracking-wide">
          {{ t('login_subtitle', 'វិទ្យាស្ថាន សន្តប៉ូល') }}
        </span>
      </div>

      <!-- Main Glassmorphism Container -->
      <div :class="['w-full bg-white/95 dark:bg-slate-900/70 backdrop-blur-xl border border-slate-200/90 dark:border-slate-800/80 rounded-3xl p-4 sm:p-5.5 shadow-2xl space-y-3 sm:space-y-3.5 transition-all duration-300', isAuthenticating ? 'text-center' : '']">
        
        <!-- Regular Login Flow -->
        <div v-if="!isAuthenticating" class="space-y-3">
          
          <!-- OAuth Notification Banner -->
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
                'rounded-xl p-2.5 text-xs flex items-start justify-between gap-2.5 shadow-xs transition-all border',
                oauthNotice.type === 'warning'
                  ? 'bg-amber-500/15 border-amber-500/30 text-amber-600 dark:text-amber-300'
                  : 'bg-rose-500/15 border-rose-500/30 text-rose-600 dark:text-rose-300'
              ]"
            >
              <div class="flex items-start gap-2">
                <i
                  :class="[
                    'shrink-0 text-xs mt-0.5',
                    oauthNotice.type === 'warning' ? 'pi pi-exclamation-triangle text-amber-500' : 'pi pi-times-circle text-rose-500'
                  ]"
                ></i>
                <span class="font-medium text-[11px] leading-tight">{{ oauthNotice.message }}</span>
              </div>
              <button
                type="button"
                @click="oauthNotice = null"
                class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-0.5 rounded-full transition-colors shrink-0 cursor-pointer"
              >
                <i class="pi pi-times text-[9px]"></i>
              </button>
            </div>
          </Transition>

          <!-- Error Banner -->
          <div v-if="form.errors.email" class="bg-rose-500/10 border border-rose-500/30 rounded-xl p-2 text-rose-600 dark:text-rose-300 text-xs flex items-start gap-2 shadow-xs animate-shake">
            <i class="pi pi-exclamation-circle text-xs text-rose-500 shrink-0 mt-0.5"></i>
            <span class="leading-tight font-medium text-[11px]">{{ form.errors.email }}</span>
          </div>

          <!-- Social Notice Toast -->
          <div v-if="socialNotice" class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-2 text-blue-700 dark:text-blue-300 text-xs flex items-center gap-2">
            <i class="pi pi-info-circle text-blue-500 text-xs shrink-0"></i>
            <span class="font-medium text-[11px]">{{ socialNotice }}</span>
          </div>

          <!-- Caps Lock Alert -->
          <div v-if="capsLockOn" class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-2 text-amber-700 dark:text-amber-300 text-xs flex items-center gap-2">
            <i class="pi pi-exclamation-triangle text-amber-500 text-xs shrink-0"></i>
            <span class="text-[11px] font-semibold">{{ t('login_caps_lock_active', 'Caps Lock is ON') }}</span>
          </div>

          <!-- Role Selector Tabs (Single-Line Compact Mobile-Optimized Grid) -->
          <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 dark:bg-slate-950/60 rounded-2xl border border-slate-200 dark:border-slate-800/60">
            <!-- Student Tab -->
            <button
              type="button"
              @click="form.role = 'student'"
              :class="[
                'py-2 px-1 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-1.5 cursor-pointer select-none active:scale-95',
                form.role === 'student'
                  ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-bold'
                  : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
              ]"
            >
              <span class="text-xs">🎓</span>
              <span class="truncate">{{ t('login_tab_student', 'និស្សិត') }}</span>
            </button>

            <!-- Teacher Tab -->
            <button
              type="button"
              @click="form.role = 'teacher'"
              :class="[
                'py-2 px-1 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-1.5 cursor-pointer select-none active:scale-95',
                form.role === 'teacher'
                  ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-bold'
                  : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
              ]"
            >
              <span class="text-xs">📖</span>
              <span class="truncate">{{ t('login_tab_teacher', 'គ្រូបង្រៀន') }}</span>
            </button>

            <!-- Admin Tab -->
            <button
              type="button"
              @click="form.role = 'admin'"
              :class="[
                'py-2 px-1 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-1.5 cursor-pointer select-none active:scale-95',
                form.role === 'admin'
                  ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-bold'
                  : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
              ]"
            >
              <span class="text-xs">🛡️</span>
              <span class="truncate">{{ t('login_tab_admin', 'រដ្ឋបាល') }}</span>
            </button>
          </div>

          <!-- Main Form -->
          <form @submit.prevent="submit" class="space-y-3 pt-0.5">
            
            <!-- Identity Input -->
            <div>
              <label class="block text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1">
                {{ identityLabel }}
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <input
                  v-model="form.email"
                  type="text"
                  required
                  autocomplete="username"
                  :placeholder="identityPlaceholder"
                  class="w-full h-10.5 pl-10 pr-9 py-2 bg-slate-50 dark:bg-slate-950/50 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                />
                <button
                  v-if="form.email"
                  type="button"
                  @click="clearEmail"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition cursor-pointer"
                  title="Clear"
                >
                  <i class="pi pi-times-circle text-xs"></i>
                </button>
              </div>
            </div>

            <!-- Password Input -->
            <div>
              <label class="block text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1">
                {{ t('login_input_password_label', 'ពាក្យសម្ងាត់') }}
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  autocomplete="current-password"
                  placeholder="••••••••••••"
                  class="w-full h-10.5 pl-10 pr-10 py-2 bg-slate-50 dark:bg-slate-950/50 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition cursor-pointer"
                >
                  <svg v-if="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                  </svg>
                </button>
              </div>
              <span v-if="form.errors.password" class="text-[10px] text-rose-600 dark:text-rose-400 block font-semibold mt-0.5">{{ form.errors.password }}</span>
            </div>

            <!-- Remember & Forgot Password -->
            <div class="flex items-center justify-between text-xs pt-0.5">
              <label class="flex items-center gap-2 text-slate-600 dark:text-slate-400 cursor-pointer select-none">
                <input
                  type="checkbox"
                  v-model="form.remember"
                  class="w-3.5 h-3.5 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-blue-600 focus:ring-0 focus:ring-offset-0"
                />
                <span class="text-[11px] sm:text-xs">{{ t('login_remember_me', 'ចងចាំគណនី') }}</span>
              </label>
              <Link
                href="/forgot-password"
                class="text-[11px] sm:text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-500 transition"
              >
                {{ t('login_forgot_password', 'ភ្លេចពាក្យសម្ងាត់?') }}
              </Link>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="isSubmitting || form.processing"
              class="w-full mt-1.5 h-10.5 sm:h-11 bg-blue-600 hover:bg-blue-500 active:scale-[0.98] disabled:opacity-50 text-white text-xs sm:text-sm font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-600/30 cursor-pointer"
            >
              <i v-if="isSubmitting || form.processing" class="pi pi-spin pi-spinner text-xs shrink-0"></i>
              <template v-else>
                <span>{{ t('login_btn_submit', 'ចូលប្រព័ន្ធ') }}</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
              </template>
            </button>
          </form>

          <!-- Divider -->
          <div class="relative flex items-center justify-center my-2 sm:my-2.5">
            <div class="border-t border-slate-200 dark:border-slate-800 w-full"></div>
            <span class="bg-white dark:bg-[#0d1730] px-3 text-[11px] text-slate-400 dark:text-slate-500 uppercase tracking-widest absolute">
              {{ t('login_or', 'ឬ') }}
            </span>
          </div>

          <!-- Social Quick Logins -->
          <div class="grid grid-cols-2 gap-2">
            <button
              type="button"
              :disabled="isAuthenticating"
              @click="redirectToGoogleOAuth"
              class="h-9.5 flex items-center justify-center gap-2 px-3 bg-slate-50 dark:bg-slate-950/60 hover:bg-slate-100 dark:hover:bg-slate-800/60 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 active:scale-95 transition cursor-pointer select-none disabled:opacity-50"
            >
              <template v-if="isGoogleLoading">
                <i class="pi pi-spin pi-spinner text-rose-500 text-xs"></i>
                <span class="text-[11px]">{{ currentLang === 'km' ? 'កំពុងភ្ជាប់...' : 'Connecting...' }}</span>
              </template>
              <template v-else>
                <i class="pi pi-google text-rose-500 text-xs"></i>
                <span>Google</span>
              </template>
            </button>

            <button
              type="button"
              :disabled="isAuthenticating"
              @click="redirectToTelegramOAuth"
              class="h-9.5 flex items-center justify-center gap-2 px-3 bg-slate-50 dark:bg-slate-950/60 hover:bg-slate-100 dark:hover:bg-slate-800/60 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 active:scale-95 transition cursor-pointer select-none disabled:opacity-50"
            >
              <template v-if="isTelegramLoading">
                <i class="pi pi-spin pi-spinner text-sky-500 text-xs"></i>
                <span class="text-[11px]">{{ currentLang === 'km' ? 'កំពុងភ្ជាប់...' : 'Connecting...' }}</span>
              </template>
              <template v-else>
                <i class="pi pi-telegram text-sky-500 text-xs"></i>
                <span>Telegram</span>
              </template>
            </button>
          </div>

          <!-- Register Callout -->
          <div class="pt-2 border-t border-slate-200 dark:border-slate-800/80 text-center text-xs">
            <p class="text-[11px] text-slate-600 dark:text-slate-400 flex items-center justify-center gap-1.5">
              <span>{{ t('login_dont_have_account', 'មិនទាន់មានគណនី?') }}</span>
              <Link href="/register" class="font-bold text-blue-600 dark:text-blue-400 hover:underline">
                {{ t('login_register_now', 'ចុះឈ្មោះគណនីថ្មី') }}
              </Link>
            </p>
          </div>

        </div>

        <!-- Loading State during Authentication -->
        <div v-else class="w-full flex flex-col items-center justify-center text-center animate-fade-in select-none py-4">
          <div class="relative mb-3">
            <div class="absolute -inset-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-sky-400 rounded-full blur-md opacity-40 animate-pulse"></div>
            <div class="relative w-16 h-16 rounded-full p-1 bg-white dark:bg-slate-800 shadow-md">
              <img :src="logoUrl" alt="E-LMS Logo" class="w-full h-full object-cover rounded-full" />
            </div>
          </div>

          <div class="flex items-center justify-center gap-2 mb-1">
            <i class="pi pi-spin pi-spinner text-xs text-blue-600 dark:text-sky-400"></i>
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">
              {{ authLoadingTitle || (currentLang === 'km' ? 'កំពុងរៀបចំ Dashboard របស់អ្នក...' : 'Setting up your dashboard...') }}
            </h3>
          </div>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mb-4 max-w-xs leading-relaxed">
            {{ authLoadingSubtitle || (currentLang === 'km' ? 'សូមរង់ចាំមួយភ្លែត ប្រព័ន្ធកំពុងដំណើរការផ្ទៀងផ្ទាត់' : 'Please wait a moment while verifying your account') }}
          </p>

          <div class="w-48 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden p-0.5 border border-slate-200/80 dark:border-slate-700">
            <div class="bg-gradient-to-r from-blue-600 via-sky-400 to-teal-400 h-full rounded-full animate-indeterminate"></div>
          </div>
        </div>

      </div>

    </div>

    <!-- Mobile Safe Area Bottom Spacing & Legal Footer -->
    <div class="w-full text-center py-1 sm:py-2 z-10 space-y-1">
      <div class="flex items-center justify-center gap-3 text-[11px] text-slate-400 dark:text-slate-500 select-none">
        <Link href="/privacy" class="hover:text-blue-600 dark:hover:text-sky-400 hover:underline transition-colors">{{ t('privacy_policy', 'គោលការណ៍ឯកជនភាព') }}</Link>
        <span>•</span>
        <Link href="/terms" class="hover:text-blue-600 dark:hover:text-sky-400 hover:underline transition-colors">{{ t('terms_of_service', 'លក្ខខណ្ឌប្រើប្រាស់') }}</Link>
      </div>
      <p class="text-[10.5px] text-slate-400 dark:text-slate-500">© 2026 SPI AI-ELMS • {{ t('login_subtitle', 'វិទ្យាស្ថាន សន្តប៉ូល') }}</p>
    </div>

    <!-- Compact Success Alert Modal -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 scale-90 translate-y-2"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-90 translate-y-2"
    >
      <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/40 backdrop-blur-sm select-none">
        <div class="max-w-xs w-full bg-white dark:bg-[#0E172E] rounded-3xl p-5 shadow-2xl border border-emerald-500/30 text-center flex flex-col items-center space-y-3 transform transition-all">
          <div class="relative flex items-center justify-center">
            <div class="absolute -inset-2 bg-gradient-to-r from-emerald-500/30 to-teal-500/30 rounded-full blur-md animate-pulse"></div>
            <div class="relative w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-lg shadow-emerald-500/25 ring-4 ring-white dark:ring-[#0E172E]">
              <svg class="w-6 h-6 text-white stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" class="checkmark-path" d="M4.5 12.75l6 6 9-13.5"/>
              </svg>
            </div>
          </div>
          
          <div class="space-y-1">
            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">
              {{ statusMessage ? t('login_modal_status_title', 'ជូនដំណឹង') : t('login_modal_success_title', 'ចូលប្រើប្រាស់ជោគជ័យ!') }}
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
              {{ statusMessage || t('login_modal_success_msg', 'កំពុងបញ្ជូនអ្នកទៅកាន់ទំព័រដើម...') }}
            </p>
          </div>

          <button
            v-if="statusMessage"
            type="button"
            @click="showSuccessModal = false"
            class="w-full py-2 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white text-xs font-bold shadow-lg shadow-emerald-600/25 transition-all duration-150 cursor-pointer select-none"
          >
            {{ t('login_modal_btn_login_now', 'ចូលប្រព័ន្ធឥឡូវនេះ (Login)') }}
          </button>
        </div>
      </div>
    </Transition>

    <!-- Compact Error Alert Modal -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 scale-90 translate-y-2"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-90 translate-y-2"
    >
      <div v-if="showErrorModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/40 backdrop-blur-sm select-none">
        <div class="max-w-xs w-full bg-white dark:bg-[#0E172E] rounded-3xl p-5 shadow-2xl border border-rose-500/30 text-center flex flex-col items-center space-y-3 transform transition-all">
          <div class="relative flex items-center justify-center">
            <div class="absolute -inset-2 bg-gradient-to-r from-rose-500/30 to-amber-500/30 rounded-full blur-md animate-pulse"></div>
            <div class="relative w-12 h-12 rounded-full bg-gradient-to-br from-amber-500 via-rose-500 to-rose-600 text-white flex items-center justify-center shadow-lg shadow-rose-500/25 ring-4 ring-white dark:ring-[#0E172E]">
              <span class="text-2xl font-black font-sans leading-none">?</span>
            </div>
          </div>
          
          <div class="space-y-1">
            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">
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

@keyframes indeterminate {
  0% {
    transform: translateX(-100%) scaleX(0.2);
  }
  50% {
    transform: translateX(0%) scaleX(0.6);
  }
  100% {
    transform: translateX(100%) scaleX(0.2);
  }
}

.animate-indeterminate {
  animation: indeterminate 1.2s infinite cubic-bezier(0.65, 0.815, 0.735, 0.395);
  transform-origin: 0% 50%;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.96) translateY(4px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 0.35s ease-out forwards;
}
</style>



