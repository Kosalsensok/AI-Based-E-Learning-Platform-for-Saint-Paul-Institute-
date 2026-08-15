<script setup lang="ts">
import { ref, watch, defineAsyncComponent } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'

// Sub-components inside PaymentABAModule
import PaymentDashboard from './PaymentDashboard.vue'
import CoursePricing from './CoursePricing.vue'
import AbaTransactions from './AbaTransactions.vue'
import TransactionDrawer from './TransactionDrawer.vue'
import ReceiptVerification from './ReceiptVerification.vue'
import PaymentHistory from './PaymentHistory.vue'
import Refunds from './Refunds.vue'
import RevenueReports from './RevenueReports.vue'

// Lazy-Loaded Modals
const PricingModal = defineAsyncComponent(() => import('./PricingModal.vue'))
const DiscountCodeModal = defineAsyncComponent(() => import('./DiscountCodeModal.vue'))
const ReceiptVerificationModal = defineAsyncComponent(() => import('./ReceiptVerificationModal.vue'))
const InvoiceModal = defineAsyncComponent(() => import('./InvoiceModal.vue'))
const RefundModal = defineAsyncComponent(() => import('./RefundModal.vue'))
const TeacherPayoutModal = defineAsyncComponent(() => import('./TeacherPayoutModal.vue'))

const props = defineProps<{
  tab?: string
  stats: any
  paymentMethodsDistribution: any[]
  liveActivityFeed: any[]
  coursePricing: any[]
  discountCodes: any[]
  abaTransactions: any[]
  receiptsQueue: any[]
  paymentHistory: any[]
  refundRequests: any[]
  revenueByMajor: any[]
  topEarningCourses: any[]
  teacherEarnings: any[]
  majors: any[]
  teachers: any[]
}>()

const toast = useToast()

// Active Tab State
const activeTab = ref(props.tab || 'dashboard')

const setTab = (tabName: string) => {
  activeTab.value = tabName
  router.get(route('payments.index'), { tab: tabName }, { preserveState: true, preserveScroll: true, replace: true })
}

watch(() => props.tab, (newTab) => {
  if (newTab) activeTab.value = newTab
})

// Modal States
const showPricingModal = ref(false)
const selectedPricingData = ref<any>(null)

const showDiscountModal = ref(false)

const showTransactionDrawer = ref(false)
const selectedTransaction = ref<any>(null)

const showVerificationModal = ref(false)
const selectedReceipt = ref<any>(null)

const showInvoiceModal = ref(false)
const selectedInvoice = ref<any>(null)

const showRefundModal = ref(false)
const selectedRefund = ref<any>(null)

const showPayoutModal = ref(false)
const selectedTeacherPayout = ref<any>(null)

// Action Handlers
const openAddPricing = () => {
  selectedPricingData.value = null
  showPricingModal.value = true
}

const openEditPricing = (item: any) => {
  selectedPricingData.value = item
  showPricingModal.value = true
}

const handleSavePricing = (formData: any) => {
  router.post(route('payments.pricing.save'), formData, {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({ severity: 'success', summary: 'Pricing Saved', detail: 'Course pricing rule saved and activated successfully! 🏷️', life: 4000 })
    }
  })
}

const handleSaveDiscountCode = (formData: any) => {
  toast.add({ severity: 'success', summary: 'Discount Code Created', detail: `Code ${formData.code} created successfully! 🏷️`, life: 4000 })
}

const openTransactionDetails = (txn: any) => {
  selectedTransaction.value = txn
  showTransactionDrawer.value = true
}

const openReceiptVerification = (rcp: any) => {
  selectedReceipt.value = rcp
  showVerificationModal.value = true
}

const handleApproveReceipt = (rcp: any) => {
  router.post(route('payments.verify-receipt'), { id: rcp.id }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({ severity: 'success', summary: 'Receipt Approved', detail: 'Course access unlocked for student! 🧾✅', life: 4000 })
    }
  })
}

const handleRejectReceipt = (data: any) => {
  router.post(route('payments.reject-receipt'), { id: data.receipt.id, reason: data.reason }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({ severity: 'warn', summary: 'Receipt Rejected', detail: `Rejected: ${data.reason}`, life: 4000 })
    }
  })
}

const openInvoiceModal = (item: any) => {
  selectedInvoice.value = item
  showInvoiceModal.value = true
}

const openRefundReview = (refItem: any) => {
  selectedRefund.value = refItem
  showRefundModal.value = true
}

const handleApproveRefundConfirm = (data: any) => {
  router.post(route('payments.refunds.approve'), { id: data.refund.request_id, note: data.note }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({ severity: 'success', summary: 'Refund Approved', detail: 'Refund processed via ABA Merchant API. ↩️', life: 4000 })
    }
  })
}

const handleRejectRefundConfirm = (refund: any) => {
  router.post(route('payments.refunds.reject'), { id: refund.request_id }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({ severity: 'info', summary: 'Refund Rejected', detail: 'Refund request rejected.', life: 4000 })
    }
  })
}

const handleAskDocsRefundConfirm = (data: any) => {
  toast.add({ severity: 'info', summary: 'Docs Requested', detail: `Additional documentation requested for ${data.refund?.student?.name || 'student'}. 💬`, life: 4000 })
}

const openPayoutModal = (teacherItem: any) => {
  selectedTeacherPayout.value = teacherItem
  showPayoutModal.value = true
}

const handleConfirmPayout = (data: any) => {
  router.post(route('payments.payouts.process'), { teacher_name: data.teacher.teacher, amount: data.amount }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({ severity: 'success', summary: 'Payout Recorded', detail: `Payout of $${data.amount.toFixed(2)} recorded for ${data.teacher.teacher}. 💵`, life: 4000 })
    }
  })
}

const handleRequeryTransaction = () => {
  router.post(route('payments.transactions.requery'), {}, {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({ severity: 'info', summary: 'ABA Sync Complete', detail: 'ABA PayWay status synchronized successfully. Status: PAID ✅', life: 4000 })
    }
  })
}
</script>

<template>
  <AdminLayout title="💳 Payment & ABA Management">
    <Toast />
    
    <div class="w-full px-2 sm:px-4 py-4 space-y-6 text-slate-100 font-sans">
      
      <!-- TOP FINANCIAL CONTROL CENTER & ABA CONNECTION STATUS BAR (MINIMALIST EDGE-TO-EDGE) -->
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-5 rounded-2xl text-white shadow-xl border border-slate-700/80 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
          <div class="shrink-0 flex items-center gap-3">
            <span class="p-2.5 bg-indigo-600/30 border border-indigo-500/50 rounded-2xl text-2xl shadow-inner inline-flex items-center justify-center">💳</span>
            <div>
              <div class="flex items-center gap-2">
                <span class="text-[11px] uppercase font-extrabold text-indigo-400 tracking-wider">Financial Control Center</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-900/90 text-indigo-200 border border-indigo-700/50">ABA PayWay Integration</span>
              </div>
              <p class="text-xs text-slate-200 font-medium mt-0.5">Real-time payment gateway monitoring and financial accounting</p>
            </div>
          </div>

          <!-- ABA Connection Status & Clean Unified Action Group -->
          <div class="flex flex-wrap items-center gap-2.5 text-xs">
            <!-- Sleek API Status Badge with Technical Info Hover Popover -->
            <div class="relative group">
              <div class="bg-slate-900/95 hover:bg-slate-900 px-3.5 py-2 rounded-xl border border-slate-800 flex items-center gap-2 shadow-inner cursor-pointer transition">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="font-bold text-emerald-400">API Connected</span>
                <span class="text-slate-400 text-[10px] bg-slate-800 px-1.5 py-0.5 rounded">MID info</span>
              </div>

              <!-- Technical Details Popover on Hover/Focus -->
              <div class="absolute right-0 top-full mt-2 w-60 bg-slate-900/95 backdrop-blur-md border border-slate-700 rounded-xl p-3 shadow-2xl z-30 opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none group-hover:pointer-events-auto transform group-hover:translate-y-0 translate-y-1">
                <div class="text-[11px] font-bold text-white border-b border-slate-800 pb-1.5 mb-2 flex items-center justify-between">
                  <span>Gateway Technical Info</span>
                  <span class="text-emerald-400 font-mono text-[10px]">🟢 Live</span>
                </div>
                <div class="space-y-1.5 font-mono text-[11px]">
                  <div class="flex justify-between text-slate-300">
                    <span>Merchant ID:</span>
                    <span class="text-indigo-300 font-bold">{{ stats.merchant_id }}</span>
                  </div>
                  <div class="flex justify-between text-slate-300">
                    <span>Last Sync:</span>
                    <span class="text-white font-bold">{{ stats.last_sync }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-1 bg-slate-900/90 p-1 rounded-xl border border-slate-800 shadow-sm">
              <button class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg text-slate-100 font-semibold transition border border-slate-700/80 flex items-center gap-1 cursor-pointer">
                ⚙️ Config
              </button>
              <button @click="handleRequeryTransaction" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-bold transition shadow-md flex items-center gap-1 cursor-pointer">
                🔄 Test Connection
              </button>
            </div>
          </div>
        </div>

        <!-- TOP ABA METRICS BAR (CLICKABLE ANALYTICS CARDS & REFINED VISUAL HIERARCHY) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 text-xs">
          <!-- 1. Gateway Status -->
          <div class="bg-slate-900/80 p-4 rounded-2xl border border-slate-800/90 flex flex-col justify-between">
            <span class="text-slate-300 text-[11px] uppercase font-sans font-bold">ABA Gateway Status</span>
            <div class="mt-2 flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
              <span class="text-emerald-400 font-extrabold text-xs uppercase tracking-wide">Live / Connected</span>
            </div>
          </div>

          <!-- 2. ABA Transactions (Clickable Card with Top-Right Icon - UX Item #4) -->
          <div 
            @click="setTab('transactions')"
            class="bg-slate-900/80 p-4 rounded-2xl border border-slate-800/90 hover:border-indigo-500/80 flex flex-col justify-between cursor-pointer transition-all hover:scale-[1.01] group shadow-sm"
            title="Click to view transactions"
          >
            <div class="flex items-center justify-between">
              <span class="text-slate-300 text-[11px] uppercase font-sans font-bold group-hover:text-indigo-300 transition">ABA Transactions</span>
              <svg class="w-4 h-4 text-slate-500 group-hover:text-indigo-400 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </div>
            <div class="text-2xl font-black text-indigo-300 tracking-tight mt-2">{{ stats.aba_transactions_count }}</div>
          </div>

          <!-- 3. Pending Queue (Clickable Card with Top-Right Icon - UX Item #4) -->
          <div 
            @click="setTab('verification')"
            class="bg-slate-900/80 p-4 rounded-2xl border border-slate-800/90 hover:border-amber-500/80 flex flex-col justify-between cursor-pointer transition-all hover:scale-[1.01] group shadow-sm"
            title="Click to verify queue"
          >
            <div class="flex items-center justify-between">
              <span class="text-slate-300 text-[11px] uppercase font-sans font-bold group-hover:text-amber-300 transition">Pending Queue</span>
              <svg class="w-4 h-4 text-slate-500 group-hover:text-amber-400 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </div>
            <div class="mt-2 flex items-baseline justify-between gap-1">
              <span class="text-2xl font-black text-amber-400 tracking-tight">{{ stats.pending_count }} <span class="text-xs font-bold text-amber-300/80">items</span></span>
              <span class="text-xs font-black text-amber-300 bg-amber-950 px-2 py-0.5 rounded-lg border border-amber-800/60">${{ stats.pending_amount }} USD</span>
            </div>
          </div>

          <!-- 4. Failed Payments (Clickable Card with Top-Right Icon - UX Item #4) -->
          <div 
            @click="setTab('transactions')"
            class="bg-slate-900/80 p-4 rounded-2xl border border-slate-800/90 hover:border-rose-500/80 flex flex-col justify-between cursor-pointer transition-all hover:scale-[1.01] group shadow-sm"
            title="Click to resolve failed payments"
          >
            <div class="flex items-center justify-between">
              <span class="text-slate-300 text-[11px] uppercase font-sans font-bold group-hover:text-rose-300 transition">Failed Payments</span>
              <svg class="w-4 h-4 text-slate-500 group-hover:text-rose-400 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </div>
            <div class="text-2xl font-black text-rose-400 tracking-tight mt-2">{{ stats.failed_count }}</div>
          </div>

          <!-- 5. Success Rate -->
          <div class="bg-slate-900/80 p-4 rounded-2xl border border-slate-800/90 flex flex-col justify-between">
            <span class="text-slate-300 text-[11px] uppercase font-sans font-bold">Success Rate</span>
            <div class="text-2xl font-black text-emerald-400 tracking-tight mt-2">{{ stats.success_rate }}%</div>
          </div>
        </div>
      </div>

      <!-- SUB-MODULE TABS NAVIGATION BAR (MONOTONE NEUTRALIZED INACTIVE ICONS) -->
      <div class="bg-slate-800/90 p-1.5 rounded-2xl border border-slate-700/80 shadow-xl">
        <div class="flex flex-wrap items-center gap-1.5 text-xs">
          <button 
            @click="setTab('dashboard')"
            class="px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 cursor-pointer group"
            :class="activeTab === 'dashboard' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-700/60'"
          >
            <span class="transition-all" :class="activeTab === 'dashboard' ? 'text-white scale-110' : 'text-slate-400 opacity-60 group-hover:opacity-100 group-hover:text-slate-200'">📊</span> Dashboard
          </button>

          <button 
            @click="setTab('pricing')"
            class="px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 cursor-pointer group"
            :class="activeTab === 'pricing' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-700/60'"
          >
            <span class="transition-all" :class="activeTab === 'pricing' ? 'text-white scale-110' : 'text-slate-400 opacity-60 group-hover:opacity-100 group-hover:text-slate-200'">🏷️</span> Course Pricing
          </button>

          <button 
            @click="setTab('transactions')"
            class="px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 cursor-pointer group"
            :class="activeTab === 'transactions' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-700/60'"
          >
            <span class="transition-all" :class="activeTab === 'transactions' ? 'text-white scale-110' : 'text-slate-400 opacity-60 group-hover:opacity-100 group-hover:text-slate-200'">🏦</span> ABA Transactions
          </button>

          <button 
            @click="setTab('verification')"
            class="px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 relative cursor-pointer group"
            :class="activeTab === 'verification' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-700/60'"
          >
            <span class="transition-all" :class="activeTab === 'verification' ? 'text-white scale-110' : 'text-slate-400 opacity-60 group-hover:opacity-100 group-hover:text-slate-200'">🧾</span> Receipt Verification
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
          </button>

          <button 
            @click="setTab('history')"
            class="px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 cursor-pointer group"
            :class="activeTab === 'history' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-700/60'"
          >
            <span class="transition-all" :class="activeTab === 'history' ? 'text-white scale-110' : 'text-slate-400 opacity-60 group-hover:opacity-100 group-hover:text-slate-200'">📜</span> Payment History
          </button>

          <button 
            @click="setTab('refunds')"
            class="px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 cursor-pointer group"
            :class="activeTab === 'refunds' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-700/60'"
          >
            <span class="transition-all" :class="activeTab === 'refunds' ? 'text-white scale-110' : 'text-slate-400 opacity-60 group-hover:opacity-100 group-hover:text-slate-200'">↩️</span> Refunds
          </button>

          <button 
            @click="setTab('revenue')"
            class="px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 cursor-pointer group"
            :class="activeTab === 'revenue' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-700/60'"
          >
            <span class="transition-all" :class="activeTab === 'revenue' ? 'text-white scale-110' : 'text-slate-400 opacity-60 group-hover:opacity-100 group-hover:text-slate-200'">📈</span> Revenue Reports
          </button>
        </div>
      </div>

      <!-- ACTIVE SUB-VIEW RENDERER -->
      <transition name="fade" mode="out-in">
        <!-- 1. DASHBOARD VIEW -->
        <PaymentDashboard 
          v-if="activeTab === 'dashboard'" 
          :stats="stats" 
          :paymentMethodsDistribution="paymentMethodsDistribution" 
          :liveActivityFeed="liveActivityFeed" 
          :recentTransactions="abaTransactions" 
          @switch-tab="setTab" 
          @open-pricing="openAddPricing" 
          @view-transaction="openTransactionDetails" 
        />

        <!-- 2. COURSE PRICING VIEW -->
        <CoursePricing 
          v-else-if="activeTab === 'pricing'" 
          :pricingList="coursePricing" 
          :discountCodes="discountCodes" 
          :stats="stats" 
          :majors="majors" 
          :teachers="teachers" 
          @open-modal="openAddPricing" 
          @edit-pricing="openEditPricing" 
          @open-discount-modal="showDiscountModal = true" 
        />

        <!-- 3. ABA TRANSACTIONS VIEW -->
        <AbaTransactions 
          v-else-if="activeTab === 'transactions'" 
          :transactions="abaTransactions" 
          :stats="stats" 
          @view-transaction="openTransactionDetails" 
          @requery="handleRequeryTransaction" 
        />

        <!-- 4. RECEIPT VERIFICATION VIEW -->
        <ReceiptVerification 
          v-else-if="activeTab === 'verification'" 
          :receipts="receiptsQueue" 
          @open-verification="openReceiptVerification" 
        />

        <!-- 5. PAYMENT HISTORY VIEW -->
        <PaymentHistory 
          v-else-if="activeTab === 'history'" 
          :history="paymentHistory" 
          :majors="majors" 
          :teachers="teachers" 
          @view-invoice="openInvoiceModal" 
        />

        <!-- 6. REFUNDS VIEW -->
        <Refunds 
          v-else-if="activeTab === 'refunds'" 
          :refunds="refundRequests" 
          @open-refund-review="openRefundReview" 
        />

        <!-- 7. REVENUE REPORTS VIEW -->
        <RevenueReports 
          v-else-if="activeTab === 'revenue'" 
          :stats="stats" 
          :revenueByMajor="revenueByMajor" 
          :topEarningCourses="topEarningCourses" 
          :teacherEarnings="teacherEarnings" 
          :paymentMethodsDistribution="paymentMethodsDistribution" 
          @open-payout="openPayoutModal" 
        />
      </transition>

    </div>

    <!-- MODALS & DRAWERS OVERLAYS -->
    <PricingModal 
      :show="showPricingModal" 
      :pricingData="selectedPricingData" 
      :majors="majors" 
      :teachers="teachers" 
      @close="showPricingModal = false" 
      @save="handleSavePricing" 
    />

    <DiscountCodeModal 
      :show="showDiscountModal" 
      @close="showDiscountModal = false" 
      @save="handleSaveDiscountCode" 
    />

    <TransactionDrawer 
      :show="showTransactionDrawer" 
      :transaction="selectedTransaction" 
      @close="showTransactionDrawer = false" 
      @view-receipt="openInvoiceModal" 
      @issue-refund="openRefundReview" 
    />

    <ReceiptVerificationModal 
      :show="showVerificationModal" 
      :receipt="selectedReceipt" 
      @close="showVerificationModal = false" 
      @approve="handleApproveReceipt" 
      @reject="handleRejectReceipt" 
    />

    <InvoiceModal 
      :show="showInvoiceModal" 
      :invoice="selectedInvoice" 
      @close="showInvoiceModal = false" 
    />

    <RefundModal 
      :show="showRefundModal" 
      :refund="selectedRefund" 
      @close="showRefundModal = false" 
      @approve-confirm="handleApproveRefundConfirm" 
      @reject-confirm="handleRejectRefundConfirm" 
      @ask-docs="handleAskDocsRefundConfirm"
    />

    <TeacherPayoutModal 
      :show="showPayoutModal" 
      :teacherData="selectedTeacherPayout" 
      @close="showPayoutModal = false" 
      @confirm-payout="handleConfirmPayout" 
    />
  </AdminLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
