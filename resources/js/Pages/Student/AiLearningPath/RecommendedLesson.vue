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
