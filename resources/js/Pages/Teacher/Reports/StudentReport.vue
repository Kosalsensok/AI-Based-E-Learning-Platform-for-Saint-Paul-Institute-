<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  stats: {
    total_students: number
    completed: number
    in_progress: number
    not_started: number
  }
  students: Array<any>
}>()

const emit = defineEmits(['viewStudent', 'exportStudentPdf', 'exportStudentExcel', 'emailStudent'])

const searchQuery = ref('')
const selectedMajor = ref('All Majors')
const selectedProgress = ref('All Progress')
const selectedPayment = ref('All Payments')
const selectedSemester = ref('Semester 1')
const savedTemplate = ref('Default')

const filteredStudents = computed(() => {
  return props.students.filter(s => {
    const matchesSearch = s.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          s.major.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchesMajor = selectedMajor.value === 'All Majors' || s.major === selectedMajor.value
    const matchesPayment = selectedPayment.value === 'All Payments' || s.payment === selectedPayment.value

    return matchesSearch && matchesMajor && matchesPayment
  })
})

const getPaymentBadge = (status: string) => {
  switch (status) {
    case 'paid':
      return { text: '✅ Paid', class: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 font-extrabold' }
    case 'pending':
      return { text: '⏳ Pending', class: 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 font-extrabold' }
    default:
      return { text: '🔴 Unpaid', class: 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 font-extrabold animate-pulse' }
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header & Saved Templates Bar -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-100 dark:border-gray-700 pb-3">
        <div>
          <h2 class="text-base font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
            <span>👨‍🎓 STUDENT REPORT</span>
          </h2>
          <p class="text-xs text-slate-500">
            Individual student performance analytics (Progress, Learning Time, Quiz Score, Assignments, Payment & Certificates).
          </p>
        </div>

        <!-- Saved Templates -->
        <div class="flex items-center gap-2 text-xs">
          <span class="font-extrabold text-slate-500">🔖 Template:</span>
          <select
            v-model="savedTemplate"
            class="px-3 py-1.5 bg-slate-100 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl font-bold text-slate-700 dark:text-white"
          >
            <option value="Default">Default View</option>
            <option value="At-Risk IT Students">🔴 At-Risk IT Students</option>
            <option value="High Performers">⭐ High Performers (&gt;90%)</option>
            <option value="Pending Payments">⏳ Pending Payments</option>
          </select>
        </div>
      </div>

      <!-- Filter Controls Toolbar -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="🔍 Search Student Name..."
          class="px-3.5 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl font-medium text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500"
        >

        <select
          v-model="selectedMajor"
          class="px-3.5 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl font-bold text-slate-800 dark:text-white"
        >
          <option value="All Majors">All Majors ▼</option>
          <option value="IT & Networking">IT & Networking</option>
          <option value="Computer Science">Computer Science</option>
          <option value="MIS">MIS</option>
          <option value="Telecom">Telecom</option>
          <option value="Software Eng">Software Eng</option>
        </select>

        <select
          v-model="selectedProgress"
          class="px-3.5 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl font-bold text-slate-800 dark:text-white"
        >
          <option value="All Progress">All Progress ▼</option>
          <option value="Completed">Completed (&gt;90%)</option>
          <option value="In Progress">In Progress (40-90%)</option>
          <option value="Low Progress">Low Progress (&lt;40%)</option>
        </select>

        <select
          v-model="selectedPayment"
          class="px-3.5 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl font-bold text-slate-800 dark:text-white"
        >
          <option value="All Payments">All Payments ▼</option>
          <option value="paid">Paid</option>
          <option value="pending">Pending</option>
          <option value="unpaid">Unpaid</option>
        </select>

        <select
          v-model="selectedSemester"
          class="px-3.5 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl font-bold text-slate-800 dark:text-white"
        >
          <option value="Semester 1">Semester 1 ▼</option>
          <option value="Semester 2">Semester 2</option>
        </select>
      </div>

      <!-- Metric Summary Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-1 text-center">
        <div class="p-3.5 rounded-2xl bg-blue-50/70 dark:bg-blue-900/20 border border-blue-200/60 dark:border-blue-800/40">
          <span class="text-[10px] font-extrabold text-blue-800 dark:text-blue-300 uppercase">Students</span>
          <p class="text-2xl font-black text-blue-700 dark:text-blue-400 mt-0.5">{{ stats?.total_students || 120 }}</p>
        </div>

        <div class="p-3.5 rounded-2xl bg-emerald-50/70 dark:bg-emerald-900/20 border border-emerald-200/60 dark:border-emerald-800/40">
          <span class="text-[10px] font-extrabold text-emerald-800 dark:text-emerald-300 uppercase">Completed</span>
          <p class="text-2xl font-black text-emerald-700 dark:text-emerald-400 mt-0.5">{{ stats?.completed || 26 }}</p>
        </div>

        <div class="p-3.5 rounded-2xl bg-amber-50/70 dark:bg-amber-900/20 border border-amber-200/60 dark:border-amber-800/40">
          <span class="text-[10px] font-extrabold text-amber-800 dark:text-amber-300 uppercase">In Progress</span>
          <p class="text-2xl font-black text-amber-700 dark:text-amber-400 mt-0.5">{{ stats?.in_progress || 82 }}</p>
        </div>

        <div class="p-3.5 rounded-2xl bg-rose-50/70 dark:bg-rose-900/20 border border-rose-200/60 dark:border-rose-800/40">
          <span class="text-[10px] font-extrabold text-rose-800 dark:text-rose-300 uppercase">Not Started</span>
          <p class="text-2xl font-black text-rose-700 dark:text-rose-400 mt-0.5">{{ stats?.not_started || 12 }}</p>
        </div>
      </div>
    </div>

    <!-- Student Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden text-xs">
      <div class="p-4 bg-slate-50 dark:bg-gray-700/50 border-b border-slate-200/80 dark:border-gray-700 flex justify-between items-center">
        <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">👨‍🎓 Student Roster & Individual Metrics</h3>
        <span class="text-slate-500 font-medium">Showing {{ filteredStudents.length }} students</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-100/70 dark:bg-gray-700/80 text-slate-500 font-extrabold uppercase border-b">
              <th class="p-3.5">Student</th>
              <th class="p-3.5">Major</th>
              <th class="p-3.5">Progress</th>
              <th class="p-3.5">Quiz Score</th>
              <th class="p-3.5">Learning Time</th>
              <th class="p-3.5">Payment</th>
              <th class="p-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
            <tr v-for="s in filteredStudents" :key="s.id" class="hover:bg-slate-50/70 dark:hover:bg-gray-700/50 transition">
              <td class="p-3.5 font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                <span class="text-base">{{ s.avatar }}</span>
                <span>{{ s.name }}</span>
              </td>
              <td class="p-3.5 font-semibold text-slate-600 dark:text-slate-300">{{ s.major }}</td>
              <td class="p-3.5">
                <div class="flex items-center gap-2">
                  <div class="w-16 bg-slate-200 dark:bg-gray-600 h-2 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full" :style="{ width: s.progress + '%' }"></div>
                  </div>
                  <span class="font-bold text-slate-800 dark:text-white">{{ s.progress }}%</span>
                </div>
              </td>
              <td class="p-3.5 font-bold text-blue-600 dark:text-blue-400">{{ s.quiz_score }}%</td>
              <td class="p-3.5 font-bold text-indigo-600 dark:text-indigo-400">{{ s.learning_time }}</td>
              <td class="p-3.5">
                <span class="px-2.5 py-1 rounded-full text-[10px]" :class="getPaymentBadge(s.payment).class">
                  {{ getPaymentBadge(s.payment).text }}
                </span>
              </td>
              <td class="p-3.5 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    @click="emit('viewStudent', s)"
                    class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 rounded-lg font-extrabold transition flex items-center gap-1"
                  >
                    <span>👁</span> View
                  </button>

                  <button
                    @click="emit('exportStudentPdf', s)"
                    class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 rounded-lg font-bold transition flex items-center gap-1"
                  >
                    <span>📄</span> PDF
                  </button>

                  <button
                    @click="emit('exportStudentExcel', s)"
                    class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 rounded-lg font-bold transition flex items-center gap-1"
                  >
                    <span>📊</span> Excel
                  </button>

                  <button
                    @click="emit('emailStudent', s)"
                    class="px-2.5 py-1 bg-purple-50 hover:bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 rounded-lg font-bold transition flex items-center gap-1"
                  >
                    <span>📧</span> Email
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
