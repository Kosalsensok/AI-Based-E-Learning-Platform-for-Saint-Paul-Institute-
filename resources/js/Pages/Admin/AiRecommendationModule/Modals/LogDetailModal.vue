<script setup lang="ts">
import type { RecommendationLogItem } from '../types'

const props = defineProps<{
  show: boolean
  log?: RecommendationLogItem | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()
</script>

<template>
  <div v-if="show && log" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-[#0e1322] border border-purple-500/40 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl text-xs font-sans">
      <!-- Modal Header -->
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-3.5">
        <h3 class="font-black text-sm text-white flex items-center gap-2.5">
          <div class="p-2 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-indigo-400/30 text-indigo-300 shrink-0">
            <svg class="w-4 h-4 text-indigo-300" viewBox="0 0 24 24" fill="none">
              <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="#818CF8" stroke-width="1.8" />
              <path d="M14 2V8H20" stroke="#818CF8" stroke-width="1.8" />
              <path d="M16 13H8M16 17H8M10 9H8" stroke="#A5B4FC" stroke-width="1.8" stroke-linecap="round" />
            </svg>
          </div>
          <span>RECOMMENDATION AUDIT PAYLOAD (LOG #{{ log.id }})</span>
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

      <!-- Log Details -->
      <div class="space-y-3 bg-[#121827] p-4 rounded-xl border border-slate-700/80">
        <div class="grid grid-cols-2 gap-3 pb-3 border-b border-slate-800">
          <div>
            <span class="text-slate-400 text-[10px] block font-semibold">STUDENT:</span>
            <span class="font-bold text-white text-xs">{{ log.student_name }}</span>
            <span class="text-[10px] font-mono text-purple-400 block">{{ log.student_id }}</span>
          </div>

          <div>
            <span class="text-slate-400 text-[10px] block font-semibold">TIMESTAMP:</span>
            <span class="font-bold text-slate-200 text-xs font-mono">{{ log.timestamp }}</span>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3 pb-3 border-b border-slate-800">
          <div>
            <span class="text-slate-400 text-[10px] block font-semibold">TRIGGERED RULE:</span>
            <span class="px-2 py-0.5 rounded bg-purple-950/80 text-purple-300 border border-purple-500/30 text-[10px] font-mono mr-1">{{ log.rule_code }}</span>
            <span class="font-bold text-slate-200 text-xs">{{ log.rule_name }}</span>
          </div>

          <div>
            <span class="text-slate-400 text-[10px] block font-semibold">DELIVERY STATUS:</span>
            <span class="font-bold text-emerald-400 text-xs">{{ log.status }}</span>
          </div>
        </div>

        <div>
          <span class="text-slate-400 text-[10px] block font-semibold mb-1">RECOMMENDATION ACTION DELIVERED:</span>
          <p class="text-slate-200 bg-slate-900 p-2.5 rounded-lg border border-slate-800 font-medium text-xs">
            "{{ log.recommendation_text }}"
          </p>
        </div>

        <!-- Raw JSON Payload Sandbox -->
        <div>
          <span class="text-slate-400 text-[10px] block font-semibold mb-1">AUDIT JSON PAYLOAD:</span>
          <pre class="bg-[#0a0d18] text-teal-300 p-3 rounded-lg border border-slate-800 text-[11px] font-mono overflow-x-auto">
{
  "log_id": {{ log.id }},
  "student_id": "{{ log.student_id }}",
  "rule_code": "{{ log.rule_code }}",
  "action": "{{ log.recommendation_text }}",
  "confidence_score": 0.94,
  "engine_version": "v2.4-hybrid"
}
          </pre>
        </div>
      </div>

      <!-- Footer Button -->
      <div class="flex justify-end border-t border-slate-700/60 pt-4">
        <button
          @click="emit('close')"
          class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl font-bold transition-all border border-slate-700"
        >
          Close Audit Window
        </button>
      </div>
    </div>
  </div>
</template>
