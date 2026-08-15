<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const showWhyModal = ref(false)
const whyContext = ref('')

const reviewQueue = ref([
  {
    id: 1,
    chapter: 'Chapter 2.2 — Data Types & Modifiers',
    status: '🔄 Review Needed',
    lastQuiz: '55% (Weak)',
    timeEst: '12 min',
    isWeak: true,
    reason: 'Your last quiz score was 55%. AI identified data type overflow concepts as a weak spot.'
  },
  {
    id: 2,
    chapter: 'Chapter 1.2 — Setup Environment & GCC Compiler',
    status: '🟡 Quick Refresh',
    lastQuiz: '70% (Fair)',
    timeEst: '8 min',
    isWeak: false,
    reason: 'It has been 14 days since you last revised compiler flags. A 8-minute refresh prevents memory decay.'
  }
])

const openWhy = (item: any) => {
  whyContext.value = item.reason
  showWhyModal.value = true
}
</script>

<template>
  <StudentLayout title="AI Learning Path — Review Queue">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-amber-950 via-slate-900 to-indigo-950 border border-amber-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold uppercase tracking-wider">
            🔁 REVISION ENGINE
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🔁 REVIEW LESSONS (Suggested Revision)</span>
            <span class="text-amber-400 font-mono text-sm">🕒</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ការពិនិត្យឡើងវិញសម្រាប់ផ្នែកដែលអ្នកខ្សោយ ឬមិនទាន់ចាំបានល្អ មុនបន្តទៅកម្រិតខ្ពស់
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Pending Reviews</p>
            <p class="text-base font-black text-amber-400">2 Chapters</p>
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
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-amber-600 text-white shadow-md"
        >
          🔄 2. Review Lessons
        </Link>
        <Link
          href="/student/ai-path/weak-topics"
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700"
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

      <!-- REVIEW QUEUE LIST CONTAINER (Matching Prompt Layout) -->
      <div class="bg-slate-800/90 border border-amber-500/40 rounded-3xl p-6 shadow-xl space-y-4">
        
        <div class="border-b border-slate-700/60 pb-3 flex items-center justify-between">
          <h3 class="text-sm font-bold text-white uppercase tracking-wider">REVIEW QUEUE</h3>
          <span class="text-xs text-slate-400 font-mono">Prioritized by score decay</span>
        </div>

        <!-- Queue Item List -->
        <div class="space-y-3">
          <div
            v-for="(item, idx) in reviewQueue"
            :key="item.id"
            class="p-5 bg-slate-900/90 rounded-2xl border border-slate-700/60 space-y-3 hover:border-amber-500/40 transition-all"
          >
            <div class="flex flex-wrap items-center justify-between gap-2">
              <h4 class="text-sm font-bold text-white">
                {{ idx + 1 }}) {{ item.chapter }}
              </h4>

              <div class="flex items-center gap-2">
                <span :class="[item.isWeak ? 'bg-rose-500/20 text-rose-300 border-rose-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30', 'px-2.5 py-0.5 rounded-full text-[11px] font-bold border']">
                  {{ item.status }}
                </span>
                <span class="text-xs text-slate-400 font-mono">Time Est.: {{ item.timeEst }}</span>
              </div>
            </div>

            <p class="text-xs text-slate-400">
              Last Quiz Score: <span :class="[item.isWeak ? 'text-rose-400 font-bold' : 'text-amber-300 font-bold']">{{ item.lastQuiz }}</span>
            </p>

            <div class="flex items-center gap-3 pt-1">
              <Link
                href="/student/learning-content/videos"
                class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-md"
              >
                ▶ Review Now
              </Link>
              <button
                @click="openWhy(item)"
                class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700"
              >
                📌 Why?
              </button>
            </div>
          </div>
        </div>

      </div>

      <!-- RULES EXPLANATION CARD -->
      <div class="p-5 bg-slate-900/80 border border-slate-800 rounded-3xl space-y-2 text-xs">
        <h4 class="font-bold text-amber-300 uppercase tracking-wider">🧠 AI Revision Logic Rules:</h4>
        <ul class="space-y-1 text-slate-300">
          <li>• ប្រសិនបើ Score មធ្យម/ទាប (&lt; 60%) → Review ត្រូវដាក់ក្នុង Queue អាទិភាពខ្ពស់</li>
          <li>• ប្រសិនបើ Score ខ្ពស់ (≥ 80%) → AI សម្គាល់ជា Mastered មិនបាច់ Review ឡើយ</li>
        </ul>
      </div>

      <!-- WHY MODAL -->
      <div v-if="showWhyModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-amber-500/40 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
          <div class="border-b border-slate-800 pb-2">
            <h3 class="text-base font-black text-white">📌 Why is this review suggested?</h3>
          </div>
          <p class="text-xs text-slate-200 leading-relaxed font-medium">
            {{ whyContext }}
          </p>
          <div class="flex justify-end pt-2">
            <button @click="showWhyModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
              Close
            </button>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
