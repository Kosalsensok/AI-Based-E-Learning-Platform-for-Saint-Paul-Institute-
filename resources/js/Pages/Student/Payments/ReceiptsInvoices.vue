<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const selectedInvoice = ref<any | null>(null)
const emailSentSuccess = ref(false)

const invoicesList = ref([
  { invNo: 'INV-000451', course: 'C Programming Basics', teacher: 'Mr. Sophea', student: 'Chan Dara (STU24001)', amount: '$25.00', date: '15 Jun 2025', status: '✅ Paid', method: 'ABA KHQR', txnId: 'ABA98452178' },
  { invNo: 'INV-000452', course: 'Database Systems & SQL', teacher: 'Mr. Sophea', student: 'Chan Dara (STU24001)', amount: '$25.00', date: '20 Jun 2025', status: '⏳ Pending', method: 'ABA KHQR', txnId: 'PENDING_VERIFY' }
])

const openReceiptPreview = (inv: any) => {
  selectedInvoice.value = inv
  emailSentSuccess.value = false
}

const sendEmailReceipt = () => {
  emailSentSuccess.value = true
}
</script>

<template>
  <StudentLayout title="Receipts & Invoices">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-purple-950 via-slate-900 to-indigo-950 border border-purple-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold uppercase tracking-wider">
            🧾 OFFICIAL INVOICING
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🧾 RECEIPTS / INVOICES</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ទាញយកឯកសារវិក្កយបត្រ PDF ផ្លូវការ ឬផ្ញើចូល Email ផ្ទាល់ខ្លួនឡើងវិញ
          </p>
        </div>

        <button class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md self-start">
          📄 Download All Receipts
        </button>
      </div>

      <!-- RECEIPTS / INVOICES TABLE (Matching Prompt Mock) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
        <div class="border-b border-slate-700/60 pb-3 flex items-center justify-between">
          <h3 class="text-sm font-bold text-white uppercase tracking-wider">ISSUED RECEIPTS LIST</h3>
          <span class="text-xs text-purple-300 font-mono font-bold">2 Issued Documents</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-900 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-700">
              <tr>
                <th class="p-3">Receipt/Invoice #</th>
                <th class="p-3">Course</th>
                <th class="p-3">Amount</th>
                <th class="p-3">Date</th>
                <th class="p-3">Status</th>
                <th class="p-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50 font-medium">
              <tr v-for="inv in invoicesList" :key="inv.invNo" class="hover:bg-slate-700/30">
                <td class="p-3 font-mono font-bold text-purple-300">{{ inv.invNo }}</td>
                <td class="p-3 font-bold text-white">{{ inv.course }}</td>
                <td class="p-3 font-mono font-bold text-emerald-400">{{ inv.amount }}</td>
                <td class="p-3 font-mono text-slate-400">{{ inv.date }}</td>
                <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 font-bold">{{ inv.status }}</span></td>
                <td class="p-3 text-right space-x-1.5">
                  <button @click="openReceiptPreview(inv)" class="px-2.5 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[10px]">
                    Open Detail
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- RECEIPT PREVIEW MODAL / DRAWER (Matching Prompt Mock) -->
      <div v-if="selectedInvoice" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-purple-500/40 rounded-3xl max-w-md w-full p-6 space-y-5 shadow-2xl">
          <!-- Header -->
          <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
            <div>
              <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold">Status: {{ selectedInvoice.status }}</span>
              <h3 class="text-lg font-black text-white mt-1">{{ selectedInvoice.invNo }}</h3>
            </div>
            <button @click="selectedInvoice = null" class="text-slate-400 hover:text-white font-bold text-sm">✕</button>
          </div>

          <!-- Receipt Details Body -->
          <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-2 text-xs font-mono text-slate-200">
            <p>Student: <strong class="text-white">{{ selectedInvoice.student }}</strong></p>
            <p>Course: <span class="text-indigo-300">{{ selectedInvoice.course }}</span></p>
            <p>Teacher: {{ selectedInvoice.teacher }}</p>
            <p>Amount: <strong class="text-emerald-400">{{ selectedInvoice.amount }}</strong></p>
            <p>Payment Method: {{ selectedInvoice.method }}</p>
            <p>ABA Txn ID: {{ selectedInvoice.txnId }}</p>
            <p>Issued Date: {{ selectedInvoice.date }}</p>
          </div>

          <div v-if="emailSentSuccess" class="p-3 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-center text-xs text-emerald-300 font-bold">
            📧 Receipt sent to your email successfully!
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-between pt-2">
            <button @click="selectedInvoice = null" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
              Back
            </button>
            <div class="flex items-center gap-2">
              <button @click="sendEmailReceipt" class="px-3.5 py-2 rounded-xl bg-indigo-600 text-white font-bold text-xs">
                Email Me Again
              </button>
              <button class="px-4 py-2 rounded-xl bg-purple-600 text-white font-bold text-xs shadow-md">
                Download PDF
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
