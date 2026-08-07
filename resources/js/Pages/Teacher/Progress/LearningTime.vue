<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  overview: {
    class_total_hours: number
    avg_per_student: string
    most_active: { name: string; hours: string }
    least_active: { name: string; hours: string }
  }
  studentList: Array<any>
  chartDays: Array<any>
  distribution: Array<any>
}>()

const emit = defineEmits(['selectStudent', 'sendReminder'])

const getTrendBadge = (trend: string) => {
  if (trend.includes('Good')) return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 font-extrabold'
  if (trend.includes('Stable')) return 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 font-extrabold'
  if (trend.includes('Slow')) return 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 font-extrabold'
  return 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 animate-pulse font-extrabold'
}
</script>

<template>
  <div class="space-y-6">
    <!-- ⏱️ Learning Time Overview Cards -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
      <div class="border-b border-slate-100 dark:border-gray-700 pb-3">
        <h2 class="text-base font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
          <span>⏱️ Learning Time Overview</span>
        </h2>
        <p class="text-xs text-slate-500">
          Monitor total hours spent studying content, daily averages, and engagement trends.
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-900/20 border border-blue-200/60 dark:border-blue-800/40">
          <span class="text-[11px] font-bold text-blue-800 dark:text-blue-300 uppercase">Class Total Hours</span>
          <div class="text-2xl font-extrabold text-blue-700 dark:text-blue-400 mt-1">
            {{ overview?.class_total_hours || 878 }} hours
          </div>
          <span class="text-[10px] text-slate-500 font-medium">Accumulated learning time</span>
        </div>

        <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-900/20 border border-indigo-200/60 dark:border-indigo-800/40">
          <span class="text-[11px] font-bold text-indigo-800 dark:text-indigo-300 uppercase">Avg / Student</span>
          <div class="text-2xl font-extrabold text-indigo-700 dark:text-indigo-400 mt-1">
            {{ overview?.avg_per_student || '19h 30m' }}
          </div>
          <span class="text-[10px] text-slate-500 font-medium">Average time across course</span>
        </div>

        <div class="p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-900/20 border border-emerald-200/60 dark:border-emerald-800/40">
          <span class="text-[11px] font-bold text-emerald-800 dark:text-emerald-300 uppercase">🏆 Most Active</span>
          <div class="text-lg font-extrabold text-emerald-700 dark:text-emerald-400 mt-1 truncate">
            {{ overview?.most_active?.name || 'Long Vicheka' }}
          </div>
          <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-300">({{ overview?.most_active?.hours || '32h' }})</span>
        </div>

        <div class="p-4 rounded-2xl bg-rose-50/70 dark:bg-rose-900/20 border border-rose-200/60 dark:border-rose-800/40">
          <span class="text-[11px] font-bold text-rose-800 dark:text-rose-300 uppercase">⚠️ Least Active</span>
          <div class="text-lg font-extrabold text-rose-700 dark:text-rose-400 mt-1 truncate">
            {{ overview?.least_active?.name || 'Sok Channa' }}
          </div>
          <span class="text-[11px] font-bold text-rose-600 dark:text-rose-300">({{ overview?.least_active?.hours || '8h 15m' }})</span>
        </div>
      </div>
    </div>

    <!-- ⏱️ Learning Time by Student Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden text-xs">
      <div class="p-4 bg-slate-50 dark:bg-gray-700/50 border-b border-slate-200/80 dark:border-gray-700 flex justify-between items-center">
        <h3 class="font-extrabold text-sm text-slate-800 dark:text-white flex items-center gap-2">
          <span>⏱️ Learning Time by Student</span>
        </h3>
        <span class="text-slate-500 font-medium">Click any row to open student detail modal</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-100/70 dark:bg-gray-700/80 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200">
              <th class="p-3.5">Student</th>
              <th class="p-3.5">Total Time</th>
              <th class="p-3.5">This Week</th>
              <th class="p-3.5">Avg / Day</th>
              <th class="p-3.5">Last Active</th>
              <th class="p-3.5">Trend</th>
              <th class="p-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
            <tr
              v-for="s in studentList"
              :key="s.id"
              @click="emit('selectStudent', s)"
              class="hover:bg-blue-50/40 dark:hover:bg-gray-700/60 cursor-pointer transition"
            >
              <td class="p-3.5 font-extrabold text-slate-800 dark:text-white">
                <span>{{ s.student }}</span>
              </td>
              <td class="p-3.5 font-bold text-slate-700 dark:text-slate-200">{{ s.total_time }}</td>
              <td class="p-3.5 font-bold text-blue-600 dark:text-blue-400">{{ s.this_week }}</td>
              <td class="p-3.5 font-bold text-indigo-600 dark:text-indigo-400">{{ s.avg_day }}</td>
              <td class="p-3.5 font-medium text-slate-500 dark:text-slate-400">{{ s.last_active }}</td>
              <td class="p-3.5">
                <span class="px-2.5 py-1 rounded-full text-[10px]" :class="getTrendBadge(s.trend)">
                  {{ s.trend }}
                </span>
              </td>
              <td class="p-3.5 text-right">
                <button
                  @click.stop="emit('sendReminder', s)"
                  class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300 rounded-lg font-bold transition flex items-center gap-1 ml-auto"
                >
                  <span>🔔</span> Reminder
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Charts & Distribution Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- 📈 Weekly Learning Time Chart -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
        <div class="border-b pb-3 flex justify-between items-center">
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">📈 Weekly Learning Time Trend</h3>
          <span class="text-[11px] font-extrabold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
            🔥 Peak: Thursday
          </span>
        </div>

        <!-- Custom CSS Bar Chart -->
        <div class="h-44 flex items-end justify-between gap-2 pt-6 pb-2 px-2 border-b border-slate-200 dark:border-gray-700">
          <div v-for="d in chartDays" :key="d.day" class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
            <span class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 opacity-80 group-hover:opacity-100">
              {{ d.label }}
            </span>
            <div class="w-full max-w-[36px] bg-slate-100 dark:bg-gray-700 rounded-t-lg h-32 flex items-end overflow-hidden">
              <div
                class="w-full rounded-t-lg transition-all duration-700"
                :class="d.isPeak ? 'bg-blue-600' : d.isLowest ? 'bg-rose-400' : 'bg-indigo-400 dark:bg-indigo-500'"
                :style="{ height: (d.hours / 3.5 * 100) + '%' }"
              ></div>
            </div>
            <span class="text-[11px] font-extrabold text-slate-700 dark:text-slate-300">{{ d.day }}</span>
          </div>
        </div>

        <div class="flex justify-between items-center text-xs text-slate-500 pt-1">
          <span>Peak Study Day: <strong class="text-blue-600">Thursday (3h average)</strong></span>
          <span>Lowest Activity: <strong class="text-rose-500">Sunday</strong></span>
        </div>
      </div>

      <!-- 📊 Learning Time Distribution -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
        <div class="border-b pb-3">
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">📊 Learning Time Distribution</h3>
          <p class="text-xs text-slate-500">Number of students categorized by study hour brackets.</p>
        </div>

        <div class="space-y-3 pt-1 text-xs">
          <div v-for="dist in distribution" :key="dist.range" class="space-y-1">
            <div class="flex justify-between font-bold text-slate-700 dark:text-slate-200">
              <span>{{ dist.range }}</span>
              <span>{{ dist.students }} students</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-gray-700 h-3.5 rounded-full overflow-hidden p-0.5">
              <div
                class="bg-indigo-500 h-full rounded-full transition-all duration-700"
                :style="{ width: (dist.students / 15 * 100) + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Learning Time Logic Insights -->
    <div class="p-5 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl border border-blue-200/80 dark:border-blue-800/40 space-y-2 text-xs">
      <div class="font-extrabold text-blue-900 dark:text-blue-300 uppercase tracking-wider">
        💡 Teacher Insight Rules:
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
        <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-slate-200/60 dark:border-gray-700">
          <span class="font-bold text-rose-600">High Time + Low Score</span>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
            Lesson is too complex; students are re-watching video without understanding core concepts.
          </p>
        </div>

        <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-slate-200/60 dark:border-gray-700">
          <span class="font-bold text-amber-600">Low Time + Low Progress</span>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
            Student is inactive or disengaged; immediate reminder email recommended.
          </p>
        </div>

        <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-slate-200/60 dark:border-gray-700">
          <span class="font-bold text-emerald-600">High Time + High Completion</span>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
            Diligent learner pursuing mastery; prime candidate for advanced bonus projects.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
