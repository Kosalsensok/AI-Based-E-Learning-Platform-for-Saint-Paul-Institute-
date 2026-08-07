<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const showCreateModal = ref(false)
const selectedTicket = ref<any | null>(null)
const createdSuccess = ref(false)

const ticketCategory = ref('💳 Payment')
const ticketSubject = ref('My payment is pending but I already paid')
const ticketDetails = ref('I scanned ABA KHQR 2 hours ago but access is still locked.')
const ticketPriority = ref('High')

const ticketList = ref([
  { id: 'T-2001', category: '💳 Payment Issue', subject: 'ABA Payment pending over 2h', status: '⏳ Pending', opened: '2h ago', priority: 'High', details: 'Paid via ABA PayWay, txn ID ABA98452178.' },
  { id: 'T-1996', category: '🔐 Login Problem', subject: 'JWT token expired on mobile', status: '✅ Resolved', opened: '2 days ago', priority: 'Normal', details: 'Admin cleared session cache and updated account permissions.' }
])

const submitTicket = () => {
  ticketList.value.unshift({
    id: `T-${Math.floor(2002 + Math.random() * 100)}`,
    category: ticketCategory.value,
    subject: ticketSubject.value,
    status: '⏳ Pending',
    opened: 'Just now',
    priority: ticketPriority.value,
    details: ticketDetails.value
  })
  showCreateModal.value = false
  createdSuccess.value = true
  setTimeout(() => createdSuccess.value = false, 3000)
}
</script>

<template>
  <StudentLayout title="Support Tickets">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-rose-950 via-slate-900 to-indigo-950 border border-rose-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-bold uppercase tracking-wider">
            🎫 TECHNICAL &amp; PAYMENT SUPPORT
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🎫 SUPPORT TICKETS</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ដោះស្រាយបញ្ហាបច្ចេកទេស បង់ប្រាក់ Login ឬគណនីជាមួយ Admin និង Support Team
          </p>
        </div>

        <button @click="showCreateModal = true" class="px-5 py-2.5 rounded-2xl bg-rose-600 hover:bg-rose-500 text-white font-black text-xs shadow-lg self-start">
          ➕ Create New Ticket
        </button>
      </div>

      <!-- Toast Alert -->
      <div v-if="createdSuccess" class="p-4 bg-emerald-500/10 border border-emerald-500/40 rounded-2xl text-xs text-emerald-300 font-bold">
        ✅ Support ticket submitted successfully! Ticket ID generated.
      </div>

      <!-- FILTER BAR -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-5 shadow-xl flex flex-wrap items-center justify-between gap-3 text-xs">
        <select class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
          <option>Filter: All</option>
          <option>Open / Pending</option>
          <option>Resolved</option>
        </select>
        <span class="text-slate-400 font-mono font-bold">{{ ticketList.length }} Tickets Total</span>
      </div>

      <!-- TICKET QUEUE LIST (Matching Prompt Mock Layout) -->
      <div class="space-y-4">
        <div
          v-for="t in ticketList"
          :key="t.id"
          class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-3"
        >
          <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <div class="flex items-center gap-2">
              <span class="px-3 py-1 rounded-full bg-slate-900 font-mono font-bold text-xs text-indigo-300 border border-slate-700">
                {{ t.id }}
              </span>
              <span class="font-bold text-white text-xs">{{ t.category }}</span>
            </div>
            <span :class="[t.status === '✅ Resolved' ? 'text-emerald-400' : 'text-amber-400', 'font-bold text-xs']">
              {{ t.status }}
            </span>
          </div>

          <div class="space-y-1 text-xs">
            <h3 class="text-sm font-black text-white">Subject: {{ t.subject }}</h3>
            <p class="text-slate-400">Opened: {{ t.opened }} | Priority: <span class="text-rose-400 font-bold">{{ t.priority }}</span></p>
          </div>

          <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-700/60 text-xs">
            <button @click="selectedTicket = t" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-md">
              👁 View
            </button>
            <button v-if="t.status !== '✅ Resolved'" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 font-bold">
              📎 Add Evidence
            </button>
            <button v-if="t.status !== '✅ Resolved'" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-rose-400 font-bold">
              ↩ Cancel
            </button>
            <button v-else class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-emerald-400 font-bold">
              Rate Support ⭐
            </button>
          </div>
        </div>
      </div>

      <!-- CREATE TICKET MODAL (Matching Prompt Spec) -->
      <div v-if="showCreateModal" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-rose-500/40 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
          <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
            <h3 class="text-base font-black text-white">➕ CREATE SUPPORT TICKET</h3>
            <button @click="showCreateModal = false" class="text-slate-400 hover:text-white font-bold text-sm">✕</button>
          </div>

          <div class="space-y-3 text-xs">
            <div class="space-y-1">
              <label class="font-bold text-white uppercase">Category:</label>
              <select v-model="ticketCategory" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                <option>💳 Payment</option>
                <option>🔐 Account</option>
                <option>🛠 Bug</option>
                <option>📚 Course Access</option>
              </select>
            </div>

            <div class="space-y-1">
              <label class="font-bold text-white uppercase">Subject:</label>
              <input
                v-model="ticketSubject"
                type="text"
                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold"
              />
            </div>

            <div class="space-y-1">
              <label class="font-bold text-white uppercase">Details:</label>
              <textarea
                v-model="ticketDetails"
                rows="3"
                class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white custom-scrollbar"
              ></textarea>
            </div>

            <div class="space-y-1">
              <label class="font-bold text-white uppercase">Evidence:</label>
              <div class="flex items-center gap-2">
                <button class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-700 text-slate-300 font-bold">📎 Screenshot</button>
                <button class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-700 text-slate-300 font-bold">📎 ABA receipt</button>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
            <button @click="showCreateModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
              Save Draft
            </button>
            <button @click="submitTicket" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-md">
              📤 Submit Ticket →
            </button>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
