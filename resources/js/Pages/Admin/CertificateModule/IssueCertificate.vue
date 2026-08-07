<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps<{
  eligibleStudents: Array<any>
  autoRules: Record<string, any>
  courses?: Array<any>
  templates?: Array<any>
}>()

const showIssueWizard = ref(false)
const wizardStep = ref(1) // 1: Select Student, 2: Verify Eligibility, 3: Choose Template, 4: Preview & Issue

const availableStudents = [
  { id: 101, name: 'Chan Dara', student_id: 'STU24001', course: 'C Programming Basics', major: 'IT & Networking', progress: 100, post_test: 82, assignments: '4/4 Graded', payment: 'Verified ($55.00 ABA Txn: ABA98452178)', standing: 'No violations', learning_time: '28h 30m' },
  { id: 102, name: 'Bun Rithy', student_id: 'STU24002', course: 'C Programming Basics', major: 'IT & Networking', progress: 100, post_test: 78, assignments: '4/4 Graded', payment: 'Verified ($55.00 ABA Txn: ABA98452179)', standing: 'No violations', learning_time: '24h 15m' },
  { id: 103, name: 'Pov Sreynich', student_id: 'STU24003', course: 'Plant Science & Soil Biology', major: 'Agronomy', progress: 100, post_test: 76, assignments: '3/3 Graded', payment: 'Verified ($45.00 ABA Txn: ABA98452180)', standing: 'No violations', learning_time: '31h 00m' },
  { id: 104, name: 'Long Vichida', student_id: 'STU24004', course: 'English Academic Writing', major: 'Social Work', progress: 100, post_test: 71, assignments: '4/4 Graded', payment: 'Pending', standing: 'No violations', learning_time: '22h 40m' }
]

const selectedStudentId = ref<number>(101)
const selectedTemplateId = ref<number>(1)
const sendEmail = ref(true)
const downloadPdf = ref(true)

const currentStudent = computed(() => {
  return availableStudents.find(s => s.id === selectedStudentId.value) || availableStudents[0]
})

const openIssueWizard = (student?: any) => {
  if (student) {
    selectedStudentId.value = student.id || 101
  }
  wizardStep.value = 1
  showIssueWizard.value = true
}

const nextStep = () => {
  if (wizardStep.value < 4) {
    wizardStep.value++
  }
}

const prevStep = () => {
  if (wizardStep.value > 1) {
    wizardStep.value--
  }
}

const confirmIssueSingle = () => {
  router.post('/admin/certificates/issue-single', {
    student_id: currentStudent.value.id,
    course_id: 1,
    template_id: selectedTemplateId.value,
    send_email: sendEmail.value,
    download_pdf: downloadPdf.value,
  }, {
    onSuccess: () => {
      showIssueWizard.value = false
      alert(`Certificate issued successfully for ${currentStudent.value.name}!`)
      router.get('/admin/certificates/issued')
    }
  })
}

const issueBulkAll = () => {
  if (confirm('Issue certificates to all 28 eligible students now?')) {
    router.post('/admin/certificates/issue-bulk', {}, {
      onSuccess: () => {
        alert('Bulk certificate issuance initiated successfully!')
        router.get('/admin/certificates/issued')
      }
    })
  }
}
</script>

<template>
  <AdminLayout title="Certificate Module — Issue Certificate">
    <div class="space-y-6">
      <!-- 🏅 Header Title -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl">
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            🏅 Issue Certificate
            <span class="text-xs bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 px-2.5 py-0.5 rounded-full font-mono">
              Certificate Module
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            ចេញវិញ្ញាបនបត្រដោយ ស្វ័យប្រវត្តិ (ពេលសិស្សគ្រប់លក្ខខណ្ឌ) ឬ ដោយដៃ (Single / Bulk) ដោយមាន Eligibility Check។
          </p>
        </div>

        <button
          @click="openIssueWizard()"
          class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center gap-1.5 self-start md:self-auto"
        >
          🏅 Single Issue Wizard
        </button>
      </div>

      <!-- Submenu Tabs Header -->
      <div class="flex items-center gap-1 border-b border-slate-800 overflow-x-auto pb-1 custom-scrollbar">
        <Link href="/admin/certificates/templates" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🎨 Templates
        </Link>
        <Link href="/admin/certificates/issue" class="px-4 py-2.5 text-xs rounded-xl border bg-indigo-500/15 text-indigo-300 border-indigo-500/40 font-semibold shadow-sm shrink-0">
          🏅 Issue Certificate
        </Link>
        <Link href="/admin/certificates/issued" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📜 Issued Certificates
        </Link>
        <Link href="/admin/certificates/verify" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🔍 Certificate Verification
        </Link>
        <Link href="/admin/certificates/revoked" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🚫 Revoked Certificates
        </Link>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">⏳ Pending</span>
          <p class="text-2xl font-extrabold text-amber-400">45 Students</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">✅ Eligible Ready</span>
          <p class="text-2xl font-extrabold text-emerald-400">28 Ready</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">🏅 Issued Total</span>
          <p class="text-2xl font-extrabold text-white">1,352 Total</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">🚫 Blocked</span>
          <p class="text-2xl font-extrabold text-red-400">17 Violations</p>
        </div>
      </div>

      <!-- ⚙️ Auto-Issue Settings Card -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h3 class="font-bold text-sm text-white flex items-center gap-2">
          ⚙️ AUTO-ISSUE SETTINGS
        </h3>

        <div class="space-y-3 bg-slate-950 p-4 rounded-xl border border-slate-800 text-xs">
          <label class="flex items-center gap-2 text-slate-200 font-semibold">
            <input type="checkbox" checked class="rounded border-slate-700 text-indigo-600 focus:ring-indigo-500" />
            Auto-issue when all requirements met
          </label>

          <div class="pl-6 space-y-2 text-slate-400">
            <label class="flex items-center gap-2"><input type="checkbox" checked /> Course progress = 100%</label>
            <label class="flex items-center gap-2"><input type="checkbox" checked /> Post-Test passed (≥ passing score)</label>
            <label class="flex items-center gap-2"><input type="checkbox" checked /> All assignments submitted & graded</label>
            <label class="flex items-center gap-2"><input type="checkbox" checked /> Payment fully verified (paid courses)</label>
            <label class="flex items-center gap-2"><input type="checkbox" checked /> No active academic violation</label>
          </div>
        </div>
      </div>

      <!-- Eligible Students Table -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <h3 class="font-bold text-sm text-white">ELIGIBLE STUDENTS — READY TO ISSUE</h3>
          <div class="flex items-center gap-2">
            <button @click="openIssueWizard()" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg transition">
              🏅 Issue Selected
            </button>
            <button @click="issueBulkAll()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-lg transition">
              🏅 Issue All Eligible (28)
            </button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold border-b border-slate-800">
              <tr>
                <th class="p-3 w-8"><input type="checkbox" class="rounded border-slate-700" /></th>
                <th class="p-3">Student</th>
                <th class="p-3">Course</th>
                <th class="p-3">Progress</th>
                <th class="p-3">Post-Test</th>
                <th class="p-3">Payment</th>
                <th class="p-3">Template</th>
                <th class="p-3">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
              <tr v-for="s in eligibleStudents" :key="s.id" class="hover:bg-slate-800/40">
                <td class="p-3"><input type="checkbox" :disabled="s.status === 'blocked'" class="rounded border-slate-700" /></td>
                <td class="p-3 font-semibold text-white">
                  {{ s.name }}
                  <span class="block text-[10px] text-slate-400 font-mono">{{ s.student_id }}</span>
                </td>
                <td class="p-3 text-slate-300">{{ s.course }}</td>
                <td class="p-3 text-emerald-400 font-semibold">✅ {{ s.progress }}%</td>
                <td class="p-3 text-emerald-400 font-semibold">✅ {{ s.post_test }}%</td>
                <td class="p-3">
                  <span :class="s.payment === 'Paid' ? 'text-emerald-400' : 'text-amber-400'" class="font-semibold">
                    {{ s.payment === 'Paid' ? '✅ Paid' : '⏳ Pending' }}
                  </span>
                </td>
                <td class="p-3 text-slate-400">{{ s.template }}</td>
                <td class="p-3">
                  <button
                    v-if="s.status !== 'blocked'"
                    @click="openIssueWizard(s)"
                    class="px-2.5 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-lg hover:bg-indigo-500/30 transition text-[11px] font-semibold"
                  >
                    Wizard →
                  </button>
                  <span v-else class="text-[10px] text-amber-400 font-semibold">Blocked</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 4-STEP SINGLE ISSUE WIZARD MODAL -->
    <div v-if="showIssueWizard" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-3xl p-6 space-y-6 shadow-2xl overflow-hidden">
        <!-- Wizard Header -->
        <div class="flex justify-between items-center border-b border-slate-800 pb-4">
          <h3 class="font-bold text-base text-white">🏅 ISSUE CERTIFICATE — WIZARD</h3>
          <button @click="showIssueWizard = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <!-- Stepper Navigation Header -->
        <div class="flex items-center justify-between text-xs border-b border-slate-800 pb-3">
          <span :class="wizardStep === 1 ? 'text-indigo-400 font-bold border-b-2 border-indigo-500 pb-1' : 'text-slate-400'">
            ① Select Student
          </span>
          <span class="text-slate-600">→</span>
          <span :class="wizardStep === 2 ? 'text-indigo-400 font-bold border-b-2 border-indigo-500 pb-1' : 'text-slate-400'">
            ② Verify Eligibility
          </span>
          <span class="text-slate-600">→</span>
          <span :class="wizardStep === 3 ? 'text-indigo-400 font-bold border-b-2 border-indigo-500 pb-1' : 'text-slate-400'">
            ③ Choose Template
          </span>
          <span class="text-slate-600">→</span>
          <span :class="wizardStep === 4 ? 'text-indigo-400 font-bold border-b-2 border-indigo-500 pb-1' : 'text-slate-400'">
            ④ Preview & Issue
          </span>
        </div>

        <!-- STEP 1: SELECT STUDENT -->
        <div v-if="wizardStep === 1" class="space-y-4">
          <h4 class="text-xs font-bold text-slate-300">STEP 1: SELECT STUDENT & COURSE</h4>
          <div class="space-y-3">
            <div>
              <label class="text-xs text-slate-400 block mb-1">Choose Candidate Student:</label>
              <select v-model="selectedStudentId" class="w-full bg-slate-950 border border-slate-800 text-white text-xs rounded-xl p-3">
                <option v-for="s in availableStudents" :key="s.id" :value="s.id">
                  {{ s.name }} ({{ s.student_id }}) — {{ s.course }}
                </option>
              </select>
            </div>

            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 text-xs space-y-1 text-slate-300">
              <p>Selected Student: <strong class="text-white">{{ currentStudent.name }}</strong></p>
              <p>Student ID: <span class="font-mono text-indigo-400">{{ currentStudent.student_id }}</span></p>
              <p>Course: <span class="text-slate-200">{{ currentStudent.course }}</span></p>
              <p>Major: <span class="text-slate-200">{{ currentStudent.major }}</span></p>
            </div>
          </div>
        </div>

        <!-- STEP 2: VERIFY ELIGIBILITY REPORT -->
        <div v-if="wizardStep === 2" class="space-y-4">
          <h4 class="text-xs font-bold text-slate-300">STEP 2: ELIGIBILITY VERIFICATION — {{ currentStudent.name }} ({{ currentStudent.student_id }})</h4>
          <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 text-xs space-y-2 text-slate-300">
            <p>✅ <strong>Course Progress:</strong> {{ currentStudent.progress }}% (16/16 chapters completed)</p>
            <p>✅ <strong>Post-Test:</strong> Passed — {{ currentStudent.post_test }}% (passing: 70%)</p>
            <p>✅ <strong>Assignments:</strong> {{ currentStudent.assignments }}</p>
            <p>✅ <strong>Payment:</strong> {{ currentStudent.payment }}</p>
            <p>✅ <strong>Academic Standing:</strong> {{ currentStudent.standing }}</p>
            <p>✅ <strong>Learning Time:</strong> {{ currentStudent.learning_time }} (minimum: 20h)</p>

            <div class="pt-2 text-emerald-400 font-bold border-t border-slate-800 flex items-center gap-2">
              <span>✅ ALL REQUIREMENTS MET — Eligible for certificate</span>
            </div>
          </div>
        </div>

        <!-- STEP 3: CHOOSE TEMPLATE -->
        <div v-if="wizardStep === 3" class="space-y-4">
          <h4 class="text-xs font-bold text-slate-300">STEP 3: CHOOSE TEMPLATE</h4>
          <div>
            <label class="text-xs text-slate-400 block mb-1">Select Certificate Template:</label>
            <select v-model="selectedTemplateId" class="w-full bg-slate-950 border border-slate-800 text-white text-xs rounded-xl p-3">
              <option :value="1">Classic Blue (IT & Networking)</option>
              <option :value="2">Royal Gold (Tourism Mgt)</option>
              <option :value="3">Green Agriculture (Agronomy)</option>
              <option :value="4">Elegant Purple (Social Work)</option>
            </select>
          </div>
        </div>

        <!-- STEP 4: PREVIEW & ISSUE -->
        <div v-if="wizardStep === 4" class="space-y-4">
          <h4 class="text-xs font-bold text-slate-300">STEP 4: LIVE CERTIFICATE PREVIEW</h4>

          <!-- Render Mockup Certificate -->
          <div class="bg-slate-950 p-6 rounded-xl border border-slate-800 text-center space-y-3 font-serif text-slate-200">
            <div class="flex justify-between items-center text-xs text-slate-400 font-sans">
              <span>🎓 E.LMS Logo</span>
              <span>🏛️ Faculty Seal</span>
            </div>

            <p class="text-[10px] text-indigo-400 tracking-widest font-sans uppercase">Certificate of Completion</p>
            <p class="text-xs text-slate-400 italic">This is to certify that</p>
            <h3 class="text-2xl font-bold text-white font-sans">{{ currentStudent.name }}</h3>
            <p class="text-xs text-slate-400 font-sans">(Student ID: {{ currentStudent.student_id }})</p>
            <p class="text-xs text-slate-400 font-sans">has successfully completed the course</p>
            <p class="text-lg font-bold text-cyan-300 font-sans">{{ currentStudent.course }}</p>
            <p class="text-xs text-slate-400 font-sans">Major: {{ currentStudent.major }}</p>
            <p class="text-xs text-emerald-400 font-bold font-sans">with grade: A · Score: {{ currentStudent.post_test }}%</p>

            <div class="pt-6 border-t border-slate-800 flex justify-between items-center text-[10px] font-sans text-slate-400">
              <div class="text-left">
                <p class="font-mono text-cyan-400">Certificate ID: ELMS-2025-000452</p>
                <p>Verify at: elms.edu/verify/ELMS-2025-000452</p>
              </div>
              <div class="w-12 h-12 bg-white text-slate-950 p-1 rounded font-bold text-[8px] flex items-center justify-center">
                [📱 QR Code]
              </div>
            </div>
          </div>

          <div class="flex items-center gap-4 text-xs">
            <label class="flex items-center gap-1.5 text-slate-300">
              <input type="checkbox" v-model="sendEmail" class="rounded border-slate-700" />
              📧 Email certificate to student
            </label>
            <label class="flex items-center gap-1.5 text-slate-300">
              <input type="checkbox" v-model="downloadPdf" class="rounded border-slate-700" />
              📥 Also download PDF
            </label>
          </div>
        </div>

        <!-- Wizard Stepper Controls Footer -->
        <div class="flex justify-between items-center border-t border-slate-800 pt-4">
          <button v-if="wizardStep > 1" @click="prevStep" class="px-4 py-2 bg-slate-800 text-slate-300 text-xs font-semibold rounded-xl hover:bg-slate-700 transition">
            ← Back
          </button>
          <div v-else></div>

          <button v-if="wizardStep < 4" @click="nextStep" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition">
            Next Step →
          </button>
          <button v-else @click="confirmIssueSingle" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-emerald-600/30 transition">
            🏅 Issue Certificate Now
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
