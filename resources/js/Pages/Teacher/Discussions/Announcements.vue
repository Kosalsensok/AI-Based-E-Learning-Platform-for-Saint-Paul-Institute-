<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  announcements: Array<any>
  courses: Array<any>
}>()

const emit = defineEmits<{
  (e: 'createAnnouncement'): void
  (e: 'previewAnnouncement', item: any): void
  (e: 'viewMetrics', item: any): void
  (e: 'togglePin', item: any): void
  (e: 'sendAgain', item: any): void
  (e: 'exportList'): void
}>()

const searchQuery = ref('')
const selectedStatus = ref('all')
const selectedPriority = ref('all')

const filteredAnnouncements = computed(() => {
  return props.announcements.filter((a) => {
    const matchesSearch =
      a.title_kh.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      a.title_en.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      a.audience.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchesStatus = selectedStatus.value === 'all' || a.status === selectedStatus.value
    const matchesPriority = selectedPriority.value === 'all' || a.priority === selectedPriority.value

    return matchesSearch && matchesStatus && matchesPriority
  })
})

const getPriorityBadge = (priority: string) => {
  if (priority === 'high') return 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300'
  if (priority === 'normal') return 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'
  return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300'
}

const getStatusBadge = (status: string) => {
  if (status === 'published') return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300'
  return 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300'
}
</script>

<template>
  <div class="space-y-6">
    <!-- Top Action Toolbar & Search -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex flex-wrap items-center gap-3 flex-1">
        <div class="relative flex-1 min-w-[200px]">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">🔍</span>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search announcement title or audience..."
            class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 dark:text-white"
          />
        </div>

        <select
          v-model="selectedStatus"
          class="px-3 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-200"
        >
          <option value="all">Status: All</option>
          <option value="published">🟢 Published</option>
          <option value="scheduled">📅 Scheduled</option>
        </select>

        <select
          v-model="selectedPriority"
          class="px-3 py-2 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-200"
        >
          <option value="all">Priority: All</option>
          <option value="high">🔴 High</option>
          <option value="normal">🟡 Normal</option>
          <option value="low">🟢 Low</option>
        </select>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="emit('createAnnouncement')"
          class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl text-xs shadow-md transition flex items-center gap-1.5"
        >
          <span>➕</span> Create Announcement
        </button>

        <button
          @click="emit('exportList')"
          class="px-3 py-2.5 bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-slate-700 dark:text-white font-bold rounded-xl text-xs transition"
        >
          📤 Export
        </button>
      </div>
    </div>

    <!-- Announcements Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 dark:bg-gray-700/50 text-[11px] font-black text-slate-500 uppercase tracking-wider border-b border-slate-200 dark:border-gray-700">
              <th class="py-3.5 px-4">#</th>
              <th class="py-3.5 px-4">Title (KH / EN)</th>
              <th class="py-3.5 px-4">Audience</th>
              <th class="py-3.5 px-4">Priority</th>
              <th class="py-3.5 px-4">Status</th>
              <th class="py-3.5 px-4 text-right">Actions</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100 dark:divide-gray-700 text-xs">
            <tr
              v-for="(ann, idx) in filteredAnnouncements"
              :key="ann.id"
              class="hover:bg-slate-50/70 dark:hover:bg-gray-700/40 transition"
            >
              <td class="py-4 px-4 font-bold text-slate-400">
                0{{ idx + 1 }}
              </td>

              <td class="py-4 px-4 max-w-xs">
                <div class="space-y-0.5">
                  <div class="font-extrabold text-slate-900 dark:text-white flex items-center gap-1.5">
                    <span v-if="ann.pinned" class="text-amber-500">📌</span>
                    <span>{{ ann.title_kh }}</span>
                  </div>
                  <div class="text-[11px] text-slate-500 font-medium italic">
                    {{ ann.title_en }}
                  </div>
                </div>
              </td>

              <td class="py-4 px-4 font-semibold text-slate-700 dark:text-slate-300">
                {{ ann.audience }}
              </td>

              <td class="py-4 px-4">
                <span :class="['px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase', getPriorityBadge(ann.priority)]">
                  {{ ann.priority === 'high' ? '🔴 High' : ann.priority === 'normal' ? '🟡 Normal' : '🟢 Low' }}
                </span>
              </td>

              <td class="py-4 px-4">
                <span :class="['px-2.5 py-1 rounded-full text-[10px] font-extrabold capitalize', getStatusBadge(ann.status)]">
                  {{ ann.status === 'published' ? '🟢 Published' : '📅 Scheduled' }}
                </span>
              </td>

              <td class="py-4 px-4 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    @click="emit('previewAnnouncement', ann)"
                    class="px-2.5 py-1.5 bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-slate-700 dark:text-white font-bold rounded-lg text-[11px] transition"
                  >
                    👁 Preview
                  </button>

                  <button
                    @click="emit('viewMetrics', ann)"
                    class="px-2.5 py-1.5 bg-indigo-50 dark:bg-indigo-900/40 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 font-bold rounded-lg text-[11px] transition"
                  >
                    📊 Metrics
                  </button>

                  <button
                    @click="emit('togglePin', ann)"
                    class="px-2 py-1.5 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded-lg text-[11px] font-bold"
                  >
                    📌 {{ ann.pinned ? 'Unpin' : 'Pin' }}
                  </button>

                  <button
                    @click="emit('sendAgain', ann)"
                    class="px-2 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-[11px] font-bold"
                  >
                    📧 Send Again
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
