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
  <Head title="Terms of Service - Saint Paul Institute LMS">
    <meta name="description" content="Terms of Service and Conditions of Use for Saint Paul Institute AI-ELMS (spilms.tech)" />
    <meta name="robots" content="index, follow" />
  </Head>

  <div class="min-h-screen bg-slate-950 text-slate-100 font-sans antialiased selection:bg-blue-500 selection:text-white transition-colors duration-300">
    <!-- Background Ambient Glow -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
      <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-blue-600/15 via-indigo-600/10 to-transparent blur-3xl rounded-full"></div>
      <div class="absolute top-1/3 -right-40 w-96 h-96 bg-purple-600/10 blur-3xl rounded-full"></div>
      <div class="absolute bottom-10 -left-40 w-96 h-96 bg-sky-600/10 blur-3xl rounded-full"></div>
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
          <svg class="w-3.5 h-3.5 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
          </svg>
          <span>{{ currentLang === 'km' ? 'លក្ខខណ្ឌប្រើប្រាស់ប្រព័ន្ធសិក្សា' : 'Platform Terms & Academic Agreement' }}</span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
          {{ currentLang === 'km' ? 'លក្ខខណ្ឌប្រើប្រាស់សេវាកម្ម' : 'Terms of Service' }}
        </h1>

        <p class="text-sm sm:text-base text-slate-400 max-w-2xl mx-auto leading-relaxed">
          {{ currentLang === 'km' 
            ? 'សូមអានលក្ខខណ្ឌប្រើប្រាស់ខាងក្រោមឱ្យបានច្បាស់លាស់មុនពេលប្រើប្រាស់ប្រព័ន្ធគ្រប់គ្រងការសិក្សា SPI AI-ELMS (https://spilms.tech)។ ការចូលប្រើប្រាស់របស់អ្នកមានន័យថាអ្នកបានយល់ព្រមតាមលក្ខខណ្ឌទាំងអស់នេះ។'
            : 'Please review these Terms of Service carefully before accessing or using the Saint Paul Institute AI-ELMS platform (https://spilms.tech). By signing in, you agree to be bound by these institutional terms and policies.'
          }}
        </p>

        <!-- Meta Bar -->
        <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 pt-2 text-xs text-slate-400 font-mono">
          <div>Platform: <code class="text-blue-400 font-bold">https://spilms.tech</code></div>
          <div>•</div>
          <div>Last Revised: <strong>August 17, 2026</strong></div>
        </div>
      </div>

      <!-- Detailed Terms Sections -->
      <div class="space-y-8 text-slate-300 text-sm sm:text-base leading-relaxed">

        <!-- 1. Acceptance -->
        <article class="p-6 sm:p-8 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-3">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 font-black text-sm flex items-center justify-center border border-blue-500/30">1</span>
            <h3 class="text-lg font-bold text-white">
              {{ currentLang === 'km' ? '១. ការយល់ព្រមលើលក្ខខណ្ឌ (Acceptance of Terms)' : '1. Acceptance of Terms' }}
            </h3>
          </div>
          <p class="text-xs sm:text-sm text-slate-300">
            {{ currentLang === 'km'
              ? 'ដោយការបង្កើតគណនី ការចូលតាមរយៈ Google Sign-In ឬការប្រើប្រាស់គេហទំព័រ https://spilms.tech អ្នកបញ្ជាក់ថាអ្នកជាសិស្ស និស្សិត សាស្ត្រាចារ្យ ឬបុគ្គលិកដែលមានសិទ្ធិស្របច្បាប់របស់វិទ្យាស្ថាន សន្តប៉ូល ហើយយល់ព្រមគោរពតាមបទបញ្ជាផ្ទៃក្នុងរបស់វិទ្យាស្ថាន។'
              : 'By creating an account, authenticating via Google Sign-In, or using https://spilms.tech, you confirm that you are an authorized student, faculty member, or staff of Saint Paul Institute and agree to adhere to these Terms and SPI institutional bylaws.'
            }}
          </p>
        </article>

        <!-- 2. Account Security & Google Sign-In -->
        <article class="p-6 sm:p-8 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-3">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 font-black text-sm flex items-center justify-center border border-blue-500/30">2</span>
            <h3 class="text-lg font-bold text-white">
              {{ currentLang === 'km' ? '២. សុវត្ថិភាពគណនី និងការ Login ជាមួយ Google' : '2. Account Security & Google Authentication' }}
            </h3>
          </div>
          <p class="text-xs sm:text-sm text-slate-300">
            {{ currentLang === 'km'
              ? 'អ្នកប្រើប្រាស់ត្រូវទទួលខុសត្រូវចំពោះការរក្សាការសម្ងាត់នៃព័ត៌មាន Login របស់ខ្លួន។ ប្រសិនបើអ្នកប្រើ Google Sign-In អ្នកត្រូវប្រាកដថាគណនី Google ផ្ទាល់ខ្លួនរបស់អ្នកមានសុវត្ថិភាពខ្ពស់។ ហាមដាច់ខាតការចែករំលែក Password ឬ Session ឱ្យអ្នកដទៃចូលប្រើជំនួស។'
              : 'Users are strictly responsible for safeguarding their login credentials. When using Google Sign-In, ensure your Google account is secured. Account sharing, proxy examination taking, or unauthorized credential disclosure is strictly prohibited under SPI disciplinary rules.'
            }}
          </p>
        </article>

        <!-- 3. Academic Integrity & Course Materials -->
        <article class="p-6 sm:p-8 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-3">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 font-black text-sm flex items-center justify-center border border-blue-500/30">3</span>
            <h3 class="text-lg font-bold text-white">
              {{ currentLang === 'km' ? '៣. សេចក្តីថ្លៃថ្នូរក្នុងការសិក្សា និងកម្មសិទ្ធិបញ្ញា' : '3. Academic Integrity & Intellectual Property' }}
            </h3>
          </div>
          <p class="text-xs sm:text-sm text-slate-300">
            {{ currentLang === 'km'
              ? 'រាល់ឯកសារមេរៀន វីដេអូបង្រៀន កម្រងសំណួរ និងវិញ្ញាបនបត្រទាំងអស់នៅលើ SPI AI-ELMS គឺជាកម្មសិទ្ធិបញ្ញារបស់វិទ្យាស្ថាន សន្តប៉ូល និងសាស្ត្រាចារ្យដែលពាក់ព័ន្ធ។ ហាមលួចចម្លង ចែកចាយលក់បន្ត ឬយកទៅប្រើប្រាស់ក្រៅប្រព័ន្ធដោយគ្មានការអនុញ្ញាតជាលាយលក្ខណ៍អក្សរ។'
              : 'All lecture videos, syllabus materials, question banks, AI recommendations, and digital certificates hosted on SPI AI-ELMS are the intellectual property of Saint Paul Institute and respective faculty. Redistribution, copying, or commercial exploitation is strictly prohibited.'
            }}
          </p>
        </article>

        <!-- 4. Prohibited Uses -->
        <article class="p-6 sm:p-8 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-3">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 font-black text-sm flex items-center justify-center border border-blue-500/30">4</span>
            <h3 class="text-lg font-bold text-white">
              {{ currentLang === 'km' ? '៤. សកម្មភាពដែលត្រូវបានហាមឃាត់ (Prohibited Conduct)' : '4. Prohibited Conduct' }}
            </h3>
          </div>
          <ul class="text-xs sm:text-sm text-slate-400 space-y-1.5 list-disc list-inside">
            <li>{{ currentLang === 'km' ? 'ការប៉ុនប៉ង Hack, បំពានប្រព័ន្ធសុវត្ថិភាព ឬ Brute-force លើប្រព័ន្ធ' : 'Attempting to breach, probe, test vulnerabilities, or disrupt system security' }}</li>
            <li>{{ currentLang === 'km' ? 'ការក្លែងបន្លំពិន្ទុ កិច្ចការ ឬវិញ្ញាបនបត្រសិក្សា' : 'Fabricating, tampering with, or falsifying academic grades, quizzes, or certificates' }}</li>
            <li>{{ currentLang === 'km' ? 'ការបង្ហោះមាតិកាដែលខុសច្បាប់ មិនសមរម្យ ឬបង្កគ្រោះថ្នាក់' : 'Uploading malicious code, offensive content, or unauthorized copyrighted materials' }}</li>
          </ul>
        </article>

        <!-- 5. Termination of Access -->
        <article class="p-6 sm:p-8 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-3">
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 font-black text-sm flex items-center justify-center border border-blue-500/30">5</span>
            <h3 class="text-lg font-bold text-white">
              {{ currentLang === 'km' ? '៥. ការផ្អាក ឬលុបគណនី (Termination)' : '5. Account Suspension & Termination' }}
            </h3>
          </div>
          <p class="text-xs sm:text-sm text-slate-300">
            {{ currentLang === 'km'
              ? 'វិទ្យាស្ថាន សន្តប៉ូល រក្សាសិទ្ធិក្នុងការផ្អាក ឬបិទគណនីរបស់អ្នកប្រើប្រាស់ណាដែលបានបំពានលើលក្ខខណ្ឌប្រើប្រាស់ ឬបទបញ្ជាផ្ទៃក្នុងរបស់វិទ្យាស្ថានដោយពុំចាំបាច់ជូនដំណឹងជាមុន។'
              : 'Saint Paul Institute reserves the right to suspend or terminate platform access for any user found violating these terms, engaging in academic misconduct, or endangering system security.'
            }}
          </p>
        </article>

      </div>

      <!-- Bottom Quick Links Footer -->
      <footer class="mt-14 pt-8 border-t border-slate-800 text-center space-y-4">
        <div class="flex flex-wrap items-center justify-center gap-6 text-xs text-slate-400">
          <Link href="/privacy" class="hover:text-slate-200 transition-colors">{{ currentLang === 'km' ? 'គោលការណ៍ឯកជនភាព' : 'Privacy Policy' }}</Link>
          <span>•</span>
          <Link href="/terms" class="text-blue-400 font-bold hover:underline">{{ currentLang === 'km' ? 'លក្ខខណ្ឌប្រើប្រាស់' : 'Terms of Service' }}</Link>
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
