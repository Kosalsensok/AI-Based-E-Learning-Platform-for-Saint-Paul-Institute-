<script setup lang="ts">
import { ref } from 'vue'

const emit = defineEmits<{
  (e: 'generateReport', config: any): void
  (e: 'saveSchedule', schedule: any): void
  (e: 'downloadExisting', reportId: number): void
}>()

const selectedModules = ref(['student', 'teacher', 'course', 'quiz', 'payment'])
const startDate = ref('2025-06-01')
const endDate = ref('2025-06-30')
const exportFormat = ref('csv')
const emailRecipients = ref('admin@elms.edu.kh, dean@elms.edu.kh')

const scheduleFrequency = ref('weekly')
const scheduleTime = ref('08:00')
const scheduleEmails = ref('management@elms.edu.kh')

const recentReports = [
  { id: 1, name: 'Financial_Revenue_Report_Q2_2025.csv', type: 'Payment', date: '2025-06-25 09:30', format: 'CSV', size: '1.2 MB' },
  { id: 2, name: 'Student_Engagement_Funnel_June.pdf', type: 'Student', date: '2025-06-20 14:15', format: 'PDF', size: '3.4 MB' },
  { id: 3, name: 'Teacher_Performance_Evaluation_2025.xlsx', type: 'Teacher', date: '2025-06-15 11:00', format: 'Excel', size: '2.8 MB' },
  { id: 4, name: 'Quiz_Difficulty_And_Pass_Rate.csv', type: 'Quiz', date: '2025-06-10 16:45', format: 'CSV', size: '890 KB' },
]

function handleGenerate() {
  emit('generateReport', {
    modules: selectedModules.value,
    startDate: startDate.value,
    endDate: endDate.value,
    format: exportFormat.value,
    recipients: emailRecipients.value,
  })
}

function handleSaveSchedule() {
  emit('saveSchedule', {
    frequency: scheduleFrequency.value,
    time: scheduleTime.value,
    emails: scheduleEmails.value,
  })
}
</script>

<template>
  <div class="space-y-6 text-xs font-sans">
    <!-- Header Banner -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl space-y-1">
      <h3 class="text-base font-black text-white flex items-center gap-2">
        <span>📤</span> EXPORT SYSTEM REPORTS & AUTOMATION
      </h3>
      <p class="text-slate-400 text-xs">Generate custom data exports, download system audit logs, or configure scheduled automated email reports.</p>
    </div>

    <!-- Custom Report Generator & Scheduled Automation Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <!-- 1. Custom Report Generator -->
      <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5 flex items-center gap-2">
          <span>⚙️</span> CUSTOM REPORT GENERATOR
        </h4>

        <div class="space-y-3.5 bg-[#121827] p-4 rounded-xl border border-slate-700/80">
          <div>
            <label class="block text-slate-300 font-bold mb-1.5">Include Modules:</label>
            <div class="flex flex-wrap items-center gap-3 text-slate-200">
              <label class="flex items-center gap-1.5 cursor-pointer">
                <input v-model="selectedModules" value="student" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700" />
                <span>Student</span>
              </label>
              <label class="flex items-center gap-1.5 cursor-pointer">
                <input v-model="selectedModules" value="teacher" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700" />
                <span>Teacher</span>
              </label>
              <label class="flex items-center gap-1.5 cursor-pointer">
                <input v-model="selectedModules" value="course" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700" />
                <span>Course</span>
              </label>
              <label class="flex items-center gap-1.5 cursor-pointer">
                <input v-model="selectedModules" value="quiz" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700" />
                <span>Quiz</span>
              </label>
              <label class="flex items-center gap-1.5 cursor-pointer">
                <input v-model="selectedModules" value="payment" type="checkbox" class="w-4 h-4 text-purple-600 rounded bg-slate-900 border-slate-700" />
                <span>Payment</span>
              </label>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-slate-400 mb-1 font-semibold">Start Date:</label>
              <input v-model="startDate" type="date" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-1.5 focus:border-purple-500 focus:outline-none" />
            </div>
            <div>
              <label class="block text-slate-400 mb-1 font-semibold">End Date:</label>
              <input v-model="endDate" type="date" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-1.5 focus:border-purple-500 focus:outline-none" />
            </div>
          </div>

          <div>
            <label class="block text-slate-400 mb-1 font-semibold">Export Format:</label>
            <div class="flex items-center gap-3">
              <label class="flex items-center gap-1.5 cursor-pointer">
                <input v-model="exportFormat" value="csv" type="radio" name="format" class="text-purple-600" />
                <span class="text-emerald-400 font-bold">CSV / Excel</span>
              </label>
              <label class="flex items-center gap-1.5 cursor-pointer">
                <input v-model="exportFormat" value="pdf" type="radio" name="format" class="text-purple-600" />
                <span class="text-cyan-300 font-bold">PDF Document</span>
              </label>
              <label class="flex items-center gap-1.5 cursor-pointer">
                <input v-model="exportFormat" value="json" type="radio" name="format" class="text-purple-600" />
                <span class="text-purple-300 font-bold">Raw JSON</span>
              </label>
            </div>
          </div>

          <div>
            <label class="block text-slate-400 mb-1 font-semibold">Email Recipients (Optional):</label>
            <input v-model="emailRecipients" type="text" placeholder="Separate emails by comma..." class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-1.5 focus:border-purple-500 focus:outline-none" />
          </div>

          <button @click="handleGenerate" class="w-full py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl shadow-lg shadow-purple-600/30 transition-all flex items-center justify-center gap-2">
            <span>📤</span> Generate & Export Report Now
          </button>
        </div>
      </div>

      <!-- 2. Scheduled Automation -->
      <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5 flex items-center gap-2">
          <span>⏰</span> SCHEDULED AUTO-REPORT AUTOMATION
        </h4>

        <div class="space-y-3.5 bg-[#121827] p-4 rounded-xl border border-slate-700/80">
          <div>
            <label class="block text-slate-400 mb-1 font-semibold">Frequency:</label>
            <select v-model="scheduleFrequency" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-1.5 focus:border-purple-500 focus:outline-none">
              <option value="daily">Daily Morning Digest (08:00 AM)</option>
              <option value="weekly">Weekly Progress Report (Every Monday)</option>
              <option value="monthly">Monthly Executive Summary (1st of Month)</option>
            </select>
          </div>

          <div>
            <label class="block text-slate-400 mb-1 font-semibold">Delivery Time:</label>
            <input v-model="scheduleTime" type="time" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-1.5 focus:border-purple-500 focus:outline-none" />
          </div>

          <div>
            <label class="block text-slate-400 mb-1 font-semibold">Auto-Email Recipients:</label>
            <input v-model="scheduleEmails" type="text" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-1.5 focus:border-purple-500 focus:outline-none" />
          </div>

          <div class="p-3 bg-purple-950/40 border border-purple-500/30 rounded-xl text-slate-300 text-[11px] font-medium">
            ℹ️ Scheduled reports will automatically compile all active analytics datasets and dispatch PDF/CSV summaries to the management email list.
          </div>

          <button @click="handleSaveSchedule" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 font-bold rounded-xl transition-all flex items-center justify-center gap-2">
            <span>💾</span> Save Schedule Automation
          </button>
        </div>
      </div>
    </div>

    <!-- Recent Downloadable Reports Table -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
        📁 RECENTLY GENERATED REPORTS
      </h4>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80 bg-[#121827]">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-700/80">
            <tr>
              <th class="p-3">Report Name</th>
              <th class="p-3">Module Type</th>
              <th class="p-3">Generated Date</th>
              <th class="p-3">Format</th>
              <th class="p-3">File Size</th>
              <th class="p-3 text-right">Download</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/80 font-medium">
            <tr v-for="r in recentReports" :key="r.id" class="hover:bg-slate-800/40 transition-colors">
              <td class="p-3 font-bold text-white flex items-center gap-2">
                <span>📄</span> {{ r.name }}
              </td>
              <td class="p-3 text-purple-300 font-semibold">{{ r.type }}</td>
              <td class="p-3 text-slate-400 font-mono">{{ r.date }}</td>
              <td class="p-3 font-bold" :class="r.format === 'CSV' ? 'text-emerald-400' : (r.format === 'PDF' ? 'text-cyan-300' : 'text-amber-300')">
                {{ r.format }}
              </td>
              <td class="p-3 text-slate-400 font-mono">{{ r.size }}</td>
              <td class="p-3 text-right">
                <button @click="emit('downloadExisting', r.id)" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-lg text-xs font-semibold">
                  📥 Download
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
