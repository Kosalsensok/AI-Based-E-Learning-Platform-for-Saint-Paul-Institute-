<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const isHidden = ref(false)
const showLessonPlan = ref(false)

const recommendedData = ref({
  module: 'Module 2 (Variables & Operators)',
  chapter: 'Chapter 2.3 — Operators in C',
  difficulty: '⭐⭐☆☆☆',
  progress: 68,
  reason: 'Because your Practice Quiz score was 72%. AI customized this to reinforce your mastery.'
})

const checkpoints = ref([
  { id: 1, type: 'video', name: '🎬 Watch Video: "Operators in C"', duration: '12 min', status: 'completed' },
  { id: 2, type: 'pdf', name: '📄 Read PDF: "Operator Precedence Cheat Sheet"', duration: '5 min', status: 'current' },
  { id: 3, type: 'quiz', name: '🟩 Practice Quiz: "Operators Drill"', duration: '15 Qs', status: 'pending' }
])
</script>

<template>
  <StudentLayout title="AI Learning Path — Recommended Lesson">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-indigo-950 border border-emerald-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider">
            🤖 AI SMART ENGINE
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>✅ RECOMMENDED LESSON</span>
            <span class="text-amber-400 text-sm">⭐</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            មេរៀនដែល AI ណែនាំឲ្យរៀនភ្លាមៗ ដោយផ្អែកលើ Quiz Score និងល្បឿនសិក្សារបស់អ្នក
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Target Module Progress</p>
            <p class="text-base font-black text-emerald-400">{{ recommendedData.progress }}%</p>
          </div>
        </div>
      </div>

      <!-- Sub-Tab Navigation Bar -->
      <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-700/60 custom-scrollbar">
        <Link
          href="/student/ai-path/recommended"
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-emerald-600 text-white shadow-md"
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

      <!-- SVG AI PATHWAY ROADMAP VISUALIZATION -->
      <div class="bg-slate-900/90 border border-purple-500/30 rounded-3xl p-6 shadow-2xl space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-800 pb-3">
          <div class="flex items-center gap-2">
            <span class="text-xl">🗺️</span>
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">AI Learning Pathway & Milestone Map</h3>
          </div>
          <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
            Current Stage: Milestone 3 of 6
          </span>
        </div>

        <!-- Interactive SVG Roadmap -->
        <div class="relative py-4 overflow-x-auto">
          <div class="min-w-[640px] flex items-center justify-between relative px-6">
            <!-- Background connecting line -->
            <div class="absolute left-10 right-10 top-1/2 -translate-y-1/2 h-1.5 bg-slate-800 z-0"></div>
            <!-- Progress active line -->
            <div class="absolute left-10 w-[45%] top-1/2 -translate-y-1/2 h-1.5 bg-gradient-to-r from-emerald-500 via-teal-400 to-indigo-500 z-0"></div>

            <!-- Milestone 1 -->
            <div class="relative z-10 flex flex-col items-center gap-2">
              <div class="w-10 h-10 rounded-full bg-emerald-500 text-slate-950 font-bold flex items-center justify-center shadow-lg shadow-emerald-500/30 ring-4 ring-slate-900">
                ✓
              </div>
              <div class="text-center">
                <p class="text-xs font-bold text-emerald-400">Intro to C</p>
                <span class="text-[10px] text-slate-400">100% Score</span>
              </div>
            </div>

            <!-- Milestone 2 -->
            <div class="relative z-10 flex flex-col items-center gap-2">
              <div class="w-10 h-10 rounded-full bg-emerald-500 text-slate-950 font-bold flex items-center justify-center shadow-lg shadow-emerald-500/30 ring-4 ring-slate-900">
                ✓
              </div>
              <div class="text-center">
                <p class="text-xs font-bold text-emerald-400">Variables</p>
                <span class="text-[10px] text-slate-400">92% Score</span>
              </div>
            </div>

            <!-- Milestone 3 (Current AI Recommendation) -->
            <div class="relative z-10 flex flex-col items-center gap-2">
              <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-600 text-white font-bold flex items-center justify-center shadow-xl shadow-purple-500/50 ring-4 ring-purple-400/50 animate-bounce">
                🎯
              </div>
              <div class="text-center">
                <p class="text-xs font-extrabold text-purple-300">Operators in C</p>
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-300 font-bold border border-purple-500/30">AI Target</span>
              </div>
            </div>

            <!-- Milestone 4 -->
            <div class="relative z-10 flex flex-col items-center gap-2">
              <div class="w-10 h-10 rounded-full bg-slate-800 text-slate-400 font-bold flex items-center justify-center border border-slate-700 ring-4 ring-slate-900">
                4
              </div>
              <div class="text-center">
                <p class="text-xs font-bold text-slate-400">Control Flow</p>
                <span class="text-[10px] text-slate-500">Upcoming</span>
              </div>
            </div>

            <!-- Milestone 5 -->
            <div class="relative z-10 flex flex-col items-center gap-2">
              <div class="w-10 h-10 rounded-full bg-slate-800 text-slate-400 font-bold flex items-center justify-center border border-slate-700 ring-4 ring-slate-900">
                5
              </div>
              <div class="text-center">
                <p class="text-xs font-bold text-slate-400">Functions</p>
                <span class="text-[10px] text-slate-500">Upcoming</span>
              </div>
            </div>

            <!-- Milestone 6 -->
            <div class="relative z-10 flex flex-col items-center gap-2">
              <div class="w-10 h-10 rounded-full bg-slate-800 text-slate-400 font-bold flex items-center justify-center border border-slate-700 ring-4 ring-slate-900">
                🏆
              </div>
              <div class="text-center">
                <p class="text-xs font-bold text-slate-400">Final Project</p>
                <span class="text-[10px] text-slate-500">Certificate</span>
              </div>
            </div>
          </div>
        </div>

        <!-- AI Rule Thresholds Status Bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 pt-2 text-[11px] border-t border-slate-800">
          <div class="p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300">
            <span class="font-bold block">≥ 80% (Advance)</span>
            <span class="text-slate-400 text-[10px]">Unlock Next Module</span>
          </div>
          <div class="p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300">
            <span class="font-bold block">50% - 79% (Review)</span>
            <span class="text-slate-400 text-[10px]">AI Revision Scheduled</span>
          </div>
          <div class="p-2.5 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300">
            <span class="font-bold block">< 40% (Remedial)</span>
            <span class="text-slate-400 text-[10px]">Extra Practice Required</span>
          </div>
          <div class="p-2.5 rounded-xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-300">
            <span class="font-bold block">Idle &gt; 3 Days</span>
            <span class="text-slate-400 text-[10px]">Auto Re-engagement Alert</span>
          </div>
        </div>
      </div>

      <!-- MAIN RECOMMENDED CARD (Matching Prompt Layout) -->
      <div v-if="!isHidden" class="bg-slate-800/90 border border-emerald-500/40 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-700/60 pb-4">
          <div>
            <span class="px-3 py-1 rounded-full bg-emerald-600/20 text-emerald-300 font-bold text-xs border border-emerald-500/40">
              Module: {{ recommendedData.module }}
            </span>
            <h2 class="text-lg md:text-xl font-black text-white mt-2">
              {{ recommendedData.chapter }}
            </h2>
          </div>

          <div class="text-right">
            <p class="text-xs text-slate-400 font-bold">Difficulty: <span class="text-amber-300 font-mono">{{ recommendedData.difficulty }}</span></p>
            <p class="text-[10px] text-slate-400 mt-0.5">Based on your learning tempo</p>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="space-y-1.5">
          <div class="flex items-center justify-between text-xs font-bold">
            <span class="text-slate-300">Progress Now:</span>
            <span class="text-emerald-400 font-mono">{{ recommendedData.progress }}%</span>
          </div>
          <div class="w-full h-3 rounded-full bg-slate-950 overflow-hidden border border-slate-800">
            <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 rounded-full" :style="{ width: recommendedData.progress + '%' }"></div>
          </div>
        </div>

        <!-- Transparency Reason (Why this recommendation?) -->
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl space-y-1 text-xs">
          <h4 class="font-bold text-emerald-300 uppercase tracking-wider flex items-center gap-1.5">
            <span>💡</span>
            <span>Why this recommendation?</span>
          </h4>
          <p class="text-slate-200 leading-relaxed font-medium">
            → "{{ recommendedData.reason }}"
          </p>
        </div>

        <!-- Actions (User Control) -->
        <div class="flex flex-wrap items-center gap-3 pt-2">
          <Link
            href="/student/learning-content/videos"
            class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-lg transition-all hover:scale-105"
          >
            ▶ Start Learning →
          </Link>
          <button
            @click="showLessonPlan = !showLessonPlan"
            class="px-5 py-3 rounded-2xl bg-slate-900 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700"
          >
            🔎 {{ showLessonPlan ? 'Hide Lesson Plan' : 'View Lesson Plan' }}
          </button>
          <button
            @click="isHidden = true"
            class="px-4 py-3 rounded-2xl bg-slate-900 text-slate-400 hover:text-slate-200 font-bold text-xs border border-slate-700"
          >
            🙈 Hide This
          </button>
        </div>

        <!-- LESSON PLAN SEQUENCE DRAWER -->
        <div v-if="showLessonPlan" class="p-5 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
          <h4 class="text-xs font-bold text-white uppercase tracking-wider">📋 AI Recommended Checkpoint Sequence:</h4>
          <div class="space-y-2 text-xs">
            <div
              v-for="cp in checkpoints"
              :key="cp.id"
              class="p-3 bg-slate-900 rounded-xl border border-slate-800 flex items-center justify-between"
            >
              <div class="flex items-center gap-3">
                <span :class="[cp.status === 'completed' ? 'text-emerald-400 font-bold' : cp.status === 'current' ? 'text-amber-400 font-bold' : 'text-slate-500']">
                  {{ cp.status === 'completed' ? '✅' : cp.status === 'current' ? '🟡' : '⚪' }}
                </span>
                <span class="text-slate-200 font-bold">{{ cp.name }}</span>
              </div>
              <span class="text-slate-400 font-mono text-[11px]">{{ cp.duration }}</span>
            </div>
          </div>
        </div>

      </div>

      <!-- IF HIDDEN STATE -->
      <div v-else class="p-6 bg-slate-800/60 rounded-3xl border border-slate-700 text-center space-y-2">
        <p class="text-xs text-slate-400 font-bold">Recommended card hidden. AI engine continues tracking in background.</p>
        <button @click="isHidden = false" class="px-4 py-2 rounded-xl bg-slate-700 text-white text-xs font-bold">
          Restore Recommendation Card
        </button>
      </div>

    </div>
  </StudentLayout>
</template>
