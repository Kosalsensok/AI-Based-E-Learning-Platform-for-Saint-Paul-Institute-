<script setup lang="ts">
import { ref, watch } from 'vue'
import type { LearningPathNode } from '../types'

const props = defineProps<{
  show: boolean
  nodeToEdit?: LearningPathNode | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', payload: { id?: number; title: string; status: 'completed' | 'in_progress' | 'next' | 'locked' }): void
}>()

const nodeTitle = ref('')
const nodeStatus = ref<'completed' | 'in_progress' | 'next' | 'locked'>('next')

watch(() => props.show, (newVal) => {
  if (newVal) {
    if (props.nodeToEdit) {
      nodeTitle.value = props.nodeToEdit.title
      nodeStatus.value = props.nodeToEdit.status as any
    } else {
      nodeTitle.value = 'New Learning Path Module'
      nodeStatus.value = 'next'
    }
  }
})

function handleSave() {
  if (!nodeTitle.value.trim()) return
  emit('save', {
    id: props.nodeToEdit ? props.nodeToEdit.id : Date.now(),
    title: nodeTitle.value,
    status: nodeStatus.value
  })
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-[#0e1322] border border-purple-500/40 rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl text-xs font-sans">
      <!-- Modal Header -->
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-3.5">
        <h3 class="font-black text-sm text-white flex items-center gap-2.5">
          <div class="p-2 rounded-xl bg-gradient-to-br from-purple-500/20 to-indigo-500/20 border border-purple-400/30 text-purple-300 shrink-0">
            <svg class="w-4 h-4 text-purple-300" viewBox="0 0 24 24" fill="none">
              <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="#A855F7" fill-opacity="0.3" stroke="#C084FC" stroke-width="1.8" />
            </svg>
          </div>
          <span>{{ nodeToEdit ? 'EDIT LEARNING PATH NODE' : 'CREATE LEARNING PATH NODE' }}</span>
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
          <label class="block text-slate-300 mb-1 font-semibold">Node Module Title:</label>
          <input
            v-model="nodeTitle"
            type="text"
            placeholder="e.g. Loops in C (For & While)"
            class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3.5 py-2 text-xs focus:border-purple-500 focus:outline-none font-medium"
          />
        </div>

        <div>
          <label class="block text-slate-300 mb-1 font-semibold">Node Status:</label>
          <select
            v-model="nodeStatus"
            class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-2 text-xs focus:border-purple-500 focus:outline-none font-medium cursor-pointer"
          >
            <option value="completed">Completed (🟢)</option>
            <option value="in_progress">In Progress (🟡)</option>
            <option value="next">Next Up (⭕)</option>
            <option value="locked">Locked (🔒)</option>
          </select>
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
          <span>{{ nodeToEdit ? 'Save Node Changes' : 'Add Node to Sequence' }}</span>
        </button>
      </div>
    </div>
  </div>
</template>
