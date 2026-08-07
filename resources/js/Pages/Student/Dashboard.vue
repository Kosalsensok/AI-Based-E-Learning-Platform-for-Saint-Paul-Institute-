<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const props = defineProps<{
  enrollments?: any[]
  stats?: {
    enrolledCount?: number
    inProgressCount?: number
    completedCount?: number
    certificatesCount?: number
    learningTime?: string
    averageScore?: number
  }
}>()

const page = usePage<any>()
const user = computed(() => page.props.auth?.user || {})

const continueCourse = ref({
  title: 'C Programming Basics',
  chapter: 'Module 2: Operators',
  progress: 65,
  teacher: 'Mr. Sophea',
  lastAccessed: 'Today, 09:30 AM',
  href: '/student/courses'
})

const aiRecommendation = ref({
  title: 'Arrays in C',
  reason: 'Based on your performance in Operators quiz (72%)',
  difficulty: 2,
  href: '/student/ai-path'
})

const todayGoal = ref({
  target: 2,
  completed: 1,
  percentage: 50
})
</script>

<template>
  <StudentLayout title="Student Dashboard">
    <div class="space-y-6">
      <!-- Welcome Hero Banner -->
      <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 p-6 md:p-8 text-white shadow-2xl">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
          <div class="space-y-2 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-semibold border border-white/20">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
              <span>{{ user.major || 'IT & Networking' }} • Semester 2</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
              Welcome back, {{ user.name || 'Chan Dara' }}! 👋
            </h1>
            <p class="text-blue-100 text-xs md:text-sm leading-relaxed">
               Here is your learning overview for today. Continue your courses, practice quizzes, and follow your AI learning path easily.
            </p>
          </div>

          <div class="flex items-center gap-3 shrink-0">
            <Link
              href="/student/courses?tab=current"
              class="px-5 py-2.5 rounded-xl bg-white text-indigo-900 font-bold text-xs hover:bg-blue-50 transition-all shadow-lg hover:scale-105 active:scale-95 flex items-center gap-2"
            >
              <span>▶ Continue Learning</span>
            </Link>
            <Link
              href="/student/browse"
              class="px-4 py-2.5 rounded-xl bg-indigo-900/50 hover:bg-indigo-900/70 border border-white/20 text-white font-semibold text-xs transition-all backdrop-blur-md"
            >
              Explore Catalog
            </Link>
          </div>
        </div>
      </div>

      <!-- Stat Cards Grid (Matching Prompt Stats) -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 flex flex-col justify-between shadow-lg">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-xs font-medium">Enrolled</span>
            <span class="p-1.5 rounded-lg bg-blue-500/10 text-blue-400">📘</span>
          </div>
          <div class="mt-3">
            <p class="text-2xl font-extrabold text-white">{{ stats?.enrolledCount ?? 4 }}</p>
            <p class="text-[10px] text-slate-400">Total Courses</p>
          </div>
        </div>

        <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 flex flex-col justify-between shadow-lg">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-xs font-medium">In Progress</span>
            <span class="p-1.5 rounded-lg bg-amber-500/10 text-amber-400">🔄</span>
          </div>
          <div class="mt-3">
            <p class="text-2xl font-extrabold text-amber-400">{{ stats?.inProgressCount ?? 3 }}</p>
            <p class="text-[10px] text-slate-400">Active Courses</p>
          </div>
        </div>

        <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 flex flex-col justify-between shadow-lg">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-xs font-medium">Completed</span>
            <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400">✅</span>
          </div>
          <div class="mt-3">
            <p class="text-2xl font-extrabold text-emerald-400">{{ stats?.completedCount ?? 1 }}</p>
            <p class="text-[10px] text-slate-400">Finished Courses</p>
          </div>
        </div>

        <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 flex flex-col justify-between shadow-lg">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-xs font-medium">Certificates</span>
            <span class="p-1.5 rounded-lg bg-purple-500/10 text-purple-400">🏅</span>
          </div>
          <div class="mt-3">
            <p class="text-2xl font-extrabold text-purple-400">{{ stats?.certificatesCount ?? 2 }}</p>
            <p class="text-[10px] text-slate-400">Earned</p>
          </div>
        </div>

        <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 flex flex-col justify-between shadow-lg">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-xs font-medium">Learning Time</span>
            <span class="p-1.5 rounded-lg bg-cyan-500/10 text-cyan-400">⏱️</span>
          </div>
          <div class="mt-3">
            <p class="text-lg font-extrabold text-cyan-300">{{ stats?.learningTime ?? '28h 30m' }}</p>
            <p class="text-[10px] text-slate-400">Total Hours</p>
          </div>
        </div>

        <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 flex flex-col justify-between shadow-lg">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-xs font-medium">Avg Score</span>
            <span class="p-1.5 rounded-lg bg-indigo-500/10 text-indigo-400">📊</span>
          </div>
          <div class="mt-3">
            <p class="text-2xl font-extrabold text-indigo-300">{{ stats?.averageScore ?? 78 }}%</p>
            <p class="text-[10px] text-slate-400">Quiz Average</p>
          </div>
        </div>
      </div>

      <!-- Main Grid: Continue Learning, AI Recommendation & Today's Goal -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Columns: Continue Learning & AI Recommendation -->
        <div class="lg:col-span-2 space-y-6">
          
          <!-- Continue Learning Card -->
          <div class="bg-slate-800/80 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
              <div class="flex items-center gap-2">
                <span class="text-lg">📖</span>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Continue Learning</h3>
              </div>
              <span class="text-xs text-slate-400">Last accessed: {{ continueCourse.lastAccessed }}</span>
            </div>

            <div class="bg-slate-900/60 rounded-2xl p-4 border border-slate-700/40 flex flex-col md:flex-row md:items-center justify-between gap-4">
              <div class="space-y-2 flex-1">
                <div class="flex items-center gap-2">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                    Teacher-Led • Mr. Sophea
                  </span>
                </div>
                <h4 class="text-base font-bold text-white">{{ continueCourse.title }}</h4>
                <p class="text-xs text-slate-400">{{ continueCourse.chapter }}</p>

                <!-- Progress Bar -->
                <div class="space-y-1.5 pt-2">
                  <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400">Overall Progress</span>
                    <span class="font-bold text-indigo-400">{{ continueCourse.progress }}%</span>
                  </div>
                  <div class="w-full h-2 rounded-full bg-slate-700/60 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full" :style="{ width: continueCourse.progress + '%' }"></div>
                  </div>
                </div>
              </div>

              <Link
                :href="continueCourse.href"
                class="shrink-0 px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition-all shadow-md shadow-indigo-600/30 text-center"
              >
                Continue Lesson →
              </Link>
            </div>
          </div>

          <!-- AI Recommendation Card (Matching Module 1 Prompt Specs) -->
          <div class="bg-gradient-to-br from-indigo-900/40 via-purple-900/30 to-slate-800 border border-indigo-500/30 rounded-3xl p-6 shadow-xl space-y-4 relative overflow-hidden">
            <div class="flex items-center justify-between border-b border-indigo-500/20 pb-3">
              <div class="flex items-center gap-2">
                <span class="text-lg">🤖</span>
                <h3 class="text-sm font-bold text-indigo-300 uppercase tracking-wider">AI Recommendation</h3>
              </div>
              <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30">
                Personalized
              </span>
            </div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
              <div class="space-y-1.5">
                <p class="text-xs text-slate-300">Next Lesson For You:</p>
                <h4 class="text-lg font-extrabold text-white">{{ aiRecommendation.title }}</h4>
                <p class="text-xs text-slate-400">{{ aiRecommendation.reason }}</p>
                <div class="flex items-center gap-2 pt-1 text-xs">
                  <span class="text-slate-400">Difficulty:</span>
                  <span class="text-amber-400">⭐⭐☆☆☆</span>
                </div>
              </div>

              <Link
                :href="aiRecommendation.href"
                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg transition-all"
              >
                Start Learning →
              </Link>
            </div>
          </div>
        </div>

        <!-- Right Column: Today's Goal & Quick Actions -->
        <div class="space-y-6">
          
          <!-- Today's Goal Card -->
          <div class="bg-slate-800/80 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
              <div class="flex items-center gap-2">
                <span class="text-lg">🎯</span>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Today's Goal</h3>
              </div>
              <span class="text-xs font-bold text-emerald-400">{{ todayGoal.completed }} / {{ todayGoal.target }} Completed</span>
            </div>

            <div class="space-y-3">
              <p class="text-xs text-slate-300">Learn 2 lessons today</p>
              <div class="w-full h-3 rounded-full bg-slate-700/60 overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full" :style="{ width: todayGoal.percentage + '%' }"></div>
              </div>
              <p class="text-[11px] text-slate-400 text-right">50% completed</p>
            </div>
          </div>

          <!-- My Enrolled Courses Overview -->
          <div class="bg-slate-800/80 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
              <h3 class="text-sm font-bold text-white uppercase tracking-wider">My Courses Overview</h3>
              <Link href="/student/courses" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300">View All →</Link>
            </div>

            <div class="space-y-3">
              <div class="p-3.5 bg-slate-900/60 rounded-2xl border border-slate-700/40 flex items-center justify-between gap-3">
                <div class="min-w-0">
                  <p class="font-bold text-xs text-white truncate">📘 C Programming Basics</p>
                  <p class="text-[10px] text-slate-400">Mr. Sophea • $25 Paid</p>
                </div>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-500/20 text-emerald-400">65%</span>
              </div>

              <div class="p-3.5 bg-slate-900/60 rounded-2xl border border-slate-700/40 flex items-center justify-between gap-3">
                <div class="min-w-0">
                  <p class="font-bold text-xs text-white truncate">📗 Introduction to Networking</p>
                  <p class="text-[10px] text-slate-400">Mr. Vuthy • $20 Paid</p>
                </div>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-500/20 text-amber-400">40%</span>
              </div>

              <div class="p-3.5 bg-slate-900/60 rounded-2xl border border-slate-700/40 flex items-center justify-between gap-3 opacity-80">
                <div class="min-w-0">
                  <p class="font-bold text-xs text-white truncate">📙 Database Systems</p>
                  <p class="text-[10px] text-amber-400">⏳ Pending Payment ($25)</p>
                </div>
                <Link href="/student/payments" class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/30 hover:bg-amber-500/30">
                  Pay Now 🔒
                </Link>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </StudentLayout>
</template>
