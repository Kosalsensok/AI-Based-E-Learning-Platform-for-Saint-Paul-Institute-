<script setup lang="ts">
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps<{
  verificationLogs: Array<any>
  publicSettings: Record<string, any>
}>()

const quickVerifyInput = ref('ELMS-2025-000451')
const quickVerifyResult = ref<any>(null)
const isVerifying = ref(false)

const runQuickVerify = () => {
  if (!quickVerifyInput.value.trim()) return
  isVerifying.value = true
  router.post('/admin/certificates/quick-verify', {
    certificate_id: quickVerifyInput.value.trim()
  }, {
    onSuccess: () => {
      isVerifying.value = false
      quickVerifyResult.value = {
        status: 'valid',
        cert_number: quickVerifyInput.value,
        student_name: 'Chan Dara',
        course_name: 'C Programming Basics',
        grade: 'A (82%)',
        issued_at: '16 June 2025',
      }
    },
    onError: () => {
      isVerifying.value = false
    }
  })
}
</script>

<template>
  <AdminLayout title="Certificate Module — Certificate Verification">
    <div class="space-y-6">
      <!-- 🏅 Header Title -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl">
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            🔍 Certificate Verification
            <span class="text-xs bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 px-2.5 py-0.5 rounded-full font-mono">
              Certificate Module
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            គ្រប់គ្រងការផ្ទៀងផ្ទាត់ — Public Page / QR / Verification Logs។
          </p>
        </div>

        <a
          href="/certificate/verify"
          target="_blank"
          class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1.5 self-start md:self-auto"
        >
          🌐 View Public Verification Portal ↗
        </a>
      </div>

      <!-- Submenu Tabs Header -->
      <div class="flex items-center gap-1 border-b border-slate-800 overflow-x-auto pb-1 custom-scrollbar">
        <Link href="/admin/certificates/templates" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🎨 Templates
        </Link>
        <Link href="/admin/certificates/issue" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🏅 Issue Certificate
        </Link>
        <Link href="/admin/certificates/issued" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📜 Issued Certificates
        </Link>
        <Link href="/admin/certificates/verify" class="px-4 py-2.5 text-xs rounded-xl border bg-indigo-500/15 text-indigo-300 border-indigo-500/40 font-semibold shadow-sm shrink-0">
          🔍 Certificate Verification
        </Link>
        <Link href="/admin/certificates/revoked" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🚫 Revoked Certificates
        </Link>
      </div>

      <!-- Quick Verify Tool Card -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h3 class="font-bold text-sm text-white flex items-center gap-2">
          🔎 QUICK VERIFY (Admin Diagnostic Tool)
        </h3>

        <div class="flex gap-3">
          <input
            v-model="quickVerifyInput"
            type="text"
            placeholder="Enter Certificate ID: ELMS-2025-000451"
            class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-xs font-mono text-white focus:outline-none focus:border-indigo-500"
          />
          <button @click="runQuickVerify" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl transition">
            🔍 Verify Now
          </button>
        </div>

        <div v-if="quickVerifyResult" class="bg-emerald-500/10 border border-emerald-500/30 p-4 rounded-xl text-xs space-y-1 text-emerald-300">
          <p class="font-bold text-sm">✅ RESULT: VALID</p>
          <p>Holder: <strong>{{ quickVerifyResult.student_name }}</strong> · Course: {{ quickVerifyResult.course_name }} · Grade: {{ quickVerifyResult.grade }}</p>
          <p>Issued: {{ quickVerifyResult.issued_at }} · Status: Active</p>
        </div>
      </div>

      <!-- Public Verification Page Settings Card -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h3 class="font-bold text-sm text-white flex items-center gap-2">
          🌐 PUBLIC VERIFICATION PAGE SETTINGS
        </h3>

        <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 text-xs space-y-3 text-slate-300">
          <p>Public URL: <code class="text-cyan-400 font-mono">https://elms.edu/verify</code></p>
          <div class="space-y-2">
            <label class="flex items-center gap-2"><input type="checkbox" checked /> Public verification page enabled (no login required)</label>
            <label class="flex items-center gap-2"><input type="checkbox" checked /> QR code scanning supported</label>
            <label class="flex items-center gap-2"><input type="checkbox" checked /> Show student name, course, grade, issue date</label>
            <label class="flex items-center gap-2"><input type="checkbox" /> Show student email (privacy consideration)</label>
            <label class="flex items-center gap-2"><input type="checkbox" checked /> Log every verification attempt (IP, location, source)</label>
          </div>
        </div>
      </div>

      <!-- Verification Activity Logs Table -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-sm text-white">VERIFICATION LOGS (Recent Verification Activity)</h3>
          <button class="px-3 py-1.5 bg-slate-800 text-slate-300 text-xs rounded-lg border border-slate-700">
            📤 Export Verification Logs
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold border-b border-slate-800">
              <tr>
                <th class="p-3">Time</th>
                <th class="p-3">Cert ID</th>
                <th class="p-3">Result</th>
                <th class="p-3">IP / Location</th>
                <th class="p-3">Source</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
              <tr v-for="log in verificationLogs" :key="log.time" class="hover:bg-slate-800/40">
                <td class="p-3 text-slate-400 font-mono text-[11px]">{{ log.time }}</td>
                <td class="p-3 font-mono font-bold text-indigo-400">{{ log.cert_id }}</td>
                <td class="p-3">
                  <span :class="{
                    'bg-emerald-500/20 text-emerald-400 border-emerald-500/30': log.result === 'valid',
                    'bg-red-500/20 text-red-400 border-red-500/30': log.result === 'revoked',
                    'bg-slate-800 text-slate-400 border-slate-700': log.result === 'not_found'
                  }" class="px-2.5 py-0.5 text-[10px] font-semibold border rounded-full">
                    {{ log.result === 'valid' ? '✅ Valid' : log.result === 'revoked' ? '🔴 Revoked' : '⚪ NotFound' }}
                  </span>
                </td>
                <td class="p-3 text-slate-400 font-mono">{{ log.ip }} ({{ log.location }})</td>
                <td class="p-3 text-slate-300">{{ log.source }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
