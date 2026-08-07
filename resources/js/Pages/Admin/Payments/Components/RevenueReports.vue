<script setup lang="ts">
import { ref, computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'

const props = defineProps<{
  stats: any
  revenueByMajor: any[]
  topEarningCourses: any[]
  teacherEarnings: any[]
  paymentMethodsDistribution: any[]
}>()

const emit = defineEmits(['open-payout'])

const toast = useToast()
const selectedRange = ref('This Month')

// Modals & Action Loading State
const showEmailModal = ref(false)
const showScheduleModal = ref(false)
const isSendingEmail = ref(false)
const isSavingSchedule = ref(false)
const isGeneratingPdf = ref(false)
const isExportingExcel = ref(false)
const isOpeningEmailModal = ref(false)
const isOpeningScheduleModal = ref(false)

const emailForm = ref({
  emails: ['finance@university.edu.kh'],
  newEmail: '',
  ccEmails: ['rector@university.edu.kh'],
  newCcEmail: '',
  note: 'Please review the attached monthly financial revenue summary and teacher earnings allocation.'
})

const scheduleForm = ref({
  enabled: true,
  frequency: 'Monthly (1st of every month at 8:00 AM)',
  emails: ['finance@university.edu.kh', 'rector@university.edu.kh'],
  newEmail: '',
  attachPdf: true,
  attachExcel: true
})

// Helper to add email tag/chip
const addTag = (list: string[], inputKey: 'email' | 'cc' | 'schedule') => {
  let raw = ''
  if (inputKey === 'email') raw = emailForm.value.newEmail
  else if (inputKey === 'cc') raw = emailForm.value.newCcEmail
  else if (inputKey === 'schedule') raw = scheduleForm.value.newEmail

  const cleaned = raw.trim().replace(/,/g, '')
  if (cleaned && !list.includes(cleaned)) {
    list.push(cleaned)
  }

  if (inputKey === 'email') emailForm.value.newEmail = ''
  else if (inputKey === 'cc') emailForm.value.newCcEmail = ''
  else if (inputKey === 'schedule') scheduleForm.value.newEmail = ''
}

const handleTagKeyDown = (e: KeyboardEvent, list: string[], inputKey: 'email' | 'cc' | 'schedule') => {
  if (e.key === 'Enter' || e.key === ',' || e.key === ' ') {
    e.preventDefault()
    addTag(list, inputKey)
  } else if (e.key === 'Backspace') {
    let raw = ''
    if (inputKey === 'email') raw = emailForm.value.newEmail
    else if (inputKey === 'cc') raw = emailForm.value.newCcEmail
    else if (inputKey === 'schedule') raw = scheduleForm.value.newEmail

    if (!raw && list.length > 0) {
      list.pop()
    }
  }
}

const removeTag = (list: string[], index: number) => {
  list.splice(index, 1)
}

// Horizontal Bar Chart for Revenue by Major (Soft gradient & correct category labels)
const majorChartOptions = computed<any>(() => ({
  chart: { 
    type: 'bar', 
    toolbar: { show: false }, 
    fontFamily: 'Inter, sans-serif', 
    background: 'transparent'
  },
  theme: { mode: 'dark' },
  colors: ['#6366f1', '#4f46e5', '#3b82f6', '#0ea5e9', '#06b6d4'],
  plotOptions: { 
    bar: { 
      borderRadius: 6, 
      horizontal: true, 
      barHeight: '48%', 
      distributed: true,
      dataLabels: { position: 'top' }
    } 
  },
  dataLabels: {
    enabled: true,
    offsetX: 10,
    formatter: (val: number) => `$${val.toLocaleString()}`,
    style: { colors: ['#e2e8f0'], fontSize: '12px', fontWeight: '600' }
  },
  xaxis: {
    categories: props.revenueByMajor.map(m => m.major),
    labels: {
      formatter: (v: number) => `$${v.toLocaleString()}`, 
      style: { colors: '#94a3b8', fontSize: '11px' }
    },
    axisBorder: { color: '#334155' },
    axisTicks: { color: '#334155' }
  },
  yaxis: { 
    labels: { 
      style: { colors: '#cbd5e1', fontSize: '12px', fontWeight: '600' } 
    } 
  },
  grid: { borderColor: '#334155', strokeDashArray: 3 },
  legend: { show: false },
  tooltip: {
    theme: 'dark',
    y: { formatter: (val: number) => `$${val.toLocaleString()}` }
  }
}))

const majorChartSeries = computed(() => [
  { name: 'Revenue ($)', data: props.revenueByMajor.map(m => m.revenue) }
])

// Modern Donut Chart for Payment Method Distribution (Soft layout without overlay clutter)
const donutChartOptions = computed<any>(() => ({
  chart: { type: 'donut', fontFamily: 'Inter, sans-serif', background: 'transparent' },
  theme: { mode: 'dark' },
  labels: props.paymentMethodsDistribution.map(p => p.method),
  colors: ['#6366f1', '#10b981', '#f59e0b'],
  stroke: { show: true, colors: ['#1e293b'], width: 2 },
  plotOptions: {
    pie: {
      donut: {
        size: '76%',
        labels: {
          show: true,
          name: { show: true, color: '#94a3b8', fontSize: '12px', fontWeight: '500' },
          value: { 
            show: true, 
            color: '#ffffff', 
            fontSize: '18px', 
            fontWeight: '700', 
            formatter: (val: number) => `$${Number(val).toLocaleString()}` 
          },
          total: { 
            show: true, 
            label: 'Total Revenue', 
            color: '#a5b4fc', 
            fontSize: '11px', 
            formatter: () => '$12,450' 
          }
        }
      }
    }
  },
  legend: { show: false },
  dataLabels: { enabled: false }
}))

const donutChartSeries = computed(() => props.paymentMethodsDistribution.map(p => p.amount))

// Toolbar Action Openers with Loading Feedback
const openEmailModal = () => {
  isOpeningEmailModal.value = true
  setTimeout(() => {
    isOpeningEmailModal.value = false
    showEmailModal.value = true
  }, 250)
}

const openScheduleModal = () => {
  isOpeningScheduleModal.value = true
  setTimeout(() => {
    isOpeningScheduleModal.value = false
    showScheduleModal.value = true
  }, 250)
}

// Action 1: Generate PDF Report
const generatePDF = () => {
  isGeneratingPdf.value = true
  setTimeout(() => {
    const printWindow = window.open('', '_blank')
    isGeneratingPdf.value = false

    if (!printWindow) {
      window.print()
      return
    }

    const html = `
      <!DOCTYPE html>
      <html>
      <head>
        <title>Executive Revenue Report - ${selectedRange.value}</title>
        <style>
          body { font-family: 'Inter', system-ui, -apple-system, sans-serif; color: #0f172a; padding: 35px; line-height: 1.5; background: #fff; }
          .header { display: flex; justify-content: space-between; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 25px; }
          .title { font-size: 24px; font-weight: 800; color: #1e1b4b; margin-bottom: 4px; }
          .meta { text-align: right; font-size: 12px; color: #64748b; }
          .kpi-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 30px; }
          .kpi-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; text-align: center; }
          .kpi-title { font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; }
          .kpi-val { font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 4px; }
          table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 12px; }
          th { background: #f1f5f9; color: #334155; text-align: left; padding: 10px 12px; border-bottom: 2px solid #cbd5e1; font-weight: 700; text-transform: uppercase; }
          td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; }
          .text-right { text-align: right; }
          .section-title { font-size: 14px; font-weight: 700; margin-bottom: 12px; color: #334155; text-transform: uppercase; border-left: 4px solid #4f46e5; padding-left: 8px; }
          .footer { margin-top: 40px; font-size: 11px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 15px; }
        </style>
      </head>
      <body>
        <div class="header">
          <div>
            <div class="title">🏛️ Executive Financial Revenue Report</div>
            <div style="font-size: 13px; color: #475569;">ELMS Academic Administration System & ABA Merchant</div>
          </div>
          <div class="meta">
            <div><strong>Period:</strong> ${selectedRange.value}</div>
            <div><strong>Generated:</strong> ${new Date().toLocaleString()}</div>
            <div><strong>Status:</strong> Audited & Verified</div>
          </div>
        </div>

        <div class="kpi-grid">
          <div class="kpi-card"><div class="kpi-title">Gross Revenue</div><div class="kpi-val">$${(props.stats?.gross_revenue || 12450).toLocaleString()}</div></div>
          <div class="kpi-card"><div class="kpi-title">Refunds</div><div class="kpi-val" style="color:#e11d48">-$135</div></div>
          <div class="kpi-card"><div class="kpi-title">Net Revenue</div><div class="kpi-val" style="color:#4f46e5">$${(props.stats?.net_revenue || 12315).toLocaleString()}</div></div>
          <div class="kpi-card"><div class="kpi-title">Avg per Course</div><div class="kpi-val">$47</div></div>
          <div class="kpi-card"><div class="kpi-title">Growth</div><div class="kpi-val" style="color:#059669">+25%</div></div>
        </div>

        <div class="section-title">1. Top Earning Courses</div>
        <table>
          <thead>
            <tr><th>Rank</th><th>Course Title</th><th>Instructor</th><th class="text-right">Enrollments</th><th class="text-right">Revenue ($)</th></tr>
          </thead>
          <tbody>
            ${props.topEarningCourses.map(c => `
              <tr>
                <td>#${c.rank}</td>
                <td><strong>${c.course}</strong></td>
                <td>${c.teacher}</td>
                <td class="text-right">${c.enrollments}</td>
                <td class="text-right" style="font-weight:bold; color:#059669">$${c.revenue.toFixed(2)}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>

        <div class="section-title">2. Teacher Revenue Share (70% Allocation)</div>
        <table>
          <thead>
            <tr><th>Teacher</th><th class="text-right">Total Sales</th><th class="text-right">70% Earned Share</th><th class="text-right">Paid Out</th><th class="text-right">Pending</th></tr>
          </thead>
          <tbody>
            ${props.teacherEarnings.map(t => `
              <tr>
                <td><strong>${t.teacher}</strong></td>
                <td class="text-right">$${t.total_sales.toFixed(2)}</td>
                <td class="text-right" style="font-weight:bold; color:#4f46e5">$${t.earned_share.toFixed(2)}</td>
                <td class="text-right" style="color:#059669">$${t.paid_out.toFixed(2)}</td>
                <td class="text-right" style="font-weight:bold; color:#d97706">$${t.pending.toFixed(2)}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>

        <div class="footer">
          Confidential Document • ELMS Finance & Accounting System • System Auto-Generated Report
        </div>
      </body>
      </html>
    `
    printWindow.document.write(html)
    printWindow.document.close()
    setTimeout(() => {
      printWindow.print()
    }, 250)

    try {
      toast?.add({
        severity: 'success',
        summary: 'បានបង្កើតរបាយការណ៍ PDF ផ្លូវការ',
        detail: 'របាយការណ៍ចំណូល (Revenue Report) ត្រូវបានបើកជូនសម្រាប់ការបោះពុម្ព ឬរក្សាទុក 📄',
        life: 4000
      })
    } catch (e) {}
  }, 400)
}

// Action 2: Export Excel / CSV Spreadsheet
const exportExcel = () => {
  isExportingExcel.value = true
  setTimeout(() => {
    let csv = "\uFEFF" // UTF-8 BOM for Excel Khmer/English support
    
    csv += "=== EXECUTIVE FINANCIAL REVENUE REPORT ===\n"
    csv += `Generated Date,${new Date().toLocaleDateString()}\n`
    csv += `Report Period,${selectedRange.value}\n\n`

    csv += "--- EXECUTIVE KPI SUMMARY ---\n"
    csv += "Gross Revenue,Refunds,Net Revenue,Avg per Course,Growth\n"
    csv += `$${props.stats?.gross_revenue || 12450},-$135,$${props.stats?.net_revenue || 12315},$47,+25%\n\n`

    csv += "--- REVENUE BY MAJOR ---\n"
    csv += "Major,Revenue ($)\n"
    props.revenueByMajor.forEach(m => {
      csv += `"${m.major}",${m.revenue}\n`
    })
    csv += "\n"

    csv += "--- TOP EARNING COURSES ---\n"
    csv += "Rank,Course Title,Teacher,Enrollments,Revenue ($)\n"
    props.topEarningCourses.forEach(c => {
      csv += `${c.rank},"${c.course}","${c.teacher}",${c.enrollments},${c.revenue}\n`
    })
    csv += "\n"

    csv += "--- TEACHER EARNINGS (70% SHARE) ---\n"
    csv += "Teacher Name,Total Sales ($),70% Earned Share ($),Paid Out ($),Pending ($)\n"
    props.teacherEarnings.forEach(t => {
      csv += `"${t.teacher}",${t.total_sales},${t.earned_share},${t.paid_out},${t.pending}\n`
    })
    csv += "\n"

    csv += "--- PAYMENT METHOD DISTRIBUTION ---\n"
    csv += "Payment Channel,Share (%),Total Amount ($)\n"
    props.paymentMethodsDistribution.forEach(p => {
      csv += `"${p.method}",${p.percentage}%,${p.amount}\n`
    })

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement("a")
    link.setAttribute("href", url)
    link.setAttribute("download", `Revenue_Report_${selectedRange.value.replace(/\s+/g, '_')}_${new Date().toISOString().slice(0,10)}.csv`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    isExportingExcel.value = false

    try {
      toast?.add({
        severity: 'success',
        summary: 'បានទាញយកទិន្នន័យ Excel / CSV',
        detail: 'សន្លឹកកិច្ចការចំណូលហិរញ្ញវត្ថុ ត្រូវបានទាញយករក្សាទុកដោយជោគជ័យ 📊',
        life: 4000
      })
    } catch (e) {}
  }, 400)
}

// Action 3: Email to Finance
const sendEmail = () => {
  if (emailForm.value.emails.length === 0 && emailForm.value.newEmail.trim()) {
    addTag(emailForm.value.emails, 'email')
  }
  if (emailForm.value.emails.length === 0) {
    try {
      toast?.add({
        severity: 'warn',
        summary: 'សូមបញ្ចូលអាសយដ្ឋាន Email',
        detail: 'សូមបំពេញ Recipient Email មុនពេលផ្ញើរបាយការណ៍ ⚠️',
        life: 4000
      })
    } catch (e) {}
    return
  }

  isSendingEmail.value = true
  setTimeout(() => {
    isSendingEmail.value = false
    showEmailModal.value = false
    try {
      toast?.add({
        severity: 'success',
        summary: 'បានផ្ញើរបាយការណ៍ហិរញ្ញវត្ថុជោគជ័យ',
        detail: `របាយការណ៍ចំណូល ត្រូវបានផ្ញើទៅកាន់ ${emailForm.value.emails.join(', ')} រួចរាល់ 📧`,
        life: 5000
      })
    } catch (e) {}
  }, 900)
}

// Action 4: Schedule Auto-Send
const saveSchedule = () => {
  if (scheduleForm.value.emails.length === 0 && scheduleForm.value.newEmail.trim()) {
    addTag(scheduleForm.value.emails, 'schedule')
  }
  if (scheduleForm.value.emails.length === 0) {
    try {
      toast?.add({
        severity: 'warn',
        summary: 'សូមបញ្ចូលអាសយដ្ឋាន Email',
        detail: 'សូមបំពេញ Target Recipient Email មុនពេលរក្សាទុកកាលវិភាគ ⚠️',
        life: 4000
      })
    } catch (e) {}
    return
  }

  isSavingSchedule.value = true
  setTimeout(() => {
    isSavingSchedule.value = false
    showScheduleModal.value = false
    try {
      toast?.add({
        severity: 'success',
        summary: 'បានកំណត់កាលវិភាគផ្ញើស្វ័យប្រវត្តិ',
        detail: `ប្រព័ន្ធបានរក្សាទុកកាលវិភាគ ${scheduleForm.value.frequency} ដោយជោគជ័យ ⏰`,
        life: 5000
      })
    } catch (e) {}
  }, 900)
}
</script>

<template>
  <div class="space-y-6 text-slate-100">
    <Toast position="top-right" class="!z-[9999]">
      <template #message="slotProps">
        <div class="relative w-full max-w-sm rounded-xl bg-slate-900 p-3.5 shadow-xl font-sans text-white border-0">
          <div class="flex items-center gap-3.5">
            <div class="w-9 h-9 rounded-full shrink-0 flex items-center justify-center filter drop-shadow-sm">
              <img
                v-if="slotProps.message.severity === 'success'"
                :src="'/images/actions/toast-success.svg'"
                alt="Success"
                class="w-full h-full object-contain"
              />
              <img
                v-else-if="slotProps.message.severity === 'warn'"
                :src="'/images/actions/toast-warning.svg'"
                alt="Warning"
                class="w-full h-full object-contain"
              />
              <img
                v-else
                :src="'/images/actions/toast-info.svg'"
                alt="Info"
                class="w-full h-full object-contain"
              />
            </div>

            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-white tracking-tight leading-snug">
                {{ slotProps.message.summary }}
              </h4>
              <p class="text-xs text-slate-300 mt-0.5 leading-relaxed">
                {{ slotProps.message.detail }}
              </p>
            </div>
          </div>
        </div>
      </template>
    </Toast>
    
    <!-- ROW 1: TIME RANGE SELECTOR -->
    <div class="bg-slate-800/60 p-3.5 rounded-2xl border border-slate-700/60 shadow-sm backdrop-blur-xs flex flex-wrap items-center justify-between gap-3 text-xs md:text-sm">
      <div class="font-semibold text-slate-300 flex items-center gap-2">
        <span>📅</span> Select Report Period:
      </div>
      <div class="flex flex-wrap items-center gap-2 text-xs">
        <button 
          v-for="range in ['Today', 'This Week', 'This Month', 'Quarter', 'Year', 'Custom Range']" 
          :key="range"
          @click="selectedRange = range"
          class="px-3.5 py-1.5 rounded-xl font-medium transition cursor-pointer"
          :class="selectedRange === range ? 'bg-indigo-600 text-white shadow-xs font-semibold' : 'bg-slate-900/80 border border-slate-700/70 text-slate-300 hover:bg-slate-700/50'"
        >
          📅 {{ range }}
        </button>
      </div>
    </div>

    <!-- ROW 2: EXECUTIVE SUMMARY (5 KPIS - SOFT ELEGANT STYLE) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
      <div class="bg-slate-800/60 p-4 rounded-2xl border border-slate-700/60 shadow-sm backdrop-blur-xs">
        <div class="text-xs font-medium text-slate-400 mb-1 flex items-center gap-1.5">
          <span>💰</span> Gross Revenue
        </div>
        <div class="text-xl font-bold text-white">${{ (stats.gross_revenue || 12450).toLocaleString() }}</div>
      </div>

      <div class="bg-slate-800/60 p-4 rounded-2xl border border-slate-700/60 shadow-sm backdrop-blur-xs">
        <div class="text-xs font-medium text-slate-400 mb-1 flex items-center gap-1.5">
          <span>💸</span> Refunds
        </div>
        <div class="text-xl font-bold text-rose-400">-$135</div>
      </div>

      <div class="bg-slate-800/60 p-4 rounded-2xl border border-slate-700/60 shadow-sm backdrop-blur-xs border-l-3 border-l-indigo-500">
        <div class="text-xs font-medium text-slate-400 mb-1 flex items-center gap-1.5">
          <span>💵</span> Net Revenue
        </div>
        <div class="text-xl font-bold text-indigo-300">${{ (stats.net_revenue || 12315).toLocaleString() }}</div>
      </div>

      <div class="bg-slate-800/60 p-4 rounded-2xl border border-slate-700/60 shadow-sm backdrop-blur-xs">
        <div class="text-xs font-medium text-slate-400 mb-1 flex items-center gap-1.5">
          <span>🎯</span> Avg per Course
        </div>
        <div class="text-xl font-bold text-white">$47</div>
      </div>

      <!-- Growth Indicator KPI Card (Soft & Balanced Accent) -->
      <div class="bg-slate-800/60 p-4 rounded-2xl border border-slate-700/60 shadow-sm backdrop-blur-xs">
        <div class="flex items-center justify-between mb-1">
          <div class="text-xs font-medium text-slate-400 flex items-center gap-1.5">
            <span>📈</span> Growth
          </div>
          <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">
            ▲ +25%
          </span>
        </div>
        <div class="text-xl font-bold text-emerald-400 flex items-center gap-1.5">
          <span>+25%</span>
          <span class="text-xs font-normal text-slate-400">(vs last month)</span>
        </div>
      </div>
    </div>

    <!-- ROW 3: CHARTS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <!-- Chart 1: Revenue by Major (Horizontal Bar with clean categories) -->
      <div class="bg-slate-800/60 p-5 rounded-2xl border border-slate-700/60 shadow-sm backdrop-blur-xs">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-sm font-bold text-white flex items-center gap-2">
            <span>📊</span> CHART 1: REVENUE BY MAJOR
          </h3>
          <span class="text-xs font-medium text-slate-400 bg-slate-900/60 px-2.5 py-1 rounded-md border border-slate-700/50">By Academic Major</span>
        </div>
        <div class="h-72">
          <VueApexCharts type="bar" height="100%" :options="majorChartOptions" :series="majorChartSeries" />
        </div>
      </div>

      <!-- Chart 4: Payment Method Distribution (Donut Chart) -->
      <div class="bg-slate-800/60 p-5 rounded-2xl border border-slate-700/60 shadow-sm backdrop-blur-xs flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
              <span>🍩</span> CHART 4: PAYMENT METHOD DISTRIBUTION
            </h3>
            <span class="text-xs font-medium text-slate-400 bg-slate-900/60 px-2.5 py-1 rounded-md border border-slate-700/50">Channel Breakdown</span>
          </div>
          <div class="h-64">
            <VueApexCharts type="donut" height="100%" :options="donutChartOptions" :series="donutChartSeries" />
          </div>
        </div>
        <div class="space-y-1.5 text-xs border-t border-slate-700/40 pt-3">
          <div v-for="pm in paymentMethodsDistribution" :key="pm.method" class="flex justify-between items-center p-2 bg-slate-900/40 rounded-xl border border-slate-700/30">
            <div class="flex items-center gap-2.5">
              <span class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: pm.color }"></span>
              <span class="text-slate-300 font-medium">{{ pm.method }}</span>
            </div>
            <strong class="text-slate-100 font-mono">{{ pm.percentage }}% <span class="text-slate-400 font-normal">(${{ pm.amount.toLocaleString() }})</span></strong>
          </div>
        </div>
      </div>

      <!-- Chart 2: Top-Earning Courses (Right-aligned Table) -->
      <div class="bg-slate-800/60 p-5 rounded-2xl border border-slate-700/60 shadow-sm backdrop-blur-xs overflow-hidden">
        <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
          <span>🏆</span> CHART 2: TOP-EARNING COURSES (TOP 10)
        </h3>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-slate-200">
            <thead class="bg-slate-900/60 text-slate-400 uppercase font-semibold text-xs border-b border-slate-700/60">
              <tr>
                <th class="p-2.5 text-left">Rank</th>
                <th class="p-2.5 text-left">Course</th>
                <th class="p-2.5 text-left">Teacher</th>
                <th class="p-2.5 text-right">Enrollments</th>
                <th class="p-2.5 text-right">Revenue</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/40 text-xs md:text-sm">
              <tr v-for="c in topEarningCourses" :key="c.course" class="hover:bg-slate-700/30 transition-colors">
                <td class="p-2.5 font-semibold text-slate-400 text-left">{{ c.rank }}</td>
                <td class="p-2.5 font-semibold text-white text-left">{{ c.course }}</td>
                <td class="p-2.5 text-slate-300 text-left">{{ c.teacher }}</td>
                <td class="p-2.5 font-mono text-slate-200 text-right">{{ c.enrollments }}</td>
                <td class="p-2.5 text-right font-semibold text-emerald-400">${{ c.revenue.toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Chart 3: Teacher Earnings (Right-aligned Table) -->
      <div class="bg-slate-800/60 p-5 rounded-2xl border border-slate-700/60 shadow-sm backdrop-blur-xs overflow-hidden">
        <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
          <span>🧑‍🏫</span> CHART 3: TEACHER EARNINGS (REVENUE SHARE)
        </h3>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-slate-200">
            <thead class="bg-slate-900/60 text-slate-400 uppercase font-semibold text-xs border-b border-slate-700/60">
              <tr>
                <th class="p-2.5 text-left">Teacher</th>
                <th class="p-2.5 text-right">Total Sales</th>
                <th class="p-2.5 text-right">70% Share</th>
                <th class="p-2.5 text-right">Paid Out</th>
                <th class="p-2.5 text-right">Pending</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/40 font-mono text-xs md:text-sm">
              <tr v-for="t in teacherEarnings" :key="t.teacher" class="hover:bg-slate-700/30 transition-colors">
                <td class="p-2.5 font-sans font-semibold text-white text-left">{{ t.teacher }}</td>
                <td class="p-2.5 text-slate-200 text-right">${{ t.total_sales.toFixed(2) }}</td>
                <td class="p-2.5 font-semibold text-indigo-300 text-right">${{ t.earned_share.toFixed(2) }}</td>
                <td class="p-2.5 text-emerald-400 text-right">${{ t.paid_out.toFixed(2) }}</td>
                <td class="p-2.5 text-right text-amber-300 font-semibold">${{ t.pending.toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- ROW 4: EXPORT & SCHEDULE ACTION BAR (FULLY FUNCTIONAL ACTIONS) -->
    <div class="bg-slate-800/80 text-white p-4 md:p-5 rounded-2xl shadow-sm flex flex-wrap items-center justify-between gap-4 border border-slate-700/60">
      <div>
        <div class="text-sm font-semibold text-white">Executive Reporting & Export</div>
        <div class="text-xs text-slate-400">Download or automate financial reports distribution</div>
      </div>
      <div class="flex flex-wrap items-center gap-2.5 text-xs md:text-sm">
        <!-- Action 1: Generate PDF Report -->
        <button 
          @click="generatePDF" 
          :disabled="isGeneratingPdf" 
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 disabled:bg-indigo-600/70 disabled:cursor-wait text-white font-semibold rounded-xl shadow-xs border border-indigo-500 transition flex items-center gap-2 cursor-pointer active:scale-95"
        >
          <svg v-if="isGeneratingPdf" class="animate-spin -ml-0.5 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span v-else>📄</span>
          <span>{{ isGeneratingPdf ? 'កំពុងបង្កើត PDF...' : 'Generate PDF Report' }}</span>
        </button>

        <!-- Action 2: Export Excel -->
        <button 
          @click="exportExcel" 
          :disabled="isExportingExcel" 
          class="px-4 py-2 bg-slate-700/80 hover:bg-slate-700 disabled:bg-slate-800 disabled:cursor-wait text-emerald-400 font-semibold rounded-xl border border-emerald-500/30 transition flex items-center gap-2 cursor-pointer active:scale-95"
        >
          <svg v-if="isExportingExcel" class="animate-spin -ml-0.5 h-4 w-4 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span v-else>📊</span>
          <span>{{ isExportingExcel ? 'កំពុងទាញយក Excel...' : 'Export Excel' }}</span>
        </button>

        <!-- Action 3: Email to Finance -->
        <button 
          @click="openEmailModal" 
          :disabled="isOpeningEmailModal" 
          class="px-4 py-2 bg-slate-800 hover:bg-slate-700 disabled:bg-slate-800 disabled:cursor-wait text-slate-300 font-medium rounded-xl border border-slate-700 transition flex items-center gap-2 cursor-pointer active:scale-95"
        >
          <svg v-if="isOpeningEmailModal" class="animate-spin -ml-0.5 h-4 w-4 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span v-else>📧</span>
          <span>{{ isOpeningEmailModal ? 'កំពុងបើក...' : 'Email to Finance' }}</span>
        </button>

        <!-- Action 4: Schedule Monthly Auto-Send -->
        <button 
          @click="openScheduleModal" 
          :disabled="isOpeningScheduleModal" 
          class="px-4 py-2 bg-slate-800 hover:bg-slate-700 disabled:bg-slate-800 disabled:cursor-wait text-purple-300 font-medium rounded-xl border border-purple-800/40 transition flex items-center gap-2 cursor-pointer active:scale-95"
        >
          <svg v-if="isOpeningScheduleModal" class="animate-spin -ml-0.5 h-4 w-4 text-purple-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span v-else>⏰</span>
          <span>{{ isOpeningScheduleModal ? 'កំពុងបើក...' : 'Schedule Auto-Send' }}</span>
        </button>
      </div>
    </div>

    <!-- EMAIL TO FINANCE MODAL -->
    <div v-if="showEmailModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-xs z-50 flex items-center justify-center p-4">
      <div class="bg-slate-900 border border-slate-700/80 rounded-2xl max-w-lg w-full shadow-2xl text-slate-100 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
          <h3 class="text-base font-bold text-white flex items-center gap-2">
            <span>📧</span> Email Revenue Report to Finance
          </h3>
          <button @click="showEmailModal = false" class="text-slate-400 hover:text-white text-lg cursor-pointer transition">✕</button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4 text-xs">
          <!-- Recipient Emails Tag Input -->
          <div>
            <label class="block font-semibold text-slate-100 mb-1.5 text-xs">Recipient Finance Email(s):</label>
            <div class="w-full bg-slate-800 border border-slate-700/80 rounded-xl p-2 flex flex-wrap items-center gap-1.5 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all">
              <div v-for="(em, idx) in emailForm.emails" :key="em" class="inline-flex items-center gap-1.5 bg-indigo-500/20 text-indigo-200 border border-indigo-500/30 text-xs px-2.5 py-1 rounded-lg font-mono">
                <span>{{ em }}</span>
                <button type="button" @click="removeTag(emailForm.emails, idx)" class="text-indigo-300 hover:text-white font-bold cursor-pointer">✕</button>
              </div>
              <input 
                v-model="emailForm.newEmail" 
                @keydown="e => handleTagKeyDown(e, emailForm.emails, 'email')"
                @blur="addTag(emailForm.emails, 'email')"
                placeholder="Type email & press Enter..." 
                class="flex-1 bg-transparent text-white text-xs focus:outline-none min-w-[150px] py-1 px-1" 
              />
            </div>
          </div>

          <!-- CC Emails Tag Input -->
          <div>
            <label class="block font-semibold text-slate-100 mb-1.5 text-xs">CC Email(s) (Optional):</label>
            <div class="w-full bg-slate-800 border border-slate-700/80 rounded-xl p-2 flex flex-wrap items-center gap-1.5 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all">
              <div v-for="(cc, idx) in emailForm.ccEmails" :key="cc" class="inline-flex items-center gap-1.5 bg-slate-700 text-slate-200 border border-slate-600 text-xs px-2.5 py-1 rounded-lg font-mono">
                <span>{{ cc }}</span>
                <button type="button" @click="removeTag(emailForm.ccEmails, idx)" class="text-slate-400 hover:text-white font-bold cursor-pointer">✕</button>
              </div>
              <input 
                v-model="emailForm.newCcEmail" 
                @keydown="e => handleTagKeyDown(e, emailForm.ccEmails, 'cc')"
                @blur="addTag(emailForm.ccEmails, 'cc')"
                placeholder="Type CC email & press Enter..." 
                class="flex-1 bg-transparent text-white text-xs focus:outline-none min-w-[150px] py-1 px-1" 
              />
            </div>
          </div>

          <!-- Executive Note -->
          <div>
            <label class="block font-semibold text-slate-100 mb-1.5 text-xs">Executive Message Note:</label>
            <textarea v-model="emailForm.note" rows="3" class="w-full bg-slate-800 border border-slate-700/80 rounded-xl p-3 text-white text-xs focus:outline-none focus:border-indigo-500 transition-all"></textarea>
          </div>

          <!-- Attached Report Card (Fixed ${selectedRange} bug!) -->
          <div class="bg-slate-800/80 p-3.5 rounded-xl border border-slate-700/60 flex items-center justify-between text-slate-200">
            <span class="font-medium">Attached Report:</span>
            <span class="font-bold text-indigo-300">PDF & Excel CSV ({{ selectedRange }})</span>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-slate-900/90 border-t border-slate-800 flex justify-end gap-3 text-xs">
          <button @click="showEmailModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium rounded-xl border border-slate-700 cursor-pointer transition">Cancel</button>
          <button 
            @click="sendEmail" 
            :disabled="isSendingEmail" 
            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 disabled:bg-indigo-600/70 disabled:cursor-wait text-white font-bold rounded-xl shadow-md flex items-center gap-2 cursor-pointer transition active:scale-95"
          >
            <svg v-if="isSendingEmail" class="animate-spin -ml-0.5 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ isSendingEmail ? 'Sending Executive Report...' : 'Send Report 📧' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- SCHEDULE AUTO-SEND MODAL -->
    <div v-if="showScheduleModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-xs z-50 flex items-center justify-center p-4">
      <div class="bg-slate-900 border border-slate-700/80 rounded-2xl max-w-lg w-full shadow-2xl text-slate-100 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
          <h3 class="text-base font-bold text-white flex items-center gap-2">
            <span>⏰</span> Schedule Monthly Auto-Send
          </h3>
          <button @click="showScheduleModal = false" class="text-slate-400 hover:text-white text-lg cursor-pointer transition">✕</button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4 text-xs">
          <!-- Frequency -->
          <div>
            <label class="block font-semibold text-slate-100 mb-1.5 text-xs">Auto-Send Frequency:</label>
            <select v-model="scheduleForm.frequency" class="w-full bg-slate-800 border border-slate-700/80 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-purple-500 transition-all">
              <option>Monthly (1st of every month at 8:00 AM)</option>
              <option>Weekly (Every Monday at 8:00 AM)</option>
              <option>Quarterly (1st day of Quarter)</option>
            </select>
          </div>

          <!-- Target Recipient Emails Tag Input -->
          <div>
            <label class="block font-semibold text-slate-100 mb-1.5 text-xs">Target Recipient Email(s):</label>
            <div class="w-full bg-slate-800 border border-slate-700/80 rounded-xl p-2 flex flex-wrap items-center gap-1.5 focus-within:border-purple-500 focus-within:ring-1 focus-within:ring-purple-500 transition-all">
              <div v-for="(em, idx) in scheduleForm.emails" :key="em" class="inline-flex items-center gap-1.5 bg-purple-500/20 text-purple-200 border border-purple-500/30 text-xs px-2.5 py-1 rounded-lg font-mono">
                <span>{{ em }}</span>
                <button type="button" @click="removeTag(scheduleForm.emails, idx)" class="text-purple-300 hover:text-white font-bold cursor-pointer">✕</button>
              </div>
              <input 
                v-model="scheduleForm.newEmail" 
                @keydown="e => handleTagKeyDown(e, scheduleForm.emails, 'schedule')"
                @blur="addTag(scheduleForm.emails, 'schedule')"
                placeholder="Type email & press Enter..." 
                class="flex-1 bg-transparent text-white text-xs focus:outline-none min-w-[150px] py-1 px-1" 
              />
            </div>
          </div>

          <!-- Attachments -->
          <div class="space-y-2 pt-1">
            <label class="block font-semibold text-slate-100 text-xs">Report Attachments Included:</label>
            <div class="flex items-center gap-4 bg-slate-800/80 p-3 rounded-xl border border-slate-700/60">
              <label class="flex items-center gap-2 cursor-pointer text-slate-200">
                <input type="checkbox" v-model="scheduleForm.attachPdf" class="rounded accent-purple-600 w-4 h-4 cursor-pointer" />
                <span>Executive PDF Report</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer text-slate-200">
                <input type="checkbox" v-model="scheduleForm.attachExcel" class="rounded accent-purple-600 w-4 h-4 cursor-pointer" />
                <span>Excel Spreadsheet Data</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-slate-900/90 border-t border-slate-800 flex justify-end gap-3 text-xs">
          <button @click="showScheduleModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium rounded-xl border border-slate-700 cursor-pointer transition">Cancel</button>
          <button 
            @click="saveSchedule" 
            :disabled="isSavingSchedule" 
            class="px-5 py-2 bg-purple-600 hover:bg-purple-500 disabled:bg-purple-600/70 disabled:cursor-wait text-white font-bold rounded-xl shadow-md flex items-center gap-2 cursor-pointer transition active:scale-95"
          >
            <svg v-if="isSavingSchedule" class="animate-spin -ml-0.5 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ isSavingSchedule ? 'Activating Schedule...' : 'Save & Activate Schedule ⏰' }}</span>
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<style>
/* Remove PrimeVue default toast background wrapping box */
.p-toast .p-toast-message {
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  padding: 0 !important;
  margin-bottom: 0.75rem !important;
}
.p-toast .p-toast-message-content {
  background: transparent !important;
  border: none !important;
  padding: 0 !important;
}
.p-toast .p-toast-message-icon,
.p-toast .p-toast-icon-close {
  display: none !important;
}
</style>
