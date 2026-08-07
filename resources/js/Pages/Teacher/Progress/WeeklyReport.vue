<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  report: {
    week_range: string
    kpis: {
      active_students: string
      video_hours: string
      quizzes_completed: string
      pdfs_opened: string
      avg_progress_gain: string
      at_risk_alerts: string
    }
    daily_summary: Array<any>
    progress_trend: Array<any>
    alerts: Array<any>
    recommendations: Array<string>
  }
}>()

const emit = defineEmits(['openExport', 'triggerAction'])

const selectedWeek = ref('This Week')

const getAlertBadge = (type: string) => {
  switch (type) {
    case 'red':
      return { icon: '🔴', bg: 'bg-rose-50 dark:bg-rose-900/20 text-rose-800 dark:text-rose-300 border-rose-200/80 dark:border-rose-800/40' }
    case 'yellow':
      return { icon: '🟡', bg: 'bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-300 border-amber-200/80 dark:border-amber-800/40' }
    case 'green':
      return { icon: '🟢', bg: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-300 border-emerald-200/80 dark:border-emerald-800/40' }
    default:
      return { icon: '🔵', bg: 'bg-blue-50 text-blue-800 border-blue-200' }
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- 📅 Weekly Report Header Controls & KPI Cards -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-5">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-gray-700 pb-4">
        <div>
          <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Weekly Dashboard Analytics</div>
          <h2 class="text-lg font-extrabold text-slate-800 dark:text-white flex items-center gap-2 mt-0.5">
            <span>📅 Weekly Report – {{ report?.week_range || 'May 19 to May 25, 2025' }}</span>
          </h2>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <div class="flex items-center bg-slate-100 dark:bg-gray-700 p-1 rounded-xl text-xs font-bold">
            <button
              @click="selectedWeek = 'Previous Week'"
              :class="['px-3 py-1.5 rounded-lg transition', selectedWeek === 'Previous Week' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-600 dark:text-slate-300']"
            >
              Previous
            </button>
            <button
              @click="selectedWeek = 'This Week'"
              :class="['px-3 py-1.5 rounded-lg transition', selectedWeek === 'This Week' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-600 dark:text-slate-300']"
            >
              This Week
            </button>
            <button
              @click="selectedWeek = 'Next Week'"
              :class="['px-3 py-1.5 rounded-lg transition', selectedWeek === 'Next Week' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-600 dark:text-slate-300']"
            >
              Next
            </button>
          </div>

          <button
            @click="emit('openExport')"
            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl font-extrabold text-xs shadow-sm flex items-center gap-1.5 transition"
          >
            <span>📤</span> Export Report
          </button>
        </div>
      </div>

      <!-- KPI Cards Grid -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-3.5 bg-blue-50/70 dark:bg-blue-900/20 rounded-2xl border border-blue-200/60 dark:border-blue-800/40 text-center">
          <span class="text-[10px] font-extrabold text-blue-800 dark:text-blue-300 uppercase">👥 Active Students</span>
          <p class="text-xl font-extrabold text-blue-700 dark:text-blue-400 mt-1">{{ report?.kpis?.active_students || '38 / 45' }}</p>
        </div>

        <div class="p-3.5 bg-indigo-50/70 dark:bg-indigo-900/20 rounded-2xl border border-indigo-200/60 dark:border-indigo-800/40 text-center">
          <span class="text-[10px] font-extrabold text-indigo-800 dark:text-indigo-300 uppercase">🎬 Video Hours</span>
          <p class="text-xl font-extrabold text-indigo-700 dark:text-indigo-400 mt-1">{{ report?.kpis?.video_hours || '245h' }}</p>
        </div>

        <div class="p-3.5 bg-emerald-50/70 dark:bg-emerald-900/20 rounded-2xl border border-emerald-200/60 dark:border-emerald-800/40 text-center">
          <span class="text-[10px] font-extrabold text-emerald-800 dark:text-emerald-300 uppercase">📝 Quizzes Done</span>
          <p class="text-xl font-extrabold text-emerald-700 dark:text-emerald-400 mt-1">{{ report?.kpis?.quizzes_completed || '128' }}</p>
        </div>

        <div class="p-3.5 bg-amber-50/70 dark:bg-amber-900/20 rounded-2xl border border-amber-200/60 dark:border-amber-800/40 text-center">
          <span class="text-[10px] font-extrabold text-amber-800 dark:text-amber-300 uppercase">📄 PDFs Opened</span>
          <p class="text-xl font-extrabold text-amber-700 dark:text-amber-400 mt-1">{{ report?.kpis?.pdfs_opened || '320' }}</p>
        </div>

        <div class="p-3.5 bg-teal-50/70 dark:bg-teal-900/20 rounded-2xl border border-teal-200/60 dark:border-teal-800/40 text-center">
          <span class="text-[10px] font-extrabold text-teal-800 dark:text-teal-300 uppercase">📈 Progress Gain</span>
          <p class="text-xl font-extrabold text-teal-700 dark:text-teal-400 mt-1">{{ report?.kpis?.avg_progress_gain || '+6%' }}</p>
        </div>

        <div class="p-3.5 bg-rose-50/70 dark:bg-rose-900/20 rounded-2xl border border-rose-200/60 dark:border-rose-800/40 text-center">
          <span class="text-[10px] font-extrabold text-rose-800 dark:text-rose-300 uppercase">🚨 At-Risk Alerts</span>
          <p class="text-xl font-extrabold text-rose-700 dark:text-rose-400 mt-1">{{ report?.kpis?.at_risk_alerts || '5 std' }}</p>
        </div>
      </div>
    </div>

    <!-- Activity & Progress Gain Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- 📊 Weekly Activity Summary -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden text-xs">
        <div class="p-4 bg-slate-50 dark:bg-gray-700/50 border-b border-slate-200/80 dark:border-gray-700">
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">📊 Weekly Activity Summary</h3>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-gray-700 p-2">
          <div
            v-for="day in report?.daily_summary"
            :key="day.day"
            class="p-3 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-gray-700/50 transition rounded-xl"
          >
            <div class="font-extrabold text-slate-800 dark:text-white w-24">
              {{ day.day }}
            </div>
            <div class="flex-1 flex items-center gap-3 text-slate-600 dark:text-slate-300 font-medium">
              <span>👥 <strong>{{ day.students }}</strong> active</span>
              <span>·</span>
              <span>🎬 <strong>{{ day.video_hours }}h</strong> watched</span>
              <span>·</span>
              <span>📝 <strong>{{ day.quizzes }}</strong> quizzes</span>
            </div>
            <span v-if="day.highlight" class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
              {{ day.highlight }}
            </span>
          </div>
        </div>
      </div>

      <!-- 📈 Weekly Progress Trend -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden text-xs">
        <div class="p-4 bg-slate-50 dark:bg-gray-700/50 border-b border-slate-200/80 dark:border-gray-700">
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">📈 Progress Gain This Week</h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-100/70 dark:bg-gray-700/80 text-slate-500 font-bold uppercase border-b">
                <th class="p-3">Student</th>
                <th class="p-3">Last Wk</th>
                <th class="p-3">This Wk</th>
                <th class="p-3">Gain</th>
                <th class="p-3">Trend Bar</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
              <tr v-for="st in report?.progress_trend" :key="st.student" class="hover:bg-slate-50 dark:hover:bg-gray-700/40">
                <td class="p-3 font-extrabold text-slate-800 dark:text-white">{{ st.student }}</td>
                <td class="p-3 font-bold text-slate-500">{{ st.last_week }}%</td>
                <td class="p-3 font-extrabold text-blue-600">{{ st.this_week }}%</td>
                <td class="p-3 font-extrabold">{{ st.gain }}</td>
                <td class="p-3 w-32">
                  <div class="w-full bg-slate-100 dark:bg-gray-700 h-2.5 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full" :style="{ width: st.bar_pct + '%' }"></div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Alerts & Recommended Actions Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- 🚨 Weekly Alerts Panel -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
        <div class="border-b pb-3">
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white flex items-center gap-2">
            <span>🚨 Weekly Alerts Panel</span>
          </h3>
          <p class="text-xs text-slate-500">Automated system flags requiring immediate teacher attention.</p>
        </div>

        <div class="space-y-3 text-xs">
          <div
            v-for="(alt, idx) in report?.alerts"
            :key="idx"
            class="p-3.5 rounded-xl border flex items-start gap-2.5"
            :class="getAlertBadge(alt.type).bg"
          >
            <span class="text-sm mt-0.5">{{ getAlertBadge(alt.type).icon }}</span>
            <span class="font-bold leading-relaxed">{{ alt.text }}</span>
          </div>
        </div>
      </div>

      <!-- 💡 Recommended Actions for Teacher -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
        <div class="border-b pb-3">
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white flex items-center gap-2">
            <span>💡 Recommended Actions for Teacher</span>
          </h3>
          <p class="text-xs text-slate-500">Actionable recommendations to improve class engagement this week.</p>
        </div>

        <div class="space-y-2 text-xs">
          <div
            v-for="(rec, idx) in report?.recommendations"
            :key="idx"
            class="p-3 rounded-xl bg-slate-50 dark:bg-gray-700/50 border border-slate-200/60 dark:border-gray-600 flex items-center justify-between gap-3 font-semibold text-slate-800 dark:text-slate-200"
          >
            <span>{{ rec }}</span>
            <button
              @click="emit('triggerAction', rec)"
              class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-[11px] shrink-0 transition"
            >
              Take Action
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
