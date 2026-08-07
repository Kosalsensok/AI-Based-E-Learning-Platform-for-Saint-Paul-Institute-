<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  initialCode?: string
  status?: string // 'valid', 'revoked', 'not_found', 'pending'
  student_name?: string
  student_id?: string
  course_name?: string
  major_name?: string
  grade?: string
  score?: number | string
  issued_at?: string
  certificate_number?: string
  revoked_at?: string
  revocation_reason?: string
  checked_at?: string
}>()

const searchCode = ref(props.initialCode || props.certificate_number || '')
const isScanning = ref(false)
const showImageModal = ref(false)

const handleVerify = () => {
  if (!searchCode.value.trim()) return
  router.get(`/certificate/verify/${searchCode.value.trim()}`, {}, { preserveState: false })
}

const toggleScanner = () => {
  isScanning.value = !isScanning.value
  if (isScanning.value) {
    // Simulate instant camera scan fill
    setTimeout(() => {
      searchCode.value = 'ELMS-2025-000451'
      isScanning.value = false
      handleVerify()
    }, 1800)
  }
}
</script>

<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-indigo-500/30">
    <!-- Header Navbar -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-xl sticky top-0 z-50">
      <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-cyan-500 to-emerald-400 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-indigo-500/20">
            🎓
          </div>
          <div>
            <h1 class="text-base font-bold text-white tracking-tight flex items-center gap-2">
              E.LMS Certification Portal
              <span class="px-2 py-0.5 text-[10px] font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-full">
                Official Verification
              </span>
            </h1>
            <p class="text-xs text-slate-400">Public Verification System · No Login Required</p>
          </div>
        </div>

        <a href="/login" class="text-xs font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 px-3 py-1.5 rounded-lg transition border border-slate-700">
          Admin Portal →
        </a>
      </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-3xl w-full mx-auto px-4 py-10 space-y-8">
      <!-- Title & Intro -->
      <div class="text-center space-y-3">
        <h2 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-indigo-300 via-cyan-200 to-white tracking-tight">
          🔍 Verify a Certificate
        </h2>
        <p class="text-sm text-slate-400 max-w-md mx-auto">
          Enter the unique Certificate ID (e.g., <code class="text-indigo-400 font-mono">ELMS-2025-000451</code>) or scan the QR code printed on the official document.
        </p>
      </div>

      <!-- Search & QR Bar Card -->
      <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-2xl space-y-4">
        <form @submit.prevent="handleVerify" class="flex flex-col sm:flex-row gap-3">
          <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
              🏷️
            </div>
            <input
              v-model="searchCode"
              type="text"
              placeholder="Enter Certificate ID: ELMS-2025-000451"
              class="w-full pl-10 pr-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm font-mono text-white placeholder:text-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
            />
          </div>

          <button
            type="submit"
            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center justify-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Verify Now
          </button>
        </form>

        <div class="flex items-center justify-center gap-2 pt-2">
          <span class="text-xs text-slate-500">or</span>
          <button
            @click="toggleScanner"
            type="button"
            class="inline-flex items-center gap-2 text-xs font-semibold text-cyan-400 hover:text-cyan-300 bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/20 px-3.5 py-1.5 rounded-lg transition"
          >
            📱 {{ isScanning ? 'Cancel Camera Scanner' : 'Scan QR Code' }}
          </button>
        </div>

        <!-- Simulated Camera Scan Viewfinder -->
        <div v-if="isScanning" class="relative bg-slate-950 border-2 border-dashed border-cyan-500/50 rounded-xl p-8 text-center space-y-3 overflow-hidden">
          <div class="w-16 h-16 border-4 border-cyan-400 border-t-transparent rounded-full animate-spin mx-auto"></div>
          <p class="text-xs font-mono text-cyan-300">Point your camera at the certificate QR code...</p>
          <span class="text-[10px] text-slate-500 animate-pulse">Detecting E.LMS Certificate Matrix...</span>
        </div>
      </div>

      <!-- Verification Results States -->
      <div v-if="status" class="transition-all duration-300">
        <!-- 🟢 VALID STATE -->
        <div v-if="status === 'valid'" class="bg-slate-900 border-2 border-emerald-500/40 rounded-2xl p-6 sm:p-8 space-y-6 shadow-2xl shadow-emerald-500/5 relative overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

          <!-- Header status banner -->
          <div class="flex items-center gap-4 border-b border-slate-800 pb-5">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-2xl shadow-inner text-emerald-400 shrink-0">
              ✅
            </div>
            <div>
              <div class="inline-flex items-center gap-2 px-2.5 py-0.5 text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-full uppercase tracking-wider">
                CERTIFICATE IS VALID
              </div>
              <h3 class="text-xl font-extrabold text-white mt-1">Official Academic Credentials Verified</h3>
              <p class="text-xs text-slate-400">Issued by E.LMS — Faculty of Computing</p>
            </div>
          </div>

          <!-- Details Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
              <span class="text-slate-400 block mb-0.5">Certificate Holder:</span>
              <span class="text-sm font-bold text-white">{{ student_name || 'Chan Dara' }}</span>
            </div>

            <div class="bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
              <span class="text-slate-400 block mb-0.5">Student ID:</span>
              <span class="text-sm font-mono font-bold text-slate-200">{{ student_id || 'STU24001' }}</span>
            </div>

            <div class="bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
              <span class="text-slate-400 block mb-0.5">Course Completed:</span>
              <span class="text-sm font-bold text-indigo-300">{{ course_name || 'C Programming Basics' }}</span>
            </div>

            <div class="bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
              <span class="text-slate-400 block mb-0.5">Major / Specialization:</span>
              <span class="text-sm font-semibold text-slate-200">{{ major_name || 'IT & Networking' }}</span>
            </div>

            <div class="bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
              <span class="text-slate-400 block mb-0.5">Academic Evaluation:</span>
              <span class="text-sm font-bold text-emerald-400">Grade: {{ grade || 'A' }} ({{ score || 82 }}%)</span>
            </div>

            <div class="bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
              <span class="text-slate-400 block mb-0.5">Issued On:</span>
              <span class="text-sm font-semibold text-slate-200">{{ issued_at || '16 June 2025' }}</span>
            </div>

            <div class="bg-slate-950/60 p-3.5 rounded-xl border border-slate-800 sm:col-span-2">
              <span class="text-slate-400 block mb-0.5">Certificate ID:</span>
              <span class="text-sm font-mono font-bold text-cyan-400">{{ certificate_number || 'ELMS-2025-000451' }}</span>
            </div>
          </div>

          <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3">
            <button
              @click="showImageModal = true"
              type="button"
              class="w-full sm:w-auto px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl border border-slate-700 transition flex items-center justify-center gap-2"
            >
              📥 View Certificate Image
            </button>
            <span class="text-[11px] text-slate-500">
              Verification checked on {{ checked_at || '16 Jun 2025, 14:20' }} (logged for security)
            </span>
          </div>
        </div>

        <!-- 🔴 REVOKED STATE -->
        <div v-else-if="status === 'revoked'" class="bg-slate-900 border-2 border-red-500/50 rounded-2xl p-6 sm:p-8 space-y-6 shadow-2xl shadow-red-500/10">
          <div class="flex items-center gap-4 border-b border-slate-800 pb-5">
            <div class="w-14 h-14 rounded-2xl bg-red-500/15 border border-red-500/30 flex items-center justify-center text-2xl text-red-400 shrink-0">
              🔴
            </div>
            <div>
              <div class="inline-flex items-center gap-2 px-2.5 py-0.5 text-xs font-bold bg-red-500/20 text-red-400 border border-red-500/30 rounded-full uppercase tracking-wider">
                CERTIFICATE REVOKED
              </div>
              <h3 class="text-xl font-extrabold text-white mt-1">This Certificate Has Been Revoked</h3>
              <p class="text-xs text-slate-400">This document is no longer recognized as valid by E.LMS.</p>
            </div>
          </div>

          <div class="bg-slate-950 p-4 rounded-xl border border-red-900/40 text-xs space-y-2 text-slate-300">
            <p><strong>Certificate ID:</strong> <code class="text-red-400 font-mono">{{ certificate_number }}</code></p>
            <p><strong>Revocation Reason:</strong> <span class="text-red-300 font-medium">{{ revocation_reason || 'Plagiarism detected' }}</span></p>
            <p v-if="revoked_at"><strong>Revoked Date:</strong> <span>{{ revoked_at }}</span></p>
          </div>
        </div>

        <!-- ⚪ NOT FOUND STATE -->
        <div v-else-if="status === 'not_found'" class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-4 text-center">
          <div class="w-14 h-14 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center text-2xl text-slate-400 mx-auto">
            ⚪
          </div>
          <h3 class="text-xl font-bold text-white">Certificate Not Found</h3>
          <p class="text-xs text-slate-400 max-w-md mx-auto">
            No record matches Certificate ID <code class="text-yellow-400 font-mono">{{ certificate_number || searchCode }}</code>. Please check the spelling or contact academic services to report possible fraudulent documents.
          </p>
        </div>
      </div>
    </main>

    <!-- Certificate Preview Modal -->
    <div v-if="showImageModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-2xl w-full p-6 space-y-4 relative">
        <button @click="showImageModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-white">✕</button>
        <h4 class="text-base font-bold text-white">🏅 Certificate Document Preview</h4>
        
        <!-- Live Render Anatomy Mockup -->
        <div class="bg-slate-950 p-6 rounded-xl border border-slate-800 text-center space-y-4 font-serif text-slate-200">
          <div class="flex justify-between items-center text-xs text-slate-400">
            <span>🎓 E.LMS Logo</span>
            <span>🏛️ Faculty Seal</span>
          </div>

          <p class="text-xs tracking-widest text-indigo-400 uppercase font-sans">Certificate of Completion</p>
          <p class="text-[11px] text-slate-400 italic">This is to certify that</p>
          <h3 class="text-2xl font-bold text-white font-sans">{{ student_name || 'Chan Dara' }}</h3>
          <p class="text-xs text-slate-400">has successfully completed the course</p>
          <p class="text-lg font-bold text-cyan-300 font-sans">{{ course_name || 'C Programming Basics' }}</p>
          <p class="text-xs text-slate-400">Grade: {{ grade || 'A' }} · Score: {{ score || 82 }}%</p>

          <div class="pt-6 border-t border-slate-800 flex justify-between items-center text-[10px] font-sans text-slate-400">
            <div>
              <p class="font-mono text-cyan-400">{{ certificate_number || 'ELMS-2025-000451' }}</p>
              <p>Issued: {{ issued_at || '16 June 2025' }}</p>
            </div>
            <div class="w-12 h-12 bg-white text-slate-950 p-1 rounded font-bold text-[8px] flex items-center justify-center">
              [QR]
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
