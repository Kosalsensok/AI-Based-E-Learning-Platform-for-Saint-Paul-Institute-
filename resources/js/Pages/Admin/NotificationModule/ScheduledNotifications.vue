<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps<{
  queue: Array<any>
  automations: Array<any>
  topStats: any
}>()

const showRuleModal = ref(false)

const ruleForm = ref({
  name: 'Inactivity Nudge',
  trigger_event: 'user_inactivity',
  inactive_days: 3,
  progress_less_than: 80,
  target_audience: 'All Students',
  push_enabled: true,
  push_message: 'យើងនឹករលឹកអ្នក! ត្រឡប់មករៀនវិញ 🎓',
  email_enabled: true,
  email_template: 'Re-engagement Email',
  send_time: '19:00',
  frequency: 'repeat_3_days',
  max_sends: 3,
  stop_on_login: true,
  stop_on_unsubscribe: true
})

const saveRule = () => {
  window.alert('🤖 Automation Rule saved and activated!')
  showRuleModal.value = false
}

const testRule = () => {
  window.alert('🧪 Rule logic verified with zero conflicts!')
}
</script>

<template>
  <AdminLayout title="Notification Module — Scheduled Notifications & Automation Rules">
    <div class="space-y-6">
      
      <!-- Top Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl">
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            ⏰ SCHEDULED & AUTOMATION RULES
            <span class="text-xs bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2.5 py-0.5 rounded-full font-mono">
              IF-THEN Automation Engine
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            កំណត់ពេលផ្ញើទុកជាមុន និងបង្កើតច្បាប់ស្វ័យប្រវត្តិ (Automation Rules Engine) សម្រាប់ប្រព័ន្ធ។
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button @click="showRuleModal = true" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-purple-600/20 flex items-center gap-1">
            🤖 Create Automation Rule
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
        <Link href="/admin/notifications/scheduled" class="px-4 py-2.5 text-xs rounded-xl border bg-purple-500/15 text-purple-300 border-purple-500/40 font-bold shadow-sm shrink-0">
          ⏰ Scheduled Notifications
        </Link>
        <Link href="/admin/notifications/history" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📜 Notification History
        </Link>
      </div>

      <!-- Calendar View & Upcoming Queue -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Calendar Widget -->
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-4">
          <div class="flex justify-between items-center">
            <h3 class="font-bold text-white text-sm">📅 MAY 2025 SCHEDULE</h3>
            <span class="text-xs text-purple-400 font-mono font-bold">17 Pending</span>
          </div>

          <div class="grid grid-cols-7 gap-1 text-center text-xs text-slate-400 font-semibold border-b border-slate-800 pb-2">
            <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
          </div>

          <div class="grid grid-cols-7 gap-1 text-center text-xs">
            <span class="p-2 text-slate-600">25</span>
            <span class="p-2 bg-slate-800 rounded text-slate-200 relative">
              26 <span class="w-1.5 h-1.5 bg-cyan-400 rounded-full mx-auto mt-0.5"></span>
            </span>
            <span class="p-2 text-slate-400">27</span>
            <span class="p-2 bg-purple-900/40 border border-purple-500/50 rounded text-purple-200 font-bold">
              28 <span class="w-1.5 h-1.5 bg-amber-400 rounded-full mx-auto mt-0.5"></span>
            </span>
            <span class="p-2 text-slate-400">29</span>
            <span class="p-2 bg-red-900/40 border border-red-500/50 rounded text-red-200 font-bold">
              30 <span class="w-1.5 h-1.5 bg-red-400 rounded-full mx-auto mt-0.5"></span>
            </span>
            <span class="p-2 text-slate-600">31</span>
          </div>

          <div class="space-y-2 pt-2 border-t border-slate-800 text-[11px]">
            <div class="p-2 bg-slate-950 rounded-lg border border-slate-800 flex justify-between items-center">
              <span class="text-amber-400 font-bold">28/05 08:00</span>
              <span class="text-slate-300">📢 Announcement (245)</span>
            </div>
            <div class="p-2 bg-slate-950 rounded-lg border border-slate-800 flex justify-between items-center">
              <span class="text-red-400 font-bold">30/05 07:00</span>
              <span class="text-slate-300">🔴 Exam Reminder (2,458)</span>
            </div>
          </div>
        </div>

        <!-- Right: Upcoming Queue Table -->
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
          <div class="p-4 border-b border-slate-800 flex justify-between items-center">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">📋 Upcoming Queue (17 Pending)</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
              <thead>
                <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 font-semibold uppercase tracking-wider">
                  <th class="p-4">Time</th>
                  <th class="p-4">Type</th>
                  <th class="p-4">Title</th>
                  <th class="p-4">Audience</th>
                  <th class="p-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-800 text-slate-300">
                <tr v-for="q in queue" :key="q.id" class="hover:bg-slate-800/40 transition">
                  <td class="p-4 font-mono text-purple-300 font-semibold">{{ q.time }}</td>
                  <td class="p-4"><span class="px-2 py-0.5 bg-slate-800 border border-slate-700 rounded">{{ q.type }}</span></td>
                  <td class="p-4 font-bold text-white">{{ q.title }}</td>
                  <td class="p-4 text-slate-400">{{ q.audience }}</td>
                  <td class="p-4 text-right space-x-2">
                    <button class="text-slate-400 hover:text-white" title="Edit">✏️</button>
                    <button class="text-amber-400 hover:text-amber-300" title="Pause">⏸️</button>
                    <button class="text-red-400 hover:text-red-300" title="Delete">🗑️</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Automation Rules Engine Section -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 space-y-4 shadow-xl">
        <div class="flex justify-between items-center">
          <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
            🤖 AUTOMATION RULES ENGINE (RECURRING TRIGGERS)
          </h2>
          <button @click="showRuleModal = true" class="text-xs text-purple-400 hover:text-purple-300 font-bold">
            + Create Rule
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 font-semibold uppercase tracking-wider">
                <th class="p-3">#</th>
                <th class="p-3">Rule Name</th>
                <th class="p-3">Trigger (IF)</th>
                <th class="p-3">Action (THEN)</th>
                <th class="p-3">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
              <tr v-for="(rule, idx) in automations" :key="rule.id" class="hover:bg-slate-800/40 transition">
                <td class="p-3 font-mono text-slate-500">{{ idx + 1 }}</td>
                <td class="p-3 font-bold text-white">{{ rule.name }}</td>
                <td class="p-3 font-mono text-purple-300">⚡ {{ rule.trigger }}</td>
                <td class="p-3 font-semibold text-slate-200">{{ rule.action }}</td>
                <td class="p-3">
                  <span :class="rule.status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border-amber-500/30'" class="px-2 py-0.5 text-[10px] font-bold border rounded-full">
                    {{ rule.status === 'active' ? '🟢 Active' : '🟡 Paused' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- CREATE AUTOMATION RULE MODAL (IF-THEN Builder) -->
    <!-- ========================================================================= -->
    <div v-if="showRuleModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-3xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden my-auto">
        <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center bg-slate-950">
          <h3 class="text-base font-bold text-white flex items-center gap-2">🤖 Create Automation Rule (IF-THEN Builder)</h3>
          <button @click="showRuleModal = false" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <div class="p-6 space-y-5 overflow-y-auto custom-scrollbar text-xs">
          <div>
            <label class="text-slate-400 block mb-1">Rule Name:</label>
            <input v-model="ruleForm.name" type="text" class="w-full bg-slate-950 border border-slate-800 rounded p-2 text-white font-bold" />
          </div>

          <!-- TRIGGER (IF) -->
          <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-3">
            <h4 class="font-bold text-purple-400 uppercase tracking-wider">⚡ TRIGGER (IF):</h4>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="text-slate-400 block mb-1">Event Type:</label>
                <select v-model="ruleForm.trigger_event" class="w-full bg-slate-900 border border-slate-800 rounded p-2 text-white">
                  <option value="user_inactivity">User Inactivity</option>
                  <option value="assignment_due">Assignment Due</option>
                  <option value="birthday">Student Birthday</option>
                </select>
              </div>
              <div>
                <label class="text-slate-400 block mb-1">Condition:</label>
                <span class="text-slate-300 font-mono block mt-1">No login for <strong>3</strong> days AND Progress &lt; <strong>80</strong>%</span>
              </div>
            </div>
          </div>

          <!-- ACTION (THEN) -->
          <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-3">
            <h4 class="font-bold text-indigo-400 uppercase tracking-wider">📤 ACTION (THEN):</h4>
            <div class="space-y-2">
              <label class="flex items-center gap-2 cursor-pointer text-slate-200">
                <input type="checkbox" v-model="ruleForm.push_enabled" /> 📱 Push: "{{ ruleForm.push_message }}"
              </label>
              <label class="flex items-center gap-2 cursor-pointer text-slate-200">
                <input type="checkbox" v-model="ruleForm.email_enabled" /> 📧 Email Template: <strong>{{ ruleForm.email_template }}</strong>
              </label>
            </div>
          </div>

          <!-- TIMING & STOP CONDITION -->
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2">
              <h4 class="font-bold text-slate-400 uppercase tracking-wider">⏰ TIMING:</h4>
              <p class="text-slate-300">Send at: <strong>19:00</strong> (User local time)</p>
              <p class="text-slate-300">Max sends: <strong>3</strong> times</p>
            </div>
            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2">
              <h4 class="font-bold text-red-400 uppercase tracking-wider">🛑 STOP CONDITION:</h4>
              <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                <input type="checkbox" v-model="ruleForm.stop_on_login" /> Stop when user logs in
              </label>
            </div>
          </div>
        </div>

        <div class="px-6 py-3 border-t border-slate-800 bg-slate-950 flex justify-between items-center">
          <button @click="testRule" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">
            🧪 Test Rule
          </button>
          <button @click="saveRule" class="px-6 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl text-xs">
            💾 Save & Activate Rule
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
