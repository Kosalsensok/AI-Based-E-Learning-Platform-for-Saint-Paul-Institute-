<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  deadlines: Array<any>
  courses: Array<any>
}>()

const emit = defineEmits(['openCreate', 'openEdit', 'openOverdue'])

const searchQuery = ref('')
const selectedCourse = ref<string | number>('all')
const selectedType = ref('all')
const selectedStatus = ref('all')

const filteredDeadlines = computed(() => {
  return props.deadlines.filter((item) => {
    const matchesSearch = !searchQuery.value || item.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCourse = selectedCourse.value === 'all' || item.course_id == selectedCourse.value
    const matchesType = selectedType.value === 'all' || item.linked_type === selectedType.value
    const matchesStatus = selectedStatus.value === 'all' || item.status === selectedStatus.value
    return matchesSearch && matchesCourse && matchesType && matchesStatus
  })
})

const extendDeadline = (id: number) => {
  router.post(route('teacher.calendar.deadline.extend', id), { days: 2 })
}

const remindDeadline = (id: number) => {
  router.post(route('teacher.calendar.deadline.remind', id))
}

const sendBulkReminders = () => {
  router.post(route('teacher.calendar.deadlines.bulk-remind'))
}

const getTypeBadge = (type: string) => {
  switch (type) {
    case 'quiz':
      return { label: '🟥 Post-Test / Quiz', class: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300 border-red-200' }
    case 'assignment':
      return { label: '🟧 Assignment', class: 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300 border-orange-200' }
    case 'payment':
      return { label: '🟨 Payment ABA', class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 border-yellow-200' }
    case 'module':
      return { label: '🟩 Practice Quiz', class: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-200' }
    default:
      return { label: '⬜ General', class: 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300 border-slate-200' }
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Top Metrics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-gray-800 p-4 rounded-3xl shadow-sm border border-red-200 dark:border-red-900/50 flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-red-100 dark:bg-red-900/50 text-red-600 flex items-center justify-center font-bold text-lg">
          🔴
        </div>
        <div>
          <p class="text-[11px] font-bold text-slate-400">Overdue Deadlines</p>
          <h4 class="text-lg font-black text-red-600">2 Deadlines</h4>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 p-4 rounded-3xl shadow-sm border border-amber-200 dark:border-amber-900/50 flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 flex items-center justify-center font-bold text-lg">
          🟡
        </div>
        <div>
          <p class="text-[11px] font-bold text-slate-400">Due in 48 Hours</p>
          <h4 class="text-lg font-black text-amber-600">3 Deadlines</h4>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 p-4 rounded-3xl shadow-sm border border-emerald-200 dark:border-emerald-900/50 flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 flex items-center justify-center font-bold text-lg">
          🟢
        </div>
        <div>
          <p class="text-[11px] font-bold text-slate-400">Upcoming Deadlines</p>
          <h4 class="text-lg font-black text-emerald-600">8 Deadlines</h4>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 p-4 rounded-3xl shadow-sm border border-slate-200 dark:border-gray-700 flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-gray-700 text-slate-600 flex items-center justify-center font-bold text-lg">
          ✅
        </div>
        <div>
          <p class="text-[11px] font-bold text-slate-400">Closed / Graded</p>
          <h4 class="text-lg font-black text-slate-700 dark:text-slate-200">24 Deadlines</h4>
        </div>
      </div>
    </div>

    <!-- AI Insight Banner -->
    <div class="bg-gradient-to-r from-purple-900 via-indigo-900 to-slate-900 text-white p-5 rounded-3xl shadow-lg border border-purple-500/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-2xl bg-purple-500/20 border border-purple-400/40 flex items-center justify-center text-2xl">
          🧠
        </div>
        <div>
          <h4 class="font-bold text-sm text-purple-200 uppercase tracking-wider text-[11px]">AI Smart Assistant Insight</h4>
          <p class="text-xs font-semibold text-white mt-0.5">
            "Post-Test Module 1 has 80/120 submitted. 40 students are at risk of missing the deadline."
          </p>
        </div>
      </div>

      <button
        @click="$emit('openOverdue', deadlines[0] || {})"
        class="px-4 py-2 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white rounded-xl text-xs font-bold shadow-md transition whitespace-nowrap self-start md:self-auto"
      >
        <span>🚨 View 40 Overdue Students</span>
      </button>
    </div>

    <!-- Controls Toolbar -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
          <h2 class="text-lg font-black text-slate-800 dark:text-white flex items-center gap-2">
            <span>⏰ DEADLINES MANAGEMENT</span>
          </h2>
          <p class="text-xs text-slate-500 mt-1">
            គ្រប់គ្រង និងផ្លាស់ប្តូរពេលវេលាផុតកំណត់សម្រាប់ Quiz, Assignment, Payment, Module Completion
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <button
            @click="$emit('openCreate')"
            class="px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-1.5"
          >
            <span>➕ Set New Deadline</span>
          </button>

          <button
            @click="sendBulkReminders"
            class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-1.5"
          >
            <span>📧 Send Reminder</span>
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-3 pt-2">
        <div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="🔍 Search quiz/assignment..."
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-medium text-slate-800 dark:text-white"
          />
        </div>

        <div>
          <select
            v-model="selectedCourse"
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-medium text-slate-800 dark:text-white"
          >
            <option value="all">Course: All Courses</option>
            <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
          </select>
        </div>

        <div>
          <select
            v-model="selectedType"
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-medium text-slate-800 dark:text-white"
          >
            <option value="all">Type: All Types</option>
            <option value="quiz">📝 Quiz / Post-Test</option>
            <option value="assignment">🟧 Assignment</option>
            <option value="payment">🟨 Payment ABA</option>
            <option value="module">📦 Module</option>
          </select>
        </div>

        <div>
          <select
            v-model="selectedStatus"
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-medium text-slate-800 dark:text-white"
          >
            <option value="all">Status: All Status</option>
            <option value="active">Active</option>
            <option value="extended">Extended (+2 days)</option>
            <option value="closed">Closed</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Deadlines Table -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-100/80 dark:bg-gray-700/80 text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-gray-700 uppercase tracking-wider text-[11px]">
              <th class="py-3.5 px-4 w-12 text-center">#</th>
              <th class="py-3.5 px-4">Title</th>
              <th class="py-3.5 px-4">Type</th>
              <th class="py-3.5 px-4">Due Date</th>
              <th class="py-3.5 px-4">Progress / Submissions</th>
              <th class="py-3.5 px-4 text-center">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-gray-700 font-medium text-slate-700 dark:text-slate-200">
            <tr v-for="(item, index) in filteredDeadlines" :key="item.id" class="hover:bg-slate-50/80 dark:hover:bg-gray-700/50 transition">
              <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-400">
                {{ String(index + 1).padStart(2, '0') }}
              </td>

              <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-white">
                <div class="flex items-center gap-2">
                  <span>{{ item.title }}</span>
                  <span v-if="item.status === 'extended'" class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300">Extended</span>
                </div>
                <p class="text-[11px] text-slate-400 font-normal truncate max-w-xs">{{ item.course?.title || 'C Programming Basics' }}</p>
              </td>

              <td class="py-3.5 px-4">
                <span :class="getTypeBadge(item.linked_type).class" class="px-2.5 py-1 rounded-full text-[11px] font-bold border inline-block">
                  {{ getTypeBadge(item.linked_type).label }}
                </span>
              </td>

              <td class="py-3.5 px-4 whitespace-nowrap">
                <div class="font-bold text-red-600 dark:text-red-400">
                  {{ item.due_at ? new Date(item.due_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : 'May 16' }}, 11:59 PM
                </div>
                <div class="text-[10px] text-slate-400">
                  {{ item.deadline_type === 'hard' ? '🔴 Hard Deadline' : '🟡 Soft (Late allowed)' }}
                </div>
              </td>

              <td class="py-3.5 px-4 w-48">
                <div class="space-y-1">
                  <div class="flex items-center justify-between text-[11px] font-bold">
                    <span class="text-slate-700 dark:text-slate-300">{{ item.submitted_count || 80 }}/{{ item.total_students || 120 }}</span>
                    <span class="text-slate-400">{{ Math.round(((item.submitted_count || 80)/(item.total_students || 120))*100) }}%</span>
                  </div>
                  <div class="w-full h-2 bg-slate-100 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div
                      class="h-full bg-gradient-to-r from-amber-500 to-emerald-500 rounded-full"
                      :style="{ width: `${((item.submitted_count || 80)/(item.total_students || 120))*100}%` }"
                    ></div>
                  </div>
                </div>
              </td>

              <td class="py-3.5 px-4">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    @click="extendDeadline(item.id)"
                    class="px-3 py-1.5 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-700 transition shadow-sm"
                  >
                    Extend
                  </button>

                  <button
                    @click="remindDeadline(item.id)"
                    class="px-3 py-1.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition shadow-sm"
                  >
                    Remind
                  </button>

                  <button
                    @click="$emit('openEdit', item)"
                    class="px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-200 transition"
                  >
                    Edit
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
