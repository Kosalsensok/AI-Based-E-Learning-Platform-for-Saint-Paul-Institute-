<script setup lang="ts">
import { ref, computed, watch } from 'vue'

const props = defineProps<{
  show: boolean
  allCourses: any[]
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', newAsn: any): void
}>()

const courseList = computed(() => {
  if (props.allCourses && props.allCourses.length > 0) {
    return props.allCourses.map(c => typeof c === 'string' ? c : (c.title || c.name || 'C Programming Basics'))
  }
  return [
    'C Programming Basics',
    'Tourism Management 101',
    'English Literature',
    'Agronomy Basics',
  ]
})

const newAssignment = ref({
  title: '',
  course: 'C Programming Basics',
  module: 'Module 1: Introduction to C',
  chapter: 'Chapter 1.1: Environment Setup',
  due_date: '2025-08-30 23:59',
  max_marks: 50,
  file_types: 'PDF, ZIP, C',
  description: '',
  grading_rubric: 'Code correctness (20pts), Style & formatting (15pts), Documentation (15pts)',
})

watch(() => courseList.value, (list) => {
  if (list && list.length > 0 && !list.includes(newAssignment.value.course)) {
    newAssignment.value.course = list[0]
  }
}, { immediate: true })

function submitForm() {
  if (!newAssignment.value.title.trim()) {
    alert('Please enter assignment title.')
    return
  }
  emit('save', JSON.parse(JSON.stringify(newAssignment.value)))
  newAssignment.value.title = ''
  newAssignment.value.description = ''
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-[#0e1322] border border-purple-500/30 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
        <h3 class="font-bold text-base text-white flex items-center gap-2">
          <span>📎</span> CREATE NEW ASSIGNMENT
        </h3>
        <button @click="emit('close')" class="text-slate-400 hover:text-white">✕</button>
      </div>

      <div class="space-y-3 text-xs">
        <div>
          <label class="block font-semibold text-slate-300 mb-1">Assignment Title:</label>
          <input v-model="newAssignment.title" type="text" placeholder="e.g. Create Your First C Program" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white focus:ring-2 focus:ring-purple-500 focus:outline-none" />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Course:</label>
            <select v-model="newAssignment.course" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-200 focus:outline-none">
              <option v-for="cName in courseList" :key="cName" :value="cName" class="bg-[#121827] text-white py-1">{{ cName }}</option>
            </select>
          </div>
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Due Date & Time:</label>
            <input v-model="newAssignment.due_date" type="text" placeholder="YYYY-MM-DD HH:mm" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Max Marks:</label>
            <input v-model="newAssignment.max_marks" type="number" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white" />
          </div>
          <div>
            <label class="block font-semibold text-slate-300 mb-1">Allowed File Formats:</label>
            <input v-model="newAssignment.file_types" type="text" placeholder="PDF, ZIP, C, DOCX" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white" />
          </div>
        </div>

        <div>
          <label class="block font-semibold text-slate-300 mb-1">Assignment Instructions / Prompt:</label>
          <textarea v-model="newAssignment.description" rows="3" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white" placeholder="Enter detailed guidelines for students..."></textarea>
        </div>

        <div>
          <label class="block font-semibold text-slate-300 mb-1">Grading Rubric Guidelines:</label>
          <textarea v-model="newAssignment.grading_rubric" rows="2" class="w-full bg-[#121827] border border-slate-700/80 rounded-xl p-2.5 text-xs text-white"></textarea>
        </div>
      </div>

      <div class="flex justify-end gap-2 border-t border-slate-700/60 pt-4">
        <button @click="emit('close')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">Cancel</button>
        <button @click="submitForm" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold">Publish Assignment</button>
      </div>
    </div>
  </div>
</template>
