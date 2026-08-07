<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
  show: boolean
  item: any
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'submitGrade', score: number, feedback: string): void
}>()

const gradeInputScore = ref(90)
const gradeFeedback = ref('Great submission! Code structure is clean.')
const sendEmailNotification = ref(true)

watch(() => props.item, (newItem) => {
  if (newItem) {
    gradeInputScore.value = newItem.score !== undefined ? newItem.score : 85
    gradeFeedback.value = newItem.passed ? 'Excellent work! Demonstrated full comprehension.' : 'Good attempt. Please review chapter notes.'
  }
}, { immediate: true })

function submit() {
  emit('submitGrade', Number(gradeInputScore.value), gradeFeedback.value)
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl">
      <div class="flex items-center justify-between border-b border-slate-700 pb-3">
        <h3 class="font-bold text-sm text-white flex items-center gap-2">
          <span>✍</span> MANUAL GRADING & EVALUATION
        </h3>
        <button @click="emit('close')" class="text-slate-400 hover:text-white">✕</button>
      </div>

      <div class="space-y-3 text-xs" v-if="item">
        <div class="bg-slate-900 p-3 rounded-xl flex justify-between border border-slate-700">
          <div>
            <span class="font-bold text-white text-sm block">{{ item.student || item.title }}</span>
            <span class="text-slate-400 block">{{ item.course }}</span>
          </div>
          <span class="text-purple-300 font-mono font-bold">{{ item.id }}</span>
        </div>

        <div>
          <label class="block font-semibold text-slate-300 mb-1">Grade Score (Max 100):</label>
          <div class="flex items-center gap-3">
            <input v-model="gradeInputScore" type="number" min="0" max="100" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-sm font-bold text-emerald-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
            <span class="font-bold text-sm" :class="gradeInputScore >= 60 ? 'text-emerald-400' : 'text-red-400'">
              {{ gradeInputScore >= 60 ? 'PASSED ✓' : 'FAILED ✗' }}
            </span>
          </div>
        </div>

        <div>
          <label class="block font-semibold text-slate-300 mb-1">Teacher Feedback Comment:</label>
          <textarea v-model="gradeFeedback" rows="3" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-xs text-white" placeholder="Enter constructive feedback..."></textarea>
        </div>

        <div class="bg-slate-900 p-2.5 rounded-xl border border-slate-700">
          <label class="flex items-center gap-2 cursor-pointer">
            <input v-model="sendEmailNotification" type="checkbox" class="text-emerald-500 rounded" />
            <span class="text-slate-300">Send instant notification email & LMS alert to student</span>
          </label>
        </div>
      </div>

      <div class="flex justify-end gap-2 border-t border-slate-700 pt-4">
        <button @click="emit('close')" class="px-4 py-2 bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">Cancel</button>
        <button @click="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/30">Save Grade & Notify Student</button>
      </div>
    </div>
  </div>
</template>
