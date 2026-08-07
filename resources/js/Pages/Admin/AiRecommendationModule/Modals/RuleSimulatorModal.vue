<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'activateRule'): void
}>()

const simScore = ref(82)
const simCompletion = ref(75)
const simIdleDays = ref(0)
const simVideoWatch = ref(90)
const simMajor = ref('IT & Networking')

const simulationRun = ref(false)

function runSimulation() {
  simulationRun.value = true
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-[#0e1322] border border-purple-500/40 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl text-xs font-sans">
      <!-- Header with Flaticon Science Flask Icon -->
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-3.5">
        <h3 class="font-black text-sm text-white flex items-center gap-2.5">
          <div class="p-2 rounded-xl bg-gradient-to-br from-teal-500/20 to-emerald-500/20 border border-teal-400/30 text-teal-300 shrink-0">
            <svg class="w-4 h-4 text-teal-300" viewBox="0 0 24 24" fill="none">
              <path d="M9 3V8L4 16C3.2 17.3 4.1 19 5.7 19H18.3C19.9 19 20.8 17.3 20 16L15 8V3" stroke="#2DD4BF" stroke-width="1.8" stroke-linecap="round" />
              <path d="M9 3H15" stroke="#2DD4BF" stroke-width="1.8" stroke-linecap="round" />
              <circle cx="12" cy="15" r="1.5" fill="#A7F3D0" />
            </svg>
          </div>
          <span>RULE SIMULATOR — TEST BEFORE ACTIVATING</span>
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

      <!-- Input Student Profile -->
      <div class="space-y-3 bg-[#121827] p-4.5 rounded-xl border border-slate-700/80">
        <h4 class="font-bold text-xs text-slate-200 flex items-center gap-2">
          <svg class="w-3.5 h-3.5 text-purple-400" viewBox="0 0 24 24" fill="none">
            <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="1.8" />
          </svg>
          <span>Simulate a Student Profile:</span>
        </h4>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-slate-400 mb-1 font-semibold">Quiz Score (%):</label>
            <input v-model="simScore" type="number" class="w-full bg-slate-900 text-purple-300 font-bold border border-slate-700 rounded-xl px-3 py-1.5 focus:border-purple-500 focus:outline-none" />
          </div>

          <div>
            <label class="block text-slate-400 mb-1 font-semibold">Module Completion (%):</label>
            <input v-model="simCompletion" type="number" class="w-full bg-slate-900 text-teal-300 font-bold border border-slate-700 rounded-xl px-3 py-1.5 focus:border-purple-500 focus:outline-none" />
          </div>

          <div>
            <label class="block text-slate-400 mb-1 font-semibold">Idle Days:</label>
            <input v-model="simIdleDays" type="number" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-xl px-3 py-1.5 focus:border-purple-500 focus:outline-none" />
          </div>

          <div>
            <label class="block text-slate-400 mb-1 font-semibold">Video Watched (%):</label>
            <input v-model="simVideoWatch" type="number" class="w-full bg-slate-900 text-cyan-300 font-bold border border-slate-700 rounded-xl px-3 py-1.5 focus:border-purple-500 focus:outline-none" />
          </div>
        </div>

        <button
          @click="runSimulation"
          class="w-full py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white font-bold rounded-xl shadow-lg shadow-teal-600/30 flex items-center justify-center gap-2 transition-all active:scale-95 ring-1 ring-teal-400/50"
        >
          <svg class="w-4 h-4 text-emerald-100" viewBox="0 0 24 24" fill="none">
            <polygon points="5 3 19 12 5 21 5 3" fill="currentColor" />
          </svg>
          <span>Run Simulation</span>
        </button>
      </div>

      <!-- Simulation Result Banner -->
      <div v-if="simulationRun" class="space-y-2 p-4 bg-emerald-950/40 border border-emerald-500/40 rounded-xl">
        <span class="font-black text-xs text-emerald-300 flex items-center gap-2">
          <svg class="w-4 h-4 text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="9" fill="#10B981" fill-opacity="0.3" stroke="#34D399" stroke-width="1.8" />
            <path d="M8 12L11 15L16 9" stroke="#34D399" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <span>RESULT: Rule #01 "Advance High Performer" FIRED!</span>
        </span>

        <p class="text-slate-200 text-xs pl-6">
          → <strong>Action:</strong> Advance to next module<br />
          → <strong>Message:</strong> "Great job! You are ready for the next module 🎉"
        </p>

        <p class="text-[11px] text-slate-400 italic pl-6">
          (Also matched lower priority Rule #02 "Review Current Module" — skipped due to P1 priority win)
        </p>
      </div>

      <!-- Modal Footer -->
      <div class="flex justify-end gap-2.5 border-t border-slate-700/60 pt-4">
        <button
          @click="emit('close')"
          class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl font-semibold transition-all border border-slate-700"
        >
          Close
        </button>

        <button
          @click="emit('activateRule')"
          class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all active:scale-95 ring-1 ring-purple-400/50"
        >
          <svg class="w-4 h-4 text-purple-200" viewBox="0 0 24 24" fill="none">
            <path d="M19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H16L21 8V19C21 20.1046 20.1046 21 19 21Z" stroke="currentColor" stroke-width="1.8" />
            <path d="M17 21V13H7V21M7 3V8H14" stroke="currentColor" stroke-width="1.8" />
          </svg>
          <span>Activate This Rule</span>
        </button>
      </div>
    </div>
  </div>
</template>
