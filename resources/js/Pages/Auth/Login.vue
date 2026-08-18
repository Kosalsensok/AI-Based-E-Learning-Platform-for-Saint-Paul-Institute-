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
  if (typeof window !== 'undefined') {
    window.removeEventListener('message', handleTelegramPostMessage)
  }
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

const clerkPublishableKey = computed(() => {
  return (page.props as any).clerk?.publishable_key || 'pk_test_Y2xlcmsuYXBwXzNIdXFzcnd5VUlCWUR2OTBhS2dPaXdmc0treC5sY2xzdGFnZS5kZXYk'
})

const isTelegramLoggingIn = ref(false)

const handleTelegramPostMessage = (event: MessageEvent) => {
  const origin = event.origin || ''
  const isAllowedOrigin = origin.includes('telegram.org') || origin.includes('spilms.tech') || (typeof window !== 'undefined' && origin === window.location.origin)
  if (!isAllowedOrigin) return

  if (event.data) {
    try {
      const data = typeof event.data === 'string' ? JSON.parse(event.data) : event.data
      if (data.event === 'auth_result') {
        if (data.result === false) {
          // User clicked DECLINE on Telegram popup
          isTelegramLoggingIn.value = false
          oauthNotice.value = {
            type: 'warning',
            message: currentLang.value === 'km'
              ? 'លោកអ្នកបានបដិសេធការ Login! សូមចុច Accept ដើម្បីចូលប្រើប្រាស់។'
              : 'Login was cancelled. Please accept Telegram permissions to access your account.'
          }
        } else if (data.result && (data.result.id || typeof data.result === 'object')) {
          // User clicked ACCEPT on Telegram popup
          isTelegramLoggingIn.value = true
          handleTelegramAuthSuccess(data.result)
        }
      }
    } catch (e) {}
  }
}

const redirectToTelegramOAuth = () => {
  if (typeof window === 'undefined') return
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
    window.location.assign(url)
  }
}

const handleTelegramAuthSuccess = async (tgUser: any) => {
  isTelegramLoggingIn.value = true
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
      isTelegramLoggingIn.value = false
      oauthNotice.value = {
        type: 'error',
        message: currentLang.value === 'km' ? 'មានបញ្ហាក្នុងការតភ្ជាប់ទៅកាន់ម៉ាស៊ីនបម្រើ។ សូមព្យាយាមម្តងទៀត!' : 'Connection error. Please try again!'
      }
    }
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

const redirectToGoogleOAuth = () => {
  window.location.assign('/auth/google/redirect')
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
    // Listen for Telegram OAuth PostMessage events (DECLINE or ACCEPT from popup)
    window.addEventListener('message', handleTelegramPostMessage)

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
            width="16"
            height="16"
            loading="eager"
            decoding="async"
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
                <img :src="lang.flagUrl" :alt="lang.name" width="16" height="16" loading="eager" decoding="async" class="w-4 h-4 rounded-full object-cover shrink-0 shadow-xs" />
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
    <div :class="['w-full p-[1px] rounded-3xl bg-gradient-to-b from-blue-500/40 via-indigo-500/20 to-purple-500/30 dark:from-blue-500/30 dark:via-slate-800/40 dark:to-indigo-500/20 shadow-2xl shadow-slate-900/10 dark:shadow-black/60 relative z-10 my-auto transition-all duration-300', isTelegramLoggingIn ? 'max-w-sm' : 'max-w-md']">
      <div :class="['w-full bg-white/95 dark:bg-[#0E172E]/95 backdrop-blur-2xl rounded-[23px] flex flex-col justify-center relative z-20 transition-all duration-300 font-[\'Kantumruy_Pro\',sans-serif]', isTelegramLoggingIn ? 'p-8 items-center text-center' : 'p-6 sm:p-8 space-y-4']">
        
        <!-- Header with Logo & Brand Name -->
        <div v-if="!isTelegramLoggingIn" class="w-full space-y-4">
          <div class="text-center pb-1 relative">
            <div class="flex flex-col items-center justify-center gap-1.5">
              <div class="relative group">
                <div class="absolute -inset-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full blur opacity-35 group-hover:opacity-65 transition duration-300"></div>
                <img
                  :src="logoUrl"
                  alt="Saint Paul Institute Official Crest Logo"
                  width="56"
                  height="56"
                  fetchpriority="high"
                  decoding="async"
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

            <!-- OAuth Decline / Warning Notice Banner -->
            <div
              v-if="oauthNotice"
              :class="[
                'p-3 rounded-xl border flex items-center justify-between gap-2.5 text-xs font-semibold shadow-xs animate-in fade-in slide-in-from-top-2 duration-200',
                oauthNotice.type === 'warning'
                  ? 'bg-amber-500/10 border-amber-500/30 text-amber-600 dark:text-amber-300'
                  : 'bg-rose-500/10 border-rose-500/30 text-rose-600 dark:text-rose-300'
              ]"
            >
              <div class="flex items-center gap-2">
                <i :class="oauthNotice.type === 'warning' ? 'pi pi-exclamation-triangle text-amber-500 text-sm shrink-0' : 'pi pi-times-circle text-rose-500 text-sm shrink-0'"></i>
                <span>{{ oauthNotice.message }}</span>
              </div>
              <button type="button" @click="oauthNotice = null" class="opacity-70 hover:opacity-100 cursor-pointer p-0.5">
                <i class="pi pi-times text-xs"></i>
              </button>
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
                <a
                  href="/auth/google/redirect"
                  @click="redirectToGoogleOAuth"
                  class="h-10.5 py-2 px-3 bg-white dark:bg-slate-800/80 hover:bg-slate-50/90 dark:hover:bg-slate-700/60 border border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-500 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:text-slate-950 dark:hover:text-white transition-all duration-150 flex items-center justify-center gap-2 hover:shadow-xs active:scale-98 shadow-2xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-slate-300 dark:focus:ring-slate-600 select-none"
                >
                  <i class="pi pi-google text-rose-500 text-sm"></i> <span>Google</span>
                </a>
                <button
                  type="button"
                  @click="handleSocialLogin('Telegram')"
                  class="h-10.5 py-2 px-3 bg-white dark:bg-slate-800/80 hover:bg-slate-50/90 dark:hover:bg-slate-700/60 border border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-500 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:text-slate-950 dark:hover:text-white transition-all duration-150 flex items-center justify-center gap-2 hover:shadow-xs active:scale-98 shadow-2xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-slate-300 dark:focus:ring-slate-600 select-none"
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

            <!-- Privacy & Terms Legal Footer -->
            <div class="mt-2.5 pt-2 flex items-center justify-center gap-3 text-[11px] text-slate-500 dark:text-slate-400 select-none">
              <Link href="/privacy" class="hover:text-blue-600 dark:hover:text-sky-400 hover:underline transition-colors">{{ t('privacy_policy', 'Privacy Policy') }}</Link>
              <span>•</span>
              <Link href="/terms" class="hover:text-blue-600 dark:hover:text-sky-400 hover:underline transition-colors">{{ t('terms_of_service', 'Terms of Service') }}</Link>
            </div>
          </div>
        </div>

        <!-- ២. ផ្ទាំង LOADING (បង្ហាញតែពេលកំពុង Authenticate ជំនួស Form ខាងលើ) -->
        <div v-else class="w-full flex flex-col items-center justify-center text-center animate-fade-in select-none">
          
          <!-- Logo E-LMS with Soft Glow -->
          <div class="relative mb-5">
            <div class="absolute -inset-1.5 bg-gradient-to-r from-blue-500 to-sky-400 rounded-full blur-md opacity-40 animate-pulse"></div>
            <div class="relative w-18 h-18 rounded-full p-1 bg-white dark:bg-slate-800 shadow-md">
              <img 
                :src="logoUrl" 
                alt="E-LMS Logo" 
                class="w-full h-full object-cover rounded-full"
              />
            </div>
          </div>

          <!-- ចំណងជើងច្បាស់ៗ -->
          <h3 class="text-lg font-bold text-slate-800 dark:text-white tracking-wide mb-1.5">
            កំពុងរៀបចំ Dashboard របស់អ្នក...
          </h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mb-6">
            សូមរង់ចាំមួយភ្លែត ប្រព័ន្ធកំពុងផ្ទៀងផ្ទាត់គណនី
          </p>

          <!-- Modern Loading Progress Bar -->
          <div class="w-48 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden p-0.5 border border-slate-200/80 dark:border-slate-700">
            <div class="bg-gradient-to-r from-blue-600 via-sky-400 to-teal-400 h-full rounded-full animate-indeterminate"></div>
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
              <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                {{ statusMessage || t('login_modal_success_msg', 'កំពុងបញ្ជូនអ្នកទៅកាន់ទំព័រដើម...') }}
              </p>
            </div>

            <!-- Action Button: Login Now or Got It -->
            <button
              v-if="statusMessage"
              type="button"
              @click="showSuccessModal = false"
              class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white text-xs sm:text-sm font-bold shadow-lg shadow-emerald-600/25 transition-all duration-150 cursor-pointer select-none"
            >
              {{ t('login_modal_btn_login_now', 'ចូលប្រព័ន្ធឥឡូវនេះ (Login)') }}
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



