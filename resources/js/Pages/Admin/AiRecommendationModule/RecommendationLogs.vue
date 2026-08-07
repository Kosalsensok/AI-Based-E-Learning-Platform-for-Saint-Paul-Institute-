<script setup lang="ts">
import { ref, computed } from 'vue'
import type { RecommendationLogItem, EffectivenessMetric } from './types'

const props = defineProps<{
  logs: RecommendationLogItem[]
  effectiveness: EffectivenessMetric[]
}>()

const emit = defineEmits<{
  (e: 'exportLogs'): void
  (e: 'applySuggestion'): void
  (e: 'viewDetail', log: RecommendationLogItem): void
}>()

const searchQuery = ref('')
const selectedRule = ref('all')
const selectedStatus = ref('all')
const selectedDateRange = ref('all')

const filteredLogs = computed(() => {
  return props.logs.filter(l => {
    const q = searchQuery.value.toLowerCase()
    const matchesSearch = !q || l.student_name.toLowerCase().includes(q) || l.student_id.toLowerCase().includes(q) || l.recommendation_text.toLowerCase().includes(q)
    const matchesRule = selectedRule.value === 'all' || l.rule_code === selectedRule.value
    const matchesStatus = selectedStatus.value === 'all' || l.status === selectedStatus.value
    return matchesSearch && matchesRule && matchesStatus
  })
})
</script>

<template>
  <div class="space-y-6 text-xs font-sans">
    <!-- Header Banner -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-700/60 pb-3.5">
        <div>
          <h3 class="text-base font-black text-white flex items-center gap-2.5">
            <div class="p-2 rounded-xl bg-gradient-to-br from-purple-500/20 to-indigo-500/20 border border-purple-400/30 text-purple-300 shrink-0">
              <svg class="w-4 h-4 text-purple-300" viewBox="0 0 24 24" fill="none">
                <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="#C084FC" stroke-width="1.8" />
                <path d="M14 2V8H20" stroke="#C084FC" stroke-width="1.8" />
                <path d="M16 13H8M16 17H8M10 9H8" stroke="#E9D5FF" stroke-width="1.8" stroke-linecap="round" />
              </svg>
            </div>
            <span>RECOMMENDATION LOGS & AUDIT TRAIL</span>
          </h3>
          <p class="text-slate-400 text-xs mt-0.5">Track every AI recommendation delivered to students — measure acceptance, completion, and effectiveness.</p>
        </div>

        <button
          @click="emit('exportLogs')"
          class="px-4.5 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all active:scale-95 ring-1 ring-purple-400/50"
        >
          <svg class="w-4 h-4 text-purple-200" viewBox="0 0 24 24" fill="none">
            <path d="M4 16V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V16M12 12V4M12 4L8 8M12 4L16 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <span>Export Logs</span>
        </button>
      </div>

      <!-- 4 Audit KPI Cards with Dual-tone Icons -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#121827] p-4 rounded-xl border border-slate-700/80 space-y-1">
          <div class="flex items-center justify-between">
            <span class="text-slate-400 text-[10px] block font-semibold">SENT RECOMMENDATIONS</span>
            <div class="p-1 rounded-md bg-slate-800 text-slate-400">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="#94A3B8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
          </div>
          <p class="text-2xl font-black text-white">12,450</p>
          <span class="text-[10px] text-slate-400 font-semibold block">Total Dispatched</span>
        </div>

        <div class="bg-[#121827] p-4 rounded-xl border border-slate-700/80 space-y-1">
          <div class="flex items-center justify-between">
            <span class="text-slate-400 text-[10px] block font-semibold">ACCEPTED</span>
            <div class="p-1 rounded-md bg-purple-500/15 text-purple-300">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                <path d="M14 9V5C14 3.89543 13.1046 3 12 3C10.8954 3 10 3.89543 10 5V12" stroke="#C084FC" stroke-width="1.8" stroke-linecap="round" />
                <path d="M18 11V7C18 5.89543 17.1046 5 16 5C14.8954 5 14 5.89543 14 7V12" stroke="#C084FC" stroke-width="1.8" stroke-linecap="round" />
                <path d="M10 9V7C10 5.89543 9.10457 5 8 5C6.89543 5 6 5.89543 6 7V15L3.5 12.5C2.67157 11.6716 1.32843 11.6716 0.5 12.5C-0.328427 13.3284 -0.328427 14.6716 0.5 15.5L6 21H18C19.6569 21 21 19.6569 21 18V13C21 11.8954 20.1046 11 19 11H18" stroke="#C084FC" stroke-width="1.8" stroke-linecap="round" />
              </svg>
            </div>
          </div>
          <p class="text-2xl font-black text-purple-300">8,715 (70%)</p>
          <span class="text-[10px] text-purple-400 font-semibold block">Clicked / Viewed</span>
        </div>

        <div class="bg-[#121827] p-4 rounded-xl border border-slate-700/80 space-y-1">
          <div class="flex items-center justify-between">
            <span class="text-slate-400 text-[10px] block font-semibold">COMPLETED</span>
            <div class="p-1 rounded-md bg-emerald-500/15 text-emerald-300">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9" stroke="#34D399" stroke-width="1.8" />
                <path d="M8 12L11 15L16 9" stroke="#34D399" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
          </div>
          <p class="text-2xl font-black text-emerald-400">6,847 (55%)</p>
          <span class="text-[10px] text-emerald-300 font-bold block">Remedial Done</span>
        </div>

        <div class="bg-[#121827] p-4 rounded-xl border border-slate-700/80 space-y-1">
          <div class="flex items-center justify-between">
            <span class="text-slate-400 text-[10px] block font-semibold">IGNORED</span>
            <div class="p-1 rounded-md bg-slate-800 text-slate-500">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9" stroke="#64748B" stroke-width="1.8" />
                <path d="M15 9L9 15M9 9L15 15" stroke="#64748B" stroke-width="1.8" stroke-linecap="round" />
              </svg>
            </div>
          </div>
          <p class="text-2xl font-black text-slate-400">3,735 (30%)</p>
          <span class="text-[10px] text-slate-500 font-semibold block">Dismissed or expired</span>
        </div>
      </div>
    </div>

    <!-- Consolidated Left-Aligned Filters Toolbar with Date Filter -->
    <div class="bg-[#0d1222]/90 border border-slate-700/60 p-3 rounded-2xl flex flex-wrap items-center justify-start gap-3">
      <!-- Search Input -->
      <div class="relative w-full sm:w-72">
        <div class="absolute left-3 top-2.5 text-cyan-400">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
            <circle cx="11" cy="11" r="7" stroke="#38BDF8" stroke-width="1.8" />
            <path d="M20 20L16 16" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" />
          </svg>
        </div>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search student name, ID, or recommendation..."
          class="w-full bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl pl-9 pr-3 py-2 text-xs focus:border-purple-500 focus:outline-none transition-all placeholder:text-slate-500 font-medium"
        />
      </div>

      <!-- Date Range Filter -->
      <div class="relative">
        <select
          v-model="selectedDateRange"
          class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-2 text-xs focus:border-purple-500 focus:outline-none cursor-pointer font-medium"
        >
          <option value="all">Date: All Time</option>
          <option value="today">Date: Today</option>
          <option value="7days">Date: Last 7 Days</option>
          <option value="30days">Date: This Month</option>
        </select>
      </div>

      <!-- Rule Code Filter Dropdown -->
      <div class="relative">
        <select
          v-model="selectedRule"
          class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-2 text-xs focus:border-purple-500 focus:outline-none cursor-pointer font-medium"
        >
          <option value="all">Rule: All</option>
          <option value="R-01">R-01 Advance High Performer</option>
          <option value="W-01">W-01 Weak Topic Remedial</option>
          <option value="R-05">R-05 Re-engagement</option>
        </select>
      </div>

      <!-- Status Filter Dropdown -->
      <div class="relative">
        <select
          v-model="selectedStatus"
          class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-2 text-xs focus:border-purple-500 focus:outline-none cursor-pointer font-medium"
        >
          <option value="all">Status: All</option>
          <option value="Done">✅ Done</option>
          <option value="Accepted">👆 Accepted</option>
          <option value="Sent">📨 Sent</option>
          <option value="Ignored">🙈 Ignored</option>
        </select>
      </div>

      <!-- Clear Filters Reset Button -->
      <button
        v-if="searchQuery !== '' || selectedRule !== 'all' || selectedStatus !== 'all' || selectedDateRange !== 'all'"
        @click="searchQuery = ''; selectedRule = 'all'; selectedStatus = 'all'; selectedDateRange = 'all'"
        class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-slate-200 rounded-lg text-xs transition-all flex items-center gap-1 font-medium border border-slate-700/50"
      >
        <span>✕ Reset Filters</span>
      </button>
    </div>

    <!-- Logs Audit Table with Explicit Column Headers -->
    <div class="overflow-x-auto rounded-2xl border border-slate-700/60 bg-[#0d1222]/95 shadow-xl">
      <table class="w-full text-left text-xs text-slate-300">
        <thead class="bg-[#121827] text-slate-400 uppercase font-bold border-b border-slate-700/80 tracking-wider">
          <tr>
            <th class="p-3.5">
              <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8" />
                  <path d="M12 7V12L15 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                </svg>
                <span>TIMESTAMP</span>
              </span>
            </th>
            <th class="p-3.5">
              <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-purple-400" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="1.8" />
                  <path d="M4 21V19C4 16.7909 5.79086 15 8 15H16C18.2091 15 20 16.7909 20 19V21" stroke="currentColor" stroke-width="1.8" />
                </svg>
                <span>STUDENT</span>
              </span>
            </th>
            <th class="p-3.5">
              <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-teal-400" viewBox="0 0 24 24" fill="none">
                  <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.8" />
                </svg>
                <span>TRIGGERED RULE</span>
              </span>
            </th>
            <th class="p-3.5">
              <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-indigo-400" viewBox="0 0 24 24" fill="none">
                  <path d="M22 12C22 12 19 8 12 8H7L3 12H7V18C7 18 12 18 12 18L22 12Z" stroke="currentColor" stroke-width="1.8" />
                </svg>
                <span>ACTION / RECOMMENDATION DELIVERED</span>
              </span>
            </th>
            <th class="p-3.5 w-28">
              <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-emerald-400" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8" />
                  <path d="M8 12L11 15L16 9" stroke="currentColor" stroke-width="2" />
                </svg>
                <span>STATUS</span>
              </span>
            </th>
            <th class="p-3.5 text-right w-28">ACTIONS</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-700/60 font-medium">
          <tr v-for="l in filteredLogs" :key="l.id" class="hover:bg-slate-800/50 transition-colors">
            <!-- Explicit Full Timestamp Format -->
            <td class="p-3.5 font-mono text-slate-300 text-[11px] whitespace-nowrap">
              {{ l.timestamp }} 2026
            </td>
            <td class="p-3.5">
              <div class="font-bold text-white text-sm">{{ l.student_name }}</div>
              <div class="text-[10px] font-mono text-purple-400">{{ l.student_id }}</div>
            </td>
            <td class="p-3.5 font-semibold text-slate-200">
              <span class="px-2 py-0.5 rounded bg-purple-950/80 text-purple-300 border border-purple-500/30 text-[10px] font-mono mr-1.5">{{ l.rule_code }}</span>
              <span>{{ l.rule_name }}</span>
            </td>
            <td class="p-3.5 font-medium text-slate-200">
              {{ l.recommendation_text }}
            </td>
            <td class="p-3.5 font-bold">
              <span
                class="px-2.5 py-1 rounded-full text-[10px] font-bold border transition-all inline-flex items-center gap-1 shadow-sm"
                :class="
                  l.status === 'Done' ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/40' : 
                  (l.status === 'Accepted' ? 'bg-purple-500/15 text-purple-300 border-purple-500/40' : 
                  (l.status === 'Sent' ? 'bg-cyan-500/15 text-cyan-300 border-cyan-500/40' : 'bg-slate-800 text-slate-400 border-slate-700'))
                "
              >
                <span>{{ l.status === 'Done' ? '✅ Done' : (l.status === 'Accepted' ? '👆 Accepted' : (l.status === 'Sent' ? '📨 Sent' : '🙈 Ignored')) }}</span>
              </span>
            </td>
            <td class="p-3.5 text-right">
              <!-- Flaticon Eye Button -->
              <button
                @click="emit('viewDetail', l)"
                title="View Audit Payload Detail"
                class="px-3 py-1 bg-blue-950/60 hover:bg-blue-900/80 text-blue-400 hover:text-blue-300 rounded-lg text-xs font-semibold border border-blue-500/30 shadow-sm flex items-center gap-1.5 ml-auto"
              >
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                  <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="#38BDF8" stroke-width="1.8" />
                  <circle cx="12" cy="12" r="3" fill="#0284C7" fill-opacity="0.5" />
                </svg>
                <span>Detail</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Recommendation Effectiveness Analytics with Dynamic Performance Bars -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5 flex items-center gap-2">
        <svg class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="none">
          <path d="M18 20V10M12 20V4M6 20V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
        </svg>
        <span>RECOMMENDATION EFFECTIVENESS (ACCEPTANCE RATE BY RULE TYPE)</span>
      </h4>

      <div class="space-y-3.5">
        <div v-for="(e, idx) in effectiveness" :key="idx" class="space-y-1">
          <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-slate-200 w-48">{{ e.rule_type }}</span>
            
            <!-- Dynamic Colored Progress Bar based on Performance Level -->
            <div class="flex-1 mx-3 h-3 bg-slate-900 rounded-full overflow-hidden border border-slate-700/80 p-0.5">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="
                  e.acceptance_rate >= 80
                    ? 'bg-gradient-to-r from-emerald-500 to-teal-400 shadow-sm shadow-emerald-500/30'
                    : (e.acceptance_rate >= 60
                    ? 'bg-gradient-to-r from-blue-500 to-cyan-400 shadow-sm shadow-blue-500/30'
                    : 'bg-gradient-to-r from-amber-500 to-rose-500 shadow-sm shadow-rose-500/30')
                "
                :style="{ width: e.acceptance_rate + '%' }"
              ></div>
            </div>

            <div class="w-40 text-right font-black flex items-center justify-end gap-2">
              <span class="text-white text-sm font-mono">{{ e.acceptance_rate }}%</span>
              
              <!-- Distinct Status Badges -->
              <span
                class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider"
                :class="
                  e.acceptance_rate >= 80
                    ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/40'
                    : (e.acceptance_rate >= 60
                    ? 'bg-cyan-500/15 text-cyan-300 border border-cyan-500/40'
                    : 'bg-rose-500/15 text-rose-300 border border-rose-500/40 animate-pulse')
                "
              >
                {{ e.status_tag }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- AI Insight Banner -->
      <div class="p-4.5 bg-gradient-to-r from-purple-950/60 via-indigo-950/60 to-slate-900 border border-purple-500/50 rounded-2xl space-y-2.5 text-xs shadow-xl">
        <span class="font-bold text-purple-300 flex items-center gap-2 uppercase tracking-wide">
          <svg class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="none">
            <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2Z" fill="#A855F7" fill-opacity="0.3" stroke="#C084FC" stroke-width="1.8" />
            <path d="M12 6V12L16 14" stroke="#C084FC" stroke-width="1.8" stroke-linecap="round" />
          </svg>
          <span>AI RECOMMENDATION INSIGHT</span>
        </span>

        <p class="text-slate-200 leading-relaxed font-medium">
          "Re-engagement emails sent at <strong class="text-purple-300 font-bold">7:00 PM</strong> have <strong class="text-emerald-400 font-bold">2x higher acceptance rate</strong> than those sent at 9:00 AM. Suggest changing alert schedule to evening hours."
        </p>

        <button
          @click="emit('applySuggestion')"
          class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all active:scale-95 ring-1 ring-purple-400/50 mt-1"
        >
          <svg class="w-4 h-4 text-purple-200" viewBox="0 0 24 24" fill="none">
            <path d="M5 13L9 17L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <span>Apply Evening Schedule Suggestion</span>
        </button>
      </div>
    </div>
  </div>
</template>
