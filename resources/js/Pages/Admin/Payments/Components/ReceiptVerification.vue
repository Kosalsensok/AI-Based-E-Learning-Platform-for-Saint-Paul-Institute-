<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  receipts: any[]
}>()

const emit = defineEmits(['open-verification'])

const search = ref('')
const statusFilter = ref('All')

const filteredReceipts = computed(() => {
  return props.receipts.filter(r => {
    const matchesSearch = !search.value || 
      r.student.name.toLowerCase().includes(search.value.toLowerCase()) ||
      r.course.toLowerCase().includes(search.value.toLowerCase())
    const matchesStatus = statusFilter.value === 'All' || r.status.toLowerCase() === statusFilter.value.toLowerCase()
    return matchesSearch && matchesStatus
  })
})
</script>

<template>
  <div class="space-y-6">
    
    <!-- Alert Banner -->
    <div class="bg-indigo-900 text-white p-4 rounded-2xl shadow-xl flex items-center justify-between text-xs border border-indigo-800">
      <div class="flex items-center gap-2 font-bold">
        <span class="text-base">📋</span>
        <span><strong>17 receipts pending verification.</strong> Average review time: 3 minutes.</span>
      </div>
      <span class="bg-indigo-800 text-indigo-200 px-3 py-1 rounded-xl font-mono">Real-Time Queue Active</span>
    </div>

    <!-- Filter & Header Control Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
      <div>
        <h2 class="text-lg font-bold text-slate-900">🧾 RECEIPT VERIFICATION QUEUE</h2>
        <p class="text-xs text-slate-500">សម្រាប់ករណីនិស្សិតបង់ផ្ទាល់ ឬ Upload រូបភាព Screenshot ABA – Admin ផ្ទៀងផ្ទាត់ដោយដៃ</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-100 text-xs">
        <input 
          v-model="search" 
          type="text" 
          placeholder="🔍 Student / Course..." 
          class="p-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none" 
        />
        <select v-model="statusFilter" class="p-2 bg-slate-50 border border-slate-300 rounded-xl">
          <option value="All">Status: All</option>
          <option value="pending">Pending</option>
          <option value="review">Review</option>
        </select>
        <select class="p-2 bg-slate-50 border border-slate-300 rounded-xl">
          <option>Verification Method: All</option>
          <option>Manual Slip Upload</option>
          <option>Cash on Campus</option>
        </select>
      </div>
    </div>

    <!-- Queue Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-700">
          <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase font-semibold">
            <tr>
              <th class="p-3.5">#</th>
              <th class="p-3.5">Student</th>
              <th class="p-3.5">Course</th>
              <th class="p-3.5">Amount</th>
              <th class="p-3.5">Uploaded Receipt</th>
              <th class="p-3.5">Submitted</th>
              <th class="p-3.5">AI Match</th>
              <th class="p-3.5">Status</th>
              <th class="p-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="rcp in filteredReceipts" :key="rcp.id" class="hover:bg-slate-50/80 transition">
              <td class="p-3.5 font-bold text-slate-400 font-mono">#{{ rcp.id }}</td>
              <td class="p-3.5">
                <div class="font-bold text-slate-900">{{ rcp.student.name }}</div>
                <div class="text-[11px] text-slate-400 font-mono">{{ rcp.student.id }}</div>
              </td>
              <td class="p-3.5 font-medium text-slate-800">{{ rcp.course }}</td>
              <td class="p-3.5 font-bold text-emerald-600">${{ rcp.amount.toFixed(2) }}</td>
              <td class="p-3.5">
                <button @click="$emit('open-verification', rcp)" class="text-indigo-600 hover:underline font-bold flex items-center gap-1">
                  🖼️ [View]
                </button>
              </td>
              <td class="p-3.5 text-slate-500 font-medium">{{ rcp.submitted_time }}</td>
              <td class="p-3.5">
                <span 
                  class="px-2.5 py-0.5 rounded-full text-[11px] font-bold inline-flex items-center gap-1"
                  :class="{
                    'bg-emerald-100 text-emerald-800': rcp.ai_match_level === 'high',
                    'bg-amber-100 text-amber-800': rcp.ai_match_level === 'medium',
                    'bg-rose-100 text-rose-800': rcp.ai_match_level === 'low',
                  }"
                >
                  {{ rcp.ai_match_label }}
                </span>
              </td>
              <td class="p-3.5">
                <span 
                  class="px-2 py-0.5 rounded-md text-[11px] font-bold capitalize"
                  :class="rcp.status === 'Pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800'"
                >
                  {{ rcp.status === 'Pending' ? '⏳ Pending' : '⚠️ Review' }}
                </span>
              </td>
              <td class="p-3.5 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button @click="$emit('open-verification', rcp)" class="p-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-lg" title="Approve">✅</button>
                  <button @click="$emit('open-verification', rcp)" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg" title="Reject">❌</button>
                  <button @click="$emit('open-verification', rcp)" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg" title="Ask Info">💬</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
