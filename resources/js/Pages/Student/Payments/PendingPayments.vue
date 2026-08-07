<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const showQrModal = ref(false)
const showUploadModal = ref(false)
const hasPaidSuccess = ref(false)

const pendingItem = ref({
  course: 'Database Systems & SQL Optimization',
  teacher: 'Mr. Sophea',
  mode: '💻 Self-Study',
  amount: '$25.00',
  orderId: 'PAY-25060109',
  expiresIn: '14:32',
  deadline: 'May 20, 11:59 PM',
  status: '🔒 Locked (Unlock after ABA verification)'
})

const confirmPayment = () => {
  showQrModal.value = false
  hasPaidSuccess.value = true
  pendingItem.value.status = '🔓 Unlocked (Access Granted!)'
}
</script>

<template>
  <StudentLayout title="Pending Payments — ABA Checkout">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-amber-950 via-slate-900 to-indigo-950 border border-amber-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold uppercase tracking-wider">
            ⏳ PENDING CHECKOUT
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>⏳ PENDING PAYMENTS (1 item)</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            បង្ហាញប្រកាសទូទាត់រង់ចាំ — បើក ABA KHQR ស្កែនទូទាត់ភ្លាមៗដើម្បីបើកសោរមេរៀន 🔓
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Pending Amount</p>
            <p class="text-base font-black text-amber-400">$25.00</p>
          </div>
        </div>
      </div>

      <!-- PENDING ITEM CARD (Matching Prompt Mock) -->
      <div class="bg-slate-800/90 border border-amber-500/40 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-700/60 pb-4">
          <div>
            <span class="px-3 py-1 rounded-full bg-amber-600/20 text-amber-300 font-bold text-xs border border-amber-500/40">
              Order ID: {{ pendingItem.orderId }}
            </span>
            <h2 class="text-lg font-black text-white mt-2">
              Course: {{ pendingItem.course }}
            </h2>
            <p class="text-xs text-slate-400">
              Teacher: {{ pendingItem.teacher }}  •  Mode: {{ pendingItem.mode }}
            </p>
          </div>

          <div class="text-right">
            <p class="text-base font-black text-emerald-400 font-mono">{{ pendingItem.amount }}</p>
            <p class="text-xs font-mono text-rose-400 font-bold">Expires in: ⏱️ {{ pendingItem.expiresIn }}</p>
          </div>
        </div>

        <!-- Access Status Alert -->
        <div :class="[hasPaidSuccess ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300' : 'bg-rose-500/10 border-rose-500/30 text-rose-300', 'p-4 rounded-2xl border text-xs font-bold flex items-center justify-between']">
          <span>✅ Access Status: {{ pendingItem.status }}</span>
          <span class="text-slate-400 font-normal">Notifications: 🔔 Enabled</span>
        </div>

        <!-- Action Buttons Row -->
        <div class="flex flex-wrap items-center gap-3 pt-2">
          <button
            @click="showQrModal = true"
            class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-lg transition-all hover:scale-105"
          >
            📱 Show ABA QR →
          </button>
          <button
            @click="showQrModal = true"
            class="px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md"
          >
            📲 Open ABA App
          </button>
          <button
            @click="showUploadModal = true"
            class="px-4 py-3 rounded-2xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700"
          >
            🧾 Upload Receipt (optional)
          </button>
        </div>

        <!-- Payment Guide Note -->
        <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 text-xs text-slate-300 space-y-1">
          <p class="font-bold text-white">Payment Guide:</p>
          <p>Scan ABA KHQR → Pay → Wait webhook verify (usually 1–5 min) → 🔓 Access will unlock automatically.</p>
        </div>

      </div>

      <!-- ABA KHQR CHECKOUT MODAL (Matching Prompt Mock) -->
      <div v-if="showQrModal" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-emerald-500/50 rounded-3xl max-w-sm w-full p-6 text-center space-y-5 shadow-2xl">
          <div class="border-b border-slate-800 pb-3 space-y-1">
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 font-bold text-[10px]">📱 ABA KHQR / PayWay Checkout</span>
            <h3 class="text-sm font-black text-white pt-1">Order ID: {{ pendingItem.orderId }}</h3>
            <p class="text-base font-black text-emerald-400 font-mono">Amount: {{ pendingItem.amount }}</p>
          </div>

          <!-- QR Code Container Mockup -->
          <div class="p-4 bg-white rounded-2xl border-4 border-emerald-500 shadow-xl inline-block">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=ABA_PAYWAY_E_LMS_25USD" alt="ABA KHQR" class="w-44 h-44 mx-auto" />
            <p class="text-[10px] text-slate-900 font-black mt-2 uppercase tracking-wider">KHQR • ABA PAYWAY</p>
          </div>

          <p class="text-[11px] text-rose-400 font-mono font-bold">Expires in: {{ pendingItem.expiresIn }}</p>

          <div class="flex items-center justify-center gap-2 pt-2 border-t border-slate-800">
            <button @click="confirmPayment" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md">
              I have paid ✅
            </button>
            <button @click="showQrModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
              Cancel
            </button>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
