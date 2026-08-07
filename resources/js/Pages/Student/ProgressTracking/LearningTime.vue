<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const timeBreakdown = ref([
  { category: '🎥 Video', duration: '4h 10m', percent: 53, color: 'bg-indigo-500' },
  { category: '📄 PDF', duration: '1h 30m', percent: 19, color: 'bg-emerald-500' },
  { category: '📊 Slides', duration: '55m', percent: 12, color: 'bg-amber-500' },
  { category: '📝 Notes', duration: '1h 05m', percent: 14, color: 'bg-purple-500' },
  { category: '🧪 Quiz/Practice', duration: '5m', percent: 2, color: 'bg-cyan-500' }
])

const weeklyBarChart = ref([
  { day: 'Mon', hours: '1.2h', intensity: 'low', height: '30%' },
  { day: 'Tue', hours: '2.5h', intensity: 'med', height: '60%' },
  { day: 'Wed', hours: '3.8h', intensity: 'high', height: '90%' },
  { day: 'Thu', hours: '2.1h', intensity: 'med', height: '55%' },
  { day: 'Fri', hours: '4.0h', intensity: 'high', height: '100%' },
  { day: 'Sat', hours: '2.0h', intensity: 'med', height: '50%' },
  { day: 'Sun', hours: '0.8h', intensity: 'low', height: '20%' }
])
</script>

<template>
  <StudentLayout title="Progress Tracking — Learning Time">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-cyan-950 via-slate-900 to-indigo-950 border border-cyan-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold uppercase tracking-wider">
            ⏱️ ENGAGEMENT TRACKER
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>⏱️ LEARNING TIME</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ម៉ោងសិក្សាជាក់ស្តែង Breakdown តាមប្រភេទមាតិកា (Video, PDF, Slides, Quiz) និង Peak Hours
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">This Week vs Total</p>
            <p class="text-base font-black text-cyan-400">7h 45m / 28h 30m</p>
          </div>
        </div>
      </div>

      <!-- MAIN TIME BREAKDOWN CARD (Matching Prompt Spec Layout) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        
        <div class="border-b border-slate-700/60 pb-3 flex flex-wrap items-center justify-between gap-2">
          <h2 class="text-base font-black text-white">Breakdown by Content Type</h2>
          <span class="text-xs text-cyan-300 font-mono font-bold">Peak Study Hours: 19:00–22:00 🌙</span>
        </div>

        <!-- Breakdown List -->
        <div class="space-y-3">
          <div v-for="item in timeBreakdown" :key="item.category" class="space-y-1 text-xs">
            <div class="flex items-center justify-between font-bold text-slate-200">
              <span>{{ item.category }}</span>
              <span class="font-mono text-cyan-400">{{ item.duration }} ({{ item.percent }}%)</span>
            </div>
            <div class="w-full h-2.5 rounded-full bg-slate-950 overflow-hidden border border-slate-800">
              <div :class="[item.color, 'h-full rounded-full']" :style="{ width: item.percent + '%' }"></div>
            </div>
          </div>
        </div>

        <!-- WEEKLY TIME CHART BAR (Mon-Sun) -->
        <div class="p-5 bg-slate-950 rounded-2xl border border-slate-800 space-y-4">
          <h3 class="text-xs font-bold text-white uppercase tracking-wider">📊 Weekly Study Intensity (Mon – Sun):</h3>
          
          <div class="flex items-end justify-between h-36 gap-2 pt-4 px-2">
            <div v-for="day in weeklyBarChart" :key="day.day" class="flex flex-col items-center gap-1 flex-1">
              <span class="text-[10px] font-mono text-cyan-400 font-bold">{{ day.hours }}</span>
              <div class="w-full max-w-[28px] bg-slate-900 rounded-t-lg overflow-hidden h-full flex items-end">
                <div
                  :class="[day.intensity === 'high' ? 'bg-cyan-500' : day.intensity === 'med' ? 'bg-indigo-500' : 'bg-slate-700', 'w-full rounded-t-lg transition-all']"
                  :style="{ height: day.height }"
                ></div>
              </div>
              <span class="text-[10px] text-slate-400 font-bold mt-1">{{ day.day }}</span>
            </div>
          </div>
        </div>

        <!-- Utility Note Card -->
        <div class="p-4 bg-cyan-500/10 border border-cyan-500/30 rounded-2xl text-xs text-slate-200 space-y-1">
          <h4 class="font-bold text-cyan-300 uppercase text-[11px]">🔒 Why it’s useful?</h4>
          <p>• បង្ហាញថាអ្នក "រៀនបានពិតប្រាកដ" មិនមែនត្រឹមតែចុច Play វីដេអូហើយទុកចោល</p>
          <p>• ជួយ AI និងគ្រូបង្រៀនដឹងពីពេលដែលអ្នកខ្សោយ ឬអសកម្ម (Idle days)</p>
        </div>

      </div>

    </div>
  </StudentLayout>
</template>
