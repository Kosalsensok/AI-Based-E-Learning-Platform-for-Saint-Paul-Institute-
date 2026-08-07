<script setup lang="ts">
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps<{
  certificates?: Array<any>
}>()

const showRevokeModal = ref(false)
const revokeTargetCert = ref<any>(null)
const revokeForm = ref({
  cert_id: 'ELMS-2025-000412',
  reason: 'Plagiarism detected',
  evidence_file: 'plagiarism_report.pdf',
  note: 'Assignment post-test submission was copied from another student.',
  notify_student: true,
  show_publicly: true,
  revoke_course_completion: true,
  auth_code: '',
})

const openRevokeModal = (cert?: any) => {
  if (cert) {
    revokeTargetCert.value = cert
    revokeForm.value.cert_id = cert.certificate_number
  }
  showRevokeModal.value = true
}

const submitRevocation = () => {
  if (!revokeForm.value.auth_code) {
    alert('Please enter 2FA / Approval authorization code.')
    return
  }
  const certId = revokeTargetCert.value ? revokeTargetCert.value.id : 5
  router.post(`/admin/certificates/revoke/${certId}`, revokeForm.value, {
    onSuccess: () => {
      showRevokeModal.value = false
      alert('Certificate revoked successfully.')
    }
  })
}

const restoreCertificate = (certId: number) => {
  if (confirm('Are you sure you want to restore this certificate back to VALID status?')) {
    router.post(`/admin/certificates/restore/${certId}`, {}, {
      onSuccess: () => {
        alert('Certificate restored back to VALID status.')
      }
    })
  }
}
</script>

<template>
  <AdminLayout title="Certificate Module — Revoked Certificates">
    <div class="space-y-6">
      <!-- 🏅 Header Title -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl">
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            🚫 Revoked Certificates
            <span class="text-xs bg-red-500/20 text-red-300 border border-red-500/30 px-2.5 py-0.5 rounded-full font-mono">
              Certificate Module
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            គ្រប់គ្រងការដកហូតវិញ្ញាបនបត្រ — Request → Approve → Revoke → Notify & Restore Workflow।
          </p>
        </div>

        <button
          @click="openRevokeModal()"
          class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-red-600/30 transition flex items-center gap-1.5 self-start md:self-auto"
        >
          ➕ Request Revocation
        </button>
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
        <Link href="/admin/certificates/verify" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🔍 Certificate Verification
        </Link>
        <Link href="/admin/certificates/revoked" class="px-4 py-2.5 text-xs rounded-xl border bg-red-500/15 text-red-300 border-red-500/40 font-semibold shadow-sm shrink-0">
          🚫 Revoked Certificates
        </Link>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">🚫 Total Revoked</span>
          <p class="text-2xl font-extrabold text-red-400">17 Certificates</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">⏳ Pending Requests</span>
          <p class="text-2xl font-extrabold text-amber-400">3 Requests</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">✅ Active Revoked</span>
          <p class="text-2xl font-extrabold text-white">14 Active</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">↩️ Restored</span>
          <p class="text-2xl font-extrabold text-emerald-400">2 Restored</p>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold border-b border-slate-800">
              <tr>
                <th class="p-3.5">Cert ID</th>
                <th class="p-3.5">Student</th>
                <th class="p-3.5">Course</th>
                <th class="p-3.5">Reason</th>
                <th class="p-3.5">Revoked Date</th>
                <th class="p-3.5">Revoked By</th>
                <th class="p-3.5 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
              <tr class="hover:bg-slate-800/40">
                <td class="p-3.5 font-mono text-red-400 font-bold">ELMS-2025-000412</td>
                <td class="p-3.5 font-semibold text-white">Sok Chanra</td>
                <td class="p-3.5 text-slate-300">Tourism Basics</td>
                <td class="p-3.5 text-red-300 font-semibold">Plagiarism detected</td>
                <td class="p-3.5 text-slate-400">10 Jun 2025</td>
                <td class="p-3.5 text-slate-400">Admin A</td>
                <td class="p-3.5 text-right space-x-2">
                  <button @click="restoreCertificate(5)" class="px-2.5 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-lg text-[11px] font-semibold hover:bg-emerald-500/30">
                    ↩ Restore
                  </button>
                </td>
              </tr>
              <tr class="hover:bg-slate-800/40">
                <td class="p-3.5 font-mono text-red-400 font-bold">ELMS-2025-000380</td>
                <td class="p-3.5 font-semibold text-white">Unknown X</td>
                <td class="p-3.5 text-slate-300">Social Work</td>
                <td class="p-3.5 text-red-300 font-semibold">Fraud detected</td>
                <td class="p-3.5 text-slate-400">05 Jun 2025</td>
                <td class="p-3.5 text-slate-400">Admin B</td>
                <td class="p-3.5 text-right space-x-2">
                  <button @click="restoreCertificate(6)" class="px-2.5 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-lg text-[11px] font-semibold hover:bg-emerald-500/30">
                    ↩ Restore
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Revocation Modal -->
    <div v-if="showRevokeModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 space-y-6 shadow-2xl">
        <div class="flex justify-between items-center border-b border-slate-800 pb-4">
          <h3 class="font-bold text-base text-red-400">🚫 REQUEST REVOCATION</h3>
          <button @click="showRevokeModal = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="text-slate-400 block mb-1">Target Certificate ID:</label>
            <input v-model="revokeForm.cert_id" type="text" readonly class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 font-mono text-red-400 font-bold" />
          </div>

          <div>
            <label class="text-slate-400 block mb-1">Revocation Reason:</label>
            <select v-model="revokeForm.reason" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white">
              <option value="Plagiarism detected">Plagiarism detected</option>
              <option value="Fraud detected">Fraud detected</option>
              <option value="Full Refund">Full Refund</option>
              <option value="Data Error">Data Error</option>
              <option value="Academic Violation">Academic Violation</option>
            </select>
          </div>

          <div>
            <label class="text-slate-400 block mb-1">Evidence Attachment:</label>
            <div class="flex items-center gap-2">
              <span class="text-slate-300 font-mono text-[11px] bg-slate-950 px-2 py-1 rounded border border-slate-800">📎 plagiarism_report.pdf</span>
              <button type="button" class="text-indigo-400 hover:underline">＋ Upload</button>
            </div>
          </div>

          <div>
            <label class="text-slate-400 block mb-1">Detailed Explanation Note:</label>
            <textarea v-model="revokeForm.note" rows="3" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white"></textarea>
          </div>

          <div>
            <label class="text-slate-400 block mb-1">2FA / Authorization Approval Code:</label>
            <input v-model="revokeForm.auth_code" type="password" placeholder="••••••" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white font-mono" />
          </div>
        </div>

        <div class="flex justify-between items-center border-t border-slate-800 pt-4">
          <button @click="showRevokeModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 text-xs font-semibold rounded-xl">Cancel</button>
          <button @click="submitRevocation" class="px-5 py-2 bg-red-600 hover:bg-red-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-red-600/30">
            🚫 Confirm Revocation
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
