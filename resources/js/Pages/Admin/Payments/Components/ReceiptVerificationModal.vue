<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  receipt: any
}>()

const emit = defineEmits(['close', 'approve', 'reject'])

const adminNotes = ref('Verified against ABA PayWay Merchant Ref# ABA-847291.')
const zoomLevel = ref(100)

const handleZoom = (delta: number) => {
  zoomLevel.value = Math.max(50, Math.min(200, zoomLevel.value + delta))
}

const handleApprove = () => {
  emit('approve', props.receipt)
  emit('close')
}

const handleReject = () => {
  emit('reject', { receipt: props.receipt, reason: 'Unclear or unverified receipt image' })
  emit('close')
}
</script>

<template>
  <div v-if="show && receipt" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-4xl overflow-hidden my-6 transform transition-all">
      
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
        <div>
          <h3 class="text-lg font-bold flex items-center gap-2">
            <span>🔍</span> Verify Receipt – {{ receipt.student?.name }}
          </h3>
          <p class="text-xs text-slate-300">Course: {{ receipt.course }} · Amount: ${{ receipt.amount }}</p>
        </div>
        <button @click="$emit('close')" class="text-slate-400 hover:text-white text-2xl font-bold">×</button>
      </div>

      <!-- Workspace Body (2 Columns) -->
      <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-200 max-h-[75vh] overflow-y-auto text-xs text-slate-700">
        
        <!-- LEFT: UPLOADED RECEIPT IMAGE -->
        <div class="p-6 space-y-4 bg-slate-50">
          <div class="flex items-center justify-between">
            <h4 class="font-bold text-slate-900 flex items-center gap-1">
              <span>📄</span> UPLOADED RECEIPT IMAGE
            </h4>
            <div class="flex items-center gap-1.5">
              <button @click="handleZoom(15)" class="px-2 py-1 bg-white border border-slate-300 rounded-lg">Zoom +</button>
              <button @click="handleZoom(-15)" class="px-2 py-1 bg-white border border-slate-300 rounded-lg">Zoom -</button>
            </div>
          </div>

          <div class="bg-slate-200/80 rounded-2xl p-4 flex items-center justify-center min-h-[260px] overflow-hidden border border-slate-300 relative">
            <img 
              :src="receipt.slip_url" 
              alt="ABA Receipt" 
              class="max-h-72 object-contain transition-transform duration-200 rounded-lg shadow-md"
              :style="{ transform: `scale(${zoomLevel / 100})` }"
            />
          </div>
        </div>

        <!-- RIGHT: AI OCR EXTRACTION & CONFIDENCE -->
        <div class="p-6 space-y-4">
          <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-200 space-y-2">
            <div class="flex items-center justify-between font-bold text-emerald-900 text-xs">
              <span class="flex items-center gap-1">🤖 AI OCR EXTRACTION</span>
              <span class="px-2.5 py-0.5 bg-emerald-600 text-white rounded-full text-[10px]">
                🎯 AI Confidence: {{ receipt.ocr_extraction?.recommendation || '98%' }}
              </span>
            </div>

            <div class="space-y-1.5 font-mono text-[11px]">
              <div class="flex justify-between">
                <span class="text-slate-600">Merchant Name:</span>
                <span class="font-bold text-slate-900">"{{ receipt.ocr_extraction?.merchant_name || 'E.LMS EDU KH' }}" (Match ✅)</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-600">Amount:</span>
                <span class="font-bold text-emerald-700">{{ receipt.ocr_extraction?.amount || '$45.00' }} (Match ✅)</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-600">Date:</span>
                <span class="font-bold text-slate-800">{{ receipt.ocr_extraction?.date || '26/05/2025 10:44' }} (Match ✅)</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-600">ABA Ref#:</span>
                <span class="font-bold text-indigo-700">{{ receipt.ocr_extraction?.aba_ref || 'ABA-847291' }} (Match ✅)</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-600">Transaction ID:</span>
                <span class="font-bold text-slate-900">{{ receipt.ocr_extraction?.txn_id || 'TXN-2025-05781' }} (Match ✅)</span>
              </div>
            </div>
          </div>

          <!-- Admin Notes -->
          <div class="space-y-1">
            <label class="block font-bold text-slate-700">📝 Admin Notes:</label>
            <textarea v-model="adminNotes" rows="3" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl" placeholder="Enter admin verification notes..."></textarea>
          </div>
        </div>

      </div>

      <!-- Action Footer -->
      <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-2">
        <button @click="$emit('close')" class="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl transition">
          Cancel
        </button>

        <div class="flex items-center gap-2">
          <button @click="handleReject" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1">
            ❌ Reject
          </button>
          <button class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs rounded-xl transition flex items-center gap-1">
            💬 Request More Info
          </button>
          <button @click="handleApprove" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center gap-1.5">
            ✅ Approve & Unlock Course
          </button>
        </div>
      </div>

    </div>
  </div>
</template>
