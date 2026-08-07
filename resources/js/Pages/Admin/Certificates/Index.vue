<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import dayjs from 'dayjs'

const props = defineProps<{
  templates: Array<any>
  certificates: Array<any>
  eligibleStudents: Array<any>
  verificationLogs: Array<any>
  autoRules: Record<string, any>
  publicSettings: Record<string, any>
  majors: Array<any>
  courses: Array<any>
}>()

// Active Tab state: 'templates', 'issue', 'issued', 'verify', 'revoked'
const urlParams = new URLSearchParams(window.location.search)
const activeTab = ref(urlParams.get('tab') || 'issued')

const setTab = (tab: string) => {
  activeTab.value = tab
  const url = new URL(window.location.href)
  url.searchParams.set('tab', tab)
  window.history.pushState({}, '', url.toString())
}

// -------------------------------------------------------------
// TAB 1: TEMPLATES & CANVAS DRAG & DROP EDITOR
// -------------------------------------------------------------
const showEditorModal = ref(false)
const selectedTemplate = ref<any>(null)
const canvasZoom = ref(100)
const selectedElementId = ref<string>('student_name')

const templateForm = ref({
  id: null as number | null,
  name: 'Classic Blue',
  assigned_type: 'major',
  assigned_name: 'IT & Networking',
  language: 'kh_en',
  paper_size: 'a4_landscape',
  status: 'active',
  elements: [
    { id: 'logo', type: 'logo', label: '🎓 Logo', x: 40, y: 40, fontSize: 24, color: '#1e3a8a', style: 'bold' },
    { id: 'seal', type: 'seal', label: '🏛️ Seal', x: 720, y: 40, fontSize: 24, color: '#1e3a8a', style: 'bold' },
    { id: 'title', type: 'text', label: 'Certificate Title', value: 'CERTIFICATE OF COMPLETION', x: 260, y: 110, fontSize: 26, color: '#1e3a8a', style: 'bold' },
    { id: 'subtitle', type: 'text', label: 'Subtitle', value: 'This is to certify that', x: 320, y: 170, fontSize: 14, color: '#64748b', style: 'italic' },
    { id: 'student_name', type: 'variable', label: 'Student Name', value: '{student_name}', x: 310, y: 220, fontSize: 32, color: '#0f172a', style: 'bold' },
    { id: 'student_id', type: 'variable', label: 'Student ID', value: '(Student ID: {student_id})', x: 330, y: 265, fontSize: 12, color: '#64748b', style: 'normal' },
    { id: 'course_intro', type: 'text', label: 'Course Intro', value: 'has successfully completed the course', x: 290, y: 300, fontSize: 14, color: '#64748b', style: 'normal' },
    { id: 'course_name', type: 'variable', label: 'Course Name', value: '{course_name}', x: 280, y: 340, fontSize: 22, color: '#1e3a8a', style: 'bold' },
    { id: 'major_name', type: 'variable', label: 'Major Name', value: 'Major: {major_name}', x: 340, y: 375, fontSize: 13, color: '#475569', style: 'normal' },
    { id: 'grade_score', type: 'variable', label: 'Grade & Score', value: 'with grade: {grade}  ·  Score: {score}%', x: 295, y: 415, fontSize: 14, color: '#047857', style: 'bold' },
    { id: 'issue_date', type: 'variable', label: 'Issue Date', value: 'Issued on: {issue_date}', x: 330, y: 450, fontSize: 12, color: '#64748b', style: 'normal' },
    { id: 'signature_1', type: 'signature', label: 'Instructor Signature', value: '{teacher_name}', sub: 'Instructor', x: 80, y: 500, fontSize: 12, color: '#1e293b', style: 'normal' },
    { id: 'signature_2', type: 'signature', label: 'Dean Signature', value: '{dean_name}', sub: 'Dean of Faculty', x: 350, y: 500, fontSize: 12, color: '#1e293b', style: 'normal' },
    { id: 'signature_3', type: 'signature', label: 'Director Signature', value: '{director_name}', sub: 'Academic Director', x: 620, y: 500, fontSize: 12, color: '#1e293b', style: 'normal' },
    { id: 'cert_id', type: 'variable', label: 'Certificate ID', value: 'Certificate ID: {certificate_id}', x: 60, y: 565, fontSize: 11, color: '#475569', style: 'mono' },
    { id: 'qr_code', type: 'qr', label: 'QR Code', x: 710, y: 530, fontSize: 40, color: '#0f172a', style: 'bold' },
  ]
})

const activeElement = computed(() => {
  return templateForm.value.elements.find(el => el.id === selectedElementId.value)
})

const openEditor = (template?: any) => {
  if (template) {
    selectedTemplate.value = template
    templateForm.value.name = template.name
    templateForm.value.assigned_name = template.assigned_name || 'IT & Networking'
    templateForm.value.status = template.status
  }
  showEditorModal.value = true
}

const addCanvasElement = (elementType: string, label: string, defaultValue: string) => {
  const newId = 'elem_' + Date.now()
  templateForm.value.elements.push({
    id: newId,
    type: elementType,
    label: label,
    value: defaultValue,
    x: 350,
    y: 250,
    fontSize: 16,
    color: '#1e293b',
    style: 'normal'
  })
  selectedElementId.value = newId
}

// Interactive Canvas Element Dragging
const isDragging = ref(false)
const dragElementId = ref<string | null>(null)

const startDrag = (elId: string, event: MouseEvent) => {
  selectedElementId.value = elId
  isDragging.value = true
  dragElementId.value = elId
}

const onCanvasMouseMove = (event: MouseEvent) => {
  if (!isDragging.value || !dragElementId.value) return
  const canvas = document.getElementById('certificate-canvas')
  if (!canvas) return
  const rect = canvas.getBoundingClientRect()
  const x = Math.round(event.clientX - rect.left)
  const y = Math.round(event.clientY - rect.top)

  const target = templateForm.value.elements.find(el => el.id === dragElementId.value)
  if (target) {
    target.x = Math.max(10, Math.min(x, 750))
    target.y = Math.max(10, Math.min(y, 580))
  }
}

const stopDrag = () => {
  isDragging.value = false
  dragElementId.value = null
}

const saveTemplate = () => {
  router.post('/admin/certificates/template', templateForm.value, {
    onSuccess: () => {
      showEditorModal.value = false
      alert('Template saved successfully!')
    }
  })
}

// -------------------------------------------------------------
// TAB 2: ISSUE CERTIFICATE WIZARD & AUTO RULES
// -------------------------------------------------------------
const selectedStudentIds = ref<number[]>([])
const showIssueWizard = ref(false)
const wizardStep = ref(1)
const wizardData = ref({
  student: { id: 101, name: 'Chan Dara', student_id: 'STU24001', email: 'dara@elms.edu' },
  course: { id: 1, title: 'C Programming Basics', major: 'IT & Networking' },
  progress: 100,
  post_test: 82,
  assignments: '4/4 Graded',
  payment: 'Verified ($55.00 ABA Txn: ABA98452178)',
  standing: 'No Academic Violations',
  learning_time: '28h 30m (Min: 20h)',
  template_id: 1,
  send_email: true,
  download_pdf: true,
})

const openIssueWizard = (student?: any) => {
  if (student) {
    wizardData.value.student.name = student.name
    wizardData.value.student.student_id = student.student_id
    wizardData.value.course.title = student.course
    wizardData.value.course.major = student.major
  }
  wizardStep.value = 1
  showIssueWizard.value = true
}

const confirmIssueSingle = () => {
  router.post('/admin/certificates/issue-single', {
    student_id: wizardData.value.student.id,
    course_id: wizardData.value.course.id,
    template_id: wizardData.value.template_id,
  }, {
    onSuccess: () => {
      showIssueWizard.value = false
      setTab('issued')
      alert('Certificate issued successfully!')
    }
  })
}

// -------------------------------------------------------------
// TAB 3: ISSUED CERTIFICATES & DETAIL DRAWER
// -------------------------------------------------------------
const searchQuery = ref('')
const selectedMajorFilter = ref('All')
const selectedCourseFilter = ref('All')
const selectedStatusFilter = ref('All')
const showDetailDrawer = ref(false)
const activeCertDetail = ref<any>(null)

const filteredCertificates = computed(() => {
  return props.certificates.filter(c => {
    const certNum = c.certificate_number || ''
    const studentName = c.student?.name || ''
    const courseTitle = c.course?.title || ''
    const matchSearch = certNum.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        studentName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        courseTitle.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchMajor = selectedMajorFilter.value === 'All' || c.major?.name === selectedMajorFilter.value
    const matchStatus = selectedStatusFilter.value === 'All' || c.status === selectedStatusFilter.value

    return matchSearch && matchMajor && matchStatus
  })
})

const openCertDetail = (cert: any) => {
  activeCertDetail.value = cert
  showDetailDrawer.value = true
}

// -------------------------------------------------------------
// TAB 4: CERTIFICATE VERIFICATION & LOGS
// -------------------------------------------------------------
const quickVerifyInput = ref('ELMS-2025-000451')
const quickVerifyResult = ref<any>(null)
const isVerifying = ref(false)

const runQuickVerify = () => {
  if (!quickVerifyInput.value.trim()) return
  isVerifying.value = true
  router.post('/admin/certificates/quick-verify', {
    certificate_id: quickVerifyInput.value.trim()
  }, {
    onSuccess: (page: any) => {
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

// -------------------------------------------------------------
// TAB 5: REVOKED CERTIFICATES & WORKFLOW
// -------------------------------------------------------------
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
  <AdminLayout title="Certificate Module">
    <div class="space-y-6">
      <!-- 🏅 Header Title & Tabs -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl">
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            🏅 Certificate Module
            <span class="text-xs bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 px-2.5 py-0.5 rounded-full font-mono">
              Admin Panel
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            គ្រប់គ្រង វិញ្ញាបនបត្រទាំងមូល — ពីការរចនា Template, ការចេញវិញ្ញាបនបត្រ, ការផ្ទៀងផ្ទាត់ រហូតដល់ការដកហូត។
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button
            @click="openIssueWizard()"
            class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center gap-1.5"
          >
            🏅 Issue Certificate Now
          </button>
          <a
            href="/certificate/verify"
            target="_blank"
            class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1.5"
          >
            🔍 Public Portal ↗
          </a>
        </div>
      </div>

      <!-- 0️⃣ Module Navigation Tabs -->
      <div class="flex items-center gap-1 border-b border-slate-800 overflow-x-auto pb-1 custom-scrollbar">
        <button
          @click="setTab('templates')"
          :class="[
            activeTab === 'templates'
              ? 'bg-indigo-500/15 text-indigo-300 border-indigo-500/40 font-semibold shadow-sm'
              : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-transparent',
            'px-4 py-2.5 text-xs rounded-xl border transition-all flex items-center gap-2 shrink-0'
          ]"
        >
          🎨 Templates
        </button>

        <button
          @click="setTab('issue')"
          :class="[
            activeTab === 'issue'
              ? 'bg-indigo-500/15 text-indigo-300 border-indigo-500/40 font-semibold shadow-sm'
              : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-transparent',
            'px-4 py-2.5 text-xs rounded-xl border transition-all flex items-center gap-2 shrink-0'
          ]"
        >
          🏅 Issue Certificate
        </button>

        <button
          @click="setTab('issued')"
          :class="[
            activeTab === 'issued'
              ? 'bg-indigo-500/15 text-indigo-300 border-indigo-500/40 font-semibold shadow-sm'
              : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-transparent',
            'px-4 py-2.5 text-xs rounded-xl border transition-all flex items-center gap-2 shrink-0'
          ]"
        >
          📜 Issued Certificates
        </button>

        <button
          @click="setTab('verify')"
          :class="[
            activeTab === 'verify'
              ? 'bg-indigo-500/15 text-indigo-300 border-indigo-500/40 font-semibold shadow-sm'
              : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-transparent',
            'px-4 py-2.5 text-xs rounded-xl border transition-all flex items-center gap-2 shrink-0'
          ]"
        >
          🔍 Certificate Verification
        </button>

        <button
          @click="setTab('revoked')"
          :class="[
            activeTab === 'revoked'
              ? 'bg-red-500/15 text-red-300 border-red-500/40 font-semibold shadow-sm'
              : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-transparent',
            'px-4 py-2.5 text-xs rounded-xl border transition-all flex items-center gap-2 shrink-0'
          ]"
        >
          🚫 Revoked Certificates
        </button>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 1: TEMPLATES & CANVAS EDITOR -->
      <!-- ========================================================================= -->
      <div v-if="activeTab === 'templates'" class="space-y-6">
        <!-- Stat Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
            <span class="text-[11px] text-slate-400">🎨 Total Templates</span>
            <p class="text-2xl font-extrabold text-white">8 Templates</p>
          </div>

          <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
            <span class="text-[11px] text-slate-400">🟢 Active</span>
            <p class="text-2xl font-extrabold text-emerald-400">6 Active</p>
          </div>

          <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
            <span class="text-[11px] text-slate-400">🟡 Draft</span>
            <p class="text-2xl font-extrabold text-amber-400">2 Drafts</p>
          </div>

          <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
            <span class="text-[11px] text-slate-400">🏛️ By Faculty</span>
            <p class="text-2xl font-extrabold text-cyan-400">5 Covered</p>
          </div>
        </div>

        <!-- Action Toolbar -->
        <div class="flex items-center justify-between">
          <h2 class="text-base font-bold text-white">TEMPLATE GALLERY</h2>
          <div class="flex items-center gap-2">
            <button @click="openEditor()" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg transition">
              ➕ Create Template
            </button>
            <button class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg border border-slate-700 transition">
              📋 Duplicate
            </button>
            <button class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg border border-slate-700 transition">
              📤 Import Design
            </button>
          </div>
        </div>

        <!-- Gallery Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div v-for="t in templates" :key="t.id" class="bg-slate-900 border border-slate-800 rounded-2xl p-4 space-y-3 hover:border-slate-700 transition group">
            <div class="flex justify-between items-start">
              <h3 class="font-bold text-sm text-white flex items-center gap-2">
                🏅 {{ t.name }}
              </h3>
              <span :class="t.status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border-amber-500/30'" class="px-2 py-0.5 text-[10px] font-semibold border rounded-full">
                {{ t.status === 'active' ? '🟢 Active' : '🟡 Draft' }}
              </span>
            </div>

            <!-- Thumbnail Mockup -->
            <div class="bg-slate-950 border border-slate-800 rounded-xl h-32 flex flex-col items-center justify-center p-3 relative overflow-hidden group-hover:border-indigo-500/50 transition">
              <div class="text-[10px] text-indigo-400 font-bold tracking-widest uppercase">Certificate of Completion</div>
              <div class="text-xs font-bold text-slate-300 mt-1">{student_name}</div>
              <div class="text-[9px] text-slate-500 mt-0.5">{course_name}</div>
              <div class="mt-2 flex items-center gap-3 text-[8px] text-slate-600">
                <span>[Signatures]</span>
                <span>[QR Code]</span>
              </div>
            </div>

            <div class="text-xs space-y-1 text-slate-400">
              <p>Used by: <strong class="text-slate-200">{{ t.assigned_name || 'IT & Networking' }}</strong></p>
              <p>Language: <span class="text-slate-300 font-mono text-[11px]">{{ t.language === 'kh_en' ? 'Bilingual KH/EN' : 'English' }}</span></p>
            </div>

            <div class="pt-2 border-t border-slate-800 flex items-center justify-between">
              <button @click="openEditor(t)" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold">
                ✏ Edit Template
              </button>
              <button @click="openEditor(t)" class="text-xs text-slate-400 hover:text-white">
                👁 Preview
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 2: ISSUE CERTIFICATE & AUTO RULES -->
      <!-- ========================================================================= -->
      <div v-if="activeTab === 'issue'" class="space-y-6">
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
              <button @click="confirmIssueSingle()" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg transition">
                🏅 Issue Selected
              </button>
              <button @click="confirmIssueSingle()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-lg transition">
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

      <!-- ========================================================================= -->
      <!-- TAB 3: ISSUED CERTIFICATES & CATALOG -->
      <!-- ========================================================================= -->
      <div v-if="activeTab === 'issued'" class="space-y-6">
        <!-- Stat Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
            <span class="text-[11px] text-slate-400">🏅 Total Issued</span>
            <p class="text-2xl font-extrabold text-white">1,352</p>
          </div>

          <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
            <span class="text-[11px] text-slate-400">📅 This Month</span>
            <p class="text-2xl font-extrabold text-emerald-400">245 Issued</p>
          </div>

          <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
            <span class="text-[11px] text-slate-400">📥 Downloads</span>
            <p class="text-2xl font-extrabold text-cyan-400">3,240 Times</p>
          </div>

          <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
            <span class="text-[11px] text-slate-400">🔍 Verified</span>
            <p class="text-2xl font-extrabold text-indigo-400">890 Times</p>
          </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl flex flex-col md:flex-row gap-3 items-center justify-between">
          <div class="relative w-full md:w-80">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search cert ID, student, course..."
              class="w-full pl-9 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500"
            />
            <span class="absolute left-3 top-2.5 text-slate-500 text-xs">🔍</span>
          </div>

          <div class="flex items-center gap-2 w-full md:w-auto">
            <select v-model="selectedMajorFilter" class="bg-slate-950 border border-slate-800 text-slate-300 text-xs rounded-xl px-3 py-2">
              <option value="All">Major: All</option>
              <option value="IT & Networking">IT & Networking</option>
              <option value="Agronomy">Agronomy</option>
              <option value="Social Work">Social Work</option>
            </select>

            <select v-model="selectedStatusFilter" class="bg-slate-950 border border-slate-800 text-slate-300 text-xs rounded-xl px-3 py-2">
              <option value="All">Status: All</option>
              <option value="valid">🟢 Valid</option>
              <option value="revoked">🔴 Revoked</option>
            </select>

            <button class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700 transition">
              📤 Export
            </button>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
              <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold border-b border-slate-800">
                <tr>
                  <th class="p-3.5">Certificate ID</th>
                  <th class="p-3.5">Student</th>
                  <th class="p-3.5">Course</th>
                  <th class="p-3.5">Grade</th>
                  <th class="p-3.5">Issued Date</th>
                  <th class="p-3.5">Status</th>
                  <th class="p-3.5 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-800">
                <tr v-for="c in filteredCertificates" :key="c.id" class="hover:bg-slate-800/40">
                  <td class="p-3.5 font-mono text-indigo-400 font-bold">{{ c.certificate_number }}</td>
                  <td class="p-3.5 font-semibold text-white">
                    {{ c.student?.name }}
                    <span class="block text-[10px] text-slate-400 font-mono">{{ c.student_id_code || 'STU24001' }}</span>
                  </td>
                  <td class="p-3.5 text-slate-300">{{ c.course?.title }}</td>
                  <td class="p-3.5 font-bold text-emerald-400">{{ c.grade }} ({{ c.score }}%)</td>
                  <td class="p-3.5 text-slate-400">{{ dayjs(c.issued_at).format('DD MMM YYYY') }}</td>
                  <td class="p-3.5">
                    <span :class="c.status === 'valid' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-red-500/20 text-red-400 border-red-500/30'" class="px-2.5 py-0.5 text-[10px] font-semibold border rounded-full">
                      {{ c.status === 'valid' ? '🟢 Valid' : '🔴 Revoked' }}
                    </span>
                  </td>
                  <td class="p-3.5 text-right space-x-1">
                    <button @click="openCertDetail(c)" class="p-1 text-slate-400 hover:text-white" title="View Detail">👁</button>
                    <a :href="`/certificate/download/${c.id}`" target="_blank" class="p-1 text-slate-400 hover:text-cyan-300" title="Download PDF">📥</a>
                    <button @click="openRevokeModal(c)" v-if="c.status === 'valid'" class="p-1 text-slate-400 hover:text-red-400" title="Revoke">🚫</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- TAB 4: CERTIFICATE VERIFICATION & LOGS -->
      <!-- ========================================================================= -->
      <div v-if="activeTab === 'verify'" class="space-y-6">
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

        <!-- Verification Activity Logs Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="font-bold text-sm text-white">VERIFICATION LOGS (Recent Verification Activity)</h3>
            <button class="px-3 py-1.5 bg-slate-800 text-slate-300 text-xs rounded-lg border border-slate-700">
              📤 Export Logs
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

      <!-- ========================================================================= -->
      <!-- TAB 5: REVOKED CERTIFICATES -->
      <!-- ========================================================================= -->
      <div v-if="activeTab === 'revoked'" class="space-y-6">
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

        <!-- Action Bar -->
        <div class="flex justify-between items-center">
          <h2 class="text-base font-bold text-white">REVOKED CERTIFICATES REGISTRY</h2>
          <button @click="openRevokeModal()" class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white font-semibold text-xs rounded-xl transition">
            ➕ Request Revocation
          </button>
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
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: TEMPLATE EDITOR CANVAS -->
    <!-- ========================================================================= -->
    <div v-if="showEditorModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-6xl h-[85vh] flex flex-col shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center bg-slate-950">
          <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              ✏️ TEMPLATE EDITOR — {{ templateForm.name }}
            </h3>
            <p class="text-xs text-slate-400">Drag elements directly onto the canvas to adjust positioning</p>
          </div>
          <button @click="showEditorModal = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <!-- Body 3 Columns -->
        <div class="flex-1 flex overflow-hidden">
          <!-- Left Palette -->
          <div class="w-64 border-r border-slate-800 p-4 space-y-4 bg-slate-950 overflow-y-auto custom-scrollbar">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Elements Palette</h4>
            <div class="space-y-1.5 text-xs">
              <button @click="addCanvasElement('logo', '🎓 Logo', 'E.LMS Logo')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left">🎓 Logo</button>
              <button @click="addCanvasElement('variable', 'Student Name', '{student_name}')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left">📛 Student Name</button>
              <button @click="addCanvasElement('variable', 'Course Name', '{course_name}')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left">📘 Course Name</button>
              <button @click="addCanvasElement('variable', 'Major Name', '{major_name}')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left">🎓 Major Name</button>
              <button @click="addCanvasElement('variable', 'Issue Date', '{issue_date}')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left">📅 Issue Date</button>
              <button @click="addCanvasElement('variable', 'Grade/Score', '{grade}')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left">📊 Grade & Score</button>
              <button @click="addCanvasElement('signature', 'Signature', '{teacher_name}')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left">✍️ Signature</button>
              <button @click="addCanvasElement('qr', 'QR Code', '[QR]')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left">📱 QR Code</button>
              <button @click="addCanvasElement('seal', 'Faculty Seal', 'Seal')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left">🏛️ Seal</button>
            </div>
          </div>

          <!-- Middle Canvas Area -->
          <div class="flex-1 bg-slate-950 p-6 flex flex-col items-center justify-center relative overflow-auto" @mousemove="onCanvasMouseMove" @mouseup="stopDrag">
            <div
              id="certificate-canvas"
              class="w-[800px] h-[600px] bg-white text-slate-900 rounded-lg shadow-2xl relative border-8 border-indigo-900/40 select-none overflow-hidden"
            >
              <!-- Draggable elements on Canvas -->
              <div
                v-for="el in templateForm.elements"
                :key="el.id"
                @mousedown="startDrag(el.id, $event)"
                :style="{ left: el.x + 'px', top: el.y + 'px', fontSize: el.fontSize + 'px', color: el.color }"
                :class="selectedElementId === el.id ? 'ring-2 ring-indigo-500 bg-indigo-50/50' : ''"
                class="absolute cursor-move px-1.5 py-0.5 rounded transition-shadow font-serif"
              >
                <template v-if="el.type === 'logo'">🎓 E.LMS Logo</template>
                <template v-else-if="el.type === 'seal'">🏛️ Seal</template>
                <template v-else-if="el.type === 'qr'">
                  <div class="w-12 h-12 bg-slate-900 text-white text-[8px] flex items-center justify-center rounded">
                    [QR Code]
                  </div>
                </template>
                <template v-else-if="el.type === 'signature'">
                  <div class="text-center font-sans">
                    <div class="border-b border-slate-800 pb-1 font-serif italic">{{ el.value }}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">{{ el.sub || 'Signatory' }}</div>
                  </div>
                </template>
                <template v-else>{{ el.value || el.label }}</template>
              </div>
            </div>
          </div>

          <!-- Right Properties Panel -->
          <div class="w-72 border-l border-slate-800 p-4 space-y-4 bg-slate-950 overflow-y-auto custom-scrollbar">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Properties Inspector</h4>
            <div v-if="activeElement" class="space-y-3 text-xs">
              <div>
                <label class="text-slate-400 block mb-1">Selected Element:</label>
                <span class="font-bold text-white">{{ activeElement.label }}</span>
              </div>

              <div>
                <label class="text-slate-400 block mb-1">Text Value / Placeholder:</label>
                <input v-model="activeElement.value" type="text" class="w-full bg-slate-900 border border-slate-800 rounded p-1.5 text-white" />
              </div>

              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="text-slate-400 block mb-1">Font Size:</label>
                  <input v-model.number="activeElement.fontSize" type="number" class="w-full bg-slate-900 border border-slate-800 rounded p-1.5 text-white" />
                </div>
                <div>
                  <label class="text-slate-400 block mb-1">Text Color:</label>
                  <input v-model="activeElement.color" type="color" class="w-full h-8 bg-slate-900 border border-slate-800 rounded cursor-pointer" />
                </div>
              </div>

              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="text-slate-400 block mb-1">X Coord (px):</label>
                  <input v-model.number="activeElement.x" type="number" class="w-full bg-slate-900 border border-slate-800 rounded p-1.5 text-white" />
                </div>
                <div>
                  <label class="text-slate-400 block mb-1">Y Coord (px):</label>
                  <input v-model.number="activeElement.y" type="number" class="w-full bg-slate-900 border border-slate-800 rounded p-1.5 text-white" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-3 border-t border-slate-800 bg-slate-950 flex justify-between items-center">
          <span class="text-xs text-slate-500">A4 Landscape Canvas Layout</span>
          <button @click="saveTemplate" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl">
            💾 Save Template
          </button>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: SINGLE ISSUE WIZARD -->
    <!-- ========================================================================= -->
    <div v-if="showIssueWizard" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-2xl p-6 space-y-6 shadow-2xl">
        <div class="flex justify-between items-center border-b border-slate-800 pb-4">
          <h3 class="font-bold text-base text-white">🏅 ISSUE CERTIFICATE — WIZARD</h3>
          <button @click="showIssueWizard = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-4">
          <h4 class="text-xs font-bold text-indigo-400 uppercase tracking-wider">ELIGIBILITY VERIFICATION REPORT</h4>
          <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 text-xs space-y-2 text-slate-300">
            <p>✅ <strong>Course Progress:</strong> 100% (16/16 chapters completed)</p>
            <p>✅ <strong>Post-Test:</strong> Passed — 82% (passing threshold: 70%)</p>
            <p>✅ <strong>Assignments:</strong> 4/4 submitted & graded</p>
            <p>✅ <strong>Payment:</strong> Verified — $55.00 (ABA Txn: ABA98452178)</p>
            <p>✅ <strong>Academic Standing:</strong> No active violations</p>
            <p>✅ <strong>Learning Time:</strong> 28h 30m (minimum: 20h)</p>
            <div class="pt-2 text-emerald-400 font-bold border-t border-slate-800">
              ALL REQUIREMENTS MET — Eligible for certificate
            </div>
          </div>
        </div>

        <div class="flex justify-between items-center border-t border-slate-800 pt-4">
          <button @click="showIssueWizard = false" class="px-4 py-2 bg-slate-800 text-slate-300 text-xs font-semibold rounded-xl">Cancel</button>
          <button @click="confirmIssueSingle" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-indigo-600/30">
            🏅 Issue Certificate Now
          </button>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: REVOCATION WORKFLOW FORM -->
    <!-- ========================================================================= -->
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

    <!-- ========================================================================= -->
    <!-- DRAWER: CERTIFICATE DETAIL INSPECTOR -->
    <!-- ========================================================================= -->
    <div v-if="showDetailDrawer" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex justify-end">
      <div class="w-full max-w-xl bg-slate-900 border-l border-slate-800 h-full p-6 space-y-6 overflow-y-auto">
        <div class="flex justify-between items-center border-b border-slate-800 pb-4">
          <h3 class="font-bold text-base text-white">🏅 CERTIFICATE DETAIL</h3>
          <button @click="showDetailDrawer = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div v-if="activeCertDetail" class="space-y-4 text-xs">
          <!-- Document Mockup -->
          <div class="bg-slate-950 p-6 rounded-xl border border-slate-800 text-center space-y-3 font-serif text-slate-200">
            <p class="text-[10px] text-indigo-400 tracking-widest font-sans uppercase">Certificate of Completion</p>
            <h4 class="text-xl font-bold text-white font-sans">{{ activeCertDetail.student?.name }}</h4>
            <p class="text-xs text-slate-400 font-sans">{{ activeCertDetail.course?.title }}</p>
            <p class="text-xs font-mono text-cyan-400 font-sans">{{ activeCertDetail.certificate_number }}</p>
          </div>

          <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2 text-slate-300">
            <p><strong>Certificate ID:</strong> <code class="text-cyan-400 font-mono">{{ activeCertDetail.certificate_number }}</code></p>
            <p><strong>Student:</strong> <span>{{ activeCertDetail.student?.name }}</span></p>
            <p><strong>Course:</strong> <span>{{ activeCertDetail.course?.title }}</span></p>
            <p><strong>Grade & Score:</strong> <span class="text-emerald-400 font-bold">{{ activeCertDetail.grade }} ({{ activeCertDetail.score }}%)</span></p>
            <p><strong>Status:</strong> <span :class="activeCertDetail.status === 'valid' ? 'text-emerald-400' : 'text-red-400'" class="font-bold uppercase">{{ activeCertDetail.status }}</span></p>
            <p><strong>Times Verified:</strong> <span>{{ activeCertDetail.verifications_count || 12 }} times</span></p>
            <p><strong>Downloads:</strong> <span>{{ activeCertDetail.downloads_count || 4 }} downloads</span></p>
          </div>

          <div class="flex gap-2">
            <a :href="`/certificate/download/${activeCertDetail.id}`" target="_blank" class="flex-1 py-2 bg-indigo-600 text-white font-semibold rounded-xl text-center">
              📥 Download PDF
            </a>
            <button @click="showDetailDrawer = false" class="py-2 px-4 bg-slate-800 text-slate-300 font-semibold rounded-xl">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
