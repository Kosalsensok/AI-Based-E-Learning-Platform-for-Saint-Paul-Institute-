<script setup lang="ts">
import TeacherLayout from '@/Layouts/TeacherLayout.vue'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  revenueList: Array<any>
  totalRevenue: number
  payoutHistory: Array<any>
}>()

const showPayoutModal = ref(false)
const payoutForm = useForm({
  amount: 500,
  account_name: 'SOPHEA IT',
  account_number: '000 123 456',
})

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
  <TeacherLayout title="Earnings & Payments">
    <div class="space-y-6">
      <!-- Title & Summary Banner -->
      <div class="bg-gradient-to-r from-emerald-600 via-teal-700 to-slate-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase">
            💰 Revenue Share: 70%
          </span>
          <h1 class="text-3xl font-extrabold mt-2">${{ totalRevenue ? totalRevenue.toLocaleString() : '2,173.50' }}</h1>
          <p class="text-xs text-emerald-100 mt-1">Total accumulated revenue earned from paid course enrollments</p>
        </div>

        <button
          @click="showPayoutModal = true"
          class="px-5 py-3 bg-white text-emerald-700 font-extrabold rounded-xl text-xs shadow-lg hover:bg-emerald-50 transition"
        >
          🏦 Request Payout (ABA)
        </button>
      </div>

      <!-- Course Revenue Table -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 p-6 space-y-4">
        <h2 class="text-base font-bold text-slate-800 dark:text-white">Course Revenue Breakdown</h2>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <th class="p-3.5">Course Name</th>
                <th class="p-3.5">Price</th>
                <th class="p-3.5">Paid Students</th>
                <th class="p-3.5">Gross Sales</th>
                <th class="p-3.5">Teacher Share (70%)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
              <tr v-for="rev in revenueList" :key="rev.id" class="hover:bg-slate-50/50">
                <td class="p-3.5 font-semibold text-slate-800 dark:text-white">{{ rev.title }}</td>
                <td class="p-3.5 font-bold text-slate-700 dark:text-slate-300">${{ rev.price }}</td>
                <td class="p-3.5 font-bold text-blue-600">{{ rev.paid_students }}</td>
                <td class="p-3.5 font-bold text-slate-700 dark:text-slate-300">${{ rev.gross_revenue.toLocaleString() }}</td>
                <td class="p-3.5 font-extrabold text-emerald-600">${{ rev.teacher_share.toLocaleString() }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Payout History -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 p-6 space-y-4">
        <h2 class="text-base font-bold text-slate-800 dark:text-white">Payout History</h2>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <th class="p-3.5">Payout ID</th>
                <th class="p-3.5">Amount</th>
                <th class="p-3.5">Payout Method</th>
                <th class="p-3.5">Status</th>
                <th class="p-3.5">Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
              <tr v-for="po in payoutHistory" :key="po.id" class="hover:bg-slate-50/50">
                <td class="p-3.5 font-mono font-bold text-blue-600">{{ po.id }}</td>
                <td class="p-3.5 font-extrabold text-slate-800 dark:text-white">${{ po.amount.toFixed(2) }}</td>
                <td class="p-3.5 text-slate-500">{{ po.method }}</td>
                <td class="p-3.5">
                  <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 font-bold rounded-full text-[10px]">
                    🟢 Completed
                  </span>
                </td>
                <td class="p-3.5 text-slate-400">{{ po.requested_at }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Payout Request Modal -->
      <div v-if="showPayoutModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">Request Payout to ABA Account</h3>
          <div class="space-y-3 text-xs">
            <div>
              <label class="block font-semibold mb-1">Amount ($)</label>
              <input v-model="payoutForm.amount" type="number" min="50" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
            <div>
              <label class="block font-semibold mb-1">ABA Account Name</label>
              <input v-model="payoutForm.account_name" type="text" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
            <div>
              <label class="block font-semibold mb-1">ABA Account Number</label>
              <input v-model="payoutForm.account_number" type="text" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button @click="showPayoutModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
            <button @click="submitPayout" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold">Submit Payout Request</button>
          </div>
        </div>
      </div>
    </div>
  </TeacherLayout>
</template>
