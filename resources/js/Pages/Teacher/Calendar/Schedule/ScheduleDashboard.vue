<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  schedules: Array<any>
  courses: Array<any>
}>()

const emit = defineEmits(['openCreate', 'openEdit', 'openLobby'])

const searchQuery = ref('')
const selectedCourse = ref<string | number>('all')
const selectedType = ref('all')
const selectedStatus = ref('all')

const filteredSchedules = computed(() => {
  return props.schedules.filter((item) => {
    const matchesSearch = !searchQuery.value || item.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCourse = selectedCourse.value === 'all' || item.course_id == selectedCourse.value
    const matchesType = selectedType.value === 'all' || item.type === selectedType.value
    const matchesStatus = selectedStatus.value === 'all' || item.status === selectedStatus.value
    return matchesSearch && matchesCourse && matchesType && matchesStatus
  })
})

const syncGoogle = () => {
  router.post(route('teacher.calendar.sync-google'))
}

const deleteSchedule = (id: number) => {
  if (confirm('តើអ្នកពិតជាចង់លុបកាលវិភាគនេះមែនទេ?')) {
    router.delete(route('teacher.calendar.schedule.destroy', id))
  }
}

const getTypeBadge = (type: string) => {
  switch (type) {
    case 'live_class':
      return { label: '🟦 Live Class', class: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border-blue-200' }
    case 'qa_session':
      return { label: '🟪 Q&A Session', class: 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 border-purple-200' }
    case 'office_hour':
      return { label: '🏢 Office Hour', class: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300 border-indigo-200' }
    case 'exam':
      return { label: '🟥 Exam Review', class: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300 border-red-200' }
    default:
      return { label: '⬜ General', class: 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300 border-slate-200' }
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header / Actions Toolbar -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
          <h2 class="text-lg font-black text-slate-800 dark:text-white flex items-center gap-2">
            <span>📌 SCHEDULE MANAGEMENT</span>
          </h2>
          <p class="text-xs text-slate-500 mt-1">
            គ្រប់គ្រងកាលវិភាគបង្រៀនផ្ទាល់, Q&A, Office Hour, Exam សម្រាប់សិស្សរៀនបែប Instructor-Led
          </p>
        </div>

        <!-- Create Actions -->
        <div class="flex flex-wrap items-center gap-2">
          <button
            @click="$emit('openCreate', 'live_class')"
            class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-1.5"
          >
            <span>➕ Create Live Class</span>
          </button>

          <button
            @click="$emit('openCreate', 'qa_session')"
            class="px-3.5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-1.5"
          >
            <span>➕ Q&A Session</span>
          </button>

          <button
            @click="$emit('openCreate', 'office_hour')"
            class="px-3.5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-1.5"
          >
            <span>➕ Office Hour</span>
          </button>

          <button
            @click="syncGoogle"
            class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-1.5"
          >
            <span>📅 Sync to Google</span>
          </button>
        </div>
      </div>

      <!-- Search & Filters -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-3 pt-2">
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="🔍 Search schedule title..."
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
            <option value="live_class">🟦 Live Class</option>
            <option value="qa_session">🟪 Q&A Session</option>
            <option value="office_hour">🏢 Office Hour</option>
            <option value="exam">🟥 Exam Review</option>
          </select>
        </div>

        <div>
          <select
            v-model="selectedStatus"
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-medium text-slate-800 dark:text-white"
          >
            <option value="all">Status: All Status</option>
            <option value="upcoming">Upcoming</option>
            <option value="live">Live Now</option>
            <option value="completed">Completed</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-100/80 dark:bg-gray-700/80 text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-gray-700 uppercase tracking-wider text-[11px]">
              <th class="py-3.5 px-4 w-12 text-center">#</th>
              <th class="py-3.5 px-4">Schedule Title</th>
              <th class="py-3.5 px-4">Type</th>
              <th class="py-3.5 px-4">Course</th>
              <th class="py-3.5 px-4">Date & Time</th>
              <th class="py-3.5 px-4 text-center">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-gray-700 font-medium text-slate-700 dark:text-slate-200">
            <tr v-for="(item, index) in filteredSchedules" :key="item.id" class="hover:bg-slate-50/80 dark:hover:bg-gray-700/50 transition">
              <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-400">
                {{ String(index + 1).padStart(2, '0') }}
              </td>

              <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-white">
                <div class="flex items-center gap-2">
                  <span>{{ item.title }}</span>
                  <span v-if="item.status === 'live'" class="px-2 py-0.5 rounded-full text-[9px] font-black bg-red-500 text-white animate-pulse">LIVE</span>
                </div>
                <p v-if="item.description" class="text-[11px] text-slate-400 font-normal truncate max-w-xs">{{ item.description }}</p>
              </td>

              <td class="py-3.5 px-4">
                <span :class="getTypeBadge(item.type).class" class="px-2.5 py-1 rounded-full text-[11px] font-bold border inline-block">
                  {{ getTypeBadge(item.type).label }}
                </span>
              </td>

              <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300 font-medium">
                {{ item.course?.title || 'C Programming Basics' }}
              </td>

              <td class="py-3.5 px-4 whitespace-nowrap">
                <div class="font-bold text-slate-800 dark:text-slate-100">
                  {{ item.start_at ? new Date(item.start_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'May 15, 2025' }}
                </div>
                <div class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold">
                  {{ item.start_at ? new Date(item.start_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '02:00 PM' }}
                </div>
              </td>

              <td class="py-3.5 px-4">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    @click="$emit('openLobby', item)"
                    class="px-3 py-1.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition shadow-sm"
                  >
                    Join
                  </button>

                  <button
                    @click="$emit('openEdit', item)"
                    class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-200 transition"
                  >
                    Edit
                  </button>

                  <button
                    @click="deleteSchedule(item.id)"
                    class="px-2.5 py-1.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 font-bold transition"
                  >
                    Cancel
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="filteredSchedules.length === 0">
              <td colspan="6" class="py-8 text-center text-slate-400">
                មិនមានកាលវិភាគដែលត្រូវនឹងការស្វែងរកឡើយ
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer Stats -->
      <div class="px-6 py-4 bg-slate-100/90 dark:bg-gray-700/90 border-t border-slate-200 dark:border-gray-700 font-bold text-xs text-slate-700 dark:text-slate-200 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span>📊 THIS WEEK: 5 Live Classes · 2 Q&A · 120 Attendees expected</span>
        </div>
        <span class="text-blue-600 dark:text-blue-400 font-semibold">Total Schedules: {{ schedules.length }}</span>
      </div>
    </div>
  </div>
</template>
