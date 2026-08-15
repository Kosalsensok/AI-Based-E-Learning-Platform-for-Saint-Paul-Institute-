<script setup lang="ts">
import TeacherLayout from '@/Layouts/TeacherLayout.vue'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  revenueList?: Array<any>
  totalRevenue?: number
  payoutHistory?: Array<any>
  studentPayments?: Array<any>
}>()

const showPayoutModal = ref(false)
const showReceiptModal = ref(false)
const selectedPayment = ref<any>(null)

const payoutForm = useForm({
  amount: 500,
  account_name: 'SOPHEA IT',
  account_number: '000 123 456',
})

const defaultRevenueList = [
  { id: 1, title: 'C Programming Basics', price: 15, paid_students: 40, pending_students: 5, gross_revenue: 600, teacher_share: 420 },
  { id: 2, title: 'Database Systems Fundamentals', price: 20, paid_students: 35, pending_students: 3, gross_revenue: 700, teacher_share: 490 },
  { id: 3, title: 'Web Development Bootcamp', price: 25, paid_students: 28, pending_students: 2, gross_revenue: 700, teacher_share: 490 },
  { id: 4, title: 'Computer Networks', price: 18, paid_students: 20, pending_students: 2, gross_revenue: 360, teacher_share: 252 }
]

const defaultStudentPayments = [
  { id: 'PAY_1001', student_name: 'Chan Dara', course: 'C Programming Basics', amount: 15, status: 'Paid', date: '2025-06-25 10:15 AM', transaction_id: 'ABA_TRX_981240' },
  { id: 'PAY_1002', student_name: 'Bun Rithy', course: 'C Programming Basics', amount: 15, status: 'Paid', date: '2025-06-25 11:30 AM', transaction_id: 'ABA_TRX_981241' },
  { id: 'PAY_1003', student_name: 'Sok Chara', course: 'C Programming Basics', amount: 15, status: 'Pending Verification (Admin)', date: '2025-06-26 08:45 AM', transaction_id: 'ABA_TRX_981245' },
  { id: 'PAY_1004', student_name: 'Long Vicheka', course: 'C Programming Basics', amount: 15, status: 'Paid', date: '2025-06-26 02:20 PM', transaction_id: 'ABA_TRX_981249' }
]

const viewReceipt = (p: any) => {
  selectedPayment.value = p
  showReceiptModal.value = true
}

const submitPayout = () => {
  payoutForm.post(route('teacher.earnings.request-payout'), {
    onSuccess: () => {
      showPayoutModal.value = false
      alert('Payout request submitted successfully!')
    }
  })
}
</script>

<template>
  <TeacherLayout title="Earnings & ABA Status">
    <div class="space-y-6 max-w-7xl mx-auto pb-12">
      
      <!-- Permission Notice Banner (Requirements 30 & 35) -->
      <div class="p-4 bg-indigo-950/60 border border-indigo-500/30 rounded-2xl flex items-center justify-between gap-4 text-xs text-indigo-200 backdrop-blur-xl">
        <div class="flex items-center gap-3">
          <span class="p-2 bg-indigo-500/20 rounded-xl text-indigo-300 font-bold">🔒</span>
          <div>
            <p class="font-bold text-white">ABA Payment Flow — Teacher Scope</p>
            <p class="text-slate-300 text-[11px]">
              Teacher sets Course Fee request & views payment status. Payment verification is handled exclusively by Admin.
            </p>
          </div>
        </div>
        <span class="px-3 py-1 bg-indigo-500/20 border border-indigo-400/30 rounded-full font-mono text-[10px] font-bold text-cyan-300">
          Admin Verified System
        </span>
      </div>

      <!-- Summary Banner -->
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 border border-slate-800">
        <div>
          <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-xs font-bold uppercase tracking-wider">
            💰 Teacher Revenue Share: 70%
          </span>
          <h1 class="text-3xl font-black mt-2 font-mono">${{ totalRevenue ? totalRevenue.toLocaleString() : '1,652.00' }}</h1>
          <p class="text-xs text-slate-300 mt-1">Accumulated net earnings from paid course enrollments after Admin verification.</p>
        </div>

        <button
          @click="showPayoutModal = true"
          class="px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold rounded-2xl text-xs shadow-lg transition flex items-center gap-2 cursor-pointer self-start md:self-auto"
        >
          <span>🏦 Request Payout (ABA Bank)</span>
        </button>
      </div>

      <!-- Course Fee & Revenue Breakdown Table -->
      <div class="bg-slate-900/80 rounded-3xl shadow-xl border border-slate-800 p-6 space-y-4 backdrop-blur-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <h2 class="text-base font-bold text-white flex items-center gap-2">
            <span>📚 Course Fee & Revenue Breakdown</span>
          </h2>
          <span class="text-xs font-mono text-cyan-400 font-bold">Currency: USD ($)</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-950 text-slate-400 uppercase tracking-wider border-b border-slate-800 font-semibold">
                <th class="p-3.5">Course Name</th>
                <th class="p-3.5">Fee ($)</th>
                <th class="p-3.5 text-center">Paid Students</th>
                <th class="p-3.5 text-center">Pending</th>
                <th class="p-3.5">Gross Revenue</th>
                <th class="p-3.5 text-emerald-400">Teacher Earnings (70%)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
              <tr v-for="rev in (revenueList || defaultRevenueList)" :key="rev.id" class="hover:bg-slate-800/30 transition-colors">
                <td class="p-3.5 font-bold text-white flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                  <span>{{ rev.title }}</span>
                </td>
                <td class="p-3.5 font-mono font-bold text-slate-300">${{ rev.price }}</td>
                <td class="p-3.5 text-center font-mono font-bold text-emerald-400">{{ rev.paid_students }}</td>
                <td class="p-3.5 text-center font-mono font-bold text-amber-400">{{ rev.pending_students }}</td>
                <td class="p-3.5 font-mono font-bold text-slate-200">${{ rev.gross_revenue.toLocaleString() }}</td>
                <td class="p-3.5 font-mono font-extrabold text-emerald-400 text-sm">${{ rev.teacher_share.toLocaleString() }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Student Payment List & Receipt View -->
      <div class="bg-slate-900/80 rounded-3xl shadow-xl border border-slate-800 p-6 space-y-4 backdrop-blur-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <h2 class="text-base font-bold text-white flex items-center gap-2">
            <span>💳 Student ABA Payment Status List</span>
          </h2>
          <span class="text-xs font-mono text-slate-400">Live Status Feed</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-950 text-slate-400 uppercase tracking-wider border-b border-slate-800 font-semibold">
                <th class="p-3.5">Transaction ID</th>
                <th class="p-3.5">Student Name</th>
                <th class="p-3.5">Course</th>
                <th class="p-3.5">Amount</th>
                <th class="p-3.5">Payment Status</th>
                <th class="p-3.5">Date</th>
                <th class="p-3.5">Receipt</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
              <tr v-for="p in defaultStudentPayments" :key="p.id" class="hover:bg-slate-800/30 transition-colors">
                <td class="p-3.5 font-mono font-bold text-indigo-400">{{ p.transaction_id }}</td>
                <td class="p-3.5 font-bold text-white">{{ p.student_name }}</td>
                <td class="p-3.5 text-slate-300">{{ p.course }}</td>
                <td class="p-3.5 font-mono font-bold text-emerald-400">${{ p.amount }}</td>
                <td class="p-3.5">
                  <span :class="[p.status === 'Paid' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30', 'px-2.5 py-1 rounded-full font-bold text-[10px]']">
                    {{ p.status }}
                  </span>
                </td>
                <td class="p-3.5 font-mono text-slate-400">{{ p.date }}</td>
                <td class="p-3.5">
                  <button @click="viewReceipt(p)" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-cyan-300 rounded text-[11px] font-bold">
                    View Receipt
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Receipt Modal -->
      <div v-if="showReceiptModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl text-white">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-cyan-400">📄 ABA Payment Receipt</h3>
            <button @click="showReceiptModal = false" class="text-slate-400 hover:text-white text-xs">✕ Close</button>
          </div>

          <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 space-y-2 font-mono text-xs">
            <div class="flex justify-between text-slate-400">
              <span>Transaction Ref:</span>
              <span class="text-white font-bold">{{ selectedPayment?.transaction_id }}</span>
            </div>
            <div class="flex justify-between text-slate-400">
              <span>Student:</span>
              <span class="text-white font-bold">{{ selectedPayment?.student_name }}</span>
            </div>
            <div class="flex justify-between text-slate-400">
              <span>Course:</span>
              <span class="text-white font-bold">{{ selectedPayment?.course }}</span>
            </div>
            <div class="flex justify-between text-slate-400">
              <span>Amount Paid:</span>
              <span class="text-emerald-400 font-bold">${{ selectedPayment?.amount }}.00 USD</span>
            </div>
            <div class="flex justify-between text-slate-400">
              <span>Payment Gateway:</span>
              <span class="text-indigo-400 font-bold">ABA PAY / KHQR</span>
            </div>
            <div class="flex justify-between text-slate-400">
              <span>Admin Verification:</span>
              <span class="text-emerald-400 font-bold">Verified & Active</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Payout Modal -->
      <div v-if="showPayoutModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl text-white">
          <h3 class="text-base font-bold text-white">Request Payout to ABA Account</h3>
          <div class="space-y-3 text-xs">
            <div>
              <label class="block font-semibold mb-1 text-slate-300">Payout Amount ($)</label>
              <input v-model="payoutForm.amount" type="number" min="50" class="w-full p-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white font-mono" />
            </div>
            <div>
              <label class="block font-semibold mb-1 text-slate-300">ABA Account Name</label>
              <input v-model="payoutForm.account_name" type="text" class="w-full p-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white" />
            </div>
            <div>
              <label class="block font-semibold mb-1 text-slate-300">ABA Account Number</label>
              <input v-model="payoutForm.account_number" type="text" class="w-full p-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white font-mono" />
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button @click="showPayoutModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-semibold">Cancel</button>
            <button @click="submitPayout" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold">Submit Payout Request</button>
          </div>
        </div>
      </div>

    </div>
  </TeacherLayout>
</template>
