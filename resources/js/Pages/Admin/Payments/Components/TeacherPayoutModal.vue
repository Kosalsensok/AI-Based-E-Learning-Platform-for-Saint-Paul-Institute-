<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  teacherData: any
}>()

const emit = defineEmits(['close', 'confirm-payout'])

const reference = ref('ABA-PAYOUT-' + Math.floor(100000 + Math.random() * 900000))

const handleConfirm = () => {
  emit('confirm-payout', {
    teacher: props.teacherData,
    amount: props.teacherData.pending || props.teacherData.pending_payout || 0,
    reference: reference.value
  })
  emit('close')
}
</script>

<template>
  <div v-if="show && teacherData" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden my-6 transform transition-all">
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
        <div>
          <h3 class="text-base font-bold flex items-center gap-2">
            <span>💵</span> Process Teacher Payout
          </h3>
          <p class="text-xs text-slate-300">Teacher: {{ teacherData.teacher }}</p>
        </div>
        <button @click="$emit('close')" class="text-slate-400 hover:text-white text-xl font-bold">×</button>
      </div>

      <div class="p-6 space-y-4 text-xs text-slate-700">
        <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-200 space-y-1">
          <div class="text-[10px] uppercase font-bold text-emerald-800">Pending Payout</div>
          <div class="text-2xl font-black text-emerald-700">${{ (teacherData.pending || teacherData.pending_payout || 0).toFixed(2) }} USD</div>
          <div class="text-slate-500 text-[11px]">Teacher Revenue Share (70%)</div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">ABA Reference ID:</label>
          <input v-model="reference" type="text" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-mono text-slate-900 font-bold" />
        </div>
      </div>

      <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-between">
        <button @click="$emit('close')" class="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold rounded-xl">
          Cancel
        </button>
        <button @click="handleConfirm" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-md">
          Confirm Payout
        </button>
      </div>
    </div>
  </div>
</template>
