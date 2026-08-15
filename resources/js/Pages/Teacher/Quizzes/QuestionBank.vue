<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  courses?: Array<any>
}>()

const searchQuery = ref('')
const selectedType = ref('All')
const selectedDifficulty = ref('All')

// Question Types list
const questionTypeFilters = [
  'All', 'MCQ', 'True-False', 'Short Answer', 'Essay', 'Coding', 'Matching'
]

// Sample Question Bank Data
const questions = ref([
  {
    id: 'Q-0001',
    title: 'What is the output of printf("%d", 10 + 20); in C?',
    title_kh: 'តើលទ្ធផលនៃ printf("%d", 10 + 20); ក្នុង C គឺអ្វី?',
    type: 'MCQ',
    difficulty: 'Easy',
    used: 12,
    status: 'Live',
    course: 'C Programming Basics',
    module: 'Module 1: Intro',
    marks: 2,
    options: ['A. 30', 'B. 1020', 'C. Error', 'D. 0'],
    correct: 'A. 30',
  },
  {
    id: 'Q-0002',
    title: 'C language was created by Dennis Ritchie at Bell Labs.',
    title_kh: 'ភាសា C ត្រូវបានបង្កើតឡើងដោយ Dennis Ritchie នៅ Bell Labs។',
    type: 'True-False',
    difficulty: 'Easy',
    used: 8,
    status: 'Live',
    course: 'C Programming Basics',
    module: 'Module 1: Intro',
    marks: 1,
    options: ['True', 'False'],
    correct: 'True',
  },
  {
    id: 'Q-0003',
    title: 'Explain the main purpose of a variable in computer programming.',
    title_kh: 'ពន្យល់ពីគោលបំណងចម្បងនៃអថេរក្នុងកម្មវិធីកុំព្យូទ័រ។',
    type: 'Short Answer',
    difficulty: 'Medium',
    used: 5,
    status: 'Live',
    course: 'C Programming Basics',
    module: 'Module 2: Variables',
    marks: 5,
    options: [],
    correct: 'Variables allocate memory locations in RAM to store data values during program execution.',
  },
  {
    id: 'Q-0004',
    title: 'Match the data types with their correct size in C (int, float, char).',
    title_kh: 'ផ្គូផ្គងប្រភេទទិន្នន័យជាមួយទំហំក្នុង memory (int, float, char)។',
    type: 'Matching',
    difficulty: 'Medium',
    used: 6,
    status: 'Live',
    course: 'C Programming Basics',
    module: 'Module 2: Variables',
    marks: 4,
    options: [],
    correct: 'int -> 4 bytes, char -> 1 byte, float -> 4 bytes',
  },
  {
    id: 'Q-0005',
    title: 'Write a C function to reverse a singly linked list in O(n) time.',
    title_kh: 'សរសេរអនុគមន៍ C ដើម្បីបញ្ច្រាស Singly Linked List ក្នុង O(n) time។',
    type: 'Coding',
    difficulty: 'Hard',
    used: 3,
    status: 'Live',
    course: 'C Programming Basics',
    module: 'Module 3: Pointers',
    marks: 10,
    options: [],
    correct: 'struct Node* reverse(struct Node* head) { ... }',
  }
])

const filteredQuestions = computed(() => {
  return questions.value.filter(q => {
    const matchQuery = q.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                       q.id.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                       q.title_kh.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchType = selectedType.value === 'All' || q.type === selectedType.value
    const matchDiff = selectedDifficulty.value === 'All' || q.difficulty === selectedDifficulty.value
    return matchQuery && matchType && matchDiff
  })
})

// Create Question Modal
const showCreateModal = ref(false)
const createForm = useForm({
  type: 'MCQ',
  difficulty: 'Easy',
  marks: 2,
  question_en: '',
  question_kh: '',
  option_a: '',
  option_b: '',
  option_c: '',
  option_d: '',
  correct_option: 'A',
  explanation: '',
})

const submitCreateQuestion = () => {
  if (!createForm.question_en) {
    alert('Please enter question text')
    return
  }

  questions.value.unshift({
    id: `Q-000${questions.value.length + 1}`,
    title: createForm.question_en,
    title_kh: createForm.question_kh || createForm.question_en,
    type: createForm.type,
    difficulty: createForm.difficulty,
    used: 0,
    status: 'Live',
    course: 'C Programming Basics',
    module: 'Module 1: Intro',
    marks: createForm.marks,
    options: createForm.type === 'MCQ' ? [createForm.option_a, createForm.option_b, createForm.option_c, createForm.option_d] : [],
    correct: createForm.correct_option,
  })

  alert('សំណួរត្រូវបានបន្ថែមទៅក្នុង Question Bank ដោយជោគជ័យ!')
  showCreateModal.value = false
  createForm.reset()
}

// AI Question Generator Modal
const showAiModal = ref(false)
const generateAiQuestions = () => {
  alert('AI is analyzing lesson content to generate 5 high-yield questions...')
  showAiModal.value = false
}
</script>

<template>
  <div class="space-y-6">
    <!-- Top Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div class="p-4 bg-white dark:bg-gray-800 rounded-3xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 font-black flex items-center justify-center text-lg">❓</div>
        <div>
          <p class="text-[10px] text-slate-400 font-extrabold uppercase">Total Questions</p>
          <p class="text-xl font-black text-slate-800 dark:text-white">420</p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-3xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 font-black flex items-center justify-center text-lg">🟢</div>
        <div>
          <p class="text-[10px] text-slate-400 font-extrabold uppercase">Published</p>
          <p class="text-xl font-black text-slate-800 dark:text-white">385</p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-3xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 font-black flex items-center justify-center text-lg">♻️</div>
        <div>
          <p class="text-[10px] text-slate-400 font-extrabold uppercase">Reused in Quizzes</p>
          <p class="text-xl font-black text-slate-800 dark:text-white">1,240 <span class="text-xs font-normal">ដង</span></p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-3xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 font-black flex items-center justify-center text-lg">🟡</div>
        <div>
          <p class="text-[10px] text-slate-400 font-extrabold uppercase">Draft Vault</p>
          <p class="text-xl font-black text-slate-800 dark:text-white">35</p>
        </div>
      </div>
    </div>

    <!-- Toolbar & Filter Tags -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-3xl border border-slate-200/80 dark:border-gray-700 shadow-sm space-y-4">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
          <div class="relative">
            <i class="pi pi-search absolute left-3 top-3 text-slate-400 text-xs"></i>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="ស្វែងរកសំណួរ ឬពាក្យគន្លឹះ..."
              class="pl-8 pr-3 py-2 rounded-2xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs w-64 focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <select v-model="selectedDifficulty" class="p-2 rounded-2xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-semibold">
            <option value="All">All Difficulty ▼</option>
            <option value="Easy">🟢 Easy</option>
            <option value="Medium">🟡 Medium</option>
            <option value="Hard">🔴 Hard</option>
          </select>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <button @click="showAiModal = true" class="px-3.5 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 rounded-2xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
            <span>✨ AI Auto-Gen Questions</span>
          </button>
          <button @click="showCreateModal = true" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-black shadow-md shadow-blue-500/20 transition flex items-center gap-1.5 cursor-pointer">
            <span>+ បន្ថែមសំណួរ (Add Question)</span>
          </button>
        </div>
      </div>

      <!-- Tag Filter Pills -->
      <div class="flex flex-wrap gap-1.5 pt-1 border-t border-slate-100 dark:border-gray-700">
        <button
          v-for="tag in questionTypeFilters"
          :key="tag"
          @click="selectedType = tag"
          :class="[
            'px-3 py-1 rounded-xl text-xs font-bold transition',
            selectedType === tag
              ? 'bg-blue-600 text-white shadow-sm'
              : 'bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200'
          ]"
        >
          {{ tag }}
        </button>
      </div>
    </div>

    <!-- Question Cards List -->
    <div class="space-y-3">
      <div
        v-for="q in filteredQuestions"
        :key="q.id"
        class="bg-white dark:bg-gray-800 rounded-3xl p-5 border border-slate-200/80 dark:border-gray-700 shadow-sm hover:shadow-md transition space-y-3"
      >
        <div class="flex flex-wrap items-center justify-between gap-2">
          <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 font-mono text-[10px] font-extrabold">
              {{ q.id }}
            </span>
            <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold">
              {{ q.type }}
            </span>
            <span
              class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold"
              :class="{
                'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300': q.difficulty === 'Easy',
                'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300': q.difficulty === 'Medium',
                'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300': q.difficulty === 'Hard',
              }"
            >
              {{ q.difficulty }}
            </span>
          </div>

          <!-- Reuse Indicator (Used in N Quizzes) -->
          <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-200 dark:border-purple-800 font-extrabold text-[11px]">
              ♻️ ប្រើប្រាស់ក្នុង {{ q.used }} Quizzes
            </span>
            <span class="text-xs font-bold text-slate-400">{{ q.marks }} Marks</span>
          </div>
        </div>

        <div>
          <p class="font-black text-slate-800 dark:text-white text-sm">{{ q.title }}</p>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">{{ q.title_kh }}</p>
        </div>

        <div v-if="q.options && q.options.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
          <div
            v-for="(opt, idx) in q.options"
            :key="idx"
            :class="[
              'p-2.5 rounded-xl text-xs font-medium border',
              opt.startsWith('A.')
                ? 'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800 font-bold'
                : 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-gray-700 dark:text-slate-200 dark:border-gray-600'
            ]"
          >
            {{ opt }}
          </div>
        </div>

        <div v-else class="p-3 bg-slate-50 dark:bg-gray-700/50 rounded-2xl border border-slate-100 dark:border-gray-700 text-xs text-slate-600 dark:text-slate-300">
          <span class="font-bold text-slate-500">Correct Key / Reference:</span> {{ q.correct }}
        </div>
      </div>
    </div>

    <!-- ➕ ADD QUESTION MODAL -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-gray-700 overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between border-b pb-3">
          <h3 class="text-base font-extrabold text-slate-800 dark:text-white">➕ បន្ថែមសំណួរទៅ Question Bank</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-3 text-xs">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold mb-1">Question Type</label>
              <select v-model="createForm.type" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700">
                <option value="MCQ">MCQ (Multiple Choice)</option>
                <option value="True-False">True / False</option>
                <option value="Short Answer">Short Answer</option>
                <option value="Essay">Essay</option>
                <option value="Coding">Coding</option>
              </select>
            </div>
            <div>
              <label class="block font-bold mb-1">Difficulty Level</label>
              <select v-model="createForm.difficulty" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700">
                <option value="Easy">🟢 Easy</option>
                <option value="Medium">🟡 Medium</option>
                <option value="Hard">🔴 Hard</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-bold mb-1">Question Text (English)</label>
            <input v-model="createForm.question_en" type="text" placeholder="e.g. What is the difference between TCP and UDP?" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
          </div>

          <div>
            <label class="block font-bold mb-1">Question Text (Khmer)</label>
            <input v-model="createForm.question_kh" type="text" placeholder="e.g. តើអ្វីជាភាពខុសគ្នារវាង TCP និង UDP?" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
          </div>

          <div v-if="createForm.type === 'MCQ'" class="space-y-2 pt-1">
            <label class="font-bold">Choices & Correct Answer</label>
            <input v-model="createForm.option_a" placeholder="Option A (Correct)" class="w-full p-2 rounded-lg border bg-slate-50 dark:bg-gray-700" />
            <input v-model="createForm.option_b" placeholder="Option B" class="w-full p-2 rounded-lg border bg-slate-50 dark:bg-gray-700" />
            <input v-model="createForm.option_c" placeholder="Option C" class="w-full p-2 rounded-lg border bg-slate-50 dark:bg-gray-700" />
            <input v-model="createForm.option_d" placeholder="Option D" class="w-full p-2 rounded-lg border bg-slate-50 dark:bg-gray-700" />
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t">
          <button @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="submitCreateQuestion" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold shadow">Save to Vault</button>
        </div>
      </div>
    </div>
  </div>
</template>
