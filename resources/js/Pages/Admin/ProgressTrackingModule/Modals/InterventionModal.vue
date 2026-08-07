<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  showMessageModal: boolean
  showFeedbackModal: boolean
  showResetModal: boolean
  studentId: string | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'submitMessage', studentId: string, msg: string): void
  (e: 'submitFeedback', studentId: string, fb: string): void
  (e: 'confirmReset', studentId: string): void
}>()

const messageText = ref('Hi Chan Dara, we noticed you have been inactive for 3 days. Please check your Module 2 assignment!')
const feedbackText = ref('Great effort on C Variables! Make sure to review Pointers & Heap memory allocation in Module 4.')
</script>

<template>
  <div>
    <!-- Direct Message Modal -->
    <div v-if="showMessageModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-[#0e1322] border border-purple-500/30 rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
          <h3 class="font-bold text-sm text-white flex items-center gap-2">
            <span>📧</span> SEND DIRECT MESSAGE TO STUDENT {{ studentId }}
          </h3>
          <button @click="emit('close')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Message Content:</label>
            <textarea v-model="messageText" rows="4" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white focus:border-purple-500 focus:outline-none"></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-2 border-t border-slate-700/60 pt-4">
          <button @click="emit('close')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="emit('submitMessage', studentId || '', messageText)" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold shadow-md shadow-purple-600/30">Send Direct Message</button>
        </div>
      </div>
    </div>

    <!-- Teacher Feedback Modal -->
    <div v-if="showFeedbackModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-[#0e1322] border border-purple-500/30 rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
          <h3 class="font-bold text-sm text-white flex items-center gap-2">
            <span>📝</span> ADD TEACHER FEEDBACK (STUDENT {{ studentId }})
          </h3>
          <button @click="emit('close')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Feedback & Recommendations:</label>
            <textarea v-model="feedbackText" rows="4" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white focus:border-purple-500 focus:outline-none"></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-2 border-t border-slate-700/60 pt-4">
          <button @click="emit('close')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="emit('submitFeedback', studentId || '', feedbackText)" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold shadow-md shadow-purple-600/30">Save Feedback</button>
        </div>
      </div>
    </div>

    <!-- Reset Progress Modal -->
    <div v-if="showResetModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-[#0e1322] border border-red-500/40 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
          <h3 class="font-bold text-sm text-red-400 flex items-center gap-2">
            <span>⚠️</span> CONFIRM RESET PROGRESS
          </h3>
          <button @click="emit('close')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <p class="text-slate-200">
            Are you sure you want to reset course learning progress for student <strong class="text-purple-400">{{ studentId }}</strong>?
          </p>
          <p class="text-red-400 text-[11px] font-semibold bg-red-950/40 p-2.5 rounded-xl border border-red-500/30">
            ⚠️ This will clear completed chapters and require the student to retake module quizzes.
          </p>
        </div>

        <div class="flex justify-end gap-2 border-t border-slate-700/60 pt-4">
          <button @click="emit('close')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="emit('confirmReset', studentId || '')" class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-xl text-xs font-bold shadow-md shadow-red-600/30">Reset Progress Now</button>
        </div>
      </div>
    </div>
  </div>
</template>
