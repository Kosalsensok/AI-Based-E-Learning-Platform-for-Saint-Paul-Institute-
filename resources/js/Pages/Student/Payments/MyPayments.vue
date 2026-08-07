<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const summary = ref({
  paid: 3,
  pending: 1,
  free: 1,
  refunded: 0,
  totalSpent: '$95.00'
})

const paymentsList = ref([
  { id: '01', course: 'C Programming Basics', teacher: 'Mr. Sophea', amount: '$25.00', status: 'paid', badge: '✅ Paid', access: '🔓 Unlocked' },
  { id: '02', course: 'Database Systems', teacher: 'Mr. Sophea', amount: '$25.00', status: 'pending', badge: '⏳ Pending', access: '🔒 Locked' },
  { id: '03', course: 'Web Development (Vue & Laravel)', teacher: 'Ms. Dara', amount: '$30.00', status: 'paid', badge: '✅ Paid', access: '🔓 Unlocked' },
  { id: '04', course: 'English Grammar & Tech Writing', teacher: 'Ms. Srey', amount: 'FREE', status: 'free', badge: '🎁 Free', access: '🔓 Unlocked' }
])
</script>

<template>
  <StudentLayout title="My Payments — Dashboard">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-indigo-950 border border-emerald-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider">
            💳 ABA PAYWAY GATEWAY
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>✅ MY PAYMENTS (ABA KHQR)</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ទិដ្ឋភាពសរុបនៃការទូទាត់ប្រាក់ តាមដាន status ចូលរៀនបាន (Access Unlocked) និងទាញយក Receipts
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Total Spent</p>
            <p class="text-base font-black text-emerald-400">{{ summary.totalSpent }}</p>
          </div>
        </div>
      </div>

      <!-- STATUS LEGEND (Matching Prompt Spec) -->
      <div class="p-4 bg-slate-900/90 border border-slate-800 rounded-3xl flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30">✅ Paid / Verified</span>
          <span class="text-slate-400">= ចូលរៀនបាន 🔓</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 font-bold border border-amber-500/30">⏳ Pending</span>
          <span class="text-slate-400">= រង់ចាំផ្ទៀងផ្ទាត់ 🔒</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-1 rounded-lg bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/30">🎁 Free</span>
          <span class="text-slate-400">= ឥតគិតថ្លៃ 🔓</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-1 rounded-lg bg-purple-500/20 text-purple-300 font-bold border border-purple-500/30">↩️ Refunded</span>
          <span class="text-slate-400">= សងវិញ</span>
        </div>
      </div>

      <!-- SUMMARY CARDS -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
        <div class="p-4 bg-slate-800/90 border border-slate-700 rounded-2xl">
          <p class="text-[10px] text-slate-400 font-bold uppercase">Paid Courses</p>
          <p class="text-xl font-black text-emerald-400 mt-1">{{ summary.paid }}</p>
        </div>
        <div class="p-4 bg-slate-800/90 border border-slate-700 rounded-2xl">
          <p class="text-[10px] text-slate-400 font-bold uppercase">Pending Verifications</p>
          <p class="text-xl font-black text-amber-400 mt-1">{{ summary.pending }}</p>
        </div>
        <div class="p-4 bg-slate-800/90 border border-slate-700 rounded-2xl">
          <p class="text-[10px] text-slate-400 font-bold uppercase">Free Courses</p>
          <p class="text-xl font-black text-cyan-400 mt-1">{{ summary.free }}</p>
        </div>
        <div class="p-4 bg-slate-800/90 border border-slate-700 rounded-2xl">
          <p class="text-[10px] text-slate-400 font-bold uppercase">Refunded</p>
          <p class="text-xl font-black text-slate-400 mt-1">{{ summary.refunded }}</p>
        </div>
      </div>

      <!-- PAYMENTS CARDS / TABLE (Matching Prompt Mock) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
        <div class="border-b border-slate-700/60 pb-3 flex flex-wrap items-center justify-between gap-3">
          <h3 class="text-sm font-bold text-white uppercase tracking-wider">MY ENROLLED COURSE PAYMENTS</h3>
          <div class="flex items-center gap-2 text-xs">
            <select class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-slate-300">
              <option>Status: All</option>
              <option>Paid</option>
              <option>Pending</option>
            </select>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-900 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-700">
              <tr>
                <th class="p-3">#</th>
                <th class="p-3">Course / Subject</th>
                <th class="p-3">Teacher</th>
                <th class="p-3">Amount</th>
                <th class="p-3">Status</th>
                <th class="p-3">Access</th>
                <th class="p-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50 font-medium">
              <tr v-for="item in paymentsList" :key="item.id" class="hover:bg-slate-700/30">
                <td class="p-3 font-mono text-slate-400">{{ item.id }}</td>
                <td class="p-3 font-bold text-white">{{ item.course }}</td>
                <td class="p-3 text-slate-300">{{ item.teacher }}</td>
                <td class="p-3 font-mono font-bold text-emerald-400">{{ item.amount }}</td>
                <td class="p-3">
                  <span :class="[item.status === 'paid' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : item.status === 'pending' ? 'bg-amber-500/20 text-amber-300 border-amber-500/30' : 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30', 'px-2.5 py-0.5 rounded-lg text-[11px] font-bold border']">
                    {{ item.badge }}
                  </span>
                </td>
                <td class="p-3 font-bold">
                  <span :class="[item.access.includes('Unlocked') ? 'text-emerald-400' : 'text-rose-400']">{{ item.access }}</span>
                </td>
                <td class="p-3 text-right space-x-1.5">
                  <Link v-if="item.status === 'pending'" href="/student/payments/pending" class="px-2.5 py-1 rounded-lg bg-amber-600 text-white font-bold text-[10px]">
                    Pay Now
                  </Link>
                  <Link v-else href="/student/payments/receipts" class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-700 text-slate-300 font-bold text-[10px]">
                    View Receipt
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
