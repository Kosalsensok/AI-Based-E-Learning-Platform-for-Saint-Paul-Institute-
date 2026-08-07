<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps<{
  logs: Array<any>
  analytics: any
  topStats: any
}>()

const activeChip = ref('all')
const searchQuery = ref('')
const selectedLog = ref<any>(null)
const showDetailDrawer = ref(false)

const openDetail = (log: any) => {
  selectedLog.value = log
  showDetailDrawer.value = true
}

const resendLog = (log: any) => {
  alert(`🔄 Notification ${log.notif_code} queued for resend!`)
}

const filteredLogs = computed(() => {
  return props.logs.filter(item => {
    const matchesSearch = item.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          item.recipient_name.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchesSearch
  })
})
</script>

<template>
  <AdminLayout title="Notification Module — Notification History & Audit Log">
    <div class="space-y-6">
      
      <!-- Top Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl">
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            📜 NOTIFICATION HISTORY & AUDIT LOG
            <span class="text-xs bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-2.5 py-0.5 rounded-full font-mono">
              Full Delivery Audit
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            តាមដានរាល់សារដែលបានផ្ញើ ស្ថានភាពបញ្ជូន លទ្ធផល និងវិភាគប្រសិទ្ធភាពសរុប។
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl border border-slate-700 flex items-center gap-1">
            📤 Export Log (CSV/PDF)
          </button>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-1 border-b border-slate-800 overflow-x-auto pb-1 custom-scrollbar">
        <Link href="/admin/notifications/announcements" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📢 Announcements
        </Link>
        <Link href="/admin/notifications/emails" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📧 Email Notifications
        </Link>
        <Link href="/admin/notifications/push" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📱 Push Notifications
        </Link>
        <Link href="/admin/notifications/scheduled" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          ⏰ Scheduled Notifications
        </Link>
        <Link href="/admin/notifications/history" class="px-4 py-2.5 text-xs rounded-xl border bg-emerald-500/15 text-emerald-300 border-emerald-500/40 font-bold shadow-sm shrink-0">
          📜 Notification History
        </Link>
      </div>

      <!-- Filter Chips Bar -->
      <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-3 text-xs">
        <div class="flex items-center gap-2 flex-wrap">
          <button @click="activeChip = 'all'" :class="activeChip === 'all' ? 'bg-slate-800 text-white font-bold' : 'text-slate-400'" class="px-3 py-1 rounded-lg border border-slate-800">All 28,450</button>
          <button @click="activeChip = 'email'" :class="activeChip === 'email' ? 'bg-slate-800 text-cyan-300 font-bold' : 'text-slate-400'" class="px-3 py-1 rounded-lg border border-slate-800">📧 Email 18,450</button>
          <button @click="activeChip = 'push'" :class="activeChip === 'push' ? 'bg-slate-800 text-indigo-300 font-bold' : 'text-slate-400'" class="px-3 py-1 rounded-lg border border-slate-800">📱 Push 12,450</button>
          <button @click="activeChip = 'in_app'" :class="activeChip === 'in_app' ? 'bg-slate-800 text-amber-300 font-bold' : 'text-slate-400'" class="px-3 py-1 rounded-lg border border-slate-800">🔔 In-App 8,920</button>
          <button @click="activeChip = 'delivered'" :class="activeChip === 'delivered' ? 'bg-emerald-500/20 text-emerald-300 font-bold' : 'text-slate-400'" class="px-3 py-1 rounded-lg border border-emerald-500/30">✅ Delivered 27,540</button>
          <button @click="activeChip = 'failed'" :class="activeChip === 'failed' ? 'bg-red-500/20 text-red-300 font-bold' : 'text-slate-400'" class="px-3 py-1 rounded-lg border border-red-500/30">🔴 Failed 142</button>
        </div>

        <div class="flex justify-between items-center">
          <input v-model="searchQuery" type="text" placeholder="🔍 Search history logs by title, recipient..." class="w-full md:w-80 bg-slate-950 border border-slate-800 rounded-lg p-2 text-white" />
        </div>
      </div>

      <!-- Logs Table -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 font-semibold uppercase tracking-wider">
                <th class="p-4">Time</th>
                <th class="p-4">Channel</th>
                <th class="p-4">Title</th>
                <th class="p-4">Recipient</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
              <tr v-for="log in filteredLogs" :key="log.notif_code" class="hover:bg-slate-800/40 transition">
                <td class="p-4 font-mono text-slate-400 text-[11px]">{{ log.time }}</td>
                <td class="p-4 font-semibold text-slate-200">{{ log.channel }}</td>
                <td class="p-4 font-bold text-white">{{ log.title }}</td>
                <td class="p-4 text-slate-300">{{ log.recipient_name }}</td>
                <td class="p-4 font-bold">
                  <span :class="log.status_key === 'failed' ? 'text-red-400' : 'text-emerald-400'">
                    {{ log.status }}
                  </span>
                </td>
                <td class="p-4 text-right space-x-2">
                  <button @click="openDetail(log)" class="p-1.5 hover:bg-slate-800 text-slate-200 rounded" title="View Audit Detail">👁️</button>
                  <button @click="resendLog(log)" class="p-1.5 hover:bg-slate-800 text-cyan-400 rounded" title="Resend">🔄</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Bottom Analytics Dashboard Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
        
        <!-- 📈 Delivery Trend & Best Send Times -->
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-4">
          <h3 class="font-bold text-white text-sm uppercase tracking-wider">⏰ Best Send Times (AI Heatmap)</h3>
          <div class="space-y-2">
            <div class="p-2.5 bg-slate-950 rounded-xl border border-slate-800 flex justify-between items-center">
              <span>Mon-Fri 19:00 - 21:00</span>
              <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 font-bold border border-emerald-500/30 rounded">🟢 Best (92% Open)</span>
            </div>
            <div class="p-2.5 bg-slate-950 rounded-xl border border-slate-800 flex justify-between items-center">
              <span>Sat-Sun 10:00 - 12:00</span>
              <span class="px-2 py-0.5 bg-amber-500/20 text-amber-400 font-bold border border-amber-500/30 rounded">🟡 Good (76% Open)</span>
            </div>
            <div class="p-2.5 bg-slate-950 rounded-xl border border-slate-800 flex justify-between items-center">
              <span>Daily 23:00 - 06:00</span>
              <span class="px-2 py-0.5 bg-red-500/20 text-red-400 font-bold border border-red-500/30 rounded">🔴 Avoid (Quiet Hours)</span>
            </div>
          </div>
        </div>

        <!-- 🔴 Failure Reasons Breakdown -->
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-4">
          <h3 class="font-bold text-white text-sm uppercase tracking-wider">🔴 Delivery Failure Reasons</h3>
          <div class="space-y-2.5">
            <div v-for="f in analytics.failure_reasons" :key="f.reason" class="p-2.5 bg-slate-950 rounded-xl border border-slate-800 flex justify-between items-center">
              <span class="text-slate-300 font-medium">{{ f.reason }}</span>
              <span class="text-red-400 font-mono font-bold">{{ f.count }} ({{ f.pct }})</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- NOTIFICATION DETAIL AUDIT DRAWER -->
    <!-- ========================================================================= -->
    <div v-if="showDetailDrawer" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex justify-end">
      <div class="bg-slate-900 border-l border-slate-800 w-full max-w-xl h-full p-6 space-y-6 overflow-y-auto custom-scrollbar shadow-2xl">
        <div class="flex justify-between items-center border-b border-slate-800 pb-3">
          <div>
            <h3 class="font-bold text-white text-base">📜 Notification Audit Detail</h3>
            <p class="text-xs text-slate-400 font-mono">{{ selectedLog?.notif_code || 'NOTIF-2025-28450' }}</p>
          </div>
          <button @click="showDetailDrawer = false" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <!-- Basic Info -->
        <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2 text-xs">
          <h4 class="font-bold text-emerald-400 uppercase tracking-wider">📋 Basic Info:</h4>
          <p class="text-slate-300">Type: <strong>{{ selectedLog?.channel }} (Transactional)</strong></p>
          <p class="text-slate-300">Title: <strong>"{{ selectedLog?.title }}"</strong></p>
          <p class="text-slate-300">Sent Time: <strong>{{ selectedLog?.time }}</strong></p>
        </div>

        <!-- Recipient -->
        <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2 text-xs">
          <h4 class="font-bold text-cyan-400 uppercase tracking-wider">👤 Recipient Details:</h4>
          <p class="text-slate-300">Name: <strong>{{ selectedLog?.recipient_name }}</strong></p>
          <p class="text-slate-300">Email: <strong>{{ selectedLog?.recipient_email }}</strong></p>
          <p class="text-slate-300">Device: <strong>{{ selectedLog?.device || 'iPhone 15 / iOS 17' }}</strong></p>
        </div>

        <!-- Delivery Timeline -->
        <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-3 text-xs">
          <h4 class="font-bold text-amber-400 uppercase tracking-wider">📊 Delivery Timeline:</h4>
          <div class="space-y-2 font-mono text-[11px]">
            <div class="flex items-center gap-2 text-slate-400"><span>10:45:02</span> <span>→</span> <span class="text-slate-200">📤 Queued in DB</span></div>
            <div class="flex items-center gap-2 text-slate-400"><span>10:45:04</span> <span>→</span> <span class="text-slate-200">✅ Sent to SMTP (Mailgun)</span></div>
            <div class="flex items-center gap-2 text-slate-400"><span>10:45:07</span> <span>→</span> <span class="text-emerald-400">✅ Delivered to inbox</span></div>
            <div class="flex items-center gap-2 text-slate-400"><span>10:52:18</span> <span>→</span> <span class="text-cyan-400">👁️ Opened (iPhone Mail)</span></div>
            <div class="flex items-center gap-2 text-slate-400"><span>10:52:45</span> <span>→</span> <span class="text-amber-400">🔗 Clicked "START LEARNING" button</span></div>
          </div>
        </div>

        <div class="flex justify-between items-center pt-3 border-t border-slate-800 text-xs">
          <button @click="resendLog(selectedLog)" class="px-4 py-2 bg-slate-800 text-cyan-300 font-bold rounded-xl">
            🔄 Resend Notification
          </button>
          <button @click="showDetailDrawer = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl">
            Close
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
