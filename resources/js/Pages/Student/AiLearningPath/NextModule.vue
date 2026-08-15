<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const isUnlocked = ref(false)
const unlockProgress = ref(92)

const requirements = ref([
  { id: 1, name: 'Complete Chapter 2.3 (Operators in C)', done: true },
  { id: 2, name: 'Practice Quiz average score ≥ 60%', done: true },
  { id: 3, name: 'Post-Test Module 2 score ≥ 70%', done: false }
])

const simulateUnlock = () => {
  isUnlocked.value = true
  unlockProgress.value = 100
  requirements.value[2].done = true
}
</script>

<template>
  <StudentLayout title="AI Learning Path — Next Module">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 border border-blue-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold uppercase tracking-wider">
            ⏭️ MILESTONE UNLOCKER
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>⏭️ NEXT MODULE: Module 3 — Control Flow</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            តាមដានលក្ខខណ្ឌតម្រូវដើម្បីបើកសោរ Module បន្ទាប់តាម Completion Rules
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Unlock Status</p>
            <p :class="[isUnlocked ? 'text-emerald-400 font-black' : 'text-blue-400 font-black', 'text-base']">
              {{ isUnlocked ? '🔓 Unlocked' : '🔒 Locked (1 step away)' }}
            </p>
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
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700"
        >
          ⚠️ 3. Weak Topics (<40%)
        </Link>
        <Link
          href="/student/ai-path/next-module"
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-blue-600 text-white shadow-md"
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

      <!-- NEXT MODULE CARD (Matching Prompt Layout) -->
      <div class="bg-slate-800/90 border border-blue-500/40 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-700/60 pb-4">
          <div>
            <span class="px-3 py-1 rounded-full bg-blue-600/20 text-blue-300 font-bold text-xs border border-blue-500/40">
              Target Module: Module 3 — Control Flow
            </span>
            <h2 class="text-lg md:text-xl font-black text-white mt-2">
              Loops, Branching &amp; Conditionals Architecture
            </h2>
          </div>

          <span :class="[isUnlocked ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30', 'px-3.5 py-1.5 rounded-full text-xs font-black border']">
            {{ isUnlocked ? '🔓 UNLOCKED NOW' : '🔒 LOCKED (92% Ready)' }}
          </span>
        </div>

        <!-- Requirements Checklist -->
        <div class="space-y-3">
          <h4 class="text-xs font-bold text-white uppercase tracking-wider">Required to unlock:</h4>
          <div class="space-y-2 text-xs">
            <div
              v-for="req in requirements"
              :key="req.id"
              class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 flex items-center justify-between"
            >
              <div class="flex items-center gap-3">
                <span :class="[req.done ? 'text-emerald-400 font-bold' : 'text-slate-500 font-bold']">
                  {{ req.done ? '✅' : '⏳' }}
                </span>
                <span :class="[req.done ? 'text-slate-200 font-bold' : 'text-slate-400 font-medium']">
                  {{ req.name }}
                </span>
              </div>
              <span :class="[req.done ? 'text-emerald-400 font-bold' : 'text-amber-400 font-bold', 'text-[11px]']">
                {{ req.done ? 'Completed' : 'Pending' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Unlock Progress Bar -->
        <div class="space-y-1.5">
          <div class="flex items-center justify-between text-xs font-bold">
            <span class="text-slate-300">Progress toward unlock:</span>
            <span class="text-blue-400 font-mono">{{ unlockProgress }}%</span>
          </div>
          <div class="w-full h-3 rounded-full bg-slate-950 overflow-hidden border border-slate-800">
            <div class="h-full bg-gradient-to-r from-blue-500 to-cyan-400 rounded-full transition-all duration-500" :style="{ width: unlockProgress + '%' }"></div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-slate-700/60">
          <button
            v-if="!isUnlocked"
            @click="simulateUnlock"
            class="px-6 py-3 rounded-2xl bg-amber-600 hover:bg-amber-500 text-white font-black text-xs shadow-lg transition-all hover:scale-105"
          >
            🔓 Unlock Progress (Complete Step)
          </button>
          <Link
            href="/student/learning-content/videos"
            class="px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs shadow-lg transition-all hover:scale-105"
          >
            ▶ Continue Learning →
          </Link>
        </div>

      </div>

    </div>
  </StudentLayout>
</template>
