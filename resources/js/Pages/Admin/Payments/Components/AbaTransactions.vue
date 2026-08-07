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
  <div class="space-y-6">
    <!-- Filter Bar & Status Chips -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <h2 class="text-lg font-bold text-slate-900">🏦 ABA TRANSACTIONS LOG</h2>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> 🔄 Auto-Refresh: Every 30 seconds
            </span>
          </div>
          <p class="text-xs text-slate-500">Live Transaction Log with ABA PayWay API Sync & Merchant Verification</p>
        </div>

        <button @click="$emit('requery')" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
          <span>🔄</span> Refresh ABA Status
        </button>
      </div>

      <!-- Status Chips Bar -->
      <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100 text-xs">
        <button 
          @click="selectedStatusChip = 'All'"
          class="px-3 py-1.5 rounded-xl font-bold transition"
          :class="selectedStatusChip === 'All' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
        >
          All (278)
        </button>
        <button 
          @click="selectedStatusChip = 'Success'"
          class="px-3 py-1.5 rounded-xl font-bold transition"
          :class="selectedStatusChip === 'Success' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'"
        >
          ✅ Success (267)
        </button>
        <button 
          @click="selectedStatusChip = 'Processing'"
          class="px-3 py-1.5 rounded-xl font-bold transition"
          :class="selectedStatusChip === 'Processing' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'"
        >
          ⏳ Pending (3)
        </button>
        <button 
          @click="selectedStatusChip = 'Failed'"
          class="px-3 py-1.5 rounded-xl font-bold transition"
          :class="selectedStatusChip === 'Failed' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-700 hover:bg-rose-100'"
        >
          ❌ Failed (8)
        </button>
        <button 
          @click="selectedStatusChip = 'Refunded'"
          class="px-3 py-1.5 rounded-xl font-bold transition"
          :class="selectedStatusChip === 'Refunded' ? 'bg-purple-600 text-white shadow-xs' : 'bg-purple-50 text-purple-700 hover:bg-purple-100'"
        >
          💸 Refunded (3)
        </button>
      </div>

      <!-- Search Input -->
      <div class="pt-2">
        <input 
          v-model="search" 
          type="text" 
          placeholder="🔍 Transaction ID / ABA Ref# / Student / Course..." 
          class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none text-xs" 
        />
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-700">
          <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase font-semibold">
            <tr>
              <th class="p-3.5">Transaction ID</th>
              <th class="p-3.5">Student</th>
              <th class="p-3.5">Course</th>
              <th class="p-3.5">Amount</th>
              <th class="p-3.5">ABA Ref#</th>
              <th class="p-3.5">Method</th>
              <th class="p-3.5">Time</th>
              <th class="p-3.5">Status</th>
              <th class="p-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-mono">
            <tr v-for="t in filteredTransactions" :key="t.txn_id" class="hover:bg-slate-50/80 transition">
              <td class="p-3.5 font-bold text-slate-900">{{ t.txn_id }}</td>
              <td class="p-3.5 font-sans font-bold text-slate-900">{{ t.student.name }}</td>
              <td class="p-3.5 font-sans font-medium text-slate-800">{{ t.course }}</td>
              <td class="p-3.5 font-bold text-emerald-600">${{ t.amount.toFixed(2) }}</td>
              <td class="p-3.5 text-indigo-700 font-bold">{{ t.aba_ref }}</td>
              <td class="p-3.5 font-sans font-bold text-slate-700">
                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-md">
                  {{ t.method === 'KHQR' ? '📱 KHQR' : '💳 Card' }}
                </span>
              </td>
              <td class="p-3.5 font-sans text-slate-500">{{ t.time }}</td>
              <td class="p-3.5 font-sans">
                <span 
                  class="px-2.5 py-0.5 rounded-full text-[11px] font-bold capitalize"
                  :class="{
                    'bg-emerald-100 text-emerald-800': t.status === 'Success',
                    'bg-amber-100 text-amber-800': t.status === 'Processing',
                    'bg-rose-100 text-rose-800': t.status === 'Failed',
                    'bg-purple-100 text-purple-800': t.status === 'Refunded',
                  }"
                >
                  {{ t.status === 'Success' ? '✅ Success' : (t.status === 'Processing' ? '⏳ Processing' : (t.status === 'Failed' ? '❌ Failed' : '💸 Refunded')) }}
                </span>
              </td>
              <td class="p-3.5 text-right font-sans">
                <div class="flex items-center justify-end gap-1.5">
                  <button @click="$emit('view-transaction', t)" class="px-2.5 py-1 bg-slate-100 hover:bg-indigo-50 text-indigo-700 font-medium rounded-lg transition text-xs">
                    👁 View
                  </button>
                  <button @click="$emit('requery')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs">
                    🧾
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
