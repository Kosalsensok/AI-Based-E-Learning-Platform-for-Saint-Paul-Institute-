<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  receipts: any[]
}>()

const emit = defineEmits(['open-verification', 'approve-receipt', 'reject-receipt'])

const search = ref('')
const statusFilter = ref('All')
const methodFilter = ref('All')

// Rejection Confirmation Modal state
const rejectConfirmReceipt = ref<any>(null)
const rejectReason = ref('Unclear or unverified receipt image')

const openRejectConfirm = (rcp: any) => {
  rejectConfirmReceipt.value = rcp
}

const confirmRejectAction = () => {
  if (rejectConfirmReceipt.value) {
    emit('reject-receipt', { receipt: rejectConfirmReceipt.value, reason: rejectReason.value })
    rejectConfirmReceipt.value = null
  }
}

// Clean emoji characters from label strings to ensure clean vector icon rendering
const cleanLabel = (text: string) => {
  if (!text) return ''
  return text.replace(/[\u{1F300}-\u{1F9FF}]|[\u{2600}-\u{26FF}]|🟢|🟡|🔴|⚠️|⏳/gu, '').trim()
}

const filteredReceipts = computed(() => {
  return props.receipts.filter(r => {
    const matchesSearch = !search.value || 
      r.student.name.toLowerCase().includes(search.value.toLowerCase()) ||
      r.student.id.toLowerCase().includes(search.value.toLowerCase()) ||
      r.course.toLowerCase().includes(search.value.toLowerCase())
    const matchesStatus = statusFilter.value === 'All' || r.status.toLowerCase() === statusFilter.value.toLowerCase()
    return matchesSearch && matchesStatus
  })
})
</script>

<template>
  <div class="space-y-6 text-slate-100 font-sans">
    
    <!-- Alert Banner (Sleek Dark Indigo) -->
    <div class="bg-indigo-950/90 text-white p-4 rounded-2xl shadow-xl flex items-center justify-between text-xs border border-indigo-800/80">
      <div class="flex items-center gap-2.5 font-bold">
        <span class="p-1.5 bg-indigo-500/20 border border-indigo-500/30 rounded-lg text-indigo-300 inline-flex items-center justify-center">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
        </span>
        <span><strong class="text-indigo-300">{{ receipts.length }} receipts pending verification.</strong> Average review time: 3 minutes.</span>
      </div>
      <span class="bg-indigo-900/90 text-indigo-200 px-3 py-1 rounded-xl font-mono text-[11px] border border-indigo-700/60 flex items-center gap-1.5">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
        <span>Real-Time Queue Active</span>
      </span>
    </div>

    <!-- Filter & Header Control Bar -->
    <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 shadow-xl space-y-4">
      <div>
        <h2 class="text-base font-extrabold text-white flex items-center gap-2">
          <span class="p-1.5 bg-indigo-500/10 border border-indigo-500/20 rounded-lg text-indigo-400 inline-flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </span>
          <span>RECEIPT VERIFICATION QUEUE</span>
        </h2>
        <p class="text-xs text-slate-400 mt-0.5 font-medium">Manual verification for student ABA payment screenshots and cash deposits.</p>
      </div>

      <!-- Unified 1-Line Search & Filter Bar -->
      <div class="flex flex-col sm:flex-row items-center gap-3 pt-2 border-t border-slate-700/60 text-xs">
        <!-- Expanded Search Input with Clear X Button -->
        <div class="relative flex-1 w-full">
          <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
          <input 
            v-model="search" 
            type="text" 
            placeholder="Search Student Name / ID / Course..." 
            class="w-full pl-9 pr-9 py-2.5 bg-slate-900/90 border border-slate-700/80 rounded-xl focus:ring-2 focus:ring-indigo-500 text-white placeholder:text-slate-500 focus:outline-none text-xs transition" 
          />
          <button 
            v-if="search" 
            @click="search = ''" 
            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white font-bold text-xs w-5 h-5 flex items-center justify-center rounded-full bg-slate-800 hover:bg-slate-700 transition cursor-pointer"
            title="Clear Search"
          >
            ✕
          </button>
        </div>

        <select v-model="statusFilter" class="w-full sm:w-44 p-2.5 bg-slate-900/90 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500">
          <option value="All">Status: All</option>
          <option value="pending">Pending Only</option>
          <option value="review">Review Only</option>
        </select>

        <select v-model="methodFilter" class="w-full sm:w-48 p-2.5 bg-slate-900/90 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500">
          <option value="All">Verification Method: All</option>
          <option value="Manual Slip Upload">Manual Slip Upload</option>
          <option value="Cash on Campus">Cash on Campus</option>
        </select>
      </div>
    </div>

    <!-- Queue Table -->
    <div class="bg-slate-800/90 rounded-2xl border border-slate-700/80 shadow-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-200">
          <thead class="bg-slate-900/90 border-b border-slate-700/80 text-slate-400 uppercase font-bold text-[11px] tracking-wider whitespace-nowrap">
            <tr>
              <th class="py-3.5 pl-4 pr-2 text-center w-10">#</th>
              <th class="py-3.5 px-2.5">Student</th>
              <th class="py-3.5 px-2.5">Course</th>
              <th class="py-3.5 px-2.5 text-right">Amount</th>
              <th class="py-3.5 px-2.5">Uploaded Receipt</th>
              <th class="py-3.5 px-2.5">Submitted</th>
              <th class="py-3.5 px-2.5">AI Match</th>
              <th class="py-3.5 px-2.5">Status</th>
              <th class="py-3.5 pr-4 pl-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60 font-mono">
            <tr v-for="rcp in filteredReceipts" :key="rcp.id" class="hover:bg-slate-700/40 transition-colors">
              <td class="py-3 pl-4 pr-2 font-bold text-slate-400 text-center align-middle">#{{ rcp.id }}</td>
              <td class="py-3 px-2.5 font-sans whitespace-nowrap align-middle">
                <div class="font-bold text-white text-xs">{{ rcp.student.name }}</div>
                <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ rcp.student.id }}</div>
              </td>
              <td class="py-3 px-2.5 font-sans font-medium text-slate-300 text-xs whitespace-nowrap align-middle">{{ rcp.course }}</td>
              
              <!-- Strict Align Right for Amount Column -->
              <td class="py-3 px-2.5 text-right font-bold text-emerald-400 font-mono text-xs whitespace-nowrap align-middle">${{ rcp.amount.toFixed(2) }}</td>
              
              <!-- Uploaded Receipt Column with Image Thumbnail & Hover Zoom Card -->
              <td class="py-3 px-2.5 font-sans whitespace-nowrap align-middle">
                <div class="relative group/thumb inline-block">
                  <button 
                    @click="$emit('open-verification', rcp)" 
                    class="flex items-center gap-1.5 p-1 pr-2.5 rounded-xl bg-slate-950/80 border border-slate-700/60 hover:border-indigo-500/80 transition-all cursor-pointer shadow-xs"
                    title="Click to inspect full receipt"
                  >
                    <div class="w-6 h-6 rounded-lg overflow-hidden bg-slate-900 border border-slate-700/80 shrink-0">
                      <img :src="rcp.slip_url" alt="Slip" class="w-full h-full object-cover" />
                    </div>
                    <span class="text-[11px] font-semibold text-indigo-300 group-hover/thumb:text-white flex items-center gap-1">
                      <span>View Slip</span>
                      <svg class="w-3 h-3 text-indigo-400 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </span>
                  </button>

                  <!-- Hover Zoom Quick Preview Card -->
                  <div class="absolute left-0 top-full mt-2 w-56 p-2 bg-slate-950/95 border border-indigo-500/40 rounded-2xl shadow-2xl z-30 opacity-0 group-hover/thumb:opacity-100 transition-all duration-200 pointer-events-none transform translate-y-1 group-hover/thumb:translate-y-0">
                    <div class="text-[10px] font-bold text-slate-300 mb-1 flex items-center justify-between font-mono px-1">
                      <span>Quick Preview</span>
                      <span class="text-indigo-400 font-bold">ABA Slip</span>
                    </div>
                    <div class="rounded-xl overflow-hidden border border-slate-800 bg-slate-900 p-1">
                      <img :src="rcp.slip_url" alt="Preview Slip" class="w-full h-44 object-contain rounded-lg" />
                    </div>
                  </div>
                </div>
              </td>

              <td class="py-3 px-2.5 font-sans text-slate-400 text-[11px] whitespace-nowrap align-middle">{{ rcp.submitted_time }}</td>
              
              <!-- Clean 1-Line AI Match Badge -->
              <td class="py-3 px-2.5 font-sans whitespace-nowrap align-middle">
                <span 
                  class="px-2.5 py-1 rounded-xl text-[11px] font-bold inline-flex items-center gap-1 transition-all shadow-xs"
                  :class="{
                    'bg-emerald-500/10 text-emerald-400 border border-emerald-500/25': rcp.ai_match_level === 'high',
                    'bg-amber-500/10 text-amber-400 border border-amber-500/25': rcp.ai_match_level === 'medium',
                    'bg-rose-500/10 text-rose-400 border border-rose-500/25': rcp.ai_match_level === 'low',
                  }"
                >
                  <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                  <span>{{ cleanLabel(rcp.ai_match_label) }}</span>
                </span>
              </td>

              <!-- Clean 1-Line Status Badge -->
              <td class="py-3 px-2.5 font-sans whitespace-nowrap align-middle">
                <span 
                  class="px-2.5 py-1 rounded-xl text-[11px] font-bold inline-flex items-center gap-1 transition-all shadow-xs"
                  :class="rcp.status === 'Pending' ? 'bg-amber-500/10 text-amber-300 border border-amber-500/25' : 'bg-rose-500/10 text-rose-300 border border-rose-500/25'"
                >
                  <svg v-if="rcp.status === 'Pending'" class="w-3 h-3 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <svg v-else class="w-3 h-3 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <span>{{ cleanLabel(rcp.status) }}</span>
                </span>
              </td>

              <!-- Safe Refined Vector SVG Action Buttons -->
              <td class="py-3 pr-4 pl-2 text-right font-sans align-middle whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                  <button 
                    @click="$emit('open-verification', rcp)" 
                    class="px-2.5 py-1 bg-emerald-500/15 text-emerald-300 hover:bg-emerald-600 hover:text-white border border-emerald-500/30 font-semibold rounded-lg text-[11px] transition-all shadow-xs flex items-center gap-1 cursor-pointer"
                    title="Approve & Unlock Course Access"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Approve</span>
                  </button>

                  <button 
                    @click="openRejectConfirm(rcp)" 
                    class="px-2.5 py-1 bg-rose-500/10 text-rose-300 hover:bg-rose-600 hover:text-white border border-rose-500/20 font-semibold rounded-lg text-[11px] transition-all shadow-xs flex items-center gap-1 cursor-pointer"
                    title="Reject Receipt"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Reject</span>
                  </button>

                  <button 
                    @click="$emit('open-verification', rcp)" 
                    class="p-1.5 bg-slate-900/80 hover:bg-slate-800 text-slate-400 hover:text-white rounded-lg text-[11px] border border-slate-700/60 transition-all cursor-pointer"
                    title="Ask Info / Review Full Modal"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- REJECTION CONFIRMATION MODAL -->
    <div v-if="rejectConfirmReceipt" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-slate-900/95 rounded-2xl shadow-2xl border border-rose-900/60 w-full max-w-md overflow-hidden p-6 space-y-4 text-slate-100 font-sans">
        <div class="flex items-center gap-3 text-rose-400 font-bold text-sm border-b border-slate-800 pb-3">
          <div class="p-2 bg-rose-500/10 border border-rose-500/20 rounded-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div>
            <h3>Confirm Receipt Rejection</h3>
            <p class="text-[11px] text-slate-400 font-normal">Prevent accidental rejection of student payment</p>
          </div>
        </div>

        <p class="text-xs text-slate-300 leading-relaxed">
          Are you sure you want to reject the receipt for student <strong class="text-white font-bold">{{ rejectConfirmReceipt.student?.name }}</strong> (<span class="font-mono text-emerald-400">${{ rejectConfirmReceipt.amount.toFixed(2) }}</span>)?
        </p>

        <div class="space-y-1.5">
          <label class="block text-xs font-semibold text-slate-300">Rejection Reason:</label>
          <input 
            v-model="rejectReason" 
            type="text" 
            class="w-full p-2.5 bg-slate-950 border border-slate-700/80 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500" 
            placeholder="e.g. Unclear screenshot / Invalid ABA reference" 
          />
        </div>

        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-800">
          <button 
            @click="rejectConfirmReceipt = null" 
            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold rounded-xl border border-slate-700/80 transition cursor-pointer"
          >
            Cancel
          </button>
          <button 
            @click="confirmRejectAction" 
            class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl shadow-md transition cursor-pointer flex items-center gap-1.5"
          >
            <span>Confirm Rejection</span>
          </button>
        </div>
      </div>
    </div>

  </div>
</template>
