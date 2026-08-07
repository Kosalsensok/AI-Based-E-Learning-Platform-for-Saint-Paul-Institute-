<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const paymentAlertsList = ref([
  {
    id: 1,
    type: 'confirmed',
    title: '💳 Payment Confirmed ✅',
    badge: '🟢 Paid',
    course: 'Database Systems & SQL Optimization',
    amount: '$25.00',
    date: '16 Jun 2025 · 10:02 AM',
    access: 'Access Unlocked 🔓',
    hidden: false
  },
  {
    id: 2,
    type: 'pending',
    title: '💳 Payment Pending ⏳',
    badge: '🔒 Locked',
    course: 'Web Development (Vue & Laravel)',
    amount: '$30.00',
    statusText: 'Waiting ABA verification',
    hidden: false
  }
])

const hideAlert = (item: any) => {
  item.hidden = true
}
</script>

<template>
  <StudentLayout title="Notifications — Payment Alerts">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-indigo-950 border border-emerald-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider">
            💳 ABA TRANSACTION NOTIFICATIONS
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>💳 PAYMENT ALERTS</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ការជូនដំណឹងពីស្ថានភាពទូទាត់ប្រាក់ (Pending, Confirmed, Verified ចូលរៀនបាន)
          </p>
        </div>
      </div>

      <!-- PAYMENT ALERTS CARDS (Matching Prompt Spec Layout) -->
      <div class="space-y-4">
        <template v-for="item in paymentAlertsList" :key="item.id">
          <div
            v-if="!item.hidden"
            :class="[item.type === 'confirmed' ? 'border-emerald-500/40' : 'border-amber-500/40', 'bg-slate-800/90 rounded-3xl p-6 border shadow-xl space-y-4']"
          >
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
              <h3 class="text-base font-black text-white">{{ item.title }}</h3>
              <span :class="[item.type === 'confirmed' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30', 'px-3 py-1 rounded-full font-bold text-xs border']">
                {{ item.badge }}
              </span>
            </div>

            <!-- Content for Confirmed -->
            <div v-if="item.type === 'confirmed'" class="space-y-1 text-xs text-slate-300">
              <p class="text-slate-200">Course: <strong class="text-white">{{ item.course }}</strong></p>
              <p>Amount: <strong class="text-emerald-400 font-mono">{{ item.amount }}</strong> · Date: {{ item.date }}</p>
              <p class="text-emerald-400 font-bold pt-1">Status: {{ item.access }}</p>
            </div>

            <!-- Content for Pending -->
            <div v-else class="space-y-1 text-xs text-slate-300">
              <p class="text-slate-200">Course: <strong class="text-white">{{ item.course }}</strong></p>
              <p>Amount: <strong class="text-amber-400 font-mono">{{ item.amount }}</strong></p>
              <p class="text-amber-400 font-bold pt-1">Status: {{ item.statusText }}</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-700/60 text-xs">
              <template v-if="item.type === 'confirmed'">
                <Link href="/student/learning-content/videos" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold shadow-md">
                  📖 Start Learning
                </Link>
                <Link href="/student/payments/receipts" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-md">
                  🧾 View Receipt
                </Link>
              </template>
              <template v-else>
                <Link href="/student/payments/pending" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold shadow-md">
                  📱 Show ABA QR
                </Link>
                <Link href="/student/payments/pending" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 font-bold">
                  🔄 Refresh Status
                </Link>
              </template>
              <button @click="hideAlert(item)" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-400 font-bold">
                🙈 Hide
              </button>
            </div>
          </div>
        </template>
      </div>

    </div>
  </StudentLayout>
</template>
