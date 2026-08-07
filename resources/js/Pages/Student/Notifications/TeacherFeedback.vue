<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const feedbackList = ref([
  {
    id: 1,
    title: '💬 Feedback: Assignment graded ✍️',
    badge: '🟢 Received',
    assignment: '“Create Your First C Program”',
    score: '16/20 (80%)',
    khFeedback: 'ល្អណាស់! សូមបន្ថែម comment នៅក្នុង code បន្ថែម។',
    enFeedback: 'Great work! Please add more comments in your code.',
    allowResubmit: true,
    hidden: false
  }
])

const hideItem = (item: any) => {
  item.hidden = true
}
</script>

<template>
  <StudentLayout title="Notifications — Teacher Feedback">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-indigo-950 border border-emerald-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider">
            💬 DIRECT INSTRUCTOR EVALUATION
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>💬 TEACHER FEEDBACK</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ការវាយតម្លៃពិន្ទុ និងមតិយោបល់ផ្ទាល់ពីគ្រូបង្រៀនលើកិច្ចការ (Assignments / Projects)
          </p>
        </div>
      </div>

      <!-- FEEDBACK LIST (Matching Prompt Spec Layout) -->
      <div class="space-y-4">
        <template v-for="item in feedbackList" :key="item.id">
          <div
            v-if="!item.hidden"
            class="bg-slate-800/90 border border-emerald-500/40 rounded-3xl p-6 shadow-xl space-y-4"
          >
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
              <h3 class="text-base font-black text-white">{{ item.title }}</h3>
              <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 font-bold text-xs border border-emerald-500/30">
                {{ item.badge }}
              </span>
            </div>

            <!-- Content -->
            <div class="space-y-2 text-xs text-slate-300">
              <p class="text-slate-200">Assignment: <strong class="text-white">{{ item.assignment }}</strong></p>
              <p>Score: <strong class="text-emerald-400 font-mono text-sm">{{ item.score }}</strong></p>
              
              <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                <span class="font-bold text-indigo-300 uppercase text-[10px]">Instructor Comments:</span>
                <p class="text-white font-bold">KH: {{ item.khFeedback }}</p>
                <p class="text-slate-400 italic">EN: {{ item.enFeedback }}</p>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-700/60 text-xs">
              <Link href="/student/quizzes/assignments" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-md">
                👁 View Feedback
              </Link>
              <Link v-if="item.allowResubmit" href="/student/quizzes/assignments" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold shadow-md">
                ▶ Improve &amp; Re-submit
              </Link>
              <button @click="hideItem(item)" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-400 font-bold">
                🙈 Hide
              </button>
            </div>
          </div>
        </template>
      </div>

    </div>
  </StudentLayout>
</template>
