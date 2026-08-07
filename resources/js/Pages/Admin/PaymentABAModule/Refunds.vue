<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  refunds: any[]
}>()

const emit = defineEmits(['open-refund-review'])

const search = ref('')
const statusFilter = ref('All')

const filteredRefunds = computed(() => {
  return props.refunds.filter(r => {
    const matchesSearch = !search.value || 
      r.request_id.toLowerCase().includes(search.value.toLowerCase()) ||
      r.student.name.toLowerCase().includes(search.value.toLowerCase()) ||
      r.course.toLowerCase().includes(search.value.toLowerCase())
    const matchesStatus = statusFilter.value === 'All' || r.status.toLowerCase() === statusFilter.value.toLowerCase()
    return matchesSearch && matchesStatus
  })
})

// Dynamic stats calculations
const totalRefundsCount = computed(() => props.refunds?.length || 0)
const pendingCount = computed(() => props.refunds?.filter(r => r.status?.toLowerCase() === 'pending').length || 0)
const approvedCount = computed(() => props.refunds?.filter(r => r.status?.toLowerCase() === 'refunded').length || 0)
const totalRefundedSum = computed(() => {
  return (props.refunds || [])
    .filter(r => r.status?.toLowerCase() === 'refunded')
    .reduce((sum, r) => sum + (r.refund_amount || 0), 0)
})
</script>

<template>
  <div class="space-y-6 text-slate-100 font-sans">
    
    <!-- Unified Header Control & Summary Bar (UX Item #5: Single Integrated Layout) -->
    <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 shadow-xl space-y-4">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2.5">
            <span class="p-1.5 bg-indigo-500/10 border border-indigo-500/20 rounded-lg text-indigo-400 inline-flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </span>
            <h2 class="text-base font-extrabold text-white tracking-wide">💸 REFUND REQUEST WORKFLOW</h2>
          </div>
          <p class="text-xs text-slate-400 mt-1 font-medium">គ្រប់គ្រងសំណើសងប្រាក់វិញ ដោយមានដំណើរការអនុម័តច្បាស់លាស់ និងភ្ជាប់ ABA Refund API</p>
        </div>

        <!-- Integrated Summary Badges -->
        <div class="flex flex-wrap items-center gap-2 text-xs">
          <div class="bg-slate-900/90 px-3 py-1.5 rounded-xl border border-slate-700/70 flex items-center gap-2">
            <span class="text-slate-400 font-bold">Total:</span>
            <strong class="text-white font-mono text-sm">{{ totalRefundsCount }}</strong>
          </div>
          <div class="bg-slate-900/90 px-3 py-1.5 rounded-xl border border-amber-900/50 flex items-center gap-2">
            <span class="text-amber-400/90 font-bold">Pending:</span>
            <strong class="text-amber-300 font-mono text-sm">{{ pendingCount }}</strong>
          </div>
          <div class="bg-slate-900/90 px-3 py-1.5 rounded-xl border border-purple-900/50 flex items-center gap-2">
            <span class="text-purple-400/90 font-bold">Approved:</span>
            <strong class="text-purple-300 font-mono text-sm">{{ approvedCount }}</strong>
          </div>
          <div class="bg-slate-900/90 px-3 py-1.5 rounded-xl border border-emerald-900/50 flex items-center gap-2">
            <span class="text-emerald-400/90 font-bold">Total Refunded:</span>
            <strong class="text-emerald-300 font-mono text-sm">${{ totalRefundedSum.toFixed(2) }}</strong>
          </div>
        </div>
      </div>

      <!-- Unified Filters Row -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-slate-700/60 text-xs">
        <div class="sm:col-span-2 relative">
          <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">🔍</span>
          <input 
            v-model="search" 
            type="text" 
            placeholder="Search Request # / Student / Course..." 
            class="w-full pl-9 pr-8 py-2 bg-slate-900/90 border border-slate-700/80 rounded-xl focus:ring-2 focus:ring-indigo-500 text-white placeholder:text-slate-500 focus:outline-none transition" 
          />
          <button 
            v-if="search" 
            @click="search = ''" 
            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white font-bold text-xs"
          >
            ✕
          </button>
        </div>
        <select v-model="statusFilter" class="p-2 bg-slate-900/90 border border-slate-700/80 rounded-xl text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
          <option value="All">Status: All</option>
          <option value="pending">Pending</option>
          <option value="refunded">Refunded</option>
        </select>
      </div>
    </div>

    <!-- Refund Requests Data Table -->
    <div class="bg-slate-800/90 rounded-2xl border border-slate-700/80 shadow-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-200">
          <thead class="bg-slate-900/90 border-b border-slate-700 text-slate-400 uppercase font-semibold">
            <tr>
              <th class="p-3.5">Request #</th>
              <th class="p-3.5">Student</th>
              <th class="p-3.5">Course</th>
              <!-- UX Item #2: Financial UX Right Alignment -->
              <th class="p-3.5 text-right">Original</th>
              <th class="p-3.5 text-right">Refund Amt</th>
              <th class="p-3.5">Reason</th>
              <th class="p-3.5 text-right">Requested</th>
              <th class="p-3.5 text-center">Status</th>
              <th class="p-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60">
            <tr v-for="ref in filteredRefunds" :key="ref.request_id" class="hover:bg-slate-700/40 transition">
              <!-- UX Item #3: High Contrast Colors for ID and Amounts -->
              <td class="p-3.5">
                <span class="px-2 py-1 bg-indigo-950/80 border border-indigo-700/70 text-indigo-300 font-extrabold font-mono rounded-lg shadow-xs">
                  {{ ref.request_id }}
                </span>
              </td>
              <td class="p-3.5 font-sans font-bold text-white">{{ ref.student.name }}</td>
              <td class="p-3.5 font-sans font-medium text-slate-200">{{ ref.course }}</td>
              <!-- UX Item #2: Right Align Financial Columns -->
              <td class="p-3.5 text-right font-mono font-medium text-slate-300">${{ ref.original_amount.toFixed(2) }}</td>
              <td class="p-3.5 text-right font-mono font-bold text-purple-300">
                ${{ ref.refund_amount.toFixed(2) }} <span class="text-xs text-purple-200/90 font-normal font-sans">({{ ref.refund_percentage }})</span>
              </td>
              <td class="p-3.5 font-sans text-slate-300 max-w-xs truncate">{{ ref.reason }}</td>
              <td class="p-3.5 text-right font-sans text-slate-300">{{ ref.requested_date }}</td>
              <td class="p-3.5 text-center font-sans">
                <span 
                  class="px-2.5 py-1 rounded-full text-[11px] font-bold capitalize inline-flex items-center gap-1"
                  :class="ref.status === 'Refunded' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800/60' : 'bg-amber-950 text-amber-400 border border-amber-800/60'"
                >
                  {{ ref.status === 'Refunded' ? '✅ Refunded' : '⏳ Pending' }}
                </span>
              </td>
              <!-- UX Item #1: Actions button per status -->
              <td class="p-3.5 text-right font-sans">
                <button 
                  @click="$emit('open-refund-review', ref)" 
                  class="px-3.5 py-1.5 font-bold rounded-xl shadow-xs transition text-xs cursor-pointer inline-flex items-center gap-1.5"
                  :class="ref.status === 'Refunded' 
                    ? 'bg-slate-900 hover:bg-slate-700 text-slate-200 border border-slate-700 hover:border-slate-500' 
                    : 'bg-purple-600 hover:bg-purple-500 text-white shadow-purple-900/40'"
                >
                  <template v-if="ref.status === 'Refunded'">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>View Details</span>
                  </template>
                  <template v-else>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Review Request</span>
                  </template>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

