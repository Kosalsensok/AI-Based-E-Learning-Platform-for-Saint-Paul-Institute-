<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  weekRange: string
}>()

const emit = defineEmits(['close', 'export'])

const exportFormat = ref<'pdf' | 'excel' | 'csv'>('pdf')
const includeCharts = ref(true)
const includeStudentList = ref(true)
const includeAtRisk = ref(true)
const language = ref<'km' | 'en'>('km')

const handleDownload = () => {
  emit('export', {
    format: exportFormat.value,
    includeCharts: includeCharts.value,
    includeStudentList: includeStudentList.value,
    includeAtRisk: includeAtRisk.value,
    language: language.value,
  })
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 dark:border-gray-700 overflow-hidden">
      <!-- Header -->
      <div class="px-6 py-5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white flex items-center justify-between">
        <div>
          <div class="text-xs font-semibold text-purple-200 uppercase tracking-wider">📤 Weekly Analytics Export</div>
          <h2 class="text-xl font-extrabold mt-0.5">Export Weekly Report</h2>
          <p class="text-xs text-purple-100 font-medium">{{ weekRange }}</p>
        </div>
        <button
          @click="emit('close')"
          class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition"
        >
          ✕
        </button>
      </div>

      <!-- Body -->
      <div class="p-6 space-y-5 text-xs text-slate-700 dark:text-slate-200">
        <!-- Format Selection -->
        <div class="space-y-2">
          <label class="font-extrabold uppercase tracking-wider text-slate-400 text-[11px]">Format:</label>
          <div class="grid grid-cols-3 gap-3">
            <button
              @click="exportFormat = 'pdf'"
              :class="['py-2.5 rounded-xl font-bold border transition flex items-center justify-center gap-1.5', exportFormat === 'pdf' ? 'bg-purple-50 border-purple-500 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : 'bg-slate-50 dark:bg-gray-700 border-slate-200 dark:border-gray-600']"
            >
              <span>📄</span> PDF
            </button>
            <button
              @click="exportFormat = 'excel'"
              :class="['py-2.5 rounded-xl font-bold border transition flex items-center justify-center gap-1.5', exportFormat === 'excel' ? 'bg-emerald-50 border-emerald-500 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-slate-50 dark:bg-gray-700 border-slate-200 dark:border-gray-600']"
            >
              <span>📊</span> Excel
            </button>
            <button
              @click="exportFormat = 'csv'"
              :class="['py-2.5 rounded-xl font-bold border transition flex items-center justify-center gap-1.5', exportFormat === 'csv' ? 'bg-blue-50 border-blue-500 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-slate-50 dark:bg-gray-700 border-slate-200 dark:border-gray-600']"
            >
              <span>📑</span> CSV
            </button>
          </div>
        </div>

        <!-- Inclusion Checkboxes -->
        <div class="space-y-2.5 bg-slate-50 dark:bg-gray-700/50 p-4 rounded-2xl border border-slate-200/60 dark:border-gray-600">
          <label class="font-extrabold uppercase tracking-wider text-slate-400 text-[11px] block mb-1">Report Options:</label>

          <label class="flex items-center gap-2.5 cursor-pointer font-semibold">
            <input type="checkbox" v-model="includeCharts" class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500">
            <span>☑️ Include Visual Charts & Trends</span>
          </label>

          <label class="flex items-center gap-2.5 cursor-pointer font-semibold">
            <input type="checkbox" v-model="includeStudentList" class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500">
            <span>☑️ Include Full Student List</span>
          </label>

          <label class="flex items-center gap-2.5 cursor-pointer font-semibold">
            <input type="checkbox" v-model="includeAtRisk" class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500">
            <span>☑️ Include At-Risk Students & Alerts</span>
          </label>
        </div>

        <!-- Language Selector -->
        <div class="space-y-2">
          <label class="font-extrabold uppercase tracking-wider text-slate-400 text-[11px]">Report Language:</label>
          <div class="flex gap-3">
            <button
              @click="language = 'km'"
              :class="['px-4 py-2 rounded-xl font-bold border transition flex items-center gap-1.5', language === 'km' ? 'bg-indigo-50 border-indigo-500 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'bg-slate-50 dark:bg-gray-700 border-slate-200 dark:border-gray-600']"
            >
              <span>🇰🇭</span> Khmer
            </button>

            <button
              @click="language = 'en'"
              :class="['px-4 py-2 rounded-xl font-bold border transition flex items-center gap-1.5', language === 'en' ? 'bg-indigo-50 border-indigo-500 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'bg-slate-50 dark:bg-gray-700 border-slate-200 dark:border-gray-600']"
            >
              <span>🇬🇧</span> English
            </button>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="pt-2 flex flex-wrap gap-2">
          <button
            @click="handleDownload"
            class="flex-1 py-3 bg-purple-600 hover:bg-purple-700 text-white font-extrabold rounded-xl shadow-sm flex items-center justify-center gap-1.5 transition text-xs"
          >
            <span>📥</span> Download Report
          </button>
          <button
            @click="handleDownload"
            class="px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-xl shadow-sm flex items-center justify-center gap-1.5 transition text-xs"
          >
            <span>📧</span> Email Report
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
