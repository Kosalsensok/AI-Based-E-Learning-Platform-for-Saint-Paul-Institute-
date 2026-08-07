<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps<{
  templates: Array<any>
  smtp: any
  topStats: any
}>()

// Editor State
const showTemplateModal = ref(false)
const showSmtpModal = ref(false)
const activeTemplate = ref<any>(null)

// Editing template form
const templateForm = ref({
  id: 2,
  name: '💳 Payment Receipt',
  trigger_event: 'payment.success',
  subject_kh: 'បង្កាន់ដៃទូទាត់ប្រាក់ — {{course_name}}',
  subject_en: 'Payment Receipt — {{course_name}}',
  from_name: 'E.LMS Education',
  reply_to: 'support@elms.com',
  attach_pdf_receipt: true,
  send_delay: 'immediate',
  html_body: `
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #e2e8f0; rounded: 12px;">
  <div style="text-align: center; margin-bottom: 20px;">
    <h2 style="color: #1e3a8a;">🎓 E.LMS Education</h2>
  </div>
  <p>ជម្រាបសួរ <strong>{{student_name}}</strong>,</p>
  <p>យើងបានទទួលការទូទាត់របស់អ្នកដោយជោគជ័យ។</p>
  
  <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; margin: 15px 0;">
    <p style="margin: 4px 0;"><strong>Course:</strong> {{course_name}}</p>
    <p style="margin: 4px 0;"><strong>Amount:</strong> {{amount}}</p>
    <p style="margin: 4px 0;"><strong>TXN ID:</strong> {{transaction_id}}</p>
    <p style="margin: 4px 0;"><strong>Date:</strong> {{payment_date}}</p>
  </div>
  
  <div style="text-align: center; margin-top: 20px;">
    <a href="#" style="background-color: #0284c7; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; inline-block;">🎓 START LEARNING NOW</a>
  </div>
</div>
  `
})

// Editing SMTP form
const smtpForm = ref({ ...props.smtp })

const availableVars = [
  '{{student_name}}', '{{student_id}}', '{{course_name}}', '{{amount}}',
  '{{transaction_id}}', '{{payment_date}}', '{{teacher_name}}', '{{receipt_url}}'
]

const insertVariable = (v: string) => {
  templateForm.value.subject_kh += ' ' + v
}

const openTemplateEditor = (tpl?: any) => {
  if (tpl) {
    activeTemplate.value = tpl
    templateForm.value.name = tpl.name
    templateForm.value.trigger_event = tpl.trigger_event
  }
  showTemplateModal.value = true
}

const sendTestEmail = () => {
  window.alert('📧 Test email sent to admin@elms.com!')
}

const saveSmtp = () => {
  window.alert('⚙️ SMTP settings updated and verified!')
  showSmtpModal.value = false
}

const testConnection = () => {
  window.alert('🔄 Test Connection OK!')
}
</script>

<template>
  <AdminLayout title="Notification Module — Email Notifications">
    <div class="space-y-6">
      
      <!-- Top Banner -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl">
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            📧 EMAIL NOTIFICATIONS
            <span class="text-xs bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 px-2.5 py-0.5 rounded-full font-mono">
              Transactional Templates
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            គ្រប់គ្រងគំរូអ៊ីមែលស្វ័យប្រវត្តិ (Transactional Emails) និងកំណត់រចនាសម្ព័ន្ធ SMTP Server។
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button @click="showSmtpModal = true" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl border border-slate-700 flex items-center gap-1">
            ⚙️ SMTP Settings
          </button>
          <button @click="openTemplateEditor()" class="px-4 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-cyan-600/20 flex items-center gap-1">
            ➕ New Template
          </button>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-1 border-b border-slate-800 overflow-x-auto pb-1 custom-scrollbar">
        <Link href="/admin/notifications/announcements" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📢 Announcements
        </Link>
        <Link href="/admin/notifications/emails" class="px-4 py-2.5 text-xs rounded-xl border bg-cyan-500/15 text-cyan-300 border-cyan-500/40 font-bold shadow-sm shrink-0">
          📧 Email Notifications
        </Link>
        <Link href="/admin/notifications/push" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📱 Push Notifications
        </Link>
        <Link href="/admin/notifications/scheduled" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          ⏰ Scheduled Notifications
        </Link>
        <Link href="/admin/notifications/history" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📜 Notification History
        </Link>
      </div>

      <!-- 30-Day Delivery Stats Panel -->
      <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-3">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">📊 30-Day Delivery Performance</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 text-xs">
          <div class="bg-slate-950 p-3 rounded-xl border border-slate-800"><span class="text-slate-400 block">Sent</span><strong class="text-base text-white">18,450</strong></div>
          <div class="bg-slate-950 p-3 rounded-xl border border-slate-800"><span class="text-slate-400 block">Delivered</span><strong class="text-base text-emerald-400">96.8%</strong></div>
          <div class="bg-slate-950 p-3 rounded-xl border border-slate-800"><span class="text-slate-400 block">Opened</span><strong class="text-base text-cyan-400">68.2%</strong></div>
          <div class="bg-slate-950 p-3 rounded-xl border border-slate-800"><span class="text-slate-400 block">Bounced</span><strong class="text-base text-amber-400">2.1%</strong></div>
          <div class="bg-slate-950 p-3 rounded-xl border border-slate-800"><span class="text-slate-400 block">Failed</span><strong class="text-base text-red-400">1.1%</strong></div>
          <div class="bg-slate-950 p-3 rounded-xl border border-slate-800"><span class="text-slate-400 block">Unsubscribed</span><strong class="text-base text-slate-400">0.3%</strong></div>
        </div>
      </div>

      <!-- Template Library Table -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-4 border-b border-slate-800 flex justify-between items-center">
          <h2 class="text-sm font-bold text-white uppercase tracking-wider">Email Template Library</h2>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 font-semibold uppercase tracking-wider">
                <th class="p-4">Template Name</th>
                <th class="p-4">Trigger Event</th>
                <th class="p-4">Total Sent</th>
                <th class="p-4">Open Rate</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
              <tr v-for="t in templates" :key="t.id" class="hover:bg-slate-800/40 transition">
                <td class="p-4 font-bold text-white flex items-center gap-2">
                  {{ t.name }}
                </td>
                <td class="p-4 font-mono text-[11px] text-cyan-400">
                  ⚡ {{ t.trigger_label || t.trigger_event }}
                </td>
                <td class="p-4 font-mono text-slate-200">{{ t.sent_count.toLocaleString() }}</td>
                <td class="p-4">
                  <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 font-bold border border-emerald-500/20 rounded">
                    {{ t.open_rate }}
                  </span>
                </td>
                <td class="p-4">
                  <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-full font-semibold">
                    🟢 Active
                  </span>
                </td>
                <td class="p-4 text-right space-x-2">
                  <button @click="openTemplateEditor(t)" class="text-cyan-400 hover:text-cyan-300 font-semibold">✏️ Edit</button>
                  <button @click="sendTestEmail" class="text-slate-400 hover:text-white">📧 Test</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- EDIT EMAIL TEMPLATE MODAL -->
    <!-- ========================================================================= -->
    <div v-if="showTemplateModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden my-auto">
        <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center bg-slate-950">
          <h3 class="text-base font-bold text-white">✏️ Edit Email Template — {{ templateForm.name }}</h3>
          <button @click="showTemplateModal = false" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <div class="p-6 space-y-5 overflow-y-auto custom-scrollbar text-xs">
          <div class="bg-slate-950 border border-slate-800 p-3 rounded-lg flex items-center justify-between text-slate-300 font-mono">
            <span>Trigger Event: <strong class="text-cyan-400">{{ templateForm.trigger_event }}</strong></span>
            <span class="text-slate-500 text-[11px]">(ABA Transaction Confirmed)</span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="text-slate-300 font-semibold block mb-1">Subject (KH):</label>
              <input v-model="templateForm.subject_kh" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white" />
            </div>
            <div>
              <label class="text-slate-300 font-semibold block mb-1">Subject (EN):</label>
              <input v-model="templateForm.subject_en" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white" />
            </div>
          </div>

          <!-- Variables Palette -->
          <div>
            <label class="text-slate-400 block mb-1">🏷️ Available Variables (Click to insert):</label>
            <div class="flex items-center gap-1.5 flex-wrap">
              <button
                v-for="v in availableVars"
                :key="v"
                @click="insertVariable(v)"
                class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-cyan-300 border border-slate-700 rounded font-mono text-[11px]"
              >
                {{ v }}
              </button>
            </div>
          </div>

          <!-- Live HTML Code Body -->
          <div>
            <label class="text-slate-300 font-semibold block mb-1">📄 HTML Body:</label>
            <textarea v-model="templateForm.html_body" rows="8" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-3 text-emerald-400 font-mono text-[11px]"></textarea>
          </div>

          <!-- Template Settings -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-950 p-4 rounded-xl border border-slate-800">
            <div>
              <label class="text-slate-400 block mb-1">From Name:</label>
              <input v-model="templateForm.from_name" type="text" class="w-full bg-slate-900 border border-slate-800 rounded p-1.5 text-white" />
            </div>
            <div>
              <label class="text-slate-400 block mb-1">Reply-To:</label>
              <input v-model="templateForm.reply_to" type="text" class="w-full bg-slate-900 border border-slate-800 rounded p-1.5 text-white" />
            </div>
            <div>
              <label class="text-slate-400 block mb-1">PDF Receipt:</label>
              <label class="flex items-center gap-1.5 text-slate-200 mt-1.5 cursor-pointer">
                <input type="checkbox" v-model="templateForm.attach_pdf_receipt" /> Attach PDF
              </label>
            </div>
            <div>
              <label class="text-slate-400 block mb-1">Send Delay:</label>
              <select v-model="templateForm.send_delay" class="w-full bg-slate-900 border border-slate-800 rounded p-1.5 text-white">
                <option value="immediate">Immediate</option>
                <option value="5m">5 Minutes</option>
              </select>
            </div>
          </div>
        </div>

        <div class="px-6 py-3 border-t border-slate-800 bg-slate-950 flex justify-between items-center">
          <button @click="sendTestEmail" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">
            📧 Send Test Email
          </button>
          <button @click="showTemplateModal = false" class="px-6 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold rounded-xl text-xs">
            💾 Save Template
          </button>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SMTP SETTINGS MODAL -->
    <!-- ========================================================================= -->
    <div v-if="showSmtpModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-xl p-6 space-y-5 shadow-2xl">
        <div class="flex justify-between items-center border-b border-slate-800 pb-3">
          <h3 class="font-bold text-white text-base">⚙️ SMTP Configuration Panel</h3>
          <button @click="showSmtpModal = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-4 text-xs">
          <div>
            <label class="text-slate-400 block mb-1">Provider:</label>
            <div class="flex items-center gap-3">
              <label class="flex items-center gap-1 text-slate-200"><input type="radio" v-model="smtpForm.provider" value="gmail" /> Gmail</label>
              <label class="flex items-center gap-1 text-slate-200"><input type="radio" v-model="smtpForm.provider" value="sendgrid" /> SendGrid</label>
              <label class="flex items-center gap-1 text-slate-200"><input type="radio" v-model="smtpForm.provider" value="mailgun" /> Mailgun</label>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-slate-400 block mb-1">Host:</label>
              <input v-model="smtpForm.host" type="text" class="w-full bg-slate-950 border border-slate-800 rounded p-2 text-white font-mono" />
            </div>
            <div>
              <label class="text-slate-400 block mb-1">Port & Encryption:</label>
              <div class="flex gap-2">
                <input v-model="smtpForm.port" type="number" class="w-1/2 bg-slate-950 border border-slate-800 rounded p-2 text-white font-mono" />
                <select v-model="smtpForm.encryption" class="w-1/2 bg-slate-950 border border-slate-800 rounded p-2 text-white">
                  <option value="tls">TLS</option>
                  <option value="ssl">SSL</option>
                </select>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-slate-400 block mb-1">Username:</label>
              <input v-model="smtpForm.username" type="text" class="w-full bg-slate-950 border border-slate-800 rounded p-2 text-white font-mono" />
            </div>
            <div>
              <label class="text-slate-400 block mb-1">Password:</label>
              <input type="password" value="secret_password" class="w-full bg-slate-950 border border-slate-800 rounded p-2 text-white font-mono" />
            </div>
          </div>

          <!-- Usage Bar -->
          <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl space-y-1.5">
            <div class="flex justify-between text-slate-400">
              <span>Daily Limit Usage:</span>
              <span class="text-cyan-400 font-bold">1,245 / 10,000 (12%)</span>
            </div>
            <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
              <div class="w-[12%] h-full bg-cyan-400 rounded-full"></div>
            </div>
          </div>
        </div>

        <div class="flex justify-between items-center pt-3 border-t border-slate-800">
          <button @click="testConnection" class="px-4 py-2 bg-slate-800 text-slate-200 font-semibold rounded-xl text-xs">
            🔄 Test Connection
          </button>
          <button @click="saveSmtp" class="px-6 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl text-xs">
            💾 Save Settings
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
