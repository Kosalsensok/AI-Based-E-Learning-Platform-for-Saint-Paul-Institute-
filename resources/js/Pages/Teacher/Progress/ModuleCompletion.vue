<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  overview: {
    total_students: number
    completed: number
    completed_pct: number
    in_progress: number
    in_progress_pct: number
    not_started: number
    not_started_pct: number
    class_avg_completion: number
  }
  modules: Array<any>
  heatmap: Array<any>
}>()

const emit = defineEmits(['selectModule', 'action'])

const viewMode = ref<'table' | 'bar' | 'heatmap'>('table')

const getStatusBadge = (status: string) => {
  switch (status) {
    case 'green':
      return { text: '🟢 Good', bg: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300' }
    case 'yellow':
      return { text: '🟡 Slowing', bg: 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300' }
    case 'red':
      return { text: '🔴 Hard / Stuck', bg: 'bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-300 animate-pulse' }
    default:
      return { text: '🟢 Good', bg: 'bg-emerald-100 text-emerald-800' }
  }
}

const getHeatmapColor = (pct: number) => {
  if (pct >= 100) return { icon: '🟩', text: '100%', bg: 'bg-emerald-500 text-white' }
  if (pct >= 75) return { icon: '🟢', text: `${pct}%`, bg: 'bg-emerald-400 text-white' }
  if (pct >= 50) return { icon: '🟡', text: `${pct}%`, bg: 'bg-amber-400 text-slate-900' }
  if (pct >= 25) return { icon: '🟠', text: `${pct}%`, bg: 'bg-orange-500 text-white' }
  return { icon: '🔴', text: `${pct}%`, bg: 'bg-rose-500 text-white' }
}
</script>

<template>
  <div class="space-y-6">
    <!-- 📦 Module Completion Overview Cards -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-gray-700 pb-3">
        <div>
          <h2 class="text-base font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
            <span>📦 Module Completion Overview</span>
          </h2>
          <p class="text-xs text-slate-500 dark:text-slate-400">
            Track student completion rates across all course modules and discover drop-off friction points.
          </p>
        </div>

        <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-gray-700 p-1 rounded-xl text-xs font-bold self-start">
          <button
            @click="viewMode = 'table'"
            :class="['px-3 py-1.5 rounded-lg transition', viewMode === 'table' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-600 dark:text-slate-300']"
          >
            📋 Table View
          </button>
          <button
            @click="viewMode = 'bar'"
            :class="['px-3 py-1.5 rounded-lg transition', viewMode === 'bar' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-600 dark:text-slate-300']"
          >
            📊 Bar View
          </button>
          <button
            @click="viewMode = 'heatmap'"
            :class="['px-3 py-1.5 rounded-lg transition', viewMode === 'heatmap' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-600 dark:text-slate-300']"
          >
            🔥 Heatmap View
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-1">
        <div class="p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-900/20 border border-emerald-200/60 dark:border-emerald-800/40">
          <div class="text-[11px] font-bold text-emerald-800 dark:text-emerald-300 uppercase">✅ Completed Students</div>
          <div class="text-2xl font-extrabold text-emerald-700 dark:text-emerald-400 mt-1">
            {{ overview?.completed || 32 }} / {{ overview?.total_students || 45 }}
            <span class="text-sm font-bold">({{ overview?.completed_pct || 71 }}%)</span>
          </div>
          <div class="w-full bg-emerald-200/80 dark:bg-emerald-950 h-2 rounded-full mt-2 overflow-hidden">
            <div class="bg-emerald-500 h-full rounded-full" :style="{ width: (overview?.completed_pct || 71) + '%' }"></div>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-amber-50/70 dark:bg-amber-900/20 border border-amber-200/60 dark:border-amber-800/40">
          <div class="text-[11px] font-bold text-amber-800 dark:text-amber-300 uppercase">🟡 In Progress</div>
          <div class="text-2xl font-extrabold text-amber-700 dark:text-amber-400 mt-1">
            {{ overview?.in_progress || 8 }}
            <span class="text-sm font-bold">({{ overview?.in_progress_pct || 18 }}%)</span>
          </div>
          <div class="w-full bg-amber-200/80 dark:bg-amber-950 h-2 rounded-full mt-2 overflow-hidden">
            <div class="bg-amber-500 h-full rounded-full" :style="{ width: (overview?.in_progress_pct || 18) + '%' }"></div>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-rose-50/70 dark:bg-rose-900/20 border border-rose-200/60 dark:border-rose-800/40">
          <div class="text-[11px] font-bold text-rose-800 dark:text-rose-300 uppercase">🔴 Not Started / Stuck</div>
          <div class="text-2xl font-extrabold text-rose-700 dark:text-rose-400 mt-1">
            {{ overview?.not_started || 5 }}
            <span class="text-sm font-bold">({{ overview?.not_started_pct || 11 }}%)</span>
          </div>
          <div class="w-full bg-rose-200/80 dark:bg-rose-950 h-2 rounded-full mt-2 overflow-hidden">
            <div class="bg-rose-500 h-full rounded-full" :style="{ width: (overview?.not_started_pct || 11) + '%' }"></div>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-900/20 border border-blue-200/60 dark:border-blue-800/40">
          <div class="text-[11px] font-bold text-blue-800 dark:text-blue-300 uppercase">📈 Class Average Completion</div>
          <div class="text-2xl font-extrabold text-blue-700 dark:text-blue-400 mt-1">
            {{ overview?.class_avg_completion || 72 }}%
          </div>
          <div class="w-full bg-blue-200/80 dark:bg-blue-950 h-2 rounded-full mt-2 overflow-hidden">
            <div class="bg-blue-500 h-full rounded-full" :style="{ width: (overview?.class_avg_completion || 72) + '%' }"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- 1. TABLE VIEW -->
    <div v-if="viewMode === 'table'" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden space-y-4">
      <div class="p-4 bg-slate-50 dark:bg-gray-700/50 border-b border-slate-200/80 dark:border-gray-700 flex justify-between items-center">
        <h3 class="font-extrabold text-sm text-slate-800 dark:text-white flex items-center gap-2">
          <span>📦 Completion by Module</span>
        </h3>
        <span class="text-xs text-slate-500 font-medium">Click any row to inspect detail drill-down</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-100/70 dark:bg-gray-700/80 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200">
              <th class="p-3.5">Module Name</th>
              <th class="p-3.5">Students</th>
              <th class="p-3.5">Completed</th>
              <th class="p-3.5">In Progress</th>
              <th class="p-3.5">Not Started</th>
              <th class="p-3.5">Rate</th>
              <th class="p-3.5 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
            <tr
              v-for="m in modules"
              :key="m.id"
              @click="emit('selectModule', m)"
              class="hover:bg-blue-50/40 dark:hover:bg-gray-700/60 cursor-pointer transition"
            >
              <td class="p-3.5 font-extrabold text-slate-800 dark:text-white">
                <div>{{ m.name }}: {{ m.title }}</div>
                <div class="text-[10px] text-slate-400 font-normal">Avg time: {{ m.avg_time }}</div>
              </td>
              <td class="p-3.5 font-bold text-slate-600 dark:text-slate-300">{{ m.students }}</td>
              <td class="p-3.5 font-bold text-emerald-600">{{ m.completed }}</td>
              <td class="p-3.5 font-bold text-amber-600">{{ m.in_progress }}</td>
              <td class="p-3.5 font-bold text-rose-600">{{ m.not_started }}</td>
              <td class="p-3.5">
                <span class="px-2.5 py-1 rounded-full font-bold text-[11px]" :class="getStatusBadge(m.status).bg">
                  {{ m.rate }}% {{ getStatusBadge(m.status).text.split(' ')[0] }}
                </span>
              </td>
              <td class="p-3.5 text-right">
                <button
                  @click.stop="emit('selectModule', m)"
                  class="px-3 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 rounded-lg font-bold transition"
                >
                  🔍 Detail View
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Color Meaning Legend -->
      <div class="p-4 bg-slate-50 dark:bg-gray-700/40 border-t border-slate-200/80 dark:border-gray-700 text-xs">
        <span class="font-extrabold text-slate-700 dark:text-slate-200 mr-3">Color Meaning:</span>
        <div class="inline-flex flex-wrap gap-4 mt-1 sm:mt-0">
          <span class="flex items-center gap-1 font-semibold text-emerald-700 dark:text-emerald-400">
            🟢 Green = Module completion high (&gt;75%)
          </span>
          <span class="flex items-center gap-1 font-semibold text-amber-700 dark:text-amber-400">
            🟡 Yellow = Slowing down or drop-off starting (50–74%)
          </span>
          <span class="flex items-center gap-1 font-semibold text-rose-700 dark:text-rose-400">
            🔴 Red = High difficulty / Many students stuck (&lt;50%)
          </span>
        </div>
      </div>
    </div>

    <!-- 2. BAR VIEW -->
    <div v-if="viewMode === 'bar'" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-5">
      <h3 class="font-extrabold text-sm text-slate-800 dark:text-white border-b pb-3">
        📊 Module Completion Bar View (Visual Visualizer)
      </h3>

      <div class="space-y-4">
        <div v-for="m in modules" :key="m.id" class="space-y-1.5 text-xs">
          <div class="flex justify-between font-extrabold">
            <span class="text-slate-800 dark:text-white">{{ m.name }}: {{ m.title }}</span>
            <span :class="m.status === 'green' ? 'text-emerald-600' : m.status === 'yellow' ? 'text-amber-600' : 'text-rose-600'">
              {{ m.rate }}% {{ getStatusBadge(m.status).text.split(' ')[0] }}
            </span>
          </div>
          <div class="w-full bg-slate-100 dark:bg-gray-700 h-4 rounded-full overflow-hidden p-0.5 border border-slate-200/60 dark:border-gray-600">
            <div
              class="h-full rounded-full transition-all duration-700 flex items-center justify-end pr-2 text-[10px] font-extrabold text-white"
              :class="m.status === 'green' ? 'bg-emerald-500' : m.status === 'yellow' ? 'bg-amber-500' : 'bg-rose-500'"
              :style="{ width: m.rate + '%' }"
            >
              <span v-if="m.rate >= 15">{{ m.rate }}%</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 3. 🔥 HEATMAP VIEW -->
    <div v-if="viewMode === 'heatmap'" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
      <div class="flex justify-between items-center border-b pb-3">
        <div>
          <h3 class="font-extrabold text-sm text-slate-800 dark:text-white flex items-center gap-2">
            <span>🔥 Module Completion Heatmap</span>
          </h3>
          <p class="text-xs text-slate-500">
            Instant matrix view showing exact progress per student per module.
          </p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-center border-collapse text-xs">
          <thead>
            <tr class="bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300 font-extrabold uppercase border-b">
              <th class="p-3 text-left w-48">Student</th>
              <th v-for="m in modules" :key="m.id" class="p-3">{{ m.name.split(':')[0] }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
            <tr v-for="h in heatmap" :key="h.student" class="hover:bg-slate-50/60 dark:hover:bg-gray-700/50">
              <td class="p-3 text-left font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <span class="text-base">{{ h.avatar }}</span>
                <span>{{ h.student }}</span>
              </td>
              <td class="p-3">
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold" :class="getHeatmapColor(h.m1).bg">
                  {{ getHeatmapColor(h.m1).icon }} {{ h.m1 }}%
                </span>
              </td>
              <td class="p-3">
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold" :class="getHeatmapColor(h.m2).bg">
                  {{ getHeatmapColor(h.m2).icon }} {{ h.m2 }}%
                </span>
              </td>
              <td class="p-3">
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold" :class="getHeatmapColor(h.m3).bg">
                  {{ getHeatmapColor(h.m3).icon }} {{ h.m3 }}%
                </span>
              </td>
              <td class="p-3">
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold" :class="getHeatmapColor(h.m4).bg">
                  {{ getHeatmapColor(h.m4).icon }} {{ h.m4 }}%
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Heatmap Legend -->
      <div class="p-4 bg-slate-50 dark:bg-gray-700/40 rounded-xl border border-slate-200/60 dark:border-gray-600 text-xs">
        <span class="font-extrabold text-slate-700 dark:text-slate-200 mr-3 block sm:inline mb-1 sm:mb-0">Heatmap Legend:</span>
        <div class="inline-flex flex-wrap gap-3">
          <span class="px-2 py-0.5 bg-emerald-500 text-white rounded font-bold">🟩 100%</span>
          <span class="px-2 py-0.5 bg-emerald-400 text-white rounded font-bold">🟢 75–99%</span>
          <span class="px-2 py-0.5 bg-amber-400 text-slate-900 rounded font-bold">🟡 50–74%</span>
          <span class="px-2 py-0.5 bg-orange-500 text-white rounded font-bold">🟠 25–49%</span>
          <span class="px-2 py-0.5 bg-rose-500 text-white rounded font-bold">🔴 0–24%</span>
        </div>
      </div>
    </div>
  </div>
</template>
