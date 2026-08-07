<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  courses: Array<any>
}>()

const gradebook = ref([
  { student: 'Long Vicheka', pre: '72%', prac: '91%', m1: '95%', m2: '90%', m3: '88%', final: '92%', grade: 'A 🥇', status: '✅ Pass' },
  { student: 'Chan Dara', pre: '65%', prac: '85%', m1: '88%', m2: '82%', m3: '75%', final: '85%', grade: 'B+ 🥈', status: '✅ Pass' },
  { student: 'Bun Rithy', pre: '58%', prac: '78%', m1: '82%', m2: '76%', m3: '68%', final: '78%', grade: 'B 🥉', status: '✅ Pass' },
  { student: 'Mao Sreynich', pre: '55%', prac: '71%', m1: '74%', m2: '68%', m3: '62%', final: '71%', grade: 'C+', status: '✅ Pass' },
  { student: 'Sok Channa', pre: '40%', prac: '55%', m1: '58%', m2: '52%', m3: '—', final: '—', grade: 'F ⚠️', status: '🔴 Fail' },
])

const learningGrowth = ref([
  { student: 'Chan Dara', pre: '65%', post: '88%', growth: '+23% 🚀' },
  { student: 'Long Vicheka', pre: '72%', post: '95%', growth: '+23% 🚀' },
  { student: 'Bun Rithy', pre: '58%', post: '82%', growth: '+24% 🚀' },
  { student: 'Mao Sreynich', pre: '55%', post: '74%', growth: '+19% ✅' },
  { student: 'Sok Channa', pre: '40%', post: '58%', growth: '+18% ✅' },
])

const weakTopics = ref([
  { topic: '🔴 Pointers', score: '42%', struggling: '28 / 45 (62%)', action: '📹 Add Video' },
  { topic: '🔴 Memory Alloc', score: '48%', struggling: '24 / 45 (53%)', action: '🟩 Add Practice' },
  { topic: '🟡 Recursion', score: '58%', struggling: '18 / 45 (40%)', action: '📄 Add Notes' },
  { topic: '🟢 Variables', score: '85%', struggling: '3 / 45 (7%)', action: '✅ Good' },
])
</script>

<template>
  <div class="space-y-6">
    <!-- Top Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 font-bold flex items-center justify-center text-lg">🏆</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Class Avg</p>
          <p class="text-xl font-extrabold text-slate-800 dark:text-white">78%</p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 font-bold flex items-center justify-center text-lg">🥇</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Highest Score</p>
          <p class="text-xl font-extrabold text-emerald-600">92%</p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 font-bold flex items-center justify-center text-lg">⚠️</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Lowest Score</p>
          <p class="text-xl font-extrabold text-rose-600">52%</p>
        </div>
      </div>
      <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 font-bold flex items-center justify-center text-lg">✅</div>
        <div>
          <p class="text-xs text-slate-400 font-bold uppercase">Pass Rate</p>
          <p class="text-xl font-extrabold text-purple-600">82%</p>
        </div>
      </div>
    </div>

    <!-- GRADEBOOK TABLE -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden text-xs">
      <div class="p-4 border-b font-extrabold text-sm flex items-center justify-between">
        <span>🏆 Quiz Scores (Gradebook) – C Programming Basics</span>
        <button class="px-3 py-1.5 bg-emerald-600 text-white rounded-xl font-bold shadow">📤 Export Gradebook Excel</button>
      </div>
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 text-slate-500 uppercase font-bold border-b">
            <th class="p-3.5">Student</th>
            <th class="p-3.5">Pre-Test</th>
            <th class="p-3.5">Prac Avg</th>
            <th class="p-3.5">M1 Post</th>
            <th class="p-3.5">M2 Post</th>
            <th class="p-3.5">M3 Post</th>
            <th class="p-3.5">Final Exam</th>
            <th class="p-3.5">Grade</th>
            <th class="p-3.5">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="g in gradebook" :key="g.student" class="hover:bg-slate-50/50 transition font-medium">
            <td class="p-3.5 font-bold text-slate-800 dark:text-white">{{ g.student }}</td>
            <td class="p-3.5 text-blue-600 font-bold">{{ g.pre }}</td>
            <td class="p-3.5 text-emerald-600 font-bold">{{ g.prac }}</td>
            <td class="p-3.5 font-bold">{{ g.m1 }}</td>
            <td class="p-3.5 font-bold">{{ g.m2 }}</td>
            <td class="p-3.5 font-bold">{{ g.m3 }}</td>
            <td class="p-3.5 font-extrabold text-indigo-600">{{ g.final }}</td>
            <td class="p-3.5 font-extrabold text-amber-600 text-sm">{{ g.grade }}</td>
            <td class="p-3.5 font-bold" :class="g.status.includes('Pass') ? 'text-emerald-600' : 'text-rose-600'">{{ g.status }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 📈 LEARNING GROWTH PRE VS POST & 🎯 WEAK TOPICS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-xs">
      <!-- Learning Growth -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm space-y-3">
        <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">📈 Learning Growth: Pre-Test → Post-Test</h3>
        <div class="space-y-2">
          <div v-for="l in learningGrowth" :key="l.student" class="p-2.5 bg-slate-50 dark:bg-gray-700 rounded-xl flex items-center justify-between font-bold">
            <span class="w-32">{{ l.student }}</span>
            <span class="text-blue-600">{{ l.pre }}</span>
            <span>→</span>
            <span class="text-rose-600">{{ l.post }}</span>
            <span class="text-emerald-600 font-extrabold">{{ l.growth }}</span>
          </div>
        </div>
        <p class="p-3 bg-emerald-50 text-emerald-800 rounded-xl font-bold">📊 Class Average Growth: +21.4% (Proof of Teaching Effectiveness 🎉)</p>
      </div>

      <!-- Class Weak Topics -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm space-y-3">
        <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">🎯 Class Weak Topics Analysis</h3>
        <div class="space-y-2">
          <div v-for="w in weakTopics" :key="w.topic" class="p-2.5 bg-slate-50 dark:bg-gray-700 rounded-xl flex items-center justify-between">
            <span class="font-bold w-32">{{ w.topic }}</span>
            <span class="text-rose-600 font-extrabold">{{ w.score }}</span>
            <span class="text-slate-500 font-semibold">{{ w.struggling }}</span>
            <button class="px-2.5 py-1 bg-purple-600 text-white rounded-lg font-bold text-[10px]">{{ w.action }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
