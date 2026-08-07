<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps<{
  reports: Array<any>
  topStats: any
}>()

const activeFilter = ref('all')
const showModerationModal = ref(false)
const selectedReport = ref<any>(null)

const moderationForm = ref({
  decision_type: 'violation',
  hide_content: true,
  send_warning: true,
  suspend_author: false,
  suspend_days: 3,
  admin_note: 'User used harassing language violating community guidelines.',
  notify_reporter: true
})

const openModerationModal = (rpt?: any) => {
  if (rpt) {
    selectedReport.value = rpt
  }
  showModerationModal.value = true
}

const confirmModeration = () => {
  window.alert('⚖️ Action executed & notifications sent to reporter and author!')
  showModerationModal.value = false
}

const dismissReport = () => {
  window.alert('❌ Report dismissed cleanly!')
}
</script>

<template>
  <AdminLayout title="Discussions & Support — Reported Content Queue">
    <div class="space-y-6">
      
      <!-- Top Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl">
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            🚩 REPORTED CONTENT REVIEW QUEUE
            <span class="text-xs bg-red-500/20 text-red-300 border border-red-500/30 px-2.5 py-0.5 rounded-full font-mono">
              AI Moderation Guard
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            ត្រួតពិនិត្យមាតិកាមិនសមរម្យ Spam ការបន្លំ Cheat Code និងការពារសុវត្ថិភាពសហគមន៍។
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button @click="openModerationModal()" class="px-4 py-2 bg-gradient-to-r from-red-600 to-amber-600 hover:from-red-500 hover:to-amber-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-red-600/20">
            ⚖️ Moderate Queue
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
        <Link href="/admin/discussions/tickets" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🎫 Support Tickets
        </Link>
        <Link href="/admin/discussions/reports" class="px-4 py-2.5 text-xs rounded-xl border bg-red-500/15 text-red-300 border-red-500/40 font-bold shadow-sm shrink-0">
          🚩 Reported Content
        </Link>
      </div>

      <!-- Filter Chips -->
      <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl flex items-center gap-2 flex-wrap text-xs">
        <span class="text-slate-400 font-semibold mr-2">Filter Reason:</span>
        <button @click="activeFilter = 'all'" :class="activeFilter === 'all' ? 'bg-red-500/20 text-red-300 border-red-500/40 font-bold' : 'text-slate-400'" class="px-3 py-1 rounded-lg border border-slate-800">All 28</button>
        <button @click="activeFilter = 'spam'" :class="activeFilter === 'spam' ? 'bg-red-500/20 text-red-300 border-red-500/40 font-bold' : 'text-slate-400'" class="px-3 py-1 rounded-lg border border-slate-800">Spam</button>
        <button @click="activeFilter = 'harassment'" :class="activeFilter === 'harassment' ? 'bg-red-500/20 text-red-300 border-red-500/40 font-bold' : 'text-slate-400'" class="px-3 py-1 rounded-lg border border-slate-800">💬 Harassment</button>
        <button @click="activeFilter = 'cheating'" :class="activeFilter === 'cheating' ? 'bg-red-500/20 text-red-300 border-red-500/40 font-bold' : 'text-slate-400'" class="px-3 py-1 rounded-lg border border-slate-800">📝 Cheating</button>
        <button @click="activeFilter = 'copyright'" :class="activeFilter === 'copyright' ? 'bg-red-500/20 text-red-300 border-red-500/40 font-bold' : 'text-slate-400'" class="px-3 py-1 rounded-lg border border-slate-800">Copyright</button>
      </div>

      <!-- Reports List -->
      <div class="space-y-4">
        <div v-for="r in reports" :key="r.report_code" class="bg-slate-900 border border-slate-800 rounded-2xl p-5 space-y-3">
          <div class="flex justify-between items-start text-xs">
            <div class="space-y-0.5">
              <span class="font-mono text-red-400 font-bold">Report #{{ r.report_code }}</span>
              <p class="text-slate-400">Reported by: <strong>{{ r.reported_by }}</strong> • {{ r.time_ago }}</p>
            </div>

            <span class="px-2.5 py-1 bg-red-500/20 text-red-300 border border-red-500/30 rounded-full font-bold">
              {{ r.reason }}
            </span>
          </div>

          <!-- Reported Content Box -->
          <div class="bg-slate-950 border border-slate-800 p-4 rounded-xl space-y-2 text-xs">
            <div class="flex justify-between items-center text-[11px]">
              <span class="text-slate-400">💬 {{ r.content_type }} by <strong>{{ r.author_name }}</strong></span>
              <span class="text-red-400 font-mono font-bold bg-red-500/10 border border-red-500/20 px-2 py-0.5 rounded">
                🤖 Toxic Score: {{ r.toxic_score }}% 🔴 High
              </span>
            </div>

            <p class="text-slate-200 font-sans italic">"{{ r.comment_text }}"</p>
            <div class="text-[11px] text-indigo-400">Link: <span class="underline cursor-pointer">{{ r.context_link }}</span></div>
          </div>

          <div class="pt-2 border-t border-slate-800 flex items-center justify-between flex-wrap gap-2 text-xs">
            <div class="flex items-center gap-2">
              <button @click="openModerationModal(r)" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold rounded-lg">
                👁 Hide Content
              </button>
              <button @click="openModerationModal(r)" class="px-3 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-300 border border-red-500/30 font-bold rounded-lg">
                🗑️ Delete Content
              </button>
              <button @click="openModerationModal(r)" class="px-3 py-1.5 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 font-bold rounded-lg">
                ⚠️ Warn User
              </button>
            </div>

            <button @click="dismissReport" class="text-slate-400 hover:text-white">
              ❌ Dismiss Report
            </button>
          </div>
        </div>
      </div>

      <!-- Bottom Audit Log Section -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 space-y-3 text-xs">
        <h3 class="font-bold text-slate-300 uppercase tracking-wider">📜 Moderation Audit Log History</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 font-semibold">
                <th class="p-3">Date</th>
                <th class="p-3">Action Taken</th>
                <th class="p-3">Content</th>
                <th class="p-3">By Moderator</th>
                <th class="p-3">Reason</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
              <tr class="hover:bg-slate-800/40">
                <td class="p-3 font-mono">26/05 11:00</td>
                <td class="p-3 font-bold text-red-400">Content Hidden</td>
                <td class="p-3">Comment by UserX</td>
                <td class="p-3">Admin User</td>
                <td class="p-3 text-slate-400">Harassment (Toxic 85%)</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODERATION ACTIONS MODAL -->
    <!-- ========================================================================= -->
    <div v-if="showModerationModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-2xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden my-auto">
        <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center bg-slate-950">
          <h3 class="text-base font-bold text-white">⚖️ Moderate Report — {{ selectedReport?.report_code || 'RPT-021' }}</h3>
          <button @click="showModerationModal = false" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <div class="p-6 space-y-4 overflow-y-auto custom-scrollbar text-xs">
          <div>
            <label class="text-slate-400 block mb-1">Decision:</label>
            <div class="space-y-2">
              <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                <input type="radio" v-model="moderationForm.decision_type" value="dismiss" /> ⭕ No violation – Dismiss report
              </label>
              <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                <input type="radio" v-model="moderationForm.decision_type" value="violation" /> ✅ Violation confirmed – Take action:
              </label>
            </div>
          </div>

          <div v-if="moderationForm.decision_type === 'violation'" class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2">
            <label class="flex items-center gap-2 text-slate-200 cursor-pointer">
              <input type="checkbox" v-model="moderationForm.hide_content" /> ☑️ Hide comment immediately
            </label>
            <label class="flex items-center gap-2 text-slate-200 cursor-pointer">
              <input type="checkbox" v-model="moderationForm.send_warning" /> ☑️ Send warning to author
            </label>
            <label class="flex items-center gap-2 text-slate-200 cursor-pointer">
              <input type="checkbox" v-model="moderationForm.suspend_author" /> ☐ Suspend author for <strong>3</strong> days
            </label>
          </div>

          <div>
            <label class="text-slate-300 font-semibold block mb-1">Admin Note:</label>
            <textarea v-model="moderationForm.admin_note" rows="3" class="w-full bg-slate-950 border border-slate-800 rounded p-2 text-white"></textarea>
          </div>

          <div class="pt-2 border-t border-slate-800">
            <label class="flex items-center gap-2 text-slate-200 cursor-pointer">
              <input type="checkbox" v-model="moderationForm.notify_reporter" /> 📧 Send notification to reporter: "Thank you, we have taken action"
            </label>
          </div>
        </div>

        <div class="px-6 py-3 border-t border-slate-800 bg-slate-950 flex justify-end">
          <button @click="confirmModeration" class="px-6 py-2 bg-gradient-to-r from-red-600 to-amber-600 hover:from-red-500 hover:to-amber-500 text-white font-bold rounded-xl text-xs shadow-lg">
            ✔️ Confirm Action
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
