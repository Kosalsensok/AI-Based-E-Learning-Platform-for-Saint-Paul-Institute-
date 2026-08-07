<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  history: any[]
  majors: any[]
  teachers: any[]
}>()

const emit = defineEmits(['view-invoice'])

const search = ref('')
const selectedQuickChip = ref('This Month')
const statusFilter = ref('All')
const methodFilter = ref('All')

const filteredHistory = computed(() => {
  return props.history.filter(item => {
    const matchesSearch = !search.value || 
      item.txn_id.toLowerCase().includes(search.value.toLowerCase()) ||
      item.student.name.toLowerCase().includes(search.value.toLowerCase()) ||
      item.course.toLowerCase().includes(search.value.toLowerCase())
    const matchesStatus = statusFilter.value === 'All' || item.status.toLowerCase() === statusFilter.value.toLowerCase()
    return matchesSearch && matchesStatus
  })
})

const handlePrint = () => {
  window.print()
}
</script>

<template>
  <div class="space-y-6">
    
    <!-- Quick Chips & Summary Cards -->
    <div class="space-y-4">
      <div class="flex flex-wrap items-center gap-2 text-xs">
        <span class="font-bold text-slate-700">Quick Range Chips:</span>
        <button 
          v-for="chip in ['Today', 'This Week', 'This Month', 'Year 2025', 'Custom']" 
          :key="chip"
          @click="selectedQuickChip = chip"
          class="px-3 py-1 rounded-xl font-bold transition"
          :class="selectedQuickChip === chip ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'"
        >
          {{ chip }}
        </button>
      </div>

      <!-- Summary Cards (5 Cards) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
          <div class="text-[11px] font-semibold text-slate-500">✅ Success</div>
          <div class="text-xl font-black text-emerald-600 mt-0.5">267 ($11,895)</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
          <div class="text-[11px] font-semibold text-slate-500">⏳ Pending</div>
          <div class="text-xl font-black text-amber-600 mt-0.5">3 ($120)</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
          <div class="text-[11px] font-semibold text-slate-500">❌ Failed</div>
          <div class="text-xl font-black text-rose-600 mt-0.5">8 ($350)</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
          <div class="text-[11px] font-semibold text-slate-500">💸 Refunded</div>
          <div class="text-xl font-black text-purple-700 mt-0.5">3 ($135)</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs border-l-4 border-l-indigo-600">
          <div class="text-[11px] font-semibold text-slate-500">💰 Net Revenue</div>
          <div class="text-xl font-black text-indigo-900 mt-0.5">$11,760</div>
        </div>
      </div>
    </div>

    <!-- Advanced Filters & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-lg font-bold text-slate-900">📜 PAYMENT HISTORY & AUDIT LOG</h2>
          <p class="text-xs text-slate-500">មើលប្រវត្តិទូទាត់ទាំងអស់ (សម្រេច/បរាជ័យ/សងវិញ) សម្រាប់សវនកម្ម និងរបាយការណ៍ពន្ធ</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <button class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">📄 PDF</button>
          <button class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs rounded-xl transition">📊 Excel</button>
          <button class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">📋 CSV</button>
          <button class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl transition">📧 Email Report</button>
          <button @click="handlePrint" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">🖨️ Print</button>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-2 border-t border-slate-100 text-xs">
        <input 
          v-model="search" 
          type="text" 
          placeholder="🔍 Search TXN ID, ABA Ref#, Student ID..." 
          class="p-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none" 
        />
        <select v-model="statusFilter" class="p-2 bg-slate-50 border border-slate-300 rounded-xl">
          <option value="All">Status: All</option>
          <option value="success">Success</option>
          <option value="pending">Pending</option>
          <option value="failed">Failed</option>
          <option value="refunded">Refunded</option>
        </select>
        <select v-model="methodFilter" class="p-2 bg-slate-50 border border-slate-300 rounded-xl">
          <option value="All">Method: All</option>
          <option value="aba">ABA</option>
          <option value="cash">Cash</option>
        </select>
        <select class="p-2 bg-slate-50 border border-slate-300 rounded-xl">
          <option>Group By: None</option>
          <option>Group By Course</option>
          <option>Group By Teacher</option>
          <option>Group By Major</option>
        </select>
      </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-700">
          <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase font-semibold">
            <tr>
              <th class="p-3.5">Date/Time</th>
              <th class="p-3.5">TXN ID</th>
              <th class="p-3.5">Student</th>
              <th class="p-3.5">Course</th>
              <th class="p-3.5">Amount</th>
              <th class="p-3.5">Method</th>
              <th class="p-3.5">Teacher</th>
              <th class="p-3.5">Status</th>
              <th class="p-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-mono">
            <tr v-for="item in filteredHistory" :key="item.txn_id" class="hover:bg-slate-50/80 transition">
              <td class="p-3.5 font-sans text-slate-500">{{ item.date_time }}</td>
              <td class="p-3.5 font-bold text-indigo-700">{{ item.txn_id }}</td>
              <td class="p-3.5 font-sans font-bold text-slate-900">{{ item.student.name }}</td>
              <td class="p-3.5 font-sans font-medium text-slate-800">{{ item.course }}</td>
              <td class="p-3.5 font-bold text-emerald-600">${{ item.amount.toFixed(2) }}</td>
              <td class="p-3.5 font-sans font-semibold text-slate-700">{{ item.method }}</td>
              <td class="p-3.5 font-sans text-slate-600">{{ item.teacher }}</td>
              <td class="p-3.5 font-sans">
                <span 
                  class="px-2.5 py-0.5 rounded-full text-[11px] font-bold capitalize"
                  :class="{
                    'bg-emerald-100 text-emerald-800': item.status === 'Success',
                    'bg-amber-100 text-amber-800': item.status === 'Pending',
                    'bg-rose-100 text-rose-800': item.status === 'Failed',
                    'bg-purple-100 text-purple-800': item.status === 'Refunded',
                  }"
                >
                  {{ item.status }}
                </span>
              </td>
              <td class="p-3.5 text-right font-sans">
                <div class="flex items-center justify-end gap-1">
                  <button @click="$emit('view-invoice', item)" class="p-1 text-slate-600 hover:text-indigo-600" title="View">👁</button>
                  <button @click="$emit('view-invoice', item)" class="p-1 text-slate-600 hover:text-indigo-600" title="Receipt">🧾</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
