<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', payload: { highScoreBranch: string; midScoreBranch: string; lowScoreBranch: string }): void
}>()

const highScoreBranch = ref('Advance directly to Step 4: Loops in C')
const midScoreBranch = ref('Go to Step 4: Loops + Assign Extra Practice Drill')
const lowScoreBranch = ref('Insert Remedial Node: Conditions Review ➔ Go to Step 4')

function handleSave() {
  emit('save', {
    highScoreBranch: highScoreBranch.value,
    midScoreBranch: midScoreBranch.value,
    lowScoreBranch: lowScoreBranch.value
  })
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-[#0e1322] border border-purple-500/40 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl text-xs font-sans">
      <!-- Modal Header -->
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-3.5">
        <h3 class="font-black text-sm text-white flex items-center gap-2.5">
          <div class="p-2 rounded-xl bg-gradient-to-br from-amber-500/20 to-purple-500/20 border border-amber-400/30 text-amber-300 shrink-0">
            <svg class="w-4 h-4 text-amber-300" viewBox="0 0 24 24" fill="none">
              <path d="M12 2L2 12L12 22L22 12L12 2Z" stroke="#F59E0B" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <span>EDIT DECISION NODE BRANCHES</span>
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
      <div class="space-y-3.5 bg-[#121827] p-4.5 rounded-xl border border-slate-700/80">
        <!-- High Score Branch -->
        <div class="p-3 bg-emerald-950/40 border border-emerald-500/30 rounded-xl space-y-1.5">
          <label class="block text-emerald-300 font-bold text-[11px]">High Score Branch (IF Score ≥ 80%):</label>
          <input
            v-model="highScoreBranch"
            type="text"
            class="w-full bg-slate-950 text-slate-200 border border-slate-700 rounded-lg px-3 py-1.5 text-xs focus:border-emerald-500 focus:outline-none"
          />
        </div>

        <!-- Mid Score Branch -->
        <div class="p-3 bg-amber-950/40 border border-amber-500/30 rounded-xl space-y-1.5">
          <label class="block text-amber-300 font-bold text-[11px]">Mid Score Branch (IF Score 50 – 79%):</label>
          <input
            v-model="midScoreBranch"
            type="text"
            class="w-full bg-slate-950 text-slate-200 border border-slate-700 rounded-lg px-3 py-1.5 text-xs focus:border-amber-500 focus:outline-none"
          />
        </div>

        <!-- Low Score Branch -->
        <div class="p-3 bg-red-950/40 border border-red-500/30 rounded-xl space-y-1.5">
          <label class="block text-red-300 font-bold text-[11px]">Low Score Remedial Branch (IF Score &lt; 50%):</label>
          <input
            v-model="lowScoreBranch"
            type="text"
            class="w-full bg-slate-950 text-slate-200 border border-slate-700 rounded-lg px-3 py-1.5 text-xs focus:border-red-500 focus:outline-none"
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
          <span>Save Decision Branches</span>
        </button>
      </div>
    </div>
  </div>
</template>
