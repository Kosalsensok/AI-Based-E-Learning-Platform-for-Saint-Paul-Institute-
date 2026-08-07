<script setup lang="ts">
import { ref, computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'

const props = defineProps<{
  stats: any
  paymentMethodsDistribution: any[]
  liveActivityFeed: any[]
  recentTransactions: any[]
}>()

const emit = defineEmits(['open-pricing', 'open-verification', 'open-refund', 'open-payout', 'switch-tab', 'view-transaction'])

const timeRange = ref('30 Days')

// ApexCharts Configuration for Revenue Trend (30 Days Line Chart)
const trendChartOptions = computed<any>(() => ({
  chart: {
    type: 'line',
    toolbar: { show: false },
    fontFamily: 'Inter, sans-serif'
  },
  colors: ['#4f46e5'],
  stroke: { curve: 'smooth', width: 3 },
  xaxis: {
    categories: ['Day 1', 'Day 5', 'Day 10', 'Day 15', 'Day 20', 'Day 25', 'Day 30'],
    labels: { style: { colors: '#64748b', fontSize: '11px' } }
  },
  yaxis: {
    labels: {
      formatter: (val: number) => `$${val.toLocaleString()}`,
      style: { colors: '#64748b', fontSize: '11px' }
    }
  },
  tooltip: {
    y: { formatter: (val: number) => `$${val.toLocaleString()}` }
  }
}))

const trendChartSeries = computed(() => [
  { name: 'Revenue ($)', data: [350, 480, 720, 1100, 950, 1400, 1800] }
])

// Pie Chart for Payment Method Distribution
const pieChartOptions = computed<any>(() => ({
  chart: { type: 'pie', fontFamily: 'Inter, sans-serif' },
  labels: props.paymentMethodsDistribution.map(p => p.method),
  colors: props.paymentMethodsDistribution.map(p => p.color),
  legend: { position: 'bottom' },
  dataLabels: { enabled: true, formatter: (val: number) => `${val.toFixed(0)}%` }
}))

const pieChartSeries = computed(() => props.paymentMethodsDistribution.map(p => p.amount))

const formatMoney = (val: number) => `$${(val || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}`
</script>

<template>
  <div class="space-y-6">
    
    <!-- ROW 1: KPI CARDS (4 CARDS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- 1. Today's Revenue -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition">
        <div class="flex items-center justify-between text-slate-500 text-xs font-semibold mb-2">
          <span>💵 Today's Revenue</span>
          <span class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md font-bold text-[11px]">↗️ +{{ stats.today_growth }}% vs yesterday</span>
        </div>
        <div class="text-2xl font-black text-slate-900">{{ formatMoney(stats.today_revenue) }}</div>
        <div class="text-xs text-slate-400 mt-1">Real-time daily collection</div>
      </div>

      <!-- 2. This Month -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition">
        <div class="flex items-center justify-between text-slate-500 text-xs font-semibold mb-2">
          <span>📅 This Month</span>
          <span class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md font-bold text-[11px]">↗️ +{{ stats.month_growth }}% vs last month</span>
        </div>
        <div class="text-2xl font-black text-indigo-700">{{ formatMoney(stats.month_revenue) }}</div>
        <div class="text-xs text-slate-400 mt-1">Monthly total revenue</div>
      </div>

      <!-- 3. Paying Students -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition">
        <div class="flex items-center justify-between text-slate-500 text-xs font-semibold mb-2">
          <span>👥 Paying Students</span>
          <span class="text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md font-bold text-[11px]">↗️ +{{ stats.new_paying_students }} new</span>
        </div>
        <div class="text-2xl font-black text-slate-900">{{ stats.paying_students?.toLocaleString() }}</div>
        <div class="text-xs text-slate-400 mt-1">Enrolled paid students</div>
      </div>

      <!-- 4. Avg. Order Value -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition">
        <div class="flex items-center justify-between text-slate-500 text-xs font-semibold mb-2">
          <span>🎯 Avg. Order Value</span>
          <span class="text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md font-bold text-[11px]">→ same as last mo</span>
        </div>
        <div class="text-2xl font-black text-slate-900">{{ formatMoney(stats.avg_order_value) }}</div>
        <div class="text-xs text-slate-400 mt-1">Average transaction value</div>
      </div>
    </div>

    <!-- ROW 2: CHARTS (Revenue Trend Line Chart + Payment Method Pie Chart) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Revenue Trend (Line Chart) -->
      <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-sm font-bold text-slate-900">📊 REVENUE TREND (30 DAYS)</h3>
            <p class="text-xs text-slate-500">Daily revenue movement across 30 days</p>
          </div>
          <span class="text-xs text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg font-bold">30 Days Chart</span>
        </div>
        <div class="h-64">
          <VueApexCharts type="line" height="100%" :options="trendChartOptions" :series="trendChartSeries" />
        </div>
      </div>

      <!-- Payment Method (Pie Chart) -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
        <div>
          <h3 class="text-sm font-bold text-slate-900 mb-1">🥧 PAYMENT METHOD DISTRIBUTION</h3>
          <p class="text-xs text-slate-500 mb-4">Breakdown by payment channels</p>
          <div class="h-52">
            <VueApexCharts type="pie" height="100%" :options="pieChartOptions" :series="pieChartSeries" />
          </div>
        </div>

        <div class="space-y-1.5 mt-4 pt-3 border-t border-slate-100 text-xs">
          <div v-for="pm in paymentMethodsDistribution" :key="pm.method" class="flex items-center justify-between p-2 rounded-xl bg-slate-50">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: pm.color }"></span>
              <span class="font-semibold text-slate-700">{{ pm.method }}</span>
            </div>
            <span class="font-bold text-slate-900">{{ pm.percentage }}% ({{ formatMoney(pm.amount) }})</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ROW 3: LIVE ACTIVITY FEED (REAL-TIME TIMELINE TABLE) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            LIVE ACTIVITY FEED (REAL-TIME)
          </h3>
          <p class="text-xs text-slate-500">Live stream of incoming student payment transactions</p>
        </div>
        <span class="text-xs text-slate-400 font-mono">Auto-Sync Active</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-700">
          <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase font-semibold">
            <tr>
              <th class="p-3.5">Time</th>
              <th class="p-3.5">Student</th>
              <th class="p-3.5">Course</th>
              <th class="p-3.5">Amount</th>
              <th class="p-3.5">Method</th>
              <th class="p-3.5">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="(act, idx) in liveActivityFeed" :key="idx" class="hover:bg-slate-50/80 transition">
              <td class="p-3.5 text-slate-400 font-mono">{{ act.time }}</td>
              <td class="p-3.5 font-bold text-slate-900">{{ act.student }}</td>
              <td class="p-3.5 font-medium text-slate-800">{{ act.course }}</td>
              <td class="p-3.5 font-bold text-emerald-600">${{ act.amount.toFixed(2) }}</td>
              <td class="p-3.5">
                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded-md">💳 {{ act.method }}</span>
              </td>
              <td class="p-3.5">
                <span 
                  class="px-2.5 py-0.5 rounded-full text-[11px] font-bold"
                  :class="{
                    'bg-emerald-100 text-emerald-800': act.status === 'Success',
                    'bg-amber-100 text-amber-800': act.status === 'Processing',
                    'bg-rose-100 text-rose-800': act.status === 'Failed',
                  }"
                >
                  {{ act.status === 'Success' ? '✅ Success' : (act.status === 'Processing' ? '⏳ Processing' : '❌ Failed') }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ROW 4: QUICK ACTIONS -->
    <div class="bg-slate-900 text-white p-5 rounded-2xl shadow-xl flex flex-wrap items-center justify-between gap-4">
      <div class="text-xs font-bold text-slate-300 flex items-center gap-2">
        <span class="p-1 bg-indigo-600 text-white rounded-lg">⚡</span>
        Financial Quick Actions:
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <button @click="$emit('switch-tab', 'transactions')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition">
          🧾 View All Transactions
        </button>
        <button @click="$emit('open-pricing')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition">
          💰 Set Pricing
        </button>
        <button @click="$emit('switch-tab', 'revenue')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl transition">
          📤 Export Report
        </button>
        <button @click="$emit('switch-tab', 'transactions')" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow-md transition">
          ⚠️ Failed Payments ({{ stats.failed_count || 8 }})
        </button>
      </div>
    </div>

  </div>
</template>
