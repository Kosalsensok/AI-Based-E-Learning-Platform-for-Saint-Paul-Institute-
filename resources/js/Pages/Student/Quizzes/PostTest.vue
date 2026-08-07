<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const showBriefingModal = ref(false)
const isExamActive = ref(false)
const showExamResults = ref(false)

const currentQuestion = ref(8)
const totalQuestions = ref(30)
const selectedOpt = ref('B')
const tabSwitchCount = ref(0)
const flaggedList = ref([7])
const answeredList = ref([1, 2, 3, 4, 5, 6, 8])

const enterExam = () => {
  showBriefingModal.value = false
  isExamActive.value = true
}

const submitExam = () => {
  isExamActive.value = false
  showExamResults.value = true
}
</script>

<template>
  <StudentLayout title="Post-Test — Formal Final Exam">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-rose-950 via-slate-900 to-indigo-950 border border-rose-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-bold uppercase tracking-wider">
            🟥 POST-TEST EXAM
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🟥 POST-TEST (ប្រឡងផ្លូវការ)</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ការប្រឡងផ្លូវការបញ្ចប់ Module — គិតចូល Final Grade 40% បើក Certificate និងមាន Anti-Cheat
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Passed Exams</p>
            <p class="text-base font-black text-rose-400">2 / 3 Passed ✅</p>
          </div>
        </div>
      </div>

      <!-- MAIN CONTENT: POST-TEST DASHBOARD -->
      <div v-if="!isExamActive && !showExamResults" class="space-y-6">
        
        <!-- AVAILABLE POST-TEST (MODULE 3) -->
        <div class="bg-slate-800/90 border border-rose-500/40 rounded-3xl p-6 shadow-xl space-y-4">
          <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-700/60 pb-3">
            <div>
              <span class="px-3 py-1 rounded-full bg-rose-600/20 text-rose-300 font-bold text-xs border border-rose-500/40">
                🟥 Post-Test: Module 3 – Loops
              </span>
              <p class="text-xs text-slate-400 mt-1">
                30 Questions  •  ⏱️ 30 minutes  •  🎯 Pass: 70%  •  3 Attempts allowed
              </p>
            </div>

            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">
              🔓 AVAILABLE NOW
            </span>
          </div>

          <!-- Impact & Rules -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="p-4 bg-slate-900/70 rounded-2xl border border-slate-700/60 space-y-2">
              <h4 class="font-bold text-white uppercase text-[11px]">🎓 Affects Your Standing:</h4>
              <ul class="space-y-1 text-slate-300">
                <li>• Final Course Grade (40% weight)</li>
                <li>• Certificate Eligibility</li>
                <li>• Unlocks Module 4: Functions</li>
              </ul>
            </div>

            <div class="p-4 bg-slate-900/70 rounded-2xl border border-slate-700/60 space-y-2">
              <h4 class="font-bold text-white uppercase text-[11px]">⚠️ Anti-Cheat Rules:</h4>
              <ul class="space-y-1 text-slate-300">
                <li>• Fullscreen mode strictly enforced</li>
                <li>• Tab switching monitored (>3 = auto-flag 🚩)</li>
                <li>• Copy/Paste & DevTools disabled</li>
              </ul>
            </div>
          </div>

          <div class="pt-2">
            <button
              @click="showBriefingModal = true"
              class="w-full md:w-auto px-8 py-3 rounded-2xl bg-rose-600 hover:bg-rose-500 text-white font-black text-sm shadow-xl transition-all hover:scale-105"
            >
              🟥 Start Post-Test Exam →
            </button>
          </div>
        </div>

        <!-- PASSED POST-TESTS LIST -->
        <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-3">
          <h3 class="text-sm font-bold text-white uppercase tracking-wider">COMPLETED EXAMS HISTORY</h3>
          <div class="space-y-2 text-xs">
            <div class="p-3 bg-slate-900/70 rounded-xl border border-slate-700/60 flex items-center justify-between">
              <span class="font-bold text-slate-200">🟥 Post-Test: Module 1 – Intro</span>
              <span class="text-emerald-400 font-bold">✅ PASSED (88%) • Attempt 1/3</span>
            </div>
            <div class="p-3 bg-slate-900/70 rounded-xl border border-slate-700/60 flex items-center justify-between">
              <span class="font-bold text-slate-200">🟥 Post-Test: Module 2 – Variables</span>
              <span class="text-emerald-400 font-bold">✅ PASSED (82%) • Attempt 2/3</span>
            </div>
          </div>
        </div>

      </div>

      <!-- PRE-EXAM BRIEFING MODAL -->
      <div v-if="showBriefingModal" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-rose-500/50 rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl">
          <div class="border-b border-slate-800 pb-3">
            <span class="px-3 py-0.5 rounded-full bg-rose-500/20 text-rose-300 text-[10px] font-bold">🚨 EXAM MODE BRIEFING</span>
            <h3 class="text-lg font-black text-white mt-1">🟥 Post-Test: Loops – C Programming</h3>
          </div>

          <div class="space-y-3 text-xs text-slate-300">
            <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1 font-mono">
              <p>• 30 Questions (Random from Bank of 100)</p>
              <p>• Time Limit: 30 minutes (strict)</p>
              <p>• Passing Score: 70%</p>
            </div>

            <div class="space-y-1.5">
              <h4 class="font-bold text-white uppercase text-[11px]">🔒 Anti-Cheat Measures Active:</h4>
              <p>☑️ Fullscreen mode will be enforced</p>
              <p>☑️ Tab switching monitored (&gt;3 = auto-flag)</p>
              <p>☑️ Copy/Paste function disabled</p>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
            <button @click="showBriefingModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
              ❌ Not Yet – Review First
            </button>
            <button @click="enterExam" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-black text-xs shadow-lg">
              🟥 Enter Exam Now →
            </button>
          </div>
        </div>
      </div>

      <!-- POST-TEST SECURE EXAM SCREEN -->
      <div v-if="isExamActive" class="bg-slate-900 border border-rose-500/60 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 pb-4">
          <div>
            <h2 class="text-base font-black text-white flex items-center gap-2">
              <span>🟥 POST-TEST: Loops</span>
              <span class="px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300 text-[10px] font-bold border border-rose-500/30">🔒 Secure Exam Mode</span>
            </h2>
            <p class="text-xs text-slate-400">Question 8 of 30 (27% completed)</p>
          </div>

          <span class="px-4 py-2 rounded-2xl bg-rose-500/20 text-rose-300 font-mono font-bold text-sm border border-rose-500/40">
            🕐 24:38 remaining
          </span>
        </div>

        <!-- Question Viewport -->
        <div class="p-6 bg-slate-950 rounded-2xl border border-slate-800 space-y-4">
          <p class="text-sm font-bold text-white">Q8: What is the exact output of the following nested loop code?</p>

          <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 font-mono text-xs text-emerald-400 space-y-1">
            <p>for(int i=0; i&lt;3; i++) {</p>
            <p>&nbsp;&nbsp;for(int j=0; j&lt;3; j++) {</p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;printf("*");</p>
            <p>&nbsp;&nbsp;}</p>
            <p>&nbsp;&nbsp;printf("\n");</p>
            <p>}</p>
          </div>

          <!-- Options -->
          <div class="space-y-2.5 text-xs font-mono">
            <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer">
              <input type="radio" name="eopt" value="A" v-model="selectedOpt" class="text-rose-600" />
              <span>A. *** \n ***</span>
            </label>
            <label class="flex items-center gap-3 p-3.5 rounded-xl bg-rose-900/30 border border-rose-500/50 font-bold text-white cursor-pointer">
              <input type="radio" name="eopt" value="B" v-model="selectedOpt" class="text-rose-600" />
              <span>B. 3x3 Grid of Asterisks (Selected)</span>
            </label>
            <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer">
              <input type="radio" name="eopt" value="C" v-model="selectedOpt" class="text-rose-600" />
              <span>C. Single Column of 3</span>
            </label>
            <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer">
              <input type="radio" name="eopt" value="D" v-model="selectedOpt" class="text-rose-600" />
              <span>D. 9 Asterisks on one line</span>
            </label>
          </div>
        </div>

        <!-- Question Navigation Row -->
        <div class="flex items-center justify-between pt-2">
          <button @click="currentQuestion > 1 && currentQuestion--" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
            ← Previous
          </button>
          <button @click="submitExam" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-black text-xs shadow-lg">
            📤 Submit All Exam Answers
          </button>
        </div>
      </div>

      <!-- POST-TEST RESULTS VIEW -->
      <div v-if="showExamResults" class="bg-slate-900 border border-rose-500/50 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        <div class="text-center space-y-2 border-b border-slate-800 pb-5">
          <span class="px-4 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 font-extrabold text-xs border border-emerald-500/40">
            🎉 PASSED! Score: 82% (Attempt 1/3)
          </span>
          <h2 class="text-2xl font-black text-white">🟥 Post-Test Results – Module 3: Loops</h2>
          <p class="text-xs text-slate-400">Passing Threshold: 70% ✅</p>
        </div>

        <div class="p-5 bg-slate-950 rounded-2xl border border-slate-800 space-y-3 text-xs">
          <h4 class="font-bold text-white uppercase tracking-wider">🎓 Grade Impact & Progress:</h4>
          <p class="text-slate-200">• Weight: 40% of Final Course Grade</p>
          <p class="text-emerald-400 font-bold">• Estimated Grade Update: B+ → A- 🚀</p>
          <p class="text-indigo-300 font-bold">• 🔓 Module 4: Functions – NOW UNLOCKED!</p>
        </div>

        <div class="flex items-center justify-between pt-2">
          <button @click="showExamResults = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
            ← Back to Post-Tests
          </button>
          <button class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-xs shadow-md">
            ▶ Continue to Module 4 →
          </button>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
