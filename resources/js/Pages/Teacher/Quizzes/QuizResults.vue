<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  courses: Array<any>
}>()

const searchQuery = ref('')
const statusFilter = ref('All')

const studentResults = ref([
  {
    id: 1,
    student_name: 'Chan Dara',
    student_id: 'STU241001',
    quiz_name: 'Post-Test Module 1: Intro to C',
    type: 'post_test',
    typeName: 'Post-Test',
    score: 85,
    grade: 'A',
    attempts: '1 / 1',
    time_spent: '22 mins',
    status: 'Graded',
    submitted_at: '2024-06-15 10:30 AM',
    essay_answer: 'Variables store data values in RAM. Example: int x = 10;',
    ai_suggested_mark: 5,
    teacher_mark: 5,
  },
  {
    id: 2,
    student_name: 'Keo Sokha',
    student_id: 'STU241002',
    quiz_name: 'Post-Test Module 1: Intro to C',
    type: 'post_test',
    typeName: 'Post-Test',
    score: 62,
    grade: 'C',
    attempts: '1 / 1',
    time_spent: '28 mins',
    status: 'Pending Essay',
    submitted_at: '2024-06-15 11:15 AM',
    essay_answer: 'Variables are used to hold numbers and text during execution.',
    ai_suggested_mark: 4,
    teacher_mark: null,
  },
  {
    id: 3,
    student_name: 'Vannak Bopha',
    student_id: 'STU241003',
    quiz_name: 'Variables Practice Drill',
    type: 'practice',
    typeName: 'Practice',
    score: 95,
    grade: 'A',
    attempts: '3 / Unlimited',
    time_spent: '14 mins',
    status: 'Graded',
    submitted_at: '2024-06-14 02:40 PM',
    essay_answer: null,
    ai_suggested_mark: null,
    teacher_mark: null,
  },
  {
    id: 4,
    student_name: 'Meng Voleak',
    student_id: 'STU241004',
    quiz_name: 'Pre-Test Module 1: Intro to C',
    type: 'pre_test',
    typeName: 'Pre-Test',
    score: 40,
    grade: 'F',
    attempts: '1 / 1',
    time_spent: '18 mins',
    status: 'Graded',
    submitted_at: '2024-06-12 09:00 AM',
    essay_answer: null,
    ai_suggested_mark: null,
    teacher_mark: null,
  }
])

const filteredResults = computed(() => {
  return studentResults.value.filter(r => {
    const matchSearch = r.student_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        r.student_id.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        r.quiz_name.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchStatus = statusFilter.value === 'All' || r.status === statusFilter.value
    return matchSearch && matchStatus
  })
})

// Grade Essay Modal State
const showGradeEssayModal = ref(false)
const gradingStudent = ref<any>(null)
const manualMarkInput = ref<number>(4)
const teacherFeedbackInput = ref('Good concise explanation of variable scope.')

const openGradeEssay = (r: any) => {
  gradingStudent.value = r
  manualMarkInput.value = r.ai_suggested_mark || 4
  showGradeEssayModal.value = true
}

const saveGrade = () => {
  if (gradingStudent.value) {
    gradingStudent.value.status = 'Graded'
    gradingStudent.value.teacher_mark = manualMarkInput.value
    gradingStudent.value.score = Math.min(100, gradingStudent.value.score + 10)
    gradingStudent.value.grade = gradingStudent.value.score >= 70 ? 'B' : 'C'
  }
  alert(`Grade saved for ${gradingStudent.value?.student_name}!`)
  showGradeEssayModal.value = false
}

const allowRetake = (r: any) => {
  alert(`Allowed retake attempt for ${r.student_name}!`)
}

const exportCsv = () => {
  alert('Exporting Quiz Results Gradebook to CSV...')
}
</script>

<template>
  <div class="space-y-6">
    <!-- Top Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 font-bold flex items-center justify-center text-lg">📊</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Pass Rate</p>
          <p class="text-xl font-extrabold text-slate-800 dark:text-white">76%</p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 font-bold flex items-center justify-center text-lg">📈</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Average Score</p>
          <p class="text-xl font-extrabold text-slate-800 dark:text-white">74.2%</p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 font-bold flex items-center justify-center text-lg">⭐</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Highest Score</p>
          <p class="text-xl font-extrabold text-slate-800 dark:text-white">100%</p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 font-bold flex items-center justify-center text-lg">⚠️</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Pending Essays</p>
          <p class="text-xl font-extrabold text-amber-600">1 Student</p>
        </div>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-4 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm">
      <div class="flex flex-wrap items-center gap-2">
        <div class="relative">
          <i class="pi pi-search absolute left-3 top-3 text-slate-400 text-xs"></i>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search student or quiz..."
            class="pl-8 pr-3 py-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs w-60"
          />
        </div>

        <select v-model="statusFilter" class="p-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-semibold">
          <option value="All">All Status ▼</option>
          <option value="Graded">Graded 🟢</option>
          <option value="Pending Essay">Pending Essay 🟡</option>
        </select>
      </div>

      <div class="flex gap-2">
        <button @click="exportCsv" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-500/20 transition flex items-center gap-1.5">
          <i class="pi pi-download"></i>
          <span>Export Gradebook CSV</span>
        </button>
      </div>
    </div>

    <!-- Quiz Results Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80 dark:border-gray-700">
            <th class="p-3.5">Student</th>
            <th class="p-3.5">Quiz Name</th>
            <th class="p-3.5">Type</th>
            <th class="p-3.5">Score</th>
            <th class="p-3.5">Grade</th>
            <th class="p-3.5">Attempts</th>
            <th class="p-3.5">Time Spent</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
          <tr v-for="r in filteredResults" :key="r.id" class="hover:bg-slate-50/50 dark:hover:bg-gray-700/30 transition">
            <td class="p-3.5">
              <p class="font-bold text-slate-800 dark:text-white">{{ r.student_name }}</p>
              <p class="text-[10px] text-slate-400 font-mono">{{ r.student_id }}</p>
            </td>
            <td class="p-3.5 font-medium text-slate-700 dark:text-slate-200">{{ r.quiz_name }}</td>
            <td class="p-3.5">
              <span
                class="px-2.5 py-1 rounded-full font-bold text-[10px]"
                :class="{
                  'bg-blue-100 text-blue-800': r.type === 'pre_test',
                  'bg-emerald-100 text-emerald-800': r.type === 'practice',
                  'bg-rose-100 text-rose-800': r.type === 'post_test',
                }"
              >
                {{ r.typeName }}
              </span>
            </td>
            <td class="p-3.5 font-extrabold text-sm" :class="r.score >= 70 ? 'text-emerald-600' : 'text-rose-600'">
              {{ r.score }}%
            </td>
            <td class="p-3.5">
              <span
                class="px-2 py-0.5 rounded font-extrabold text-[10px]"
                :class="{
                  'bg-emerald-100 text-emerald-800': r.grade === 'A' || r.grade === 'B',
                  'bg-amber-100 text-amber-800': r.grade === 'C',
                  'bg-rose-100 text-rose-800': r.grade === 'F',
                }"
              >
                Grade {{ r.grade }}
              </span>
            </td>
            <td class="p-3.5 text-slate-500">{{ r.attempts }}</td>
            <td class="p-3.5 text-slate-500">{{ r.time_spent }}</td>
            <td class="p-3.5">
              <span class="px-2 py-0.5 rounded-full font-bold text-[10px]" :class="r.status === 'Graded' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800 animate-pulse'">
                {{ r.status === 'Graded' ? '🟢 Graded' : '🟡 Pending Essay' }}
              </span>
            </td>
            <td class="p-3.5 text-right space-x-1">
              <button v-if="r.status === 'Pending Essay'" @click="openGradeEssay(r)" class="px-2.5 py-1 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded-lg font-bold">
                ✍️ Grade Essay
              </button>
              <button @click="allowRetake(r)" class="px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-bold">
                🔄 Allow Retake
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ✍️ GRADE ESSAY MODAL -->
    <div v-if="showGradeEssayModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b pb-3">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">✍️ Grade Essay Submission — {{ gradingStudent?.student_name }}</h3>
          <button @click="showGradeEssayModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-3 text-xs">
          <div class="p-3 bg-slate-50 dark:bg-gray-900 rounded-xl space-y-2">
            <p class="font-bold text-slate-500">Student Answer Submission:</p>
            <p class="font-mono bg-white dark:bg-gray-800 p-3 rounded border border-slate-200 text-slate-800 dark:text-slate-200">
              "{{ gradingStudent?.essay_answer }}"
            </p>
          </div>

          <div class="p-3 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 rounded-xl space-y-1">
            <p class="font-bold text-purple-900 dark:text-purple-300">🤖 AI Suggested Score: {{ gradingStudent?.ai_suggested_mark }} / 5 marks</p>
            <p class="text-slate-600 dark:text-slate-300">Reasoning: Answer correctly mentions RAM data storage and provides a valid code example.</p>
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

        <div class="flex justify-end gap-2 pt-2 border-t">
          <button @click="showGradeEssayModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="saveGrade" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold shadow">💾 Save Grade</button>
        </div>
      </div>
    </div>
  </div>
</template>
