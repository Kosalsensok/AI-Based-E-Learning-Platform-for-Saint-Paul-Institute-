<script setup lang="ts">
import { ref, computed } from 'vue'
import type { AssignmentItem } from './types'

const props = defineProps<{
  assignments: AssignmentItem[]
}>()

const emit = defineEmits<{
  (e: 'openCreate'): void
  (e: 'openGrading', item: any): void
  (e: 'deleteAssignment', id: string): void
}>()

const searchQuery = ref('')
const selectedCourseFilter = ref('all')
const selectedStatusFilter = ref('all')
const activeRowDropdownId = ref<string | null>(null)

const toggleRowDropdown = (id: string) => {
  activeRowDropdownId.value = activeRowDropdownId.value === id ? null : id
}

const courseOptions = computed(() => {
  const courses = new Set(props.assignments.map(a => a.course))
  return Array.from(courses)
})

const filteredAssignments = computed(() => {
  return props.assignments.filter(a => {
    const matchSearch = searchQuery.value === '' || 
      a.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      a.id.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      a.course.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      a.module.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchCourse = selectedCourseFilter.value === 'all' || a.course === selectedCourseFilter.value
    const matchStatus = selectedStatusFilter.value === 'all' || a.status === selectedStatusFilter.value

    return matchSearch && matchCourse && matchStatus
  })
})
</script>

<template>
  <div class="space-y-4 text-xs font-sans">
    <!-- Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-lg">
        <span class="text-xs text-slate-400 font-semibold">📎 Total Assignments</span>
        <h3 class="text-2xl font-black text-white mt-1">{{ assignments.length }}</h3>
        <p class="text-[11px] text-purple-400 mt-1">Course projects</p>
      </div>
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-lg">
        <span class="text-xs text-slate-400 font-semibold">⏳ Active</span>
        <h3 class="text-2xl font-black text-amber-400 mt-1">{{ assignments.filter(a => a.status === 'active').length }}</h3>
        <p class="text-[11px] text-slate-400 mt-1">Accepting student submissions</p>
      </div>
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-lg">
        <span class="text-xs text-slate-400 font-semibold">✅ Under Review</span>
        <h3 class="text-2xl font-black text-emerald-400 mt-1">{{ assignments.filter(a => a.status === 'review').length }}</h3>
        <p class="text-[11px] text-slate-400 mt-1">Evaluated by teachers</p>
      </div>
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-lg">
        <span class="text-xs text-slate-400 font-semibold">⏰ Closed</span>
        <h3 class="text-2xl font-black text-slate-400 mt-1">{{ assignments.filter(a => a.status === 'closed').length }}</h3>
        <p class="text-[11px] text-slate-400 mt-1">Past due date</p>
      </div>
    </div>

    <!-- ── 1. INLINE SEARCH & FILTER BAR ── -->
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
            placeholder="Search assignment title, ID, course..."
            class="bg-slate-900/90 text-slate-200 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-purple-500 w-full transition-all"
          />
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto shrink-0 flex-wrap sm:flex-nowrap justify-end">
          <!-- Course Filter -->
          <select v-model="selectedCourseFilter" class="bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-purple-500 cursor-pointer">
            <option value="all" class="bg-[#121827] text-slate-200">📚 All Courses</option>
            <option v-for="c in courseOptions" :key="c" :value="c" class="bg-[#121827] text-slate-200">{{ c }}</option>
          </select>

          <!-- Status Filter -->
          <select v-model="selectedStatusFilter" class="bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-purple-500 cursor-pointer">
            <option value="all" class="bg-[#121827] text-slate-200">⚡ All Statuses</option>
            <option value="active" class="bg-[#121827] text-slate-200">🟢 Active</option>
            <option value="review" class="bg-[#121827] text-slate-200">🟡 Review Required</option>
            <option value="closed" class="bg-[#121827] text-slate-200">⚪ Closed</option>
          </select>
        </div>
      </div>
    </div>

    <!-- ── 2. ASSIGNMENTS DATA TABLE ── -->
    <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl shadow-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="bg-slate-900/90 text-slate-400 uppercase font-bold text-[11px] border-b border-slate-700 select-none tracking-wider">
              <th class="py-3 px-3 pl-4 min-w-[170px]">Assignment Title</th>
              <th class="py-3 px-3">Course & Module</th>
              <th class="py-3 px-3">Due Date</th>
              <th class="py-3 px-3">Submissions</th>
              <th class="py-3 px-3">Formats</th>
              <th class="py-3 px-3">Status</th>
              <th class="py-3 px-3 pr-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60 font-medium">
            <tr v-for="asn in filteredAssignments" :key="asn.id" class="hover:bg-slate-700/30 transition-colors group">
              <!-- 1. TITLE & ID -->
              <td class="py-3 px-3 pl-4 min-w-[170px]">
                <p class="font-bold text-white group-hover:text-purple-300 transition-colors leading-snug">{{ asn.title }}</p>
                <span class="text-[10px] text-purple-400 font-mono font-bold block mt-0.5">{{ asn.id }}</span>
              </td>

              <!-- 2. COURSE & MODULE -->
              <td class="py-3 px-3 text-slate-300 whitespace-nowrap">
                <span class="font-bold text-slate-200 block">{{ asn.course }}</span>
                <span class="text-slate-400 block text-[11px] mt-0.5">📖 {{ asn.module }}</span>
              </td>

              <!-- 3. DUE DATE -->
              <td class="py-3 px-3 whitespace-nowrap">
                <div class="text-slate-200 font-mono font-semibold flex items-center gap-1">
                  <span>📅 {{ asn.due_date }}</span>
                </div>
              </td>

              <!-- 4. SUBMISSIONS WITH PROGRESS % -->
              <td class="py-3 px-3 whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <span class="font-bold text-indigo-300">
                    {{ asn.submitted_count ?? asn.submissions_count ?? 0 }} / {{ asn.total_students }}
                  </span>
                  <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-indigo-950/60 text-indigo-300 border border-indigo-500/30">
                    {{ Math.round(((asn.submitted_count ?? asn.submissions_count ?? 0) / (asn.total_students || 1)) * 100) }}%
                  </span>
                </div>
                <div class="w-24 bg-slate-900 rounded-full h-1.5 mt-1.5 overflow-hidden border border-slate-700">
                  <div
                    class="bg-gradient-to-r from-purple-500 to-indigo-500 h-full rounded-full transition-all duration-300"
                    :style="{ width: `${Math.min(100, Math.round(((asn.submitted_count ?? asn.submissions_count ?? 0) / (asn.total_students || 1)) * 100))}%` }"
                  ></div>
                </div>
              </td>

              <!-- 5. ALLOWED FORMATS -->
              <td class="py-3 px-3 text-slate-400 font-mono text-[11px] whitespace-nowrap">
                <span class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-slate-300">
                  {{ asn.file_types }}
                </span>
              </td>

              <!-- 6. STATUS BADGE -->
              <td class="py-3 px-3 whitespace-nowrap">
                <span
                  class="px-2.5 py-1 rounded-full text-[10px] font-extrabold flex items-center gap-1.5 w-max border shadow-sm"
                  :class="
                    asn.status === 'active' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 
                    (asn.status === 'review' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-slate-500/20 text-slate-400 border-slate-500/40')
                  "
                >
                  <svg v-if="asn.status === 'active'" class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <svg v-else-if="asn.status === 'review'" class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                  </svg>
                  <svg v-else class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                  </svg>
                  <span>{{ asn.status_label }}</span>
                </span>
              </td>

              <!-- 7. ACTIONS: GRADE + KEBAB MENU (⋮) -->
              <td class="p-3.5 pr-4 text-right whitespace-nowrap" @click.stop>
                <div class="flex items-center justify-end gap-1.5 relative">
                  <!-- Invisible Backdrop -->
                  <div
                    v-if="activeRowDropdownId === asn.id"
                    @click.stop="activeRowDropdownId = null"
                    class="fixed inset-0 z-20 cursor-default"
                  ></div>

                  <!-- Primary Grade Button -->
                  <button
                    @click="emit('openGrading', asn)"
                    class="px-3 py-1.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:brightness-110 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-md shadow-purple-600/20 active:scale-95 relative z-10"
                  >
                    <svg class="w-3.5 h-3.5 text-purple-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    <span>Grade</span>
                  </button>

                  <!-- More Options (⋮) Button -->
                  <div class="relative z-30">
                    <button
                      @click.stop="toggleRowDropdown(asn.id)"
                      title="More Options"
                      :class="activeRowDropdownId === asn.id ? 'bg-purple-600/40 text-purple-200 border-purple-400 ring-2 ring-purple-500/40 shadow-lg' : 'bg-slate-700/60 text-slate-300 hover:bg-slate-600/80 border-slate-600/60'"
                      class="p-1.5 rounded-xl border transition-all text-xs font-bold cursor-pointer flex items-center justify-center shadow-sm"
                    >
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                      </svg>
                    </button>

                    <!-- Kebab Menu Dropdown Popup -->
                    <div
                      v-if="activeRowDropdownId === asn.id"
                      class="absolute right-0 mt-1.5 w-44 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl z-40 py-1 divide-y divide-slate-700/60 text-xs text-left animate-fadeIn"
                    >
                      <div class="py-1">
                        <button
                          @click="emit('openGrading', asn); activeRowDropdownId = null"
                          class="w-full px-3 py-1.5 text-purple-300 hover:bg-purple-950/60 flex items-center gap-2 font-medium transition-colors cursor-pointer"
                        >
                          <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                          </svg>
                          <span>Review & Grade</span>
                        </button>
                      </div>
                      <div class="py-1">
                        <button
                          @click="emit('deleteAssignment', asn.id); activeRowDropdownId = null"
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

            <!-- Empty State -->
            <tr v-if="filteredAssignments.length === 0">
              <td colspan="7" class="p-8 text-center text-slate-400">
                No assignments found matching your search or filter criteria.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
