<script setup lang="ts">
import { ref } from 'vue'
import type { StudentProgressProfile } from './types'

const props = defineProps<{
  profile: StudentProgressProfile
}>()

const emit = defineEmits<{
  (e: 'sendMessage', studentId: string): void
  (e: 'addFeedback', studentId: string): void
  (e: 'resetProgress', studentId: string): void
}>()

const selectedStudentId = ref(props.profile.id)
const selectedCourse = ref(props.profile.course)

const studentsList = [
  { id: 'STU24001', name: 'Chan Dara', major: 'IT & Networking' },
  { id: 'STU24002', name: 'Sok Chanra', major: 'Tourism Management' },
  { id: 'STU24003', name: 'Long Vichida', major: 'English Literature' },
  { id: 'STU24004', name: 'Pov Sreynich', major: 'Agronomy' },
]

const courseOptions = [
  'C Programming Basics',
  'Tourism Management 101',
  'English Literature',
  'Agronomy Basics',
]
</script>

<template>
  <div class="space-y-5 text-xs font-sans">
    <!-- Top Filter & Student Switcher Toolbar -->
    <div class="bg-[#0d1222]/90 border border-slate-700/60 p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-3 shadow-lg">
      <div class="flex items-center gap-3 w-full md:w-auto">
        <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/40 flex items-center justify-center text-purple-300 shadow-inner">
          <svg class="w-5 h-5 text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" fill="currentColor" fill-opacity="0.2" />
            <circle cx="12" cy="7" r="4" fill="currentColor" fill-opacity="0.3" />
          </svg>
        </div>
        <div>
          <h3 class="text-sm font-black text-white">Student Progress Profile</h3>
          <p class="text-xs text-slate-300 font-medium">Individual course tracking & chapter completion metrics</p>
        </div>
      </div>

      <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
        <div>
          <label class="text-[10px] text-slate-400 block mb-0.5 font-medium">Select Student:</label>
          <select v-model="selectedStudentId" class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs focus:border-purple-500 focus:outline-none">
            <option v-for="s in studentsList" :key="s.id" :value="s.id">{{ s.name }} ({{ s.id }})</option>
          </select>
        </div>
        <div>
          <label class="text-[10px] text-slate-400 block mb-0.5 font-medium">Course Scope:</label>
          <select v-model="selectedCourse" class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs focus:border-purple-500 focus:outline-none">
            <option v-for="c in courseOptions" :key="c" :value="c">{{ c }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Student Header Banner Card -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-700/60 pb-3.5">
        <div>
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" fill="currentColor" fill-opacity="0.2" />
            </svg>
            <span class="text-base font-black text-white">{{ profile.name }}</span>
            <span class="px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono text-[10px] font-bold border border-purple-500/30">ID: {{ profile.id }}</span>
          </div>
          <p class="text-slate-300 text-xs mt-0.5 font-medium">Major: <strong class="text-white">{{ profile.major }}</strong> · Target: <strong class="text-teal-300">{{ profile.course }}</strong></p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <div class="flex items-center gap-2">
            <button @click="emit('sendMessage', profile.id)" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl text-slate-200 font-semibold flex items-center gap-1.5 transition-all active:scale-95">
              <svg class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" fill="currentColor" fill-opacity="0.2" />
              </svg>
              <span>Send Message</span>
            </button>
            <button @click="emit('addFeedback', profile.id)" class="px-3.5 py-1.5 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-md shadow-purple-600/30 flex items-center gap-1.5 transition-all active:scale-95">
              <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" fill="currentColor" fill-opacity="0.2" />
              </svg>
              <span>Feedback</span>
            </button>
          </div>
          <div class="hidden sm:block h-6 w-px bg-slate-700/80 mx-1"></div>
          <button @click="emit('resetProgress', profile.id)" class="px-3.5 py-1.5 bg-slate-800/80 hover:bg-rose-950/60 border border-rose-500/30 hover:border-rose-500/60 text-rose-300 rounded-xl font-semibold flex items-center gap-1.5 transition-all active:scale-95" title="Reset student's progress filters and state">
            <svg class="w-4 h-4 text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Reset Progress</span>
          </button>
        </div>
      </div>

      <!-- Overall Course Progress Bar -->
      <div class="space-y-1.5 bg-[#121827] p-4 rounded-xl border border-slate-700/80">
        <div class="flex items-center justify-between text-xs font-bold">
          <span class="text-slate-200">OVERALL COURSE PROGRESS: {{ profile.course }}</span>
          <span class="text-purple-300 font-black text-sm">{{ profile.overall_progress }}% Complete</span>
        </div>
        <div class="w-full h-3 bg-slate-800 rounded-full overflow-hidden p-0.5 border border-slate-700">
          <div
            class="h-full bg-gradient-to-r from-purple-500 via-teal-400 to-emerald-400 rounded-full transition-all duration-500 shadow-sm"
            :style="{ width: profile.overall_progress + '%' }"
          ></div>
        </div>
      </div>

      <!-- 4 KPI Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-[#121827] p-3.5 rounded-xl border border-slate-700/80 space-y-1">
          <div class="flex items-center text-slate-400 text-[10px] font-semibold">
            <svg class="w-3.5 h-3.5 text-cyan-400 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="9" fill="currentColor" fill-opacity="0.2" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3" />
            </svg>
            <span>LEARNING TIME</span>
          </div>
          <p class="text-base font-black text-white">{{ profile.learning_time }}</p>
          <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-[10px] font-bold">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Active Studier
          </span>
        </div>

        <div class="bg-[#121827] p-3.5 rounded-xl border border-slate-700/80 space-y-1">
          <div class="flex items-center text-slate-400 text-[10px] font-semibold">
            <svg class="w-3.5 h-3.5 text-purple-400 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" fill="currentColor" fill-opacity="0.2" />
            </svg>
            <span>QUIZ AVERAGE</span>
          </div>
          <p class="text-base font-black text-purple-300">{{ profile.quiz_avg }}%</p>
          <span class="text-[10px] text-slate-300 font-medium">Passing Threshold: 70%</span>
        </div>

        <div class="bg-[#121827] p-3.5 rounded-xl border border-slate-700/80 space-y-1">
          <div class="flex items-center text-slate-400 text-[10px] font-semibold">
            <svg class="w-3.5 h-3.5 text-amber-400 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" fill="currentColor" fill-opacity="0.2" />
            </svg>
            <span>ASSIGNMENTS</span>
          </div>
          <p class="text-base font-black text-amber-300">{{ profile.assignments_submitted }} / {{ profile.assignments_total }} Submitted</p>
          <span class="text-[10px] text-amber-400 font-semibold">1 Pending Evaluation</span>
        </div>

        <div class="bg-[#121827] p-3.5 rounded-xl border border-slate-700/80 space-y-1">
          <div class="flex items-center text-slate-400 text-[10px] font-semibold">
            <svg class="w-3.5 h-3.5 text-yellow-400 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" fill="currentColor" fill-opacity="0.2" />
            </svg>
            <span>CERTIFICATE</span>
          </div>
          <p class="text-base font-black text-cyan-300">{{ profile.cert_status }}</p>
          <span class="text-[10px] text-cyan-400 font-semibold">⏳ Requires 100% Module 3</span>
        </div>
      </div>
    </div>

    <!-- Chapter Level Tracking Breakdown -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5 flex items-center gap-2.5">
        <div class="p-1.5 rounded-lg bg-purple-500/20 border border-purple-500/40 flex items-center justify-center shrink-0 shadow-inner">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
            <path d="M4 4C4 2.89543 4.89543 2 6 2H14L20 8V20C20 21.1046 19.1046 22 18 22H6C4.89543 22 4 21.1046 4 20V4Z" fill="url(#detailHeaderGrad)" />
            <path d="M14 2V8H20L14 2Z" fill="#C084FC" />
            <rect x="7" y="11" width="10" height="2" rx="1" fill="white" />
            <rect x="7" y="15" width="7" height="2" rx="1" fill="white" fill-opacity="0.8" />
            <defs>
              <linearGradient id="detailHeaderGrad" x1="4" y1="2" x2="20" y2="22" gradientUnits="userSpaceOnUse">
                <stop stop-color="#9333EA" />
                <stop offset="1" stop-color="#6B21A8" />
              </linearGradient>
            </defs>
          </svg>
        </div>
        <span>DETAIL TRACKING BY CHAPTER</span>
      </h4>

      <div class="space-y-3">
        <div v-for="m in profile.modules" :key="m.id" class="border border-slate-700/80 rounded-xl overflow-hidden bg-[#121827]">
          <!-- Module Header -->
          <div class="p-3 bg-slate-900/90 flex items-center justify-between border-b border-slate-700/80">
            <div class="flex items-center gap-2.5">
              <!-- Vibrant Flaticon 3D Folder Icon -->
              <svg class="w-5 h-5 shrink-0" viewBox="0 0 32 32" fill="none">
                <path d="M4 8C4 6.34315 5.34315 5 7 5H12.5858C13.3814 5 14.1444 5.31607 14.7071 5.87868L16.4142 7.58579C16.9768 8.1484 17.7398 8.46447 18.5355 8.46447H25C26.6569 8.46447 28 9.80761 28 11.4645V23C28 24.6569 26.6569 26 25 26H7C5.34315 26 4 24.6569 4 23V8Z" fill="url(#folderGrad)" />
                <path d="M4 12C4 10.3431 5.34315 9 7 9H25C26.6569 9 28 10.3431 28 12V23C28 24.6569 26.6569 26 25 26H7C5.34315 26 4 24.6569 4 23V12Z" fill="url(#folderFrontGrad)" />
                <defs>
                  <linearGradient id="folderGrad" x1="4" y1="5" x2="28" y2="26" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#D97706" />
                    <stop offset="1" stop-color="#B45309" />
                  </linearGradient>
                  <linearGradient id="folderFrontGrad" x1="4" y1="9" x2="28" y2="26" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FBBF24" />
                    <stop offset="1" stop-color="#F59E0B" />
                  </linearGradient>
                </defs>
              </svg>
              <span class="text-sm font-bold text-white">{{ m.title }}</span>
            </div>
            <span
              class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border shadow-sm"
              :class="
                m.status === 'Completed' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 
                (m.status === 'In Progress' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-slate-800 text-slate-400 border-slate-700')
              "
            >
              {{ m.status === 'Completed' ? '✓ 100% Done' : (m.status === 'In Progress' ? `${m.progress}% In-Progress` : 'Locked') }}
            </span>
          </div>

          <!-- Chapters List -->
          <div class="divide-y divide-slate-800/80">
            <div
              v-for="c in m.chapters"
              :key="c.id"
              class="p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-slate-800/40 transition-colors"
              :class="c.status === 'locked' ? 'opacity-50' : ''"
            >
              <div class="flex items-center gap-2.5">
                <!-- Completed Check Icon -->
                <svg v-if="c.status === 'completed'" class="w-4 h-4 shrink-0 shadow-sm" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" fill="url(#chkGrad)" />
                  <path d="M8 12L11 15L16 9" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                  <defs>
                    <linearGradient id="chkGrad" x1="2" y1="2" x2="22" y2="22" gradientUnits="userSpaceOnUse">
                      <stop stop-color="#10B981" />
                      <stop offset="1" stop-color="#059669" />
                    </linearGradient>
                  </defs>
                </svg>

                <!-- In-Progress Clock Icon -->
                <svg v-else-if="c.status === 'in_progress'" class="w-4 h-4 shrink-0 shadow-sm" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" fill="url(#progClockGrad)" />
                  <path d="M12 7V12L15 14" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  <defs>
                    <linearGradient id="progClockGrad" x1="2" y1="2" x2="22" y2="22" gradientUnits="userSpaceOnUse">
                      <stop stop-color="#F59E0B" />
                      <stop offset="1" stop-color="#D97706" />
                    </linearGradient>
                  </defs>
                </svg>

                <!-- Locked Padlock Icon -->
                <svg v-else class="w-4 h-4 shrink-0 shadow-sm" viewBox="0 0 24 24" fill="none">
                  <rect x="5" y="10" width="14" height="11" rx="2" fill="url(#lockBodyGrad)" />
                  <path d="M8 10V7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7V10" stroke="url(#lockShackleGrad)" stroke-width="2" stroke-linecap="round" />
                  <circle cx="12" cy="15.5" r="1.5" fill="#38BDF8" />
                  <defs>
                    <linearGradient id="lockBodyGrad" x1="5" y1="10" x2="19" y2="21" gradientUnits="userSpaceOnUse">
                      <stop stop-color="#475569" />
                      <stop offset="1" stop-color="#1E293B" />
                    </linearGradient>
                    <linearGradient id="lockShackleGrad" x1="8" y1="3" x2="16" y2="10" gradientUnits="userSpaceOnUse">
                      <stop stop-color="#94A3B8" />
                      <stop offset="1" stop-color="#64748B" />
                    </linearGradient>
                  </defs>
                </svg>

                <span class="font-semibold text-slate-200">{{ c.title }}</span>
              </div>

              <!-- Streamlined Status Badges with Flaticon Vector Icons & Tooltips -->
              <div v-if="c.status !== 'locked'" class="flex flex-wrap items-center gap-1.5 text-[11px]">
                <!-- Video Status Badge (Flaticon Style Colorful Camera Icon) -->
                <span
                  class="px-2.5 py-1 rounded-md border font-semibold flex items-center gap-1.5 transition-all shadow-sm"
                  :class="c.video_watched_percent === 100 ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30' : (c.video_watched_percent > 0 ? 'bg-amber-500/10 text-amber-300 border-amber-500/30' : 'bg-slate-800/80 text-slate-400 border-slate-700/60')"
                  :title="`Video: Watched ${c.video_watched_percent}%`"
                >
                  <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
                    <rect x="2" y="5" width="14" height="14" rx="3" fill="url(#videoBodyGrad)" />
                    <path d="M18 9.5L22 7V17L18 14.5V9.5Z" fill="url(#videoLensGrad)" />
                    <circle cx="9" cy="12" r="2.5" fill="white" fill-opacity="0.9" />
                    <polygon points="8.5,10.8 10.5,12 8.5,13.2" fill="#7C3AED" />
                    <defs>
                      <linearGradient id="videoBodyGrad" x1="2" y1="5" x2="16" y2="19" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#8B5CF6" />
                        <stop offset="1" stop-color="#6D28D9" />
                      </linearGradient>
                      <linearGradient id="videoLensGrad" x1="18" y1="7" x2="22" y2="17" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#A78BFA" />
                        <stop offset="1" stop-color="#7C3AED" />
                      </linearGradient>
                    </defs>
                  </svg>
                  <span>Video: {{ c.video_watched_percent }}%</span>
                </span>

                <!-- PDF Status Badge (Flaticon Style Colorful PDF Document Icon) -->
                <span
                  class="px-2.5 py-1 rounded-md border font-semibold flex items-center gap-1.5 transition-all shadow-sm"
                  :class="c.pdf_opened ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30' : 'bg-slate-800/80 text-slate-400 border-slate-700/60'"
                  :title="`PDF Document: ${c.pdf_opened ? 'Opened' : 'Not Opened'}`"
                >
                  <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
                    <path d="M4 4C4 2.89543 4.89543 2 6 2H14L20 8V20C20 21.1046 19.1046 22 18 22H6C4.89543 22 4 21.1046 4 20V4Z" fill="url(#pdfDocGrad)" />
                    <path d="M14 2V8H20L14 2Z" fill="#FCA5A5" />
                    <rect x="7" y="11" width="10" height="2" rx="1" fill="white" />
                    <rect x="7" y="15" width="7" height="2" rx="1" fill="white" fill-opacity="0.8" />
                    <defs>
                      <linearGradient id="pdfDocGrad" x1="4" y1="2" x2="20" y2="22" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#EF4444" />
                        <stop offset="1" stop-color="#B91C1C" />
                      </linearGradient>
                    </defs>
                  </svg>
                  <span>PDF: {{ c.pdf_opened ? 'Opened' : 'Unopened' }}</span>
                </span>

                <!-- Slide Status Badge (Flaticon Style Colorful Slide Deck Icon) -->
                <span
                  class="px-2.5 py-1 rounded-md border font-semibold flex items-center gap-1.5 transition-all shadow-sm"
                  :class="c.slide_read ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/10 text-amber-300 border-amber-500/30'"
                  :title="`Slide Deck: ${c.slide_read ? 'Read' : 'Unread'}`"
                >
                  <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
                    <rect x="2" y="3" width="20" height="14" rx="2" fill="url(#slideDeckGrad)" />
                    <path d="M12 17V21M8 21H16" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" />
                    <rect x="5" y="10" width="3" height="4" rx="1" fill="#34D399" />
                    <rect x="10" y="7" width="3" height="7" rx="1" fill="#FBBF24" />
                    <rect x="15" y="5" width="3" height="9" rx="1" fill="#F472B6" />
                    <defs>
                      <linearGradient id="slideDeckGrad" x1="2" y1="3" x2="22" y2="17" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#1E293B" />
                        <stop offset="1" stop-color="#0F172A" />
                      </linearGradient>
                    </defs>
                  </svg>
                  <span>Slide: {{ c.slide_read ? 'Read' : 'Unread' }}</span>
                </span>

                <!-- Quiz Status Badge (Flaticon Style Colorful Check Quiz Icon) -->
                <span
                  v-if="c.quiz_score !== undefined"
                  class="px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-300 border border-emerald-500/30 font-bold flex items-center gap-1.5 transition-all shadow-sm"
                  :title="`Quiz Score: ${c.quiz_score}%`"
                >
                  <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="3" width="18" height="18" rx="4" fill="url(#quizCheckGrad)" />
                    <path d="M7 12L10 15L17 8" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    <defs>
                      <linearGradient id="quizCheckGrad" x1="3" y1="3" x2="21" y2="21" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#10B981" />
                        <stop offset="1" stop-color="#047857" />
                      </linearGradient>
                    </defs>
                  </svg>
                  <span>Quiz: {{ c.quiz_score }}%</span>
                </span>
              </div>

              <!-- Locked State Indicator (Uncluttered) -->
              <div v-else class="text-[11px] text-slate-400 font-medium flex items-center gap-1">
                <span class="px-2.5 py-1 rounded bg-slate-800 text-slate-400 border border-slate-700/70 text-[10px] flex items-center gap-1.5 shadow-sm">
                  <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                    <rect x="5" y="10" width="14" height="11" rx="2" fill="#475569" />
                    <path d="M8 10V7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7V10" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" />
                  </svg>
                  <span>Locked Chapter</span>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
