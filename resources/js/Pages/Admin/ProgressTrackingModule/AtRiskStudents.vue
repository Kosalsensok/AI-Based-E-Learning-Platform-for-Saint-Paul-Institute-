<script setup lang="ts">
import { ref } from 'vue'
import type { AtRiskStudentItem } from './types'

const props = defineProps<{
  students: AtRiskStudentItem[]
}>()

const emit = defineEmits<{
  (e: 'sendEmailAlert', student: AtRiskStudentItem): void
  (e: 'openLiveChat', student: AtRiskStudentItem): void
  (e: 'toggleLockAccess', student: AtRiskStudentItem): void
  (e: 'saveRules', rules: any): void
  (e: 'exportList'): void
}>()

const idleDaysLimit = ref(3)
const quizDropLimit = ref(50)
const assignmentOverdueLimit = ref(5)

const ruleAutoEmail = ref(true)
const ruleTeacherAlert = ref(true)
const ruleLockModule = ref(true)

function handleSaveRules() {
  emit('saveRules', {
    idleDays: idleDaysLimit.value,
    quizDrop: quizDropLimit.value,
    assignmentOverdue: assignmentOverdueLimit.value,
    autoEmail: ruleAutoEmail.value,
    teacherAlert: ruleTeacherAlert.value,
    lockModule: ruleLockModule.value,
  })
}
</script>

<template>
  <div class="space-y-5 text-xs font-sans">
    <!-- Top Risk Status Header Banner -->
    <div class="bg-[#0d1222]/95 border border-red-500/40 rounded-2xl p-5 shadow-2xl space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-700/60 pb-3.5">
        <div>
          <h3 class="text-base font-black text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-red-400 animate-pulse shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" fill="currentColor" fill-opacity="0.2" />
            </svg>
            <span>AT-RISK STUDENTS — PROACTIVE INTERVENTION</span>
          </h3>
          <p class="text-slate-300 text-xs font-medium">AI automatic detection engine identifies students struggling or at risk of dropping out.</p>
        </div>

        <button @click="emit('exportList')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl text-slate-200 font-semibold flex items-center gap-1.5 transition-all">
          <svg class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" fill="currentColor" fill-opacity="0.2" />
          </svg>
          <span>Export At-Risk List</span>
        </button>
      </div>

      <!-- Risk Level Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-red-950/30 border border-red-500/50 p-4 rounded-xl flex items-center justify-between">
          <div>
            <span class="text-slate-400 text-[10px] block font-semibold">🔴 HIGH RISK LEVEL</span>
            <p class="text-lg font-black text-red-400">12 Students</p>
            <span class="text-[10px] text-red-300 font-medium">Idle > 3 days OR Score < 50%</span>
          </div>
          <span class="text-2xl">⚠️</span>
        </div>

        <div class="bg-amber-950/30 border border-amber-500/50 p-4 rounded-xl flex items-center justify-between">
          <div>
            <span class="text-slate-400 text-[10px] block font-semibold">🟡 MEDIUM RISK LEVEL</span>
            <p class="text-lg font-black text-amber-300">35 Students</p>
            <span class="text-[10px] text-amber-300 font-medium">Slow progress / Pending Assignment</span>
          </div>
          <span class="text-2xl">⏳</span>
        </div>

        <div class="bg-emerald-950/30 border border-emerald-500/50 p-4 rounded-xl flex items-center justify-between">
          <div>
            <span class="text-slate-400 text-[10px] block font-semibold">🟢 SAFE & ON TRACK</span>
            <p class="text-lg font-black text-emerald-400">2,411 Students</p>
            <span class="text-[10px] text-emerald-300 font-medium">Consistent daily activity & high scores</span>
          </div>
          <span class="text-2xl">✅</span>
        </div>
      </div>
    </div>

    <!-- Detected At-Risk List Table -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
        DETECTED AT-RISK LIST
      </h4>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80 bg-[#121827]">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-700/80">
            <tr>
              <th class="p-3.5">Student</th>
              <th class="p-3.5">Course / Major</th>
              <th class="p-3.5">⚠️ Risk Factors</th>
              <th class="p-3.5">Idle Time</th>
              <th class="p-3.5">Avg Quiz</th>
              <th class="p-3.5 text-right">Intervention Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/80 font-medium">
            <tr v-for="s in students" :key="s.id" class="hover:bg-slate-800/40 transition-colors">
              <td class="p-3.5">
                <div class="font-bold text-white text-sm">{{ s.name }}</div>
                <div class="text-[11px] font-mono text-purple-400">{{ s.id }}</div>
              </td>
              <td class="p-3.5">
                <div class="text-slate-200 font-semibold">{{ s.course }}</div>
                <div class="text-[11px] text-slate-400">{{ s.major }}</div>
              </td>
              <td class="p-3.5">
                <div class="flex flex-wrap gap-1">
                  <span v-for="(rf, rIdx) in s.risk_factors" :key="rIdx" class="px-2 py-0.5 rounded bg-red-950/80 text-red-300 border border-red-500/40 text-[10px] font-bold">
                    {{ rf }}
                  </span>
                </div>
              </td>
              <td class="p-3.5 font-bold text-amber-300">
                {{ s.idle_days }} Days Idle
              </td>
              <td class="p-3.5">
                <span class="font-black text-sm" :class="s.quiz_avg < 50 ? 'text-red-400' : 'text-amber-300'">
                  {{ s.quiz_avg }}%
                </span>
              </td>
              <td class="p-3.5 text-right space-x-1.5">
                <button @click="emit('sendEmailAlert', s)" title="Send Email Alert" class="px-2.5 py-1 bg-red-600/30 hover:bg-red-600/50 text-red-300 rounded-lg text-xs font-bold transition-all">
                  📧 Email Alert
                </button>
                <button @click="emit('openLiveChat', s)" title="Live Chat" class="px-2.5 py-1 bg-purple-600/30 hover:bg-purple-600/50 text-purple-300 rounded-lg text-xs font-bold transition-all">
                  💬 Chat
                </button>
                <button @click="emit('toggleLockAccess', s)" title="Lock / Unlock Access" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-bold transition-all">
                  ⚡ Lock
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- AI Auto-Intervention Rules (Admin Configured) -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl space-y-4">
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
        <h4 class="font-black text-sm text-white uppercase tracking-wide flex items-center gap-2">
          <span>🧠</span> AUTO-INTERVENTION RULES (ADMIN CONFIGURED)
        </h4>
      </div>

      <div class="space-y-3 bg-[#121827] p-4 rounded-xl border border-slate-700/80">
        <label class="flex items-center gap-3 cursor-pointer">
          <input v-model="ruleAutoEmail" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700" />
          <span class="text-slate-200">Auto-send email warning if student is Idle for</span>
          <input v-model="idleDaysLimit" type="number" class="w-16 bg-slate-900 border border-slate-700 rounded px-2 py-1 text-center font-bold text-purple-300" />
          <span class="text-slate-200">days.</span>
        </label>

        <label class="flex items-center gap-3 cursor-pointer">
          <input v-model="ruleTeacherAlert" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700" />
          <span class="text-slate-200">Send direct alert to Teacher if Quiz average drops below</span>
          <input v-model="quizDropLimit" type="number" class="w-16 bg-slate-900 border border-slate-700 rounded px-2 py-1 text-center font-bold text-red-400" />
          <span class="text-slate-200">%.</span>
        </label>

        <label class="flex items-center gap-3 cursor-pointer">
          <input v-model="ruleLockModule" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700" />
          <span class="text-slate-200">Lock next module access if Assignment is overdue for</span>
          <input v-model="assignmentOverdueLimit" type="number" class="w-16 bg-slate-900 border border-slate-700 rounded px-2 py-1 text-center font-bold text-amber-300" />
          <span class="text-slate-200">days.</span>
        </label>
      </div>

      <div class="flex justify-end gap-2.5 pt-2">
        <button @click="handleSaveRules" class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-lg shadow-purple-600/30 flex items-center gap-1.5 transition-all">
          <span>💾</span> Save Intervention Rules
        </button>
      </div>
    </div>
  </div>
</template>
