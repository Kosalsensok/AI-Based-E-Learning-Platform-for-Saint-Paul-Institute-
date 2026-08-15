<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  courses?: Array<any>
}>()

const showCreateModal = ref(false)
const selectedAssignment = ref<any>(null)
const showSubmissionModal = ref(false)

const assignments = ref([
  {
    id: 1,
    title: 'Assignment 1: C Pointers & Dynamic Memory Management',
    course: 'C Programming Basics',
    module: 'Module 2: Variables',
    submission_type: 'File Upload (PDF/ZIP)',
    due_date: '2026-06-30 23:59',
    max_score: 100,
    submissions_count: 38,
    graded_count: 35,
    status: 'Active',
    instructions: 'Implement a dynamic vector structure in C using malloc(), realloc(), and free(). Ensure zero memory leaks using Valgrind inspection.',
    attached_file: 'assignment_1_specification.pdf'
  },
  {
    id: 2,
    title: 'Assignment 2: Database Schema ERD Design & 3NF Normalization',
    course: 'Database Systems',
    module: 'Module 1: Relational Modeling',
    submission_type: 'Text & File Upload',
    due_date: '2026-07-15 23:59',
    max_score: 100,
    submissions_count: 30,
    graded_count: 22,
    status: 'Active',
    instructions: 'Design an 3NF ER diagram for an e-learning platform including users, courses, enrollments, and ABA PayWay transactions.',
    attached_file: 'erd_template.docx'
  }
])

const studentSubmissions = ref([
  { id: 101, student_name: 'Chan Dara', student_id: 'STU241001', score: 90, status: 'Graded', submitted_at: '2026-06-28 14:30', file_name: 'chan_dara_assignment1.zip', feedback: 'Great implementation of memory reallocation and clean structs.' },
  { id: 102, student_name: 'Bun Rithy', student_id: 'STU241002', score: 85, status: 'Graded', submitted_at: '2026-06-29 09:15', file_name: 'rithy_c_pointers.c', feedback: 'Correct logic, but remember to check malloc return pointer for NULL.' },
  { id: 103, student_name: 'Sok Chara', student_id: 'STU241003', score: null, status: 'Pending Review', submitted_at: '2026-06-30 22:10', file_name: 'sok_chara_hw1.pdf', feedback: '' }
])

const newAssignment = ref({
  title: '',
  course: 'C Programming Basics',
  module: 'Module 1: Intro',
  submission_type: 'File Upload (PDF/ZIP)',
  due_date: '',
  max_score: 100,
  instructions: '',
  attached_file: 'assignment_brief.pdf'
})

const gradeForm = ref({
  submission_id: null,
  score: 85,
  feedback: 'Good work! Clearly structured submission.'
})

const openSubmissionReview = (assignment: any) => {
  selectedAssignment.value = assignment
  showSubmissionModal.value = true
}

const saveGrade = (sub: any) => {
  sub.score = gradeForm.value.score
  sub.status = 'Graded'
  sub.feedback = gradeForm.value.feedback
  alert(`Grade saved for ${sub.student_name}: ${sub.score}/100`)
}

const createAssignment = () => {
  if (!newAssignment.value.title) {
    alert('Please enter assignment title')
    return
  }
  assignments.value.unshift({
    id: Date.now(),
    title: newAssignment.value.title,
    course: newAssignment.value.course,
    module: newAssignment.value.module,
    submission_type: newAssignment.value.submission_type,
    due_date: newAssignment.value.due_date || '2026-07-30 23:59',
    max_score: newAssignment.value.max_score,
    submissions_count: 0,
    graded_count: 0,
    status: 'Active',
    instructions: newAssignment.value.instructions,
    attached_file: 'brief.pdf'
  })
  showCreateModal.value = false
  alert('Assignment created successfully!')
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 rounded-3xl border border-indigo-500/20 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h2 class="text-xl font-black text-white flex items-center gap-2">
          <span>📁 Course Assignments (កិច្ចការផ្ញើឯកសារ/Essay)</span>
        </h2>
        <p class="text-xs text-slate-300 mt-1">
          Collect file uploads, project zip files, or long essays with teacher feedback & grading rubric.
        </p>
      </div>

      <button
        @click="showCreateModal = true"
        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-black shadow-lg shadow-blue-500/20 transition flex items-center gap-2 cursor-pointer w-fit"
      >
        <span>+ បង្កើត Assignment</span>
      </button>
    </div>

    <!-- Assignment Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div
        v-for="a in assignments"
        :key="a.id"
        class="bg-white dark:bg-gray-800 rounded-3xl p-5 border border-slate-200/80 dark:border-gray-700 shadow-sm space-y-4 hover:shadow-md transition"
      >
        <div class="flex items-start justify-between gap-2">
          <div>
            <span class="px-2.5 py-0.5 rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 text-[10px] font-extrabold">
              {{ a.course }} • {{ a.module }}
            </span>
            <h3 class="font-black text-slate-800 dark:text-white text-sm mt-1.5">{{ a.title }}</h3>
          </div>
          <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 text-[10px] font-bold">
            {{ a.status }}
          </span>
        </div>

        <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed">
          {{ a.instructions }}
        </p>

        <div class="grid grid-cols-3 gap-2 p-3 bg-slate-50 dark:bg-gray-700/40 rounded-2xl text-xs border border-slate-100 dark:border-gray-700">
          <div>
            <p class="text-[10px] text-slate-400 font-bold uppercase">Submission Type</p>
            <p class="font-extrabold text-slate-700 dark:text-slate-200 truncate mt-0.5">{{ a.submission_type }}</p>
          </div>
          <div>
            <p class="text-[10px] text-slate-400 font-bold uppercase">Due Date</p>
            <p class="font-extrabold text-slate-700 dark:text-slate-200 mt-0.5">{{ a.due_date }}</p>
          </div>
          <div>
            <p class="text-[10px] text-slate-400 font-bold uppercase">Submitted</p>
            <p class="font-black text-blue-600 mt-0.5">{{ a.submissions_count }} សិស្ស ({{ a.graded_count }} Graded)</p>
          </div>
        </div>

        <div class="flex items-center justify-between pt-1">
          <span class="text-xs font-bold text-slate-500">Max Score: {{ a.max_score }} pts</span>
          <button
            @click="openSubmissionReview(a)"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm"
          >
            📋 Review Submissions ({{ a.submissions_count }})
          </button>
        </div>
      </div>
    </div>

    <!-- ➕ CREATE ASSIGNMENT MODAL -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-gray-700">
        <div class="flex items-center justify-between border-b pb-3">
          <h3 class="text-base font-extrabold text-slate-800 dark:text-white">➕ Create New Assignment</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-bold mb-1">Assignment Title (ចំណងជើង)</label>
            <input v-model="newAssignment.title" type="text" placeholder="e.g. Applied Networking Packet Tracer Lab" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold mb-1">Submission Type</label>
              <select v-model="newAssignment.submission_type" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700">
                <option value="File Upload (PDF/ZIP)">File Upload (PDF/ZIP)</option>
                <option value="Essay / Rich Text">Essay / Rich Text</option>
                <option value="Text & File Upload">Text & File Upload</option>
                <option value="Source Code File">Source Code (.c, .py, .java)</option>
              </select>
            </div>
            <div>
              <label class="block font-bold mb-1">Due Date & Time</label>
              <input v-model="newAssignment.due_date" type="datetime-local" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
            </div>
          </div>

          <div>
            <label class="block font-bold mb-1">Assignment Instructions</label>
            <textarea v-model="newAssignment.instructions" rows="3" placeholder="Provide complete guidelines and grading criteria..." class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700"></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t">
          <button @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="createAssignment" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold shadow">Save & Publish</button>
        </div>
      </div>
    </div>

    <!-- 📋 SUBMISSIONS REVIEW MODAL -->
    <div v-if="showSubmissionModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-gray-700 overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between border-b pb-3">
          <div>
            <h3 class="text-base font-extrabold text-slate-800 dark:text-white">📋 Submissions — {{ selectedAssignment?.title }}</h3>
            <p class="text-xs text-slate-400">{{ selectedAssignment?.submissions_count }} student submissions received</p>
          </div>
          <button @click="showSubmissionModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-3 text-xs">
          <div
            v-for="sub in studentSubmissions"
            :key="sub.id"
            class="p-4 bg-slate-50 dark:bg-gray-700/40 rounded-2xl border border-slate-200 dark:border-gray-600 space-y-3"
          >
            <div class="flex justify-between items-start">
              <div>
                <p class="font-black text-slate-800 dark:text-white">{{ sub.student_name }} ({{ sub.student_id }})</p>
                <p class="text-[11px] text-slate-400">Submitted on: {{ sub.submitted_at }} • File: <span class="font-mono text-blue-600">{{ sub.file_name }}</span></p>
              </div>
              <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px]" :class="sub.status === 'Graded' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'">
                {{ sub.status }}
              </span>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-1">
              <div>
                <label class="block font-bold text-[10px] text-slate-500 uppercase">Score (0-100)</label>
                <input v-model="sub.score" type="number" min="0" max="100" class="w-full p-2 rounded-xl border border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs font-bold" />
              </div>
              <div>
                <label class="block font-bold text-[10px] text-slate-500 uppercase">Feedback</label>
                <input v-model="sub.feedback" type="text" placeholder="Teacher feedback..." class="w-full p-2 rounded-xl border border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs" />
              </div>
            </div>

            <div class="flex justify-end pt-1">
              <button @click="saveGrade(sub)" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm">
                💾 Save Grade
              </button>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t">
          <button @click="showSubmissionModal = false" class="px-5 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold shadow">Close Submissions</button>
        </div>
      </div>
    </div>
  </div>
</template>
