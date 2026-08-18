<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { i18n, type LanguageCode } from '../Services/i18n'

const logoUrl = '/images/logo.png'
const isDark = ref(true)

const languages = [
  { code: 'en' as LanguageCode, name: 'English', label: 'English', short: 'EN', flagUrl: '/images/flags/en.svg' },
  { code: 'km' as LanguageCode, name: 'ភាសាខ្មែរ', label: 'ខ្មែរ', short: 'KH', flagUrl: '/images/flags/km.svg' },
]

const currentLang = computed(() => i18n.locale.value)

const setLanguage = (code: LanguageCode) => {
  i18n.setLanguage(code)
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

  <div class="min-h-screen bg-slate-950 text-slate-200 antialiased font-sans selection:bg-blue-500 selection:text-white transition-colors duration-300">
    <!-- Subtle Ambient Glow -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
      <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[350px] bg-gradient-to-b from-blue-600/10 via-indigo-600/5 to-transparent blur-3xl rounded-full"></div>
      <div class="absolute top-1/3 -left-40 w-80 h-80 bg-blue-600/5 blur-3xl rounded-full"></div>
      <div class="absolute bottom-10 -right-40 w-80 h-80 bg-indigo-600/5 blur-3xl rounded-full"></div>
    </div>

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 backdrop-blur-xl bg-slate-950/80 border-b border-slate-800/80 transition-colors">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Logo & Institution Name -->
        <Link href="/" class="flex items-center gap-3 group">
          <img :src="logoUrl" alt="SPI Logo" class="w-10 h-10 object-contain group-hover:scale-105 transition-transform duration-200" />
          <div>
            <div class="text-xs font-bold text-blue-400 tracking-wider uppercase">Saint Paul Institute</div>
            <div class="text-sm font-extrabold text-white tracking-tight group-hover:text-blue-300 transition-colors">AI-ELMS Portal</div>
          </div>
        </Link>

        <!-- Right Utilities (Language, Back Button) -->
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

          <!-- Back to Login Button -->
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
    <main class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-8">
      
      <!-- Header Section -->
      <div class="text-center space-y-4">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944z" clip-rule="evenodd"></path>
          </svg>
          {{ currentLang === 'km' ? 'គោលការណ៍សុវត្ថិភាព និងឯកជនភាព' : 'Security & Privacy Policy' }}
        </span>

        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-white font-sans">
          {{ currentLang === 'km' ? 'គោលការណ៍ឯកជនភាព' : 'Privacy Policy' }}
        </h1>

        <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto leading-relaxed">
          {{ currentLang === 'km'
            ? 'វិទ្យាស្ថាន សន្តប៉ូល (Saint Paul Institute - SPI) ប្តេជ្ញាការពារទិន្នន័យផ្ទាល់ខ្លួន និងភាពឯកជនរបស់អ្នកប្រើប្រាស់ទាំងអស់នៅលើប្រព័ន្ធ'
            : 'Saint Paul Institute (SPI) is committed to protecting the privacy and personal data of all users on the'
          }}
          <span class="text-blue-400 font-medium">AI-ELMS (spilms.tech)</span>
          {{ currentLang === 'km'
            ? 'ស្របតាមស្តង់ដារសុវត្ថិភាពខ្ពស់បំផុត និងគោលការណ៍ Google API Services User Data Policy។'
            : 'platform in accordance with rigorous security standards and Google API Services User Data Policy.'
          }}
        </p>

        <!-- Metadata Badges -->
        <div class="flex flex-wrap items-center justify-center gap-2.5 sm:gap-3 pt-2 text-xs text-slate-400">
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-medium">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            {{ currentLang === 'km' ? 'សកម្មភាព & ផ្ទៀងផ្ទាត់រួចរាល់' : 'Status: Active & Verified' }}
          </span>
          <span class="px-2.5 py-1 rounded-md bg-slate-900 border border-slate-800 text-slate-400">
            {{ currentLang === 'km' ? 'ដែនកំណត់:' : 'Domain:' }} <span class="text-slate-200 font-medium">https://spilms.tech</span>
          </span>
          <span class="px-2.5 py-1 rounded-md bg-slate-900 border border-slate-800 text-slate-400">
            {{ currentLang === 'km' ? 'កាលបរិច្ឆេទអនុវត្ត:' : 'Effective Date:' }} <span class="text-slate-200 font-medium">August 17, 2026</span>
          </span>
        </div>
      </div>

      <!-- Highlight Box: Google OAuth Limited Use -->
      <div class="relative overflow-hidden rounded-2xl bg-gradient-to-b from-blue-950/40 to-slate-900/60 p-6 sm:p-8 border border-blue-500/30 shadow-lg shadow-blue-950/20 backdrop-blur-sm">
        <div class="flex items-start gap-4">
          <div class="p-2.5 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
          </div>
          <div class="space-y-2">
            <span class="inline-block text-[11px] font-bold tracking-wider uppercase text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded border border-blue-500/20">Google API Limited Use Disclosure</span>
            <h2 class="text-lg sm:text-xl font-bold text-white">
              {{ currentLang === 'km' ? 'ការប្រើប្រាស់ទិន្នន័យ Google OAuth 2.0' : 'Google OAuth 2.0 User Data Usage & Limited Use' }}
            </h2>
            <p class="text-sm text-slate-300 leading-relaxed">
              {{ currentLang === 'km'
                ? 'ប្រព័ន្ធ SPI AI-ELMS ប្រើប្រាស់ Google Sign-In សម្រាប់តែការផ្ទៀងផ្ទាត់អត្តសញ្ញាណ (Authentication) របស់សិស្ស និស្សិត និងសាស្ត្រាចារ្យតែប៉ុណ្ណោះ។ យើងប្រមូលតែ ឈ្មោះ (Name), អ៊ីមែល (Email Address) និង រូបភាព Profile (Avatar) ពី Google ដើម្បីបង្កើតគណនី និងអនុញ្ញាតឱ្យចូលប្រើប្រាស់មុខងារសិក្សា។ SPI មិនលក់ មិនចែករំលែក និងមិនប្រើប្រាស់ទិន្នន័យ Google សម្រាប់គោលបំណងផ្សព្វផ្សាយពាណិជ្ជកម្មឡើយ។'
                : 'SPI AI-ELMS uses Google Sign-In strictly for authenticating students, teachers, and administrators. We only access basic profile information (Full Name, Email Address, and Avatar URL) via standard Google OAuth 2.0 scopes (openid, email, profile) to create or match your academic account. We NEVER sell, share, transfer, or use Google user data for advertising purposes or third-party brokers.'
              }}
            </p>
          </div>
        </div>
      </div>

      <!-- Section List -->
      <div class="space-y-4">
        
        <!-- Section 1 -->
        <div class="rounded-xl bg-slate-900/50 p-6 sm:p-7 border border-slate-800/80 backdrop-blur-sm transition hover:border-slate-700">
          <div class="flex items-center gap-3 mb-3">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-500/10 text-blue-400 font-semibold text-sm border border-blue-500/20 shrink-0">១</span>
            <h3 class="text-base sm:text-lg font-bold text-white">
              {{ currentLang === 'km' ? 'អំពីស្ថាប័ន និងវិសាលភាព (About SPI & Scope)' : '1. About the Institution & Scope' }}
            </h3>
          </div>
          <p class="text-sm text-slate-300/90 leading-relaxed pl-0 sm:pl-10">
            {{ currentLang === 'km'
              ? 'វិទ្យាស្ថាន សន្តប៉ូល (Saint Paul Institute - SPI) គឺជាគ្រឹះស្ថានឧត្តមសិក្សាដែលមានទីតាំងស្ថិតនៅ ខេត្តតាកែវ និងរាជធានីភ្នំពេញ ព្រះរាជាណាចក្រកម្ពុជា។ គោលការណ៍ឯកជនភាពនេះអនុវត្តចំពោះប្រព័ន្ធគ្រប់គ្រងការសិក្សាអេឡិចត្រូនិកផ្លូវការ SPI AI-ELMS ដែលមានអាសយដ្ឋានផ្លូវការ https://spilms.tech និងសេវាកម្មពាក់ព័ន្ធទាំងអស់។'
              : 'Saint Paul Institute (SPI) is a premier higher education institution located in Takeo Province and Phnom Penh, Kingdom of Cambodia. This Privacy Policy applies to the SPI AI-ELMS digital learning management platform hosted at https://spilms.tech and all affiliated educational services.'
            }}
          </p>
        </div>

        <!-- Section 2 -->
        <div class="rounded-xl bg-slate-900/50 p-6 sm:p-7 border border-slate-800/80 backdrop-blur-sm transition hover:border-slate-700">
          <div class="flex items-center gap-3 mb-3">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-500/10 text-blue-400 font-semibold text-sm border border-blue-500/20 shrink-0">២</span>
            <h3 class="text-base sm:text-lg font-bold text-white">
              {{ currentLang === 'km' ? 'ព័ត៌មានដែលយើងប្រមូល (Information We Collect)' : '2. Information We Collect' }}
            </h3>
          </div>
          <div class="pl-0 sm:pl-10 space-y-4">
            <p class="text-sm text-slate-300/90 leading-relaxed">
              {{ currentLang === 'km'
                ? 'យើងប្រមូលព័ត៌មានចាំបាច់ដើម្បីផ្តល់សេវាកម្មអប់រំ និងគ្រប់គ្រងគណនីឱ្យមានសុវត្ថិភាព៖'
                : 'We collect minimal necessary information to deliver academic services and secure user accounts:'
              }}
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
              <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 space-y-2">
                <h4 class="font-bold text-slate-200 flex items-center gap-2">
                  <span class="text-blue-400">👤</span>
                  {{ currentLang === 'km' ? 'ព័ត៌មានគណនីផ្ទាល់ខ្លួន' : 'Personal & Academic Data' }}
                </h4>
                <ul class="text-xs text-slate-400 space-y-1.5 list-disc list-inside">
                  <li>{{ currentLang === 'km' ? 'ឈ្មោះពេញ (ជាភាសាខ្មែរ និងអង់គ្លេស)' : 'Full Name (Khmer & English)' }}</li>
                  <li>{{ currentLang === 'km' ? 'អាសយដ្ឋានអ៊ីមែល' : 'Email Address' }}</li>
                  <li>{{ currentLang === 'km' ? 'លេខកូដសម្គាល់សិស្ស ឬសាស្ត្រាចារ្យ (ID)' : 'Student / Teacher ID' }}</li>
                  <li>{{ currentLang === 'km' ? 'លេខទូរស័ព្ទ (សម្រាប់ OTP & សេចក្តីជូនដំណឹង)' : 'Phone Number (For OTP & Notices)' }}</li>
                  <li>{{ currentLang === 'km' ? 'ប្រវត្តិពិន្ទុ និងវិញ្ញាបនបត្រ' : 'Academic performance & certificates' }}</li>
                </ul>
              </div>

              <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 space-y-2">
                <h4 class="font-bold text-slate-200 flex items-center gap-2">
                  <span class="text-emerald-400">🔑</span>
                  {{ currentLang === 'km' ? 'ទិន្នន័យផ្ទៀងផ្ទាត់ពី Google (Google OAuth)' : 'Google OAuth Data' }}
                </h4>
                <ul class="text-xs text-slate-400 space-y-1.5 list-disc list-inside">
                  <li><strong class="text-slate-300">Google ID / Sub:</strong> {{ currentLang === 'km' ? 'លេខកូដសម្គាល់គណនី' : 'Unique identifier for login' }}</li>
                  <li><strong class="text-slate-300">Email Address:</strong> {{ currentLang === 'km' ? 'អ៊ីមែលផ្ទៀងផ្ទាត់ដោយ Google' : 'Google verified primary email' }}</li>
                  <li><strong class="text-slate-300">Profile Name:</strong> {{ currentLang === 'km' ? 'ឈ្មោះបង្ហាញលើ Profile' : 'Display name for profile' }}</li>
                  <li><strong class="text-slate-300">Avatar / Picture:</strong> {{ currentLang === 'km' ? 'រូបតំណាងគណនី' : 'Profile avatar image URL' }}</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 3 -->
        <div class="rounded-xl bg-slate-900/50 p-6 sm:p-7 border border-slate-800/80 backdrop-blur-sm transition hover:border-slate-700">
          <div class="flex items-center gap-3 mb-3">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-500/10 text-blue-400 font-semibold text-sm border border-blue-500/20 shrink-0">៣</span>
            <h3 class="text-base sm:text-lg font-bold text-white">
              {{ currentLang === 'km' ? 'គោលបំណងនៃការប្រើប្រាស់ទិន្នន័យ (How We Use Information)' : '3. How We Use Information' }}
            </h3>
          </div>
          <div class="pl-0 sm:pl-10 space-y-3">
            <p class="text-sm text-slate-300/90 leading-relaxed">
              {{ currentLang === 'km'
                ? 'ទិន្នន័យទាំងអស់ដែលបានប្រមូលត្រូវបានប្រើប្រាស់សម្រាប់តែគោលបំណងអប់រំ និងការគ្រប់គ្រងប្រព័ន្ធសិក្សារបស់វិទ្យាស្ថាន សន្តប៉ូល ប៉ុណ្ណោះ រួមមាន៖'
                : 'All collected information is used solely for legitimate educational, academic, and administrative operations of Saint Paul Institute, including:'
              }}
            </p>
            <ul class="text-sm text-slate-400 space-y-2 list-disc list-inside">
              <li>{{ currentLang === 'km' ? 'ការផ្ទៀងផ្ទាត់ភាពត្រឹមត្រូវនៃការចូលប្រើប្រាស់គណនី និងការការពារសុវត្ថិភាព' : 'Authenticating account sign-ins, session management, and preventing unauthorized access' }}</li>
              <li>{{ currentLang === 'km' ? 'ការគ្រប់គ្រងការចុះឈ្មោះរៀន មុខវិជ្ជា កិច្ចការផ្ទះ និងតារាងពិន្ទុ' : 'Managing course enrollments, lecture materials, assignments, quizzes, and grade reports' }}</li>
              <li>{{ currentLang === 'km' ? 'ការចេញវិញ្ញាបនបត្រអេឡិចត្រូនិច (E-Certificate) និងការផ្ទៀងផ្ទាត់ QR Code' : 'Issuing digital academic certificates and facilitating public QR code verification' }}</li>
              <li>{{ currentLang === 'km' ? 'ការផ្ញើសារជូនដំណឹងសំខាន់ៗពីសាលា ឬកូដ OTP តាមរយៈ Telegram/Email' : 'Sending essential academic announcements and secure OTP verification codes' }}</li>
            </ul>
          </div>
        </div>

        <!-- Section 4: Google Compliance -->
        <div class="rounded-xl bg-slate-900/50 p-6 sm:p-7 border border-blue-500/30 backdrop-blur-sm transition hover:border-blue-500/50">
          <div class="flex items-center gap-3 mb-3">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-600 text-white font-semibold text-sm shadow-md shadow-blue-500/30 shrink-0">៤</span>
            <h3 class="text-base sm:text-lg font-bold text-white">
              {{ currentLang === 'km' ? 'ការអនុលោមតាមគោលការណ៍ Google API Services (Google Compliance)' : '4. Google API Services User Data Policy Compliance' }}
            </h3>
          </div>
          <div class="pl-0 sm:pl-10 space-y-3">
            <div class="p-4 rounded-xl bg-blue-950/40 border border-blue-500/20 text-xs sm:text-sm text-slate-300 leading-relaxed space-y-3">
              <p>
                <strong class="text-white">SPI AI-ELMS (https://spilms.tech)</strong> adheres strictly to the 
                <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener noreferrer" class="text-blue-400 underline font-semibold hover:text-blue-300">
                  Google API Services User Data Policy
                </a>, including the Limited Use requirements.
              </p>
              <div class="space-y-1.5 text-xs text-slate-300">
                <div class="flex items-start gap-2">
                  <span class="text-emerald-400 font-bold">✓</span>
                  <span><strong class="text-white">No Third-Party Transfer:</strong> We do not sell, rent, or transfer user data obtained via Google APIs to third parties, advertising networks, or data brokers.</span>
                </div>
                <div class="flex items-start gap-2">
                  <span class="text-emerald-400 font-bold">✓</span>
                  <span><strong class="text-white">No Advertising:</strong> Google user data is NEVER used for serving advertisements, personalized marketing, or retargeting.</span>
                </div>
                <div class="flex items-start gap-2">
                  <span class="text-emerald-400 font-bold">✓</span>
                  <span><strong class="text-white">No Unauthorized Human Reading:</strong> No human reads Google user data unless required by applicable law, needed for security troubleshooting with user consent, or for internal aggregate platform metrics.</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 5: Data Security -->
        <div class="rounded-xl bg-slate-900/50 p-6 sm:p-7 border border-slate-800/80 backdrop-blur-sm transition hover:border-slate-700">
          <div class="flex items-center gap-3 mb-3">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-500/10 text-blue-400 font-semibold text-sm border border-blue-500/20 shrink-0">៥</span>
            <h3 class="text-base sm:text-lg font-bold text-white">
              {{ currentLang === 'km' ? 'សុវត្ថិភាពទិន្នន័យ និងការការពារ (Data Security)' : '5. Data Security & Protection' }}
            </h3>
          </div>
          <div class="pl-0 sm:pl-10 space-y-3">
            <p class="text-sm text-slate-300/90 leading-relaxed">
              {{ currentLang === 'km'
                ? 'យើងអនុវត្តវិធានការបច្ចេកវិទ្យា និងការគ្រប់គ្រងយ៉ាងម៉ឺងម៉ាត់បំផុតដើម្បីការពារទិន្នន័យរបស់អ្នក៖'
                : 'We implement industry-leading technical and organizational security measures to protect your personal data:'
              }}
            </p>
            <ul class="text-sm text-slate-400 space-y-2 list-disc list-inside">
              <li><strong class="text-slate-200">HTTPS / TLS 1.3 Encryption:</strong> {{ currentLang === 'km' ? 'រាល់ការបញ្ជូនទិន្នន័យរវាង Browser និង Server ត្រូវបាន Encrypt ១០០%' : 'All network traffic is fully encrypted in transit via TLS 1.3 certificates' }}</li>
              <li><strong class="text-slate-200">Password Hashing:</strong> {{ currentLang === 'km' ? 'ពាក្យសម្ងាត់ត្រូវបាន Hash ដោយក្បួន Bcrypt / Argon2 មិនអាច Decode បាន' : 'All user passwords are encrypted using one-way Bcrypt/Argon2 algorithms' }}</li>
              <li><strong class="text-slate-200">Role-Based Access Control (RBAC):</strong> {{ currentLang === 'km' ? 'កំណត់សិទ្ធិច្បាស់លាស់រវាង Admin, Teacher និង Student' : 'Strict role authorization separating administrative, faculty, and student access' }}</li>
              <li><strong class="text-slate-200">Automated Threat Prevention:</strong> {{ currentLang === 'km' ? 'ប្រព័ន្ធ Firewall ការពារការ Brute-force និងបញ្ឈប់ការប៉ុនប៉ង Hack ភ្លាមៗ' : 'Automated brute-force lockout, rate limiting, and intrusion detection monitoring' }}</li>
            </ul>
          </div>
        </div>

        <!-- Section 6: Cookies -->
        <div class="rounded-xl bg-slate-900/50 p-6 sm:p-7 border border-slate-800/80 backdrop-blur-sm transition hover:border-slate-700">
          <div class="flex items-center gap-3 mb-3">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-500/10 text-blue-400 font-semibold text-sm border border-blue-500/20 shrink-0">៦</span>
            <h3 class="text-base sm:text-lg font-bold text-white">
              {{ currentLang === 'km' ? 'ខូគី និងការរក្សាទុកទិន្នន័យ (Cookies & Local Storage)' : '6. Cookies & Local Storage' }}
            </h3>
          </div>
          <p class="text-sm text-slate-300/90 leading-relaxed pl-0 sm:pl-10">
            {{ currentLang === 'km'
              ? 'យើងប្រើប្រាស់ Cookies និង Local Storage សម្រាប់រក្សាស្ថានភាព Login (Session), ការចងចាំភាសា (Khmer/English), និងរចនាបថពណ៌ (Dark/Light mode)។ យើងមិនប្រើប្រាស់ Third-party Tracking Cookies សម្រាប់តាមដានអ្នកឡើយ។'
              : 'SPI AI-ELMS uses essential first-party session cookies and browser localStorage strictly for user authentication sessions, language preference persistence (Khmer/English), and theme preferences (Dark/Light mode). We do not use third-party tracking cookies.'
            }}
          </p>
        </div>

        <!-- Section 7: User Rights & Deletion -->
        <div class="rounded-xl bg-slate-900/50 p-6 sm:p-7 border border-slate-800/80 backdrop-blur-sm transition hover:border-slate-700">
          <div class="flex items-center gap-3 mb-3">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-500/10 text-blue-400 font-semibold text-sm border border-blue-500/20 shrink-0">៧</span>
            <h3 class="text-base sm:text-lg font-bold text-white">
              {{ currentLang === 'km' ? 'សិទ្ធិរបស់អ្នកប្រើប្រាស់ និងការស្នើសុំលុបទិន្នន័យ (User Rights & Data Deletion)' : '7. User Rights & Data Deletion' }}
            </h3>
          </div>
          <div class="pl-0 sm:pl-10 space-y-3 text-sm text-slate-300/90 leading-relaxed">
            <p>
              {{ currentLang === 'km'
                ? 'អ្នកប្រើប្រាស់គ្រប់រូបមានសិទ្ធិមើល កែសម្រួល ឬស្នើសុំលុបគណនី និងទិន្នន័យផ្ទាល់ខ្លួនចេញពីប្រព័ន្ធ SPI AI-ELMS បានគ្រប់ពេលវេលា។'
                : 'You retain full ownership rights over your personal data. You may at any time inspect, correct, export, or request the permanent deletion of your account and associated records.'
              }}
            </p>
            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 text-xs sm:text-sm space-y-1.5">
              <p class="font-semibold text-slate-200 flex items-center gap-1.5">
                <span>🗑️</span>
                {{ currentLang === 'km' ? 'របៀបស្នើសុំលុបគណនី ឬទិន្នន័យ Google OAuth:' : 'How to request data deletion / Google account unlinking:' }}
              </p>
              <p class="text-slate-400 leading-relaxed">
                {{ currentLang === 'km'
                  ? 'សូមផ្ញើអ៊ីមែលមកកាន់'
                  : 'Send an email to'
                }}
                <a href="mailto:support@spilms.tech" class="text-blue-400 hover:underline font-medium">support@spilms.tech</a>
                {{ currentLang === 'km' ? 'ឬ' : 'or' }}
                <a href="mailto:info@spi.edu.kh" class="text-blue-400 hover:underline font-medium">info@spi.edu.kh</a>
                {{ currentLang === 'km'
                  ? 'ដោយបញ្ជាក់ពីឈ្មោះ អ៊ីមែល និងលេខកូដសម្គាល់របស់អ្នក។ ក្រុមការងារ IT របស់ SPI នឹងដំណើរការលុបទិន្នន័យជូនក្នុងរយៈពេលយ៉ាងយូរបំផុត'
                  : 'specifying your registered email and ID with the subject "Data Deletion Request". Our IT team will process your request within'
                }}
                <strong class="text-slate-200">{{ currentLang === 'km' ? '៤៨ ម៉ោង' : '48 business hours' }}</strong>
                {{ currentLang === 'km' ? 'នៃថ្ងៃធ្វើការ។' : '.' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Section 8: Contact -->
        <div class="rounded-xl bg-slate-900/50 p-6 sm:p-7 border border-slate-800/80 backdrop-blur-sm transition hover:border-slate-700">
          <div class="flex items-center gap-3 mb-4">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-500/10 text-blue-400 font-semibold text-sm border border-blue-500/20 shrink-0">៨</span>
            <h3 class="text-base sm:text-lg font-bold text-white">
              {{ currentLang === 'km' ? 'ព័ត៌មានទំនាក់ទំនង (Contact Information)' : '8. Contact Information' }}
            </h3>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-0 sm:pl-10 text-sm text-slate-400">
            <div class="space-y-1">
              <p class="font-semibold text-slate-200">Saint Paul Institute (SPI)</p>
              <p>Angkeo District, Takeo Province, Cambodia</p>
              <p>Phnom Penh Liaison Office, Cambodia</p>
            </div>
            <div class="space-y-1 md:text-right">
              <p>Email: <a href="mailto:support@spilms.tech" class="text-blue-400 hover:underline font-medium">support@spilms.tech</a></p>
              <p>Official Email: <a href="mailto:info@spi.edu.kh" class="text-blue-400 hover:underline font-medium">info@spi.edu.kh</a></p>
              <p>Portal Website: <a href="https://spilms.tech" target="_blank" class="text-blue-400 hover:underline font-medium">https://spilms.tech</a></p>
            </div>
          </div>
        </div>

      </div>

      <!-- Bottom Quick Links Footer -->
      <footer class="mt-14 pt-8 border-t border-slate-800/80 text-center space-y-4">
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

