<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const gradeComponents = ref([
  { name: 'Pre-Test Quizzes', weight: '10%', max: 100, score: '65.0%', weighted: '6.5 / 10', status: '✅ Done' },
  { name: 'Practice Quizzes', weight: '10%', max: 100, score: '78.0%', weighted: '7.8 / 10', status: '✅ Active' },
  { name: 'Assignments / Projects', weight: '40%', max: 100, score: '85.0%', weighted: '34.0 / 40', status: '✅ Graded' },
  { name: 'Post-Test Final Exams', weight: '40%', max: 100, score: '82.0%', weighted: '32.8 / 40', status: '✅ Passed' }
])
</script>

<template>
  <StudentLayout title="My Scores & Gradebook">
    <div class="space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
          <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
            <span>🏆</span>
            <span>MY SCORES & PERSONAL GRADEBOOK</span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            C Programming Basics  •  Academic Year: 2024–2025  •  Semester 2  •  Teacher: Mr. Sophea
          </p>
        </div>

        <button class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md self-start">
          📥 Download PDF Transcript
        </button>
      </div>

      <!-- PERFORMANCE METRICS SUMMARY GRID -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Overall Weighted Grade -->
        <div class="p-5 bg-gradient-to-br from-indigo-950 via-slate-900 to-slate-900 border border-indigo-500/40 rounded-3xl space-y-1 shadow-xl">
          <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Overall Grade</p>
          <div class="flex items-baseline justify-between">
            <span class="text-2xl font-black text-white">84.5%</span>
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 font-black text-xs border border-emerald-500/30">
              Grade A-
            </span>
          </div>
          <p class="text-[11px] text-slate-400 pt-1">Weighted Total Score</p>
        </div>

        <!-- Class Rank -->
        <div class="p-5 bg-gradient-to-br from-amber-950 via-slate-900 to-slate-900 border border-amber-500/40 rounded-3xl space-y-1 shadow-xl">
          <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Class Ranking</p>
          <div class="flex items-baseline justify-between">
            <span class="text-2xl font-black text-white">Rank #4</span>
            <span class="text-xs font-bold text-amber-300">Top 10% 🌟</span>
          </div>
          <p class="text-[11px] text-slate-400 pt-1">Out of 45 enrolled students</p>
        </div>

        <!-- Pre vs Post Growth -->
        <div class="p-5 bg-gradient-to-br from-emerald-950 via-slate-900 to-slate-900 border border-emerald-500/40 rounded-3xl space-y-1 shadow-xl">
          <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Knowledge Growth</p>
          <div class="flex items-baseline justify-between">
            <span class="text-2xl font-black text-white">+17%</span>
            <span class="text-xs font-bold text-emerald-400">Pre 65% → Post 82%</span>
          </div>
          <p class="text-[11px] text-slate-400 pt-1">Net Learning Improvement</p>
        </div>

        <!-- Quizzes Completed -->
        <div class="p-5 bg-gradient-to-br from-cyan-950 via-slate-900 to-slate-900 border border-cyan-500/40 rounded-3xl space-y-1 shadow-xl">
          <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Assessments Done</p>
          <div class="flex items-baseline justify-between">
            <span class="text-2xl font-black text-white">24</span>
            <span class="text-xs font-bold text-cyan-300">Pass Rate: 88%</span>
          </div>
          <p class="text-[11px] text-slate-400 pt-1">Total Completed Tasks</p>
        </div>

      </div>

      <!-- GRADEBOOK WEIGHT DISTRIBUTION TABLE (Matching Prompt Specs) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider">GRADEBOOK WEIGHT DISTRIBUTION</h3>
        
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-900 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-700">
              <tr>
                <th class="p-3">Assessment Component</th>
                <th class="p-3">Weight</th>
                <th class="p-3">Max Pts</th>
                <th class="p-3">My Score</th>
                <th class="p-3">Weighted Score</th>
                <th class="p-3">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50 font-medium">
              <tr v-for="comp in gradeComponents" :key="comp.name" class="hover:bg-slate-700/30">
                <td class="p-3 font-bold text-white">{{ comp.name }}</td>
                <td class="p-3 font-mono text-indigo-300 font-bold">{{ comp.weight }}</td>
                <td class="p-3 font-mono text-slate-400">{{ comp.max }}</td>
                <td class="p-3 font-bold text-emerald-400">{{ comp.score }}</td>
                <td class="p-3 font-mono font-bold text-white">{{ comp.weighted }}</td>
                <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-emerald-300">{{ comp.status }}</span></td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-950 font-bold border-t-2 border-slate-700 text-white text-xs">
              <tr>
                <td class="p-3 text-indigo-300">TOTAL WEIGHTED SCORE</td>
                <td class="p-3 font-mono">100%</td>
                <td class="p-3 font-mono">—</td>
                <td class="p-3 text-emerald-400">84.5%</td>
                <td class="p-3 font-mono text-emerald-300">84.5 / 100 (Grade: A-)</td>
                <td class="p-3 text-emerald-400">🟢 Excellent</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- AI WEAK TOPICS SUMMARY CARD -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
          <span>🤖</span>
          <span>AI TOPIC MASTERY SUMMARY</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
          <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 font-medium flex items-center justify-between">
            <span>🔴 Pointers &amp; Memory Allocation</span>
            <span class="font-bold font-mono">42% (Needs Focus)</span>
          </div>
          <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 font-medium flex items-center justify-between">
            <span>⚠️ Operators &amp; Type Casting</span>
            <span class="font-bold font-mono">48% (Needs Work)</span>
          </div>
          <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 font-medium flex items-center justify-between">
            <span>🟢 Variables &amp; Data Types</span>
            <span class="font-bold font-mono">85% (Strong)</span>
          </div>
          <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 font-medium flex items-center justify-between">
            <span>🟢 Control Loops &amp; Conditions</span>
            <span class="font-bold font-mono">82% (Good)</span>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
