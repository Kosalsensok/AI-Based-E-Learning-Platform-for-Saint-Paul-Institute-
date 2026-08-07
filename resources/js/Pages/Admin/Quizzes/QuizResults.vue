<script setup lang="ts">
import { ref, computed } from 'vue'
import type { ResultItem } from './types'

const props = defineProps<{
  results: ResultItem[]
}>()

const emit = defineEmits<{
  (e: 'openGrading', item: any): void
}>()

const searchQuery = ref('')
const selectedQuizFilter = ref('all')
const selectedStatusFilter = ref('all')

const quizOptions = computed(() => {
  const quizzes = new Set(props.results.map(r => r.quiz))
  return Array.from(quizzes)
})

const filteredResults = computed(() => {
  return props.results.filter(r => {
    const matchSearch = searchQuery.value === '' ||
      r.student.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      r.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      r.id.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      r.quiz.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchQuiz = selectedQuizFilter.value === 'all' || r.quiz === selectedQuizFilter.value
    
    let matchStatus = true
    if (selectedStatusFilter.value === 'needs_review') {
      matchStatus = r.graded_by === 'Needs Review' || (!r.passed && r.score === 0)
    } else if (selectedStatusFilter.value === 'passed') {
      matchStatus = r.passed && r.graded_by !== 'Needs Review'
    } else if (selectedStatusFilter.value === 'failed') {
      matchStatus = !r.passed && r.graded_by !== 'Needs Review'
    }

    return matchSearch && matchQuiz && matchStatus
  })
})

// Metrics computation
const totalSubmissions = computed(() => props.results.length)
const pendingCount = computed(() => props.results.filter(r => r.graded_by === 'Needs Review' || (!r.passed && r.score === 0)).length)
const avgScore = computed(() => {
  if (props.results.length === 0) return 0
  const sum = props.results.reduce((acc, r) => acc + r.score, 0)
  return Math.round(sum / props.results.length)
})
const passRate = computed(() => {
  if (props.results.length === 0) return 0
  const passed = props.results.filter(r => r.passed).length
  return Math.round((passed / props.results.length) * 100)
})

const handleExport = () => {
  const headers = ['Attempt ID', 'Student', 'Email', 'Quiz', 'Course', 'Score', 'Passed', 'Time Spent', 'Submitted Date', 'Graded By']
  const rows = filteredResults.value.map(r => [
    r.id,
    `"${r.student}"`,
    `"${r.email}"`,
    `"${r.quiz}"`,
    `"${r.course}"`,
    `${r.score}%`,
    r.passed ? 'Yes' : 'No',
    `"${r.time_spent}"`,
    `"${r.submitted_at}"`,
    `"${r.graded_by}"`
  ])
  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n')
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement('a')
  link.setAttribute('href', encodedUri)
  link.setAttribute('download', `quiz_results_export_${Date.now()}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}
</script>

<template>
  <div class="space-y-4 text-xs font-sans">
    <!-- ── 1. SUMMARY METRIC CARDS ── -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-lg">
        <span class="text-xs text-slate-400 font-semibold">📝 Total Submissions</span>
        <h3 class="text-2xl font-black text-white mt-1">{{ totalSubmissions }}</h3>
        <p class="text-[11px] text-purple-400 mt-1">Student attempts logged</p>
      </div>
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-lg">
        <span class="text-xs text-slate-400 font-semibold">⚠️ Pending Review</span>
        <h3 class="text-2xl font-black text-amber-400 mt-1">{{ pendingCount }}</h3>
        <p class="text-[11px] text-amber-300/80 mt-1">Needs teacher action</p>
      </div>
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-lg">
        <span class="text-xs text-slate-400 font-semibold">📊 Average Score</span>
        <h3 class="text-2xl font-black text-indigo-400 mt-1">{{ avgScore }}%</h3>
        <p class="text-[11px] text-slate-400 mt-1">Across all submissions</p>
      </div>
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-lg">
        <span class="text-xs text-slate-400 font-semibold">🎯 Pass Rate</span>
        <h3 class="text-2xl font-black text-emerald-400 mt-1">{{ passRate }}%</h3>
        <p class="text-[11px] text-slate-400 mt-1">Met passing criteria</p>
      </div>
    </div>

    <!-- ── 2. CLEAN SEARCH & FILTER BAR ── -->
    <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-3.5 shadow-xl">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <!-- Search Input -->
        <div class="relative flex-1 w-full">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 14 0 0114 0z" />
            </svg>
          </span>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search student name, email, attempt ID..."
            class="bg-slate-900/90 text-slate-200 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-purple-500 w-full transition-all"
          />
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto shrink-0 flex-wrap sm:flex-nowrap justify-end">
          <!-- Quiz Filter -->
          <select v-model="selectedQuizFilter" class="bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-purple-500 cursor-pointer">
            <option value="all" class="bg-[#121827] text-slate-200">📚 All Quizzes</option>
            <option v-for="q in quizOptions" :key="q" :value="q" class="bg-[#121827] text-slate-200">{{ q }}</option>
          </select>

          <!-- Status Filter -->
          <select v-model="selectedStatusFilter" class="bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-purple-500 cursor-pointer">
            <option value="all" class="bg-[#121827] text-slate-200">⚡ All Statuses</option>
            <option value="needs_review" class="bg-[#121827] text-slate-200">⚠️ Needs Review</option>
            <option value="passed" class="bg-[#121827] text-slate-200">✅ Passed</option>
            <option value="failed" class="bg-[#121827] text-slate-200">❌ Failed</option>
          </select>

          <!-- Export Button -->
          <button
            @click="handleExport"
            class="px-3.5 py-2 bg-slate-900 hover:bg-slate-700 text-slate-200 border border-slate-700 hover:border-slate-600 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shrink-0 shadow-sm active:scale-95"
          >
            <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            <span>Export</span>
          </button>
        </div>
      </div>
    </div>

    <!-- ── 3. ATTEMPTS & RESULTS DATA TABLE ── -->
    <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl shadow-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="bg-slate-900/90 text-slate-400 uppercase font-bold text-[11px] border-b border-slate-700 select-none tracking-wider">
              <th class="py-3 px-3 pl-4 whitespace-nowrap">Attempt ID</th>
              <th class="py-3 px-3 min-w-[140px]">Student Name</th>
              <th class="py-3 px-3 min-w-[160px]">Quiz / Assessment</th>
              <th class="py-3 px-3 whitespace-nowrap">Score</th>
              <th class="py-3 px-3 whitespace-nowrap">Time Spent</th>
              <th class="py-3 px-3 whitespace-nowrap">Submitted Date</th>
              <th class="py-3 px-3 whitespace-nowrap">Graded By</th>
              <th class="py-3 px-3 pr-4 text-right whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60 font-medium">
            <tr v-for="res in filteredResults" :key="res.id" class="hover:bg-slate-700/30 transition-colors group">
              <!-- 1. ATTEMPT ID -->
              <td class="py-3 px-3 pl-4 font-mono text-purple-300 font-extrabold text-[11px] whitespace-nowrap">
                {{ res.id }}
              </td>

              <!-- 2. STUDENT NAME & EMAIL -->
              <td class="py-3 px-3 min-w-[140px]">
                <p class="font-bold text-white group-hover:text-purple-300 transition-colors leading-tight">{{ res.student }}</p>
                <span class="text-[10px] text-slate-400 block mt-0.5 font-mono truncate max-w-[160px]">{{ res.email }}</span>
              </td>

              <!-- 3. QUIZ / ASSESSMENT & COURSE -->
              <td class="py-3 px-3 min-w-[160px]">
                <p class="font-bold text-slate-200 leading-tight">{{ res.quiz }}</p>
                <span class="text-[10px] text-purple-400 block font-mono mt-0.5">📚 {{ res.course }}</span>
              </td>

              <!-- 4. SCORE & STATUS BADGE -->
              <td class="py-3 px-3 whitespace-nowrap">
                <div class="flex items-center gap-1.5">
                  <span class="font-black text-xs" :class="res.graded_by === 'Needs Review' ? 'text-amber-400' : (res.passed ? 'text-emerald-400' : 'text-red-400')">
                    {{ res.score }}%
                  </span>
                  <span
                    class="px-1.5 py-0.5 rounded text-[9px] font-extrabold border"
                    :class="
                      res.graded_by === 'Needs Review' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' :
                      (res.passed ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-red-500/20 text-red-300 border-red-500/40')
                    "
                  >
                    {{ res.graded_by === 'Needs Review' ? 'Pending' : (res.passed ? 'Pass' : 'Fail') }}
                  </span>
                </div>
              </td>

              <!-- 5. TIME SPENT -->
              <td class="py-3 px-3 text-slate-400 font-mono text-[11px] whitespace-nowrap">
                ⏱️ {{ res.time_spent }}
              </td>

              <!-- 6. SUBMITTED DATE -->
              <td class="py-3 px-3 text-slate-300 font-mono text-[11px] whitespace-nowrap">
                📅 {{ res.submitted_at }}
              </td>

              <!-- 7. GRADED BY STATUS BADGE -->
              <td class="py-3 px-3 whitespace-nowrap">
                <span
                  v-if="res.graded_by === 'Needs Review'"
                  class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center gap-1 w-max animate-pulse shadow-sm"
                >
                  <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                  </svg>
                  <span>Needs Review</span>
                </span>
                <span
                  v-else
                  class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-900/90 text-slate-300 border border-slate-700 flex items-center gap-1 w-max"
                >
                  <svg class="w-3 h-3 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>{{ res.graded_by }}</span>
                </span>
              </td>

              <!-- 8. ACTIONS: CONTEXT-AWARE BUTTONS -->
              <td class="py-3 px-3 pr-4 text-right whitespace-nowrap">
                <!-- For Pending Review: Primary Highlight Button "✍️ Grade Now" -->
                <button
                  v-if="res.graded_by === 'Needs Review'"
                  @click="emit('openGrading', res)"
                  class="px-2.5 py-1 bg-gradient-to-r from-purple-600 to-indigo-600 hover:brightness-110 text-white rounded-xl text-[11px] font-bold transition-all flex items-center gap-1 ml-auto cursor-pointer shadow-md shadow-purple-600/30 active:scale-95 ring-1 ring-purple-400/40"
                >
                  <svg class="w-3.5 h-3.5 text-purple-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                  </svg>
                  <span>Grade Now</span>
                </button>

                <!-- For Graded / Completed: Secondary Ghost Button "👁️ View Details" -->
                <button
                  v-else
                  @click="emit('openGrading', res)"
                  class="px-2.5 py-1 bg-slate-900/90 hover:bg-slate-700/80 text-purple-300 border border-slate-700 hover:border-purple-500/50 rounded-xl text-[11px] font-semibold transition-all flex items-center gap-1 ml-auto cursor-pointer shadow-sm active:scale-95"
                >
                  <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12c.077-.19.152-.38.229-.567C3.864 7.643 7.625 4.5 12 4.5c4.375 0 8.136 3.143 9.735 6.933.077.186.152.376.229.567-.077.19-.152.38-.229.567-1.599 3.79-5.36 6.933-9.735 6.933-4.375 0-8.136-3.143-9.735-6.933a9.344 9.344 0 01-.229-.567z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <span>View Details</span>
                </button>
              </td>
            </tr>

            <!-- Empty State -->
            <tr v-if="filteredResults.length === 0">
              <td colspan="8" class="p-8 text-center text-slate-400">
                No attempt results found matching your search or filter criteria.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
