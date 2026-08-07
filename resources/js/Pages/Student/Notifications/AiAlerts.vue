<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const aiAlertsList = ref([
  {
    id: 1,
    type: 'weak_topic',
    title: '🤖 AI Alert: Focus More — Loops',
    badge: '🔴 Weak Topic',
    score: '45%',
    time: '12 minutes',
    remedialWatch: '“Loops in C” (Chapter 3.1)',
    remedialQuiz: '“Loops Drill” (Unlimited)',
    hidden: false
  },
  {
    id: 2,
    type: 'idle',
    title: '🤖 AI Alert: We miss you 👋 (Idle 3 days)',
    badge: '🟡 Reminder',
    details: 'You have 2 chapters left to complete Module 2.',
    hidden: false
  }
])

const hideAlert = (item: any) => {
  item.hidden = true
}
</script>

<template>
  <StudentLayout title="Notifications — AI Alerts">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-purple-950 via-slate-900 to-indigo-950 border border-purple-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold uppercase tracking-wider">
            🤖 SMART AI RECOMMENDATIONS
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🤖 AI ALERTS</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ការដាស់តឿនឆ្លាតវៃពី AI (Weak Topics, Idle days, ណែនាំមេរៀនត្រូវរំលឹកឡើងវិញ)
          </p>
        </div>
      </div>

      <!-- AI ALERTS CARDS (Matching Prompt Spec Layout) -->
      <div class="space-y-4">
        <template v-for="item in aiAlertsList" :key="item.id">
          <div
            v-if="!item.hidden"
            class="bg-slate-800/90 border border-purple-500/40 rounded-3xl p-6 shadow-xl space-y-4"
          >
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
              <h3 class="text-base font-black text-white">{{ item.title }}</h3>
              <span :class="[item.type === 'weak_topic' ? 'bg-rose-500/20 text-rose-300 border-rose-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30', 'px-3 py-1 rounded-full font-bold text-xs border']">
                {{ item.badge }}
              </span>
            </div>

            <!-- Content for Weak Topic -->
            <div v-if="item.type === 'weak_topic'" class="space-y-2 text-xs text-slate-300">
              <p>Based on your Practice Quiz score: <strong class="text-rose-400 font-bold">{{ item.score }}</strong> · Recommended time: <span class="text-purple-400 font-bold font-mono">{{ item.time }}</span></p>
              <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                <span class="font-bold text-white uppercase text-[10px]">Recommended Remedial:</span>
                <p>• Re-watch: <span class="text-indigo-300 font-bold">{{ item.remedialWatch }}</span></p>
                <p>• Practice Quiz: <span class="text-emerald-400 font-bold">{{ item.remedialQuiz }}</span></p>
              </div>
            </div>

            <!-- Content for Idle -->
            <div v-else class="space-y-2 text-xs text-slate-300">
              <p class="text-slate-200 font-bold">{{ item.details }}</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-700/60 text-xs">
              <template v-if="item.type === 'weak_topic'">
                <Link href="/student/ai-path/review" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold shadow-md">
                  🔁 Review Lesson
                </Link>
                <Link href="/student/quizzes/practice" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold shadow-md">
                  🟩 Practice Now
                </Link>
              </template>
              <template v-else>
                <Link href="/student/learning-content/videos" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-md">
                  ▶ Continue
                </Link>
                <button class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 font-bold">
                  📌 Set Reminder
                </button>
              </template>
              <button @click="hideAlert(item)" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-400 font-bold">
                🙈 Dismiss
              </button>
            </div>
          </div>
        </template>
      </div>

    </div>
  </StudentLayout>
</template>
