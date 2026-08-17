<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { i18n, type LanguageCode } from '../Services/i18n'

const logoUrl = '/images/logo.png'
const isDark = ref(true)
const activeTab = ref<'all' | 'google' | 'data' | 'security' | 'deletion'>('all')

const languages = [
  { code: 'en' as LanguageCode, name: 'English', label: 'English', short: 'EN', flagUrl: '/images/flags/en.svg' },
  { code: 'km' as LanguageCode, name: 'ភាសាខ្មែរ', label: 'ខ្មែរ', short: 'KH', flagUrl: '/images/flags/km.svg' },
]

const currentLang = computed(() => i18n.locale.value)

const setLanguage = (code: LanguageCode) => {
  i18n.setLanguage(code)
}

const toggleTheme = () => {
  isDark.value = !isDark.value
  if (typeof document !== 'undefined') {
    if (isDark.value) {
      document.documentElement.classList.add('dark')
      localStorage.setItem('theme', 'dark')
    } else {
      document.documentElement.classList.remove('dark')
      localStorage.setItem('theme', 'light')
    }
  }
}

onMounted(() => {
  try {
    const saved = localStorage.getItem('theme')
    isDark.value = saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches) || saved === null
    if (isDark.value) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  } catch (e) {}
})
</script>

<template>
  <Head title="Privacy Policy - Saint Paul Institute LMS">
    <meta name="description" content="Privacy Policy and Google API Services User Data Compliance for Saint Paul Institute AI-ELMS (spilms.tech)" />
    <meta name="robots" content="index, follow" />
  </Head>

  <div class="min-h-screen bg-slate-950 text-slate-100 font-sans antialiased selection:bg-blue-500 selection:text-white transition-colors duration-300">
    <!-- Background Ambient Glow -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
      <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-blue-600/15 via-indigo-600/10 to-transparent blur-3xl rounded-full"></div>
      <div class="absolute top-1/3 -left-40 w-96 h-96 bg-purple-600/10 blur-3xl rounded-full"></div>
      <div class="absolute bottom-10 -right-40 w-96 h-96 bg-sky-600/10 blur-3xl rounded-full"></div>
    </div>

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 backdrop-blur-xl bg-slate-950/80 border-b border-slate-800/80 transition-colors">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Logo & Institution Name -->
        <Link href="/" class="flex items-center gap-3 group">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg shadow-blue-500/25 ring-1 ring-white/20 group-hover:scale-105 transition-transform duration-200">
            <img :src="logoUrl" alt="SPI Logo" class="w-7 h-7 object-contain" onerror="this.style.display='none'" />
            <span class="text-white font-black text-sm tracking-wider">SPI</span>
          </div>
          <div>
            <div class="text-xs font-bold text-blue-400 tracking-wider uppercase">Saint Paul Institute</div>
            <div class="text-sm font-extrabold text-white tracking-tight group-hover:text-blue-300 transition-colors">AI-ELMS Portal</div>
          </div>
        </Link>

        <!-- Right Utilities (Language, Theme, Back Button) -->
        <div class="flex items-center gap-2 sm:gap-3">
          <!-- Language Toggle Buttons -->
          <div class="flex items-center bg-slate-900 border border-slate-800 rounded-xl p-1 text-xs">
            <button
              v-for="lang in languages"
              :key="lang.code"
              type="button"
              @click="setLanguage(lang.code)"
              :class="[
                'px-2.5 py-1 rounded-lg font-semibold transition-all duration-200 cursor-pointer',
                currentLang === lang.code
                  ? 'bg-blue-600 text-white shadow-sm'
                  : 'text-slate-400 hover:text-slate-200'
              ]"
            >
              {{ lang.label }}
            </button>
          </div>

          <!-- Back to Login / Home Button -->
          <Link
            href="/login"
            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 rounded-xl shadow-md shadow-blue-600/20 ring-1 ring-white/10 transition-all duration-200 active:scale-95"
          >
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M15 18l-6-6 6-6"/>
            </svg>
            <span class="hidden sm:inline">{{ currentLang === 'km' ? 'ចូលប្រព័ន្ធ' : 'Sign In' }}</span>
            <span class="sm:hidden">{{ currentLang === 'km' ? 'ចូល' : 'Login' }}</span>
          </Link>
        </div>
      </div>
    </header>

    <!-- Main Content Container -->
    <main class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
      
      <!-- Hero Section -->
      <div class="text-center space-y-4 max-w-3xl mx-auto mb-12">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/30 text-blue-400 text-xs font-semibold uppercase tracking-wider backdrop-blur-md shadow-inner">
          <svg class="w-3.5 h-3.5 text-blue-400" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
          </svg>
          <span>{{ currentLang === 'km' ? 'គោលការណ៍សុវត្ថិភាព និងឯកជនភាពផ្លូវការ' : 'Official Privacy Policy & Google Compliance' }}</span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
          {{ currentLang === 'km' ? 'គោលការណ៍ឯកជនភាព' : 'Privacy Policy' }}
        </h1>

        <p class="text-sm sm:text-base text-slate-400 max-w-2xl mx-auto leading-relaxed">
          {{ currentLang === 'km' 
            ? 'វិទ្យាស្ថាន សន្តប៉ូល (Saint Paul Institute - SPI) ប្តេជ្ញាការពារទិន្នន័យផ្ទាល់ខ្លួន និងភាពឯកជនរបស់អ្នកប្រើប្រាស់ទាំងអស់នៅលើប្រព័ន្ធ AI-ELMS (https://spilms.tech) ស្របតាមស្តង់ដារសុវត្ថិភាពខ្ពស់បំផុត និងគោលការណ៍ Google API Services User Data Policy។'
            : 'Saint Paul Institute (SPI) is committed to protecting the privacy and security of all students, faculty, and users on the AI-ELMS platform (https://spilms.tech) in accordance with rigorous data security standards and the Google API Services User Data Policy.'
          }}
        </p>

        <!-- Meta Bar -->
        <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 pt-2 text-xs text-slate-400 font-mono">
          <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Status: <strong>Active & Verified</strong></span>
          </div>
          <div>•</div>
          <div>Domain: <code class="text-blue-400 font-bold">https://spilms.tech</code></div>
          <div>•</div>
          <div>Effective Date: <strong>August 17, 2026</strong></div>
        </div>
      </div>

      <!-- Google OAuth Notice Highlight Card -->
      <section class="mb-10 p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-blue-950/60 via-slate-900/90 to-indigo-950/60 border-2 border-blue-500/40 shadow-2xl backdrop-blur-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="flex flex-col sm:flex-row items-start gap-4">
          <div class="w-12 h-12 rounded-2xl bg-blue-500/20 border border-blue-400/40 flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/20 text-2xl">
            🔒
          </div>
          <div class="space-y-2">
            <div class="flex items-center gap-2">
              <span class="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 text-[11px] font-bold uppercase tracking-wide border border-blue-500/30">Google API Limited Use Disclosure</span>
            </div>
            <h2 class="text-lg sm:text-xl font-black text-white">
              {{ currentLang === 'km' ? 'ការប្រើប្រាស់ទិន្នន័យ Google OAuth 2.0' : 'Google OAuth 2.0 User Data Usage & Limited Use' }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
              {{ currentLang === 'km'
                ? 'ប្រព័ន្ធ SPI AI-ELMS ប្រើប្រាស់ Google Sign-In សម្រាប់តែការផ្ទៀងផ្ទាត់អត្តសញ្ញាណ (Authentication) របស់សិស្ស និស្សិត និងសាស្ត្រាចារ្យតែប៉ុណ្ណោះ។ យើងប្រមូលតែ ឈ្មោះ (Name), អ៊ីមែល (Email Address) និងរូបភាព Profile (Avatar) ពី Google ដើម្បីបង្កើតគណនី និងអនុញ្ញាតឱ្យចូលប្រើប្រាស់មុខងារសិក្សា។ SPI មិនលក់ មិនចែករំលែក និងមិនប្រើប្រាស់ទិន្នន័យ Google សម្រាប់គោលបំណងផ្សព្វផ្សាយពាណិជ្ជកម្មឡើយ។'
                : 'SPI AI-ELMS uses Google Sign-In strictly for authenticating students, teachers, and administrators. We only access basic profile information (Full Name, Email Address, and Avatar URL) via standard Google OAuth 2.0 scopes (openid, email, profile) to create or match your academic account. We NEVER sell, share, transfer, or use Google user data for advertising purposes or third-party brokers.'
              }}
            </p>
          </div>
        </div>
      </section>

      <!-- Detailed Privacy Policy Content Sections -->
      <div class="space-y-8 text-slate-300 text-sm sm:text-base leading-relaxed">

        <!-- 1. Who We Are -->
        <article class="p-6 sm:p-8 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-3">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 font-black text-sm flex items-center justify-center border border-blue-500/30">1</span>
            <h3 class="text-lg font-bold text-white">
              {{ currentLang === 'km' ? '១. អំពីស្ថាប័ន និងវិសាលភាព (About SPI & Scope)' : '1. About the Institution & Scope' }}
            </h3>
          </div>
          <p class="text-xs sm:text-sm text-slate-300">
            {{ currentLang === 'km'
              ? 'វិទ្យាស្ថាន សន្តប៉ូល (Saint Paul Institute - SPI) គឺជាគ្រឹះស្ថានឧត្តមសិក្សាដែលមានទីតាំងស្ថិតនៅ ខេត្តតាកែវ និងរាជធានីភ្នំពេញ ព្រះរាជាណាចក្រកម្ពុជា។ គោលការណ៍ឯកជនភាពនេះអនុវត្តចំពោះប្រព័ន្ធគ្រប់គ្រងការសិក្សាអេឡិចត្រូនិកឆ្លាតវៃ SPI AI-ELMS ដែលមានអាសយដ្ឋានផ្លូវការ https://spilms.tech និងសេវាកម្មពាក់ព័ន្ធទាំងអស់។'
              : 'Saint Paul Institute (SPI) is a premier higher education institution located in Takeo Province and Phnom Penh, Kingdom of Cambodia. This Privacy Policy applies to the SPI AI-ELMS digital learning management platform hosted at https://spilms.tech and all affiliated educational services.'
            }}
          </p>
        </article>

        <!-- 2. Information We Collect -->
        <article class="p-6 sm:p-8 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-4">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 font-black text-sm flex items-center justify-center border border-blue-500/30">2</span>
            <h3 class="text-lg font-bold text-white">
              {{ currentLang === 'km' ? '២. ព័ត៌មានដែលយើងប្រមូល (Information We Collect)' : '2. Information We Collect' }}
            </h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800/80 space-y-2">
              <h4 class="font-bold text-sm text-blue-300 flex items-center gap-2">
                <span>👤</span>
                {{ currentLang === 'km' ? 'ព័ត៌មានគណនីផ្ទាល់ខ្លួន' : 'Personal & Academic Data' }}
              </h4>
              <ul class="text-xs text-slate-400 space-y-1.5 list-disc list-inside">
                <li>{{ currentLang === 'km' ? 'ឈ្មោះពេញ (ជាភាសាខ្មែរ និងអង់គ្លេស)' : 'Full Name (Khmer & English)' }}</li>
                <li>{{ currentLang === 'km' ? 'អាសយដ្ឋានអ៊ីមែលស្ថាប័ន ឬផ្ទាល់ខ្លួន' : 'Email Address' }}</li>
                <li>{{ currentLang === 'km' ? 'លេខកូដសម្គាល់សិស្ស ឬសាស្ត្រាចារ្យ (ID Number)' : 'Student / Teacher Identification ID' }}</li>
                <li>{{ currentLang === 'km' ? 'លេខទូរស័ព្ទ (សម្រាប់ OTP និងការជូនដំណឹង)' : 'Phone Number (For OTP recovery & notices)' }}</li>
                <li>{{ currentLang === 'km' ? 'ប្រវត្តិពិន្ទុ ការចូលរៀន និងវិញ្ញាបនបត្រ' : 'Academic performance, quiz scores, certificates' }}</li>
              </ul>
            </div>

            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800/80 space-y-2">
              <h4 class="font-bold text-sm text-emerald-300 flex items-center gap-2">
                <span>🔑</span>
                {{ currentLang === 'km' ? 'ទិន្នន័យផ្ទៀងផ្ទាត់ពី Google (Google OAuth)' : 'Google OAuth Data' }}
              </h4>
              <ul class="text-xs text-slate-400 space-y-1.5 list-disc list-inside">
                <li><strong class="text-slate-200">Google ID / OpenID Sub:</strong> {{ currentLang === 'km' ? 'លេខកូដសម្គាល់គណនីតែមួយគត់' : 'Unique identifier for login matching' }}</li>
                <li><strong class="text-slate-200">Email Address:</strong> {{ currentLang === 'km' ? 'អ៊ីមែលដែលបានផ្ទៀងផ្ទាត់ដោយ Google' : 'Google verified primary email' }}</li>
                <li><strong class="text-slate-200">Profile Name:</strong> {{ currentLang === 'km' ? 'ឈ្មោះបង្ហាញលើ Profile' : 'Display name for student/teacher profile' }}</li>
                <li><strong class="text-slate-200">Avatar / Picture URL:</strong> {{ currentLang === 'km' ? 'រូបតំណាងគណនី' : 'Profile avatar image URL' }}</li>
              </ul>
            </div>
          </div>
        </article>

        <!-- 3. How We Use Your Data -->
        <article class="p-6 sm:p-8 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-3">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 font-black text-sm flex items-center justify-center border border-blue-500/30">3</span>
            <h3 class="text-lg font-bold text-white">
              {{ currentLang === 'km' ? '៣. គោលបំណងនៃការប្រើប្រាស់ទិន្នន័យ (How We Use Information)' : '3. How We Use Information' }}
            </h3>
          </div>
          <p class="text-xs sm:text-sm text-slate-300">
            {{ currentLang === 'km'
              ? 'ទិន្នន័យទាំងអស់ដែលបានប្រមូលត្រូវបានប្រើប្រាស់សម្រាប់តែគោលបំណងអប់រំ និងការគ្រប់គ្រងប្រព័ន្ធសិក្សារបស់វិទ្យាស្ថាន សន្តប៉ូល ប៉ុណ្ណោះ រួមមាន៖'
              : 'All collected information is used solely for legitimate educational, academic, and administrative operations of Saint Paul Institute, including:'
            }}
          </p>
          <ul class="text-xs sm:text-sm text-slate-400 space-y-2 list-disc list-inside pt-1">
            <li>{{ currentLang === 'km' ? 'ការផ្ទៀងផ្ទាត់ភាពត្រឹមត្រូវនៃការចូលប្រើប្រាស់គណនី និងការការពារសុវត្ថិភាព' : 'Authenticating account sign-ins, session management, and preventing unauthorized access' }}</li>
            <li>{{ currentLang === 'km' ? 'ការគ្រប់គ្រងការចុះឈ្មោះរៀន មុខវិជ្ជា កិច្ចការផ្ទះ និងតារាងពិន្ទុ' : 'Managing course enrollments, lecture materials, assignments, quizzes, and grade reports' }}</li>
            <li>{{ currentLang === 'km' ? 'ការចេញវិញ្ញាបនបត្រអេឡិចត្រូនិច (E-Certificate) និងការផ្ទៀងផ្ទាត់ QR Code' : 'Issuing digital academic certificates and facilitating public QR code verification' }}</li>
            <li>{{ currentLang === 'km' ? 'ការផ្ញើសារជូនដំណឹងសំខាន់ៗពីសាលា ឬកូដ OTP តាមរយៈ Telegram/Email' : 'Sending essential academic announcements and secure OTP verification codes' }}</li>
          </ul>
        </article>

        <!-- 4. Google API Services User Data Policy Compliance -->
        <article class="p-6 sm:p-8 rounded-2xl bg-slate-900/70 border border-blue-500/30 space-y-3 bg-gradient-to-b from-blue-950/30 to-slate-900/70">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-blue-600 text-white font-black text-sm flex items-center justify-center shadow-md shadow-blue-500/30">4</span>
            <h3 class="text-lg font-bold text-white">
              {{ currentLang === 'km' ? '៤. ការអនុលោមតាមគោលការណ៍ Google API Services' : '4. Google API Services User Data Policy Compliance' }}
            </h3>
          </div>
          <div class="p-4 rounded-xl bg-blue-950/50 border border-blue-500/30 text-xs sm:text-sm text-blue-200 leading-relaxed">
            <p>
              <strong>SPI AI-ELMS (https://spilms.tech)</strong> adheres strictly to the 
              <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener noreferrer" class="text-blue-400 underline font-semibold hover:text-blue-300">
                Google API Services User Data Policy
              </a>, including the Limited Use requirements.
            </p>
            <div class="mt-3 space-y-1.5 text-xs text-slate-300">
              <div>✓ <strong>No Third-Party Transfer:</strong> We do not sell, rent, or transfer user data obtained via Google APIs to third parties, advertising networks, or data brokers.</div>
              <div>✓ <strong>No Advertising:</strong> Google user data is NEVER used for serving advertisements or retargeting.</div>
              <div>✓ <strong>No Unauthorized Human Reading:</strong> No human reads Google user data unless required by applicable law, needed for security troubleshooting with user consent, or for internal aggregate platform metrics.</div>
            </div>
          </div>
        </article>

        <!-- 5. Data Security & Encryption -->
        <article class="p-6 sm:p-8 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-3">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 font-black text-sm flex items-center justify-center border border-blue-500/30">5</span>
            <h3 class="text-lg font-bold text-white">
              {{ currentLang === 'km' ? '៥. សុវត្ថិភាពទិន្នន័យ និងការការពារ (Data Security)' : '5. Data Security & Protection' }}
            </h3>
          </div>
          <p class="text-xs sm:text-sm text-slate-300">
            {{ currentLang === 'km'
              ? 'យើងអនុវត្តវិធានការបច្ចេកវិទ្យា និងការគ្រប់គ្រងយ៉ាងម៉ឺងម៉ាត់បំផុតដើម្បីការពារទិន្នន័យរបស់អ្នក៖'
              : 'We implement industry-leading technical and organizational security measures to protect your personal data:'
            }}
          </p>
          <ul class="text-xs sm:text-sm text-slate-400 space-y-1.5 list-disc list-inside">
            <li><strong>HTTPS / TLS 1.3 Encryption:</strong> {{ currentLang === 'km' ? 'រាល់ការបញ្ជូនទិន្នន័យរវាង Browser និង Server ត្រូវបាន Encrypt ១០០%' : 'All network traffic is fully encrypted in transit via TLS 1.3 certificates' }}</li>
            <li><strong>Password Hashing:</strong> {{ currentLang === 'km' ? 'ពាក្យសម្ងាត់ត្រូវបាន Hash ដោយក្បួន Bcrypt / Argon2 មិនអាច Decode បាន' : 'All user passwords are encrypted using one-way Bcrypt/Argon2 algorithms' }}</li>
            <li><strong>Role-Based Access Control (RBAC):</strong> {{ currentLang === 'km' ? 'កំណត់សិទ្ធិច្បាស់លាស់រវាង Admin, Teacher និង Student' : 'Strict role authorization separating administrative, faculty, and student access' }}</li>
            <li><strong>Automated Threat Prevention:</strong> {{ currentLang === 'km' ? 'ប្រព័ន្ធ Firewall ការពារការ Brute-force និងបញ្ឈប់ការប៉ុនប៉ង Hack ភ្លាមៗ' : 'Automated brute-force lockout, rate limiting, and intrusion detection monitoring' }}</li>
          </ul>
        </article>

        <!-- 6. Cookies and Sessions -->
        <article class="p-6 sm:p-8 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-3">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 font-black text-sm flex items-center justify-center border border-blue-500/30">6</span>
            <h3 class="text-lg font-bold text-white">
              {{ currentLang === 'km' ? '៦. ខូគី និងការរក្សាទុកទិន្នន័យ (Cookies & Local Storage)' : '6. Cookies & Local Storage' }}
            </h3>
          </div>
          <p class="text-xs sm:text-sm text-slate-300">
            {{ currentLang === 'km'
              ? 'យើងប្រើប្រាស់ Cookies និង Local Storage សម្រាប់រក្សាស្ថានភាព Login (Session), ការចងចាំភាសា (Khmer/English), និងរចនាបថពណ៌ (Dark/Light mode)។ អ្នកអាចបិទ Cookies ក្នុង Browser បាន ប៉ុន្តែអាចប៉ះពាល់ដល់មុខងារមួយចំនួននៃការ Login។'
              : 'SPI AI-ELMS uses essential first-party session cookies and browser localStorage strictly for user authentication sessions, language preference persistence (Khmer/English), and theme preferences (Dark/Light mode). We do not use third-party tracking cookies.'
            }}
          </p>
        </article>

        <!-- 7. User Rights and Data Deletion Requests -->
        <article class="p-6 sm:p-8 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-4">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 font-black text-sm flex items-center justify-center border border-blue-500/30">7</span>
            <h3 class="text-lg font-bold text-white">
              {{ currentLang === 'km' ? '៧. សិទ្ធិរបស់អ្នកប្រើប្រាស់ និងការស្នើសុំលុបទិន្នន័យ (User Rights & Data Deletion)' : '7. User Rights & Data Deletion' }}
            </h3>
          </div>
          <p class="text-xs sm:text-sm text-slate-300">
            {{ currentLang === 'km'
              ? 'អ្នកប្រើប្រាស់គ្រប់រូបមានសិទ្ធិមើល កែសម្រួល ឬស្នើសុំលុបគណនី និងទិន្នន័យផ្ទាល់ខ្លួនចេញពីប្រព័ន្ធ SPI AI-ELMS បានគ្រប់ពេលវេលា។'
              : 'You retain full ownership rights over your personal data. You may at any time inspect, correct, export, or request the permanent deletion of your account and associated records.'
            }}
          </p>
          <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2 text-xs">
            <div class="font-bold text-white flex items-center gap-2">
              <span>🗑️</span>
              {{ currentLang === 'km' ? 'របៀបស្នើសុំលុបគណនី ឬទិន្នន័យ Google OAuth:' : 'How to request data deletion / Google account unlinking:' }}
            </div>
            <p class="text-slate-400">
              {{ currentLang === 'km'
                ? 'សូមផ្ញើអ៊ីមែលមកកាន់ support@spilms.tech ឬ info@spi.edu.kh ដោយបញ្ជាក់ពីឈ្មោះ អ៊ីមែល និងលេខកូដសិស្ស/គ្រូរបស់អ្នក។ ក្រុមការងារ IT របស់ SPI នឹងដំណើរការលុបទិន្នន័យជូនក្នុងរយៈពេលយ៉ាងយូរបំផុត ៤៨ ម៉ោងនៃថ្ងៃធ្វើការ។'
                : 'Send an email to support@spilms.tech or info@spi.edu.kh specifying your registered email and student/teacher ID with the subject "Data Deletion Request". Our IT administration team will process and purge your data within 48 business hours.'
              }}
            </p>
          </div>
        </article>

        <!-- 8. Contact Information -->
        <article class="p-6 sm:p-8 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950/70 border border-slate-800 space-y-4">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 font-black text-sm flex items-center justify-center border border-blue-500/30">8</span>
            <h3 class="text-lg font-bold text-white">
              {{ currentLang === 'km' ? '៨. ព័ត៌មានទំនាក់ទំនង (Contact Information)' : '8. Contact Information' }}
            </h3>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1 text-xs sm:text-sm">
            <div class="space-y-1">
              <div class="font-bold text-white">Saint Paul Institute (SPI)</div>
              <div class="text-slate-400">Angkeo District, Takeo Province, Cambodia</div>
              <div class="text-slate-400">Phnom Penh Liaison Office, Cambodia</div>
            </div>
            <div class="space-y-1 sm:text-right">
              <div>Email: <a href="mailto:support@spilms.tech" class="text-blue-400 font-semibold hover:underline">support@spilms.tech</a></div>
              <div>Official Email: <a href="mailto:info@spi.edu.kh" class="text-blue-400 font-semibold hover:underline">info@spi.edu.kh</a></div>
              <div>Portal Website: <a href="https://spilms.tech" class="text-blue-400 font-semibold hover:underline">https://spilms.tech</a></div>
            </div>
          </div>
        </article>

      </div>

      <!-- Bottom Quick Links Footer -->
      <footer class="mt-14 pt-8 border-t border-slate-800 text-center space-y-4">
        <div class="flex flex-wrap items-center justify-center gap-6 text-xs text-slate-400">
          <Link href="/privacy" class="text-blue-400 font-bold hover:underline">{{ currentLang === 'km' ? 'គោលការណ៍ឯកជនភាព' : 'Privacy Policy' }}</Link>
          <span>•</span>
          <Link href="/terms" class="hover:text-slate-200 transition-colors">{{ currentLang === 'km' ? 'លក្ខខណ្ឌប្រើប្រាស់' : 'Terms of Service' }}</Link>
          <span>•</span>
          <Link href="/login" class="hover:text-slate-200 transition-colors">{{ currentLang === 'km' ? 'ទំព័រដើម / ចូលប្រព័ន្ធ' : 'Sign In' }}</Link>
        </div>
        <p class="text-xs text-slate-400 font-sans">
          © 2026 Saint Paul Institute (SPI). All Rights Reserved. AI-Based E-Learning Platform.
        </p>
      </footer>

    </main>
  </div>
</template>
