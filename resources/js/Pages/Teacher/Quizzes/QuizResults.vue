<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  courses?: Array<any>
}>()

const searchQuery = ref('')
const statusFilter = ref('All')
const aiFilter = ref('All')
const selectedCourseId = ref<any>('All')

interface StudentResult {
  id: number
  student_name: string
  student_id: string
  quiz_name: string
  course_name: string
  type: string
  typeName: string
  score: number
  grade: string
  attempts: string
  time_spent: string
  status: string
  submitted_at: string
  essay_answer?: string | null
  ai_suggested_mark?: number | null
  teacher_mark?: number | null
}

const studentResults = ref<StudentResult[]>([
  {
    id: 1,
    student_name: 'Chan Dara',
    student_id: 'STU241001',
    quiz_name: 'Post-Test Module 1: Intro to C & Networks',
    course_name: 'C Programming Basics',
    type: 'post_test',
    typeName: 'Post-Test',
    score: 85,
    grade: 'A',
    attempts: '1 / 1',
    time_spent: '22 mins',
    status: 'Graded',
    submitted_at: '2026-06-15 10:30 AM',
    essay_answer: 'Variables store data values in RAM. Example: int x = 10; float rate = 0.05;',
    ai_suggested_mark: 5,
    teacher_mark: 5,
  },
  {
    id: 2,
    student_name: 'Keo Sokha',
    student_id: 'STU241002',
    quiz_name: 'Post-Test Module 1: Intro to C & Networks',
    course_name: 'C Programming Basics',
    type: 'post_test',
    typeName: 'Post-Test',
    score: 62,
    grade: 'C',
    attempts: '1 / 1',
    time_spent: '28 mins',
    status: 'Pending Essay',
    submitted_at: '2026-06-15 11:15 AM',
    essay_answer: 'Variables are used to hold numbers and text during execution in computer memory.',
    ai_suggested_mark: 4,
    teacher_mark: null,
  },
  {
    id: 3,
    student_name: 'Vannak Bopha',
    student_id: 'STU241003',
    quiz_name: 'Variables Practice Drill',
    course_name: 'C Programming Basics',
    type: 'practice',
    typeName: 'Practice',
    score: 95,
    grade: 'A',
    attempts: '3 / Unlimited',
    time_spent: '14 mins',
    status: 'Graded',
    submitted_at: '2026-06-14 02:40 PM',
    essay_answer: null,
    ai_suggested_mark: null,
    teacher_mark: null,
  },
  {
    id: 4,
    student_name: 'Meng Voleak',
    student_id: 'STU241004',
    quiz_name: 'Pre-Test Module 1: Baseline Evaluation',
    course_name: 'C Programming Basics',
    type: 'pre_test',
    typeName: 'Pre-Test',
    score: 38,
    grade: 'F',
    attempts: '1 / 1',
    time_spent: '18 mins',
    status: 'Graded',
    submitted_at: '2026-06-12 09:00 AM',
    essay_answer: null,
    ai_suggested_mark: null,
    teacher_mark: null,
  },
  {
    id: 5,
    student_name: 'Sok Piseth',
    student_id: 'STU241005',
    quiz_name: 'Final Assessment Examination',
    course_name: 'IT & Networking Masterclass',
    type: 'post_test',
    typeName: 'Post-Test',
    score: 91,
    grade: 'A',
    attempts: '1 / 1',
    time_spent: '35 mins',
    status: 'Graded',
    submitted_at: '2026-06-16 04:20 PM',
    essay_answer: 'Full explanation of subnetting and CIDR notation calculations.',
    ai_suggested_mark: 5,
    teacher_mark: 5,
  },
  {
    id: 6,
    student_name: 'Nhem Chanthy',
    student_id: 'STU241006',
    quiz_name: 'Coding Challenge 1: Linked Lists',
    course_name: 'Data Structures',
    type: 'coding',
    typeName: 'Coding',
    score: 35,
    grade: 'F',
    attempts: '2 / 3',
    time_spent: '42 mins',
    status: 'Graded',
    submitted_at: '2026-06-16 05:00 PM',
    essay_answer: null,
    ai_suggested_mark: null,
    teacher_mark: null,
  }
])

// AI Recommendation Helper based on E.LMS Thresholds
const getAiRecommendation = (score: number) => {
  if (score >= 80) {
    return {
      label: 'Advance',
      labelKh: 'រៀនបន្ត Module បន្ទាប់',
      badgeClass: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700',
      icon: '🚀',
      actionText: 'Advance to next module'
    }
  } else if (score >= 50) {
    return {
      label: 'Review',
      labelKh: 'រំលឹកមេរៀនឡើងវិញ',
      badgeClass: 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-300 dark:border-amber-700',
      icon: '📖',
      actionText: 'Review recommended content'
    }
  } else {
    return {
      label: 'Remedial',
      labelKh: 'ត្រូវការរៀនបំប៉នបន្ទាន់',
      badgeClass: 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 border border-rose-300 dark:border-rose-700',
      icon: '🚨',
      actionText: 'Remedial content required'
    }
  }
}

// 3 Metric Cards Computed
const averageScore = computed(() => {
  if (studentResults.value.length === 0) return '0.0%'
  const sum = studentResults.value.reduce((acc, r) => acc + r.score, 0)
  return (sum / studentResults.value.length).toFixed(1) + '%'
})

const completionRate = computed(() => {
  if (studentResults.value.length === 0) return '0%'
  const gradedCount = studentResults.value.filter(r => r.status === 'Graded').length
  return Math.round((gradedCount / studentResults.value.length) * 100) + '%'
})

const totalStudentsAttempted = computed(() => {
  const uniqueStudents = new Set(studentResults.value.map(r => r.student_id))
  return uniqueStudents.size
})

// Filtered Results
const filteredResults = computed(() => {
  return studentResults.value.filter(r => {
    const matchSearch = r.student_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        r.student_id.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        r.quiz_name.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchStatus = statusFilter.value === 'All' || r.status === statusFilter.value
    
    let matchAi = true
    if (aiFilter.value === 'Advance') matchAi = r.score >= 80
    else if (aiFilter.value === 'Review') matchAi = r.score >= 50 && r.score < 80
    else if (aiFilter.value === 'Remedial') matchAi = r.score < 50

    return matchSearch && matchStatus && matchAi
  })
})

// Grade Essay Modal State
const showGradeEssayModal = ref(false)
const gradingStudent = ref<StudentResult | null>(null)
const manualMarkInput = ref<number>(4)
const teacherFeedbackInput = ref('Good explanation with clear examples.')

const openGradeEssay = (r: StudentResult) => {
  gradingStudent.value = r
  manualMarkInput.value = r.ai_suggested_mark || 4
  showGradeEssayModal.value = true
}

const saveGrade = () => {
  if (gradingStudent.value) {
    gradingStudent.value.status = 'Graded'
    gradingStudent.value.teacher_mark = manualMarkInput.value
    gradingStudent.value.score = Math.min(100, gradingStudent.value.score + 10)
    gradingStudent.value.grade = gradingStudent.value.score >= 80 ? 'A' : gradingStudent.value.score >= 50 ? 'C' : 'F'
  }
  alert(`Grade saved successfully for ${gradingStudent.value?.student_name}!`)
  showGradeEssayModal.value = false
}

const allowRetake = (r: StudentResult) => {
  alert(`Allowed 1 additional retake attempt for ${r.student_name} (${r.student_id})!`)
}

// Real Export Functions
const exportCsv = () => {
  const headers = ['ID,Student Name,Student ID,Course,Quiz Name,Type,Score,Grade,AI Recommendation,Status,Attempts,Time Taken,Submitted At\n']
  const rows = studentResults.value.map(r => {
    const ai = getAiRecommendation(r.score).label
    return `"${r.id}","${r.student_name}","${r.student_id}","${r.course_name}","${r.quiz_name}","${r.typeName}","${r.score}%","${r.grade}","${ai}","${r.status}","${r.attempts}","${r.time_spent}","${r.submitted_at}"`
  })

  const csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + headers.concat(rows).join('\n')
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement('a')
  link.setAttribute('href', encodedUri)
  link.setAttribute('download', `elms-quiz-results-${new Date().toISOString().slice(0,10)}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const printOrExportPdf = () => {
  window.print()
}
</script>

<template>
  <div class="space-y-6">
    <!-- TOP 3 METRIC CARDS (Exact Spec Match) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <!-- 1. ពិន្ទុមធ្យម (Average Score) -->
      <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl border border-slate-200/80 dark:border-gray-700 shadow-sm relative overflow-hidden flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black flex items-center justify-center text-2xl shadow-lg shadow-blue-500/20">
          📈
        </div>
        <div>
          <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">ពិន្ទុមធ្យម • Average Score</p>
          <p class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mt-0.5">{{ averageScore }}</p>
          <p class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1 mt-0.5">
            <span>▲ +3.4%</span>
            <span class="text-slate-400 font-normal">compared to last semester</span>
          </p>
        </div>
      </div>

      <!-- 2. អត្រាបញ្ចប់ (Completion Rate) -->
      <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl border border-slate-200/80 dark:border-gray-700 shadow-sm relative overflow-hidden flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-600 text-white font-black flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/20">
          📊
        </div>
        <div>
          <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">អត្រាបញ្ចប់ • Completion Rate</p>
          <p class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mt-0.5">{{ completionRate }}</p>
          <p class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1 mt-0.5">
            <span>✓ High engagement</span>
            <span class="text-slate-400 font-normal">83% on-time submission</span>
          </p>
        </div>
      </div>

      <!-- 3. ចំនួនសិស្សបានធ្វើ (Total Students Attempted) -->
      <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl border border-slate-200/80 dark:border-gray-700 shadow-sm relative overflow-hidden flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white font-black flex items-center justify-center text-2xl shadow-lg shadow-purple-500/20">
          👥
        </div>
        <div>
          <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">ចំនួនសិស្សបានធ្វើ • Students Attempted</p>
          <p class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mt-0.5">{{ totalStudentsAttempted }} <span class="text-sm font-semibold text-slate-400">សិស្ស</span></p>
          <p class="text-[11px] font-semibold text-purple-600 flex items-center gap-1 mt-0.5">
            <span>● 12 Quizzes</span>
            <span class="text-slate-400 font-normal">across 5 enrolled majors</span>
          </p>
        </div>
      </div>
    </div>

    <!-- AI RECOMMENDATION ENGINE THRESHOLD LEGEND -->
    <div class="p-4 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl border border-indigo-500/30 shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="p-2 bg-purple-500/20 rounded-xl border border-purple-400/30 text-lg">🤖</div>
        <div>
          <h3 class="text-xs md:text-sm font-extrabold text-white">AI Recommendation Engine Thresholds (Saint Paul LMS)</h3>
          <p class="text-[11px] text-slate-300">Rule-based triggers feed into Student Dashboard AI Pathway & Progress Tracking</p>
        </div>
      </div>

      <div class="flex flex-wrap gap-2 text-xs">
        <span class="px-3 py-1.5 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold flex items-center gap-1.5">
          <span>🚀 ≥ 80%: Advance (បន្ត Module ថ្មី)</span>
        </span>
        <span class="px-3 py-1.5 rounded-xl bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold flex items-center gap-1.5">
          <span>📖 50%–79%: Review (រំលឹកមេរៀន)</span>
        </span>
        <span class="px-3 py-1.5 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold flex items-center gap-1.5">
          <span>🚨 &lt; 40%: Remedial (រៀនបំប៉ន)</span>
        </span>
      </div>
    </div>

    <!-- TOOLBAR & FILTERS -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-4 rounded-3xl border border-slate-200/80 dark:border-gray-700 shadow-sm">
      <div class="flex flex-wrap items-center gap-2">
        <div class="relative">
          <i class="pi pi-search absolute left-3 top-3 text-slate-400 text-xs"></i>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="ស្វែងរកសិស្ស ឬ Quiz..."
            class="pl-8 pr-3 py-2 rounded-2xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs w-60 focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <select v-model="statusFilter" class="p-2 rounded-2xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-semibold">
          <option value="All">All Status ▼</option>
          <option value="Graded">Graded 🟢</option>
          <option value="Pending Essay">Pending Essay 🟡</option>
        </select>

        <select v-model="aiFilter" class="p-2 rounded-2xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-semibold">
          <option value="All">All AI Recommendations ▼</option>
          <option value="Advance">🚀 Advance (≥80%)</option>
          <option value="Review">📖 Review (50-79%)</option>
          <option value="Remedial">🚨 Remedial (&lt;40%)</option>
        </select>
      </div>

      <div class="flex gap-2">
        <button
          @click="exportCsv"
          class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold shadow-md shadow-emerald-500/20 transition flex items-center gap-1.5 cursor-pointer"
        >
          <span>📥 Export CSV</span>
        </button>

        <button
          @click="printOrExportPdf"
          class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl text-xs font-bold shadow-md transition flex items-center gap-1.5 cursor-pointer"
        >
          <span>🖨️ Export PDF / Print</span>
        </button>
      </div>
    </div>

    <!-- STUDENT RESULTS TABLE -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80 dark:border-gray-700">
              <th class="p-4 font-bold">Student (សិស្ស)</th>
              <th class="p-4 font-bold">Quiz / Assessment</th>
              <th class="p-4 font-bold">Type</th>
              <th class="p-4 font-bold">Score</th>
              <th class="p-4 font-bold">Grade</th>
              <th class="p-4 font-bold">AI Recommendation</th>
              <th class="p-4 font-bold">Attempts</th>
              <th class="p-4 font-bold">Time Taken</th>
              <th class="p-4 font-bold">Status</th>
              <th class="p-4 font-bold text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
            <tr v-for="r in filteredResults" :key="r.id" class="hover:bg-slate-50/60 dark:hover:bg-gray-700/30 transition">
              <td class="p-4">
                <p class="font-bold text-slate-800 dark:text-white">{{ r.student_name }}</p>
                <p class="text-[10px] text-slate-400 font-mono">{{ r.student_id }}</p>
              </td>
              <td class="p-4">
                <p class="font-semibold text-slate-700 dark:text-slate-200">{{ r.quiz_name }}</p>
                <p class="text-[10px] text-slate-400">{{ r.course_name }}</p>
              </td>
              <td class="p-4">
                <span
                  class="px-2.5 py-1 rounded-full font-bold text-[10px]"
                  :class="{
                    'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300': r.type === 'pre_test',
                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300': r.type === 'practice',
                    'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300': r.type === 'post_test',
                    'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300': r.type === 'coding' || r.type === 'assignment',
                  }"
                >
                  {{ r.typeName }}
                </span>
              </td>
              <td class="p-4 font-black text-sm" :class="r.score >= 80 ? 'text-emerald-600' : r.score >= 50 ? 'text-amber-600' : 'text-rose-600'">
                {{ r.score }}%
              </td>
              <td class="p-4">
                <span
                  class="px-2.5 py-0.5 rounded-lg font-extrabold text-[11px]"
                  :class="{
                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300': r.grade === 'A' || r.grade === 'B',
                    'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300': r.grade === 'C',
                    'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300': r.grade === 'F',
                  }"
                >
                  {{ r.grade }}
                </span>
              </td>

              <!-- AI RECOMMENDATION BADGE (Advance / Review / Remedial) -->
              <td class="p-4">
                <span :class="['px-3 py-1 rounded-full font-extrabold text-[11px] inline-flex items-center gap-1.5', getAiRecommendation(r.score).badgeClass]">
                  <span>{{ getAiRecommendation(r.score).icon }}</span>
                  <span>{{ getAiRecommendation(r.score).label }}</span>
                </span>
              </td>

              <td class="p-4 text-slate-500 font-medium">{{ r.attempts }}</td>
              <td class="p-4 text-slate-500 font-medium">{{ r.time_spent }}</td>
              <td class="p-4">
                <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px]" :class="r.status === 'Graded' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 animate-pulse'">
                  {{ r.status === 'Graded' ? '🟢 Graded' : '🟡 Pending Essay' }}
                </span>
              </td>
              <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                <button
                  v-if="r.status === 'Pending Essay'"
                  @click="openGradeEssay(r)"
                  class="px-3 py-1.5 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded-xl font-bold transition"
                >
                  ✍️ Grade Essay
                </button>
                <button
                  @click="allowRetake(r)"
                  class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl font-bold transition"
                >
                  🔄 Allow Retake
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ✍️ GRADE ESSAY MODAL -->
    <div v-if="showGradeEssayModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-gray-700">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="text-base font-extrabold text-slate-800 dark:text-white">✍️ Grade Essay Submission — {{ gradingStudent?.student_name }}</h3>
          <button @click="showGradeEssayModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-3 text-xs">
          <div class="p-3.5 bg-slate-50 dark:bg-gray-900 rounded-2xl space-y-2 border border-slate-100 dark:border-gray-700">
            <p class="font-bold text-slate-500">Student Answer Submission:</p>
            <p class="font-mono bg-white dark:bg-gray-800 p-3 rounded-xl border border-slate-200 dark:border-gray-700 text-slate-800 dark:text-slate-200">
              "{{ gradingStudent?.essay_answer }}"
            </p>
          </div>

          <div class="p-3.5 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-2xl space-y-1">
            <p class="font-bold text-purple-900 dark:text-purple-300">🤖 AI Suggested Score: {{ gradingStudent?.ai_suggested_mark }} / 5 marks</p>
            <p class="text-slate-600 dark:text-slate-300">Reasoning: Answer correctly details RAM storage and provides valid C code declarations.</p>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold mb-1">Teacher Manual Mark (0-5)</label>
              <input v-model="manualMarkInput" type="number" max="5" min="0" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
            <div>
              <label class="block font-semibold mb-1">Teacher Feedback</label>
              <input v-model="teacherFeedbackInput" type="text" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-gray-700">
          <button @click="showGradeEssayModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="saveGrade" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-500/20">💾 Save Grade</button>
        </div>
      </div>
    </div>
  </div>
</template>
