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
const showExportDropdown = ref(false)

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

const exportData = (format: string) => {
  showExportDropdown.value = false
  // Trigger export format logic or alert
}
</script>

<template>
  <div class="space-y-6 text-slate-100 font-sans">
    
    <!-- Quick Range Chips & Summary Cards -->
    <div class="space-y-4">
      <div class="flex flex-wrap items-center gap-2 text-xs">
        <span class="font-bold text-slate-400 flex items-center gap-1">
          <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <span>Date Filter:</span>
        </span>
        <button 
          v-for="chip in ['Today', 'This Week', 'This Month', 'This Year', 'Custom']" 
          :key="chip"
          @click="selectedQuickChip = chip"
          class="px-3 py-1.5 rounded-xl font-semibold text-xs transition cursor-pointer"
          :class="selectedQuickChip === chip ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-800/90 border border-slate-700/80 text-slate-300 hover:bg-slate-700/80'"
        >
          {{ chip }}
        </button>
      </div>

      <!-- Summary Cards (5 Sleek Clickable Cards for Quick Filtering) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <div 
          @click="statusFilter = 'success'" 
          class="bg-slate-800/90 p-4 rounded-2xl border transition-all cursor-pointer shadow-xl space-y-1 group hover:border-emerald-500/60"
          :class="statusFilter === 'success' ? 'border-emerald-500 ring-2 ring-emerald-500/30 bg-emerald-950/20' : 'border-slate-700/80'"
          title="Click to filter by Success"
        >
          <div class="text-[11px] font-semibold text-slate-400 flex items-center justify-between">
            <span class="flex items-center gap-1.5 group-hover:text-emerald-300">
              <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Success</span>
            </span>
            <span v-if="statusFilter === 'success'" class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded-md">Active</span>
          </div>
          <div class="text-xl font-black text-emerald-400 font-mono">267 ($11,895)</div>
        </div>

        <div 
          @click="statusFilter = 'pending'" 
          class="bg-slate-800/90 p-4 rounded-2xl border transition-all cursor-pointer shadow-xl space-y-1 group hover:border-amber-500/60"
          :class="statusFilter === 'pending' ? 'border-amber-500 ring-2 ring-amber-500/30 bg-amber-950/20' : 'border-slate-700/80'"
          title="Click to filter by Pending"
        >
          <div class="text-[11px] font-semibold text-slate-400 flex items-center justify-between">
            <span class="flex items-center gap-1.5 group-hover:text-amber-300">
              <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Pending</span>
            </span>
            <span v-if="statusFilter === 'pending'" class="text-[10px] font-bold text-amber-400 bg-amber-500/10 px-1.5 py-0.5 rounded-md">Active</span>
          </div>
          <div class="text-xl font-black text-amber-400 font-mono">3 ($120)</div>
        </div>

        <div 
          @click="statusFilter = 'failed'" 
          class="bg-slate-800/90 p-4 rounded-2xl border transition-all cursor-pointer shadow-xl space-y-1 group hover:border-rose-500/60"
          :class="statusFilter === 'failed' ? 'border-rose-500 ring-2 ring-rose-500/30 bg-rose-950/20' : 'border-slate-700/80'"
          title="Click to filter by Failed"
        >
          <div class="text-[11px] font-semibold text-slate-400 flex items-center justify-between">
            <span class="flex items-center gap-1.5 group-hover:text-rose-300">
              <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Failed</span>
            </span>
            <span v-if="statusFilter === 'failed'" class="text-[10px] font-bold text-rose-400 bg-rose-500/10 px-1.5 py-0.5 rounded-md">Active</span>
          </div>
          <div class="text-xl font-black text-rose-400 font-mono">8 ($350)</div>
        </div>

        <div 
          @click="statusFilter = 'refunded'" 
          class="bg-slate-800/90 p-4 rounded-2xl border transition-all cursor-pointer shadow-xl space-y-1 group hover:border-purple-500/60"
          :class="statusFilter === 'refunded' ? 'border-purple-500 ring-2 ring-purple-500/30 bg-purple-950/20' : 'border-slate-700/80'"
          title="Click to filter by Refunded"
        >
          <div class="text-[11px] font-semibold text-slate-400 flex items-center justify-between">
            <span class="flex items-center gap-1.5 group-hover:text-purple-300">
              <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
              </svg>
              <span>Refunded</span>
            </span>
            <span v-if="statusFilter === 'refunded'" class="text-[10px] font-bold text-purple-400 bg-purple-500/10 px-1.5 py-0.5 rounded-md">Active</span>
          </div>
          <div class="text-xl font-black text-purple-400 font-mono">3 ($135)</div>
        </div>

        <div 
          @click="statusFilter = 'All'" 
          class="bg-slate-800/90 p-4 rounded-2xl border border-slate-700/80 shadow-xl space-y-1 border-l-4 border-l-indigo-500 transition-all cursor-pointer hover:border-indigo-500/60"
          :class="statusFilter === 'All' ? 'ring-2 ring-indigo-500/30 bg-indigo-950/20' : ''"
          title="Click to Reset All Filters"
        >
          <div class="text-[11px] font-semibold text-slate-400 flex items-center justify-between">
            <span class="flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Net Revenue</span>
            </span>
            <span class="text-[10px] text-slate-500 font-mono">All</span>
          </div>
          <div class="text-xl font-black text-indigo-300 font-mono">$11,760</div>
        </div>
      </div>
    </div>

    <!-- Advanced Filters & Uniform Export Action Bar -->
    <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 shadow-xl space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-base font-extrabold text-white flex items-center gap-2">
            <span class="p-1.5 bg-indigo-500/10 border border-indigo-500/20 rounded-lg text-indigo-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </span>
            <span>PAYMENT HISTORY & AUDIT LOG</span>
          </h2>
          <!-- Professional English Subtitle -->
          <p class="text-xs text-slate-400 mt-0.5 font-medium">Comprehensive transaction logs for auditing and financial reporting.</p>
        </div>

        <!-- Uniform Secondary Export Toolbar with Dropdown Option -->
        <div class="flex items-center gap-2 text-xs">
          <!-- Export Dropdown Button -->
          <div class="relative">
            <button 
              @click="showExportDropdown = !showExportDropdown" 
              class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-700 text-slate-200 font-semibold rounded-xl border border-slate-700 transition flex items-center gap-1.5 cursor-pointer shadow-xs"
            >
              <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              <span>Export Report</span>
              <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <div 
              v-if="showExportDropdown" 
              class="absolute right-0 top-full mt-2 w-44 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl z-30 py-1.5 text-xs text-slate-200"
            >
              <button @click="exportData('pdf')" class="w-full px-3.5 py-2 text-left hover:bg-slate-800 flex items-center gap-2 cursor-pointer">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span>Export as PDF</span>
              </button>
              <button @click="exportData('excel')" class="w-full px-3.5 py-2 text-left hover:bg-slate-800 flex items-center gap-2 cursor-pointer">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Export as Excel (.xlsx)</span>
              </button>
              <button @click="exportData('csv')" class="w-full px-3.5 py-2 text-left hover:bg-slate-800 flex items-center gap-2 cursor-pointer">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span>Export as CSV</span>
              </button>
            </div>
          </div>

          <!-- Quick Uniform Secondary Buttons -->
          <button 
            @click="handlePrint" 
            class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-700 text-slate-200 font-semibold rounded-xl border border-slate-700 transition flex items-center gap-1.5 cursor-pointer shadow-xs"
            title="Print Summary Report"
          >
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Print</span>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-2 border-t border-slate-700/60 text-xs">
        <!-- Search Input with Clear 'X' Button -->
        <div class="relative">
          <input 
            v-model="search" 
            type="text" 
            placeholder="🔍 Search TXN ID, ABA Ref#, Student..." 
            class="w-full p-2.5 pr-8 bg-slate-900/90 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-white placeholder:text-slate-500 focus:outline-none" 
          />
          <button 
            v-if="search" 
            @click="search = ''" 
            class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 text-[10px] flex items-center justify-center font-bold transition"
            title="Clear Search"
          >
            ✕
          </button>
        </div>

        <select v-model="statusFilter" class="p-2.5 bg-slate-900/90 border border-slate-700 rounded-xl text-slate-200 focus:outline-none">
          <option value="All">Status: All</option>
          <option value="success">Success</option>
          <option value="pending">Pending</option>
          <option value="failed">Failed</option>
          <option value="refunded">Refunded</option>
        </select>
        <select v-model="methodFilter" class="p-2.5 bg-slate-900/90 border border-slate-700 rounded-xl text-slate-200 focus:outline-none">
          <option value="All">Method: All</option>
          <option value="aba">ABA</option>
          <option value="cash">Cash</option>
        </select>
        <select class="p-2.5 bg-slate-900/90 border border-slate-700 rounded-xl text-slate-200 focus:outline-none">
          <option>Group By: None</option>
          <option>Group By Course</option>
          <option>Group By Teacher</option>
          <option>Group By Major</option>
        </select>
      </div>
    </div>

    <!-- History Table -->
    <div class="bg-slate-800/90 rounded-2xl border border-slate-700/80 shadow-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-200">
          <thead class="bg-slate-900/90 border-b border-slate-700/80 text-slate-400 uppercase font-bold text-[11px] tracking-wider whitespace-nowrap">
            <tr>
              <th class="py-3.5 pl-4 pr-2">Date/Time</th>
              <th class="py-3.5 px-2.5">TXN ID</th>
              <th class="py-3.5 px-2.5">Student</th>
              <th class="py-3.5 px-2.5">Course</th>
              <th class="py-3.5 px-2.5 text-right pr-6">Amount</th>
              <th class="py-3.5 px-2.5">Method</th>
              <th class="py-3.5 px-2.5">Teacher</th>
              <th class="py-3.5 px-2.5">Status</th>
              <th class="py-3.5 pr-4 pl-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60 font-mono">
            <tr v-for="item in filteredHistory" :key="item.txn_id" class="hover:bg-slate-700/40 transition-colors">
              <td class="py-3 pl-4 pr-2 font-sans text-slate-400 whitespace-nowrap align-middle">{{ item.date_time }}</td>
              <td class="py-3 px-2.5 font-bold text-indigo-400 whitespace-nowrap align-middle">{{ item.txn_id }}</td>
              <td class="py-3 px-2.5 font-sans font-bold text-white whitespace-nowrap align-middle">{{ item.student.name }}</td>
              <td class="py-3 px-2.5 font-sans font-medium text-slate-300 whitespace-nowrap align-middle">{{ item.course }}</td>
              
              <!-- Strict Align Right for Amount Column -->
              <td class="py-3 px-2.5 text-right font-bold text-emerald-400 font-mono pr-6 whitespace-nowrap align-middle">${{ item.amount.toFixed(2) }}</td>
              
              <td class="py-3 px-2.5 font-sans font-semibold text-slate-300 whitespace-nowrap align-middle">{{ item.method }}</td>
              <td class="py-3 px-2.5 font-sans text-slate-400 whitespace-nowrap align-middle">{{ item.teacher }}</td>
              <td class="py-3 px-2.5 font-sans whitespace-nowrap align-middle">
                <span 
                  class="px-2.5 py-1 rounded-xl text-[11px] font-bold inline-flex items-center gap-1.5 shadow-xs"
                  :class="{
                    'bg-emerald-500/10 text-emerald-400 border border-emerald-500/25': item.status === 'Success',
                    'bg-amber-500/10 text-amber-400 border border-amber-500/25': item.status === 'Pending',
                    'bg-rose-500/10 text-rose-400 border border-rose-500/25': item.status === 'Failed',
                    'bg-purple-500/10 text-purple-400 border border-purple-500/25': item.status === 'Refunded',
                  }"
                >
                  <!-- Success Vector Check Circle Icon -->
                  <svg v-if="item.status === 'Success'" class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  
                  <!-- Pending Vector Clock Icon -->
                  <svg v-else-if="item.status === 'Pending'" class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  
                  <!-- Failed Vector Cross Circle Icon -->
                  <svg v-else-if="item.status === 'Failed'" class="w-3.5 h-3.5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  
                  <!-- Refunded Vector Undo Arrow Icon -->
                  <svg v-else-if="item.status === 'Refunded'" class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                  </svg>

                  <span>{{ item.status }}</span>
                </span>
              </td>
              
              <!-- High-Contrast Vector SVG Action Buttons with Proper Spacing & Tooltips -->
              <td class="py-3 pr-4 pl-2 text-right font-sans align-middle whitespace-nowrap">
                <div class="flex items-center justify-end gap-2">
                  <button 
                    @click="$emit('view-invoice', item)" 
                    class="px-3 py-1.5 bg-indigo-500/15 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 font-semibold rounded-xl text-xs transition-all shadow-xs flex items-center gap-1.5 cursor-pointer" 
                    title="View Invoice & Audit Details"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>View Details</span>
                  </button>

                  <button 
                    @click="$emit('view-invoice', item)" 
                    class="p-1.5 bg-slate-900/80 hover:bg-slate-800 text-slate-300 hover:text-white rounded-xl text-xs border border-slate-700/60 transition-all cursor-pointer" 
                    title="Download Official Receipt (PDF)"
                  >
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
