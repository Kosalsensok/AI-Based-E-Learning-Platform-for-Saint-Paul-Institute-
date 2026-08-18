<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useForm, Link, router, usePage } from '@inertiajs/vue3'
import { i18n, type LanguageCode } from '../../Services/i18n'
import AuthAnimatedBackground from '../../Components/AuthAnimatedBackground.vue'

const logoUrl = '/images/logo.png'

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
  { code: 'km' as LanguageCode, name: 'ភាសាខ្មែរ', label: 'ខ្មែរ', short: 'KH', flag: '🇰🇭', flagUrl: '/images/flags/km.svg' },
  { code: 'en' as LanguageCode, name: 'English', label: 'English', short: 'EN', flag: '🇬🇧', flagUrl: '/images/flags/en.svg' },
]

const currentLang = computed(() => i18n.locale.value)

const selectLanguage = (code: LanguageCode) => {
  i18n.setLanguage(code)
  isLangOpen.value = false
}

const t = (key: string, defaultText?: string) => {
  return i18n.t(key, defaultText)
}

const roles = computed(() => [
  { id: 'student', label: t('login_tab_student', 'និស្សិត'), icon: '🎓' },
  { id: 'teacher', label: t('login_tab_teacher', 'សាស្ត្រាចារ្យ'), icon: '👨‍🏫' },
  { id: 'admin', label: t('login_tab_admin', 'រដ្ឋបាល'), icon: '🛡️' }
])

const identityLabel = computed(() => {
  return t(`login_input_identity_label_${form.role}`, form.role === 'teacher' ? 'អត្តលេខ អ៊ីមែល ឬ លេខទូរស័ព្ទ' : form.role === 'admin' ? 'អត្តលេខ អ៊ីមែល ឬ លេខទូរស័ព្ទ' : 'អត្តលេខ អ៊ីមែល ឬ លេខទូរស័ព្ទ')
})

const identityPlaceholder = computed(() => {
  return t(`login_input_identity_placeholder_${form.role}`, form.role === 'teacher' ? 'អត្តលេខ អ៊ីមែល ឬទូរស័ព្ទ' : form.role === 'admin' ? 'អត្តលេខ អ៊ីមែល ឬទូរស័ព្ទ' : 'អត្តលេខ អ៊ីមែល ឬទូរស័ព្ទ')
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
          isAuthenticating.value = false
          isTelegramLoading.value = false
          oauthNotice.value = {
            type: 'warning',
            message: currentLang.value === 'km'
              ? 'លោកអ្នកបានបដិសេធការ Login! សូមចុច Accept ដើម្បីចូលប្រើប្រាស់។'
              : 'Login was cancelled. Please accept Telegram permissions to access your account.'
          }
        } else if (data.result && (data.result.id || typeof data.result === 'object')) {
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

    window.addEventListener('message', handleTelegramPostMessage)
    window.addEventListener('focus', checkPopupClosed)
    window.addEventListener('pageshow', () => {
      stopPopupTracking()
      isAuthenticating.value = false
      isGoogleLoading.value = false
      isTelegramLoading.value = false
    })

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
  <div class="lms-viewport">
    
    <!-- Ambient Background Lighting & ThreeJS Layer -->
    <div class="ambient-layer">
      <AuthAnimatedBackground />
      <div class="glow-orb orb-top"></div>
      <div class="glow-orb orb-bottom"></div>
    </div>

    <!-- Header Tools (Safe Area Compliant) -->
    <header class="lms-header">
      <!-- Language Switcher Dropdown -->
      <div class="relative lang-switcher-container">
        <button 
          type="button" 
          @click.stop="isLangOpen = !isLangOpen"
          class="action-chip" 
          aria-label="Language Selector"
        >
          <span class="chip-flag">{{ currentLang === 'km' ? '🇰🇭' : '🇬🇧' }}</span>
          <span class="chip-text">{{ currentLang === 'km' ? 'KH' : 'EN' }}</span>
          <svg :class="['chip-arrow transition-transform duration-200', isLangOpen ? 'rotate-180 text-blue-400' : '']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <!-- Dropdown Menu -->
        <Transition
          enter-active-class="transition duration-150 ease-out"
          enter-from-class="transform opacity-0 scale-95 -translate-y-1"
          enter-to-class="transform opacity-100 scale-100 translate-y-0"
          leave-active-class="transition duration-100 ease-in"
          leave-from-class="transform opacity-100 scale-100 translate-y-0"
          leave-to-class="transform opacity-0 scale-95 -translate-y-1"
        >
          <div
            v-if="isLangOpen"
            class="lang-dropdown"
          >
            <button
              v-for="lang in languages"
              :key="lang.code"
              type="button"
              @click="selectLanguage(lang.code)"
              :class="[
                'lang-dropdown-item',
                currentLang === lang.code ? 'lang-dropdown-item-active' : ''
              ]"
            >
              <span class="flex items-center gap-2">
                <span>{{ lang.flag }}</span>
                <span>{{ lang.name }}</span>
              </span>
              <span v-if="currentLang === lang.code" class="text-blue-400 font-bold">✓</span>
            </button>
          </div>
        </Transition>
      </div>

      <!-- Theme Switcher -->
      <button 
        type="button" 
        @click="toggleTheme"
        :class="['action-chip', isDark ? 'text-amber-400' : 'text-indigo-400']" 
        aria-label="Theme Selector"
      >
        <svg v-if="isDark" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
        <span class="chip-text">{{ isDark ? t('theme_light', 'ពន្លឺ') : t('theme_dark', 'ងងឹត') }}</span>
      </button>
    </header>

    <!-- Main Container Card -->
    <main class="lms-main">
      
      <!-- Brand & Institute Header -->
      <div v-if="!isAuthenticating" class="branding-group">
        <div class="logo-badge">
          <div class="logo-box">
            <img :src="logoUrl" alt="Saint Paul Institute Logo" class="w-10 h-10 object-contain rounded-full bg-white p-0.5" />
          </div>
        </div>
        <div class="institute-badge">
          {{ t('login_subtitle', 'វិទ្យាស្ថាន សន្តប៉ូល') }}
        </div>
      </div>

      <!-- Auth Glassmorphic Card -->
      <div class="auth-card">
        
        <!-- Standard Login Flow -->
        <div v-if="!isAuthenticating">
          
          <!-- OAuth Notification Banner -->
          <div
            v-if="oauthNotice"
            :class="[
              'oauth-notice-banner',
              oauthNotice.type === 'warning' ? 'oauth-notice-warning' : 'oauth-notice-error'
            ]"
          >
            <div class="flex items-center gap-2">
              <span class="text-xs">{{ oauthNotice.type === 'warning' ? '⚠️' : '❌' }}</span>
              <span>{{ oauthNotice.message }}</span>
            </div>
            <button type="button" @click="oauthNotice = null" class="opacity-70 hover:opacity-100 p-0.5 cursor-pointer">✕</button>
          </div>

          <!-- Error Alert Banner -->
          <div v-if="form.errors.email" class="form-error-banner">
            <span>⚠️</span>
            <span>{{ form.errors.email }}</span>
          </div>

          <!-- Caps Lock Alert -->
          <div v-if="capsLockOn" class="caps-lock-banner">
            <span>🔒</span>
            <span>{{ t('login_caps_lock_active', 'Caps Lock is ON') }}</span>
          </div>

          <!-- Multi-Role Switcher Tabs -->
          <div class="role-switcher" role="tablist">
            <button 
              v-for="role in roles" 
              :key="role.id"
              type="button"
              role="tab"
              :aria-selected="form.role === role.id"
              @click="form.role = role.id"
              :class="['role-btn', { 'role-btn-active': form.role === role.id }]"
            >
              <span class="role-icon">{{ role.icon }}</span>
              <span class="role-title">{{ role.label }}</span>
            </button>
          </div>

          <!-- Inputs Form -->
          <form @submit.prevent="submit" class="auth-form" autocomplete="on">
            
            <!-- Identity Input -->
            <div class="form-field">
              <label for="identity-input" class="input-label">{{ identityLabel }}</label>
              <div class="input-container">
                <span class="input-prefix-icon">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </span>
                <input 
                  id="identity-input"
                  v-model="form.email"
                  type="text" 
                  autocomplete="username"
                  :placeholder="identityPlaceholder" 
                  class="text-input"
                  required
                />
                <button 
                  v-if="form.email"
                  type="button" 
                  @click="clearEmail"
                  class="input-suffix-btn"
                  title="Clear"
                >
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Password Input -->
            <div class="form-field">
              <div class="flex items-center justify-between">
                <label for="password-input" class="input-label">{{ t('login_input_password_label', 'ពាក្យសម្ងាត់') }}</label>
                <Link href="/forgot-password" class="forgot-pwd-link">{{ t('login_forgot_password', 'ភ្លេចពាក្យសម្ងាត់?') }}</Link>
              </div>
              <div class="input-container">
                <span class="input-prefix-icon">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </span>
                <input 
                  id="password-input"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  autocomplete="current-password"
                  placeholder="••••••••••••" 
                  class="text-input has-suffix"
                  required
                />
                <button 
                  type="button" 
                  @click="showPassword = !showPassword"
                  class="input-suffix-btn"
                  aria-label="Toggle Password Visibility"
                >
                  <svg v-if="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg v-else class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                  </svg>
                </button>
              </div>
              <span v-if="form.errors.password" class="text-[10px] text-rose-400 block font-semibold mt-0.5">{{ form.errors.password }}</span>
            </div>

            <!-- Remember Me Checkbox -->
            <div class="flex items-center gap-2">
              <input 
                id="remember-device"
                type="checkbox" 
                v-model="form.remember" 
                class="checkbox-custom"
              />
              <label for="remember-device" class="text-xs text-slate-400 cursor-pointer select-none">
                {{ t('login_remember_me', 'ចងចាំការចូលប្រើលើឧបករណ៍នេះ') }}
              </label>
            </div>

            <!-- Submit Button -->
            <button 
              type="submit" 
              :disabled="isSubmitting || form.processing"
              class="submit-button"
            >
              <span>{{ isSubmitting || form.processing ? t('login_btn_submitting', 'កំពុងផ្ទៀងផ្ទាត់...') : t('login_btn_submit', 'ចូលប្រព័ន្ធ') }}</span>
              <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </button>
          </form>

          <!-- Divider Line -->
          <div class="or-separator">
            <span>{{ t('login_or', 'ឬ') }}</span>
          </div>

          <!-- Social SSO Authentication -->
          <div class="sso-grid">
            <button 
              type="button" 
              :disabled="isAuthenticating"
              @click="redirectToGoogleOAuth"
              class="sso-provider-btn"
            >
              <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.3 9 5 12 5z"/>
                <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/>
                <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3s.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 12.3 0 15.1s.7 5.4 1.9 7.8l3.7-3.1z"/>
                <path fill="#34A853" d="M12 23.5c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.3-6.4-5.2L1.9 16.7C3.7 20.4 7.5 23.5 12 23.5z"/>
              </svg>
              <span>Google</span>
            </button>
            
            <button 
              type="button" 
              :disabled="isAuthenticating"
              @click="redirectToTelegramOAuth"
              class="sso-provider-btn"
            >
              <svg class="w-4 h-4 text-sky-400 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.52 2.77-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
              </svg>
              <span>Telegram</span>
            </button>
          </div>

          <!-- Register Callout -->
          <div class="register-callout">
            <p class="text-xs text-slate-400 flex items-center justify-center gap-1.5">
              <span>{{ t('login_dont_have_account', 'មិនទាន់មានគណនី?') }}</span>
              <Link href="/register" class="font-bold text-blue-400 hover:underline">
                {{ t('login_register_now', 'ចុះឈ្មោះគណនីថ្មី') }}
              </Link>
            </p>
          </div>

        </div>

        <!-- Authenticating / Loading State -->
        <div v-else class="auth-loading-state">
          <div class="logo-box mx-auto mb-3">
            <img :src="logoUrl" alt="E-LMS Logo" class="w-10 h-10 object-contain rounded-full bg-white p-0.5" />
          </div>
          <h3 class="text-sm font-bold text-slate-100 mb-1">
            {{ authLoadingTitle || (currentLang === 'km' ? 'កំពុងរៀបចំ Dashboard របស់អ្នក...' : 'Setting up your dashboard...') }}
          </h3>
          <p class="text-xs text-slate-400 font-medium mb-4 max-w-xs leading-relaxed">
            {{ authLoadingSubtitle || (currentLang === 'km' ? 'សូមរង់ចាំមួយភ្លែត ប្រព័ន្ធកំពុងដំណើរការផ្ទៀងផ្ទាត់' : 'Please wait a moment while verifying your account') }}
          </p>
          <div class="loading-progress-track">
            <div class="loading-progress-bar"></div>
          </div>
        </div>

      </div>
    </main>

    <!-- Footer Copyright Note & Links -->
    <footer class="lms-footer">
      <div class="footer-links">
        <Link href="/privacy" class="hover:text-blue-400 hover:underline">{{ t('privacy_policy', 'គោលការណ៍ឯកជនភាព') }}</Link>
        <span>•</span>
        <Link href="/terms" class="hover:text-blue-400 hover:underline">{{ t('terms_of_service', 'លក្ខខណ្ឌប្រើប្រាស់') }}</Link>
      </div>
      <p class="mt-1">© 2026 E-LMS • {{ t('login_subtitle', 'វិទ្យាស្ថាន សន្តប៉ូល') }}</p>
    </footer>

    <!-- Compact Success Alert Modal -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 scale-90"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-90"
    >
      <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm select-none">
        <div class="max-w-xs w-full bg-[#0E172E] rounded-3xl p-5 shadow-2xl border border-emerald-500/30 text-center flex flex-col items-center space-y-3">
          <div class="w-12 h-12 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center ring-2 ring-emerald-500/40 text-xl font-bold">
            ✓
          </div>
          <div class="space-y-1">
            <h3 class="text-sm font-extrabold text-white">
              {{ statusMessage ? t('login_modal_status_title', 'ជូនដំណឹង') : t('login_modal_success_title', 'ចូលប្រើប្រាស់ជោគជ័យ!') }}
            </h3>
            <p class="text-xs text-slate-300 font-medium leading-relaxed">
              {{ statusMessage || t('login_modal_success_msg', 'កំពុងបញ្ជូនអ្នកទៅកាន់ទំព័រដើម...') }}
            </p>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Compact Error Alert Modal -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 scale-90"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-90"
    >
      <div v-if="showErrorModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm select-none">
        <div class="max-w-xs w-full bg-[#0E172E] rounded-3xl p-5 shadow-2xl border border-rose-500/30 text-center flex flex-col items-center space-y-3">
          <div class="w-12 h-12 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center ring-2 ring-rose-500/40 text-xl font-bold">
            !
          </div>
          <div class="space-y-1">
            <h3 class="text-sm font-extrabold text-white">
              {{ t('login_modal_error_title', 'ព័ត៌មានមិនត្រឹមត្រូវ') }}
            </h3>
            <p class="text-xs text-slate-300 font-medium leading-relaxed">
              {{ errorMessage || form.errors.email || t('login_modal_error_msg', 'សូមពិនិត្យមើលអ៊ីមែល ឬពាក្យសម្ងាត់របស់អ្នកឡើងវិញ!') }}
            </p>
          </div>
          <button
            type="button"
            @click="showErrorModal = false"
            class="w-full py-2 px-4 rounded-xl bg-rose-950/60 hover:bg-rose-900 border border-rose-800/50 text-rose-200 text-xs font-bold transition cursor-pointer"
          >
            {{ t('login_modal_close', 'យល់ព្រម') }}
          </button>
        </div>
      </div>
    </Transition>

  </div>
</template>

<style scoped>
/* Base Universal Viewport Architecture */
.lms-viewport {
  min-height: 100vh;
  min-height: 100dvh;
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  background-color: #060b18;
  background-image: radial-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px);
  background-size: 24px 24px;
  color: #f8fafc;
  box-sizing: border-box;
  position: relative;
  overflow-x: hidden;
  padding: calc(env(safe-area-inset-top, 16px) + 8px) calc(env(safe-area-inset-right, 16px) + 8px) calc(env(safe-area-inset-bottom, 16px) + 8px) calc(env(safe-area-inset-left, 16px) + 8px);
}

/* Ambient Backdrops */
.ambient-layer {
  position: absolute;
  inset: 0;
  overflow: hidden;
  pointer-events: none;
  z-index: 0;
}
.glow-orb {
  position: absolute;
  width: 20rem;
  height: 20rem;
  border-radius: 9999px;
  filter: blur(80px);
  pointer-events: none;
  opacity: 0.16;
}
.orb-top { top: -5rem; left: 50%; transform: translateX(-50%); background-color: #2563eb; }
.orb-bottom { bottom: -5rem; right: -3rem; background-color: #4f46e5; }

/* Top Header Bar */
.lms-header {
  width: 100%;
  max-width: 410px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  z-index: 30;
}
.action-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.35rem 0.75rem;
  border-radius: 9999px;
  background-color: rgba(15, 23, 42, 0.75);
  border: 1px solid rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  cursor: pointer;
  touch-action: manipulation;
  transition: transform 0.15s ease;
}
.action-chip:active { transform: scale(0.96); }
.chip-flag { font-size: 0.8125rem; }
.chip-text { font-size: 0.75rem; font-weight: 600; }
.chip-arrow { width: 0.75rem; height: 0.75rem; opacity: 0.7; }

/* Language Dropdown */
.lang-dropdown {
  position: absolute;
  left: 0;
  margin-top: 0.375rem;
  width: 10rem;
  border-radius: 1rem;
  background-color: rgba(15, 23, 42, 0.95);
  border: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
  padding: 0.25rem 0;
  z-index: 50;
  overflow: hidden;
}
.lang-dropdown-item {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.5rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #cbd5e1;
  background: transparent;
  border: none;
  cursor: pointer;
  transition: background-color 0.15s;
}
.lang-dropdown-item:hover { background-color: rgba(255, 255, 255, 0.08); color: #ffffff; }
.lang-dropdown-item-active { background-color: rgba(37, 99, 235, 0.2); color: #60a5fa; }

/* Main Wrapper */
.lms-main {
  width: 100%;
  max-width: 410px;
  margin: auto;
  z-index: 20;
  padding: 0.5rem 0;
}

/* Branding */
.branding-group {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  margin-bottom: 0.875rem;
}
.logo-badge {
  padding: 2px;
  border-radius: 1.25rem;
  background: linear-gradient(135deg, #2563eb, #6366f1);
  box-shadow: 0 10px 24px -4px rgba(37, 99, 235, 0.3);
  margin-bottom: 0.5rem;
}
.logo-box {
  width: 3.5rem;
  height: 3.5rem;
  background-color: #0b1329;
  border-radius: 1.15rem;
  display: flex;
  align-items: center;
  justify-content: center;
}
.institute-badge {
  padding: 0.25rem 0.875rem;
  border-radius: 9999px;
  background-color: rgba(37, 99, 235, 0.12);
  border: 1px solid rgba(37, 99, 235, 0.25);
  color: #93c5fd;
  font-size: 0.75rem;
  font-weight: 600;
}

/* Glassmorphism Card */
.auth-card {
  background: linear-gradient(165deg, rgba(15, 23, 42, 0.75) 0%, rgba(10, 16, 31, 0.85) 100%);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 1.5rem;
  padding: 1.25rem;
  box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
}
@media (min-width: 640px) {
  .auth-card { padding: 1.625rem; }
}

/* Notice & Alerts */
.oauth-notice-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.5rem 0.75rem;
  border-radius: 0.75rem;
  font-size: 0.75rem;
  margin-bottom: 0.75rem;
}
.oauth-notice-warning { background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #fcd34d; }
.oauth-notice-error { background-color: rgba(244, 63, 94, 0.15); border: 1px solid rgba(244, 63, 94, 0.3); color: #fda4af; }
.form-error-banner {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  border-radius: 0.75rem;
  background-color: rgba(244, 63, 94, 0.15);
  border: 1px solid rgba(244, 63, 94, 0.3);
  color: #fda4af;
  font-size: 0.75rem;
  margin-bottom: 0.75rem;
}
.caps-lock-banner {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.35rem 0.75rem;
  border-radius: 0.75rem;
  background-color: rgba(245, 158, 11, 0.15);
  border: 1px solid rgba(245, 158, 11, 0.3);
  color: #fcd34d;
  font-size: 0.6875rem;
  margin-bottom: 0.75rem;
}

/* Role Selector Tabs */
.role-switcher {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.25rem;
  background-color: rgba(2, 6, 23, 0.7);
  padding: 0.25rem;
  border-radius: 0.875rem;
  border: 1px solid rgba(255, 255, 255, 0.05);
  margin-bottom: 1rem;
}
.role-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.25rem;
  padding: 0.5rem 0.125rem;
  border-radius: 0.625rem;
  border: none;
  background: transparent;
  color: #64748b;
  cursor: pointer;
  touch-action: manipulation;
  transition: all 0.2s ease;
}
.role-btn-active {
  background: linear-gradient(135deg, #2563eb, #3b82f6);
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
}
.role-icon { font-size: 0.875rem; }
.role-title { font-size: 0.75rem; font-weight: 600; white-space: nowrap; }

/* Form Fields */
.auth-form { display: flex; flex-direction: column; gap: 0.875rem; }
.form-field { display: flex; flex-direction: column; gap: 0.3125rem; }
.input-label { font-size: 0.75rem; font-weight: 600; color: #cbd5e1; }
.forgot-pwd-link { font-size: 0.6875rem; color: #60a5fa; text-decoration: none; }
.forgot-pwd-link:hover { text-decoration: underline; }

.input-container { position: relative; display: flex; align-items: center; width: 100%; }
.input-prefix-icon {
  position: absolute;
  left: 0.875rem;
  color: #64748b;
  pointer-events: none;
  display: flex;
  align-items: center;
}
.text-input {
  width: 100%;
  padding: 0.625rem 0.875rem 0.625rem 2.375rem;
  background-color: rgba(2, 6, 23, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 0.75rem;
  font-size: 0.875rem;
  color: #f8fafc;
  outline: none;
  box-sizing: border-box;
  -webkit-appearance: none;
  transition: all 0.2s ease;
}
.text-input:focus {
  border-color: #3b82f6;
  background-color: rgba(2, 6, 23, 0.85);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}
.text-input.has-suffix { padding-right: 2.375rem; }
.input-suffix-btn {
  position: absolute;
  right: 0.875rem;
  background: transparent;
  border: none;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
}

/* Checkbox */
.checkbox-custom {
  width: 0.95rem;
  height: 0.95rem;
  accent-color: #2563eb;
  cursor: pointer;
}

/* Primary Button */
.submit-button {
  margin-top: 0.25rem;
  width: 100%;
  padding: 0.6875rem;
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  color: #ffffff;
  font-size: 0.875rem;
  font-weight: 600;
  border: none;
  border-radius: 0.75rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.4);
  touch-action: manipulation;
  transition: all 0.2s ease;
}
.submit-button:hover { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.submit-button:active { transform: scale(0.98); }
.submit-button:disabled { opacity: 0.5; cursor: not-allowed; }

/* Separator */
.or-separator {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0.875rem 0;
}
.or-separator::before {
  content: '';
  position: absolute;
  width: 100%;
  border-top: 1px solid rgba(255, 255, 255, 0.07);
}
.or-separator span {
  position: relative;
  background-color: #0c1428;
  padding: 0 0.625rem;
  font-size: 0.6875rem;
  color: #64748b;
  text-transform: uppercase;
}

/* SSO Providers */
.sso-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.625rem;
}
.sso-provider-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  padding: 0.5625rem;
  background-color: rgba(2, 6, 23, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.07);
  border-radius: 0.75rem;
  color: #e2e8f0;
  font-size: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  touch-action: manipulation;
  transition: all 0.15s ease;
}
.sso-provider-btn:hover { background-color: rgba(255, 255, 255, 0.05); }
.sso-provider-btn:active { transform: scale(0.97); }

/* Register Callout */
.register-callout {
  margin-top: 0.875rem;
  padding-top: 0.75rem;
  border-top: 1px solid rgba(255, 255, 255, 0.07);
  text-align: center;
}

/* Loading State */
.auth-loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 1rem 0;
}
.loading-progress-track {
  width: 12rem;
  background-color: rgba(2, 6, 23, 0.7);
  border-radius: 9999px;
  height: 0.375rem;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.1);
}
.loading-progress-bar {
  background: linear-gradient(90deg, #2563eb, #38bdf8, #2dd4bf);
  height: 100%;
  border-radius: 9999px;
  animation: indeterminate 1.2s infinite cubic-bezier(0.65, 0.815, 0.735, 0.395);
  transform-origin: 0% 50%;
}

@keyframes indeterminate {
  0% { transform: translateX(-100%) scaleX(0.2); }
  50% { transform: translateX(0%) scaleX(0.6); }
  100% { transform: translateX(100%) scaleX(0.2); }
}

/* Footer */
.lms-footer {
  text-align: center;
  font-size: 0.6875rem;
  color: #64748b;
  padding-top: 0.5rem;
  z-index: 20;
}
.footer-links {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
}
</style>
