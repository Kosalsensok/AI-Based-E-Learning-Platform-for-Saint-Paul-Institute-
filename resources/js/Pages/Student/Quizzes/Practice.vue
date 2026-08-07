<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const isPracticing = ref(false)
const showPracticeResults = ref(false)
const isAnswerSubmitted = ref(false)
const isCorrect = ref(true)

const streakCount = ref(4)
const lives = ref(5)
const hintsRemaining = ref(2)
const selectedOpt = ref('B')

const practiceList = ref([
  { name: 'Variables Practice', qs: 15, best: '85% ⭐', attempts: '4x', status: '🟢 Good' },
  { name: 'Loops Practice', qs: 20, best: '71% ✅', attempts: '6x', status: '🟡 OK' },
  { name: '🔴 Pointer Drill', qs: 25, best: '42% ❌', attempts: '2x', status: 'Practice' },
  { name: 'Function Basics', qs: 15, best: '—', attempts: '0x', status: '🔒 Lock' }
])

const startPractice = () => {
  isPracticing.value = true
  isAnswerSubmitted.value = false
  showPracticeResults.value = false
}

const submitAnswer = () => {
  isAnswerSubmitted.value = true
  isCorrect.value = true
}

const finishPractice = () => {
  isPracticing.value = false
  showPracticeResults.value = true
}
</script>

<template>
  <StudentLayout title="Practice Quiz — Gamified Gym">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-indigo-950 border border-emerald-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider">
            🟩 PRACTICE GYM
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🟩 PRACTICE QUIZ (ហាត់អនុវត្ត)</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            រៀនឲ្យចេះ ហាត់ឥតដែនកំណត់ដង ទទួលបាន AI Hint ភ្លាមៗ និងសន្សំ XP / Level Up
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Total Practice XP</p>
            <p class="text-base font-black text-emerald-400">⚡ 2,340 XP (Lvl 12)</p>
          </div>
        </div>
      </div>

      <!-- MAIN PRACTICE SELECTION (If not actively in session) -->
      <div v-if="!isPracticing && !showPracticeResults" class="space-y-6">
        
        <!-- RECOMMENDED FOR YOU (URGENT FOCUS) CARD -->
        <div class="bg-gradient-to-r from-slate-900 via-rose-950/40 to-slate-900 border border-rose-500/40 rounded-3xl p-6 shadow-xl space-y-4">
          <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-700/60 pb-3">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
              <span>🎯</span>
              <span>RECOMMENDED FOR YOU (Based on weak topics)</span>
            </h3>
            <span class="px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 text-xs font-extrabold border border-rose-500/40 animate-pulse">
              ⚡ URGENT FOCUS
            </span>
          </div>

          <div class="p-4 bg-slate-900/90 rounded-2xl border border-slate-700/60 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <h4 class="text-base font-black text-white">🔴 Pointers Practice Drill</h4>
              <span class="text-xs font-mono text-slate-400">25 Questions • ∞ Unlimited • AI Adaptive</span>
            </div>

            <p class="text-xs text-slate-300">
              Your last score: <span class="text-rose-400 font-bold">42% ❌</span>  •  Class average: <strong>68%</strong>
            </p>

            <div class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-xs text-rose-300 font-semibold flex items-center gap-2">
              <span>🤖 AI Warning:</span>
              <span>"ចំណុច Pointers នេះនឹងចេញក្នុង Post-Test និង Final Exam!"</span>
            </div>

            <button
              @click="startPractice"
              class="px-6 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-lg transition-all hover:scale-105"
            >
              🟩 Start Focused Practice →
            </button>
          </div>
        </div>

        <!-- ALL PRACTICE QUIZZES TABLE -->
        <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
          <h3 class="text-sm font-bold text-white uppercase tracking-wider">ALL PRACTICE QUIZZES</h3>
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
              <thead class="bg-slate-900 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-700">
                <tr>
                  <th class="p-3">Quiz Name</th>
                  <th class="p-3">Qs</th>
                  <th class="p-3">My Best</th>
                  <th class="p-3">Attempts</th>
                  <th class="p-3">AI Hints</th>
                  <th class="p-3">Status</th>
                  <th class="p-3 text-right">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-700/50 font-medium">
                <tr v-for="pq in practiceList" :key="pq.name" class="hover:bg-slate-700/30">
                  <td class="p-3 font-bold text-white">{{ pq.name }}</td>
                  <td class="p-3 font-mono">{{ pq.qs }}</td>
                  <td class="p-3 font-bold text-emerald-400">{{ pq.best }}</td>
                  <td class="p-3 font-mono">{{ pq.attempts }}</td>
                  <td class="p-3 text-emerald-400">✅ On</td>
                  <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700">{{ pq.status }}</span></td>
                  <td class="p-3 text-right">
                    <button @click="startPractice" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold">
                      Practice
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!-- GAMIFIED PRACTICE SCREEN VIEW -->
      <div v-if="isPracticing" class="bg-slate-900 border border-emerald-500/40 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        
        <!-- Gamification Header Bar -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 pb-4">
          <div>
            <h2 class="text-base font-black text-white">🟩 Pointers Practice Drill</h2>
            <p class="text-xs text-slate-400">Question 5 / 25  •  Difficulty: ⭐⭐ Medium (Adaptive)</p>
          </div>

          <div class="flex items-center gap-4 text-xs font-bold">
            <span class="px-3 py-1.5 rounded-2xl bg-amber-500/20 text-amber-300 border border-amber-500/30">
              🔥 Streak: {{ streakCount }}
            </span>
            <span class="px-3 py-1.5 rounded-2xl bg-rose-500/20 text-rose-300 border border-rose-500/30">
              ❤️❤️❤️❤️❤️ {{ lives }} lives
            </span>
          </div>
        </div>

        <!-- Question Body with Code -->
        <div class="p-6 bg-slate-950 rounded-2xl border border-slate-800 space-y-4">
          <p class="text-sm font-bold text-white">Q5: What is the output of the following C code snippet?</p>

          <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 font-mono text-xs text-emerald-400 space-y-1">
            <p>int a = 10;</p>
            <p>int *p = &amp;a;</p>
            <p>printf("%d", *p);</p>
          </div>

          <!-- Options -->
          <div class="space-y-2.5 text-xs">
            <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer">
              <input type="radio" name="popt" value="A" v-model="selectedOpt" class="text-emerald-600" />
              <span class="text-slate-200">A. Memory address of a</span>
            </label>
            <label class="flex items-center gap-3 p-3.5 rounded-xl bg-emerald-900/30 border border-emerald-500/50 font-bold text-white cursor-pointer">
              <input type="radio" name="popt" value="B" v-model="selectedOpt" class="text-emerald-600" />
              <span>B. 10 (Selected)</span>
            </label>
            <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer">
              <input type="radio" name="popt" value="C" v-model="selectedOpt" class="text-emerald-600" />
              <span class="text-slate-200">C. NULL</span>
            </label>
            <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer">
              <input type="radio" name="popt" value="D" v-model="selectedOpt" class="text-emerald-600" />
              <span class="text-slate-200">D. Error</span>
            </label>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-between pt-2">
            <button class="px-3.5 py-1.5 rounded-xl bg-amber-500/20 text-amber-300 font-bold text-xs border border-amber-500/30">
              💡 Hint ({{ hintsRemaining }} left)
            </button>
            <button @click="submitAnswer" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-lg">
              ✅ Submit Answer
            </button>
          </div>
        </div>

        <!-- Immediate Feedback Box if submitted -->
        <div v-if="isAnswerSubmitted" class="p-5 bg-emerald-500/10 border border-emerald-500/40 rounded-2xl space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-sm font-black text-emerald-300">✅ Correct! +10 XP 🎉</span>
            <span class="text-xs text-amber-300 font-bold">🔥 5 Streak Active!</span>
          </div>

          <div class="space-y-1.5 text-xs text-slate-200">
            <p class="font-bold">💡 Explanation:</p>
            <p>*p ជាការ Dereference pointer variable p គឺទាញយកតម្លៃដែលរក្សាទុកក្នុង address នោះ ដែលគឺ a = 10 ដូច្នេះ *p = 10</p>
          </div>

          <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800 text-xs text-indigo-300 italic">
            🤖 AI Tip: ចាំ! * មុន pointer variable = ទាញយកតម្លៃ, &amp; មុន variable = យក Address នៃ Memory
          </div>

          <div class="pt-2">
            <button @click="finishPractice" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs shadow-md">
              Next Question / Complete Drill →
            </button>
          </div>
        </div>

      </div>

      <!-- PRACTICE RESULTS & XP SYSTEM -->
      <div v-if="showPracticeResults" class="bg-slate-900 border border-emerald-500/40 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        <div class="text-center space-y-2 border-b border-slate-800 pb-4">
          <h2 class="text-2xl font-black text-white">🟩 Practice Complete! – Pointers Drill (Attempt 3)</h2>
          <p class="text-base font-bold text-emerald-400">Score: 68% (+26% from last time! 🚀)</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
          <!-- XP Earned -->
          <div class="p-5 bg-slate-950 rounded-2xl border border-slate-800 space-y-2">
            <h4 class="font-bold text-amber-300 uppercase tracking-wider text-[11px]">🏆 XP Earned Today:</h4>
            <ul class="space-y-1 text-slate-300">
              <li>+150 XP Practice Completion</li>
              <li>+50 XP Improvement Bonus</li>
              <li>+30 XP Streak Bonus</li>
            </ul>
            <p class="pt-2 text-sm font-black text-white">Total XP: 2,340  •  Level 12 (14/20 to Lvl 13)</p>
            <p class="text-emerald-400 font-bold">New Badge Unlocked: 🔄 "Comeback Kid" (+20% Growth)!</p>
          </div>

          <!-- AI Progress -->
          <div class="p-5 bg-slate-950 rounded-2xl border border-slate-800 space-y-2">
            <h4 class="font-bold text-cyan-300 uppercase tracking-wider text-[11px]">🤖 AI Progress Update:</h4>
            <p class="text-slate-200">Pointers score improved: <span class="text-rose-400 font-bold">42%</span> → <span class="text-emerald-400 font-bold">68% ↗️</span> (+26%)</p>
            <p class="text-slate-400">Still needs work: Pointer Arithmetic (55%)</p>
          </div>
        </div>

        <div class="flex items-center justify-between pt-2">
          <button @click="showPracticeResults = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
            ← Back to Practice Gym
          </button>
          <button @click="startPractice" class="px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-md">
            ▶ Practice Pointer Arithmetic →
          </button>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
