<script setup lang="ts">
import TeacherLayout from '@/Layouts/TeacherLayout.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps<{
  teacherInfo: {
    name: string
    title: string
    department: string
    status: string
  }
  stats: {
    total_courses: number
    total_students: number
    active_students: number
    pending_tasks: number
  }
  recent_courses: Array<any>
  recentActivities: Array<{
    id: number
    type: string
    title: string
    time: string
    icon: string
  }>
  completionTrend: {
    labels: string[]
    data: number[]
  }
}>()
</script>

<template>
  <TeacherLayout title="Dashboard">
    <div class="space-y-6">
      <!-- Banner / Welcome Header -->
      <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 rounded-2xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-medium mb-3">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
              <span>{{ teacherInfo?.status || 'Online' }} • {{ teacherInfo?.department || 'IT & Networking' }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
              Welcome back, {{ teacherInfo?.name || 'Mr. Sophea' }} 👋
            </h1>
            <p class="text-blue-100 text-sm mt-1 max-w-xl">
              Here is your teaching activity overview for today. Manage your courses, track student performance, and create content easily.
            </p>
          </div>

          <div class="flex items-center gap-3">
            <Link
              href="/teacher/courses"
              class="px-4 py-2.5 bg-white text-blue-700 hover:bg-blue-50 font-bold rounded-xl text-sm shadow-md transition flex items-center gap-2"
            >
              <i class="pi pi-plus text-xs"></i>
              <span>+ Create Course</span>
            </Link>
            <Link
              href="/teacher/content"
              class="px-4 py-2.5 bg-blue-600/60 hover:bg-blue-600 border border-white/20 text-white font-semibold rounded-xl text-sm transition"
            >
              Upload Content
            </Link>
          </div>
        </div>
      </div>

      <!-- Stat Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- My Courses -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 flex items-center justify-between hover:shadow-md transition">
          <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">📚 My Courses</p>
            <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ stats.total_courses }}</h3>
            <p class="text-xs text-emerald-600 font-medium mt-1">Active teaching courses</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl font-bold">
            <i class="pi pi-book"></i>
          </div>
        </div>

        <!-- My Students -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 flex items-center justify-between hover:shadow-md transition">
          <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">👥 My Students</p>
            <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ stats.total_students }}</h3>
            <p class="text-xs text-blue-600 font-medium mt-1">Total enrolled learners</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl font-bold">
            <i class="pi pi-users"></i>
          </div>
        </div>

        <!-- Active Students -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 flex items-center justify-between hover:shadow-md transition">
          <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">✅ Active Students</p>
            <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ stats.active_students }}</h3>
            <p class="text-xs text-emerald-600 font-medium mt-1">Active this week (74%)</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl font-bold">
            <i class="pi pi-user-check"></i>
          </div>
        </div>

        <!-- Pending Tasks -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 flex items-center justify-between hover:shadow-md transition">
          <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">⏳ Pending Tasks</p>
            <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1">{{ stats.pending_tasks }}</h3>
            <p class="text-xs text-amber-600 font-medium mt-1">Quizzes to grade / Questions</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl font-bold">
            <i class="pi pi-clock"></i>
          </div>
        </div>
      </div>

      <!-- Main Overview Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Progress Overview Graph (2 Columns) -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="font-bold text-slate-800 dark:text-white text-base">📈 Course Completion Trend</h3>
              <p class="text-xs text-slate-500">Weekly student lesson completion progress</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300 rounded-lg">
              This Week
            </span>
          </div>

          <!-- Trend Bar Graph / Progress Bars -->
          <div class="space-y-4 py-2">
            <div v-for="(val, idx) in completionTrend?.data || [45, 52, 58, 65, 72, 80, 85]" :key="idx" class="space-y-1.5">
              <div class="flex justify-between text-xs font-semibold text-slate-600 dark:text-slate-300">
                <span>{{ completionTrend?.labels[idx] || `Day ${idx+1}` }}</span>
                <span>{{ val }}% Average Completion</span>
              </div>
              <div class="w-full bg-slate-100 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                <div
                  class="bg-gradient-to-r from-blue-600 to-indigo-500 h-3 rounded-full transition-all duration-500"
                  :style="{ width: val + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activities Feed (1 Column) -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-800 dark:text-white text-base">🔔 Recent Activities</h3>
            <span class="text-xs text-blue-600 font-semibold cursor-pointer hover:underline">View All</span>
          </div>

          <div class="space-y-4">
            <div
              v-for="act in recentActivities"
              :key="act.id"
              class="flex items-start gap-3.5 p-3 rounded-xl bg-slate-50 dark:bg-gray-700/40 border border-slate-100 dark:border-gray-700/50"
            >
              <div class="w-9 h-9 rounded-xl bg-white dark:bg-gray-700 shadow-sm flex items-center justify-center flex-shrink-0">
                <i :class="act.icon"></i>
              </div>
              <div class="flex-1 text-xs">
                <p class="font-semibold text-slate-800 dark:text-slate-100">{{ act.title }}</p>
                <p class="text-slate-400 mt-0.5">{{ act.time }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </TeacherLayout>
</template>
