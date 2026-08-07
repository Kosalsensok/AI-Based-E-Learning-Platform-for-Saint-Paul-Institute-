<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const showQrModal = ref<any | null>(null)

const certList = ref([
  {
    id: 'ELMS-2025-000451',
    course: 'C Programming Basics',
    major: 'IT & Networking',
    grade: 'A',
    score: '82%',
    issued: '15 May 2025',
    status: '🟢 Issued/Valid',
    active: true
  },
  {
    id: 'ELMS-2025-000398',
    course: 'English Grammar & Tech Writing',
    major: 'English Literature',
    grade: 'B+',
    score: '78%',
    issued: '10 May 2025',
    status: '🟢 Issued/Valid',
    active: true
  }
])
</script>

<template>
  <StudentLayout title="My Certificates">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-indigo-950 border border-emerald-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider">
            🏅 AUTO-ISSUED DEGREES
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🏅 MY CERTIFICATES</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            បញ្ជីវិញ្ញាបនបត្រឌីជីថល ផ្ទៀងផ្ទាត់ដោយ QR Code &amp; Verified Security Check
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button class="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md">
            📥 Download All
          </button>
        </div>
      </div>

      <!-- STATUS LEGEND & FILTERS -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-5 shadow-xl flex flex-wrap items-center justify-between gap-4 text-xs">
        <div class="flex items-center gap-3">
          <span class="flex items-center gap-1.5 text-emerald-400 font-bold">
            🟢 Issued/Valid = មានសុពលភាព
          </span>
          <span class="flex items-center gap-1.5 text-rose-400 font-bold">
            🔴 Revoked = ត្រូវបានដកហូត
          </span>
        </div>

        <div class="flex items-center gap-2">
          <input type="text" placeholder="Search Cert ID or Course..." class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white placeholder-slate-500" />
          <select class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-slate-300">
            <option>Status: All</option>
            <option>Valid</option>
            <option>Revoked</option>
          </select>
        </div>
      </div>

      <!-- CERTIFICATES GRID (Matching Prompt Spec Layout) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          v-for="cert in certList"
          :key="cert.id"
          class="bg-slate-800/90 border border-emerald-500/40 rounded-3xl p-6 shadow-2xl space-y-4 hover:border-emerald-400 transition-all"
        >
          <!-- Card Header -->
          <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 font-bold text-xs border border-emerald-500/30">
              {{ cert.status }}
            </span>
            <span class="text-xs font-mono text-slate-400 font-bold">Cert ID: {{ cert.id }}</span>
          </div>

          <!-- Card Details -->
          <div class="space-y-1.5 text-xs">
            <h3 class="text-base font-black text-white">Course: {{ cert.course }}</h3>
            <p class="text-slate-300">Major: <span class="text-indigo-300 font-bold">{{ cert.major }}</span></p>
            <div class="flex items-center gap-4 text-emerald-400 font-bold pt-1">
              <span>Grade: {{ cert.grade }}</span>
              <span>Score: {{ cert.score }}</span>
              <span class="text-slate-400 font-normal">Issued: {{ cert.issued }}</span>
            </div>
          </div>

          <!-- Card Actions Grid -->
          <div class="grid grid-cols-4 gap-2 pt-2 border-t border-slate-700/60">
            <button @click="showQrModal = cert" class="px-2 py-2 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold text-[11px] border border-slate-700">
              📱 QR
            </button>
            <Link href="/student/certificates/download-share" class="px-2 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] text-center shadow-md">
              📥 Download
            </Link>
            <Link href="/student/certificates/download-share" class="px-2 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[11px] text-center shadow-md">
              📤 Share
            </Link>
            <Link href="/student/certificates/verify" class="px-2 py-2 rounded-xl bg-slate-900 hover:bg-slate-700 text-cyan-300 font-bold text-[11px] text-center border border-slate-700">
              🔍 Verify
            </Link>
          </div>
        </div>
      </div>

      <!-- QR CODE MODAL -->
      <div v-if="showQrModal" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-emerald-500/50 rounded-3xl max-w-sm w-full p-6 text-center space-y-4 shadow-2xl">
          <div class="border-b border-slate-800 pb-2">
            <h3 class="text-sm font-black text-white">📱 Digital Verification QR</h3>
            <p class="text-xs font-mono text-emerald-400">{{ showQrModal.id }}</p>
          </div>

          <div class="p-4 bg-white rounded-2xl border-4 border-emerald-500 shadow-xl inline-block">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=elms.edu/verify/ELMS-2025-000451" alt="Cert QR" class="w-44 h-44 mx-auto" />
            <p class="text-[10px] text-slate-900 font-black mt-2 uppercase tracking-wider">OFFICIAL E.LMS CERTIFICATE</p>
          </div>

          <div class="flex justify-center">
            <button @click="showQrModal = null" class="px-5 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
              Close
            </button>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
