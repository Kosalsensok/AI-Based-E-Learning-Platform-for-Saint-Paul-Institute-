<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useForm, Head, Link, router, usePage } from '@inertiajs/vue3'
import { i18n, type LanguageCode } from '../../Services/i18n'
import AuthAnimatedBackground from '../../Components/AuthAnimatedBackground.vue'

const logoUrl = '/images/logo.png'

const props = defineProps<{
  status?: string
}>()

const page = usePage()

const selectedRole = ref<'student' | 'teacher' | 'admin'>('student')

const form = useForm({
  role: 'student' as 'student' | 'teacher' | 'admin',
  email: '',
  password: '',
  remember: false,
})

const selectRole = (role: 'student' | 'teacher' | 'admin') => {
  selectedRole.value = role
  form.role = role
}

const showPassword = ref(false)
const capsLockOn = ref(false)
const socialNotice = ref<string | null>(null)
const isDark = ref(true)
const isLangOpen = ref(false)
const showSuccessModal = ref(false)
const showErrorModal = ref(false)
const statusMessage = ref<string | null>(null)

// Email OTP State
const authMode = ref<'password' | 'otp'>('password')
const otpStep = ref<1 | 2>(1)
const otpEmail = ref('')
const otpCode = ref('')
const isOtpSending = ref(false)
const isOtpVerifying = ref(false)
const otpCountdown = ref(0)
let otpCountdownTimer: any = null

const formattedOtpTime = computed(() => {
  const mins = Math.floor(otpCountdown.value / 60).toString().padStart(2, '0')
  const secs = (otpCountdown.value % 60).toString().padStart(2, '0')
  return `${mins}:${secs}`
})

const startOtpTimer = (seconds = 300) => {
  otpCountdown.value = seconds
  if (otpCountdownTimer) clearInterval(otpCountdownTimer)
  otpCountdownTimer = setInterval(() => {
    if (otpCountdown.value > 0) {
      otpCountdown.value--
    } else {
      clearInterval(otpCountdownTimer)
    }
  }, 1000)
}

const sendEmailOtp = async () => {
  if (!otpEmail.value || isOtpSending.value) return
  isOtpSending.value = true
  oauthNotice.value = null

  try {
    const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
    const response = await fetch('/auth/email-otp/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({ email: otpEmail.value }),
    })

    const data = await response.json()
    if (response.ok && data.success) {
      otpStep.value = 2
      startOtpTimer(300)
      oauthNotice.value = {
        type: 'warning',
        message: data.message || (currentLang.value === 'km' ? 'លេខកូដ OTP ត្រូវបានផ្ញើចូលប្រអប់សំបុត្រ Gmail របស់អ្នកហើយ!' : 'OTP code has been sent to your email!')
      }
    } else {
      oauthNotice.value = {
        type: 'error',
        message: data.message || (currentLang.value === 'km' ? 'មិនអាចផ្ញើលេខកូដបានទេ!' : 'Failed to send OTP code!')
      }
    }
  } catch (err: any) {
    oauthNotice.value = {
      type: 'error',
      message: currentLang.value === 'km' ? 'មានបញ្ហាក្នុងការតភ្ជាប់ទៅកាន់ម៉ាស៊ីនបម្រើ' : 'Connection error'
    }
  } finally {
    isOtpSending.value = false
  }
}

const verifyEmailOtp = async () => {
  if (!otpCode.value || otpCode.value.length < 6 || isOtpVerifying.value) return
  isOtpVerifying.value = true
  isAuthenticating.value = true
  authLoadingTitle.value = currentLang.value === 'km' ? 'កំពុងផ្ទៀងផ្ទាត់ OTP...' : 'Verifying OTP...'
  authLoadingSubtitle.value = currentLang.value === 'km' ? 'សូមរង់ចាំមួយភ្លែត ប្រព័ន្ធកំពុងផ្ទៀងផ្ទាត់ និងនាំអ្នកទៅកាន់ Dashboard' : 'Please wait a moment while verifying your OTP...'

  try {
    const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
    const response = await fetch('/auth/email-otp/verify', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({
        email: otpEmail.value,
        otp: otpCode.value,
      }),
    })

    const rawText = await response.text()
    let data: any = {}
    try {
      data = JSON.parse(rawText)
    } catch (e) {
      if (response.ok || response.status === 200 || response.redirected) {
        window.location.assign('/dashboard')
        return
      }
      data = {
        message: currentLang.value === 'km' ? 'មានបញ្ហាបច្ចេកទេសលើ Server' : 'Technical server error'
      }
    }

    if (response.ok && data.success) {
      if (data.token) {
        try { localStorage.setItem('auth_token', data.token) } catch (e) {}
      }
      setTimeout(() => {
        window.location.assign(data.redirect || '/student/dashboard')
      }, 300)
    } else {
      isAuthenticating.value = false
      let errMsg = data.message || ''
      if (typeof errMsg === 'string' && (errMsg.startsWith('<') || errMsg.includes('<!DOCTYPE'))) {
        errMsg = currentLang.value === 'km' ? 'មានបញ្ហាបច្ចេកទេសលើ Server សូមព្យាយាមម្តងទៀត' : 'Server error, please try again'
      }
      oauthNotice.value = {
        type: 'error',
        message: errMsg || (currentLang.value === 'km' ? 'លេខកូដ OTP មិនត្រឹមត្រូវ ឬផុតកំណត់!' : 'Invalid or expired OTP code!')
      }
    }
  } catch (err: any) {
    isAuthenticating.value = false
    oauthNotice.value = {
      type: 'error',
      message: err?.message || (currentLang.value === 'km' ? 'មានបញ្ហាក្នុងការផ្ទៀងផ្ទាត់ OTP' : 'OTP verification error')
    }
  } finally {
    isOtpVerifying.value = false
  }
}

onUnmounted(() => {
  if (otpCountdownTimer) clearInterval(otpCountdownTimer)
})

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
      const formEl = document.createElement('form')
      formEl.method = 'POST'
      formEl.action = '/auth/telegram'
      for (const key in tgUser) {
        if (Object.prototype.hasOwnProperty.call(tgUser, key) && tgUser[key] !== null && tgUser[key] !== undefined) {
          const input = document.createElement('input')
          input.type = 'hidden'
          input.name = key
          input.value = typeof tgUser[key] === 'object' ? JSON.stringify(tgUser[key]) : String(tgUser[key])
          formEl.appendChild(input)
        }
      }
      document.body.appendChild(formEl)
      formEl.submit()
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

  if (provider === 'Email') {
    authMode.value = 'otp'
    otpStep.value = 1
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
  <Head title="Sign In - SPI AI-ELMS" />

  <div class="min-h-screen w-full bg-[#050a17] text-slate-200 flex flex-col justify-between items-center p-3.5 sm:p-6 lg:p-8 relative overflow-x-hidden font-sans selection:bg-blue-600 selection:text-white transition-colors duration-300">
    
    <!-- 3D Three.js Futuristic Wireframe & Particle Background -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0 select-none">
      <AuthAnimatedBackground />

      <!-- Tech Matrix Dot Grid Background -->
      <div class="absolute inset-0 bg-[radial-gradient(#38bdf8_1.2px,transparent_1.2px)] [background-size:32px_32px] opacity-15"></div>

      <!-- Futuristic Blur Glows -->
      <div class="absolute -top-28 -left-28 w-80 sm:w-96 h-80 sm:h-96 bg-blue-600/20 rounded-full blur-[100px] pointer-events-none animate-float-slow"></div>
      <div class="absolute -bottom-28 -right-28 w-80 sm:w-96 h-80 sm:h-96 bg-indigo-600/20 rounded-full blur-[100px] pointer-events-none animate-float-reverse"></div>
    </div>

    <!-- Top Navigation Controls -->
    <header class="w-full max-w-5xl flex justify-end items-center gap-2.5 z-20">
      <!-- Language Switcher Pill -->
      <div class="relative lang-switcher-container">
        <button 
          type="button" 
          @click.stop="isLangOpen = !isLangOpen"
          class="flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-full bg-slate-900/85 backdrop-blur-md border border-slate-700/60 text-xs font-medium text-slate-300 hover:text-white hover:border-slate-500 hover:bg-slate-800 transition duration-150 active:scale-95 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
          :title="currentLang === 'km' ? 'ប្តូរភាសា / Change Language' : 'Change Language / ប្តូរភាសា'"
        >
          <img
            :src="languages.find(l => l.code === currentLang)?.flagUrl || '/images/flags/km.svg'"
            :alt="currentLang"
            width="16"
            height="16"
            loading="eager"
            decoding="async"
            class="w-4 h-4 rounded-full object-cover shrink-0 ring-1 ring-slate-600"
          />
          <span class="font-bold text-[11px] sm:text-xs tracking-wide">{{ currentLang === 'km' ? 'KH' : 'EN' }}</span>
          <svg :class="['w-3 h-3 opacity-70 transition-transform duration-200', isLangOpen ? 'rotate-180 text-blue-400' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
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
            class="absolute right-0 mt-2 w-44 rounded-2xl bg-[#0b1329]/95 backdrop-blur-xl border border-slate-700/80 shadow-2xl shadow-black/60 py-1.5 z-50 overflow-hidden"
          >
            <button
              v-for="lang in languages"
              :key="lang.code"
              type="button"
              @click="selectLanguage(lang.code)"
              :class="[
                'w-full flex items-center justify-between px-3.5 py-2.5 text-xs font-semibold transition-colors cursor-pointer select-none',
                currentLang === lang.code
                  ? 'bg-blue-950/60 text-blue-400'
                  : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'
              ]"
            >
              <span class="flex items-center gap-2.5">
                <img :src="lang.flagUrl" :alt="lang.name" width="16" height="16" loading="eager" decoding="async" class="w-4 h-4 rounded-full object-cover shrink-0 shadow-xs" />
                <span>{{ lang.name }}</span>
              </span>
              <svg v-if="currentLang === lang.code" class="w-3.5 h-3.5 text-blue-400 font-bold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
              </svg>
            </button>
          </div>
        </Transition>
      </div>

      <!-- Theme Switcher Pill -->
      <button 
        type="button" 
        @click="toggleTheme"
        class="flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-full bg-slate-900/85 backdrop-blur-md border border-slate-700/60 text-xs font-medium text-slate-300 hover:text-white hover:border-slate-500 hover:bg-slate-800 transition duration-150 active:scale-95 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
        :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
      >
        <svg v-if="isDark" class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" />
        </svg>
        <svg v-else class="w-3.5 h-3.5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
        </svg>
        <span class="hidden sm:inline text-[11px] sm:text-xs font-bold">{{ isDark ? t('theme_light', 'Light Mode') : t('theme_dark', 'Dark Mode') }}</span>
      </button>
    </header>

    <!-- Main Authentication Card -->
    <main class="w-full max-w-[430px] my-auto py-3 sm:py-4 z-10 mx-auto">
      <div class="bg-[#0b1329]/90 backdrop-blur-2xl border border-slate-800/90 shadow-2xl rounded-3xl p-5 sm:p-7 sm:px-8 transition-all duration-300 relative">
        
        <!-- Authenticating Loading State -->
        <div v-if="isAuthenticating" class="w-full flex flex-col items-center justify-center text-center animate-fade-in select-none py-6">
          <div class="relative mb-5">
            <div class="absolute -inset-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-cyan-400 rounded-full blur-md opacity-50 animate-pulse"></div>
            <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 via-cyan-500 to-blue-400 p-[2px] shadow-lg shadow-blue-500/25 flex items-center justify-center">
              <div class="w-full h-full bg-[#080e1e] rounded-[14px] flex items-center justify-center p-1.5">
                <img :src="logoUrl" alt="SPI Logo" class="w-full h-full object-contain rounded-full" />
              </div>
            </div>
          </div>

          <div class="flex items-center justify-center gap-2 mb-2">
            <svg class="animate-spin w-4 h-4 text-cyan-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <h3 class="text-sm sm:text-base font-bold text-white tracking-wide">
              {{ authLoadingTitle || (currentLang === 'km' ? 'កំពុងរៀបចំ Dashboard របស់អ្នក...' : 'Setting up your dashboard...') }}
            </h3>
          </div>
          <p class="text-xs text-slate-400 font-medium mb-6 max-w-xs leading-relaxed">
            {{ authLoadingSubtitle || (currentLang === 'km' ? 'សូមរង់ចាំមួយភ្លែត ប្រព័ន្ធកំពុងដំណើរការផ្ទៀងផ្ទាត់' : 'Please wait a moment while verifying your account') }}
          </p>

          <!-- Indeterminate Progress Bar -->
          <div class="w-48 bg-slate-800 rounded-full h-1.5 overflow-hidden p-0.5 border border-slate-700">
            <div class="bg-gradient-to-r from-blue-500 via-cyan-400 to-teal-400 h-full rounded-full animate-indeterminate"></div>
          </div>
        </div>

        <!-- Normal Login Form & Controls -->
        <div v-else>
          <!-- App Logo & Branding -->
          <div class="flex flex-col items-center text-center mb-4 sm:mb-5">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-blue-600 via-cyan-500 to-blue-400 p-[2px] shadow-lg shadow-blue-500/25 mb-2.5 flex items-center justify-center">
              <div class="w-full h-full bg-[#080e1e] rounded-[14px] flex items-center justify-center p-1.5">
                <img :src="logoUrl" alt="Saint Paul Institute" class="w-full h-full object-contain rounded-full" />
              </div>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white">SPI AI-ELMS</h1>
            <p class="text-[10px] sm:text-xs text-slate-400 mt-1 font-medium bg-slate-900/80 border border-slate-800 px-3 py-0.5 rounded-full inline-block">
              {{ t('login_subtitle', 'Saint Paul Institute') }}
            </p>
          </div>

          <!-- OAuth / System Alerts -->
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
                'rounded-xl p-3 text-xs flex items-start justify-between gap-2.5 shadow-md mb-4 border',
                oauthNotice.type === 'warning'
                  ? 'bg-amber-500/15 border-amber-500/30 text-amber-300'
                  : 'bg-rose-500/15 border-rose-500/30 text-rose-300'
              ]"
            >
              <div class="flex items-start gap-2">
                <svg v-if="oauthNotice.type === 'warning'" class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <svg v-else class="w-4 h-4 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium text-[11px] leading-relaxed">{{ oauthNotice.message }}</span>
              </div>
              <button
                type="button"
                @click="oauthNotice = null"
                class="text-slate-400 hover:text-white p-0.5 rounded-md hover:bg-white/10 transition-colors shrink-0 cursor-pointer"
                title="Close"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
              </button>
            </div>
          </Transition>

          <!-- Error Alert Banner -->
          <div v-if="form.errors.email" class="bg-rose-500/10 border border-rose-500/30 rounded-xl p-2.5 text-rose-300 text-xs flex items-start gap-2 shadow-sm animate-shake mb-4">
            <svg class="w-4 h-4 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="leading-tight font-medium text-[11px]">{{ form.errors.email }}</span>
          </div>

          <!-- Caps Lock Alert -->
          <div v-if="capsLockOn" class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-2 text-amber-300 text-xs flex items-center gap-2 mb-3">
            <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="text-[11px] font-semibold">{{ t('login_caps_lock_active', 'Caps Lock is ON') }}</span>
          </div>

          <!-- Social Notice Toast -->
          <div v-if="socialNotice" class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-2.5 text-blue-300 text-xs flex items-center gap-2 mb-3">
            <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium text-[11px]">{{ socialNotice }}</span>
          </div>

          <!-- Main Password Form Mode -->
          <div v-if="authMode === 'password'">
            <!-- Role Switcher (3 Tabs: Student, Teacher, Admin) -->
            <div class="grid grid-cols-3 gap-1 bg-[#060b18] p-1 rounded-xl border border-slate-800/80 mb-4 sm:mb-5">
              <!-- Student -->
              <button 
                type="button" 
                @click="selectRole('student')" 
                :class="[
                  'min-w-0 min-h-[42px] py-2 px-1 sm:px-2 text-[11px] sm:text-xs font-semibold rounded-lg transition-all duration-200 flex items-center justify-center gap-1 sm:gap-1.5 cursor-pointer select-none active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500',
                  selectedRole === 'student' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 border border-blue-400/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40'
                ]"
              >
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
                <span class="truncate">{{ t('login_tab_student', 'Student') }}</span>
              </button>

              <!-- Teacher -->
              <button 
                type="button" 
                @click="selectRole('teacher')" 
                :class="[
                  'min-w-0 min-h-[42px] py-2 px-1 sm:px-2 text-[11px] sm:text-xs font-semibold rounded-lg transition-all duration-200 flex items-center justify-center gap-1 sm:gap-1.5 cursor-pointer select-none active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500',
                  selectedRole === 'teacher' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 border border-blue-400/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40'
                ]"
              >
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="truncate">{{ t('login_tab_teacher', 'Teacher') }}</span>
              </button>

              <!-- Admin -->
              <button 
                type="button" 
                @click="selectRole('admin')" 
                :class="[
                  'min-w-0 min-h-[42px] py-2 px-1 sm:px-2 text-[11px] sm:text-xs font-semibold rounded-lg transition-all duration-200 flex items-center justify-center gap-1 sm:gap-1.5 cursor-pointer select-none active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500',
                  selectedRole === 'admin' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 border border-blue-400/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40'
                ]"
              >
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="truncate">{{ t('login_tab_admin', 'Admin') }}</span>
              </button>
            </div>

            <!-- Form Fields -->
            <form @submit.prevent="submit" class="space-y-3.5 sm:space-y-4">
              <!-- Identifier Field -->
              <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">{{ identityLabel }}</label>
                <div class="relative group">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500 group-focus-within:text-blue-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                  </span>
                  <input 
                    v-model="form.email"
                    type="text" 
                    required 
                    autocomplete="username"
                    :placeholder="identityPlaceholder" 
                    class="w-full h-11 sm:h-12 pl-10 pr-9 bg-[#070e1e] border border-slate-700/80 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus-visible:ring-2 focus-visible:ring-blue-500 transition duration-150"
                  />
                  <button 
                    v-if="form.email" 
                    type="button" 
                    @click="clearEmail" 
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300 transition cursor-pointer"
                    title="Clear"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                  </button>
                </div>
              </div>

              <!-- Password Field -->
              <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">{{ t('login_input_password_label', 'Password') }}</label>
                <div class="relative group">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500 group-focus-within:text-blue-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                  </span>
                  <input 
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'" 
                    required 
                    autocomplete="current-password"
                    :placeholder="t('login_input_password_placeholder', '••••••••••••')" 
                    class="w-full h-11 sm:h-12 pl-10 pr-10 bg-[#070e1e] border border-slate-700/80 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus-visible:ring-2 focus-visible:ring-blue-500 transition duration-150"
                  />
                  <button 
                    type="button" 
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-500 hover:text-slate-300 transition cursor-pointer min-w-[36px] justify-center focus:outline-none"
                    :title="showPassword ? 'Hide password' : 'Show password'"
                  >
                    <!-- Eye On -->
                    <svg v-if="!showPassword" class="w-4 h-4 transition-transform duration-150 hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <!-- Eye Off -->
                    <svg v-else class="w-4 h-4 text-blue-400 transition-transform duration-150 hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Remember & Forgot Password -->
              <div class="flex items-center justify-between text-xs pt-0.5">
                <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none">
                  <input 
                    v-model="form.remember" 
                    type="checkbox" 
                    class="w-4 h-4 rounded bg-[#070e1e] border-slate-700 text-blue-600 focus:ring-0 focus:ring-offset-0 focus-visible:ring-2 focus-visible:ring-blue-500 cursor-pointer" 
                  />
                  <span class="text-[11px] sm:text-xs">{{ t('login_remember_me', 'Remember me') }}</span>
                </label>
                <Link href="/forgot-password" class="text-blue-400 hover:text-blue-300 font-medium transition text-[11px] sm:text-xs">
                  {{ t('login_forgot_password', 'Forgot Password?') }}
                </Link>
              </div>

              <!-- Submit Button -->
              <button 
                type="submit" 
                :disabled="isSubmitting || form.processing" 
                class="w-full min-h-[44px] sm:min-h-[48px] bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white font-semibold py-2.5 sm:py-3 rounded-xl shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2 active:scale-[0.98] transition duration-150 mt-2 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900"
              >
                <svg v-if="isSubmitting || form.processing" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <template v-else>
                  <span class="text-xs sm:text-sm">{{ t('login_btn_submit', 'Sign In') }}</span>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                  </svg>
                </template>
              </button>
            </form>

            <!-- Divider -->
            <div class="relative my-4 sm:my-5 text-center">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-800"></div>
              </div>
              <span class="relative bg-[#0b1329] px-3 text-[11px] font-bold tracking-wider text-slate-500 uppercase select-none">{{ t('login_or', 'OR') }}</span>
            </div>

            <!-- 3 Buttons: Google, Telegram, Email -->
            <div class="grid grid-cols-3 gap-1.5 sm:gap-2.5">
              <!-- Google -->
              <button 
                type="button" 
                :disabled="isAuthenticating"
                @click="redirectToGoogleOAuth"
                class="min-w-0 min-h-[44px] sm:min-h-[46px] flex items-center justify-center gap-1 sm:gap-1.5 py-2.5 px-1 sm:px-2 rounded-xl bg-[#070e1e] hover:bg-[#101b38] border border-slate-800 hover:border-slate-700 text-xs font-medium text-slate-200 transition duration-150 active:scale-95 cursor-pointer select-none disabled:opacity-60 disabled:cursor-not-allowed focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
              >
                <svg v-if="isGoogleLoading" class="animate-spin w-4 h-4 text-rose-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <template v-else>
                  <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                  </svg>
                  <span class="truncate text-[11px] sm:text-xs">Google</span>
                </template>
              </button>

              <!-- Telegram -->
              <button 
                type="button" 
                :disabled="isAuthenticating"
                @click="redirectToTelegramOAuth"
                class="min-w-0 min-h-[44px] sm:min-h-[46px] flex items-center justify-center gap-1 sm:gap-1.5 py-2.5 px-1 sm:px-2 rounded-xl bg-[#070e1e] hover:bg-[#101b38] border border-slate-800 hover:border-slate-700 text-xs font-medium text-slate-200 transition duration-150 active:scale-95 cursor-pointer select-none disabled:opacity-60 disabled:cursor-not-allowed focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
              >
                <svg v-if="isTelegramLoading" class="animate-spin w-4 h-4 text-[#229ED9] shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <template v-else>
                  <svg class="w-4 h-4 text-[#229ED9] shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 0C5.37 0 0 5.37 0 12s5.37 12 12 12 12-5.37 12-12S18.63 0 12 0zm5.56 8.16l-1.97 9.28c-.15.65-.53.81-1.08.51l-3-2.21-1.45 1.39c-.16.16-.3.3-.61.3l.22-3.05 5.56-5.02c.24-.22-.05-.34-.38-.13l-6.87 4.33-2.96-.92c-.64-.2-.66-.64.13-.95l11.58-4.46c.54-.2 1.01.12.83.92z"/>
                  </svg>
                  <span class="truncate text-[11px] sm:text-xs">Telegram</span>
                </template>
              </button>

              <!-- Email OTP Toggle -->
              <button 
                type="button" 
                :disabled="isAuthenticating"
                @click="handleSocialLogin('Email')"
                class="min-w-0 min-h-[44px] sm:min-h-[46px] flex items-center justify-center gap-1 sm:gap-1.5 py-2.5 px-1 sm:px-2 rounded-xl bg-[#070e1e] hover:bg-[#101b38] border border-slate-800 hover:border-slate-700 text-xs font-medium text-slate-200 transition duration-150 active:scale-95 cursor-pointer select-none disabled:opacity-60 disabled:cursor-not-allowed focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
              >
                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span class="truncate text-[11px] sm:text-xs">Email</span>
              </button>
            </div>
          </div>

          <!-- Email OTP Flow Mode -->
          <div v-else class="space-y-4">
            <div class="flex items-center justify-between pb-1 border-b border-slate-800">
              <button 
                type="button" 
                @click="authMode = 'password'" 
                class="text-xs font-semibold text-blue-400 hover:text-blue-300 flex items-center gap-1.5 cursor-pointer py-1 transition-colors select-none"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                <span>{{ currentLang === 'km' ? 'ត្រឡប់ទៅ Password' : 'Back to Password' }}</span>
              </button>
              <span class="text-[11px] font-bold text-slate-400">
                {{ otpStep === 1 ? (currentLang === 'km' ? 'ជំហានទី ១: ផ្ញើកូដ' : 'Step 1: Send OTP') : (currentLang === 'km' ? 'ជំហានទី ២: ផ្ទៀងផ្ទាត់' : 'Step 2: Verify') }}
              </span>
            </div>

            <!-- OTP Step 1: Send OTP -->
            <div v-if="otpStep === 1" class="space-y-3.5">
              <div class="space-y-1.5">
                <label class="block text-xs font-medium text-slate-300">{{ currentLang === 'km' ? 'អាសយដ្ឋាន Gmail / អ៊ីមែលរបស់អ្នក' : 'Your Official Gmail / Email' }}</label>
                <div class="relative group">
                  <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 group-focus-within:text-blue-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                  </span>
                  <input 
                    v-model="otpEmail" 
                    type="email" 
                    required 
                    placeholder="student@gmail.com" 
                    class="w-full h-11 sm:h-12 pl-10 pr-4 bg-[#070e1e] border border-slate-700/80 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150" 
                    @keydown.enter.prevent="sendEmailOtp" 
                  />
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed">{{ currentLang === 'km' ? 'ប្រព័ន្ធនឹងផ្ញើលេខកូដសម្ងាត់ ៦ ខ្ទង់ពី info@spilms.tech ចូល Gmail របស់អ្នក។' : 'We will send a 6-digit verification code from info@spilms.tech to your Gmail.' }}</p>
              </div>
              <button 
                type="button" 
                @click="sendEmailOtp" 
                :disabled="isOtpSending || !otpEmail" 
                class="w-full min-h-[44px] sm:min-h-[48px] bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold py-2.5 sm:py-3 rounded-xl shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2 active:scale-[0.98] transition duration-150 disabled:opacity-50 text-xs sm:text-sm cursor-pointer"
              >
                <svg v-if="isOtpSending" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <template v-else>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                  <span>{{ currentLang === 'km' ? 'ផ្ញើលេខកូដ OTP ទៅកាន់ Gmail' : 'Send OTP to Gmail' }}</span>
                </template>
              </button>
            </div>

            <!-- OTP Step 2: Verify OTP -->
            <div v-else class="space-y-3.5">
              <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                  <label class="block text-xs font-medium text-slate-300">{{ currentLang === 'km' ? 'វាយបញ្ចូលលេខកូដ OTP ៦ ខ្ទង់' : 'Enter 6-digit OTP Code' }}</label>
                  <button type="button" @click="otpStep = 1" class="text-[11px] text-blue-400 hover:text-blue-300 font-medium cursor-pointer transition-colors">{{ currentLang === 'km' ? 'ប្តូរ Email' : 'Edit email' }}</button>
                </div>
                <p class="text-[11px] text-slate-400">{{ currentLang === 'km' ? 'ផ្ញើទៅកាន់:' : 'Sent to:' }} <strong class="text-cyan-400">{{ otpEmail }}</strong></p>
                <div class="relative">
                  <input 
                    v-model="otpCode" 
                    type="text" 
                    maxlength="6" 
                    required 
                    placeholder="••••••" 
                    class="h-12 w-full text-center tracking-[10px] text-xl font-mono font-bold rounded-xl border border-slate-700/80 bg-[#070e1e] text-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all" 
                    @keydown.enter.prevent="verifyEmailOtp" 
                  />
                </div>
                <div class="flex items-center justify-between text-[11px] pt-1">
                  <span v-if="otpCountdown > 0" class="text-slate-400">{{ currentLang === 'km' ? 'ផុតកំណត់ក្នុងរយៈពេល:' : 'Expires in:' }} <span class="font-bold text-amber-400">{{ formattedOtpTime }}</span></span>
                  <span v-else class="text-rose-400 font-bold">{{ currentLang === 'km' ? 'កូដផុតកំណត់ហើយ' : 'Code expired' }}</span>
                  <button type="button" @click="sendEmailOtp" :disabled="isOtpSending" class="text-blue-400 hover:text-blue-300 font-bold disabled:opacity-50 cursor-pointer transition-colors">{{ currentLang === 'km' ? 'ផ្ញើម្តងទៀត' : 'Resend Code' }}</button>
                </div>
              </div>
              <button 
                type="button" 
                @click="verifyEmailOtp" 
                :disabled="isOtpVerifying || otpCode.length < 6" 
                class="w-full min-h-[44px] sm:min-h-[48px] bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold py-2.5 sm:py-3 rounded-xl shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2 active:scale-[0.98] transition duration-150 disabled:opacity-50 text-xs sm:text-sm cursor-pointer"
              >
                <svg v-if="isOtpVerifying" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <template v-else>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  <span>{{ currentLang === 'km' ? 'ផ្ទៀងផ្ទាត់ & ចូលប្រើប្រព័ន្ធ' : 'Verify & Sign In' }}</span>
                </template>
              </button>
            </div>
          </div>

          <!-- Register Link -->
          <p class="text-center text-xs text-slate-400 mt-5 sm:mt-6">
            {{ t('login_dont_have_account', "Don't have an account?") }}
            <Link href="/register" class="text-blue-400 hover:text-blue-300 font-semibold ml-1 inline-flex items-center gap-1 transition">
              <span>{{ t('login_register_now', 'Register Account') }}</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </Link>
          </p>

          <!-- Terms & Policy -->
          <div class="flex justify-center gap-4 text-[11px] text-slate-500 mt-4 sm:mt-5 pt-3.5 border-t border-slate-800/80">
            <Link href="/privacy" class="hover:text-slate-400 transition">{{ t('privacy_policy', 'Privacy Policy') }}</Link>
            <span>•</span>
            <Link href="/terms" class="hover:text-slate-400 transition">{{ t('terms_of_service', 'Terms of Service') }}</Link>
          </div>
        </div>

      </div>
    </main>

    <!-- Success Modal Alert -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 scale-90 translate-y-2"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-90 translate-y-2"
    >
      <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm select-none">
        <div class="max-w-xs w-full bg-[#0b1329] rounded-3xl p-6 shadow-2xl border border-emerald-500/30 text-center flex flex-col items-center space-y-3.5 transform transition-all">
          <div class="relative flex items-center justify-center">
            <div class="absolute -inset-2 bg-gradient-to-r from-emerald-500/30 to-teal-500/30 rounded-full blur-md animate-pulse"></div>
            <div class="relative w-14 h-14 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-lg shadow-emerald-500/25 ring-4 ring-[#0b1329]">
              <svg class="w-7 h-7 text-white stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" class="checkmark-path" d="M4.5 12.75l6 6 9-13.5"/>
              </svg>
            </div>
          </div>
          
          <div class="space-y-1">
            <h3 class="text-base font-extrabold text-white">
              {{ statusMessage ? t('login_modal_status_title', 'ជូនដំណឹង') : t('login_modal_success_title', 'ចូលប្រើប្រាស់ជោគជ័យ!') }}
            </h3>
            <p class="text-xs text-slate-400 font-medium leading-relaxed">
              {{ statusMessage || t('login_modal_success_msg', 'កំពុងបញ្ជូនអ្នកទៅកាន់ទំព័រដើម...') }}
            </p>
          </div>

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

    <!-- Error Modal Alert -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 scale-90 translate-y-2"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-90 translate-y-2"
    >
      <div v-if="showErrorModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm select-none">
        <div class="max-w-xs w-full bg-[#0b1329] rounded-3xl p-6 shadow-2xl border border-rose-500/30 text-center flex flex-col items-center space-y-3.5 transform transition-all">
          <div class="relative flex items-center justify-center">
            <div class="absolute -inset-2 bg-gradient-to-r from-rose-500/30 to-amber-500/30 rounded-full blur-md animate-pulse"></div>
            <div class="relative w-14 h-14 rounded-full bg-gradient-to-br from-amber-500 via-rose-500 to-rose-600 text-white flex items-center justify-center shadow-lg shadow-rose-500/25 ring-4 ring-[#0b1329]">
              <span class="text-3xl font-black font-sans leading-none">?</span>
            </div>
          </div>
          
          <div class="space-y-1">
            <h3 class="text-base font-extrabold text-white">
              {{ t('login_modal_error_title', 'ព័ត៌មានមិនត្រឹមត្រូវ') }}
            </h3>
            <p class="text-xs text-slate-400 font-medium leading-relaxed">
              {{ errorMessage || form.errors.email || t('login_modal_error_msg', 'សូមពិនិត្យមើលអ៊ីមែល ឬពាក្យសម្ងាត់របស់អ្នកឡើងវិញ!') }}
            </p>
          </div>

          <button
            type="button"
            @click="showErrorModal = false"
            class="w-full py-2.5 px-4 rounded-xl bg-rose-950/60 hover:bg-rose-900/80 border border-rose-800/60 text-rose-300 text-xs font-bold transition-all duration-150 cursor-pointer active:scale-95"
          >
            {{ t('login_modal_close', 'យល់ព្រម') }}
          </button>
        </div>
      </div>
    </Transition>

    <!-- Footer Copyright -->
    <footer class="w-full text-center text-[11px] text-slate-600 z-10 py-1 select-none">
      &copy; 2026 Saint Paul Institute. All rights reserved.
    </footer>

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
  50% { transform: translateY(-20px) rotate(3deg); }
}

@keyframes floatReverse {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(20px) rotate(-3deg); }
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

.animate-float-slow {
  animation: floatSlow 8s ease-in-out infinite;
}

.animate-float-reverse {
  animation: floatReverse 11s ease-in-out infinite;
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

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  20%, 60% { transform: translateX(-4px); }
  40%, 80% { transform: translateX(4px); }
}

.animate-shake {
  animation: shake 0.4s ease-in-out;
}
</style>
