<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  show: boolean
  deadline: any
  students: Array<any>
}>()

const emit = defineEmits(['close'])

const aiPlanGenerated = ref(false)
const aiPlanText = ref('')

const remindStudent = (studentName: string) => {
  alert(`បានផ្ញើសាររំលឹក Email + Push ទៅកាន់ ${studentName} រួចរាល់!`)
}

const extendStudent = (studentName: string) => {
  alert(`បានពន្យារពេល Deadline ចំនួន ២ ថ្ងៃបន្ថែមសម្រាប់ ${studentName}!`)
}

const callStudent = (studentName: string) => {
  alert(`បានតេទៅលេខទូរស័ព្ទរបស់សិស្ស ${studentName}`)
}

const extendAll = () => {
  if (props.deadline?.id) {
    router.post(route('teacher.calendar.deadline.extend', props.deadline.id), { days: 2 }, {
      onSuccess: () => {
        alert('បានពន្យារពេល Deadline ចំនួន ២ ថ្ងៃសម្រាប់សិស្ស Overdue ទាំងអស់!')
      }
    })
  } else {
    alert('បានពន្យារពេល Deadline ចំនួន ២ ថ្ងៃសម្រាប់សិស្ស Overdue ទាំងអស់!')
  }
}

const remindAll = () => {
  if (props.deadline?.id) {
    router.post(route('teacher.calendar.deadline.remind', props.deadline.id), {}, {
      onSuccess: () => {
        alert('បានផ្ញើសាររំលឹក Reminder ទៅកាន់សិស្ស Overdue ទាំងអស់!')
      }
    })
  } else {
    alert('បានផ្ញើសាររំលឹក Reminder ទៅកាន់សិស្ស Overdue ទាំងអស់!')
  }
}

const generateAiRemedialPlan = () => {
  aiPlanGenerated.value = true
  aiPlanText.value = '🤖 AI Remedial Action Plan: ផ្ញើការរំលឹកកិច្ចការស្វ័យប្រវត្តិ + បង្កើត Short Quiz រំលឹកឡើងវិញមេរៀន Module 1 សម្រាប់សិស្ស ៤០ នាក់ដែលខកខាន'
}
</script>

<template>
  <div v-if="show && deadline" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-3xl w-full shadow-2xl border border-slate-200 dark:border-gray-700 my-8 overflow-hidden transform transition-all">
      <!-- Header -->
      <div class="px-6 py-4 bg-gradient-to-r from-red-600 via-rose-700 to-pink-700 text-white flex items-center justify-between">
        <div class="space-y-0.5">
          <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-white text-red-700">🚨 AT RISK</span>
            <h3 class="font-bold text-lg leading-tight truncate">
              OVERDUE STUDENTS — {{ deadline.title }}
            </h3>
          </div>
          <p class="text-xs text-red-100">
            Overdue: <span class="font-bold text-yellow-300">40 Students</span> · Due: {{ deadline.due_at ? new Date(deadline.due_at).toLocaleString() : 'May 16, 11:59 PM' }}
          </p>
        </div>
        <button @click="$emit('close')" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white font-bold transition">✕</button>
      </div>

      <!-- Bulk Actions Toolbar -->
      <div class="px-6 py-3 bg-red-50/80 dark:bg-gray-700/60 border-b border-red-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-2 text-xs">
        <span class="font-bold text-red-800 dark:text-red-300">⚡ Fast Recovery Actions:</span>
        <div class="flex flex-wrap items-center gap-2">
          <button @click="remindAll" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold shadow-sm transition">
            📧 Remind All
          </button>
          <button @click="extendAll" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold shadow-sm transition">
            📅 Extend Deadline for All (+2 days)
          </button>
          <button @click="generateAiRemedialPlan" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-sm transition flex items-center gap-1">
            <span>🎯 Create Remedial Plan with AI</span>
          </button>
        </div>
      </div>

      <!-- AI Banner if generated -->
      <div v-if="aiPlanGenerated" class="mx-6 mt-4 p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-900/40 border border-indigo-200 dark:border-indigo-700 text-xs space-y-1">
        <p class="font-bold text-indigo-800 dark:text-indigo-200">{{ aiPlanText }}</p>
        <p class="text-slate-500 dark:text-slate-300">ប្រព័ន្ធនឹងផ្ញើសារកំណត់គោលដៅពិសេស ព្រមទាំងផ្តល់ Link រំលឹកមេរៀន 5-min ទៅសិស្សរៀងៗខ្លួន។</p>
      </div>

      <!-- Students Table -->
      <div class="p-6 space-y-4 text-xs">
        <div class="overflow-x-auto border border-slate-200 dark:border-gray-700 rounded-2xl">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-gray-700 text-[11px] uppercase">
                <th class="py-3 px-4">Student Name</th>
                <th class="py-3 px-4">Major</th>
                <th class="py-3 px-4">Last Activity</th>
                <th class="py-3 px-4 text-center">Current Score</th>
                <th class="py-3 px-4 text-center">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-gray-700 font-medium text-slate-700 dark:text-slate-200">
              <tr v-for="st in students" :key="st.id" class="hover:bg-slate-50 dark:hover:bg-gray-700/50">
                <td class="py-3 px-4 font-bold text-slate-800 dark:text-white">
                  <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-red-100 text-red-700 font-bold flex items-center justify-center text-xs">
                      {{ st.name[0] }}
                    </div>
                    <div>
                      <p>{{ st.name }}</p>
                      <p class="text-[10px] text-slate-400 font-normal">{{ st.email }}</p>
                    </div>
                  </div>
                </td>

                <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                  {{ st.major }}
                </td>

                <td class="py-3 px-4 text-amber-600 font-semibold">
                  {{ st.last_activity }}
                </td>

                <td class="py-3 px-4 text-center">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">
                    {{ st.score }}
                  </span>
                </td>

                <td class="py-3 px-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <button @click="remindStudent(st.name)" class="px-2.5 py-1 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition">
                      📧 Remind
                    </button>
                    <button @click="extendStudent(st.name)" class="px-2.5 py-1 bg-amber-600 text-white rounded-lg font-bold hover:bg-amber-700 transition">
                      📅 Extend
                    </button>
                    <button @click="callStudent(st.name)" class="px-2.5 py-1 bg-slate-700 text-white rounded-lg font-bold hover:bg-slate-800 transition">
                      📞 Call
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-6 py-4 bg-slate-100 dark:bg-gray-700/80 border-t border-slate-200 dark:border-gray-700 flex justify-end">
        <button @click="$emit('close')" class="px-5 py-2.5 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-900 transition">
          Close List
        </button>
      </div>
    </div>
  </div>
</template>
