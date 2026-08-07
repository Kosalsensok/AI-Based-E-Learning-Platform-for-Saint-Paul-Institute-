<script setup lang="ts">
import { ref } from 'vue'
import type { PaymentAnalyticsData } from './types'

const props = defineProps<{
  data: PaymentAnalyticsData
}>()

const emit = defineEmits<{
  (e: 'optimizeFunnel'): void
  (e: 'exportReport'): void
}>()

const selectedMonth = ref('this_month')
</script>

<template>
  <div class="space-y-6 text-xs font-sans">
    <!-- Header Banner -->
    <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-4">
      <div>
        <h3 class="text-base font-black text-white flex items-center gap-2">
          <span>💳</span> PAYMENT & REVENUE ANALYTICS
        </h3>
        <p class="text-slate-400 text-xs">Analyze gross/net revenue, conversion funnels, payment gateways breakdown, and refund metrics.</p>
      </div>

      <div class="flex items-center gap-2">
        <select v-model="selectedMonth" class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs focus:border-purple-500 focus:outline-none">
          <option value="this_month">Period: This Month (June 2025)</option>
          <option value="last_month">Period: Last Month</option>
        </select>

        <!-- Compact Icon Refresh Button -->
        <button 
          @click="emit('exportReport')" 
          title="Refresh Data"
          class="p-2 bg-slate-800 hover:bg-slate-700/90 border border-slate-700 text-slate-300 hover:text-white rounded-xl transition-all active:scale-95 flex items-center justify-center shrink-0"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>

        <button @click="emit('exportReport')" class="px-4 py-1.5 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-md shadow-purple-600/30 flex items-center gap-1.5 transition-all">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          <span>Export Financial Report</span>
        </button>
      </div>
    </div>

    <!-- 8 Revenue KPI Cards Grid -->
    <div class="space-y-3">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
          <span class="text-slate-400 text-[10px] block font-semibold">💰 GROSS REVENUE</span>
          <p class="text-2xl font-black text-white">{{ data.kpis.gross_revenue }}</p>
          <span class="text-[10px] text-emerald-400 font-bold">Total Processed</span>
        </div>

        <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
          <span class="text-slate-400 text-[10px] block font-semibold">🏫 NET REVENUE</span>
          <p class="text-2xl font-black text-emerald-400">{{ data.kpis.net_revenue }}</p>
          <span class="text-[10px] text-slate-400">After Gateway Fees</span>
        </div>

        <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
          <span class="text-slate-400 text-[10px] block font-semibold">↩️ REFUND AMOUNT</span>
          <p class="text-2xl font-black text-amber-300">{{ data.kpis.refund_amount }} ({{ data.kpis.refund_percent }}%)</p>
          <span class="text-[10px] text-amber-400 font-semibold">Low Refund Ratio</span>
        </div>

        <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
          <span class="text-slate-400 text-[10px] block font-semibold">📈 MOM GROWTH</span>
          <p class="text-2xl font-black text-purple-300">+{{ data.kpis.growth }}%</p>
          <span class="text-[10px] text-emerald-400 font-bold">Positive Trajectory</span>
        </div>
      </div>

      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
          <span class="text-slate-400 text-[10px] block font-semibold">💳 PAID TRANSACTIONS</span>
          <p class="text-lg font-black text-white">{{ data.kpis.paid_txn }} Txns</p>
          <span class="text-[10px] text-emerald-400 font-semibold">Completed Orders</span>
        </div>

        <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
          <span class="text-slate-400 text-[10px] block font-semibold">⏳ PENDING VERIFICATION</span>
          <p class="text-lg font-black text-amber-300">{{ data.kpis.pending_txn }} Txns</p>
          <span class="text-[10px] text-amber-400 font-semibold">ABA Receipt Verification</span>
        </div>

        <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
          <span class="text-slate-400 text-[10px] block font-semibold">❌ FAILED TRANSACTIONS</span>
          <p class="text-lg font-black text-red-400">{{ data.kpis.failed_txn }} ({{ data.kpis.failed_percent }}%)</p>
          <span class="text-[10px] text-slate-400">Payment Cancellations</span>
        </div>

        <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4 rounded-2xl shadow-xl space-y-1">
          <span class="text-slate-400 text-[10px] block font-semibold">🎁 FREE ENROLLMENTS</span>
          <p class="text-lg font-black text-cyan-300">{{ data.kpis.free_enrolls }} Enrolls</p>
          <span class="text-[10px] text-cyan-400 font-semibold">Public Free Courses</span>
        </div>
      </div>
    </div>

    <!-- Revenue Trend Chart representation (12 Months) -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
        <h4 class="font-black text-sm text-white uppercase tracking-wide">
          📈 REVENUE TREND (12 MONTHS TRAJECTORY)
        </h4>
        <span class="text-slate-400 font-mono text-[11px]">Total 12-Month Revenue: <strong class="text-emerald-400">$215,320</strong> · YoY Growth: <strong class="text-purple-300">+45%</strong></span>
      </div>

      <div class="h-44 flex items-end justify-between gap-3 pt-6 pb-2 px-4 bg-[#121827] rounded-xl border border-slate-700/80">
        <div v-for="(item, idx) in data.revenue_trend_12m" :key="idx" class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
          <span class="text-[9px] font-bold text-emerald-400 group-hover:scale-110 transition-transform">${{ item.amount / 1000 }}k</span>
          <div class="w-full bg-slate-800 rounded-t-lg overflow-hidden flex items-end h-32 p-0.5">
            <div
              class="w-full bg-gradient-to-t from-emerald-600 via-teal-500 to-purple-400 rounded-t transition-all duration-500 group-hover:brightness-125"
              :style="{ height: (item.amount / 60000) * 100 + '%' }"
            ></div>
          </div>
          <span class="text-[10px] font-bold text-slate-400">{{ item.month }}</span>
        </div>
      </div>
    </div>

    <!-- Revenue by Major & Revenue by Mode -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <!-- Revenue by Major -->
      <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
          💰 REVENUE BY ACADEMIC MAJOR
        </h4>

        <div class="space-y-3">
          <div v-for="(m, idx) in data.revenue_by_major" :key="idx" class="space-y-1">
            <div class="flex items-center justify-between text-xs">
              <span class="font-semibold text-slate-300 w-40">{{ m.major }}</span>
              <div class="flex-1 mx-3 h-2.5 bg-slate-900 rounded-full overflow-hidden border border-slate-700/80">
                <div class="h-full bg-emerald-400 rounded-full" :style="{ width: m.percent * 2.5 + '%' }"></div>
              </div>
              <span class="font-bold text-white w-32 text-right">{{ m.amount }} ({{ m.percent }}%)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Revenue by Mode -->
      <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
          💰 REVENUE BY LEARNING MODE
        </h4>

        <div class="space-y-3">
          <div v-for="(mode, idx) in data.revenue_by_mode" :key="idx" class="space-y-1">
            <div class="flex items-center justify-between text-xs">
              <span class="font-semibold text-slate-300 w-44">{{ mode.mode }}</span>
              <div class="flex-1 mx-3 h-2.5 bg-slate-900 rounded-full overflow-hidden border border-slate-700/80">
                <div class="h-full bg-purple-500 rounded-full" :style="{ width: mode.percent + '%' }"></div>
              </div>
              <span class="font-bold text-white w-32 text-right">{{ mode.amount }} ({{ mode.percent }}%)</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Conversion Funnel & Payment Gateways Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <!-- Conversion Funnel -->
      <div class="bg-[#0d1222]/95 border border-purple-500/30 rounded-2xl p-5 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-2.5">
          <h4 class="font-black text-sm text-white uppercase tracking-wide flex items-center gap-2">
            <span>🎯</span> CONVERSION FUNNEL
          </h4>
          <button @click="emit('optimizeFunnel')" class="text-xs text-purple-300 font-bold hover:underline">
            Optimize Checkout →
          </button>
        </div>

        <div class="space-y-2.5">
          <div v-for="(stage, idx) in data.conversion_funnel" :key="idx" class="space-y-1">
            <div class="flex items-center justify-between text-xs font-semibold">
              <span class="text-slate-300 w-44">{{ stage.stage }}</span>
              <div class="flex-1 mx-3 h-3 bg-slate-900 rounded-full overflow-hidden border border-slate-700/80">
                <div class="h-full bg-gradient-to-r from-purple-600 to-teal-400 rounded-full" :style="{ width: stage.percent + '%' }"></div>
              </div>
              <span class="font-bold text-white w-32 text-right">{{ stage.percent }}% ({{ stage.count.toLocaleString() }})</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Gateways Distribution -->
      <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="font-black text-sm text-white uppercase tracking-wide border-b border-slate-700/60 pb-2.5">
          💳 PAYMENT METHOD DISTRIBUTION
        </h4>

        <div class="space-y-3">
          <div v-for="(pm, idx) in data.payment_methods" :key="idx" class="space-y-1">
            <div class="flex items-center justify-between text-xs">
              <span class="font-semibold text-slate-300 w-32">{{ pm.name }}</span>
              <div class="flex-1 mx-3 h-2.5 bg-slate-900 rounded-full overflow-hidden border border-slate-700/80">
                <div class="h-full bg-cyan-400 rounded-full" :style="{ width: pm.percent + '%' }"></div>
              </div>
              <span class="font-bold text-white w-36 text-right">{{ pm.count }} Txns ({{ pm.percent }}%)</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
