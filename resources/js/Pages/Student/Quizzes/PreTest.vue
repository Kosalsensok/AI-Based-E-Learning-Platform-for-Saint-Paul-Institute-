<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const showBriefingModal = ref(false)
const isTakingTest = ref(false)
const showResults = ref(false)

const currentQuestion = ref(4)
const totalQuestions = ref(10)
const selectedOption = ref('C')
const flaggedQuestions = ref<number[]>([8])
const answeredQuestions = ref<number[]>([1, 2, 3, 4])

const startTest = () => {
  showBriefingModal.value = false
  isTakingTest.value = true
}

const submitTest = () => {
  isTakingTest.value = false
  showResults.value = true
}

const toggleFlag = (qNum: number) => {
  if (flaggedQuestions.value.includes(qNum)) {
    flaggedQuestions.value = flaggedQuestions.value.filter(n => n !== qNum)
  } else {
    flaggedQuestions.value.push(qNum)
  }
}
</script>

<template>
  <StudentLayout title="Pre-Test — Quiz & Assessment">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 border border-blue-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold uppercase tracking-wider">
            🟦 PRE-TEST MODULE
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🟦 PRE-TEST (ប្រឡងមុនរៀន)</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            វាស់ចំណេះដឹងដើម មុនរៀន Module បើកសោរមេរៀន និងកំណត់ AI Learning Path ផ្ទាល់ខ្លួន
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Pre-Tests Completed</p>
            <p class="text-base font-black text-blue-400">1 / 4 Modules</p>
          </div>
        </div>
      </div>

      <!-- MAIN CONTENT: PRE-TEST DASHBOARD (If not taking test) -->
      <div v-if="!isTakingTest && !showResults" class="space-y-6">
        
        <!-- REQUIRED PRE-TEST CARD (MODULE 2) -->
        <div class="bg-slate-800/90 border border-blue-500/40 rounded-3xl p-6 shadow-xl space-y-4">
          <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-700/60 pb-4">
            <div>
              <span class="px-3 py-1 rounded-full bg-blue-600/20 text-blue-300 font-bold text-xs border border-blue-500/40">
                🟦 Pre-Test: Module 2 – Variables & Data Types
              </span>
              <p class="text-xs text-slate-400 mt-1">
                📦 Module 2  •  10 Questions  •  ⏱️ 15 minutes  •  2 attempts
              </p>
            </div>

            <span class="px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 text-xs font-bold border border-rose-500/30">
              🔴 Required Before Starting Module 2
            </span>
          </div>

          <!-- Alert Note -->
          <div class="p-3.5 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-xs text-rose-300 font-semibold flex items-center gap-2">
            <span>⚠️</span>
            <span>Module 2 is locked until you complete this Pre-Test to assess your baseline knowledge.</span>
          </div>

          <!-- Details & Tips -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="p-4 bg-slate-900/70 rounded-2xl border border-slate-700/60 space-y-2">
              <h4 class="font-bold text-white uppercase text-[11px] tracking-wider">🎯 What to expect:</h4>
              <ul class="space-y-1.5 text-slate-300">
                <li>✅ MCQ Questions about Data Types</li>
                <li>✅ True/False on Variable Declaration</li>
                <li>✅ No trick questions – just check your starting level</li>
              </ul>
            </div>

            <div class="p-4 bg-slate-900/70 rounded-2xl border border-slate-700/60 space-y-2">
              <h4 class="font-bold text-white uppercase text-[11px] tracking-wider">ℹ️ Important Rules:</h4>
              <ul class="space-y-1.5 text-slate-300">
                <li>• This score does <strong>NOT</strong> affect your final grade</li>
                <li>• Answers are used by AI to build your customized path</li>
                <li>• Instant solution explanations upon submission</li>
              </ul>
            </div>
          </div>

          <!-- Action Button -->
          <div class="pt-2">
            <button
              @click="showBriefingModal = true"
              class="w-full md:w-auto px-8 py-3 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-black text-sm shadow-xl transition-all hover:scale-105"
            >
              🟦 Start Pre-Test Now →
            </button>
          </div>
        </div>

        <!-- COMPLETED PRE-TEST CARD (MODULE 1) -->
        <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
          <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-700/60 pb-3">
            <div>
              <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span>🟦 Pre-Test: Module 1 – Introduction to C</span>
                <span class="text-emerald-400 text-xs font-bold">✅ Completed</span>
              </h3>
              <p class="text-xs text-slate-400 mt-0.5">
                Score: <strong>65%</strong> (Attempt 1)  •  Taken: 05/02/2025  •  Unlocked M1 ✅
              </p>
            </div>
          </div>

          <!-- AI Learning Path Analysis -->
          <div class="p-4 bg-slate-900/80 rounded-2xl border border-slate-700/60 space-y-2 text-xs">
            <h4 class="font-bold text-blue-300 flex items-center gap-1.5">
              <span>🤖</span>
              <span>AI Learning Path generated after Pre-Test:</span>
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
              <div class="p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 font-medium">
                ✅ Strong: Basic Syntax (85%)
              </div>
              <div class="p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 font-medium">
                ⚠️ Weak: Operators (48%) → AI added "Operators Review"
              </div>
            </div>
          </div>

          <div class="pt-1">
            <button @click="showResults = true" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700">
              👁 Review My Answers
            </button>
          </div>
        </div>

      </div>

      <!-- PRE-TEST BRIEFING MODAL -->
      <div v-if="showBriefingModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-blue-500/40 rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl">
          <div class="border-b border-slate-800 pb-3">
            <h3 class="text-lg font-black text-white">🟦 Pre-Test: Module 2 – Variables & Data Types</h3>
            <p class="text-xs text-amber-400 font-bold mt-1">⚠️ Before you begin...</p>
          </div>

          <div class="space-y-3 text-xs text-slate-300">
            <div class="p-3.5 bg-slate-800 rounded-2xl border border-slate-700 space-y-1 font-mono">
              <p>• Questions: 10  |  Time: 15 minutes  |  Attempts: 2</p>
              <p>• Type: MCQ + True/False</p>
              <p>• Passing is NOT required (just take it to unlock)</p>
            </div>

            <div class="space-y-1.5">
              <h4 class="font-bold text-white uppercase text-[11px]">💡 Tips:</h4>
              <p>• Answer based on what you ALREADY KNOW (no need to study first)</p>
              <p>• Your result helps AI customize your learning path</p>
              <p>• You can see correct answers after submission</p>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
            <button @click="showBriefingModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
              ❌ Not Ready Yet
            </button>
            <button @click="startTest" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs shadow-lg">
              🟦 Start Pre-Test Now →
            </button>
          </div>
        </div>
      </div>

      <!-- PRE-TEST TAKING SCREEN -->
      <div v-if="isTakingTest" class="bg-slate-900 border border-blue-500/50 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
          <div>
            <h2 class="text-base font-black text-white">🟦 PRE-TEST: Variables & Data Types</h2>
            <p class="text-xs text-slate-400">Question {{ currentQuestion }} / {{ totalQuestions }}</p>
          </div>

          <span class="px-4 py-2 rounded-2xl bg-amber-500/20 text-amber-300 font-mono font-bold text-sm border border-amber-500/30">
            ⏱️ 13:45 remaining
          </span>
        </div>

        <!-- Question Viewport -->
        <div class="p-6 bg-slate-950 rounded-2xl border border-slate-800 space-y-4">
          <p class="text-sm md:text-base font-bold text-white">
            Q4: Which of the following is the correct way to declare an integer in C?
          </p>

          <div class="space-y-2.5 text-xs">
            <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer hover:border-blue-500/40">
              <input type="radio" name="opt" value="A" v-model="selectedOption" class="text-blue-600" />
              <span class="text-slate-200">A. int 123var;</span>
            </label>
            <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer hover:border-blue-500/40">
              <input type="radio" name="opt" value="B" v-model="selectedOption" class="text-blue-600" />
              <span class="text-slate-200">B. integer x = 5;</span>
            </label>
            <label class="flex items-center gap-3 p-3.5 rounded-xl bg-blue-900/30 border border-blue-500/50 font-bold text-white cursor-pointer">
              <input type="radio" name="opt" value="C" v-model="selectedOption" class="text-blue-600" />
              <span>C. int x = 5; (Selected)</span>
            </label>
            <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer hover:border-blue-500/40">
              <input type="radio" name="opt" value="D" v-model="selectedOption" class="text-blue-600" />
              <span class="text-slate-200">D. var x = 5;</span>
            </label>
          </div>

          <div class="pt-2">
            <button @click="toggleFlag(currentQuestion)" class="px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 text-xs font-bold border border-slate-700">
              {{ flaggedQuestions.includes(currentQuestion) ? '⚑ Unflag Question' : '⚑ Flag this question' }}
            </button>
          </div>
        </div>

        <!-- Question Overview Grid -->
        <div class="p-4 bg-slate-950/60 rounded-2xl border border-slate-800 space-y-2">
          <p class="text-[10px] text-slate-400 font-bold uppercase">📋 Question Overview (4/10 answered):</p>
          <div class="flex flex-wrap items-center gap-2 text-xs font-mono">
            <span v-for="q in 10" :key="q" class="px-2.5 py-1 rounded-lg border font-bold cursor-pointer" :class="[
              q === currentQuestion ? 'bg-blue-600 text-white border-blue-400' :
              flaggedQuestions.includes(q) ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' :
              answeredQuestions.includes(q) ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' :
              'bg-slate-900 text-slate-500 border-slate-800'
            ]">
              {{ answeredQuestions.includes(q) ? '✅' : '' }}{{ flaggedQuestions.includes(q) ? '⚑' : '' }}{{ q }}
            </span>
          </div>
        </div>

        <!-- Submission Row -->
        <div class="flex items-center justify-between pt-2">
          <button @click="currentQuestion > 1 && currentQuestion--" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
            ← Previous
          </button>
          <button @click="submitTest" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-lg">
            📤 Submit Pre-Test Early
          </button>
        </div>
      </div>

      <!-- PRE-TEST RESULTS VIEW -->
      <div v-if="showResults" class="bg-slate-900 border border-blue-500/40 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        <div class="text-center space-y-2 border-b border-slate-800 pb-5">
          <span class="px-4 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 font-extrabold text-xs border border-emerald-500/40">
            ✅ Module 2 is now UNLOCKED!
          </span>
          <h2 class="text-2xl font-black text-white">🟦 Pre-Test Results – Variables & Data Types</h2>
          <p class="text-sm font-bold text-blue-400">Your Score: 65% (Attempt 1 of 2)</p>
        </div>

        <div class="p-5 bg-slate-950 rounded-2xl border border-slate-800 space-y-3">
          <h4 class="text-xs font-bold text-white uppercase tracking-wider">🤖 AI Analysis of Your Starting Level:</h4>
          <div class="space-y-2 text-xs">
            <div class="p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300">
              ✅ Strong: Variable Declaration (90%)
            </div>
            <div class="p-2.5 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-300">
              ✅ Good: Basic Data Types (70%)
            </div>
            <div class="p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300">
              ⚠️ Needs Work: Type Casting (40%) → Added to AI Path
            </div>
            <div class="p-2.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300">
              🔴 Weak: Pointer Types (30%) → Focus lesson added
            </div>
          </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
          <button @click="showResults = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
            ← Back to Pre-Tests
          </button>
          <button class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md">
            ▶ Start Module 2 Now →
          </button>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
