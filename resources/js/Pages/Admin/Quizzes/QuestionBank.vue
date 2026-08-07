<script setup lang="ts">
import { ref, computed } from 'vue'
import type { QuestionItem, Stats } from './types'

const props = defineProps<{
  questions: QuestionItem[]
  categories: any[]
  stats: Stats
}>()

const emit = defineEmits<{
  (e: 'openPreview', question: QuestionItem): void
  (e: 'openEdit', question: QuestionItem): void
  (e: 'openAddToQuiz', question: QuestionItem): void
  (e: 'deleteQuestion', id: string): void
  (e: 'openAiGenerator'): void
  (e: 'openAddCategory'): void
}>()

// Search & Filter State
const searchQuery = ref('')
const selectedTypeFilter = ref('all')
const selectedDifficultyFilter = ref('all')
const selectedStatusFilter = ref('all')
const selectedCourseFilter = ref('all')
const showCategoryPanel = ref(false)
const showAdvancedSearch = ref(false)

// Sorting State
const sortKey = ref<'id' | 'question' | 'type' | 'difficulty' | 'marks' | 'status'>('id')
const sortOrder = ref<'asc' | 'desc'>('asc')

// Row Actions Dropdown State
const activeRowDropdownId = ref<string | null>(null)

function toggleRowDropdown(id: string) {
  activeRowDropdownId.value = activeRowDropdownId.value === id ? null : id
}

// Pagination State
const currentPage = ref(1)
const itemsPerPage = ref(10)

function toggleSort(key: 'id' | 'question' | 'type' | 'difficulty' | 'marks' | 'status') {
  if (sortKey.value === key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortOrder.value = 'asc'
  }
}

// Available unique courses for filter
const availableCourses = computed(() => {
  const coursesSet = new Set(props.questions.map(q => q.course).filter(Boolean))
  return Array.from(coursesSet)
})

// Filtered & Sorted Questions
const filteredQuestions = computed(() => {
  const filtered = props.questions.filter(q => {
    const matchSearch = searchQuery.value === '' ||
      q.question.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      q.id.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (q.course && q.course.toLowerCase().includes(searchQuery.value.toLowerCase()))
    
    const matchType = selectedTypeFilter.value === 'all' || q.type === selectedTypeFilter.value
    const matchDiff = selectedDifficultyFilter.value === 'all' || q.difficulty === selectedDifficultyFilter.value
    const matchStatus = selectedStatusFilter.value === 'all' || q.status === selectedStatusFilter.value
    const matchCourse = selectedCourseFilter.value === 'all' || q.course === selectedCourseFilter.value

    return matchSearch && matchType && matchDiff && matchStatus && matchCourse
  })

  return filtered.sort((a, b) => {
    let valA = a[sortKey.value]
    let valB = b[sortKey.value]

    if (typeof valA === 'string') valA = valA.toLowerCase()
    if (typeof valB === 'string') valB = valB.toLowerCase()

    if (valA < valB) return sortOrder.value === 'asc' ? -1 : 1
    if (valA > valB) return sortOrder.value === 'asc' ? 1 : -1
    return 0
  })
})

// Paginated output
const totalPages = computed(() => Math.ceil(filteredQuestions.value.length / itemsPerPage.value) || 1)

const paginatedQuestions = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredQuestions.value.slice(start, start + itemsPerPage.value)
})

function goToPage(page: number) {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

function resetFilters() {
  searchQuery.value = ''
  selectedTypeFilter.value = 'all'
  selectedDifficultyFilter.value = 'all'
  selectedStatusFilter.value = 'all'
  selectedCourseFilter.value = 'all'
  currentPage.value = 1
}
</script>

<template>
  <div class="space-y-6 font-sans">
    <!-- ── KPI STAT CARDS ROW (WITH REAL FLATICON VECTOR SVG ICONS) ── -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Card 1: Total Questions -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-lg hover:border-blue-500/50 hover:bg-slate-800/95 transition-all duration-200 group">
        <div class="flex items-center gap-2.5">
          <div class="p-2 rounded-xl bg-blue-500/15 border border-blue-500/30 text-blue-400 shadow-inner group-hover:scale-105 transition-transform">
            <!-- Flaticon Question Bank Vector SVG -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
          <span class="text-xs text-slate-300 font-bold">Total Questions</span>
        </div>
        <div class="text-2xl font-black text-white mt-2.5 tracking-tight">{{ questions.length }} Questions</div>
        <div class="text-[11px] text-emerald-400 font-semibold mt-1 flex items-center gap-1">
          <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
          </svg>
          <span>+12% vs last month</span>
        </div>
      </div>

      <!-- Card 2: Published Questions -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-lg hover:border-emerald-500/50 hover:bg-slate-800/95 transition-all duration-200 group">
        <div class="flex items-center gap-2.5">
          <div class="p-2 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 shadow-inner group-hover:scale-105 transition-transform">
            <!-- Flaticon Verified/Published Vector SVG -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <span class="text-xs text-slate-300 font-bold">Published</span>
        </div>
        <div class="text-2xl font-black text-emerald-400 mt-2.5 tracking-tight">{{ questions.filter(q => q.status === 'published').length }} Questions</div>
        <div class="w-full bg-slate-700/60 rounded-full h-1.5 mt-2.5 overflow-hidden">
          <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full" style="width: 83%"></div>
        </div>
      </div>

      <!-- Card 3: Draft / Pending Questions -->
      <div class="bg-slate-800/90 border border-amber-900/40 rounded-2xl p-4 shadow-lg hover:border-amber-500/50 hover:bg-slate-800/95 transition-all duration-200 group">
        <div class="flex items-center gap-2.5">
          <div class="p-2 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 shadow-inner group-hover:scale-105 transition-transform">
            <!-- Flaticon Pending/Clock Edit Vector SVG -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <span class="text-xs text-amber-300 font-bold">Pending Draft</span>
        </div>
        <div class="text-2xl font-black text-amber-400 mt-2.5 tracking-tight">{{ questions.filter(q => q.status === 'draft').length }} Question</div>
        <div class="text-[11px] text-amber-300/80 font-medium mt-1">Pending verification</div>
      </div>

      <!-- Card 4: Linked Courses -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-lg hover:border-purple-500/50 hover:bg-slate-800/95 transition-all duration-200 group">
        <div class="flex items-center gap-2.5">
          <div class="p-2 rounded-xl bg-purple-500/15 border border-purple-500/30 text-purple-400 shadow-inner group-hover:scale-105 transition-transform">
            <!-- Flaticon Graduation/Courses Vector SVG -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14" />
            </svg>
          </div>
          <span class="text-xs text-slate-300 font-bold">Linked Courses</span>
        </div>
        <div class="text-2xl font-black text-white mt-2.5 tracking-tight">{{ stats?.courses_linked || 328 }} Courses</div>
        <div class="text-[11px] text-slate-400 font-medium mt-1">Integrated into modules</div>
      </div>
    </div>

    <!-- ── SINGLE INLINE CLEAN FILTER TOOLBAR ── -->
    <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-3 shadow-xl">
      <div class="flex flex-col sm:flex-row items-center gap-2.5">
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
            placeholder="Search question, ID, topic..."
            class="bg-slate-900/90 text-slate-200 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-purple-500 w-full transition-all"
          />
        </div>

        <!-- Inline Filters -->
        <div class="flex items-center gap-2 w-full sm:w-auto shrink-0 flex-wrap sm:flex-nowrap">
          <select v-model="selectedTypeFilter" class="bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-purple-500 cursor-pointer">
            <option value="all" class="bg-[#121827] text-slate-200">📑 All Types</option>
            <option value="mcq" class="bg-[#121827] text-slate-200">☑️ Multiple Choice</option>
            <option value="true_false" class="bg-[#121827] text-slate-200">⚖️ True / False</option>
            <option value="essay" class="bg-[#121827] text-slate-200">✍️ Essay</option>
            <option value="matching" class="bg-[#121827] text-slate-200">🔗 Matching</option>
            <option value="coding" class="bg-[#121827] text-slate-200">💻 Coding</option>
            <option value="fill_blank" class="bg-[#121827] text-slate-200">🔢 Fill Blank</option>
          </select>

          <select v-model="selectedDifficultyFilter" class="bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-purple-500 cursor-pointer">
            <option value="all" class="bg-[#121827] text-slate-200">🎯 All Difficulties</option>
            <option value="easy" class="bg-[#121827] text-slate-200">🌱 Easy</option>
            <option value="medium" class="bg-[#121827] text-slate-200">⚡ Medium</option>
            <option value="hard" class="bg-[#121827] text-slate-200">🔥 Hard</option>
          </select>

          <button
            @click="showCategoryPanel = !showCategoryPanel"
            class="px-3 py-2 bg-slate-900 hover:bg-slate-700 rounded-xl text-xs font-semibold text-slate-200 border border-slate-700 transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
          >
            <span class="text-amber-400">📁</span>
            <span>Categories</span>
          </button>

          <button
            v-if="searchQuery || selectedTypeFilter !== 'all' || selectedDifficultyFilter !== 'all'"
            @click="resetFilters"
            class="px-3 py-2 bg-purple-950/60 hover:bg-purple-900/60 text-purple-300 border border-purple-500/30 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0"
          >
            🔄 Clear
          </button>
        </div>
      </div>
    </div>

    <!-- ── LAYOUT GRID WITH CATEGORIES PANEL ── -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
      <!-- Categories Tree Panel -->
      <div v-if="showCategoryPanel" class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-xl space-y-3">
        <h3 class="font-bold text-sm text-white flex items-center justify-between border-b border-slate-700/60 pb-2">
          <span class="flex items-center gap-2">📂 Question Categories</span>
          <button @click="emit('openAddCategory')" class="text-xs bg-purple-600/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded hover:bg-purple-600/30 font-bold">＋ Add</button>
        </h3>
        <div class="space-y-3 text-xs max-h-96 overflow-y-auto pr-1">
          <div v-for="cat in categories" :key="cat.id" class="space-y-1.5">
            <div class="font-bold text-slate-200 flex justify-between">
              <span>📚 {{ cat.name }}</span>
              <span class="text-slate-400">({{ cat.total_questions }})</span>
            </div>
            <div class="pl-3 space-y-1 text-slate-300 border-l border-slate-700">
              <div v-for="m in cat.modules" :key="m.id" class="space-y-1">
                <div class="font-medium text-slate-300 flex justify-between">
                  <span>📁 {{ m.name }}</span>
                  <span class="text-slate-400">({{ m.count }})</span>
                </div>
                <div class="pl-3 space-y-0.5 text-slate-400 border-l border-slate-700">
                  <div v-for="c in m.chapters" :key="c.id" class="flex justify-between hover:text-purple-300 cursor-pointer">
                    <span>📄 {{ c.name }}</span>
                    <span>({{ c.count }})</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Question Container -->
      <div :class="showCategoryPanel ? 'lg:col-span-3' : 'lg:col-span-4'" class="bg-slate-800/90 border border-slate-700/80 rounded-2xl shadow-xl overflow-hidden">
        <!-- Clean Table Title Bar (Zero Redundant Text) -->
        <div class="p-3.5 border-b border-slate-700/80 flex justify-between items-center bg-slate-800">
          <div class="flex items-center gap-2">
            <h3 class="font-bold text-sm text-white">Questions List</h3>
            <span class="px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-300 font-extrabold text-xs border border-purple-500/20">
              {{ filteredQuestions.length }} Total
            </span>
          </div>
        </div>

        <!-- ── EMPTY STATE ── -->
        <div v-if="filteredQuestions.length === 0" class="p-12 text-center space-y-4">
          <div class="w-16 h-16 mx-auto rounded-full bg-slate-700/40 border border-slate-600 flex items-center justify-center">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
          </div>
          <div>
            <h4 class="text-base font-bold text-white">No questions found in pool</h4>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">No questions match your filter or search criteria. Try clearing filters or create a new question with AI Generator.</p>
          </div>
          <div class="flex items-center justify-center gap-3 pt-2">
            <button @click="resetFilters" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-xl text-xs font-semibold">
              🔄 Reset Filters
            </button>
            <button @click="emit('openAiGenerator')" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-semibold">
              🧠 AI Generator
            </button>
          </div>
        </div>

        <!-- ── DESKTOP TABLE VIEW WITH STANDARD [ EDIT ] [ ⋮ ] ACTIONS DROPDOWN ── -->
        <div v-else class="hidden md:block overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="bg-slate-900/90 text-slate-400 uppercase font-bold border-b border-slate-700 select-none">
                <!-- Clickable Headers with Sort Indicators -->
                <th @click="toggleSort('id')" class="p-3.5 cursor-pointer hover:bg-purple-900/30 hover:text-white transition-all group">
                  <div class="flex items-center gap-1.5">
                    <span>ID</span>
                    <span class="px-1 py-0.5 rounded bg-slate-800 text-[10px] font-black text-purple-400 border border-slate-700 group-hover:border-purple-400">
                      {{ sortKey === 'id' ? (sortOrder === 'asc' ? '▲' : '▼') : '⇅' }}
                    </span>
                  </div>
                </th>

                <th @click="toggleSort('question')" class="p-3.5 cursor-pointer hover:bg-purple-900/30 hover:text-white transition-all group">
                  <div class="flex items-center gap-1.5">
                    <span>Question Preview</span>
                    <span class="px-1 py-0.5 rounded bg-slate-800 text-[10px] font-black text-purple-400 border border-slate-700 group-hover:border-purple-400">
                      {{ sortKey === 'question' ? (sortOrder === 'asc' ? '▲' : '▼') : '⇅' }}
                    </span>
                  </div>
                </th>

                <th @click="toggleSort('type')" class="p-3.5 cursor-pointer hover:bg-purple-900/30 hover:text-white transition-all group">
                  <div class="flex items-center gap-1.5">
                    <span>Type</span>
                    <span class="px-1 py-0.5 rounded bg-slate-800 text-[10px] font-black text-purple-400 border border-slate-700 group-hover:border-purple-400">
                      {{ sortKey === 'type' ? (sortOrder === 'asc' ? '▲' : '▼') : '⇅' }}
                    </span>
                  </div>
                </th>

                <th @click="toggleSort('difficulty')" class="p-3.5 cursor-pointer hover:bg-purple-900/30 hover:text-white transition-all group">
                  <div class="flex items-center gap-1.5">
                    <span>Difficulty</span>
                    <span class="px-1 py-0.5 rounded bg-slate-800 text-[10px] font-black text-purple-400 border border-slate-700 group-hover:border-purple-400">
                      {{ sortKey === 'difficulty' ? (sortOrder === 'asc' ? '▲' : '▼') : '⇅' }}
                    </span>
                  </div>
                </th>

                <th @click="toggleSort('marks')" class="p-3.5 cursor-pointer hover:bg-purple-900/30 hover:text-white transition-all group">
                  <div class="flex items-center gap-1.5">
                    <span>Marks</span>
                    <span class="px-1 py-0.5 rounded bg-slate-800 text-[10px] font-black text-purple-400 border border-slate-700 group-hover:border-purple-400">
                      {{ sortKey === 'marks' ? (sortOrder === 'asc' ? '▲' : '▼') : '⇅' }}
                    </span>
                  </div>
                </th>

                <th @click="toggleSort('status')" class="p-3.5 cursor-pointer hover:bg-purple-900/30 hover:text-white transition-all group">
                  <div class="flex items-center gap-1.5">
                    <span>Status</span>
                    <span class="px-1 py-0.5 rounded bg-slate-800 text-[10px] font-black text-purple-400 border border-slate-700 group-hover:border-purple-400">
                      {{ sortKey === 'status' ? (sortOrder === 'asc' ? '▲' : '▼') : '⇅' }}
                    </span>
                  </div>
                </th>

                <th class="p-3.5 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/60">
              <tr
                v-for="q in paginatedQuestions"
                :key="q.id"
                @click="emit('openPreview', q)"
                class="hover:bg-slate-700/40 transition-colors group cursor-pointer"
              >
                <td class="p-3.5 font-mono text-purple-400 font-bold whitespace-nowrap">{{ q.id }}</td>
                <td class="p-3.5 max-w-xs sm:max-w-md">
                  <p class="font-semibold text-white truncate group-hover:text-purple-200 transition-colors" :title="q.question">{{ q.question }}</p>
                  <span class="text-[10px] text-slate-400 block truncate mt-0.5">{{ q.course }} · {{ q.module }}</span>
                </td>
                <td class="p-3.5 whitespace-nowrap">
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-purple-500/10 text-purple-300 border border-purple-500/20 flex items-center gap-1 w-max">
                    <span>{{ q.type_label }}</span>
                  </span>
                </td>
                <td class="p-3.5 whitespace-nowrap">
                  <span
                    v-if="q.difficulty === 'easy'"
                    class="px-2.5 py-1 rounded-full text-[10px] font-extrabold capitalize border bg-emerald-500/20 text-emerald-300 border-emerald-500/40 flex items-center gap-1.5 w-max"
                  >
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <span>Easy</span>
                  </span>

                  <span
                    v-else-if="q.difficulty === 'medium'"
                    class="px-2.5 py-1 rounded-full text-[10px] font-extrabold capitalize border bg-amber-500/20 text-amber-300 border-amber-500/40 flex items-center gap-1.5 w-max"
                  >
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M7 2v11h3v9l7-12h-4l4-8z"/>
                    </svg>
                    <span>Medium</span>
                  </span>

                  <span
                    v-else
                    class="px-2.5 py-1 rounded-full text-[10px] font-extrabold capitalize border bg-red-500/20 text-red-300 border-red-500/40 flex items-center gap-1.5 w-max"
                  >
                    <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M13.5 1.5c.5 1.5-.5 3-1.5 4-1.5 1.5-2 3.5-1 5.5.5 1 1 2 1 3.5 0 2.5-2 4.5-4.5 4.5S3 16.5 3 14c0-3 2.5-5.5 5-7.5 1-1 1.5-2.5 1.5-4 0-.5.5-1 1-1s1.5.5 2 1.5z"/>
                    </svg>
                    <span>Hard</span>
                  </span>
                </td>
                <td class="p-3.5 font-bold text-amber-400 whitespace-nowrap">
                  <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span>{{ q.marks }} Marks</span>
                  </span>
                </td>

                <!-- Status Badges with Flaticon Vector SVG Icons -->
                <td class="p-3.5 whitespace-nowrap">
                  <span
                    v-if="q.status === 'published'"
                    class="px-2.5 py-1 rounded-full text-[10px] font-extrabold capitalize border bg-emerald-500/20 text-emerald-300 border-emerald-500/40 flex items-center gap-1.5 w-max"
                  >
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Active</span>
                  </span>
                  <span
                    v-else-if="q.status === 'draft'"
                    class="px-2.5 py-1 rounded-full text-[10px] font-extrabold capitalize border bg-slate-700/80 text-slate-300 border-slate-600 flex items-center gap-1.5 w-max"
                  >
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Draft</span>
                  </span>
                  <span
                    v-else
                    class="px-2.5 py-1 rounded-full text-[10px] font-extrabold capitalize border bg-amber-500/20 text-amber-300 border-amber-500/40 flex items-center gap-1.5 w-max"
                  >
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Needs Review</span>
                  </span>
                </td>

                <!-- Standard Actions: [ Edit ] [ ⋮ ] Dropdown Menu -->
                <td class="p-3.5 text-right whitespace-nowrap" @click.stop>
                  <div class="flex items-center justify-end gap-1.5 relative">
                    <!-- Invisible Backdrop Overlay to close dropdown on click outside -->
                    <div
                      v-if="activeRowDropdownId === q.id"
                      @click.stop="activeRowDropdownId = null"
                      class="fixed inset-0 z-20 cursor-default"
                    ></div>

                    <!-- Primary Edit Button with Flaticon Pencil Vector SVG -->
                    <button
                      @click="emit('openEdit', q)"
                      title="Edit Question"
                      class="px-2.5 py-1 bg-amber-500/15 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 rounded-xl transition-colors text-xs font-semibold flex items-center gap-1.5 cursor-pointer shadow-sm relative z-10"
                    >
                      <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                      <span>Edit</span>
                    </button>

                    <!-- More Actions (⋮) Button with Active Highlight State -->
                    <div class="relative z-30">
                      <button
                        @click.stop="toggleRowDropdown(q.id)"
                        title="More Actions"
                        :class="[
                          activeRowDropdownId === q.id 
                            ? 'bg-purple-600/40 text-purple-200 border-purple-400 ring-2 ring-purple-500/40 shadow-lg shadow-purple-600/30' 
                            : 'bg-slate-700/60 text-slate-300 border-slate-600/60 hover:bg-slate-600/80',
                          'p-1.5 rounded-xl transition-all text-xs font-bold cursor-pointer flex items-center justify-center shadow-sm'
                        ]"
                      >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                      </button>

                      <!-- Dropdown Menu -->
                      <div
                        v-if="activeRowDropdownId === q.id"
                        class="absolute right-0 mt-1.5 w-44 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl z-40 py-1 divide-y divide-slate-700/60 text-xs text-left animate-fadeIn"
                      >
                        <div class="py-1">
                          <button
                            @click="emit('openAddToQuiz', q); activeRowDropdownId = null"
                            class="w-full px-3 py-1.5 text-purple-300 hover:bg-purple-950/60 flex items-center gap-2 font-medium transition-colors cursor-pointer"
                          >
                            <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Add to Quiz</span>
                          </button>
                          <button
                            @click="emit('openPreview', q); activeRowDropdownId = null"
                            class="w-full px-3 py-1.5 text-blue-300 hover:bg-blue-950/60 flex items-center gap-2 font-medium transition-colors cursor-pointer"
                          >
                            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>Quick Preview</span>
                          </button>
                        </div>
                        <div class="py-1">
                          <button
                            @click="emit('deleteQuestion', q.id); activeRowDropdownId = null"
                            class="w-full px-3 py-1.5 text-red-400 hover:bg-red-950/60 flex items-center gap-2 font-medium transition-colors cursor-pointer"
                          >
                            <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Delete</span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- ── MOBILE CARD VIEW (< md breakpoint) ── -->
        <div v-if="filteredQuestions.length > 0" class="block md:hidden p-4 space-y-3">
          <div
            v-for="q in paginatedQuestions"
            :key="q.id"
            class="bg-slate-900/80 border border-slate-700/80 rounded-xl p-3.5 space-y-2.5 shadow-md"
          >
            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
              <span class="font-mono text-purple-400 font-bold text-xs">{{ q.id }}</span>
              <div class="flex items-center gap-1.5">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/10 text-purple-300 border border-purple-500/20">
                  {{ q.type_label }}
                </span>
                <span
                  class="px-2 py-0.5 rounded-full text-[10px] font-bold border"
                  :class="q.status === 'published' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : (q.status === 'draft' ? 'bg-slate-700/60 text-slate-300' : 'bg-amber-500/20 text-amber-300 border-amber-500/40')"
                >
                  {{ q.status }}
                </span>
              </div>
            </div>

            <p class="font-bold text-white text-xs leading-snug">{{ q.question }}</p>
            <div class="text-[10px] text-slate-400">{{ q.course }} · {{ q.module }}</div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-800/80 text-xs">
              <div class="flex items-center gap-2">
                <span
                  v-if="q.difficulty === 'easy'"
                  class="px-2 py-0.5 rounded-full text-[10px] font-bold capitalize bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1"
                >
                  <svg class="w-3 h-3 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                  <span>Easy</span>
                </span>
                <span
                  v-else-if="q.difficulty === 'medium'"
                  class="px-2 py-0.5 rounded-full text-[10px] font-bold capitalize bg-amber-500/20 text-amber-300 border border-amber-500/30 flex items-center gap-1"
                >
                  <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M7 2v11h3v9l7-12h-4l4-8z"/></svg>
                  <span>Medium</span>
                </span>
                <span
                  v-else
                  class="px-2 py-0.5 rounded-full text-[10px] font-bold capitalize bg-red-500/20 text-red-300 border border-red-500/30 flex items-center gap-1"
                >
                  <svg class="w-3 h-3 text-red-400" fill="currentColor" viewBox="0 0 24 24"><path d="M13.5 1.5c.5 1.5-.5 3-1.5 4-1.5 1.5-2 3.5-1 5.5.5 1 1 2 1 3.5 0 2.5-2 4.5-4.5 4.5S3 16.5 3 14c0-3 2.5-5.5 5-7.5 1-1 1.5-2.5 1.5-4 0-.5.5-1 1-1s1.5.5 2 1.5z"/></svg>
                  <span>Hard</span>
                </span>
                <span class="font-bold text-amber-400 text-[11px]">⭐ {{ q.marks }} M</span>
              </div>

              <!-- Mobile SVG Action Buttons -->
              <div class="flex items-center gap-1.5">
                <button @click="emit('openPreview', q)" title="Preview Question" class="p-1.5 bg-blue-500/20 text-blue-300 rounded-lg">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                </button>
                <button @click="emit('openEdit', q)" title="Edit Question" class="p-1.5 bg-amber-500/20 text-amber-300 rounded-lg">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                </button>
                <button @click="emit('openAddToQuiz', q)" title="Add Question to Quiz" class="p-1.5 bg-purple-600/30 text-purple-300 rounded-lg">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </button>
                <button @click="emit('deleteQuestion', q.id)" title="Delete Question" class="p-1.5 bg-red-600/30 text-red-400 rounded-lg">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- ── PAGINATION CONTROLS FOOTER ── -->
        <div v-if="filteredQuestions.length > 0" class="p-4 bg-slate-900/90 border-t border-slate-700/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
          <div class="text-slate-400 font-semibold">
            Showing <strong class="text-white">{{ (currentPage - 1) * itemsPerPage + 1 }}</strong> to
            <strong class="text-white">{{ Math.min(currentPage * itemsPerPage, filteredQuestions.length) }}</strong> of
            <strong class="text-purple-300">{{ filteredQuestions.length }}</strong> questions
          </div>

          <div class="flex items-center gap-2">
            <span class="text-slate-400 text-xs">Per Page:</span>
            <select v-model="itemsPerPage" @change="currentPage = 1" class="bg-slate-800 text-slate-200 border border-slate-700 rounded-lg px-2 py-1 text-xs font-bold">
              <option :value="5">5</option>
              <option :value="10">10</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
            </select>

            <div class="flex items-center gap-1 ml-2">
              <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 disabled:opacity-40 disabled:cursor-not-allowed font-bold"
              >
                ◀ Prev
              </button>
              
              <span class="px-3 py-1 bg-purple-600/30 text-purple-300 border border-purple-500/40 rounded-lg font-black">
                {{ currentPage }} / {{ totalPages }}
              </span>

              <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 disabled:opacity-40 disabled:cursor-not-allowed font-bold"
              >
                Next ▶
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>
