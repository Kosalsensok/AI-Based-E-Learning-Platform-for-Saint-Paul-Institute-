<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps<{
  templates: Array<any>
  majors?: Array<any>
}>()

// Modal State
const showEditorModal = ref(false)
const selectedTemplate = ref<any>(null)
const selectedElementId = ref<string>('student_name')
const previewSampleMode = ref(false)
const canvasZoom = ref(100)

// History stack for Undo / Redo
const historyStack = ref<string[]>([])
const historyIndex = ref<number>(-1)

const templateForm = ref({
  id: null as number | null,
  name: 'Classic Blue',
  assigned_type: 'major',
  assigned_name: 'IT & Networking',
  language: 'kh_en',
  paper_size: 'a4_landscape',
  border_style: 'double_gold',
  status: 'active',
  elements: [
    { id: 'logo', type: 'logo', label: '🎓 Logo', value: '🎓 E.LMS Logo', x: 50, y: 35, fontSize: 22, color: '#1e3a8a', fontStyle: 'bold', fontFamily: 'serif', align: 'left', locked: false },
    { id: 'seal', type: 'seal', label: '🏛️ Seal', value: '🏛️ Faculty Seal', x: 650, y: 35, fontSize: 20, color: '#1e3a8a', fontStyle: 'bold', fontFamily: 'serif', align: 'right', locked: false },
    { id: 'title', type: 'text', label: 'Certificate Title', value: 'CERTIFICATE OF COMPLETION', x: 210, y: 95, fontSize: 26, color: '#1e3a8a', fontStyle: 'bold', fontFamily: 'serif', align: 'center', locked: false },
    { id: 'subtitle', type: 'text', label: 'Subtitle', value: 'This is to certify that', x: 320, y: 145, fontSize: 14, color: '#64748b', fontStyle: 'italic', fontFamily: 'sans-serif', align: 'center', locked: false },
    { id: 'student_name', type: 'variable', label: 'Student Name', value: '{student_name}', x: 270, y: 185, fontSize: 32, color: '#0f172a', fontStyle: 'bold', fontFamily: 'sans-serif', align: 'center', locked: false },
    { id: 'student_id', type: 'variable', label: 'Student ID', value: '(Student ID: {student_id})', x: 315, y: 235, fontSize: 12, color: '#64748b', fontStyle: 'normal', fontFamily: 'sans-serif', align: 'center', locked: false },
    { id: 'course_intro', type: 'text', label: 'Course Intro', value: 'has successfully completed the course', x: 275, y: 275, fontSize: 14, color: '#64748b', fontStyle: 'normal', fontFamily: 'sans-serif', align: 'center', locked: false },
    { id: 'course_name', type: 'variable', label: 'Course Name', value: '{course_name}', x: 260, y: 315, fontSize: 22, color: '#1e3a8a', fontStyle: 'bold', fontFamily: 'sans-serif', align: 'center', locked: false },
    { id: 'major_name', type: 'variable', label: 'Major Name', value: 'Major: {major_name}', x: 325, y: 355, fontSize: 13, color: '#475569', fontStyle: 'normal', fontFamily: 'sans-serif', align: 'center', locked: false },
    { id: 'grade_score', type: 'variable', label: 'Grade & Score', value: 'with grade: {grade}  ·  Score: {score}%', x: 265, y: 395, fontSize: 14, color: '#047857', fontStyle: 'bold', fontFamily: 'sans-serif', align: 'center', locked: false },
    { id: 'issue_date', type: 'variable', label: 'Issue Date', value: 'Issued on: {issue_date}', x: 315, y: 435, fontSize: 12, color: '#64748b', fontStyle: 'normal', fontFamily: 'sans-serif', align: 'center', locked: false },
    { id: 'signature_1', type: 'signature', label: 'Instructor Signature', value: '{teacher_name}', sub: 'Instructor', x: 60, y: 485, fontSize: 12, color: '#1e293b', fontStyle: 'normal', fontFamily: 'sans-serif', align: 'center', locked: false },
    { id: 'signature_2', type: 'signature', label: 'Dean Signature', value: '{dean_name}', sub: 'Dean of Faculty', x: 330, y: 485, fontSize: 12, color: '#1e293b', fontStyle: 'normal', fontFamily: 'sans-serif', align: 'center', locked: false },
    { id: 'signature_3', type: 'signature', label: 'Director Signature', value: '{director_name}', sub: 'Academic Director', x: 600, y: 485, fontSize: 12, color: '#1e293b', fontStyle: 'normal', fontFamily: 'sans-serif', align: 'center', locked: false },
    { id: 'cert_id', type: 'variable', label: 'Certificate ID', value: 'Certificate ID: {certificate_id}', x: 50, y: 550, fontSize: 11, color: '#475569', fontStyle: 'mono', fontFamily: 'monospace', align: 'left', locked: false },
    { id: 'qr_code', type: 'qr', label: 'QR Code', value: '[QR Code]', x: 690, y: 510, fontSize: 40, color: '#0f172a', fontStyle: 'bold', fontFamily: 'sans-serif', align: 'right', locked: false },
  ]
})

// Sample Data Dictionary for Live Preview
const sampleData: Record<string, string> = {
  '{student_name}': 'Chan Dara',
  '{student_id}': 'STU24001',
  '{student_email}': 'dara@elms.edu',
  '{course_name}': 'C Programming Basics',
  '{major_name}': 'IT & Networking',
  '{faculty_name}': 'Faculty of Computing',
  '{grade}': 'A',
  '{score}': '82',
  '{completion_date}': '15 June 2025',
  '{issue_date}': '16 June 2025',
  '{certificate_id}': 'ELMS-2025-000451',
  '{teacher_name}': 'Mr. Sophea',
  '{dean_name}': 'Dr. Sok Vichea',
  '{director_name}': 'Academic Director',
  '{academic_year}': '2024 – 2025',
  '{verify_url}': 'elms.edu/verify/ELMS-2025-000451',
}

const colorPresets = ['#1e3a8a', '#0f172a', '#047857', '#b45309', '#4338ca', '#991b1b', '#000000']

// Active Element Computed
const activeElement = computed(() => {
  return templateForm.value.elements.find(el => el.id === selectedElementId.value)
})

// Save state snapshot into history for undo
const pushHistory = () => {
  const stateStr = JSON.stringify(templateForm.value.elements)
  if (historyIndex.value < historyStack.value.length - 1) {
    historyStack.value = historyStack.value.slice(0, historyIndex.value + 1)
  }
  historyStack.value.push(stateStr)
  historyIndex.value = historyStack.value.length - 1
}

const undo = () => {
  if (historyIndex.value > 0) {
    historyIndex.value--
    templateForm.value.elements = JSON.parse(historyStack.value[historyIndex.value])
  }
}

const redo = () => {
  if (historyIndex.value < historyStack.value.length - 1) {
    historyIndex.value++
    templateForm.value.elements = JSON.parse(historyStack.value[historyIndex.value])
  }
}

// Open Editor with given template
const openEditor = (template?: any) => {
  if (template) {
    selectedTemplate.value = template
    templateForm.value.id = template.id
    templateForm.value.name = template.name
    templateForm.value.assigned_name = template.assigned_name || 'IT & Networking'
    templateForm.value.status = template.status
  } else {
    selectedTemplate.value = null
    templateForm.value.id = null
    templateForm.value.name = 'New Custom Template'
  }
  pushHistory()
  showEditorModal.value = true
}

// Add element from palette
const addCanvasElement = (type: string, label: string, defaultValue: string) => {
  const newId = 'elem_' + Date.now()
  templateForm.value.elements.push({
    id: newId,
    type,
    label,
    value: defaultValue,
    x: 320,
    y: 240,
    fontSize: 16,
    color: '#1e293b',
    fontStyle: 'normal',
    fontFamily: 'sans-serif',
    align: 'left',
    locked: false,
  })
  selectedElementId.value = newId
  pushHistory()
}

// Delete Element
const deleteActiveElement = () => {
  if (!selectedElementId.value) return
  templateForm.value.elements = templateForm.value.elements.filter(el => el.id !== selectedElementId.value)
  selectedElementId.value = templateForm.value.elements[0]?.id || ''
  pushHistory()
}

// Align element horizontally dynamically based on canvas center (800px)
const alignCenter = () => {
  if (activeElement.value) {
    const textLen = (activeElement.value.value || '').length
    const approxWidth = textLen * (activeElement.value.fontSize * 0.55)
    activeElement.value.x = Math.max(20, Math.round(400 - approxWidth / 2))
    pushHistory()
  }
}

// Render text value (Placeholder vs Sample Data)
const renderElementValue = (el: any) => {
  if (!previewSampleMode.value) return el.value
  let val = el.value || ''
  Object.keys(sampleData).forEach(key => {
    val = val.replaceAll(key, sampleData[key])
  })
  return val
}

// Zero-Latency Mouse Dragging with requestAnimationFrame
const isDragging = ref(false)
const dragElementId = ref<string | null>(null)
const dragOffset = ref({ x: 0, y: 0 })
let rafId: number | null = null

const startDrag = (elId: string, event: MouseEvent) => {
  const target = templateForm.value.elements.find(el => el.id === elId)
  if (!target || target.locked) return

  selectedElementId.value = elId
  isDragging.value = true
  dragElementId.value = elId

  const canvas = document.getElementById('certificate-canvas')
  if (canvas) {
    const rect = canvas.getBoundingClientRect()
    const scale = canvasZoom.value / 100
    dragOffset.value = {
      x: (event.clientX - rect.left) / scale - target.x,
      y: (event.clientY - rect.top) / scale - target.y
    }
  }

  window.addEventListener('mousemove', onWindowMouseMove, { passive: true })
  window.addEventListener('mouseup', onWindowMouseUp)
}

const onWindowMouseMove = (event: MouseEvent) => {
  if (!isDragging.value || !dragElementId.value) return

  if (rafId) cancelAnimationFrame(rafId)

  rafId = requestAnimationFrame(() => {
    const canvas = document.getElementById('certificate-canvas')
    if (!canvas) return

    const rect = canvas.getBoundingClientRect()
    const scale = canvasZoom.value / 100

    const rawX = Math.round((event.clientX - rect.left) / scale - dragOffset.value.x)
    const rawY = Math.round((event.clientY - rect.top) / scale - dragOffset.value.y)

    const target = templateForm.value.elements.find(el => el.id === dragElementId.value)
    if (target) {
      target.x = Math.max(10, Math.min(rawX, 740))
      target.y = Math.max(10, Math.min(rawY, 565))
    }
  })
}

const onWindowMouseUp = () => {
  if (isDragging.value) {
    isDragging.value = false
    dragElementId.value = null
    pushHistory()
  }
  if (rafId) cancelAnimationFrame(rafId)
  window.removeEventListener('mousemove', onWindowMouseMove)
  window.removeEventListener('mouseup', onWindowMouseUp)
}

// Keyboard arrow key nudge & delete support
const onKeyDown = (event: KeyboardEvent) => {
  if (!showEditorModal.value || !activeElement.value) return
  const targetTag = (event.target as HTMLElement)?.tagName
  if (targetTag === 'INPUT' || targetTag === 'TEXTAREA' || targetTag === 'SELECT') return

  const step = event.shiftKey ? 10 : 1

  if (event.key === 'ArrowUp') {
    event.preventDefault()
    activeElement.value.y = Math.max(10, activeElement.value.y - step)
    pushHistory()
  } else if (event.key === 'ArrowDown') {
    event.preventDefault()
    activeElement.value.y = Math.min(565, activeElement.value.y + step)
    pushHistory()
  } else if (event.key === 'ArrowLeft') {
    event.preventDefault()
    activeElement.value.x = Math.max(10, activeElement.value.x - step)
    pushHistory()
  } else if (event.key === 'ArrowRight') {
    event.preventDefault()
    activeElement.value.x = Math.min(740, activeElement.value.x + step)
    pushHistory()
  } else if (event.key === 'Delete' || event.key === 'Backspace') {
    event.preventDefault()
    deleteActiveElement()
  }
}

onMounted(() => {
  window.addEventListener('keydown', onKeyDown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', onKeyDown)
  window.removeEventListener('mousemove', onWindowMouseMove)
  window.removeEventListener('mouseup', onWindowMouseUp)
})

// Save Template API Call
const saveTemplate = () => {
  router.post('/admin/certificates/template', templateForm.value, {
    onSuccess: () => {
      showEditorModal.value = false
      alert('Template saved successfully!')
    }
  })
}

// Duplicate Template API Call
const duplicateTemplate = (id: number) => {
  router.post(`/admin/certificates/template/${id}/duplicate`, {}, {
    onSuccess: () => {
      alert('Template duplicated successfully!')
    }
  })
}
</script>

<template>
  <AdminLayout title="Certificate Module — Templates">
    <div class="space-y-6">
      <!-- Header Banner -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl">
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            🎨 CERTIFICATE TEMPLATES
            <span class="text-xs bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 px-2.5 py-0.5 rounded-full font-mono">
              Admin Module
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            គ្រប់គ្រងគំរូវិញ្ញាបនបត្រ — រចនាតាម Major / Faculty / Course Type ដោយមាន Drag & Drop Editor។
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button
            @click="openEditor()"
            class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center gap-1.5"
          >
            ➕ Create Template
          </button>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-1 border-b border-slate-800 overflow-x-auto pb-1 custom-scrollbar">
        <Link href="/admin/certificates/templates" class="px-4 py-2.5 text-xs rounded-xl border bg-indigo-500/15 text-indigo-300 border-indigo-500/40 font-semibold shadow-sm shrink-0">
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
        <Link href="/admin/certificates/revoked" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🚫 Revoked Certificates
        </Link>
      </div>

      <!-- Top Metrics -->
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

      <!-- Gallery Grid -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-bold text-white uppercase tracking-wider">Template Gallery</h2>
        </div>

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
            <div class="bg-slate-950 border border-slate-800 rounded-xl h-36 flex flex-col items-center justify-center p-3 relative overflow-hidden group-hover:border-indigo-500/50 transition">
              <div class="text-[10px] text-indigo-400 font-bold tracking-widest uppercase">Certificate of Completion</div>
              <div class="text-xs font-bold text-slate-300 mt-1">{student_name}</div>
              <div class="text-[9px] text-slate-500 mt-0.5">{course_name}</div>
              <div class="mt-3 flex items-center gap-3 text-[8px] text-slate-600">
                <span>[Signatures]</span>
                <span>[QR Code]</span>
              </div>
            </div>

            <div class="text-xs space-y-1 text-slate-400">
              <p>Used by: <strong class="text-slate-200">{{ t.assigned_name || 'IT & Networking' }}</strong></p>
              <p>Language: <span class="text-slate-300 font-mono text-[11px]">{{ t.language === 'kh_en' ? 'Bilingual KH/EN' : 'English' }}</span></p>
            </div>

            <div class="pt-2 border-t border-slate-800 flex items-center justify-between">
              <button @click="openEditor(t)" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1">
                ✏ Edit Template
              </button>
              <button @click="duplicateTemplate(t.id)" class="text-xs text-slate-400 hover:text-white">
                📋 Duplicate
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- ZERO-LATENCY DRAG & DROP TEMPLATE EDITOR MODAL -->
    <!-- ========================================================================= -->
    <div v-if="showEditorModal" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-6xl h-[90vh] flex flex-col shadow-2xl overflow-hidden">
        
        <!-- Modal Top Bar -->
        <div class="px-6 py-3 border-b border-slate-800 flex justify-between items-center bg-slate-950">
          <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              ✏️ TEMPLATE EDITOR — {{ templateForm.name }}
            </h3>
            <p class="text-xs text-slate-400">Drag elements directly onto the canvas to adjust positioning. Use arrow keys for 1px fine tuning.</p>
          </div>

          <div class="flex items-center gap-2">
            <button @click="previewSampleMode = !previewSampleMode" :class="previewSampleMode ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 font-bold' : 'bg-slate-800 text-slate-300 border-slate-700'" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border transition">
              {{ previewSampleMode ? '👁 Sample Data ON' : '✏️ Placeholders Mode' }}
            </button>
            <button @click="showEditorModal = false" class="text-slate-400 hover:text-white text-lg px-2">✕</button>
          </div>
        </div>

        <!-- Editor Toolbar (Zoom, Undo, Redo) -->
        <div class="px-6 py-2 bg-slate-900 border-b border-slate-800 flex items-center justify-between text-xs">
          <div class="flex items-center gap-4 text-slate-400">
            <span>Paper: <strong class="text-slate-200">A4 Landscape (800x600px)</strong></span>
            <span>Border:
              <select v-model="templateForm.border_style" class="bg-slate-950 border border-slate-800 text-slate-200 text-xs rounded px-2 py-0.5 ml-1">
                <option value="double_gold">🏆 Double Gold Seal</option>
                <option value="solid_blue">🟦 Solid Blue Frame</option>
                <option value="none">Plain / Borderless</option>
              </select>
            </span>
          </div>

          <div class="flex items-center gap-3">
            <div class="flex items-center gap-1">
              <button @click="undo" :disabled="historyIndex <= 0" class="p-1 px-2.5 bg-slate-800 hover:bg-slate-700 disabled:opacity-40 text-slate-300 rounded border border-slate-700">↺ Undo</button>
              <button @click="redo" :disabled="historyIndex >= historyStack.length - 1" class="p-1 px-2.5 bg-slate-800 hover:bg-slate-700 disabled:opacity-40 text-slate-300 rounded border border-slate-700">↻ Redo</button>
            </div>

            <div class="flex items-center gap-1">
              <span class="text-slate-400">Zoom:</span>
              <select v-model.number="canvasZoom" class="bg-slate-950 border border-slate-800 text-slate-200 text-xs rounded px-2 py-1">
                <option :value="75">75%</option>
                <option :value="100">100%</option>
                <option :value="125">125%</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Body: 3 Columns -->
        <div class="flex-1 flex overflow-hidden">
          
          <!-- Column 1: Left Palette -->
          <div class="w-64 border-r border-slate-800 p-4 space-y-4 bg-slate-950 overflow-y-auto custom-scrollbar">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Elements Palette</h4>
            <div class="space-y-1.5 text-xs">
              <button @click="addCanvasElement('logo', '🎓 Logo', '🎓 E.LMS Logo')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left flex items-center gap-2 active:scale-95 transition-transform">🎓 Logo</button>
              <button @click="addCanvasElement('variable', 'Student Name', '{student_name}')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left flex items-center gap-2 active:scale-95 transition-transform">📛 Student Name</button>
              <button @click="addCanvasElement('variable', 'Course Name', '{course_name}')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left flex items-center gap-2 active:scale-95 transition-transform">📘 Course Name</button>
              <button @click="addCanvasElement('variable', 'Major Name', '{major_name}')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left flex items-center gap-2 active:scale-95 transition-transform">🎓 Major Name</button>
              <button @click="addCanvasElement('variable', 'Issue Date', '{issue_date}')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left flex items-center gap-2 active:scale-95 transition-transform">📅 Issue Date</button>
              <button @click="addCanvasElement('variable', 'Grade & Score', '{grade}')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left flex items-center gap-2 active:scale-95 transition-transform">📊 Grade & Score</button>
              <button @click="addCanvasElement('signature', 'Signature', '{teacher_name}')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left flex items-center gap-2 active:scale-95 transition-transform">✍️ Signature</button>
              <button @click="addCanvasElement('qr', 'QR Code', '[QR Code]')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left flex items-center gap-2 active:scale-95 transition-transform">📱 QR Code</button>
              <button @click="addCanvasElement('seal', 'Faculty Seal', '🏛️ Faculty Seal')" class="w-full p-2 bg-slate-900 hover:bg-slate-800 text-slate-200 rounded-lg border border-slate-800 text-left flex items-center gap-2 active:scale-95 transition-transform">🏛️ Seal</button>
            </div>
          </div>

          <!-- Column 2: Canvas Area -->
          <div class="flex-1 bg-slate-950 p-6 flex items-center justify-center relative overflow-auto">
            <div
              id="certificate-canvas"
              :style="{ transform: `scale(${canvasZoom / 100})`, transformOrigin: 'center center' }"
              :class="{
                'border-8 border-indigo-900/40': templateForm.border_style === 'solid_blue',
                'border-[12px] border-double border-amber-600/60': templateForm.border_style === 'double_gold',
                'border-0': templateForm.border_style === 'none',
              }"
              class="w-[800px] h-[600px] bg-white text-slate-900 rounded-lg shadow-2xl relative select-none overflow-hidden"
            >
              <!-- Render all template elements with instantaneous positioning (no transition latency) -->
              <div
                v-for="el in templateForm.elements"
                :key="el.id"
                @mousedown="startDrag(el.id, $event)"
                :style="{
                  left: el.x + 'px',
                  top: el.y + 'px',
                  fontSize: el.fontSize + 'px',
                  color: el.color,
                  fontFamily: el.fontFamily || 'serif',
                  fontWeight: el.fontStyle === 'bold' ? 'bold' : 'normal',
                  fontStyle: el.fontStyle === 'italic' ? 'italic' : 'normal',
                  whiteSpace: 'nowrap',
                }"
                :class="selectedElementId === el.id ? 'ring-2 ring-indigo-600 bg-indigo-50/70 shadow-md' : 'hover:bg-slate-100/50'"
                class="absolute cursor-move px-2 py-0.5 rounded transition-[background-color,box-shadow]"
              >
                <!-- Render types -->
                <template v-if="el.type === 'logo'">🎓 E.LMS Logo</template>
                <template v-else-if="el.type === 'seal'">🏛️ Seal</template>
                <template v-else-if="el.type === 'qr'">
                  <div class="w-12 h-12 bg-slate-900 text-white text-[8px] font-sans font-bold flex items-center justify-center rounded shadow">
                    [QR Code]
                  </div>
                </template>
                <template v-else-if="el.type === 'signature'">
                  <div class="text-center font-sans">
                    <div class="border-b border-slate-800 pb-1 font-serif italic">{{ renderElementValue(el) }}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">{{ el.sub || 'Instructor' }}</div>
                  </div>
                </template>
                <template v-else>{{ renderElementValue(el) }}</template>
              </div>
            </div>
          </div>

          <!-- Column 3: Properties Inspector Panel -->
          <div class="w-72 border-l border-slate-800 p-4 space-y-4 bg-slate-950 overflow-y-auto custom-scrollbar">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Properties Inspector</h4>
            
            <div v-if="activeElement" class="space-y-4 text-xs">
              <div>
                <span class="text-slate-400 block mb-0.5">Selected Element:</span>
                <span class="font-bold text-white text-sm bg-slate-900 px-2.5 py-1 rounded border border-slate-800 block">
                  {{ activeElement.label }}
                </span>
              </div>

              <div>
                <label class="text-slate-400 block mb-1">Text Value / Placeholder:</label>
                <input v-model="activeElement.value" type="text" class="w-full bg-slate-900 border border-slate-800 rounded p-2 text-white font-mono" />
              </div>

              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="text-slate-400 block mb-1">Font Size (px):</label>
                  <input v-model.number="activeElement.fontSize" type="number" class="w-full bg-slate-900 border border-slate-800 rounded p-2 text-white" />
                </div>
                <div>
                  <label class="text-slate-400 block mb-1">Text Color:</label>
                  <input v-model="activeElement.color" type="color" class="w-full h-9 bg-slate-900 border border-slate-800 rounded cursor-pointer p-1" />
                </div>
              </div>

              <!-- Preset Palette Quick Colors -->
              <div>
                <label class="text-slate-400 block mb-1">Quick Color Palette:</label>
                <div class="flex items-center gap-1.5">
                  <button
                    v-for="c in colorPresets"
                    :key="c"
                    @click="activeElement.color = c"
                    :style="{ backgroundColor: c }"
                    class="w-6 h-6 rounded-full border border-slate-700 hover:scale-110 transition"
                  ></button>
                </div>
              </div>

              <div>
                <label class="text-slate-400 block mb-1">Font Style:</label>
                <select v-model="activeElement.fontStyle" class="w-full bg-slate-900 border border-slate-800 text-white rounded p-2">
                  <option value="normal">Normal</option>
                  <option value="bold">Bold</option>
                  <option value="italic">Italic</option>
                </select>
              </div>

              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="text-slate-400 block mb-1">X Coord (px):</label>
                  <input v-model.number="activeElement.x" type="number" class="w-full bg-slate-900 border border-slate-800 rounded p-2 text-white font-mono" />
                </div>
                <div>
                  <label class="text-slate-400 block mb-1">Y Coord (px):</label>
                  <input v-model.number="activeElement.y" type="number" class="w-full bg-slate-900 border border-slate-800 rounded p-2 text-white font-mono" />
                </div>
              </div>

              <div class="pt-2 border-t border-slate-800 space-y-2">
                <button @click="alignCenter" class="w-full py-1.5 bg-slate-900 hover:bg-slate-800 text-slate-300 rounded border border-slate-800 font-semibold flex items-center justify-center gap-1">
                  📐 Align Center
                </button>
                <button @click="deleteActiveElement" class="w-full py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded border border-red-500/20 font-semibold flex items-center justify-center gap-1">
                  🗑️ Delete Element
                </button>
              </div>
            </div>

            <div v-else class="text-slate-500 text-xs text-center py-8">
              Click an element on the canvas to inspect & edit properties.
            </div>
          </div>
        </div>

        <!-- Modal Bottom Bar -->
        <div class="px-6 py-3 border-t border-slate-800 bg-slate-950 flex justify-between items-center">
          <span class="text-xs text-slate-500">A4 Landscape Canvas Layout</span>
          <button @click="saveTemplate" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-indigo-600/30">
            💾 Save Template
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
