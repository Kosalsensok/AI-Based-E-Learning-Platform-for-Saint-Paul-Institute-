<script setup lang="ts">
import { ref, watch } from 'vue'
import type { WeakTopicItem } from '../types'

const props = defineProps<{
  show: boolean
  mappingToEdit?: WeakTopicItem | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', item: Partial<WeakTopicItem>): void
}>()

const topicTitle = ref('')
const targetCourse = ref('C Programming Basics')
const thresholdScore = ref(50)
const remedialContentStr = ref('')
const statusOn = ref(true)

watch(() => props.show, (newVal) => {
  if (newVal) {
    if (props.mappingToEdit) {
      topicTitle.value = props.mappingToEdit.topic
      targetCourse.value = props.mappingToEdit.course
      remedialContentStr.value = props.mappingToEdit.remedial_content.join(', ')
      statusOn.value = props.mappingToEdit.status === 'active'
    } else {
      topicTitle.value = ''
      targetCourse.value = 'C Programming Basics'
      remedialContentStr.value = '🎥 Re-watch "Lesson Overview", ✍ Practice Drill Quiz'
      statusOn.value = true
    }
  }
})

function handleSave() {
  if (!topicTitle.value.trim()) return

  const items = remedialContentStr.value
    .split(',')
    .map(s => s.trim())
    .filter(Boolean)

  emit('save', {
    id: props.mappingToEdit ? props.mappingToEdit.id : Date.now(),
    topic: topicTitle.value,
    course: targetCourse.value,
    affected_students: props.mappingToEdit ? props.mappingToEdit.affected_students : 12,
    avg_score: props.mappingToEdit ? props.mappingToEdit.avg_score : thresholdScore.value - 2,
    trend: props.mappingToEdit ? props.mappingToEdit.trend : 'down',
    auto_action: 'Remedial sent ✅',
    remedial_content: items.length ? items : ['🎥 Remedial Video Lesson'],
    status: statusOn.value ? 'active' : 'disabled'
  })
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-[#0e1322] border border-purple-500/40 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl text-xs font-sans">
      <!-- Header with Flaticon Vector Icon -->
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-3.5">
        <h3 class="font-black text-sm text-white flex items-center gap-2.5">
          <div class="p-2 rounded-xl bg-gradient-to-br from-purple-500/20 to-indigo-500/20 border border-purple-400/30 text-purple-300 shrink-0">
            <svg class="w-4 h-4 text-purple-300" viewBox="0 0 24 24" fill="none">
              <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="#A855F7" fill-opacity="0.3" stroke="#C084FC" stroke-width="1.8" />
            </svg>
          </div>
          <span>{{ mappingToEdit ? 'EDIT WEAK TOPIC MAPPING' : 'CREATE WEAK TOPIC MAPPING' }}</span>
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

      <!-- Form Fields -->
      <div class="space-y-3.5 bg-[#121827] p-4.5 rounded-xl border border-slate-700/80">
        <div>
          <label class="block text-slate-300 mb-1 font-semibold flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-purple-400" viewBox="0 0 24 24" fill="none">
              <path d="M4 7H20M4 12H15M4 17H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            <span>Weak Topic Title:</span>
          </label>
          <input
            v-model="topicTitle"
            type="text"
            placeholder="e.g. Loops in C (For & While)"
            class="w-full bg-slate-900 text-slate-200 border border-slate-700/90 rounded-xl px-3.5 py-2 text-xs focus:border-purple-500 focus:outline-none transition-all font-medium"
          />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-slate-300 mb-1 font-semibold flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-teal-400" viewBox="0 0 24 24" fill="none">
                <path d="M12 14L2 9L12 4L22 9L12 14Z" stroke="currentColor" stroke-width="1.8" />
                <path d="M4.5 10.25V14.25L12 18L19.5 14.25V10.25" stroke="currentColor" stroke-width="1.8" />
              </svg>
              <span>Target Course:</span>
            </label>
            <select
              v-model="targetCourse"
              class="w-full bg-slate-900 text-slate-200 border border-slate-700/90 rounded-xl px-3 py-2 text-xs focus:border-purple-500 focus:outline-none font-medium cursor-pointer"
            >
              <option value="C Programming Basics">C Programming Basics</option>
              <option value="Database Systems">Database Systems</option>
              <option value="English Grammar">English Grammar</option>
              <option value="Web Development">Web Development</option>
            </select>
          </div>

          <div>
            <label class="block text-slate-300 mb-1 font-semibold flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-red-400" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8" />
                <path d="M12 7V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
              </svg>
              <span>Trigger Score (&lt; %):</span>
            </label>
            <input
              v-model="thresholdScore"
              type="number"
              class="w-full bg-slate-900 text-red-400 font-bold border border-slate-700/90 rounded-xl px-3 py-2 text-xs focus:border-purple-500 focus:outline-none"
            />
          </div>
        </div>

        <div>
          <label class="block text-slate-300 mb-1 font-semibold flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-indigo-400" viewBox="0 0 24 24" fill="none">
              <path d="M4 6H20M4 12H20M4 18H12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            <span>Auto-Recommended Remedial Content (comma separated):</span>
          </label>
          <input
            v-model="remedialContentStr"
            type="text"
            placeholder='e.g. 🎥 Re-watch "Loops in C", ✍ Practice Quiz: Loops Drill'
            class="w-full bg-slate-900 text-slate-200 border border-slate-700/90 rounded-xl px-3.5 py-2 text-xs focus:border-purple-500 focus:outline-none font-medium"
          />
        </div>
      </div>

      <!-- Footer Buttons -->
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
            <path d="M19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H16L21 8V19C21 20.1046 20.1046 21 19 21Z" stroke="currentColor" stroke-width="1.8" />
            <path d="M17 21V13H7V21M7 3V8H14" stroke="currentColor" stroke-width="1.8" />
          </svg>
          <span>{{ mappingToEdit ? 'Save Mapping Changes' : 'Create Mapping' }}</span>
        </button>
      </div>
    </div>
  </div>
</template>
