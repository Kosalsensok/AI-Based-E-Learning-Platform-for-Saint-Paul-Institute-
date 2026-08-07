<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps<{
  tickets: Array<any>
  topStats: any
}>()

const viewMode = ref<'kanban' | 'table'>('kanban')
const showDetailModal = ref(false)
const selectedTicket = ref<any>(null)
const replyText = ref('')

const openDetail = (ticket: any) => {
  selectedTicket.value = ticket
  showDetailModal.value = true
}

const unlockCourseDirectly = () => {
  window.alert('🔓 Course unlocked directly for student Chan Dara!')
  if (selectedTicket.value) {
    selectedTicket.value.status = 'resolved'
  }
  showDetailModal.value = false
}

const escalateTicket = () => {
  window.alert('Escalated to Manager!')
}

const openNewTicketModal = () => {
  window.alert('New ticket creation modal opened!')
}

const sendReply = () => {
  if (!replyText.value) return
  if (selectedTicket.value) {
    if (!selectedTicket.value.timeline) selectedTicket.value.timeline = []
    selectedTicket.value.timeline.push({
      time: 'Just now',
      event: '💬 Reply to Student: ' + replyText.value
    })
    replyText.value = ''
    window.alert('💬 Reply sent to student!')
  }
}
</script>

<template>
  <AdminLayout title="Discussions & Support — Support Tickets Helpdesk">
    <div class="space-y-6">
      
      <!-- Top Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl">
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            🎫 SUPPORT TICKETS HELPDESK
            <span class="text-xs bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 px-2.5 py-0.5 rounded-full font-mono">
              Helpdesk & Technical Support
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            គ្រប់គ្រងសំបុត្រជំនួយ (Helpdesk Tickets) សម្រាប់បញ្ហាបច្ចេកទេស ការទូទាត់ និងគណនី។
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button @click="viewMode = viewMode === 'kanban' ? 'table' : 'kanban'" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl border border-slate-700">
            {{ viewMode === 'kanban' ? '📑 Switch to Table View' : '📋 Switch to Kanban Board' }}
          </button>
          <button @click="openNewTicketModal" class="px-4 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-cyan-600/20">
            ➕ New Ticket
          </button>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-1 border-b border-slate-800 overflow-x-auto pb-1 custom-scrollbar">
        <Link href="/admin/discussions/board" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          💬 Discussions
        </Link>
        <Link href="/admin/discussions/questions" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          ❓ Student Questions
        </Link>
        <Link href="/admin/discussions/tickets" class="px-4 py-2.5 text-xs rounded-xl border bg-cyan-500/15 text-cyan-300 border-cyan-500/40 font-bold shadow-sm shrink-0">
          🎫 Support Tickets
        </Link>
        <Link href="/admin/discussions/reports" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🚩 Reported Content
        </Link>
      </div>

      <!-- KANBAN BOARD VIEW -->
      <div v-if="viewMode === 'kanban'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-xs">
        
        <!-- Column 1: NEW -->
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl space-y-3">
          <div class="flex justify-between items-center border-b border-slate-800 pb-2">
            <h3 class="font-bold text-cyan-400 uppercase tracking-wider text-[11px]">📥 NEW (18)</h3>
          </div>
          <div class="space-y-2">
            <div @click="openDetail(tickets[0] || {})" class="bg-slate-950 border border-slate-800 hover:border-cyan-500/50 p-3 rounded-xl space-y-2 cursor-pointer transition">
              <div class="flex justify-between text-[11px]">
                <span class="font-mono text-cyan-400 font-bold">TK-001</span>
                <span class="bg-red-500/20 text-red-400 px-1.5 py-0.5 rounded text-[10px] font-bold">🔴 High</span>
              </div>
              <h4 class="font-bold text-white text-xs">Can't login after ABA payment</h4>
              <p class="text-slate-400 text-[11px]">Chan Dara • 2h ago</p>
              <div class="text-[10px] text-amber-400 font-mono">⏰ 2h left</div>
            </div>
          </div>
        </div>

        <!-- Column 2: OPEN -->
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl space-y-3">
          <div class="flex justify-between items-center border-b border-slate-800 pb-2">
            <h3 class="font-bold text-amber-400 uppercase tracking-wider text-[11px]">⏳ OPEN (25)</h3>
          </div>
          <div class="space-y-2">
            <div @click="openDetail(tickets[1] || {})" class="bg-slate-950 border border-slate-800 hover:border-amber-500/50 p-3 rounded-xl space-y-2 cursor-pointer transition">
              <div class="flex justify-between text-[11px]">
                <span class="font-mono text-amber-400 font-bold">TK-002</span>
                <span class="bg-red-500/20 text-red-400 px-1.5 py-0.5 rounded text-[10px] font-bold">🔴 High</span>
              </div>
              <h4 class="font-bold text-white text-xs">ABA transaction failed twice</h4>
              <p class="text-slate-400 text-[11px]">Bun Rithy • 5h ago</p>
              <div class="text-[10px] text-red-400 font-mono font-bold">⏰ Overdue</div>
            </div>
          </div>
        </div>

        <!-- Column 3: WAITING -->
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl space-y-3">
          <div class="flex justify-between items-center border-b border-slate-800 pb-2">
            <h3 class="font-bold text-slate-400 uppercase tracking-wider text-[11px]">👀 WAITING (40)</h3>
          </div>
          <div class="space-y-2">
            <div @click="openDetail(tickets[2] || {})" class="bg-slate-950 border border-slate-800 p-3 rounded-xl space-y-2 cursor-pointer hover:border-slate-700 transition">
              <div class="flex justify-between text-[11px]">
                <span class="font-mono text-slate-400 font-bold">TK-003</span>
                <span class="bg-amber-500/20 text-amber-400 px-1.5 py-0.5 rounded text-[10px]">🟡 Med</span>
              </div>
              <h4 class="font-bold text-white text-xs">Video not playing on iOS</h4>
              <p class="text-slate-400 text-[11px]">Sok Channa • 1d ago</p>
            </div>
          </div>
        </div>

        <!-- Column 4: RESOLVED -->
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl space-y-3">
          <div class="flex justify-between items-center border-b border-slate-800 pb-2">
            <h3 class="font-bold text-emerald-400 uppercase tracking-wider text-[11px]">✅ RESOLVED (45)</h3>
          </div>
          <div class="space-y-2">
            <div @click="openDetail(tickets[3] || {})" class="bg-slate-950 border border-slate-800 p-3 rounded-xl space-y-2 cursor-pointer hover:border-slate-700 transition">
              <div class="flex justify-between text-[11px]">
                <span class="font-mono text-emerald-400 font-bold">TK-004</span>
                <span class="bg-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded text-[10px]">🟢 Low</span>
              </div>
              <h4 class="font-bold text-white text-xs">Certificate PDF download fixed</h4>
              <p class="text-slate-400 text-[11px]">Pov Sreynich • Fixed</p>
            </div>
          </div>
        </div>

        <!-- Column 5: CLOSED -->
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl space-y-3">
          <div class="flex justify-between items-center border-b border-slate-800 pb-2">
            <h3 class="font-bold text-slate-500 uppercase tracking-wider text-[11px]">🔒 CLOSED (200)</h3>
          </div>
          <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 text-slate-500 text-center py-6">
            200 Archived Tickets
          </div>
        </div>

      </div>

      <!-- TABLE VIEW -->
      <div v-else class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 font-semibold uppercase tracking-wider">
                <th class="p-4">Ticket ID</th>
                <th class="p-4">Student</th>
                <th class="p-4">Subject</th>
                <th class="p-4">Category</th>
                <th class="p-4">Priority</th>
                <th class="p-4">Assigned To</th>
                <th class="p-4">SLA Time</th>
                <th class="p-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
              <tr v-for="t in tickets" :key="t.ticket_code" class="hover:bg-slate-800/40 transition">
                <td class="p-4 font-mono font-bold text-cyan-400">{{ t.ticket_code }}</td>
                <td class="p-4 font-bold text-white">{{ t.student_name }}</td>
                <td class="p-4 text-slate-200">{{ t.subject }}</td>
                <td class="p-4"><span class="px-2 py-0.5 bg-slate-800 border border-slate-700 rounded">{{ t.category }}</span></td>
                <td class="p-4">
                  <span :class="t.priority === 'high' ? 'bg-red-500/20 text-red-400 border-red-500/30' : 'bg-amber-500/20 text-amber-400 border-amber-500/30'" class="px-2 py-0.5 text-[10px] font-bold border rounded-full">
                    {{ t.priority }}
                  </span>
                </td>
                <td class="p-4 text-slate-400">{{ t.assigned_to }}</td>
                <td class="p-4 font-mono text-amber-400">{{ t.sla_time_left }}</td>
                <td class="p-4 text-right space-x-2">
                  <button @click="openDetail(t)" class="text-cyan-400 hover:text-cyan-300 font-bold">👁 View</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TICKET DETAIL TIMELINE MODAL -->
    <!-- ========================================================================= -->
    <div v-if="showDetailModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-3xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden my-auto">
        <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center bg-slate-950">
          <h3 class="text-base font-bold text-white flex items-center gap-2">
            🎫 Ticket {{ selectedTicket?.ticket_code || 'TK-001' }} — {{ selectedTicket?.subject }}
          </h3>
          <button @click="showDetailModal = false" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <div class="p-6 space-y-5 overflow-y-auto custom-scrollbar text-xs">
          <!-- Student Details Header -->
          <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 flex justify-between items-center">
            <div>
              <span class="font-bold text-white text-sm block">👤 {{ selectedTicket?.student_name || 'Chan Dara' }}</span>
              <span class="text-slate-400 font-mono">{{ selectedTicket?.student_email || 'dara@example.com' }}</span>
            </div>
            <span class="px-3 py-1 bg-red-500/20 text-red-400 border border-red-500/30 rounded-full font-bold">🔴 High Priority</span>
          </div>

          <!-- Message Body -->
          <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2">
            <span class="font-bold text-slate-400 block text-[11px]">Message Content:</span>
            <p class="text-slate-200 text-sm">"{{ selectedTicket?.message || 'I paid $45 via ABA but account is locked...' }}"</p>
            <div class="text-[11px] text-cyan-400 font-mono pt-1">📎 Attached: ABA_receipt.jpg</div>
          </div>

          <!-- Timeline Log -->
          <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-3">
            <h4 class="font-bold text-amber-400 uppercase tracking-wider text-[11px]">📜 Activity Timeline Log</h4>
            <div class="space-y-2 font-mono text-[11px]">
              <div v-for="(ev, idx) in selectedTicket?.timeline || []" :key="idx" class="p-2 bg-slate-900 rounded border border-slate-800 text-slate-300">
                <strong class="text-amber-400">{{ ev.time }}</strong> - {{ ev.event }}
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-3">
            <h4 class="font-bold text-slate-300 uppercase tracking-wider text-[11px]">⚡ Quick Admin Actions:</h4>
            <div class="flex items-center gap-2 flex-wrap">
              <button @click="unlockCourseDirectly" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow">
                🔓 Unlock Course Directly
              </button>
              <button @click="escalateTicket" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-amber-300 rounded-xl border border-slate-700 font-semibold">
                ⏫ Escalate
              </button>
            </div>
          </div>

          <!-- Reply Box -->
          <div>
            <label class="text-slate-300 font-semibold block mb-1">💬 Reply to Student:</label>
            <div class="flex gap-2">
              <textarea v-model="replyText" rows="2" placeholder="Write reply to student..." class="w-full bg-slate-950 border border-slate-800 rounded p-2 text-white"></textarea>
              <button @click="sendReply" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl text-xs shrink-0">Send</button>
            </div>
          </div>
        </div>

        <div class="px-6 py-3 border-t border-slate-800 bg-slate-950 flex justify-end">
          <button @click="showDetailModal = false" class="px-5 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs">Close</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
