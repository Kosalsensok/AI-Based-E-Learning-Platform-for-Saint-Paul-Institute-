<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const showDetailModal = ref(false)

const overviewData = ref({
  course: 'C Programming Basics',
  overall: 72,
  status: '🟢 On Track (Keep going!)',
  completedChapters: 8,
  inProgressChapters: 3,
  lockedChapters: 2,
  modules: [
    { name: 'Module 1: Intro to C', status: '✅ Completed', percent: 100 },
    { name: 'Module 2: Variables & Data', status: '🔄 In Progress', percent: 65 },
    { name: 'Module 3: Control Flow', status: '🔒 Locked', percent: 10 }
  ]
})
</script>

<template>
  <StudentLayout title="Progress Tracking — Overview">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 border border-blue-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold uppercase tracking-wider">
            📈 OVERALL TRACKER
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>📌 PROGRESS OVERVIEW</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            សរុបវឌ្ឍនភាពសិក្សា Module &amp; Chapter តាមដាន status Completed, In Progress, Locked
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Overall Completion</p>
            <p class="text-base font-black text-emerald-400">{{ overviewData.overall }}%</p>
          </div>
        </div>
      </div>

      <!-- MAIN PROGRESS OVERVIEW CARD (Matching Prompt Spec Layout) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-700/60 pb-4">
          <div>
            <h2 class="text-lg font-black text-white">Course: {{ overviewData.course }}</h2>
            <p class="text-xs text-emerald-400 font-bold mt-1">Status: {{ overviewData.status }}</p>
          </div>

          <div class="text-right space-y-1">
            <p class="text-xs font-bold text-white">Overall Progress: {{ overviewData.overall }}%</p>
            <div class="w-36 h-2.5 rounded-full bg-slate-950 overflow-hidden border border-slate-800">
              <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-400 rounded-full" :style="{ width: overviewData.overall + '%' }"></div>
            </div>
          </div>
        </div>

        <!-- Chapter Counters Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
          <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl flex items-center justify-between">
            <span class="text-emerald-300 font-bold">✅ Completed Chapters</span>
            <span class="text-base font-black font-mono text-emerald-400">{{ overviewData.completedChapters }}</span>
          </div>
          <div class="p-4 bg-amber-500/10 border border-amber-500/30 rounded-2xl flex items-center justify-between">
            <span class="text-amber-300 font-bold">🔄 In Progress</span>
            <span class="text-base font-black font-mono text-amber-400">{{ overviewData.inProgressChapters }}</span>
          </div>
          <div class="p-4 bg-slate-900 border border-slate-700 rounded-2xl flex items-center justify-between">
            <span class="text-slate-400 font-bold">🔒 Locked Chapters</span>
            <span class="text-base font-black font-mono text-slate-400">{{ overviewData.lockedChapters }}</span>
          </div>
        </div>

        <!-- Module Progress List -->
        <div class="space-y-3 pt-2">
          <h3 class="text-xs font-bold text-white uppercase tracking-wider">Module Progress:</h3>
          <div class="space-y-2.5 text-xs">
            <div v-for="m in overviewData.modules" :key="m.name" class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-2">
              <div class="flex items-center justify-between font-bold">
                <span class="text-white">{{ m.name }}</span>
                <span :class="[m.percent === 100 ? 'text-emerald-400' : m.percent > 0 ? 'text-amber-400' : 'text-slate-500']">{{ m.status }} ({{ m.percent }}%)</span>
              </div>
              <div class="w-full h-2 rounded-full bg-slate-900 overflow-hidden border border-slate-800">
                <div :class="[m.percent === 100 ? 'bg-emerald-500' : m.percent > 0 ? 'bg-amber-500' : 'bg-slate-700', 'h-full rounded-full']" :style="{ width: m.percent + '%' }"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-700/60">
          <button @click="showDetailModal = true" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700">
            ↗ View Detail Table
          </button>
          <Link href="/student/learning-content/videos" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md">
            ▶ Continue Learning →
          </Link>
        </div>

      </div>

      <!-- VIEW DETAIL MODAL -->
      <div v-if="showDetailModal" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-blue-500/40 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
          <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
            <h3 class="text-base font-black text-white">📌 Detailed Chapter Completion</h3>
            <button @click="showDetailModal = false" class="text-slate-400 hover:text-white font-bold text-sm">✕</button>
          </div>

          <div class="space-y-2 text-xs max-h-64 overflow-y-auto custom-scrollbar">
            <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 flex items-center justify-between">
              <span class="text-slate-200">Ch 1.1 — History &amp; Syntax</span>
              <span class="text-emerald-400 font-bold">✅ 100%</span>
            </div>
            <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 flex items-center justify-between">
              <span class="text-slate-200">Ch 2.1 — Data Types</span>
              <span class="text-emerald-400 font-bold">✅ 100%</span>
            </div>
            <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 flex items-center justify-between">
              <span class="text-slate-200">Ch 2.2 — Operators</span>
              <span class="text-amber-400 font-bold">🔄 65%</span>
            </div>
            <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 flex items-center justify-between">
              <span class="text-slate-400">Ch 3.1 — Loops</span>
              <span class="text-slate-500 font-bold">🔒 10%</span>
            </div>
          </div>

          <div class="flex justify-end pt-2">
            <button @click="showDetailModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
              Close
            </button>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
