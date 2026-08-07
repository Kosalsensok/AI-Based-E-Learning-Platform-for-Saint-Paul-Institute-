<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const reminderSet = ref(true)
const isInLive = ref(false)

const liveClass = ref({
  title: '🟦 Live Class: C Functions — Mr. Sophea',
  date: 'May 15, 2025',
  time: '02:00 PM - 04:00 PM',
  joined: '48 / 60',
  location: 'Online (Zoom)',
  countdown: '02:13:42'
})

const handleJoin = () => {
  isInLive.value = true
}
</script>

<template>
  <StudentLayout title="Calendar — Live Class Schedule">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 border border-blue-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold uppercase tracking-wider">
            🟦 INSTRUCTOR-LED LIVE SESSIONS
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🟦 LIVE CLASS SCHEDULE</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            កាលវិភាគបង្រៀនផ្ទាល់ Live (Zoom) ជាមួយគ្រូតាមម៉ោងកំណត់ + Countdown Timer
          </p>
        </div>
      </div>

      <!-- MONTHLY CALENDAR GRID (Matching Prompt Spec Mock) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
          <h3 class="text-base font-black text-white">May 2025</h3>
          <span class="text-xs font-mono text-blue-400 font-bold">Upcoming Live: May 15 @ 02:00 PM</span>
        </div>

        <div class="grid grid-cols-7 gap-2 text-center text-xs">
          <span class="font-bold text-slate-400 py-1">Sun</span>
          <span class="font-bold text-slate-400 py-1">Mon</span>
          <span class="font-bold text-slate-400 py-1">Tue</span>
          <span class="font-bold text-slate-400 py-1">Wed</span>
          <span class="font-bold text-slate-400 py-1">Thu</span>
          <span class="font-bold text-slate-400 py-1">Fri</span>
          <span class="font-bold text-slate-400 py-1">Sat</span>

          <div v-for="d in 31" :key="d" :class="[d === 15 ? 'bg-blue-600/30 border-blue-500 font-black text-blue-300' : 'bg-slate-950/60 border-slate-800 text-slate-300', 'p-3 rounded-2xl border text-left min-h-[60px] space-y-1']">
            <span class="text-xs">{{ d }}</span>
            <div v-if="d === 15" class="p-1 rounded bg-blue-600 text-[10px] text-white font-bold truncate">
              🟦 Live: C Functions
            </div>
            <div v-if="d === 18" class="p-1 rounded bg-emerald-600 text-[10px] text-white font-bold truncate">
              🟩 Quiz: Loops
            </div>
            <div v-if="d === 20" class="p-1 rounded bg-amber-600 text-[10px] text-white font-bold truncate">
              🟧 Due: C Prog
            </div>
          </div>
        </div>
      </div>

      <!-- LIVE CLASS CARD (Matching Prompt Spec Layout) -->
      <div class="bg-slate-800/90 border border-blue-500/40 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-700/60 pb-4">
          <div>
            <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 font-bold text-xs border border-blue-500/30">
              Instructor-Led Class
            </span>
            <h2 class="text-lg font-black text-white mt-2">{{ liveClass.title }}</h2>
            <p class="text-xs text-slate-300 mt-0.5">
              Date: {{ liveClass.date }} | Time: {{ liveClass.time }}
            </p>
          </div>

          <div class="text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Countdown</p>
            <p class="text-xl font-black text-blue-400 font-mono">⏱️ {{ liveClass.countdown }}</p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-xs font-mono">
          <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
            <span class="text-[10px] text-slate-400 font-bold uppercase">Students Joined</span>
            <p class="text-base font-bold text-white">{{ liveClass.joined }}</p>
          </div>
          <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
            <span class="text-[10px] text-slate-400 font-bold uppercase">Location</span>
            <p class="text-base font-bold text-blue-300">{{ liveClass.location }}</p>
          </div>
        </div>

        <!-- Actions Row -->
        <div class="flex flex-wrap items-center justify-end gap-3 pt-2 border-t border-slate-700/60">
          <button
            v-if="!isInLive"
            @click="handleJoin"
            class="px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs shadow-lg transition-all hover:scale-105"
          >
            ▶ Join Now (Zoom)
          </button>
          <span v-else class="px-5 py-3 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 font-bold text-xs">
            ✅ You are in Live Class!
          </span>

          <button @click="reminderSet = !reminderSet" class="px-4 py-3 rounded-2xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700">
            {{ reminderSet ? '📌 Set Reminder' : '🔕 Cancel Reminder' }}
          </button>
        </div>

      </div>

    </div>
  </StudentLayout>
</template>
