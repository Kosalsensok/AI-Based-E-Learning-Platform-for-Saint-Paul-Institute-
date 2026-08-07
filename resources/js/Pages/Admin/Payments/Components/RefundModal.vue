<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  refund: any
}>()

const emit = defineEmits(['close', 'approve-confirm', 'reject-confirm', 'ask-docs'])

const refundMethod = ref('ABA PayWay Auto-Refund')
const adminNote = ref('Refund approved due to valid emergency documentation.')
const revokeAccess = ref(true)
const notifyTeacher = ref(true)
const adjustSplit = ref(true)

const handleApprove = () => {
  emit('approve-confirm', { refund: props.refund, note: adminNote.value, method: refundMethod.value })
  emit('close')
}

const handleReject = () => {
  emit('reject-confirm', props.refund)
  emit('close')
}

const handleAskDocs = () => {
  emit('ask-docs', { refund: props.refund, note: adminNote.value })
  emit('close')
}
</script>

<template>
  <div v-if="show && refund" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 transition-all">
    <!-- Soft Glassmorphism Modal Card -->
    <div class="bg-slate-900/95 rounded-3xl shadow-2xl border border-slate-800/90 w-full max-w-2xl overflow-hidden my-6 transform transition-all text-slate-100 font-sans">
      
      <!-- Modal Header (Soft Indigo/Slate Gradient) -->
      <div class="bg-gradient-to-r from-slate-950 via-indigo-950/70 to-slate-950 px-6 py-4.5 text-white flex items-center justify-between border-b border-slate-800">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-indigo-500/10 border border-indigo-500/20 rounded-xl text-indigo-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H4m0 0l4 4m-4-4l4-4" />
            </svg>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="text-[10px] uppercase font-bold text-indigo-400 tracking-wider font-mono">REFUND REQUEST WORKFLOW</span>
              <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-indigo-950 text-indigo-300 border border-indigo-700/50">
                {{ refund.request_id || 'REF-001' }}
              </span>
            </div>
            <h3 class="text-base font-extrabold text-white tracking-wide mt-0.5">
              Process Refund Request
            </h3>
          </div>
        </div>
        
        <button 
          @click="$emit('close')" 
          class="w-8 h-8 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition border border-slate-700/60 cursor-pointer"
          title="Close"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Content Body -->
      <div class="p-6 space-y-4 text-xs text-slate-200 max-h-[75vh] overflow-y-auto">
        
        <!-- Section 1: Request Details Card -->
        <div class="bg-slate-950/60 p-4.5 rounded-2xl border border-slate-800 space-y-3">
          <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
            <h4 class="font-extrabold uppercase text-[11px] tracking-wider flex items-center gap-2 text-slate-300">
              <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span>REQUEST DETAILS</span>
            </h4>
            <span class="text-[10px] text-slate-500 font-mono">ID: {{ refund.request_id }}</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div class="bg-slate-900/70 p-3 rounded-xl border border-slate-800/80">
              <span class="text-slate-400 text-[11px] block mb-0.5">Student</span>
              <strong class="text-white text-xs font-semibold">{{ refund.student?.name }}</strong>
              <span class="text-slate-400 font-mono text-[11px] ml-1">({{ refund.student?.id }})</span>
            </div>

            <div class="bg-slate-900/70 p-3 rounded-xl border border-slate-800/80">
              <span class="text-slate-400 text-[11px] block mb-0.5">Course</span>
              <strong class="text-indigo-300 text-xs font-semibold">{{ refund.course }}</strong>
            </div>

            <div class="bg-slate-900/70 p-3 rounded-xl border border-slate-800/80">
              <span class="text-slate-400 text-[11px] block mb-0.5">Original Transaction</span>
              <span class="font-mono text-slate-200 text-xs font-medium">{{ refund.original_txn || 'TXN-05765 ($40 via ABA)' }}</span>
            </div>

            <div class="bg-slate-900/70 p-3 rounded-xl border border-slate-800/80 flex items-center justify-between">
              <div>
                <span class="text-slate-400 text-[11px] block mb-0.5">Requested Amount</span>
                <strong class="text-purple-300 font-mono text-sm">${{ refund.refund_amount?.toFixed(2) }}</strong>
                <span class="text-purple-200/80 text-[11px] ml-1">({{ refund.refund_percentage }})</span>
              </div>
              <span class="px-2 py-1 bg-purple-500/10 text-purple-300 border border-purple-500/20 text-[10px] font-bold rounded-lg">100% Full</span>
            </div>

            <div class="sm:col-span-2 bg-slate-900/70 p-3 rounded-xl border border-slate-800/80">
              <span class="text-slate-400 text-[11px] block mb-0.5">Reason</span>
              <span class="text-slate-200 font-medium italic">"{{ refund.reason }}"</span>
            </div>
          </div>
        </div>

        <!-- Section 2: Refund Policy Check Card (Soft Emerald Theme) -->
        <div class="bg-emerald-950/30 p-4.5 rounded-2xl border border-emerald-500/20 space-y-2.5">
          <h4 class="font-extrabold text-emerald-300 uppercase text-[11px] tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>REFUND POLICY CHECK</span>
          </h4>

          <div class="space-y-2 font-medium text-slate-200">
            <div class="flex items-center gap-2.5 bg-emerald-950/40 p-2.5 rounded-xl border border-emerald-800/40 text-xs">
              <div class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center shrink-0 text-emerald-400">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <span>Within refund window (&lt; 14 days)</span>
            </div>

            <div class="flex items-center gap-2.5 bg-emerald-950/40 p-2.5 rounded-xl border border-emerald-800/40 text-xs">
              <div class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center shrink-0 text-emerald-400">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <span>Course progress &lt; 30% (Currently: <strong class="text-emerald-300 font-mono">{{ refund.policy_check?.current_progress || 15 }}%</strong>)</span>
            </div>

            <div v-if="refund.policy_check?.discount_applied" class="flex items-center gap-2.5 bg-amber-950/40 p-2.5 rounded-xl border border-amber-800/40 text-amber-300 text-xs">
              <div class="w-5 h-5 rounded-full bg-amber-500/20 border border-amber-500/30 flex items-center justify-center shrink-0 text-amber-400">
                ⚠️
              </div>
              <span>Discount code used → Refund limited to paid amount</span>
            </div>
          </div>
        </div>

        <!-- Section 3: Refund Method Selection (Interactive Soft Radio List) -->
        <div class="space-y-2">
          <h4 class="font-extrabold text-slate-300 uppercase text-[11px] tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>REFUND METHOD</span>
          </h4>

          <div class="space-y-2">
            <label 
              class="flex items-center justify-between p-3 rounded-2xl border transition cursor-pointer"
              :class="refundMethod === 'ABA PayWay Auto-Refund' 
                ? 'bg-indigo-950/50 border-indigo-500/60 ring-1 ring-indigo-500/30 text-white shadow-md' 
                : 'bg-slate-950/50 border-slate-800 text-slate-300 hover:bg-slate-900 hover:border-slate-700'"
            >
              <div class="flex items-center gap-3">
                <input type="radio" value="ABA PayWay Auto-Refund" v-model="refundMethod" class="accent-indigo-500 w-4 h-4 cursor-pointer" />
                <div class="flex items-center gap-2 font-bold text-xs">
                  <span>ABA PayWay Auto-Refund</span>
                </div>
              </div>
              <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase">
                Recommended
              </span>
            </label>

            <label 
              class="flex items-center justify-between p-3 rounded-2xl border transition cursor-pointer"
              :class="refundMethod === 'Manual Bank Transfer' 
                ? 'bg-indigo-950/50 border-indigo-500/60 ring-1 ring-indigo-500/30 text-white shadow-md' 
                : 'bg-slate-950/50 border-slate-800 text-slate-300 hover:bg-slate-900 hover:border-slate-700'"
            >
              <div class="flex items-center gap-3">
                <input type="radio" value="Manual Bank Transfer" v-model="refundMethod" class="accent-indigo-500 w-4 h-4 cursor-pointer" />
                <span class="font-semibold text-xs">Manual Bank Transfer</span>
              </div>
            </label>

            <label 
              class="flex items-center justify-between p-3 rounded-2xl border transition cursor-pointer"
              :class="refundMethod === 'Credit for Future Course' 
                ? 'bg-indigo-950/50 border-indigo-500/60 ring-1 ring-indigo-500/30 text-white shadow-md' 
                : 'bg-slate-950/50 border-slate-800 text-slate-300 hover:bg-slate-900 hover:border-slate-700'"
            >
              <div class="flex items-center gap-3">
                <input type="radio" value="Credit for Future Course" v-model="refundMethod" class="accent-indigo-500 w-4 h-4 cursor-pointer" />
                <span class="font-semibold text-xs">Credit for Future Course</span>
              </div>
            </label>
          </div>
        </div>

        <!-- Section 4: Automatic System Consequences (Soft Purple/Indigo Card) -->
        <div class="bg-indigo-950/30 p-4.5 rounded-2xl border border-indigo-500/20 space-y-2.5 text-xs text-indigo-200">
          <h4 class="font-extrabold uppercase text-[11px] tracking-wider flex items-center gap-2 text-indigo-300">
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>AUTOMATIC SYSTEM CONSEQUENCES</span>
          </h4>

          <div class="space-y-2">
            <label class="flex items-center gap-3 p-2 rounded-xl bg-indigo-950/40 border border-indigo-800/40 cursor-pointer hover:bg-indigo-900/40 transition">
              <input type="checkbox" v-model="revokeAccess" class="accent-indigo-500 w-4 h-4 rounded cursor-pointer" />
              <span class="text-slate-200 font-medium">Revoke course access immediately</span>
            </label>

            <label class="flex items-center gap-3 p-2 rounded-xl bg-indigo-950/40 border border-indigo-800/40 cursor-pointer hover:bg-indigo-900/40 transition">
              <input type="checkbox" v-model="notifyTeacher" class="accent-indigo-500 w-4 h-4 rounded cursor-pointer" />
              <span class="text-slate-200 font-medium">Notify teacher (Dr. Rith)</span>
            </label>

            <label class="flex items-center gap-3 p-2 rounded-xl bg-indigo-950/40 border border-indigo-800/40 cursor-pointer hover:bg-indigo-900/40 transition">
              <input type="checkbox" v-model="adjustSplit" class="accent-indigo-500 w-4 h-4 rounded cursor-pointer" />
              <span class="text-slate-200 font-medium">Adjust revenue split (Teacher: <span class="font-mono text-rose-300">-$12.25</span>, Platform: <span class="font-mono text-rose-300">-$5.25</span>)</span>
            </label>
          </div>
        </div>

        <!-- Section 5: Admin Note Input -->
        <div class="space-y-1.5">
          <label class="block font-bold text-slate-300 text-xs flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span>Admin Note</span>
          </label>
          <textarea 
            v-model="adminNote" 
            rows="2" 
            class="w-full p-3 bg-slate-950/80 border border-slate-800 rounded-2xl text-white placeholder:text-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-xs transition" 
            placeholder="Add administrative notes..."
          ></textarea>
        </div>

      </div>

      <!-- Action Footer -->
      <div class="bg-slate-950/90 px-6 py-4 border-t border-slate-800/80 flex flex-wrap items-center justify-between gap-3">
        <button 
          @click="$emit('close')" 
          class="px-4 py-2 bg-slate-900 border border-slate-800 hover:bg-slate-800 hover:border-slate-700 text-slate-300 font-semibold text-xs rounded-xl transition cursor-pointer"
        >
          Cancel
        </button>

        <div class="flex flex-wrap items-center gap-2">
          <button 
            @click="handleReject" 
            class="px-4 py-2 bg-rose-600/90 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-rose-900/20 border border-rose-500/30 transition flex items-center gap-1.5 cursor-pointer"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span>Reject Request</span>
          </button>

          <button 
            @click="handleAskDocs" 
            class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-800 hover:border-slate-700 font-semibold text-xs rounded-xl transition flex items-center gap-1.5 cursor-pointer"
          >
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <span>Ask for Docs</span>
          </button>

          <button 
            @click="handleApprove" 
            class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-indigo-600/25 transition flex items-center gap-2 cursor-pointer"
          >
            <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
            <span>Approve & Process Refund</span>
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

