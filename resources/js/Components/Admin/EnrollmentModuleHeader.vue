<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

const props = withDefaults(defineProps<{
  activeTab: 'majors' | 'courses' | 'single' | 'bulk' | 'history'
  summaryStats?: {
    totalStudents?: number
    totalCourseEnrolls?: number
    paidEnrolls?: number
    pendingEnrolls?: number
    freeEnrolls?: number
  }
}>(), {
  activeTab: 'majors',
  summaryStats: () => ({
    totalStudents: 2458,
    totalCourseEnrolls: 4250,
    paidEnrolls: 3120,
    pendingEnrolls: 480,
    freeEnrolls: 650
  })
})
</script>

<template>
  <div class="space-y-3 font-sans">
    <!-- TOP COMPACT HEADER TITLE -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-900/60 p-3.5 rounded-2xl border border-slate-800 backdrop-blur-xl shadow-lg">
      <div class="flex items-center gap-3">
        <!-- SOFT VECTOR ICON CONTAINER -->
        <div class="w-9 h-9 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shadow-sm flex-shrink-0">
          <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
          </svg>
        </div>

        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-sm font-extrabold text-white tracking-wide uppercase">ENROLLMENT MANAGEMENT</h1>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 tracking-wider">
              Official Module
            </span>
          </div>
          <p class="text-[11px] text-slate-300 mt-0.5 font-medium">
            គ្រប់គ្រងការចុះឈ្មោះសិស្សចូល Major (Admission) និងមុខវិជ្ជានីមួយៗ (Semester Courses + ABA Payment)
          </p>
        </div>
      </div>

      <!-- QUICK STAT BADGES (SOFT GLASS CARDS) -->
      <div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0">
        <div class="px-3 py-1.5 rounded-xl bg-slate-800/60 border border-slate-700/60 text-center min-w-[95px] backdrop-blur-md">
          <div class="text-[10px] font-medium text-slate-300 uppercase flex items-center justify-center gap-1">
            <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
            </svg>
            <span>Major Enrolled</span>
          </div>
          <div class="text-xs font-bold text-emerald-300 font-sans mt-0.5">{{ summaryStats?.totalStudents || 2458 }}</div>
        </div>

        <div class="px-3 py-1.5 rounded-xl bg-slate-800/60 border border-slate-700/60 text-center min-w-[95px] backdrop-blur-md">
          <div class="text-[10px] font-medium text-slate-300 uppercase flex items-center justify-center gap-1">
            <svg class="w-3 h-3 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span>Course Enrolls</span>
          </div>
          <div class="text-xs font-bold text-sky-300 font-sans mt-0.5">{{ summaryStats?.totalCourseEnrolls || 4250 }}</div>
        </div>

        <div class="px-3 py-1.5 rounded-xl bg-slate-800/60 border border-slate-700/60 text-center min-w-[95px] backdrop-blur-md">
          <div class="text-[10px] font-medium text-slate-300 uppercase flex items-center justify-center gap-1">
            <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>Paid ABA</span>
          </div>
          <div class="text-xs font-bold text-amber-300 font-sans mt-0.5">{{ summaryStats?.paidEnrolls || 3120 }}</div>
        </div>
      </div>
    </div>

    <!-- CLEAN INLINE TAB NAVIGATION BAR -->
    <div class="flex items-center gap-1.5 overflow-x-auto p-1.5 bg-slate-900/60 border border-slate-800 rounded-2xl backdrop-blur-xl">
      <Link
        href="/admin/enrollment/majors"
        :class="[
          'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap inline-flex items-center gap-1.5',
          activeTab === 'majors'
            ? 'bg-emerald-600/30 text-emerald-300 border border-emerald-500/40 shadow-sm'
            : 'text-slate-300 hover:text-white hover:bg-slate-800/60'
        ]"
      >
        <svg class="w-3.5 h-3.5" :class="activeTab === 'majors' ? 'text-emerald-400' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
        </svg>
        <span>Major Enrollments</span>
      </Link>

      <Link
        href="/admin/enrollment/courses"
        :class="[
          'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap inline-flex items-center gap-1.5',
          activeTab === 'courses'
            ? 'bg-sky-600/30 text-sky-300 border border-sky-500/40 shadow-sm'
            : 'text-slate-300 hover:text-white hover:bg-slate-800/60'
        ]"
      >
        <svg class="w-3.5 h-3.5" :class="activeTab === 'courses' ? 'text-sky-400' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <span>Course Enrollments</span>
      </Link>

      <Link
        href="/admin/enrollment/single"
        :class="[
          'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap inline-flex items-center gap-1.5',
          activeTab === 'single'
            ? 'bg-indigo-600/30 text-indigo-300 border border-indigo-500/40 shadow-sm'
            : 'text-slate-300 hover:text-white hover:bg-slate-800/60'
        ]"
      >
        <svg class="w-3.5 h-3.5" :class="activeTab === 'single' ? 'text-indigo-400' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <span>Single Enrollment</span>
      </Link>

      <Link
        href="/admin/enrollment/bulk"
        :class="[
          'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap inline-flex items-center gap-1.5',
          activeTab === 'bulk'
            ? 'bg-purple-600/30 text-purple-300 border border-purple-500/40 shadow-sm'
            : 'text-slate-300 hover:text-white hover:bg-slate-800/60'
        ]"
      >
        <svg class="w-3.5 h-3.5" :class="activeTab === 'bulk' ? 'text-purple-400' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <span>Bulk Enrollment</span>
      </Link>

      <Link
        href="/admin/enrollment/history"
        :class="[
          'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap inline-flex items-center gap-1.5',
          activeTab === 'history'
            ? 'bg-amber-600/30 text-amber-300 border border-amber-500/40 shadow-sm'
            : 'text-slate-300 hover:text-white hover:bg-slate-800/60'
        ]"
      >
        <svg class="w-3.5 h-3.5" :class="activeTab === 'history' ? 'text-amber-400' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Enrollment History</span>
      </Link>
    </div>
  </div>
</template>
