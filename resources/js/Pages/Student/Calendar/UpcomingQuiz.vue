<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const quizList = ref([
  {
    id: 1,
    title: 'Practice Quiz: Loops Drill',
    timeLeft: '3h 20m left',
    course: 'C Programming Basics',
    type: 'Practice Quiz (Unlimited attempts)',
    status: '🔒 Available anytime before submit',
    isPostTest: false
  },
  {
    id: 2,
    title: 'Post-Test Module 2',
    timeLeft: 'Tomorrow 11:59 PM',
    course: 'C Programming Basics',
    type: 'Post-Test Evaluation',
    passingScore: '70%',
    isPostTest: true
  }
])
</script>

<template>
  <StudentLayout title="Calendar — Upcoming Quizzes">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-indigo-950 border border-emerald-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider">
            🟩 QUIZ DEADLINES &amp; SCHEDULE
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🟩 UPCOMING QUIZZES</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            កាលបរិច្ឆេទប្រឡង Practice Quiz និង Post-Test ត្រូវធ្វើឆាប់ៗ
          </p>
        </div>
      </div>

      <!-- UPCOMING QUIZZES LIST (Matching Prompt Spec Layout) -->
      <div class="space-y-4">
        <div
          v-for="q in quizList"
          :key="q.id"
          :class="[q.isPostTest ? 'border-rose-500/40' : 'border-emerald-500/40', 'bg-slate-800/90 rounded-3xl p-6 border shadow-xl space-y-4']"
        >
          <!-- Header -->
          <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <h3 class="text-base font-black text-white">{{ q.id }}) {{ q.title }}</h3>
            <span class="px-3 py-1 rounded-full bg-slate-900 font-mono font-bold text-xs text-emerald-400 border border-slate-700">
              ⏱ {{ q.timeLeft }}
            </span>
          </div>

          <!-- Body Details -->
          <div class="space-y-1.5 text-xs text-slate-300">
            <p>Course: <strong class="text-white">{{ q.course }}</strong></p>
            <p>Quiz Type: <span class="text-indigo-300 font-bold">{{ q.type }}</span></p>
            <p v-if="q.passingScore">Passing Score: <strong class="text-emerald-400 font-mono">{{ q.passingScore }}</strong></p>
            <p v-if="q.status" class="text-slate-400 font-mono">{{ q.status }}</p>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-700/60 text-xs">
            <Link v-if="!q.isPostTest" href="/student/quizzes/practice" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black shadow-md">
              ▶ Start Quiz →
            </Link>
            <Link v-else href="/student/quizzes/post-test" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-black shadow-md">
              🟥 View Quiz →
            </Link>

            <button class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 font-bold">
              📌 Set Deadline Reminder
            </button>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
