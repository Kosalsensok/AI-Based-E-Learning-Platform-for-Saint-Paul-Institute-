<script setup lang="ts">
import { ref, computed } from 'vue'
import type { QuizItem } from './types'

const props = defineProps<{
  quizzes: QuizItem[]
  currentTab?: string
  filterType?: string
}>()

const emit = defineEmits<{
  (e: 'openPreview', quiz: QuizItem): void
  (e: 'openEdit', quiz: QuizItem): void
  (e: 'toggleStatus', quiz: QuizItem): void
  (e: 'deleteQuiz', id: number): void
  (e: 'duplicateQuiz', quiz: QuizItem): void
  (e: 'openCreateQuiz'): void
  (e: 'openPreTestSim'): void
  (e: 'openPracticeSim'): void
  (e: 'openPostTestResult'): void
}>()

const searchQuery = ref('')
const selectedTypeFilter = ref('all')
const selectedStatusFilter = ref('all')
const viewMode = ref<'cards' | 'table'>('cards')
const currentPage = ref(1)
const itemsPerPage = ref(6)

const activeRowDropdownId = ref<number | null>(null)
const toggleRowDropdown = (id: number) => {
  activeRowDropdownId.value = activeRowDropdownId.value === id ? null : id
}

const filteredQuizzes = computed(() => {
  const targetType = props.filterType || props.currentTab || 'all'
  return props.quizzes.filter(qz => {
    const matchSearch = searchQuery.value === '' || 
      qz.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      qz.course.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      qz.module.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchType = (selectedTypeFilter.value !== 'all') 
      ? qz.type === selectedTypeFilter.value 
      : (targetType === 'all' || qz.type === targetType)

    const matchStatus = selectedStatusFilter.value === 'all' || qz.status === selectedStatusFilter.value

    return matchSearch && matchType && matchStatus
  })
})

const totalPages = computed(() => Math.ceil(filteredQuizzes.value.length / itemsPerPage.value) || 1)

const paginatedQuizzes = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredQuizzes.value.slice(start, start + itemsPerPage.value)
})
</script>

<template>
  <div class="space-y-4 text-xs font-sans">
    <!-- Quiz Banners per Type -->
    <div v-if="(filterType || currentTab) === 'pre_test'" class="p-4.5 rounded-2xl bg-gradient-to-r from-purple-950/60 via-indigo-950/40 to-slate-900 border border-purple-500/40 flex flex-col md:flex-row items-center justify-between gap-4 shadow-xl">
      <div class="space-y-1">
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 font-extrabold text-[10px] tracking-wide uppercase">Diagnostic Module</span>
          <h3 class="text-sm font-black text-white">🚀 Pre-Test Knowledge Assessment Engine</h3>
        </div>
        <p class="text-slate-300 text-xs leading-relaxed">Evaluates student baseline knowledge before module access. Unlocks custom learning paths.</p>
      </div>
      <button @click="emit('openCreateQuiz')" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold shadow-md shadow-purple-600/30 whitespace-nowrap cursor-pointer transition-all">
        ＋ Create Pre-Test
      </button>
    </div>

    <div v-else-if="(filterType || currentTab) === 'practice'" class="p-4.5 rounded-2xl bg-gradient-to-r from-amber-950/60 via-slate-900 to-amber-900/40 border border-amber-500/40 flex flex-col md:flex-row items-center justify-between gap-4 shadow-xl">
      <div class="space-y-1">
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-extrabold text-[10px] tracking-wide uppercase">Interactive Drill</span>
          <h3 class="text-sm font-black text-white">✍️ Unlimited Practice Drill Quizzes</h3>
        </div>
        <p class="text-slate-300 text-xs leading-relaxed">Self-paced practice drills with instant answer explanations & unlimited retry attempts.</p>
      </div>
      <button @click="emit('openPracticeSim')" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-600/30 whitespace-nowrap cursor-pointer transition-all">
        ▶ Test Student Practice Mode
      </button>
    </div>

    <div v-else-if="(filterType || currentTab) === 'post_test'" class="p-4.5 rounded-2xl bg-gradient-to-r from-emerald-950/60 via-slate-900 to-teal-900/40 border border-emerald-500/40 flex flex-col md:flex-row items-center justify-between gap-4 shadow-xl">
      <div class="space-y-1">
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-extrabold text-[10px] tracking-wide uppercase">Final Evaluation</span>
          <h3 class="text-sm font-black text-white">🏁 Post-Test Certificate Threshold Assessment</h3>
        </div>
        <p class="text-slate-300 text-xs leading-relaxed">Final summative exam required to pass course modules and earn completion certificates.</p>
      </div>
      <button @click="emit('openCreateQuiz')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-600/30 whitespace-nowrap cursor-pointer transition-all">
        ＋ Create Post-Test
      </button>
    </div>

    <!-- ── 1. UNIFIED HORIZONTAL TOOLBAR: SEARCH + TYPE/STATUS FILTERS + VIEW TOGGLE ── -->
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
            placeholder="Search quiz title, course, or module..."
            class="bg-slate-900/90 text-slate-200 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-purple-500 w-full transition-all"
          />
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto shrink-0 flex-wrap sm:flex-nowrap justify-end">
          <!-- Filter by Type -->
          <select v-model="selectedTypeFilter" class="bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-purple-500 cursor-pointer">
            <option value="all" class="bg-[#121827] text-slate-200">📑 All Types</option>
            <option value="pre_test" class="bg-[#121827] text-slate-200">📋 Pre-Tests</option>
            <option value="practice" class="bg-[#121827] text-slate-200">✍️ Practice Drills</option>
            <option value="post_test" class="bg-[#121827] text-slate-200">🏆 Post-Tests</option>
          </select>

          <!-- Filter by Status -->
          <select v-model="selectedStatusFilter" class="bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-purple-500 cursor-pointer">
            <option value="all" class="bg-[#121827] text-slate-200">⚡ All Statuses</option>
            <option value="published" class="bg-[#121827] text-slate-200">🟢 Published</option>
            <option value="draft" class="bg-[#121827] text-slate-200">🟡 Draft</option>
          </select>

          <!-- View Toggle Mode (Grid Cards vs Table) -->
          <div class="flex items-center bg-slate-900 p-1 rounded-xl border border-slate-700 shrink-0">
            <button
              @click="viewMode = 'cards'"
              :class="viewMode === 'cards' ? 'bg-purple-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-slate-200'"
              class="px-2.5 py-1 rounded-lg transition-all text-xs flex items-center gap-1.5 cursor-pointer"
              title="Grid Card View"
            >
              <svg class="w-3.5 h-3.5 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
              <span>Grid</span>
            </button>
            <button
              @click="viewMode = 'table'"
              :class="viewMode === 'table' ? 'bg-purple-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-slate-200'"
              class="px-2.5 py-1 rounded-lg transition-all text-xs flex items-center gap-1.5 cursor-pointer"
              title="Table View"
            >
              <svg class="w-3.5 h-3.5 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
              <span>Table</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ── 2. QUIZ CARD GRID VIEW (PREVIEW + EDIT ACTIONS IN FOOTER, DUPLICATE/DELETE IN KEBAB MENU) ── -->
    <div v-if="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div
        v-for="qz in paginatedQuizzes"
        :key="qz.id"
        class="bg-slate-800/90 border border-slate-700/80 hover:border-purple-500/50 rounded-2xl p-4 shadow-xl hover:shadow-2xl transition-all duration-200 flex flex-col justify-between space-y-3 group relative"
      >
        <!-- ── HEADER TIER: Type Pill + Title + Top-Right Status Badge + Kebab Menu (⋮) ── -->
        <div class="space-y-1.5 border-b border-slate-700/60 pb-3">
          <div class="flex items-center justify-between gap-2">
            <!-- TYPE BADGE -->
            <span
              class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border shrink-0 inline-flex items-center gap-1 shadow-sm"
              :class="
                qz.type === 'pre_test' ? 'bg-purple-950/80 text-purple-300 border-purple-500/40' : 
                (qz.type === 'practice' ? 'bg-amber-950/80 text-amber-300 border-amber-500/40' : 'bg-emerald-950/80 text-emerald-300 border-emerald-500/40')
              "
            >
              {{ qz.type === 'pre_test' ? '📋 Pre-Test' : (qz.type === 'practice' ? '✍️ Practice Quiz' : '🏁 Post-Test') }}
            </span>

            <div class="flex items-center gap-1.5">
              <!-- TOP-RIGHT COMPACT STATUS BADGE -->
              <button
                @click="emit('toggleStatus', qz)"
                class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border transition-all flex items-center gap-1.5 cursor-pointer shrink-0 shadow-sm"
                :class="qz.status === 'published' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 hover:bg-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/40 hover:bg-amber-500/30'"
              >
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="qz.status === 'published' ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2" :class="qz.status === 'published' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                </span>
                <span>{{ qz.status === 'published' ? 'Published' : 'Draft' }}</span>
              </button>

              <!-- MORE OPTIONS (⋮) DROPDOWN MENU -->
              <div class="relative" @click.stop>
                <div v-if="activeRowDropdownId === qz.id" @click.stop="activeRowDropdownId = null" class="fixed inset-0 z-20 cursor-default"></div>
                <button
                  @click.stop="toggleRowDropdown(qz.id)"
                  title="More Options"
                  :class="activeRowDropdownId === qz.id ? 'bg-purple-600/40 text-purple-200 border-purple-400 ring-2 ring-purple-500/40 shadow-lg' : 'bg-slate-700/60 text-slate-300 hover:bg-slate-600/80 border-slate-600/60'"
                  class="p-1 rounded-lg border transition-all text-xs font-bold cursor-pointer flex items-center justify-center shadow-sm relative z-30"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                  </svg>
                </button>

                <!-- Dropdown Popup -->
                <div
                  v-if="activeRowDropdownId === qz.id"
                  class="absolute right-0 mt-1.5 w-44 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl z-40 py-1 divide-y divide-slate-700/60 text-xs text-left animate-fadeIn"
                >
                  <div class="py-1">
                    <button
                      @click="emit('duplicateQuiz', qz); activeRowDropdownId = null"
                      class="w-full px-3 py-1.5 text-indigo-300 hover:bg-indigo-950/60 flex items-center gap-2 font-medium transition-colors cursor-pointer"
                    >
                      <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                      </svg>
                      <span>Duplicate</span>
                    </button>
                    <button
                      @click="emit('toggleStatus', qz); activeRowDropdownId = null"
                      class="w-full px-3 py-1.5 text-emerald-300 hover:bg-emerald-950/60 flex items-center gap-2 font-medium transition-colors cursor-pointer"
                    >
                      <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>{{ qz.status === 'published' ? 'Set as Draft' : 'Publish Quiz' }}</span>
                    </button>
                  </div>
                  <div class="py-1">
                    <button
                      @click="emit('deleteQuiz', qz.id); activeRowDropdownId = null"
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
          </div>

          <!-- QUIZ TITLE -->
          <h4 class="font-bold text-white text-sm group-hover:text-purple-300 transition-colors leading-snug tracking-tight">
            {{ qz.title }}
          </h4>
        </div>

        <!-- ── METADATA TIER: Creator | Course | Module ── -->
        <div class="flex items-center gap-2 text-slate-300 font-medium text-[11px] flex-wrap bg-slate-900/60 p-2.5 rounded-xl border border-slate-700/40">
          <span class="text-slate-200 font-semibold flex items-center gap-1">👤 {{ qz.created_by }}</span>
          <span class="text-slate-600">|</span>
          <span class="text-slate-100 font-bold flex items-center gap-1">📚 {{ qz.course }}</span>
          <span class="text-slate-600">|</span>
          <span class="text-slate-400 flex items-center gap-1">📖 {{ qz.module }}</span>
        </div>

        <!-- ── METRICS TIER: Questions | Duration | Pass Score ── -->
        <div class="flex items-center justify-between gap-2 text-[11px] bg-slate-900/40 p-2.5 rounded-xl border border-slate-700/30 flex-wrap">
          <div class="text-amber-400 font-bold flex items-center gap-1">
            <span>❓ {{ qz.questions_count }} Questions</span>
            <span class="text-slate-400 font-normal">({{ qz.total_marks }} Marks)</span>
          </div>
          <div class="text-slate-300 font-semibold flex items-center gap-1">
            <span>⏱️ {{ qz.time_limit_minutes }} mins</span>
          </div>
          <div class="text-emerald-400 font-extrabold flex items-center gap-1">
            <span>🎯 {{ qz.passing_score }}% Pass</span>
          </div>
        </div>

        <!-- ── FOOTER TIER: Primary & Secondary Actions (Preview & Edit Quiz) ── -->
        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-700/60">
          <button
            @click="emit('openPreview', qz)"
            title="Preview Quiz"
            class="px-3 py-1.5 bg-slate-700/60 hover:bg-slate-600/80 text-slate-200 border border-slate-600/60 rounded-xl transition-all text-xs font-semibold flex items-center gap-1.5 cursor-pointer shadow-sm active:scale-95"
          >
            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Preview</span>
          </button>

          <button
            @click="emit('openEdit', qz)"
            title="Edit Quiz"
            class="px-3 py-1.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:brightness-110 text-white rounded-xl transition-all text-xs font-bold flex items-center gap-1.5 cursor-pointer shadow-md shadow-purple-600/20 active:scale-95"
          >
            <svg class="w-3.5 h-3.5 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span>Edit Quiz</span>
          </button>
        </div>
      </div>
    </div>

    <!-- ── 3. STANDARDIZED TABLE VIEW (SPLIT QUESTIONS/TIME & MARKS/PASSING COLUMNS) ── -->
    <div v-else class="overflow-x-auto rounded-2xl border border-slate-700/80 bg-slate-800/90 shadow-xl">
      <table class="w-full text-left text-xs text-slate-300">
        <thead class="bg-slate-900/90 text-slate-400 uppercase font-bold text-[11px] border-b border-slate-700 select-none tracking-wider">
          <tr>
            <th class="p-3.5 pl-4">Quiz Title & Author</th>
            <th class="p-3.5">Course / Module</th>
            <th class="p-3.5">Questions / Duration</th>
            <th class="p-3.5">Marks / Passing</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 pr-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-700/60 font-medium">
          <tr v-for="qz in paginatedQuizzes" :key="qz.id" class="hover:bg-slate-700/30 transition-colors group">
            <!-- 1. QUIZ TITLE + CATEGORY BADGE + CREATOR -->
            <td class="p-3.5 pl-4 max-w-sm">
              <div class="space-y-1.5">
                <div>
                  <span
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border shrink-0 inline-block"
                    :class="
                      qz.type === 'pre_test' ? 'bg-purple-950/70 text-purple-300 border-purple-500/40' : 
                      (qz.type === 'practice' ? 'bg-amber-950/70 text-amber-300 border-amber-500/40' : 'bg-emerald-950/70 text-emerald-300 border-emerald-500/40')
                    "
                  >
                    {{ qz.type === 'pre_test' ? '📋 Pre-Test' : (qz.type === 'practice' ? '✍️ Practice Quiz' : '🏁 Post-Test') }}
                  </span>
                </div>
                <h5 class="font-bold text-white text-sm group-hover:text-purple-300 transition-colors leading-snug">
                  {{ qz.title }}
                </h5>
                <div class="text-[11px] text-slate-400">
                  Created by <strong class="text-slate-300 font-semibold">{{ qz.created_by }}</strong>
                </div>
              </div>
            </td>

            <!-- 2. COURSE / MODULE -->
            <td class="p-3.5">
              <div class="text-slate-200 font-bold leading-tight">{{ qz.course }}</div>
              <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                <span>📖</span>
                <span>{{ qz.module }}</span>
              </div>
            </td>

            <!-- 3. QUESTIONS / DURATION -->
            <td class="p-3.5 whitespace-nowrap">
              <div class="text-slate-200 font-bold flex items-center gap-1">
                <span>❓ {{ qz.questions_count }} Questions</span>
              </div>
              <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                <span>⏱️ {{ qz.time_limit_minutes }} mins</span>
              </div>
            </td>

            <!-- 4. MARKS / PASSING -->
            <td class="p-3.5 whitespace-nowrap">
              <div class="text-amber-400 font-bold flex items-center gap-1">
                <span>★ {{ qz.total_marks }} Marks</span>
              </div>
              <div class="text-[11px] text-emerald-400 font-semibold mt-1 flex items-center gap-1">
                <span>🎯 Pass: {{ qz.passing_score }}%</span>
              </div>
            </td>

            <!-- 5. STATUS BADGE -->
            <td class="p-3.5 whitespace-nowrap">
              <button
                @click="emit('toggleStatus', qz)"
                class="px-3 py-1 rounded-full text-[10px] font-extrabold capitalize border transition-all flex items-center gap-1.5 cursor-pointer shadow-sm"
                :class="qz.status === 'published' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 hover:bg-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/40 hover:bg-amber-500/30'"
                :title="qz.status === 'published' ? 'Click to set as Draft' : 'Click to Publish'"
              >
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="qz.status === 'published' ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2" :class="qz.status === 'published' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                </span>
                <span>{{ qz.status === 'published' ? 'Published' : 'Draft' }}</span>
              </button>
            </td>

            <!-- 6. ACTIONS WITH [ EDIT ] [ ⋮ ] DROPDOWN POPUP -->
            <td class="p-3.5 pr-4 text-right whitespace-nowrap" @click.stop>
              <div class="flex items-center justify-end gap-1.5 relative">
                <!-- Invisible Backdrop Overlay -->
                <div
                  v-if="activeRowDropdownId === qz.id"
                  @click.stop="activeRowDropdownId = null"
                  class="fixed inset-0 z-20 cursor-default"
                ></div>

                <!-- Primary Edit Button with Pencil Icon -->
                <button
                  @click="emit('openEdit', qz)"
                  title="Edit Quiz"
                  class="px-2.5 py-1 bg-amber-500/15 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 rounded-xl transition-colors text-xs font-semibold flex items-center gap-1.5 cursor-pointer shadow-sm relative z-10"
                >
                  <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                  <span>Edit</span>
                </button>

                <!-- More Actions (⋮) Button -->
                <div class="relative z-30">
                  <button
                    @click.stop="toggleRowDropdown(qz.id)"
                    title="More Actions"
                    :class="[
                      activeRowDropdownId === qz.id 
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
                    v-if="activeRowDropdownId === qz.id"
                    class="absolute right-0 mt-1.5 w-48 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl z-40 py-1 divide-y divide-slate-700/60 text-xs text-left animate-fadeIn"
                  >
                    <div class="py-1">
                      <button
                        @click="emit('openPreview', qz); activeRowDropdownId = null"
                        class="w-full px-3 py-1.5 text-blue-300 hover:bg-blue-950/60 flex items-center gap-2 font-medium transition-colors cursor-pointer"
                      >
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>Preview Quiz</span>
                      </button>
                      <button
                        @click="emit('duplicateQuiz', qz); activeRowDropdownId = null"
                        class="w-full px-3 py-1.5 text-indigo-300 hover:bg-indigo-950/60 flex items-center gap-2 font-medium transition-colors cursor-pointer"
                      >
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span>Duplicate</span>
                      </button>
                      <button
                        @click="emit('toggleStatus', qz); activeRowDropdownId = null"
                        class="w-full px-3 py-1.5 text-emerald-300 hover:bg-emerald-950/60 flex items-center gap-2 font-medium transition-colors cursor-pointer"
                      >
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ qz.status === 'published' ? 'Set as Draft' : 'Publish Quiz' }}</span>
                      </button>
                    </div>
                    <div class="py-1">
                      <button
                        @click="emit('deleteQuiz', qz.id); activeRowDropdownId = null"
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

    <!-- ── 4. RICH EMPTY STATE ── -->
    <div v-if="filteredQuizzes.length === 0" class="p-10 bg-slate-800/90 border border-slate-700/80 rounded-2xl text-center space-y-3">
      <div class="w-14 h-14 mx-auto rounded-2xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-2xl text-purple-400 shadow-inner">
        📝
      </div>
      <div class="space-y-1">
        <h4 class="text-sm font-bold text-white">No quizzes found</h4>
        <p class="text-xs text-slate-400 max-w-xs mx-auto">No quizzes match your search or filter criteria.</p>
      </div>
      <button @click="emit('openCreateQuiz')" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:brightness-110 text-white rounded-xl text-xs font-bold inline-flex items-center gap-1.5 shadow-md shadow-purple-600/30 transition-all cursor-pointer">
        <span>＋ Create New Quiz</span>
      </button>
    </div>

    <!-- ── 5. STREAMLINED PAGINATION FOOTER ── -->
    <div v-if="filteredQuizzes.length > 0" class="p-3 border border-slate-700/80 bg-slate-800/90 rounded-2xl flex items-center justify-end gap-2 text-xs text-slate-400 shadow-lg">
      <button
        @click="currentPage--"
        :disabled="currentPage === 1"
        class="px-3 py-1.5 bg-slate-900 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed border border-slate-700 rounded-xl text-slate-200 font-semibold cursor-pointer transition-all"
      >
        ← Previous
      </button>
      <span class="px-3.5 py-1.5 bg-purple-950/60 border border-purple-500/30 text-purple-300 font-bold rounded-xl">
        {{ currentPage }} / {{ totalPages }}
      </span>
      <button
        @click="currentPage++"
        :disabled="currentPage === totalPages"
        class="px-3 py-1.5 bg-slate-900 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed border border-slate-700 rounded-xl text-slate-200 font-semibold cursor-pointer transition-all"
      >
        Next →
      </button>
    </div>
  </div>
</template>
