<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const badgeList = ref([
  { id: 1, name: 'Completed Streak (7 days)', icon: '🥇', status: 'Active', tier: 'Gold', active: true },
  { id: 2, name: 'Quiz Master (Score ≥ 80% x3)', icon: '🥈', status: 'Achieved', tier: 'Silver', active: true },
  { id: 3, name: 'Consistent Learner (≥ 5h/week)', icon: '🥉', status: 'Progressing', tier: 'Bronze', active: false },
  { id: 4, name: 'Weak Topic Fixer (Improve +15%)', icon: '⭐', status: 'Earnable', tier: 'Special', active: false }
])

const nextGoal = ref({
  topic: 'Loops in C',
  currentScore: '45%',
  targetScore: '60% in next practice quiz'
})
</script>

<template>
  <StudentLayout title="Progress Tracking — Achievements & Badges">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-amber-950 via-slate-900 to-indigo-950 border border-amber-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold uppercase tracking-wider">
            🏅 GAMIFICATION BADGES
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🏅 ACHIEVEMENTS &amp; BADGES</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            មេដាយលើកទឹកចិត្តសម្រាប់ការខិតខំរៀនសូត្រ ប្រឡងបានពិន្ទុខ្ពស់ និងរំលឹកមេរៀនជាប់លាប់
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Badges Unlocked</p>
            <p class="text-base font-black text-amber-400">2 / 4 Unlocked</p>
          </div>
        </div>
      </div>

      <!-- MAIN BADGE GRID (Matching Prompt Spec Layout) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        
        <div class="border-b border-slate-700/60 pb-3 flex items-center justify-between">
          <h2 class="text-base font-black text-white">MY EARNED BADGES</h2>
          <span class="text-xs text-amber-300 font-bold">2 Earned • 2 In Progress</span>
        </div>

        <!-- Badges Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div
            v-for="b in badgeList"
            :key="b.id"
            :class="[
              b.active ? 'bg-slate-950 border-amber-500/40 shadow-xl' : 'bg-slate-950/60 border-slate-800 opacity-65',
              'p-5 rounded-2xl border flex items-center gap-4 transition-all'
            ]"
          >
            <span class="text-3xl p-3 rounded-2xl bg-slate-900 border border-slate-800">{{ b.icon }}</span>
            <div class="space-y-0.5">
              <h3 class="font-bold text-white text-xs">{{ b.name }}</h3>
              <p class="text-[10px] text-slate-400">Tier: {{ b.tier }}</p>
              <span :class="[b.active ? 'text-emerald-400 font-bold' : 'text-amber-400 font-bold', 'text-[11px] block pt-1']">
                {{ b.status }}
              </span>
            </div>
          </div>
        </div>

        <!-- NEXT BADGE GOAL CARD -->
        <div class="p-5 bg-gradient-to-r from-amber-950/50 via-slate-950 to-indigo-950/50 border border-amber-500/40 rounded-2xl space-y-3">
          <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-amber-300 uppercase tracking-wider">🎯 Next Badge Goal:</h3>
            <span class="text-[10px] text-slate-400 font-bold">Target: {{ nextGoal.targetScore }}</span>
          </div>

          <p class="text-xs text-slate-200">
            Weak Topics: <strong class="text-white">{{ nextGoal.topic }}</strong> (current score: <span class="text-rose-400 font-bold">{{ nextGoal.currentScore }}</span>)
          </p>

          <div class="pt-1">
            <Link
              href="/student/quizzes/practice"
              class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-black text-xs shadow-md inline-block"
            >
              ▶ Work on Loops →
            </Link>
          </div>
        </div>

        <!-- Logic Rules Note -->
        <div class="p-4 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-slate-300 space-y-1">
          <h4 class="font-bold text-white uppercase text-[11px]">🧠 Achievements Logic Rules:</h4>
          <ul class="space-y-1">
            <li>• If practice score rises → unlocks "Weak Topic Fixer"</li>
            <li>• If continuous study time → unlocks "Streak Learner"</li>
            <li>• If completes a module → unlocks "Module Finisher"</li>
          </ul>
        </div>

      </div>

    </div>
  </StudentLayout>
</template>
