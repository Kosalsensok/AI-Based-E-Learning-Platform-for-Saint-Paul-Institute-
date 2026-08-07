<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const assignmentList = ref([
  {
    id: 1,
    title: 'Create Your First C Program',
    dueText: 'Due in 1 day',
    course: 'C Programming Basics',
    type: 'File Upload + Screenshot',
    latePolicy: 'Allowed (10% per day penalty)',
    isOverdue: false
  },
  {
    id: 2,
    title: 'Post-Test Module 1 Assignment',
    dueText: '2 days late',
    course: 'C Programming Basics',
    penalty: 'Penalty applied: 10%/day',
    isOverdue: true
  }
])
</script>

<template>
  <StudentLayout title="Calendar — Assignments Due">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-amber-950 via-slate-900 to-indigo-950 border border-amber-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold uppercase tracking-wider">
            🟧 ASSIGNMENT DEADLINES
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🟧 ASSIGNMENTS DUE</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            កាលកំណត់បញ្ជូនកិច្ចការ (Assignments / Project Files) និងប្រកាស penalty Overdue
          </p>
        </div>
      </div>

      <!-- ASSIGNMENTS DUE CARDS (Matching Prompt Spec Layout) -->
      <div class="space-y-4">
        <div
          v-for="a in assignmentList"
          :key="a.id"
          :class="[a.isOverdue ? 'border-rose-500/50 bg-rose-950/20' : 'border-amber-500/40 bg-slate-800/90', 'rounded-3xl p-6 border shadow-xl space-y-4']"
        >
          <!-- Header -->
          <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <h3 class="text-base font-black text-white">
              <span v-if="a.isOverdue" class="text-rose-400">⚠️ Overdue: </span>
              <span>{{ a.title }}</span>
            </h3>
            <span :class="[a.isOverdue ? 'bg-rose-500/20 text-rose-300 border-rose-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30', 'px-3 py-1 rounded-full font-bold text-xs border font-mono']">
              ⏰ {{ a.dueText }}
            </span>
          </div>

          <!-- Body -->
          <div class="space-y-1 text-xs text-slate-300">
            <p>Course: <strong class="text-white">{{ a.course }}</strong></p>
            <p v-if="a.type">Type: <span class="text-indigo-300 font-bold">{{ a.type }}</span></p>
            <p v-if="a.latePolicy">Late Policy: <span class="text-amber-400 font-mono">{{ a.latePolicy }}</span></p>
            <p v-if="a.penalty" class="text-rose-400 font-bold font-mono">{{ a.penalty }}</p>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-700/60 text-xs">
            <Link href="/student/quizzes/assignments" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-black shadow-md">
              {{ a.isOverdue ? '🔓 Submit Late' : '📎 Upload Now' }}
            </Link>
            <Link href="/student/quizzes/assignments" class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 font-bold">
              👁 View Requirements
            </Link>
            <button class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 font-bold">
              📌 Set Reminder
            </button>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
