<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

defineProps<{
  activeTab: 'all' | 'subjects' | 'teacher-assignments' | 'teacher-led' | 'self-study' | 'free' | 'paid'
  summaryStats?: {
    total_courses?: number
    teacher_led_count?: number
    self_study_count?: number
    paid_count?: number
    free_count?: number
    published_count?: number
    draft_count?: number
    total_subjects?: number
  }
}>()
</script>

<template>
  <div class="bg-slate-900/60 p-3.5 rounded-2xl border border-slate-800/80 backdrop-blur-md space-y-2.5 shadow-sm">
    <!-- TOP ROW: TITLE & COMPACT QUICK STATS -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
      <!-- Title & Module Icon -->
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 shadow-sm">
          <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-xs font-bold text-slate-100 tracking-wide uppercase">COURSE & SUBJECT MANAGEMENT</h1>
            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-sky-500/10 text-sky-300 border border-sky-500/20">
              Admin Core
            </span>
          </div>
          <p class="text-[11px] text-slate-300 mt-0.5 font-medium">គ្រប់គ្រងមុខវិជ្ជា / វគ្គសិក្សា / គ្រូបង្រៀន / តម្លៃ / ប្រភេទការរៀន</p>
        </div>
      </div>

      <!-- Quick Summary Badges (Compact, High Contrast & Clean Flaticon SVGs) -->
      <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 lg:pb-0 font-sans text-xs whitespace-nowrap scrollbar-none">
        <div class="px-2.5 py-1 rounded-lg bg-slate-800/60 border border-slate-700/50 flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          <span class="text-slate-300 font-medium">Total:</span>
          <span class="font-bold text-white">{{ summaryStats?.total_courses ?? 328 }}</span>
        </div>

        <div class="px-2.5 py-1 rounded-lg bg-indigo-500/15 border border-indigo-500/30 flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
          </svg>
          <span class="text-indigo-300 font-semibold">Teacher-Led:</span>
          <span class="font-bold text-indigo-200">{{ summaryStats?.teacher_led_count ?? 185 }}</span>
        </div>

        <div class="px-2.5 py-1 rounded-lg bg-sky-500/15 border border-sky-500/30 flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <span class="text-sky-300 font-semibold">Self-Study:</span>
          <span class="font-bold text-sky-200">{{ summaryStats?.self_study_count ?? 143 }}</span>
        </div>

        <div class="px-2.5 py-1 rounded-lg bg-emerald-500/15 border border-emerald-500/30 flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <span class="text-emerald-300 font-semibold">Paid:</span>
          <span class="font-bold text-emerald-200">{{ summaryStats?.paid_count ?? 220 }}</span>
        </div>

        <div class="px-2.5 py-1 rounded-lg bg-amber-500/15 border border-amber-500/30 flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V6a2 2 0 10-2 2h2zm0 13C10.832 19.477 9.246 19 7.5 19S4.168 19.477 3 20.253V7.253C4.168 6.477 5.754 6 7.5 6s3.332.477 4.5 1.253m0 13C13.168 19.477 14.754 19 16.5 19c1.747 0 3.332.477 4.5 1.253V7.253C19.832 6.477 18.247 6 16.5 6c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
          <span class="text-amber-300 font-semibold">Free:</span>
          <span class="font-bold text-amber-200">{{ summaryStats?.free_count ?? 108 }}</span>
        </div>
      </div>
    </div>

    <!-- BOTTOM ROW: CLEAN 3 MAIN NAVIGATION TABS -->
    <div class="flex items-center justify-between gap-3 pt-0.5 border-t border-slate-800/60">
      <div class="inline-flex items-center gap-1 p-1 bg-slate-950/70 border border-slate-800/80 rounded-xl font-sans text-xs whitespace-nowrap">
        <Link
          href="/admin/course-module/all"
          class="py-1.5 px-3.5 rounded-lg font-bold transition-all flex items-center gap-2"
          :class="activeTab === 'all' || activeTab === 'teacher-led' || activeTab === 'self-study' || activeTab === 'free' || activeTab === 'paid'
            ? 'bg-slate-800 text-sky-300 border border-slate-700/60 shadow-sm'
            : 'text-slate-300 hover:text-white hover:bg-slate-900/60'"
        >
          <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
          </svg>
          <span>All Courses</span>
        </Link>

        <Link
          href="/admin/course-module/subjects"
          class="py-1.5 px-3.5 rounded-lg font-bold transition-all flex items-center gap-2"
          :class="activeTab === 'subjects'
            ? 'bg-slate-800 text-sky-300 border border-slate-700/60 shadow-sm'
            : 'text-slate-300 hover:text-white hover:bg-slate-900/60'"
        >
          <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
          <span>Subjects</span>
        </Link>

        <Link
          href="/admin/course-module/teacher-assignments"
          class="py-1.5 px-3.5 rounded-lg font-bold transition-all flex items-center gap-2"
          :class="activeTab === 'teacher-assignments'
            ? 'bg-slate-800 text-sky-300 border border-slate-700/60 shadow-sm'
            : 'text-slate-300 hover:text-white hover:bg-slate-900/60'"
        >
          <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span>Teacher Assignment</span>
        </Link>
      </div>
    </div>
  </div>
</template>

