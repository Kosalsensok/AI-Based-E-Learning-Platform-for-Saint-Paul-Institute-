<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const selectedHistory = ref<any | null>(null)

const historyTable = ref([
  { date: '15 Jun 2025', orderId: 'PAY-25060101', course: 'C Programming Basics', teacher: 'Mr. Sophea', amount: '$25.00', status: '✅ Paid', txnId: 'ABA98452178', paidAt: '15 Jun 2025, 09:31 AM', source: 'ABA Webhook ✅' },
  { date: '14 Jun 2025', orderId: 'PAY-25060103', course: 'English Writing & Syntax', teacher: 'Ms. Srey', amount: '$20.00', status: '❌ Failed', txnId: 'FAILED_TIMEOUT', paidAt: '14 Jun 2025, 14:10 PM', source: 'Timeout Expiry' },
  { date: '13 Jun 2025', orderId: 'PAY-25060104', course: 'Plant Science Fundamentals', teacher: 'Dr. Chan', amount: '$25.00', status: '↩️ Refunded', txnId: 'ABA98452100', paidAt: '13 Jun 2025, 11:20 AM', source: 'Refund Approved' }
])

const openDetail = (item: any) => {
  selectedHistory.value = item
}
</script>

<template>
  <StudentLayout title="Payment History & Audit Trail">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 border border-blue-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold uppercase tracking-wider">
            📜 AUDIT TRAIL LOG
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>📜 PAYMENT HISTORY</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            រក្សាកំណត់ត្រា Payment ទាំងអស់ Order ID, ABA Transaction ID, Status, និង Verification Source
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Total Transactions</p>
            <p class="text-base font-black text-blue-400">3 Records</p>
          </div>
        </div>
      </div>

      <!-- SEARCH & FILTERS -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-5 shadow-xl">
        <div class="flex flex-wrap items-center gap-3 text-xs">
          <input type="text" placeholder="Search Order ID or Course..." class="bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" />
          <select class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-300">
            <option>Status: All</option>
            <option>Paid</option>
            <option>Failed</option>
            <option>Refunded</option>
          </select>
        </div>
      </div>

      <!-- HISTORY AUDIT TABLE (Matching Prompt Mock) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider">ALL PAYMENT TRANSACTIONS</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-900 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-700">
              <tr>
                <th class="p-3">Date</th>
                <th class="p-3">Order ID</th>
                <th class="p-3">Course</th>
                <th class="p-3">Amount</th>
                <th class="p-3">Status</th>
                <th class="p-3 text-right">Detail</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50 font-medium">
              <tr v-for="item in historyTable" :key="item.orderId" @click="openDetail(item)" class="hover:bg-slate-700/30 cursor-pointer">
                <td class="p-3 font-mono text-slate-400">{{ item.date }}</td>
                <td class="p-3 font-mono font-bold text-indigo-300">{{ item.orderId }}</td>
                <td class="p-3 font-bold text-white">{{ item.course }}</td>
                <td class="p-3 font-mono font-bold text-emerald-400">{{ item.amount }}</td>
                <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 font-bold">{{ item.status }}</span></td>
                <td class="p-3 text-right">
                  <button class="px-2.5 py-1 rounded-lg bg-indigo-600/30 text-indigo-300 font-bold text-[10px]">
                    View Detail
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- PAYMENT DETAIL DRAWER / MODAL (Matching Prompt Spec) -->
      <div v-if="selectedHistory" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-blue-500/40 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
          <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
            <h3 class="text-base font-black text-white">💳 Payment Detail – {{ selectedHistory.orderId }}</h3>
            <button @click="selectedHistory = null" class="text-slate-400 hover:text-white font-bold text-sm">✕</button>
          </div>

          <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-2 text-xs font-mono text-slate-300">
            <p>Order ID: <strong class="text-indigo-400">{{ selectedHistory.orderId }}</strong></p>
            <p>ABA Txn ID: <strong class="text-emerald-400">{{ selectedHistory.txnId }}</strong></p>
            <p>Course: <span class="text-white">{{ selectedHistory.course }}</span></p>
            <p>Teacher: {{ selectedHistory.teacher }}</p>
            <p>Amount: <strong class="text-emerald-400">{{ selectedHistory.amount }} (Verified)</strong></p>
            <p>Paid At: {{ selectedHistory.paidAt }}</p>
            <p>Verification Source: {{ selectedHistory.source }}</p>
            <p>Access Status: <span class="text-emerald-400 font-bold">🔓 Unlocked</span></p>
          </div>

          <div class="flex items-center justify-end gap-3 pt-2">
            <button @click="selectedHistory = null" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
              Close
            </button>
            <button class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md">
              Download Receipt PDF
            </button>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
