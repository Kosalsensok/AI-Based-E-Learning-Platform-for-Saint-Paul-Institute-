<script setup lang="ts">
const props = defineProps<{
  show: boolean
  invoice: any
}>()

const emit = defineEmits(['close'])

const handlePrint = () => {
  window.print()
}
</script>

<template>
  <div v-if="show && invoice" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-xl overflow-hidden my-6 transform transition-all">
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 text-white flex items-center justify-between">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xl font-black text-indigo-400">E.LMS EDU KH</span>
            <span class="text-xs bg-indigo-800 px-2 py-0.5 rounded-md font-mono">OFFICIAL RECEIPT</span>
          </div>
          <p class="text-xs text-slate-300 mt-1">Payment Confirmation & Access Voucher</p>
        </div>
        <div class="text-right">
          <div class="text-xs text-slate-400 font-bold uppercase">Receipt #</div>
          <div class="text-lg font-mono font-black text-amber-300">{{ invoice.txn_id || invoice.receipt_no || 'TXN-05781' }}</div>
        </div>
      </div>

      <div class="p-6 space-y-4 text-xs text-slate-700">
        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between font-bold text-emerald-900">
          <span>Status:</span>
          <span>✅ {{ invoice.status || 'Success' }}</span>
        </div>

        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2">
          <div class="flex justify-between">
            <span class="text-slate-500">Student Name:</span>
            <strong class="text-slate-900">{{ invoice.student?.name }}</strong>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500">Course Enrolled:</span>
            <strong class="text-indigo-900">{{ invoice.course }}</strong>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500">Teacher:</span>
            <span class="text-slate-800">{{ invoice.teacher }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500">Payment Method:</span>
            <span class="font-bold text-indigo-700">{{ invoice.method || '💳 ABA' }}</span>
          </div>
          <div class="flex justify-between text-sm font-black text-slate-900 pt-2 border-t border-slate-200">
            <span>Total Paid:</span>
            <span class="text-emerald-600">${{ (invoice.amount || 45).toFixed(2) }} USD</span>
          </div>
        </div>
      </div>

      <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-between">
        <button @click="$emit('close')" class="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold rounded-xl">
          Close
        </button>
        <button @click="handlePrint" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md flex items-center gap-1">
          <span>🖨️</span> Download PDF / Print
        </button>
      </div>
    </div>
  </div>
</template>
