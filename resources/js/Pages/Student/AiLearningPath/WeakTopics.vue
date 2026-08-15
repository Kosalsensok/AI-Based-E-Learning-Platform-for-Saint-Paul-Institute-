<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const showWeakModal = ref(false)

const weakTopicsList = ref([
  {
    id: 1,
    title: '🔴 Loops in C Programming',
    scoreAvg: '45%',
    target: 'bring to ≥ 60%',
    status: 'urgent',
    remedials: [
      'Re-watch Video: "Loops in C" (Chapter 3.1) 🔁',
      'Practice: "Loops Practice Quiz" (5 attempts max)'
    ]
  },
  {
    id: 2,
    title: '🟡 Functions & Scope',
    scoreAvg: '50%',
    target: 'bring to ≥ 60%',
    status: 'warning',
    remedials: [
      'PDF Cheat Sheet: Function Parameters & Return Types',
      'Practice Quiz: Functions & Scope (Unlimited)'
    ]
  }
])

const weakQuestionsList = ref([
  {
    q: 'Q3: What is the main risk of omitting loop counter increment in a while loop?',
    missed: 'Infinite Loop Crash',
    aiExplain: 'ដោយសារលក្ខខណ្ឌ while condition នៅតែ True ជានិច្ច ធ្វើឲ្យកូដដើរគ្មានទីបញ្ចប់។'
  },
  {
    q: 'Q7: What is the value of *p if p is a null pointer?',
    missed: 'Segmentation Fault / Runtime Crash',
    aiExplain: 'ការ Dereference NULL pointer នឹងបណ្តាលឲ្យ OS សម្លាប់ Process ភ្លាមៗ (Segfault)។'
  }
])
</script>

<template>
  <StudentLayout title="AI Learning Path — Weak Topics">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-rose-950 via-slate-900 to-indigo-950 border border-rose-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-bold uppercase tracking-wider">
            🎯 WEAK TOPIC DETECTOR
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🎯 WEAK TOPICS (Focus More)</span>
            <span class="text-rose-400 font-mono text-sm">⚠️</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ប្រធានបទដែលអ្នកធ្វើខុសច្រើនកាលពីប្រឡង Quiz — AI រកឃើញ និងរៀបចំ Remedial Action ភ្លាមៗ
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Identified Weak Topics</p>
            <p class="text-base font-black text-rose-400">2 Topics</p>
          </div>
        </div>
      </div>

      <!-- Sub-Tab Navigation Bar -->
      <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-700/60 custom-scrollbar">
        <Link
          href="/student/ai-path/recommended"
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700"
        >
          🎯 1. Recommended Lesson
        </Link>
        <Link
          href="/student/ai-path/review"
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700"
        >
          🔄 2. Review Lessons
        </Link>
        <Link
          href="/student/ai-path/weak-topics"
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-rose-600 text-white shadow-md"
        >
          ⚠️ 3. Weak Topics (<40%)
        </Link>
        <Link
          href="/student/ai-path/next-module"
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700"
        >
          ⏭️ 4. Next Module
        </Link>
        <Link
          href="/student/ai-path/next-course"
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700"
        >
          🗺️ 5. Next Course Roadmap
        </Link>
      </div>

      <!-- WEAK TOPICS LIST CONTAINER (Matching Prompt Layout) -->
      <div class="bg-slate-800/90 border border-rose-500/40 rounded-3xl p-6 shadow-xl space-y-4">
        <div class="border-b border-slate-700/60 pb-3 flex items-center justify-between">
          <h3 class="text-sm font-bold text-white uppercase tracking-wider">TOPICS IDENTIFIED BY AI FROM YOUR QUIZZES</h3>
          <span class="text-xs text-rose-300 font-bold">One-Click Remedial Active</span>
        </div>

        <!-- Topic Cards -->
        <div class="space-y-4">
          <div
            v-for="wt in weakTopicsList"
            :key="wt.id"
            class="p-5 bg-slate-900/90 rounded-2xl border border-slate-700/60 space-y-3 hover:border-rose-500/40 transition-all"
          >
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-800 pb-3">
              <h4 class="text-sm font-black text-white">{{ wt.title }}</h4>
              <div class="flex items-center gap-3 text-xs font-mono">
                <span class="text-rose-400 font-bold">Score Avg: {{ wt.scoreAvg }}</span>
                <span class="text-slate-400">Target: {{ wt.target }}</span>
              </div>
            </div>

            <!-- Recommended Remedial list -->
            <div class="space-y-1.5 text-xs text-slate-300">
              <p class="font-bold text-slate-400 uppercase text-[10px]">Recommended Remedial:</p>
              <ul class="space-y-1 pl-3 list-disc text-slate-200 font-medium">
                <li v-for="rem in wt.remedials" :key="rem">{{ rem }}</li>
              </ul>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-slate-800/60">
              <Link
                href="/student/quizzes/practice"
                class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-black text-xs shadow-md"
              >
                ▶ Fix Now
              </Link>
              <button
                @click="showWeakModal = true"
                class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700"
              >
                🧠 Show Weak Questions
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- SHOW WEAK QUESTIONS MODAL -->
      <div v-if="showWeakModal" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-rose-500/50 rounded-3xl max-w-xl w-full p-6 space-y-4 shadow-2xl">
          <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
            <h3 class="text-base font-black text-white">🧠 Questions You Missed in Recent Quizzes</h3>
            <button @click="showWeakModal = false" class="text-slate-400 hover:text-white font-bold text-sm">✕</button>
          </div>

          <div class="space-y-3 text-xs max-h-72 overflow-y-auto custom-scrollbar">
            <div v-for="wq in weakQuestionsList" :key="wq.q" class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1.5">
              <p class="font-bold text-white">{{ wq.q }}</p>
              <p class="text-rose-300">Key Concept Missed: <strong>{{ wq.missed }}</strong></p>
              <p class="text-slate-400 italic">🤖 AI Explanation: {{ wq.aiExplain }}</p>
            </div>
          </div>

          <div class="flex items-center justify-between pt-3 border-t border-slate-800">
            <button @click="showWeakModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
              Close
            </button>
            <Link href="/student/quizzes/practice" class="px-5 py-2 rounded-xl bg-rose-600 text-white font-bold text-xs shadow-md">
              ▶ Practice Missed Questions Now
            </Link>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
