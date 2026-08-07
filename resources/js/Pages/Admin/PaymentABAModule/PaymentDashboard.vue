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

// Filter & Pagination for Live Activity Feed
const searchQuery = ref('')
const statusFilter = ref('all')
const currentPage = ref(1)
const pageSize = ref(5)

// Interactive Time-Range Dropdown Filter for Chart
const timeRangeFilter = ref('30days')

const trendChartCategories = computed(() => {
  switch (timeRangeFilter.value) {
    case '7days':
      return ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
    case 'this_month':
      return ['Week 1', 'Week 2', 'Week 3', 'Week 4']
    case 'this_year':
      return ['Q1 (Jan-Mar)', 'Q2 (Apr-Jun)', 'Q3 (Jul-Sep)', 'Q4 (Oct-Dec)']
    case '30days':
    default:
      return ['Day 1', 'Day 5', 'Day 10', 'Day 15', 'Day 20', 'Day 25', 'Day 30']
  }
})

const trendChartSeriesData = computed(() => {
  switch (timeRangeFilter.value) {
    case '7days':
      return [210, 340, 520, 680, 890, 1150, 1420]
    case 'this_month':
      return [2400, 3100, 2900, 4050]
    case 'this_year':
      return [8500, 11200, 14500, 18200]
    case '30days':
    default:
      return [350, 480, 720, 1100, 950, 1400, 1800]
  }
})

const filteredActivityFeed = computed(() => {
  const feed = props.liveActivityFeed || []
  return feed.filter(act => {
    const studentName = (act.student || '').toLowerCase()
    const courseTitle = (act.course || '').toLowerCase()
    const method = (act.method || '').toLowerCase()
    const query = searchQuery.value.toLowerCase()

    const matchesSearch = studentName.includes(query) || courseTitle.includes(query) || method.includes(query)
    const matchesStatus = statusFilter.value === 'all' || act.status === statusFilter.value

    return matchesSearch && matchesStatus
  })
})

const totalPages = computed(() => Math.ceil(filteredActivityFeed.value.length / pageSize.value) || 1)

const paginatedActivityFeed = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value
  return filteredActivityFeed.value.slice(start, start + pageSize.value)
})

// ApexCharts Configuration for Revenue Trend (Area Line Chart - High Contrast #E2E8F0 Labels)
const trendChartOptions = computed<any>(() => ({
  chart: {
    type: 'area',
    toolbar: { show: false },
    fontFamily: 'Inter, sans-serif',
    background: 'transparent'
  },
  theme: { mode: 'dark' },
  colors: ['#818cf8'], // High-luminance Indigo
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.55,
      opacityTo: 0.1,
      stops: [0, 90, 100]
    }
  },
  stroke: { curve: 'smooth', width: 3 },
  xaxis: {
    categories: trendChartCategories.value,
    labels: { style: { colors: '#e2e8f0', fontSize: '12px', fontWeight: '700' } },
    axisBorder: { color: '#475569' },
    axisTicks: { color: '#475569' }
  },
  yaxis: {
    labels: {
      formatter: (val: number) => `$${val.toLocaleString()}`,
      style: { colors: '#e2e8f0', fontSize: '12px', fontWeight: '700' }
    }
  },
  grid: { borderColor: '#334155', strokeDashArray: 4 },
  tooltip: {
    theme: 'dark',
    style: { fontSize: '13px' },
    y: { formatter: (val: number) => `$${val.toLocaleString()} USD` }
  }
}))

const trendChartSeries = computed(() => [
  { name: 'Revenue ($)', data: trendChartSeriesData.value }
])

// Modern Donut Chart for Payment Method Distribution (Perfect Spacing & Monotone Legends)
const donutChartOptions = computed<any>(() => ({
  chart: { type: 'donut', fontFamily: 'Inter, sans-serif', background: 'transparent' },
  theme: { mode: 'dark' },
  labels: props.paymentMethodsDistribution.map(p => p.method),
  colors: ['#6366f1', '#10b981', '#f59e0b'],
  stroke: { show: true, colors: ['#1e293b'], width: 2 },
  plotOptions: {
    pie: {
      donut: {
        size: '65%',
        labels: {
          show: true,
          name: { show: true, color: '#e2e8f0', fontSize: '12px', fontWeight: '600' },
          value: { 
            show: true, 
            color: '#ffffff', 
            fontSize: '18px', 
            fontWeight: 'bold', 
            formatter: (val: number) => `$${Number(val).toLocaleString()}` 
          },
          total: { 
            show: true, 
            label: 'Total Revenue', 
            color: '#c7d2fe', 
            fontSize: '11px', 
            formatter: () => '$12,450' 
          }
        }
      }
    }
  },
  legend: { position: 'bottom', labels: { colors: '#f1f5f9' }, fontSize: '12px', fontWeight: '600' },
  dataLabels: { enabled: false }
}))

const donutChartSeries = computed(() => props.paymentMethodsDistribution.map(p => p.amount))

const formatMoney = (val: number) => `$${(val || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}`
</script>

<template>
  <div class="w-full space-y-6 text-slate-100 font-sans">
    
    <!-- ROW 1: KPI CARDS (4 CARDS WITH PERFECT EDGE-TO-EDGE FLUID GRID & HIGH CONTRAST 13-14PX SUBTITLES) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- 1. Today's Revenue -->
      <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 shadow-xl hover:border-slate-600 transition flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between text-slate-200 text-sm font-bold mb-2">
            <span class="flex items-center gap-1.5"><span class="text-base">💵</span> Today's Revenue</span>
            <span class="text-emerald-300 bg-emerald-950 px-2.5 py-0.5 rounded-md font-bold text-xs border border-emerald-700/60">↗️ +{{ stats.today_growth }}%</span>
          </div>
          <div class="text-3xl font-black text-white tracking-tight">{{ formatMoney(stats.today_revenue) }}</div>
        </div>
        <div class="text-sm text-slate-200 mt-2 font-medium">Real-time daily collection</div>
      </div>

      <!-- 2. This Month -->
      <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 shadow-xl hover:border-slate-600 transition flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between text-slate-200 text-sm font-bold mb-2">
            <span class="flex items-center gap-1.5"><span class="text-base">📅</span> This Month</span>
            <span class="text-emerald-300 bg-emerald-950 px-2.5 py-0.5 rounded-md font-bold text-xs border border-emerald-700/60">↗️ +{{ stats.month_growth }}%</span>
          </div>
          <div class="text-3xl font-black text-indigo-300 tracking-tight">{{ formatMoney(stats.month_revenue) }}</div>
        </div>
        <div class="text-sm text-slate-200 mt-2 font-medium">Monthly total revenue</div>
      </div>

      <!-- 3. Paying Students -->
      <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 shadow-xl hover:border-slate-600 transition flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between text-slate-200 text-sm font-bold mb-2">
            <span class="flex items-center gap-1.5"><span class="text-base">👥</span> Paying Students</span>
            <span class="text-indigo-200 bg-indigo-950 px-2.5 py-0.5 rounded-md font-bold text-xs border border-indigo-700/60">↗️ +{{ stats.new_paying_students }} new</span>
          </div>
          <div class="text-3xl font-black text-white tracking-tight">{{ stats.paying_students?.toLocaleString() }}</div>
        </div>
        <div class="text-sm text-slate-200 mt-2 font-medium">Enrolled paid students</div>
      </div>

      <!-- 4. Avg. Order Value -->
      <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 shadow-xl hover:border-slate-600 transition flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between text-slate-200 text-sm font-bold mb-2">
            <span class="flex items-center gap-1.5"><span class="text-base">🎯</span> Avg. Order Value</span>
            <span class="text-slate-200 bg-slate-900 px-2.5 py-0.5 rounded-md font-bold text-xs border border-slate-700">Stable</span>
          </div>
          <div class="text-3xl font-black text-white tracking-tight">{{ formatMoney(stats.avg_order_value) }}</div>
        </div>
        <div class="text-sm text-slate-200 mt-2 font-medium">Average transaction value</div>
      </div>
    </div>

    <!-- ROW 2: CHARTS (Revenue Trend Area Chart + Donut Chart - FLUID EXPANSION) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Revenue Trend (Area Chart with Real Interactive Dropdown Filter) -->
      <div class="lg:col-span-2 bg-slate-800/90 p-5 sm:p-6 rounded-2xl border border-slate-700/80 shadow-xl">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
          <div>
            <h3 class="text-base font-extrabold text-white flex items-center gap-2">
              <span>📈</span> REVENUE TREND
            </h3>
            <p class="text-sm text-slate-200 mt-0.5 font-medium">Revenue movement over selected timeframe</p>
          </div>
          
          <!-- Interactive Time Range Filter Dropdown -->
          <div class="relative">
            <select
              v-model="timeRangeFilter"
              class="bg-indigo-950/90 hover:bg-indigo-900 border border-indigo-700/80 text-indigo-100 font-bold text-xs rounded-xl px-3.5 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer shadow-md pr-8 transition"
            >
              <option value="7days">🗓️ Last 7 Days</option>
              <option value="30days">📅 Last 30 Days</option>
              <option value="this_month">📆 This Month</option>
              <option value="this_year">📊 This Year</option>
            </select>
          </div>
        </div>

        <div class="h-64">
          <VueApexCharts type="area" height="100%" :options="trendChartOptions" :series="trendChartSeries" />
        </div>
      </div>

      <!-- Payment Method (Modern Donut Chart - Fixed Spacing & No Cut-off) -->
      <div class="bg-slate-800/90 p-5 sm:p-6 rounded-2xl border border-slate-700/80 shadow-xl flex flex-col justify-between">
        <div>
          <h3 class="text-base font-extrabold text-white mb-1 flex items-center gap-2">
            <span>🍩</span> PAYMENT METHOD DISTRIBUTION
          </h3>
          <p class="text-sm text-slate-200 mb-2 font-medium">Breakdown by payment channels</p>
          <div class="h-52 overflow-hidden flex items-center justify-center">
            <VueApexCharts type="donut" height="100%" :options="donutChartOptions" :series="donutChartSeries" />
          </div>
        </div>

        <div class="space-y-2 mt-2 pt-3 border-t border-slate-700/60 text-xs">
          <div v-for="pm in paymentMethodsDistribution" :key="pm.method" class="flex items-center justify-between p-2.5 rounded-xl bg-slate-900/90 border border-slate-700/60">
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full shadow-sm" :style="{ backgroundColor: pm.color }"></span>
              <span class="font-bold text-slate-100 text-xs">{{ pm.method }}</span>
            </div>
            <span class="font-extrabold text-white font-mono text-xs">{{ pm.percentage }}% ({{ formatMoney(pm.amount) }})</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ROW 3: LIVE ACTIVITY FEED (REAL-TIME TIMELINE TABLE WITH SEARCH, FILTER & PAGINATION) -->
    <div class="bg-slate-800/90 rounded-2xl border border-slate-700/80 shadow-xl overflow-hidden space-y-3 p-5 sm:p-6">
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 border-b border-slate-700/80 pb-4">
        <div>
          <h3 class="text-base font-extrabold text-white flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
            LIVE ACTIVITY FEED (REAL-TIME)
          </h3>
          <p class="text-sm text-slate-200 mt-0.5 font-medium">Live stream of incoming student payment transactions</p>
        </div>

        <!-- Controls: Search with Magnifying Glass on Left & Clear X + Status Filter Pills -->
        <div class="flex flex-wrap items-center gap-2.5">
          <div class="relative w-48 sm:w-64">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none">🔍</span>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search student, course..."
              class="w-full bg-slate-950 border border-slate-700 rounded-xl pl-9 pr-8 py-2 text-xs text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500 transition-all"
            />
            <button
              v-if="searchQuery"
              @click="searchQuery = ''"
              class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white text-xs font-bold transition cursor-pointer"
            >
              ✕
            </button>
          </div>

          <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-xl border border-slate-700 text-xs">
            <button
              @click="statusFilter = 'all'"
              class="px-3 py-1.5 rounded-lg font-bold transition cursor-pointer"
              :class="statusFilter === 'all' ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:text-white'"
            >
              All
            </button>
            <button
              @click="statusFilter = 'Success'"
              class="px-3 py-1.5 rounded-lg font-bold transition cursor-pointer"
              :class="statusFilter === 'Success' ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:text-white'"
            >
              Success
            </button>
            <button
              @click="statusFilter = 'Processing'"
              class="px-3 py-1.5 rounded-lg font-bold transition cursor-pointer"
              :class="statusFilter === 'Processing' ? 'bg-amber-600 text-white' : 'text-slate-300 hover:text-white'"
            >
              Processing
            </button>
            <button
              @click="statusFilter = 'Failed'"
              class="px-3 py-1.5 rounded-lg font-bold transition cursor-pointer"
              :class="statusFilter === 'Failed' ? 'bg-rose-600 text-white' : 'text-slate-300 hover:text-white'"
            >
              Failed
            </button>
          </div>
        </div>
      </div>

      <!-- Live Activity Table (Clean Font Spacing, Align Right Amount, Center Payment Method) -->
      <div class="overflow-x-auto rounded-xl border border-slate-700/60">
        <table class="w-full text-left text-xs text-slate-200">
          <thead class="bg-slate-900/90 border-b border-slate-700 text-slate-200 uppercase font-bold text-xs">
            <tr>
              <th class="p-3.5">Time</th>
              <th class="p-3.5">Student</th>
              <th class="p-3.5">Course</th>
              <th class="p-3.5 text-right">Amount</th>
              <th class="p-3.5 text-center">Method</th>
              <th class="p-3.5 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60 bg-slate-900/40">
            <tr v-for="(act, idx) in paginatedActivityFeed" :key="idx" class="hover:bg-slate-700/50 transition">
              <td class="p-3.5 text-slate-300 font-sans font-medium text-xs">{{ act.time }}</td>
              <td class="p-3.5 font-bold text-white text-xs">{{ act.student }}</td>
              <td class="p-3.5 font-medium text-slate-100 text-xs">{{ act.course }}</td>
              <td class="p-3.5 text-right font-black text-emerald-400 font-mono text-xs">${{ act.amount.toFixed(2) }}</td>
              <td class="p-3.5 text-center">
                <span class="px-2.5 py-1 bg-indigo-950/80 text-indigo-200 font-bold text-xs rounded-lg border border-indigo-800/60 inline-flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-indigo-400"></span> {{ act.method }}
                </span>
              </td>
              <td class="p-3.5 text-center">
                <span 
                  class="px-3 py-1 rounded-full text-[11px] font-extrabold shadow-sm"
                  :class="{
                    'bg-emerald-950 text-emerald-300 border border-emerald-700/80': act.status === 'Success',
                    'bg-amber-950 text-amber-300 border border-amber-700/80': act.status === 'Processing',
                    'bg-rose-950 text-rose-300 border border-rose-700/80': act.status === 'Failed',
                  }"
                >
                  {{ act.status === 'Success' ? '✅ Success' : (act.status === 'Processing' ? '⏳ Processing' : '❌ Failed') }}
                </span>
              </td>
            </tr>
            <tr v-if="paginatedActivityFeed.length === 0">
              <td colspan="6" class="p-6 text-center text-slate-300 font-medium text-xs">
                No payment activity matching search criteria.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Controls for Live Activity Feed (Disabled State when Page 1 of 1) -->
      <div class="flex items-center justify-between text-xs text-slate-200 pt-2 px-1">
        <span class="font-sans font-medium text-slate-300">Showing {{ paginatedActivityFeed.length }} of {{ filteredActivityFeed.length }} transactions</span>
        <div class="flex items-center gap-2">
          <button
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="px-3.5 py-1.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-slate-700 transition cursor-pointer font-bold text-xs"
          >
            ◀ Prev
          </button>
          <span class="font-bold text-indigo-300 font-mono text-xs">Page {{ currentPage }} of {{ totalPages }}</span>
          <button
            @click="currentPage++"
            :disabled="currentPage >= totalPages"
            class="px-3.5 py-1.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-slate-700 transition cursor-pointer font-bold text-xs"
          >
            Next ▶
          </button>
        </div>
      </div>
    </div>

    <!-- ROW 4: FINANCIAL QUICK ACTIONS (PRIMARY & SECONDARY HIERARCHY + CONSISTENT BORDER RADIUS) -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 text-white p-5 rounded-2xl shadow-xl flex flex-col md:flex-row items-start md:items-center justify-start gap-4 border border-indigo-900/60">
      <div class="text-xs font-bold text-slate-100 flex items-center gap-2 shrink-0 pr-4 md:border-r md:border-slate-800">
        <span class="p-1.5 bg-indigo-600 text-white rounded-xl shadow-md">⚡</span>
        <span class="text-xs font-extrabold tracking-wide uppercase">Financial Quick Actions</span>
      </div>

      <!-- Quick Action Buttons with Consistent Rounded-XL Corners & Clear Visual Hierarchy -->
      <div class="flex flex-wrap items-center justify-start gap-2.5 flex-1">
        <!-- Primary Action -->
        <button @click="$emit('switch-tab', 'transactions')" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition cursor-pointer flex items-center gap-2">
          <span>🧾</span> View All Transactions
        </button>

        <!-- Secondary Action: Set Pricing -->
        <button @click="$emit('open-pricing')" class="px-4 py-2.5 bg-slate-800/90 hover:bg-slate-700 text-slate-100 font-bold text-xs rounded-xl transition border border-slate-700/80 cursor-pointer flex items-center gap-2">
          <span>💰</span> Set Pricing
        </button>

        <!-- Secondary Action: Export Report -->
        <button @click="$emit('switch-tab', 'revenue')" class="px-4 py-2.5 bg-slate-800/90 hover:bg-slate-700 text-slate-100 font-bold text-xs rounded-xl transition border border-slate-700/80 cursor-pointer flex items-center gap-2">
          <span>📤</span> Export Report
        </button>

        <!-- Secondary Alert Action: Failed Payments -->
        <button @click="$emit('switch-tab', 'transactions')" class="px-4 py-2.5 bg-slate-800/90 hover:bg-slate-700 text-slate-100 font-bold text-xs rounded-xl transition border border-rose-700/60 cursor-pointer flex items-center gap-2">
          <span class="text-rose-400">⚠️</span> Failed Payments
          <span class="px-2 py-0.5 rounded-md bg-rose-950 text-rose-300 text-[11px] font-extrabold border border-rose-800/80">{{ stats.failed_count || 8 }}</span>
        </button>
      </div>
    </div>

  </div>
</template>
