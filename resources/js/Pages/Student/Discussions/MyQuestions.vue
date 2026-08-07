<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const selectedThread = ref<any | null>(null)
const helpfulMarked = ref(false)

const questionsList = ref([
  {
    id: 1,
    status: 'in_progress',
    badge: '🟡 In Progress',
    course: 'C Programming Basics',
    chapter: 'Ch 2.3 Loops',
    title: 'How to debug infinite loop?',
    asked: '2h ago',
    detail: 'I am running a while loop and it never terminates even when condition is false.',
    answer: null
  },
  {
    id: 2,
    status: 'answered',
    badge: '🟢 Answered',
    course: 'C Programming Basics',
    chapter: 'Ch 2.1 Variables',
    title: 'What is variable scope?',
    asked: '3 days ago',
    answered: '12m ago',
    detail: 'What is the difference between global and local variable scope in C?',
    answer: 'Local variables are declared inside functions and only accessible there. Global variables are declared outside all functions and accessible anywhere.'
  }
])

const openThread = (item: any) => {
  selectedThread.value = item
  helpfulMarked.value = false
}
</script>

<template>
  <StudentLayout title="My Questions History">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-amber-950 via-slate-900 to-indigo-950 border border-amber-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold uppercase tracking-wider">
            📌 PERSONAL Q&amp;A HISTORY
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>📌 MY QUESTIONS</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            តាមដានសំណួរដែលបានសួរ ពិនិត្យមើលចម្លើយពីគ្រូបង្រៀន និងបន្ថែមព័ត៌មានបន្ថែម
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Total Questions</p>
            <p class="text-base font-black text-amber-400">2 Asked</p>
          </div>
        </div>
      </div>

      <!-- SEARCH & FILTERS -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-5 shadow-xl flex flex-wrap items-center justify-between gap-3 text-xs">
        <input type="text" placeholder="Search keyword..." class="bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500" />
        <select class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-300">
          <option>Status: All</option>
          <option>Unanswered</option>
          <option>Answered</option>
        </select>
      </div>

      <!-- QUESTIONS LIST (Matching Prompt Mock Layout) -->
      <div class="space-y-4">
        <div
          v-for="q in questionsList"
          :key="q.id"
          class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4"
        >
          <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <span :class="[q.status === 'answered' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30', 'px-3 py-1 rounded-full font-bold text-xs border']">
              {{ q.badge }}
            </span>
            <span class="text-xs text-slate-400 font-mono">Asked: {{ q.asked }}</span>
          </div>

          <div class="space-y-1 text-xs">
            <p class="text-slate-400">Course: <strong class="text-white">{{ q.course }}</strong> | {{ q.chapter }}</p>
            <h3 class="text-base font-black text-white">Title: “{{ q.title }}”</h3>
            <p v-if="q.answered" class="text-emerald-400 font-bold pt-1">
              Answered: {{ q.answered }}
            </p>
          </div>

          <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-700/60">
            <button @click="openThread(q)" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md">
              {{ q.status === 'answered' ? '👁 View Answer' : '👁 View Thread' }}
            </button>
            <button v-if="q.status === 'answered'" @click="helpfulMarked = true" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-emerald-400 font-bold text-xs">
              👍 Helpful
            </button>
            <button v-else class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 font-bold text-xs">
              📎 Add More Info
            </button>
          </div>
        </div>
      </div>

      <!-- VIEW THREAD MODAL -->
      <div v-if="selectedThread" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-amber-500/40 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
          <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
            <h3 class="text-base font-black text-white">💬 Question Thread</h3>
            <button @click="selectedThread = null" class="text-slate-400 hover:text-white font-bold text-sm">✕</button>
          </div>

          <!-- Question Box -->
          <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1 text-xs">
            <span class="text-amber-400 font-bold">👤 Chan Dara (Student) • {{ selectedThread.asked }}</span>
            <p class="text-white font-bold">{{ selectedThread.title }}</p>
            <p class="text-slate-300">{{ selectedThread.detail }}</p>
          </div>

          <!-- Teacher Answer Box -->
          <div v-if="selectedThread.answer" class="p-4 bg-emerald-950/50 border border-emerald-500/40 rounded-2xl space-y-1 text-xs">
            <span class="text-emerald-400 font-bold">👨‍🏫 Mr. Sophea (Teacher) • Verified Answer</span>
            <p class="text-slate-200 mt-1">{{ selectedThread.answer }}</p>
          </div>
          <div v-else class="p-4 bg-amber-500/10 border border-amber-500/30 rounded-2xl text-xs text-amber-300">
            ⏳ Teacher Mr. Sophea has not answered yet. Estimated response time: &lt; 4 hours.
          </div>

          <div class="flex justify-end pt-2">
            <button @click="selectedThread = null" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
              Close
            </button>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
