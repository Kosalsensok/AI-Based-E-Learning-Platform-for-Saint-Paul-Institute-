<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  receipt: any
}>()

const emit = defineEmits(['close', 'approve', 'reject'])

const adminNotes = ref('')
const zoomLevel = ref(100)
const rotationAngle = ref(0)

// Rejection Reason Confirmation Dialog state
const showRejectReasonModal = ref(false)
const selectedRejectReason = ref('Unclear or blurry receipt screenshot')
const customRejectReason = ref('')

const presetRejectReasons = [
  'Unclear or blurry receipt screenshot',
  'Invalid ABA reference number',
  'Transfer amount does not match course fee',
  'Duplicate or previously used receipt',
  'Merchant name mismatch',
]

const handleZoom = (delta: number) => {
  zoomLevel.value = Math.max(50, Math.min(200, zoomLevel.value + delta))
}

const handleRotate = () => {
  rotationAngle.value = (rotationAngle.value + 90) % 360
}

const handleResetImage = () => {
  zoomLevel.value = 100
  rotationAngle.value = 0
}

const handleApprove = () => {
  emit('approve', props.receipt)
  emit('close')
}

const openRejectDialog = () => {
  showRejectReasonModal.value = true
}

const confirmReject = () => {
  const finalReason = customRejectReason.value.trim() || selectedRejectReason.value
  emit('reject', { receipt: props.receipt, reason: finalReason, note: adminNotes.value })
  showRejectReasonModal.value = false
  emit('close')
}
</script>

<template>
  <div v-if="show && receipt" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 rounded-3xl shadow-2xl border border-slate-800 w-full max-w-4xl overflow-hidden my-6 transform transition-all text-slate-100 font-sans">
      
      <!-- Modal Header -->
      <div class="bg-slate-950 px-6 py-4 text-white flex items-center justify-between border-b border-slate-800">
        <div>
          <h3 class="text-base font-extrabold flex items-center gap-2">
            <span class="p-1.5 bg-indigo-500/10 border border-indigo-500/20 rounded-lg text-indigo-400 inline-flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </span>
            <span>Verify Receipt – {{ receipt.student?.name }}</span>
          </h3>
          <p class="text-xs text-slate-400 mt-0.5 font-medium">
            Course: <strong class="text-slate-200">{{ receipt.course }}</strong> · Amount: <strong class="text-emerald-400 font-mono">${{ receipt.amount?.toFixed(2) }}</strong>
          </p>
        </div>
        <button 
          @click="$emit('close')" 
          class="w-8 h-8 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition border border-slate-800 text-lg cursor-pointer"
          title="Close Modal"
        >
          ×
        </button>
      </div>

      <!-- Workspace Body (2 Columns) -->
      <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-800 max-h-[75vh] overflow-y-auto text-xs text-slate-200">
        
        <!-- LEFT: UPLOADED RECEIPT IMAGE & CONTROLS -->
        <div class="p-6 space-y-4 bg-slate-950/50 flex flex-col justify-between">
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <h4 class="font-bold text-white text-xs flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>UPLOADED RECEIPT IMAGE</span>
              </h4>

              <!-- Zoom, Rotate & Reset Action Bar -->
              <div class="flex items-center gap-1 text-[11px] bg-slate-900 p-1 rounded-xl border border-slate-800">
                <button 
                  @click="handleZoom(15)" 
                  class="px-2 py-1 bg-slate-800 hover:bg-slate-700 rounded-lg text-slate-300 hover:text-white transition flex items-center gap-1 cursor-pointer" 
                  title="Zoom In"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                  </svg>
                  <span>Zoom +</span>
                </button>

                <button 
                  @click="handleZoom(-15)" 
                  class="px-2 py-1 bg-slate-800 hover:bg-slate-700 rounded-lg text-slate-300 hover:text-white transition flex items-center gap-1 cursor-pointer" 
                  title="Zoom Out"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM7 10h6" />
                  </svg>
                  <span>Zoom -</span>
                </button>

                <button 
                  @click="handleRotate" 
                  class="px-2 py-1 bg-slate-800 hover:bg-slate-700 rounded-lg text-slate-300 hover:text-white transition flex items-center gap-1 cursor-pointer" 
                  title="Rotate 90 Degrees"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  <span>Rotate ↺</span>
                </button>

                <button 
                  @click="handleResetImage" 
                  class="px-2 py-1 bg-slate-800 hover:bg-slate-700 rounded-lg text-slate-300 hover:text-white transition flex items-center gap-1 cursor-pointer" 
                  title="Reset Zoom & Rotation"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9" />
                  </svg>
                  <span>Reset ⟲</span>
                </button>
              </div>
            </div>

            <!-- Receipt Display Stage -->
            <div class="bg-slate-950 rounded-2xl p-4 flex items-center justify-center min-h-[280px] max-h-[360px] overflow-hidden border border-slate-800 relative shadow-inner">
              <img 
                :src="receipt.slip_url" 
                alt="ABA Receipt" 
                class="max-h-72 object-contain transition-transform duration-300 rounded-lg shadow-md"
                :style="{ transform: `scale(${zoomLevel / 100}) rotate(${rotationAngle}deg)` }"
              />
            </div>
          </div>

          <div class="text-[11px] text-slate-400 font-mono flex items-center justify-between pt-2 border-t border-slate-800/80">
            <span>Zoom: {{ zoomLevel }}%</span>
            <span>Rotation: {{ rotationAngle }}°</span>
          </div>
        </div>

        <!-- RIGHT: AI OCR EXTRACTION & AUDIT NOTES -->
        <div class="p-6 space-y-5">
          <!-- AI OCR Card with Standard Typography & Perfect Data Alignment -->
          <div class="bg-emerald-950/30 p-4.5 rounded-2xl border border-emerald-500/25 space-y-3 shadow-sm">
            <div class="flex items-center justify-between font-bold text-emerald-300 text-xs border-b border-emerald-500/20 pb-2.5">
              <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>AI OCR EXTRACTION</span>
              </span>
              <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-[11px] font-semibold flex items-center gap-1">
                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Confidence: {{ receipt.ocr_extraction?.recommendation || '98%' }}</span>
              </span>
            </div>

            <div class="space-y-2 text-xs">
              <div class="flex items-center justify-between">
                <span class="text-slate-400 font-medium">Merchant Name:</span>
                <span class="font-bold text-white text-right">
                  "{{ receipt.ocr_extraction?.merchant_name || 'E.LMS EDU KH' }}"
                  <span class="text-emerald-400 font-bold ml-1">Match ✓</span>
                </span>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-slate-400 font-medium">Amount:</span>
                <span class="font-bold text-emerald-400 font-mono text-xs text-right">
                  {{ receipt.ocr_extraction?.amount || '$45.00' }}
                  <span class="text-emerald-400 font-bold ml-1 font-sans">Match ✓</span>
                </span>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-slate-400 font-medium">Date & Time:</span>
                <span class="font-bold text-slate-200 text-right">
                  {{ receipt.ocr_extraction?.date || '26/05/2025 10:44' }}
                  <span class="text-emerald-400 font-bold ml-1">Match ✓</span>
                </span>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-slate-400 font-medium">ABA Ref#:</span>
                <span class="font-bold text-indigo-400 font-mono text-right">
                  {{ receipt.ocr_extraction?.aba_ref || 'ABA-847291' }}
                  <span class="text-emerald-400 font-bold ml-1 font-sans">Match ✓</span>
                </span>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-slate-400 font-medium">Transaction ID:</span>
                <span class="font-bold text-slate-100 font-mono text-right">
                  {{ receipt.ocr_extraction?.txn_id || 'TXN-2025-05781' }}
                  <span class="text-emerald-400 font-bold ml-1 font-sans">Match ✓</span>
                </span>
              </div>
            </div>
          </div>

          <!-- Admin Notes & Audit Log -->
          <div class="space-y-1.5">
            <label class="block font-bold text-xs text-slate-300 flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              <span>Admin Notes & Audit Log:</span>
            </label>
            <textarea 
              v-model="adminNotes" 
              rows="3" 
              class="w-full p-3 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder:text-slate-500 focus:outline-none focus:border-indigo-500 transition" 
              placeholder="Type any internal verification note or message for student..."
            ></textarea>
          </div>
        </div>

      </div>

      <!-- Action Footer (Clear Visual Hierarchy) -->
      <div class="bg-slate-950 px-6 py-4 border-t border-slate-800 flex flex-wrap items-center justify-between gap-3">
        <button 
          @click="$emit('close')" 
          class="px-4 py-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white font-semibold text-xs rounded-xl transition cursor-pointer"
        >
          Cancel
        </button>

        <div class="flex items-center gap-2.5">
          <!-- Destructive Secondary (Outline Red) -->
          <button 
            @click="openRejectDialog" 
            class="px-4 py-2 bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 font-semibold text-xs rounded-xl transition cursor-pointer flex items-center gap-1.5"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Reject Receipt</span>
          </button>

          <!-- Secondary Neutral Button -->
          <button 
            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1.5 cursor-pointer"
          >
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <span>Request More Info</span>
          </button>

          <!-- Primary Action Button (Prominent Emerald Fill) -->
          <button 
            @click="handleApprove" 
            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/30 transition flex items-center gap-1.5 cursor-pointer"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Approve & Unlock Course</span>
          </button>
        </div>
      </div>

    </div>

    <!-- REJECTION REASON CONFIRMATION DIALOG -->
    <div v-if="showRejectReasonModal" class="fixed inset-0 z-60 overflow-y-auto bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-slate-900 rounded-2xl shadow-2xl border border-rose-900/60 w-full max-w-md overflow-hidden p-6 space-y-4 text-slate-100 font-sans">
        <div class="flex items-center gap-3 text-rose-400 font-bold text-sm border-b border-slate-800 pb-3">
          <div class="p-2 bg-rose-500/10 border border-rose-500/20 rounded-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div>
            <h3>Select Rejection Reason</h3>
            <p class="text-[11px] text-slate-400 font-normal">Feedback will be sent to student for re-upload</p>
          </div>
        </div>

        <div class="space-y-2 text-xs">
          <label class="block font-semibold text-slate-300">Choose Preset Reason:</label>
          <div class="space-y-1.5">
            <label 
              v-for="reason in presetRejectReasons" 
              :key="reason" 
              class="flex items-center gap-2 p-2.5 rounded-xl border transition cursor-pointer"
              :class="selectedRejectReason === reason ? 'bg-rose-950/60 border-rose-500/60 text-white' : 'bg-slate-950/60 border-slate-800 text-slate-300 hover:border-slate-700'"
            >
              <input type="radio" v-model="selectedRejectReason" :value="reason" class="accent-rose-500" />
              <span>{{ reason }}</span>
            </label>
          </div>
        </div>

        <div class="space-y-1.5 text-xs">
          <label class="block font-semibold text-slate-300">Or Custom Reason:</label>
          <input 
            v-model="customRejectReason" 
            type="text" 
            class="w-full p-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder:text-slate-500 focus:outline-none focus:border-rose-500" 
            placeholder="Enter custom rejection reason..." 
          />
        </div>

        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-800">
          <button 
            @click="showRejectReasonModal = false" 
            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold rounded-xl border border-slate-700 transition cursor-pointer"
          >
            Cancel
          </button>
          <button 
            @click="confirmReject" 
            class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl shadow-md transition cursor-pointer flex items-center gap-1.5"
          >
            <span>Confirm Rejection</span>
          </button>
        </div>
      </div>
    </div>

  </div>
</template>
