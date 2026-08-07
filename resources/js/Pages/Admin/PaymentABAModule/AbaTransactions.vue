<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  transactions: any[]
  stats: any
}>()

const emit = defineEmits(['view-transaction', 'requery'])

const search = ref('')
const selectedStatusChip = ref('All')

const filteredTransactions = computed(() => {
  return props.transactions.filter(t => {
    const matchesSearch = !search.value || 
      t.txn_id.toLowerCase().includes(search.value.toLowerCase()) ||
      (t.aba_ref && t.aba_ref.toLowerCase().includes(search.value.toLowerCase())) ||
      t.student.name.toLowerCase().includes(search.value.toLowerCase()) ||
      t.course.toLowerCase().includes(search.value.toLowerCase())
    const matchesStatus = selectedStatusChip.value === 'All' || t.status.toLowerCase() === selectedStatusChip.value.toLowerCase()
    return matchesSearch && matchesStatus
  })
})
</script>

<template>
  <div class="space-y-6 text-slate-100 font-sans">
    <!-- Filter Bar & Status Chips -->
    <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 shadow-xl space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2.5">
            <span class="p-1.5 bg-indigo-500/10 border border-indigo-500/20 rounded-lg text-indigo-400 inline-flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
              </svg>
            </span>
            <h2 class="text-base font-bold text-white tracking-wide">ABA TRANSACTIONS LOG</h2>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-950/80 text-emerald-400 border border-emerald-800/60">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Auto-Refresh: 30s
            </span>
          </div>
          <p class="text-xs text-slate-400 mt-0.5">Live Transaction Log with ABA PayWay API Sync & Merchant Verification</p>
        </div>

        <button @click="$emit('requery')" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span>Refresh ABA Status</span>
        </button>
      </div>

      <!-- Status Chips Bar (Active Solid vs Muted Ghost States with Vector SVG Icons) -->
      <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-700/60 text-xs">
        <button 
          @click="selectedStatusChip = 'All'"
          class="px-3 py-1.5 rounded-xl font-bold transition-all cursor-pointer text-xs flex items-center gap-1.5"
          :class="selectedStatusChip === 'All' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-900/60 text-slate-400 hover:text-slate-200 border border-slate-800 hover:bg-slate-800/80'"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
          </svg>
          <span>All (278)</span>
        </button>

        <button 
          @click="selectedStatusChip = 'Success'"
          class="px-3 py-1.5 rounded-xl font-bold transition-all cursor-pointer text-xs flex items-center gap-1.5"
          :class="selectedStatusChip === 'Success' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-900/60 text-slate-400 hover:text-emerald-400 border border-slate-800 hover:bg-slate-800/80'"
        >
          <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Success (267)</span>
        </button>

        <button 
          @click="selectedStatusChip = 'Processing'"
          class="px-3 py-1.5 rounded-xl font-bold transition-all cursor-pointer text-xs flex items-center gap-1.5"
          :class="selectedStatusChip === 'Processing' ? 'bg-amber-600 text-white shadow-md' : 'bg-slate-900/60 text-slate-400 hover:text-amber-400 border border-slate-800 hover:bg-slate-800/80'"
        >
          <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Pending (3)</span>
        </button>

        <button 
          @click="selectedStatusChip = 'Failed'"
          class="px-3 py-1.5 rounded-xl font-bold transition-all cursor-pointer text-xs flex items-center gap-1.5"
          :class="selectedStatusChip === 'Failed' ? 'bg-rose-600 text-white shadow-md' : 'bg-slate-900/60 text-slate-400 hover:text-rose-400 border border-slate-800 hover:bg-slate-800/80'"
        >
          <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Failed (8)</span>
        </button>

        <button 
          @click="selectedStatusChip = 'Refunded'"
          class="px-3 py-1.5 rounded-xl font-bold transition-all cursor-pointer text-xs flex items-center gap-1.5"
          :class="selectedStatusChip === 'Refunded' ? 'bg-purple-600 text-white shadow-md' : 'bg-slate-900/60 text-slate-400 hover:text-purple-400 border border-slate-800 hover:bg-slate-800/80'"
        >
          <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H4m0 0l4 4m-4-4l4-4" />
          </svg>
          <span>Refunded (3)</span>
        </button>
      </div>

      <!-- Search Input with Vector Search Icon & Clear "X" Icon -->
      <div class="pt-2">
        <div class="relative">
          <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
          <input 
            v-model="search" 
            type="text" 
            placeholder="Search Transaction ID / ABA Ref# / Student / Course..." 
            class="w-full pl-9 pr-9 py-2.5 bg-slate-900/90 border border-slate-700/80 rounded-xl focus:ring-2 focus:ring-indigo-500 text-white placeholder:text-slate-500 focus:outline-none text-xs font-sans transition" 
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
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-slate-800/90 rounded-2xl border border-slate-700/80 shadow-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-200">
          <thead class="bg-slate-900/90 border-b border-slate-700 text-slate-400 uppercase font-semibold text-[11px] tracking-wider">
            <tr>
              <th class="p-3.5">Transaction ID</th>
              <th class="p-3.5">Student</th>
              <th class="p-3.5">Course</th>
              <th class="p-3.5 text-right pr-6">Amount</th>
              <th class="p-3.5">ABA Ref#</th>
              <th class="p-3.5">Method</th>
              <th class="p-3.5">Time</th>
              <th class="p-3.5">Status</th>
              <th class="p-3.5 text-right pr-4">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60 font-mono">
            <tr v-for="t in filteredTransactions" :key="t.txn_id" class="hover:bg-slate-700/40 transition">
              <td class="p-3.5 font-bold text-white">{{ t.txn_id }}</td>
              <td class="p-3.5 font-sans font-bold text-white">{{ t.student.name }}</td>
              <td class="p-3.5 font-sans font-medium text-slate-300">{{ t.course }}</td>
              
              <!-- Strict Align Right for Amount Column -->
              <td class="p-3.5 text-right font-bold text-emerald-400 pr-6">${{ t.amount.toFixed(2) }}</td>
              
              <td class="p-3.5 text-indigo-400 font-bold">{{ t.aba_ref }}</td>
              <td class="p-3.5 font-sans font-semibold">
                <span v-if="t.method === 'KHQR'" class="px-2.5 py-1 bg-cyan-950/80 text-cyan-300 rounded-lg border border-cyan-800/60 text-[11px] font-bold inline-flex items-center gap-1.5 shadow-xs">
                  <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                  </svg>
                  <span>KHQR</span>
                </span>
                <span v-else class="px-2.5 py-1 bg-indigo-950/80 text-indigo-300 rounded-lg border border-indigo-800/60 text-[11px] font-bold inline-flex items-center gap-1.5 shadow-xs">
                  <svg class="w-3.5 h-3.5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                  <span>Card</span>
                </span>
              </td>
              <td class="p-3.5 font-sans text-slate-400 text-[11px]">{{ t.time }}</td>
              <td class="p-3.5 font-sans">
                <span 
                  class="px-2.5 py-0.5 rounded-full text-[11px] font-bold capitalize inline-flex items-center gap-1.5"
                  :class="{
                    'bg-emerald-950 text-emerald-400 border border-emerald-800/60': t.status === 'Success',
                    'bg-amber-950 text-amber-400 border border-amber-800/60': t.status === 'Processing',
                    'bg-rose-950 text-rose-400 border border-rose-800/60': t.status === 'Failed',
                    'bg-purple-950 text-purple-400 border border-purple-800/60': t.status === 'Refunded',
                  }"
                >
                  <svg v-if="t.status === 'Success'" class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <svg v-else-if="t.status === 'Processing'" class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <svg v-else-if="t.status === 'Failed'" class="w-3 h-3 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  <svg v-else class="w-3 h-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H4m0 0l4 4m-4-4l4-4" />
                  </svg>
                  <span>{{ t.status }}</span>
                </span>
              </td>
              
              <!-- Flaticon Quality Vector SVG Action Buttons -->
              <td class="p-3.5 text-right font-sans pr-4">
                <div class="flex items-center justify-end gap-2">
                  <button 
                    @click="$emit('view-transaction', t)" 
                    class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition text-xs shadow-xs flex items-center gap-1.5 cursor-pointer"
                    title="View Transaction Details"
                  >
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>View Details</span>
                  </button>
                  
                  <button 
                    @click="$emit('requery')" 
                    class="p-2 bg-slate-900 hover:bg-slate-800 text-indigo-400 hover:text-indigo-300 rounded-xl text-xs border border-slate-700/80 transition cursor-pointer flex items-center justify-center shadow-xs"
                    title="Requery / Sync ABA Receipt"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
