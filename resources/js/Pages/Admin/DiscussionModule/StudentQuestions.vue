<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps<{
  questions: Array<any>
  topStats: any
}>()

const showAnswerModal = ref(false)
const selectedQuestion = ref<any>(null)

const answerForm = ref({
  question_code: 'Q-2025-032',
  question_text: 'What is the exact difference between an array name and a pointer variable in C?',
  student_name: 'Sok Channa',
  ai_draft: 'An array name is a constant pointer to the first element, whereas a pointer variable stores a memory address and can be reassigned to point elsewhere.',
  final_answer: 'Array = Collection of elements stored in contiguous memory.\nPointer = Variable holding a memory address.\n\nExample code:\nint arr[5] = {1,2,3,4,5};\nint *p = arr;',
  mark_best: true,
  add_faq: true,
  notify_student: true
})

const openAnswerModal = (q?: any) => {
  if (q) {
    selectedQuestion.value = q
    answerForm.value.question_code = q.question_code
    answerForm.value.question_text = q.question_text
    answerForm.value.student_name = q.student_name
    answerForm.value.ai_draft = q.ai_draft_answer || answerForm.value.ai_draft
  }
  showAnswerModal.value = true
}

const approveAiDraft = () => {
  answerForm.value.final_answer = answerForm.value.ai_draft
  window.alert('🤖 AI Draft Answer approved!')
}

const assignToTeacher = () => {
  window.alert('Assigned to Mr. Sophea!')
}

const submitAnswer = () => {
  window.alert('🚀 Answer posted & student notified via 🔔 In-App + 📧 Email + 📱 Push!')
  showAnswerModal.value = false
}
</script>

<template>
  <AdminLayout title="Discussions & Support — Student Questions Queue">
    <div class="space-y-6">
      
      <!-- Top Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl">
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            ❓ STUDENT QUESTIONS QUEUE (Q&A)
            <span class="text-xs bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2.5 py-0.5 rounded-full font-mono">
              In-Video & Lesson Q&A
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            សំណួរដែលនិស្សិតសួរត្រង់ពី Video / Chapter (StackOverflow ក្នុង LMS)។
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button @click="openAnswerModal()" class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-slate-950 font-bold text-xs rounded-xl shadow-lg shadow-amber-500/20">
            ✍️ Answer Question
          </button>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-1 border-b border-slate-800 overflow-x-auto pb-1 custom-scrollbar">
        <Link href="/admin/discussions/board" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          💬 Discussions
        </Link>
        <Link href="/admin/discussions/questions" class="px-4 py-2.5 text-xs rounded-xl border bg-amber-500/15 text-amber-300 border-amber-500/40 font-bold shadow-sm shrink-0">
          ❓ Student Questions
        </Link>
        <Link href="/admin/discussions/tickets" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🎫 Support Tickets
        </Link>
        <Link href="/admin/discussions/reports" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🚩 Reported Content
        </Link>
      </div>

      <!-- Stats Bar -->
      <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl flex items-center justify-between gap-4 text-xs">
        <div class="flex items-center gap-6 flex-wrap">
          <span>Unanswered: <strong class="text-red-400 text-base">47</strong></span>
          <span>Answered Today: <strong class="text-emerald-400 text-base">32</strong></span>
          <span>Avg Response Time: <strong class="text-amber-400 text-base">2h 15m</strong></span>
        </div>
        <div class="text-slate-400">⏱️ SLA Alert: Unanswered &gt; 6h = Red Warning</div>
      </div>

      <!-- Questions List Cards -->
      <div class="space-y-4">
        <div v-for="q in questions" :key="q.question_code" class="bg-slate-900 border border-slate-800 rounded-2xl p-5 space-y-3 hover:border-slate-700 transition">
          
          <div class="flex justify-between items-start">
            <div class="space-y-1">
              <span class="text-[11px] font-mono text-slate-500">{{ q.question_code }}</span>
              <h3 class="font-bold text-base text-white flex items-center gap-2">
                ❓ {{ q.question_text }}
              </h3>
            </div>

            <span
              :class="q.sla_overdue ? 'bg-red-500/20 text-red-400 border-red-500/30' : 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30'"
              class="px-2.5 py-1 text-[11px] font-bold border rounded-full shrink-0"
            >
              {{ q.sla_overdue ? '🔴 Overdue (>6h)' : '🟢 On Track' }}
            </span>
          </div>

          <div class="text-xs text-slate-400 flex flex-wrap items-center gap-3">
            <span>📚 <strong>{{ q.course_name }}</strong> / {{ q.chapter_name }}</span>
            <span>👤 {{ q.student_name }} ({{ q.student_year }})</span>
            <span>🕒 {{ q.time_ago || '5h ago' }}</span>
            <span>👀 {{ q.views_count }} views</span>
          </div>

          <!-- AI Suggestion Box -->
          <div v-if="q.status === 'pending'" class="bg-slate-950 border border-indigo-500/30 p-3 rounded-xl space-y-2 text-xs">
            <div class="flex justify-between items-center">
              <span class="font-bold text-indigo-300 flex items-center gap-1">🤖 AI Draft Answer ({{ q.ai_confidence || 92 }}% confidence):</span>
            </div>
            <p class="text-slate-300 font-sans italic">"{{ q.ai_draft_answer }}"</p>
          </div>

          <div class="pt-2 border-t border-slate-800 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
              <button @click="openAnswerModal(q)" class="px-4 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-slate-950 font-bold rounded-lg shadow">
                ✍️ Answer Now
              </button>
              <button @click="openAnswerModal(q)" class="px-3 py-1.5 bg-indigo-600/20 text-indigo-300 border border-indigo-500/30 rounded-lg font-semibold">
                🤖 Use AI Draft
              </button>
            </div>

            <button @click="assignToTeacher" class="text-slate-400 hover:text-white">
              🔀 Assign to Teacher
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- ANSWER QUESTION WORKFLOW MODAL -->
    <!-- ========================================================================= -->
    <div v-if="showAnswerModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-3xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden my-auto">
        
        <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center bg-slate-950">
          <h3 class="text-base font-bold text-white">✍️ Answer Question — {{ answerForm.question_code }}</h3>
          <button @click="showAnswerModal = false" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <div class="p-6 space-y-4 overflow-y-auto custom-scrollbar text-xs">
          <div class="bg-slate-950 border border-slate-800 p-3.5 rounded-xl space-y-1">
            <span class="text-slate-400 block text-[11px]">Question from <strong>{{ answerForm.student_name }}</strong>:</span>
            <p class="font-bold text-white text-sm">❓ {{ answerForm.question_text }}</p>
          </div>

          <!-- AI Draft -->
          <div class="bg-indigo-950/40 border border-indigo-500/40 p-4 rounded-xl space-y-2">
            <div class="flex justify-between items-center">
              <span class="font-bold text-indigo-300">🤖 AI Draft Answer (92% confidence):</span>
              <button @click="approveAiDraft" class="px-2.5 py-1 bg-indigo-500 text-white font-bold rounded">
                ✅ Approve AI Draft
              </button>
            </div>
            <p class="text-slate-300 font-mono text-[11px]">{{ answerForm.ai_draft }}</p>
          </div>

          <!-- Final Answer -->
          <div>
            <label class="text-slate-300 font-semibold block mb-1">Your Final Answer (Rich Text + Code):</label>
            <textarea v-model="answerForm.final_answer" rows="6" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-white font-mono"></textarea>
          </div>

          <!-- Checkboxes -->
          <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2">
            <label class="flex items-center gap-2 text-slate-200 cursor-pointer">
              <input type="checkbox" v-model="answerForm.mark_best" /> ☑️ Mark as Best Answer
            </label>
            <label class="flex items-center gap-2 text-slate-200 cursor-pointer">
              <input type="checkbox" v-model="answerForm.add_faq" /> ☑️ Add to FAQ Library
            </label>
            <label class="flex items-center gap-2 text-slate-200 cursor-pointer">
              <input type="checkbox" v-model="answerForm.notify_student" /> ☑️ Notify Student via 🔔 In-App + 📧 Email + 📱 Push
            </label>
          </div>
        </div>

        <div class="px-6 py-3 border-t border-slate-800 bg-slate-950 flex justify-end">
          <button @click="submitAnswer" class="px-6 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-slate-950 font-bold rounded-xl text-xs shadow-lg">
            🚀 Post Answer
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
