<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  courses: Array<any>
}>()

const searchQuery = ref('')
const selectedCourse = ref('')
const selectedType = ref('All')
const selectedDifficulty = ref('All')

// Question Types list
const questionTypes = [
  '☑ MCQ', '✓/✗ True-False', '✍ Essay', '🔗 Matching',
  '🔢 Fill Blank', '💻 Coding', '📎 File Upload', '🔀 Ordering'
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
    chapter: 'Chapter 1.1',
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
    chapter: 'Chapter 1.1',
    marks: 1,
    options: ['True', 'False'],
    correct: 'True',
  },
  {
    id: 'Q-0003',
    title: 'Explain the main purpose of a variable in computer programming.',
    title_kh: 'ពន្យល់ពីគោលបំណងចម្បងនៃអថេរក្នុងកម្មវិធីកុំព្យូទ័រ។',
    type: 'Essay',
    difficulty: 'Medium',
    used: 5,
    status: 'Live',
    course: 'C Programming Basics',
    module: 'Module 2: Variables',
    chapter: 'Chapter 2.1',
    marks: 5,
    options: [],
    correct: 'Manual evaluation required',
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
    chapter: 'Chapter 2.2',
    marks: 4,
    options: [],
    correct: 'int -> 4 bytes, char -> 1 byte',
  },
  {
    id: 'Q-0005',
    title: 'Write a C program to print numbers from 1 to 10 using a for-loop.',
    title_kh: 'សរសេរកម្មវិធី C ដើម្បីបោះពុម្ពលេខពី ១ ដល់ ១០ ដោយប្រើ for-loop។',
    type: 'Coding',
    difficulty: 'Hard',
    used: 3,
    status: 'Draft',
    course: 'C Programming Basics',
    module: 'Module 3: Loops',
    chapter: 'Chapter 3.1',
    marks: 10,
    options: [],
    correct: 'for(int i=1; i<=10; i++) printf("%d", i);',
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
  course_id: null,
  module_id: null,
  chapter_id: null,
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
  shuffle: true,
  explanation: '',
})

const submitCreateQuestion = () => {
  questions.value.unshift({
    id: `Q-000${questions.value.length + 1}`,
    title: createForm.question_en || 'New Sample Question',
    title_kh: createForm.question_kh || 'សំណួរគំរូថ្មី',
    type: createForm.type,
    difficulty: createForm.difficulty,
    used: 0,
    status: 'Live',
    course: 'C Programming Basics',
    module: 'Module 1',
    chapter: 'Chapter 1.1',
    marks: createForm.marks,
    options: [createForm.option_a, createForm.option_b, createForm.option_c, createForm.option_d],
    correct: createForm.correct_option,
  })
  alert('Question created and added to Question Bank!')
  showCreateModal.value = false
  createForm.reset()
}

// AI Question Generator Modal
const showAiModal = ref(false)
const aiForm = useForm({
  source: 'Chapter 2.1: Variables',
  type: 'MCQ',
  quantity: 10,
  language: 'Khmer + English',
})

const generateAiQuestions = () => {
  alert('AI is generating 10 high-quality questions based on Chapter 2.1 content...')
  showAiModal.value = false
}

// Category Tree Modal
const showCategoryModal = ref(false)

// Question Preview Modal
const showPreviewModal = ref(false)
const previewQuestion = ref<any>(null)

const openPreview = (q: any) => {
  previewQuestion.value = q
  showPreviewModal.value = true
}
</script>

<template>
  <div class="space-y-6">
    <!-- Top Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 font-bold flex items-center justify-center text-lg">❓</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">My Questions</p>
          <p class="text-xl font-extrabold text-slate-800 dark:text-white">420</p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 font-bold flex items-center justify-center text-lg">🟢</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Published</p>
          <p class="text-xl font-extrabold text-slate-800 dark:text-white">385</p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 font-bold flex items-center justify-center text-lg">🟡</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Draft</p>
          <p class="text-xl font-extrabold text-slate-800 dark:text-white">35</p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 font-bold flex items-center justify-center text-lg">♻️</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Reused Times</p>
          <p class="text-xl font-extrabold text-slate-800 dark:text-white">1,240</p>
        </div>
      </div>
    </div>

    <!-- Toolbar & Question Types Badges -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm space-y-4">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
          <div class="relative">
            <i class="pi pi-search absolute left-3 top-3 text-slate-400 text-xs"></i>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search question or keyword..."
              class="pl-8 pr-3 py-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs w-60"
            />
          </div>

          <select v-model="selectedType" class="p-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-semibold">
            <option value="All">All Types ▼</option>
            <option value="MCQ">☑ MCQ</option>
            <option value="True-False">✓/✗ True-False</option>
            <option value="Essay">✍ Essay</option>
            <option value="Matching">🔗 Matching</option>
            <option value="Coding">💻 Coding</option>
          </select>

          <select v-model="selectedDifficulty" class="p-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-semibold">
            <option value="All">All Difficulty ▼</option>
            <option value="Easy">🟢 Easy</option>
            <option value="Medium">🟡 Medium</option>
            <option value="Hard">🔴 Hard</option>
          </select>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <button @click="showCategoryModal = true" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
            📂 Categories
          </button>
          <button @click="showAiModal = true" class="px-3.5 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl text-xs font-bold shadow-md shadow-purple-500/20 transition flex items-center gap-1.5">
            <span>🧠 AI Generate</span>
          </button>
          <button @click="showCreateModal = true" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 transition flex items-center gap-1.5">
            <span>➕ Create Question</span>
          </button>
        </div>
      </div>

      <!-- Question Types Badges Row -->
      <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100 dark:border-gray-700 text-[11px] font-semibold text-slate-600 dark:text-slate-300">
        <span class="text-xs text-slate-400 font-bold uppercase mr-1">Types:</span>
        <span v-for="t in questionTypes" :key="t" class="px-2.5 py-1 bg-slate-100 dark:bg-gray-700 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-600 transition cursor-pointer">
          {{ t }}
        </span>
      </div>
    </div>

    <!-- Question Bank Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80 dark:border-gray-700">
            <th class="p-3.5">ID</th>
            <th class="p-3.5">Question Preview</th>
            <th class="p-3.5">Type</th>
            <th class="p-3.5">Difficulty</th>
            <th class="p-3.5">Used</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
          <tr v-for="q in filteredQuestions" :key="q.id" class="hover:bg-slate-50/50 dark:hover:bg-gray-700/30 transition">
            <td class="p-3.5 font-bold text-slate-500 font-mono">{{ q.id }}</td>
            <td class="p-3.5">
              <p class="font-bold text-slate-800 dark:text-white">{{ q.title }}</p>
              <p class="text-[11px] text-blue-600 dark:text-blue-400 font-medium">{{ q.title_kh }}</p>
            </td>
            <td class="p-3.5">
              <span class="px-2.5 py-1 bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-200 rounded-lg font-bold text-[10px]">
                {{ q.type }}
              </span>
            </td>
            <td class="p-3.5">
              <span
                class="px-2 py-0.5 rounded font-bold text-[10px]"
                :class="{
                  'bg-emerald-100 text-emerald-800': q.difficulty === 'Easy',
                  'bg-amber-100 text-amber-800': q.difficulty === 'Medium',
                  'bg-rose-100 text-rose-800': q.difficulty === 'Hard',
                }"
              >
                {{ q.difficulty === 'Easy' ? '🟢 Easy' : (q.difficulty === 'Medium' ? '🟡 Med' : '🔴 Hard') }}
              </span>
            </td>
            <td class="p-3.5 font-bold text-slate-700 dark:text-slate-200">{{ q.used }}x</td>
            <td class="p-3.5">
              <span class="px-2 py-0.5 rounded-full font-bold text-[10px]" :class="q.status === 'Live' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'">
                {{ q.status === 'Live' ? '🟢 Live' : '🟡 Draft' }}
              </span>
            </td>
            <td class="p-3.5 text-right space-x-1">
              <button @click="openPreview(q)" class="px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-bold">👁 Preview</button>
              <button class="px-2.5 py-1 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg font-semibold">✏ Edit</button>
              <button class="px-2.5 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg font-bold">📌 Add to Quiz</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ➕ CREATE QUESTION MODAL -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">➕ Create Question — Multiple Choice (MCQ)</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-3 text-xs">
          <div class="grid grid-cols-3 gap-3">
            <div>
              <label class="block font-semibold mb-1">Course</label>
              <select v-model="createForm.course_id" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700">
                <option :value="null">C Programming Basics ▼</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">Difficulty</label>
              <select v-model="createForm.difficulty" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700">
                <option value="Easy">🟢 Easy</option>
                <option value="Medium">🟡 Medium</option>
                <option value="Hard">🔴 Hard</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">Marks</label>
              <input v-model="createForm.marks" type="number" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
          </div>

          <div>
            <label class="block font-semibold mb-1">Question (English)</label>
            <textarea v-model="createForm.question_en" rows="2" placeholder="What is the correct way to declare an integer variable in C?" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700"></textarea>
          </div>

          <div>
            <label class="block font-semibold mb-1">Question (Khmer Translation)</label>
            <textarea v-model="createForm.question_kh" rows="2" placeholder="តើវិធីប្រកាសអថេរចំនួនគត់ត្រឹមត្រូវក្នុង C គឺយ៉ាងណា?" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700"></textarea>
          </div>

          <!-- Options -->
          <div class="space-y-2 p-3 bg-slate-50 dark:bg-gray-700/50 rounded-xl">
            <p class="font-bold text-[11px] text-slate-500 uppercase">Answer Options</p>
            <div class="space-y-1.5">
              <input v-model="createForm.option_a" type="text" placeholder="A. int age = 20; (Correct Option)" class="w-full p-2 rounded-lg border border-emerald-300 bg-white dark:bg-gray-700" />
              <input v-model="createForm.option_b" type="text" placeholder="B. integer age = 20;" class="w-full p-2 rounded-lg border border-slate-200 bg-white dark:bg-gray-700" />
              <input v-model="createForm.option_c" type="text" placeholder="C. number age = 20;" class="w-full p-2 rounded-lg border border-slate-200 bg-white dark:bg-gray-700" />
              <input v-model="createForm.option_d" type="text" placeholder="D. int = age 20;" class="w-full p-2 rounded-lg border border-slate-200 bg-white dark:bg-gray-700" />
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-gray-700">
          <button @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="submitCreateQuestion" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold shadow">💾 Save & Publish</button>
        </div>
      </div>
    </div>

    <!-- 🧠 AI QUESTION GENERATOR MODAL -->
    <div v-if="showAiModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
          <span>🧠 AI Question Generator</span>
        </h3>
        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-semibold mb-1">Source Chapter</label>
            <select v-model="aiForm.source" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700">
              <option value="Chapter 2.1: Variables">Chapter 2.1: Variables</option>
              <option value="Chapter 3.1: Loops">Chapter 3.1: Loops</option>
            </select>
          </div>
          <div>
            <label class="block font-semibold mb-1">Quantity</label>
            <input v-model="aiForm.quantity" type="number" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
          </div>
          <p class="p-2.5 bg-amber-50 text-amber-800 rounded-xl text-[11px] font-medium">
            ⚠️ AI-generated questions must be reviewed by teacher before publishing.
          </p>
        </div>
        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-gray-700">
          <button @click="showAiModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="generateAiQuestions" class="px-5 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold shadow">🚀 Generate Questions</button>
        </div>
      </div>
    </div>

    <!-- 📂 CATEGORY TREE MODAL -->
    <div v-if="showCategoryModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 dark:text-white">📂 Question Categories</h3>
        <div class="space-y-2 text-xs">
          <div class="p-3 bg-slate-50 dark:bg-gray-700 rounded-xl space-y-1">
            <p class="font-bold text-slate-800 dark:text-white">📚 C Programming Basics (420 Qs)</p>
            <p class="pl-4 text-slate-600">├── 📁 Module 1: Introduction to C (120 Qs)</p>
            <p class="pl-4 text-slate-600">├── 📁 Module 2: Variables & Data Types (180 Qs)</p>
            <p class="pl-4 text-slate-600">└── 📁 Module 3: Loops & Conditions (120 Qs)</p>
          </div>
        </div>
        <div class="flex justify-end">
          <button @click="showCategoryModal = false" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold">Close</button>
        </div>
      </div>
    </div>

    <!-- 👁 QUESTION PREVIEW MODAL -->
    <div v-if="showPreviewModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 dark:text-white">👁 Question Preview: {{ previewQuestion?.id }}</h3>
        <div class="p-4 bg-slate-50 dark:bg-gray-900 rounded-xl space-y-3 text-xs">
          <p class="font-extrabold text-sm text-slate-800 dark:text-white">{{ previewQuestion?.title }}</p>
          <p class="text-blue-600 font-semibold">{{ previewQuestion?.title_kh }}</p>
          <div v-if="previewQuestion?.options" class="space-y-1 pl-2">
            <p v-for="opt in previewQuestion.options" :key="opt" class="p-2 rounded bg-white dark:bg-gray-800 border border-slate-200">
              {{ opt }}
            </p>
          </div>
          <p class="text-emerald-600 font-bold">✅ Correct Answer: {{ previewQuestion?.correct }}</p>
        </div>
        <div class="flex justify-end">
          <button @click="showPreviewModal = false" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>
