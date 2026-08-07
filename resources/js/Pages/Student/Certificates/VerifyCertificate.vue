<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const certInput = ref('ELMS-2025-000451')
const isVerified = ref(true)

const verifyResult = ref({
  result: '✅ VALID',
  holder: 'Chan Dara',
  course: 'C Programming Basics',
  major: 'IT & Networking',
  gradeScore: 'A (82%)',
  issuedOn: '15 May 2025',
  status: 'Active',
  verifiedBy: 'Public Verification Page',
  lastCheck: 'Today, 10:12 AM'
})

const handleVerify = () => {
  isVerified.value = true
}
</script>

<template>
  <StudentLayout title="Verify Certificate">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-cyan-950 via-slate-900 to-indigo-950 border border-cyan-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold uppercase tracking-wider">
            🔍 PUBLIC VERIFICATION GATEWAY
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🔍 VERIFY CERTIFICATE</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ផ្ទៀងផ្ទាត់ភាពត្រឹមត្រូវនៃវិញ្ញាបនបត្រ តាមរយៈ Certificate ID ឬ QR Code
          </p>
        </div>
      </div>

      <!-- SEARCH BAR CARD -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-3">
        <label class="text-xs font-bold text-white uppercase tracking-wider block">Enter Certificate ID:</label>
        <div class="flex items-center gap-3">
          <input
            v-model="certInput"
            type="text"
            placeholder="e.g. ELMS-2025-000451"
            class="flex-1 bg-slate-900 border border-slate-700 rounded-2xl px-4 py-3 text-sm text-white font-mono font-bold focus:outline-none focus:border-cyan-500"
          />
          <button @click="handleVerify" class="px-6 py-3 rounded-2xl bg-cyan-600 hover:bg-cyan-500 text-white font-black text-xs shadow-md">
            🔍 Verify
          </button>
        </div>
      </div>

      <!-- VERIFICATION RESULT BOX (Matching Prompt Spec Layout) -->
      <div v-if="isVerified" class="bg-slate-800/90 border border-emerald-500/50 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-4">
          <div>
            <span class="text-xs text-slate-400 font-bold uppercase">Verification Status</span>
            <h2 class="text-2xl font-black text-emerald-400 mt-0.5">Result: {{ verifyResult.result }}</h2>
          </div>
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold font-mono">
            STATUS: ACTIVE
          </span>
        </div>

        <!-- Result Fields Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-mono">
          <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
            <span class="text-[10px] text-slate-400 font-bold uppercase">Certificate Holder</span>
            <p class="text-sm font-bold text-white font-sans">{{ verifyResult.holder }}</p>
          </div>
          <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
            <span class="text-[10px] text-slate-400 font-bold uppercase">Course Name</span>
            <p class="text-sm font-bold text-white font-sans">{{ verifyResult.course }}</p>
          </div>
          <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
            <span class="text-[10px] text-slate-400 font-bold uppercase">Major</span>
            <p class="text-sm font-bold text-indigo-300 font-sans">{{ verifyResult.major }}</p>
          </div>
          <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
            <span class="text-[10px] text-slate-400 font-bold uppercase">Grade / Score</span>
            <p class="text-sm font-bold text-emerald-400 font-sans">{{ verifyResult.gradeScore }}</p>
          </div>
          <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
            <span class="text-[10px] text-slate-400 font-bold uppercase">Issued On</span>
            <p class="text-sm font-bold text-slate-300">{{ verifyResult.issuedOn }}</p>
          </div>
          <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
            <span class="text-[10px] text-slate-400 font-bold uppercase">Last Checked</span>
            <p class="text-sm font-bold text-slate-400">{{ verifyResult.lastCheck }}</p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-700/60">
          <Link href="/student/certificates/download-share" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md">
            📥 View Certificate PDF
          </Link>
          <div class="flex items-center gap-2">
            <Link href="/student/certificates/download-share" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md">
              📤 Share
            </Link>
            <Link href="/student/certificates/my-certificates" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700">
              ↩ Back
            </Link>
          </div>
        </div>

      </div>

    </div>
  </StudentLayout>
</template>
