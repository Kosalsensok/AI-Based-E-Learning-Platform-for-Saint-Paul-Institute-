<script setup lang="ts">
const props = defineProps<{
  show: boolean
  transaction: any
}>()

const emit = defineEmits(['close', 'view-receipt', 'issue-refund'])
</script>

<template>
  <div v-if="show && transaction" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-slate-800 rounded-3xl shadow-2xl border border-slate-700 w-full max-w-2xl overflow-hidden my-6 transform transition-all text-slate-100">
      
      <!-- Drawer Header -->
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between border-b border-slate-700">
        <div>
          <span class="text-[10px] uppercase font-bold text-indigo-400 tracking-wider">Transaction Detail Modal</span>
          <h3 class="text-base font-bold flex items-center gap-2">
            <span>🧾</span> Transaction #{{ transaction.txn_id || transaction.order_id }} 
            <span class="text-xs bg-emerald-600 text-white px-2 py-0.5 rounded-full font-bold">✅ SUCCESS</span>
          </h3>
        </div>
        <button @click="$emit('close')" class="text-slate-400 hover:text-white text-xl font-bold p-1">×</button>
      </div>

      <!-- Modal Body -->
      <div class="p-6 space-y-5 text-xs text-slate-200 max-h-[75vh] overflow-y-auto">
        
        <!-- Customer Info -->
        <div class="bg-slate-900/80 p-4 rounded-2xl border border-slate-700 space-y-1.5">
          <h4 class="font-bold text-indigo-400 uppercase text-[11px] flex items-center gap-1">
            <span>👤</span> CUSTOMER INFO
          </h4>
          <div class="grid grid-cols-2 gap-2 text-xs">
            <div><span class="text-slate-400">Name:</span> <strong class="text-white">{{ transaction.student?.name }}</strong></div>
            <div><span class="text-slate-400">ID:</span> <strong class="font-mono text-slate-300">{{ transaction.student?.id }}</strong></div>
            <div><span class="text-slate-400">Email:</span> <span class="text-slate-300">{{ transaction.student?.email }}</span></div>
            <div><span class="text-slate-400">Phone:</span> <span class="font-mono text-slate-300">{{ transaction.student?.phone }}</span></div>
          </div>
        </div>

        <!-- Purchase Details -->
        <div class="bg-indigo-950/60 p-4 rounded-2xl border border-indigo-800/60 space-y-1.5">
          <h4 class="font-bold text-indigo-300 uppercase text-[11px] flex items-center gap-1">
            <span>📚</span> PURCHASE DETAILS
          </h4>
          <div class="grid grid-cols-2 gap-2 text-xs">
            <div><span class="text-slate-400">Course:</span> <strong class="text-indigo-300">{{ transaction.course }}</strong></div>
            <div><span class="text-slate-400">Teacher:</span> <strong class="text-slate-200">{{ transaction.teacher }}</strong></div>
            <div class="col-span-2"><span class="text-slate-400">Semester:</span> <span class="text-slate-300">{{ transaction.semester }}</span></div>
          </div>
        </div>

        <!-- Payment Breakdown -->
        <div class="bg-slate-900/80 p-4 rounded-2xl border border-slate-700 space-y-1.5 font-mono">
          <h4 class="font-bold uppercase text-[11px] font-sans text-emerald-400 flex items-center gap-1">
            <span>💰</span> PAYMENT BREAKDOWN
          </h4>
          <div class="flex justify-between text-slate-400">
            <span>Base Price:</span>
            <span>${{ (transaction.base_price || 50).toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-slate-400">
            <span>Discount ({{ transaction.discount_code || 'WELCOME10' }}):</span>
            <span>-${{ (transaction.discount_amount || 5).toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-sm font-bold text-white pt-1 border-t border-slate-700">
            <span>Total Paid:</span>
            <span class="text-emerald-400">${{ (transaction.amount || 45).toFixed(2) }} USD</span>
          </div>
        </div>

        <!-- ABA PayWay Details -->
        <div class="bg-slate-900 text-white p-4 rounded-2xl space-y-2 text-xs font-mono border border-slate-700">
          <h4 class="font-bold text-amber-400 uppercase text-[11px] font-sans flex items-center gap-1">
            <span>🏦</span> ABA PAYWAY API DETAILS
          </h4>
          <div class="grid grid-cols-2 gap-2 text-[11px]">
            <div><span class="text-slate-400">ABA Ref#:</span> <strong class="text-emerald-400">{{ transaction.aba_ref }}</strong></div>
            <div><span class="text-slate-400">Payment Method:</span> <span>{{ transaction.payment_channel || 'KHQR (Bakong)' }}</span></div>
            <div><span class="text-slate-400">Bank Account:</span> <span>{{ transaction.bank_account || '--8765' }}</span></div>
            <div><span class="text-slate-400">Merchant ID:</span> <span>{{ transaction.merchant || 'elms_edu_kh' }}</span></div>
            <div><span class="text-slate-400">API Response:</span> <span class="text-emerald-400 font-bold">200 OK</span></div>
            <div><span class="text-slate-400">Callback Verified:</span> <span class="text-emerald-400 font-bold">✅ Yes</span></div>
          </div>
        </div>

        <!-- Timeline -->
        <div class="bg-slate-900/80 p-4 rounded-2xl border border-slate-700 space-y-1 text-xs">
          <h4 class="font-bold text-slate-200 uppercase text-[11px] flex items-center gap-1">
            <span>⏰</span> TRANSACTION TIMELINE
          </h4>
          <ul class="space-y-1 font-mono text-slate-400 text-[11px]">
            <li v-for="(tl, i) in (transaction.timeline || ['10:44:15 → Payment initiated', '10:44:32 → QR code scanned', '10:44:58 → Bank confirmed', '10:45:02 → Course access unlocked ✅'])" :key="i">
              {{ tl }}
            </li>
          </ul>
        </div>

        <!-- Revenue Split -->
        <div class="bg-indigo-950/80 p-4 rounded-2xl border border-indigo-800/60 flex items-center justify-between font-mono text-xs">
          <div>
            <span class="text-slate-400 block text-[10px]">Teacher Share (70%)</span>
            <span class="font-bold text-indigo-300">${{ (transaction.teacher_share_amount || 31.50).toFixed(2) }} → Pending payout</span>
          </div>
          <div class="text-right">
            <span class="text-slate-400 block text-[10px]">Platform Share (30%)</span>
            <span class="font-bold text-emerald-400">${{ (transaction.platform_share_amount || 13.50).toFixed(2) }} → Received</span>
          </div>
        </div>

      </div>

      <!-- Action Footer -->
      <div class="bg-slate-900 px-6 py-4 border-t border-slate-700 flex flex-wrap items-center justify-between gap-2">
        <button @click="$emit('close')" class="px-4 py-2 bg-slate-800 border border-slate-700 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl transition">
          Close
        </button>

        <div class="flex items-center gap-2">
          <button @click="$emit('view-receipt', transaction)" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1">
            <span>📥</span> Download Receipt
          </button>
          <button class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1">
            <span>📧</span> Resend to Student
          </button>
          <button @click="$emit('issue-refund', transaction)" class="px-3.5 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1">
            <span>💸</span> Issue Refund
          </button>
        </div>
      </div>

    </div>
  </div>
</template>
