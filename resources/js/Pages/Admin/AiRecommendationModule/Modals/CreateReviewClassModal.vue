<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  defaultTopic?: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', payload: { sessionTitle: string; instructor: string; date: string; maxCapacity: number }): void
}>()

const sessionTitle = ref('Live Review Session: Loops & Memory Management')
const instructor = ref('Prof. Sok Heng')
const scheduledDate = ref('2026-08-10 14:00')
const maxCapacity = ref(85)

function handleSave() {
  if (!sessionTitle.value.trim()) return
  emit('save', {
    sessionTitle: sessionTitle.value,
    instructor: instructor.value,
    date: scheduledDate.value,
    maxCapacity: maxCapacity.value
  })
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-[#0e1322] border border-purple-500/40 rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl text-xs font-sans">
      <!-- Header with Flaticon Megaphone Icon -->
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-3.5">
        <h3 class="font-black text-sm text-white flex items-center gap-2.5">
          <div class="p-2 rounded-xl bg-gradient-to-br from-purple-500/20 to-indigo-500/20 border border-purple-400/30 text-purple-300 shrink-0">
            <svg class="w-4 h-4 text-purple-300" viewBox="0 0 24 24" fill="none">
              <path d="M22 12C22 12 19 8 12 8H7L3 12H7V18C7 18 12 18 12 18L22 12Z" stroke="#C084FC" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M12 18V21" stroke="#C084FC" stroke-width="1.8" stroke-linecap="round" />
            </svg>
          </div>
          <span>SCHEDULE LIVE REVIEW CLASS</span>
        </h3>

        <button
          @click="emit('close')"
          class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
          </svg>
        </button>
      </div>

      <!-- Form Inputs -->
      <div class="space-y-3.5 bg-[#121827] p-4 rounded-xl border border-slate-700/80">
        <div>
          <label class="block text-slate-300 mb-1 font-semibold">Review Session Title:</label>
          <input
            v-model="sessionTitle"
            type="text"
            class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3.5 py-2 text-xs focus:border-purple-500 focus:outline-none font-medium"
          />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-slate-300 mb-1 font-semibold">Instructor Name:</label>
            <input
              v-model="instructor"
              type="text"
              class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-2 text-xs focus:border-purple-500 focus:outline-none"
            />
          </div>

          <div>
            <label class="block text-slate-300 mb-1 font-semibold">Max Student Capacity:</label>
            <input
              v-model="maxCapacity"
              type="number"
              class="w-full bg-slate-900 text-purple-300 font-bold border border-slate-700 rounded-xl px-3 py-2 text-xs focus:border-purple-500 focus:outline-none"
            />
          </div>
        </div>

        <div>
          <label class="block text-slate-300 mb-1 font-semibold">Scheduled Date & Time:</label>
          <input
            v-model="scheduledDate"
            type="text"
            class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3.5 py-2 text-xs focus:border-purple-500 focus:outline-none"
          />
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end gap-2.5 border-t border-slate-700/60 pt-4">
        <button
          @click="emit('close')"
          class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl font-semibold transition-all border border-slate-700"
        >
          Cancel
        </button>

        <button
          @click="handleSave"
          class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all active:scale-95 ring-1 ring-purple-400/50"
        >
          <svg class="w-4 h-4 text-purple-200" viewBox="0 0 24 24" fill="none">
            <path d="M5 13L9 17L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <span>Schedule & Notify Students</span>
        </button>
      </div>
    </div>
  </div>
</template>
