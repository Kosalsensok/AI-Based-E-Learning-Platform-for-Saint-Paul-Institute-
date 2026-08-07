<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const weeklyMetrics = ref([
  { label: 'Weekly Active Users (WAU)', val: '1,890', change: '↑ +12%', positive: true },
  { label: 'Avg Study Hours per Student', val: '5h 15m', change: '↑ +8%', positive: true },
  { label: 'Quizzes Taken', val: '1,200', change: '↓ -3%', positive: false },
  { label: 'Videos Watched', val: '4,250', change: '↑ +15%', positive: true }
])
</script>

<template>
  <StudentLayout title="Progress Tracking — Weekly Progress">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-purple-950 via-slate-900 to-indigo-950 border border-purple-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold uppercase tracking-wider">
            📊 WEEKLY COMPARISON
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>📊 WEEKLY PROGRESS (This vs Last Week)</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ប្រៀបធៀបវឌ្ឍនភាពប្រចាំសប្តាហ៍ តាមដាន Learning Momentum និងកំណើននៃការរៀន
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Weekly Net Growth</p>
            <p class="text-base font-black text-purple-400">+8% Overall</p>
          </div>
        </div>
      </div>

      <!-- MAIN WEEKLY PROGRESS CARD (Matching Prompt Spec Layout) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        
        <div class="border-b border-slate-700/60 pb-3 flex items-center justify-between">
          <h2 class="text-base font-black text-white">This Week vs Last Week Metrics</h2>
          <span class="text-xs text-purple-300 font-bold">Momentum: +18% Progress 🚀</span>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div v-for="m in weeklyMetrics" :key="m.label" class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
            <p class="text-[10px] text-slate-400 font-bold">{{ m.label }}</p>
            <div class="flex items-baseline justify-between">
              <span class="text-xl font-black text-white font-mono">{{ m.val }}</span>
              <span :class="[m.positive ? 'text-emerald-400' : 'text-rose-400', 'text-xs font-bold font-mono']">{{ m.change }}</span>
            </div>
          </div>
        </div>

        <!-- Progress Trend Comparison Bars -->
        <div class="p-5 bg-slate-950 rounded-2xl border border-slate-800 space-y-4 text-xs">
          <h3 class="font-bold text-white uppercase tracking-wider">🔁 Progress Trend Comparison:</h3>

          <!-- Last Week -->
          <div class="space-y-1">
            <div class="flex items-center justify-between font-bold text-slate-400">
              <span>Last Week:</span>
              <span class="font-mono">64%</span>
            </div>
            <div class="w-full h-3 rounded-full bg-slate-900 overflow-hidden border border-slate-800">
              <div class="h-full bg-slate-600 rounded-full w-[64%]"></div>
            </div>
          </div>

          <!-- This Week -->
          <div class="space-y-1">
            <div class="flex items-center justify-between font-bold text-white">
              <span>This Week:</span>
              <span class="font-mono text-purple-400">72%</span>
            </div>
            <div class="w-full h-3 rounded-full bg-slate-900 overflow-hidden border border-slate-800">
              <div class="h-full bg-gradient-to-r from-purple-500 to-indigo-400 rounded-full w-[72%]"></div>
            </div>
          </div>

          <p class="text-xs text-emerald-400 font-bold pt-1">
            🚀 Net Growth: +8% increase in overall course completion this week!
          </p>
        </div>

      </div>

    </div>
  </StudentLayout>
</template>
