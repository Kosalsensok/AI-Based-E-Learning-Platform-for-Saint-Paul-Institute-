<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const copiedSuccess = ref(false)
const shareSuccessMsg = ref('')

const certDetail = ref({
  id: 'ELMS-2025-000451',
  course: 'C Programming Basics',
  grade: 'A',
  score: '82%',
  issue: '15 May 2025',
  verifyUrl: 'https://elms.edu/verify/ELMS-2025-000451'
})

const copyLink = () => {
  navigator.clipboard.writeText(certDetail.value.verifyUrl)
  copiedSuccess.value = true
  setTimeout(() => copiedSuccess.value = false, 2500)
}

const shareTo = (platform: string) => {
  shareSuccessMsg.value = `Shared certificate ${certDetail.value.id} to ${platform}!`
  setTimeout(() => shareSuccessMsg.value = '', 3000)
}
</script>

<template>
  <StudentLayout title="Download & Share Certificate">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-indigo-950 via-slate-900 to-purple-950 border border-indigo-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-bold uppercase tracking-wider">
            📤 EXPORT &amp; SOCIAL SHARE
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>📤 DOWNLOAD / SHARE — {{ certDetail.id }}</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ទាញយកឯកសារវិញ្ញាបនបត្រជា PDF, PNG រូបភាព និងចែករំលែកទៅកាន់ LinkedIn, WhatsApp, Email
          </p>
        </div>
      </div>

      <!-- MAIN EXPORT CARD (Matching Prompt Spec Layout) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        
        <!-- Summary Info -->
        <div class="border-b border-slate-700/60 pb-4 space-y-1 text-xs">
          <h2 class="text-lg font-black text-white">Course: {{ certDetail.course }}</h2>
          <div class="flex items-center gap-4 text-emerald-400 font-bold">
            <span>Grade: {{ certDetail.grade }}</span>
            <span>Score: {{ certDetail.score }}</span>
            <span class="text-slate-400 font-normal">Issued: {{ certDetail.issue }}</span>
          </div>
          <p class="text-slate-300 pt-1">
            Verify Link: <code class="bg-slate-950 px-2 py-0.5 rounded text-indigo-300 font-mono">{{ certDetail.verifyUrl }}</code>
          </p>
        </div>

        <!-- QR CODE PREVIEW BOX -->
        <div class="p-6 bg-slate-950 rounded-2xl border border-slate-800 flex flex-col sm:flex-row items-center gap-6">
          <div class="p-3 bg-white rounded-2xl border-4 border-emerald-500 shadow-xl">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=elms.edu/verify/ELMS-2025-000451" alt="QR Preview" class="w-36 h-36" />
          </div>
          <div class="space-y-2 text-xs text-slate-300">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">QR CODE VERIFICATION</h3>
            <p>• ស្កែន QR ដើម្បីចូលទៅកាន់ទំព័រ Verification ផ្លូវការរបស់ប្រព័ន្ធ</p>
            <p>• បង្ហាញតែព័ត៌មាន Grade និង Name (រក្សាការសម្ងាត់ Email)</p>
          </div>
        </div>

        <!-- Alert messages -->
        <div v-if="copiedSuccess" class="p-3 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-xs text-emerald-300 font-bold">
          ✅ Verification link copied to clipboard!
        </div>
        <div v-if="shareSuccessMsg" class="p-3 bg-indigo-500/10 border border-indigo-500/30 rounded-xl text-xs text-indigo-300 font-bold">
          📲 {{ shareSuccessMsg }}
        </div>

        <!-- ACTION BUTTONS GRID (Matching Prompt Spec) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <button class="px-4 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-lg transition-all flex items-center justify-center gap-2">
            📥 Download PDF
          </button>
          <button class="px-4 py-3 rounded-2xl bg-slate-900 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 flex items-center justify-center gap-2">
            📎 Download Image (optional)
          </button>
          <button @click="copyLink" class="px-4 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md flex items-center justify-center gap-2">
            🔗 Copy Verify Link
          </button>
          <button @click="shareTo('WhatsApp')" class="px-4 py-3 rounded-2xl bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs shadow-md flex items-center justify-center gap-2">
            📤 Share to WhatsApp / Email
          </button>
          <button @click="shareTo('LinkedIn')" class="px-4 py-3 rounded-2xl bg-blue-700 hover:bg-blue-600 text-white font-bold text-xs shadow-md flex items-center justify-center gap-2">
            📲 Share to LinkedIn
          </button>
        </div>

        <!-- Privacy Note -->
        <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 text-xs text-slate-400">
          🔒 <strong>Privacy Note:</strong> Only public verification info is shared (no private email or personal data displayed).
        </div>

      </div>

    </div>
  </StudentLayout>
</template>
