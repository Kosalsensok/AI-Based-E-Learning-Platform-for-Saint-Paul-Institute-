<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  defaultReportType?: 'student' | 'course' | 'quiz'
}>()

const emit = defineEmits(['close', 'generate'])

const reportType = ref<'student' | 'course' | 'quiz'>(props.defaultReportType || 'student')
const format = ref<'pdf' | 'excel' | 'csv'>('pdf')
const language = ref<'km' | 'en' | 'bilingual'>('km')

const includeCharts = ref(true)
const includeTables = ref(true)
const includeStudentDetails = ref(true)
const includeComments = ref(true)
const includeAiInsights = ref(true)

const scheduledReport = ref(false)
const scheduleFrequency = ref<'weekly' | 'monthly'>('weekly')

const handleGenerate = () => {
  emit('generate', {
    reportType: reportType.value,
    format: format.value,
    language: language.value,
    includeCharts: includeCharts.value,
    includeTables: includeTables.value,
    includeStudentDetails: includeStudentDetails.value,
    includeComments: includeComments.value,
    includeAiInsights: includeAiInsights.value,
    scheduled: scheduledReport.value,
    scheduleFrequency: scheduleFrequency.value,
  })
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200 dark:border-gray-700 overflow-hidden flex flex-col max-h-[92vh]">
      <!-- Header -->
      <div class="px-6 py-5 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white flex items-center justify-between">
        <div>
          <div class="text-[11px] font-bold text-indigo-300 uppercase tracking-wider">📤 REPORT EXPORT CENTER</div>
          <h2 class="text-xl font-black mt-0.5">Generate & Schedule Custom Analytics</h2>
        </div>
        <button
          @click="emit('close')"
          class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition font-bold"
        >
          ✕
        </button>
      </div>

      <!-- Body Content -->
      <div class="p-6 overflow-y-auto space-y-5 text-xs text-slate-700 dark:text-slate-200">
        <!-- 1. Report Selection -->
        <div class="space-y-2">
          <label class="font-extrabold text-slate-400 uppercase tracking-wider text-[11px]">Select Report Type:</label>
          <div class="grid grid-cols-3 gap-3">
            <button
              @click="reportType = 'student'"
              :class="['py-2.5 rounded-xl font-bold border transition flex items-center justify-center gap-1.5', reportType === 'student' ? 'bg-blue-50 border-blue-500 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-slate-50 dark:bg-gray-700 border-slate-200 dark:border-gray-600']"
            >
              <span>👨‍🎓</span> Student Report
            </button>
            <button
              @click="reportType = 'course'"
              :class="['py-2.5 rounded-xl font-bold border transition flex items-center justify-center gap-1.5', reportType === 'course' ? 'bg-indigo-50 border-indigo-500 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'bg-slate-50 dark:bg-gray-700 border-slate-200 dark:border-gray-600']"
            >
              <span>📚</span> Course Report
            </button>
            <button
              @click="reportType = 'quiz'"
              :class="['py-2.5 rounded-xl font-bold border transition flex items-center justify-center gap-1.5', reportType === 'quiz' ? 'bg-purple-50 border-purple-500 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-slate-50 dark:bg-gray-700 border-slate-200 dark:border-gray-600']"
            >
              <span>📝</span> Quiz Report
            </button>
          </div>
        </div>

        <!-- 2. Format Selection -->
        <div class="space-y-2">
          <label class="font-extrabold text-slate-400 uppercase tracking-wider text-[11px]">Format:</label>
          <div class="grid grid-cols-3 gap-3">
            <button
              @click="format = 'pdf'"
              :class="['py-2 rounded-xl font-bold border transition flex items-center justify-center gap-1.5', format === 'pdf' ? 'bg-rose-50 border-rose-500 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-slate-50 dark:bg-gray-700 border-slate-200 dark:border-gray-600']"
            >
              <span>📄</span> PDF
            </button>
            <button
              @click="format = 'excel'"
              :class="['py-2 rounded-xl font-bold border transition flex items-center justify-center gap-1.5', format === 'excel' ? 'bg-emerald-50 border-emerald-500 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-slate-50 dark:bg-gray-700 border-slate-200 dark:border-gray-600']"
            >
              <span>📊</span> Excel
            </button>
            <button
              @click="format = 'csv'"
              :class="['py-2 rounded-xl font-bold border transition flex items-center justify-center gap-1.5', format === 'csv' ? 'bg-blue-50 border-blue-500 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-slate-50 dark:bg-gray-700 border-slate-200 dark:border-gray-600']"
            >
              <span>📑</span> CSV
            </button>
          </div>
        </div>

        <!-- 3. Language Options -->
        <div class="space-y-2">
          <label class="font-extrabold text-slate-400 uppercase tracking-wider text-[11px]">Language:</label>
          <div class="flex gap-3">
            <button
              @click="language = 'km'"
              :class="['px-4 py-2 rounded-xl font-bold border transition flex items-center gap-1.5', language === 'km' ? 'bg-blue-50 border-blue-500 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-slate-50 dark:bg-gray-700 border-slate-200 dark:border-gray-600']"
            >
              <span>🇰🇭</span> Khmer
            </button>
            <button
              @click="language = 'en'"
              :class="['px-4 py-2 rounded-xl font-bold border transition flex items-center gap-1.5', language === 'en' ? 'bg-blue-50 border-blue-500 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-slate-50 dark:bg-gray-700 border-slate-200 dark:border-gray-600']"
            >
              <span>🇬🇧</span> English
            </button>
            <button
              @click="language = 'bilingual'"
              :class="['px-4 py-2 rounded-xl font-bold border transition flex items-center gap-1.5', language === 'bilingual' ? 'bg-purple-50 border-purple-500 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-slate-50 dark:bg-gray-700 border-slate-200 dark:border-gray-600']"
            >
              <span>🌐</span> Bilingual (🇰🇭/🇬🇧)
            </button>
          </div>
        </div>

        <!-- 4. Include Checkboxes -->
        <div class="p-4 bg-slate-50 dark:bg-gray-700/50 rounded-2xl border border-slate-200/80 dark:border-gray-600 space-y-2">
          <label class="font-extrabold text-slate-400 uppercase tracking-wider text-[11px] block mb-1">Content Inclusions:</label>
          <div class="grid grid-cols-2 gap-2 font-semibold">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="includeCharts" class="w-4 h-4 rounded text-blue-600">
              <span>☑️ Visual Charts</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="includeTables" class="w-4 h-4 rounded text-blue-600">
              <span>☑️ Data Tables</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="includeStudentDetails" class="w-4 h-4 rounded text-blue-600">
              <span>☑️ Student Details</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="includeComments" class="w-4 h-4 rounded text-blue-600">
              <span>☑️ Teacher Comments</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer col-span-2">
              <input type="checkbox" v-model="includeAiInsights" class="w-4 h-4 rounded text-blue-600">
              <span>☑️ AI Insights & Action Recommendations</span>
            </label>
          </div>
        </div>

        <!-- 5. Scheduled Report Feature -->
        <div class="p-4 bg-amber-50/80 dark:bg-amber-900/20 rounded-2xl border border-amber-200/80 dark:border-amber-700/50 space-y-3">
          <div class="flex items-center justify-between">
            <span class="font-extrabold text-amber-900 dark:text-amber-300 flex items-center gap-1.5">
              <span>📅 Scheduled Recurring Reports</span>
            </span>
            <input type="checkbox" v-model="scheduledReport" class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500">
          </div>
          <p class="text-[11px] text-amber-800 dark:text-amber-200">
            Automatically generate and email this report to teacher & administration every week or month.
          </p>
          <div v-if="scheduledReport" class="flex items-center gap-3 pt-1">
            <span class="font-bold">Frequency:</span>
            <button
              @click="scheduleFrequency = 'weekly'"
              :class="['px-3 py-1 rounded-lg font-bold border text-xs', scheduleFrequency === 'weekly' ? 'bg-amber-500 text-white border-amber-600' : 'bg-white dark:bg-gray-800']"
            >
              Weekly Email
            </button>
            <button
              @click="scheduleFrequency = 'monthly'"
              :class="['px-3 py-1 rounded-lg font-bold border text-xs', scheduleFrequency === 'monthly' ? 'bg-amber-500 text-white border-amber-600' : 'bg-white dark:bg-gray-800']"
            >
              Monthly Email
            </button>
          </div>
        </div>

        <!-- Actions -->
        <div class="pt-2 flex flex-wrap gap-2.5">
          <button
            @click="handleGenerate"
            class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-extrabold text-xs shadow-md flex items-center justify-center gap-1.5 transition"
          >
            <span>⚡</span> Generate & Download
          </button>

          <button
            @click="handleGenerate"
            class="px-5 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-extrabold text-xs shadow-md flex items-center justify-center gap-1.5 transition"
          >
            <span>📧</span> Schedule Email
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
