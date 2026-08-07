<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  faqs: Array<any>
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'addFaq', payload: any): void
}>()

const showAddForm = ref(false)
const newQuestion = ref('')
const newAnswer = ref('')
const selectedCourse = ref('C Programming Basics')
const selectedModule = ref('Module 1')

const handleSaveFaq = () => {
  if (!newQuestion.value || !newAnswer.value) return
  emit('addFaq', {
    question: newQuestion.value,
    answer: newAnswer.value,
    course: selectedCourse.value,
    module: selectedModule.value,
  })
  newQuestion.value = ''
  newAnswer.value = ''
  showAddForm.value = false
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-2xl w-full p-6 space-y-5 shadow-2xl border border-slate-200 dark:border-gray-700 animate-in fade-in zoom-in duration-200">
      <!-- Modal Header -->
      <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-gray-700">
        <div class="flex items-center gap-2">
          <span class="text-xl">📚</span>
          <div>
            <h3 class="text-base font-black text-slate-900 dark:text-white">
              COURSE FAQ BUILDER — Built from Resolved Questions
            </h3>
            <p class="text-xs text-slate-500 font-semibold">
              Manage reusable explanations to prevent duplicate student questions.
            </p>
          </div>
        </div>

        <button @click="emit('close')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white font-black text-xl">
          ✕
        </button>
      </div>

      <!-- Action Toolbar -->
      <div class="flex items-center justify-between">
        <button
          @click="showAddForm = !showAddForm"
          class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-xl text-xs shadow-sm transition flex items-center gap-1.5"
        >
          <span>➕</span> Add Manual FAQ
        </button>

        <span class="text-xs text-slate-500 font-bold">Total FAQs: {{ faqs.length }}</span>
      </div>

      <!-- Manual Add Form -->
      <div v-if="showAddForm" class="p-4 bg-indigo-50/60 dark:bg-indigo-950/40 rounded-2xl border border-indigo-200 dark:border-indigo-900 space-y-3 text-xs">
        <h4 class="font-extrabold text-indigo-950 dark:text-indigo-200">Add New FAQ Entry</h4>
        <input
          v-model="newQuestion"
          type="text"
          placeholder="Question (e.g. What is a compiler?)"
          class="w-full p-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl font-medium"
        />
        <textarea
          v-model="newAnswer"
          rows="3"
          placeholder="Answer explanation..."
          class="w-full p-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl font-medium"
        ></textarea>
        <div class="flex justify-end gap-2 pt-1">
          <button @click="showAddForm = false" class="px-3 py-1.5 bg-slate-200 text-slate-700 rounded-xl font-bold">Cancel</button>
          <button @click="handleSaveFaq" class="px-4 py-1.5 bg-indigo-600 text-white rounded-xl font-bold">Save FAQ</button>
        </div>
      </div>

      <!-- FAQ Item List -->
      <div class="space-y-3 max-h-[350px] overflow-y-auto pr-1">
        <div
          v-for="item in faqs"
          :key="item.id"
          class="p-4 bg-slate-50 dark:bg-gray-700/50 rounded-2xl border border-slate-200/80 dark:border-gray-600 space-y-2 text-xs"
        >
          <div class="font-black text-slate-900 dark:text-white text-sm">
            Q: {{ item.question }}
          </div>
          <p class="text-slate-700 dark:text-slate-300 font-medium leading-relaxed bg-white dark:bg-gray-800 p-3 rounded-xl border border-slate-100 dark:border-gray-700">
            A: {{ item.answer }}
          </p>

          <div class="flex flex-wrap items-center justify-between gap-2 pt-1 text-[11px] text-slate-500 font-semibold">
            <div class="flex items-center gap-2">
              <span>Course: <strong>{{ item.course }}</strong></span>
              <span>·</span>
              <span>Module: <strong>{{ item.module }}</strong></span>
              <span>·</span>
              <span>Added by: <strong>{{ item.added_by }}</strong></span>
            </div>

            <div class="flex items-center gap-2">
              <button class="px-2.5 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg font-bold">✏ Edit</button>
              <button class="px-2.5 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-bold">🗑 Delete</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex justify-end pt-2">
        <button
          @click="emit('close')"
          class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-xs shadow transition"
        >
          Done
        </button>
      </div>
    </div>
  </div>
</template>
