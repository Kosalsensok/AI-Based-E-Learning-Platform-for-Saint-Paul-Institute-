<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  courses: Array<any>
}>()

const searchQuery = ref('')
const activeFilterChip = ref('All')
const viewMode = ref<'table' | 'cards'>('table')
const selectedStudentIds = ref<number[]>([])

// Sample Students Data matching spec
const students = ref([
  {
    id: 101,
    name: 'Chan Dara',
    student_id: 'STU241001',
    year: 'Year 2',
    major: 'IT & Networking',
    email: 'dara@example.com',
    progress: 85,
    score: 82,
    learning_time: '28h 30m',
    last_active: 'Today 09:30 AM',
    status: 'Active', // Active, Slow, At-Risk, Completed, Locked
    statusBadge: '🟢 Active',
    weak_topic: 'Pointers (42%)',
    rank: '#3 / 45',
    payment_status: '✅ Paid ($45 ABA)',
    teacher_note: 'សិស្សឆ្លាត តែត្រូវការជំនួយផ្នែក Pointer...',
  },
  {
    id: 102,
    name: 'Bun Rithy',
    student_id: 'STU241015',
    year: 'Year 2',
    major: 'Software Engineering',
    email: 'rithy@example.com',
    progress: 72,
    score: 78,
    learning_time: '20h 15m',
    last_active: '2 days ago',
    status: 'Active',
    statusBadge: '🟢 Active',
    weak_topic: 'Recursion (55%)',
    rank: '#8 / 45',
    payment_status: '✅ Paid ($45 ABA)',
    teacher_note: '',
  },
  {
    id: 103,
    name: 'Mao Sreynich',
    student_id: 'STU241208',
    year: 'Year 2',
    major: 'Computer Science',
    email: 'sreynich@example.com',
    progress: 63,
    score: 71,
    learning_time: '15h 45m',
    last_active: '3 days ago',
    status: 'Slow',
    statusBadge: '🟡 Slow',
    weak_topic: 'While Loop (60%)',
    rank: '#15 / 45',
    payment_status: '✅ Paid ($45 ABA)',
    teacher_note: '',
  },
  {
    id: 104,
    name: 'Sok Channa',
    student_id: 'STU241102',
    year: 'Year 2',
    major: 'IT & Networking',
    email: 'channa@example.com',
    progress: 45,
    score: 58,
    learning_time: '8h 15m',
    last_active: '9 days ago',
    status: 'At-Risk',
    statusBadge: '🔴 At-Risk',
    weak_topic: 'Pointers & Memory',
    rank: '#41 / 45',
    payment_status: '✅ Paid ($45 ABA)',
    teacher_note: 'មិនបានចូលរៀន ៩ ថ្ងៃ ត្រូវរំលឹក',
  },
  {
    id: 105,
    name: 'Long Vicheka',
    student_id: 'STU241305',
    year: 'Year 2',
    major: 'Software Engineering',
    email: 'vicheka@example.com',
    progress: 90,
    score: 88,
    learning_time: '32h 10m',
    last_active: 'Today 10:15 AM',
    status: 'Active',
    statusBadge: '🟢 Active',
    weak_topic: 'None',
    rank: '#1 / 45',
    payment_status: '✅ Paid ($45 ABA)',
    teacher_note: '',
  },
  {
    id: 106,
    name: 'Khem Rattana',
    student_id: 'STU241400',
    year: 'Year 2',
    major: 'Computer Science',
    email: 'rattana@example.com',
    progress: 100,
    score: 95,
    learning_time: '35h 00m',
    last_active: 'Yesterday',
    status: 'Completed',
    statusBadge: '✅ Completed',
    weak_topic: 'None',
    rank: '#2 / 45',
    payment_status: '✅ Paid ($45 ABA)',
    teacher_note: '',
  }
])

const filteredStudents = computed(() => {
  return students.value.filter(s => {
    const matchSearch = s.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        s.student_id.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchChip = activeFilterChip.value === 'All' || s.status === activeFilterChip.value
    return matchSearch && matchChip
  })
})

// Profile Drawer Modal State
const showProfileDrawer = ref(false)
const selectedStudent = ref<any>(null)
const privateNote = ref('')

const openProfile = (s: any) => {
  selectedStudent.value = s
  privateNote.value = s.teacher_note || ''
  showProfileDrawer.value = true
}

const savePrivateNote = () => {
  if (selectedStudent.value) {
    selectedStudent.value.teacher_note = privateNote.value
  }
  alert('Private teacher note saved!')
}

const sendReminder = (s: any) => {
  alert(`Study reminder sent to ${s.name}!`)
}

const sendMessage = (s: any) => {
  alert(`Direct message modal opened for ${s.name}.`)
}
</script>

<template>
  <div class="space-y-6">
    <!-- Top Toolbar & Filter Chips -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm space-y-4">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
          <!-- Search input -->
          <div class="relative">
            <i class="pi pi-search absolute left-3 top-3 text-slate-400 text-xs"></i>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search name / ID..."
              class="pl-8 pr-3 py-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs w-60"
            />
          </div>

          <!-- Sort dropdown -->
          <select class="p-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-semibold">
            <option value="progress">Sort: Progress ▼</option>
            <option value="score">Sort: Score</option>
            <option value="name">Sort: Name</option>
            <option value="active">Sort: Last Active</option>
          </select>
        </div>

        <div class="flex items-center gap-3">
          <!-- View Mode Toggle -->
          <div class="flex items-center p-1 bg-slate-100 dark:bg-gray-700 rounded-xl text-xs font-bold">
            <button
              @click="viewMode = 'table'"
              :class="['px-3 py-1.5 rounded-lg transition', viewMode === 'table' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-500']"
            >
              <i class="pi pi-list mr-1"></i> Table View
            </button>
            <button
              @click="viewMode = 'cards'"
              :class="['px-3 py-1.5 rounded-lg transition', viewMode === 'cards' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-500']"
            >
              <i class="pi pi-th-large mr-1"></i> Card View
            </button>
          </div>

          <button class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20">
            📤 Export Student List
          </button>
        </div>
      </div>

      <!-- Filter Chips Row -->
      <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100 dark:border-gray-700 text-xs font-bold">
        <button
          @click="activeFilterChip = 'All'"
          :class="['px-3 py-1.5 rounded-xl transition', activeFilterChip === 'All' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600']"
        >
          All 45
        </button>
        <button
          @click="activeFilterChip = 'Active'"
          :class="['px-3 py-1.5 rounded-xl transition', activeFilterChip === 'Active' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700']"
        >
          🟢 Active 32
        </button>
        <button
          @click="activeFilterChip = 'Slow'"
          :class="['px-3 py-1.5 rounded-xl transition', activeFilterChip === 'Slow' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700']"
        >
          🟡 Slow 8
        </button>
        <button
          @click="activeFilterChip = 'At-Risk'"
          :class="['px-3 py-1.5 rounded-xl transition', activeFilterChip === 'At-Risk' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700']"
        >
          🔴 At-Risk 5
        </button>
        <button
          @click="activeFilterChip = 'Completed'"
          :class="['px-3 py-1.5 rounded-xl transition', activeFilterChip === 'Completed' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700']"
        >
          ✅ Completed 12
        </button>
      </div>
    </div>

    <!-- VIEW 1: TABLE VIEW -->
    <div v-if="viewMode === 'table'" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80 dark:border-gray-700">
            <th class="p-3.5">Student Name</th>
            <th class="p-3.5">ID</th>
            <th class="p-3.5">Progress</th>
            <th class="p-3.5">Avg Score</th>
            <th class="p-3.5">Last Active</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
          <tr v-for="s in filteredStudents" :key="s.id" class="hover:bg-slate-50/50 dark:hover:bg-gray-700/30 transition">
            <td class="p-3.5 font-bold text-slate-800 dark:text-white flex items-center gap-2">
              <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs">
                {{ s.name.charAt(0) }}
              </span>
              <div>
                <p class="font-bold text-slate-800 dark:text-white">{{ s.name }}</p>
                <p class="text-[10px] text-slate-400 font-normal">{{ s.major }} • {{ s.year }}</p>
              </div>
            </td>
            <td class="p-3.5 font-mono text-slate-500 font-bold">{{ s.student_id }}</td>
            <td class="p-3.5">
              <div class="space-y-1 w-32">
                <div class="flex justify-between text-[10px] font-bold">
                  <span>{{ s.progress }}%</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                  <div class="bg-blue-600 h-full rounded-full" :style="{ width: `${s.progress}%` }"></div>
                </div>
              </div>
            </td>
            <td class="p-3.5 font-extrabold text-slate-800 dark:text-white" :class="s.score >= 70 ? 'text-emerald-600' : 'text-amber-600'">
              {{ s.score }}%
            </td>
            <td class="p-3.5 text-slate-500 font-medium">{{ s.last_active }}</td>
            <td class="p-3.5">
              <span
                class="px-2.5 py-1 rounded-full font-bold text-[10px]"
                :class="{
                  'bg-emerald-100 text-emerald-800': s.status === 'Active',
                  'bg-amber-100 text-amber-800': s.status === 'Slow',
                  'bg-rose-100 text-rose-800': s.status === 'At-Risk',
                  'bg-blue-100 text-blue-800': s.status === 'Completed',
                }"
              >
                {{ s.statusBadge }}
              </span>
            </td>
            <td class="p-3.5 text-right space-x-1.5">
              <button @click="openProfile(s)" class="px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-bold">👁 View Profile</button>
              <button @click="sendReminder(s)" class="px-2.5 py-1 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg font-semibold">🔔 Remind</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- VIEW 2: CARD VIEW -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <div
        v-for="s in filteredStudents"
        :key="s.id"
        class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4 hover:shadow-md transition"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-extrabold flex items-center justify-center text-sm shadow">
              {{ s.name.charAt(0) }}
            </div>
            <div>
              <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">{{ s.name }}</h3>
              <p class="text-xs text-slate-400 font-mono">{{ s.student_id }} • {{ s.year }}</p>
            </div>
          </div>
          <span
            class="px-2 py-0.5 rounded-full font-bold text-[10px]"
            :class="s.status === 'Active' ? 'bg-emerald-100 text-emerald-800' : (s.status === 'At-Risk' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800')"
          >
            {{ s.statusBadge }}
          </span>
        </div>

        <div class="space-y-2 text-xs">
          <div class="flex justify-between font-semibold">
            <span class="text-slate-500">Progress</span>
            <span class="font-bold text-blue-600">{{ s.progress }}%</span>
          </div>
          <div class="w-full bg-slate-100 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
            <div class="bg-blue-600 h-full rounded-full" :style="{ width: `${s.progress}%` }"></div>
          </div>

          <div class="grid grid-cols-2 gap-2 pt-2 text-[11px]">
            <div class="p-2 bg-slate-50 dark:bg-gray-700 rounded-lg">
              <span class="text-slate-400 block">Avg Score</span>
              <strong class="text-slate-800 dark:text-white font-extrabold">{{ s.score }}%</strong>
            </div>
            <div class="p-2 bg-slate-50 dark:bg-gray-700 rounded-lg">
              <span class="text-slate-400 block">Learning Time</span>
              <strong class="text-slate-800 dark:text-white font-extrabold">{{ s.learning_time }}</strong>
            </div>
          </div>

          <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold pt-1">
            Weak: {{ s.weak_topic }}
          </p>
        </div>

        <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-gray-700">
          <button @click="openProfile(s)" class="flex-1 py-1.5 bg-blue-600 text-white rounded-xl text-xs font-bold shadow">👁 View</button>
          <button @click="sendReminder(s)" class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">🔔 Remind</button>
        </div>
      </div>
    </div>

    <!-- 👁 STUDENT PROFILE DRAWER MODAL -->
    <div v-if="showProfileDrawer" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-end p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-xl w-full h-full p-6 space-y-5 shadow-2xl overflow-y-auto">
        <div class="flex items-center justify-between border-b pb-3">
          <div>
            <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">🎓 {{ selectedStudent?.name }}</h3>
            <p class="text-xs text-slate-400">{{ selectedStudent?.student_id }} • {{ selectedStudent?.major }} • {{ selectedStudent?.payment_status }}</p>
          </div>
          <button @click="showProfileDrawer = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times text-lg"></i></button>
        </div>

        <!-- OVERVIEW METRIC CARDS -->
        <div class="grid grid-cols-4 gap-2 text-center text-xs">
          <div class="p-3 bg-blue-50 dark:bg-gray-700 rounded-xl">
            <span class="text-slate-400 block text-[10px]">Progress</span>
            <strong class="text-blue-600 text-base font-extrabold">{{ selectedStudent?.progress }}%</strong>
          </div>
          <div class="p-3 bg-emerald-50 dark:bg-gray-700 rounded-xl">
            <span class="text-slate-400 block text-[10px]">Avg Score</span>
            <strong class="text-emerald-600 text-base font-extrabold">{{ selectedStudent?.score }}%</strong>
          </div>
          <div class="p-3 bg-purple-50 dark:bg-gray-700 rounded-xl">
            <span class="text-slate-400 block text-[10px]">Time</span>
            <strong class="text-purple-600 text-sm font-extrabold">{{ selectedStudent?.learning_time }}</strong>
          </div>
          <div class="p-3 bg-amber-50 dark:bg-gray-700 rounded-xl">
            <span class="text-slate-400 block text-[10px]">Rank</span>
            <strong class="text-amber-600 text-sm font-extrabold">{{ selectedStudent?.rank }}</strong>
          </div>
        </div>

        <!-- MODULE PROGRESS BREAKDOWN -->
        <div class="space-y-3 text-xs">
          <h4 class="font-bold text-slate-800 dark:text-white">📦 Module Progress Breakdown:</h4>
          <div class="space-y-2">
            <div>
              <div class="flex justify-between font-semibold mb-1">
                <span>Module 1: Introduction</span>
                <span class="text-emerald-600 font-bold">100% ✅</span>
              </div>
              <div class="w-full bg-slate-100 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                <div class="bg-emerald-500 h-full w-[100%]"></div>
              </div>
            </div>
            <div>
              <div class="flex justify-between font-semibold mb-1">
                <span>Module 2: Variables</span>
                <span class="text-blue-600 font-bold">90% 🟢</span>
              </div>
              <div class="w-full bg-slate-100 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                <div class="bg-blue-600 h-full w-[90%]"></div>
              </div>
            </div>
            <div>
              <div class="flex justify-between font-semibold mb-1">
                <span>Module 3: Loops</span>
                <span class="text-amber-600 font-bold">60% 🟡</span>
              </div>
              <div class="w-full bg-slate-100 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                <div class="bg-amber-500 h-full w-[60%]"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- WEAK TOPIC & AI SUGGESTION -->
        <div class="p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 rounded-2xl space-y-1 text-xs">
          <p class="font-bold text-purple-900 dark:text-purple-300">🎯 Weak Topics: Pointers (42%) • Recursion (55%)</p>
          <p class="text-slate-600 dark:text-slate-300">🤖 AI Suggests: Review Chapter 4.2 + Pointer Practice Quiz</p>
        </div>

        <!-- PRIVATE TEACHER NOTES -->
        <div class="space-y-2 text-xs">
          <label class="block font-bold text-slate-800 dark:text-white">📌 Teacher Private Note (Only visible to you):</label>
          <textarea v-model="privateNote" rows="3" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700"></textarea>
          <button @click="savePrivateNote" class="px-4 py-1.5 bg-slate-900 text-white rounded-xl font-bold shadow">💾 Save Note</button>
        </div>

        <!-- DRAWER FOOTER ACTIONS -->
        <div class="grid grid-cols-2 gap-2 pt-4 border-t text-xs">
          <button @click="sendMessage(selectedStudent)" class="py-2.5 bg-blue-600 text-white rounded-xl font-bold shadow">📧 Send Message</button>
          <button @click="sendReminder(selectedStudent)" class="py-2.5 bg-slate-100 text-slate-700 rounded-xl font-bold">🔔 Send Reminder</button>
        </div>
      </div>
    </div>
  </div>
</template>
