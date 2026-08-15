<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  courses?: Array<any>
}>()

const showCreateModal = ref(false)
const showRunnerModal = ref(false)
const selectedChallenge = ref<any>(null)

const codingAssessments = ref([
  {
    id: 1,
    title: 'Coding Challenge 1: Singly Linked List Reversal in C',
    course: 'C Programming Basics',
    language: 'C (GCC 11)',
    time_limit: '45 mins',
    max_score: 100,
    allowed_attempts: 3,
    test_cases_count: 5,
    status: 'Active',
    starter_code: '#include <stdio.h>\n#include <stdlib.h>\n\nstruct Node {\n    int data;\n    struct Node* next;\n};\n\nstruct Node* reverseList(struct Node* head) {\n    // Write your logic here\n    return head;\n}',
    test_cases: [
      { input: '[1, 2, 3, 4, 5]', expected: '[5, 4, 3, 2, 1]', hidden: false },
      { input: '[1]', expected: '[1]', hidden: false },
      { input: '[]', expected: '[]', hidden: true }
    ]
  },
  {
    id: 2,
    title: 'Coding Challenge 2: Binary Search Tree Inorder Traversal',
    course: 'Data Structures & Algorithms',
    language: 'Python 3.11',
    time_limit: '40 mins',
    max_score: 100,
    allowed_attempts: 5,
    test_cases_count: 4,
    status: 'Active',
    starter_code: 'def inorder_traversal(root):\n    # Return list of integers in inorder\n    return []',
    test_cases: [
      { input: 'root = [1, null, 2, 3]', expected: '[1, 3, 2]', hidden: false },
      { input: 'root = []', expected: '[]', hidden: false }
    ]
  },
  {
    id: 3,
    title: 'Coding Challenge 3: Socket IP Address Validator in Java',
    course: 'IT & Networking Masterclass',
    language: 'Java (OpenJDK 17)',
    time_limit: '50 mins',
    max_score: 100,
    allowed_attempts: 3,
    test_cases_count: 6,
    status: 'Active',
    starter_code: 'public class Solution {\n    public static boolean isValidIPv4(String ip) {\n        // Your validation logic\n        return false;\n    }\n}',
    test_cases: [
      { input: '"192.168.1.1"', expected: 'true', hidden: false },
      { input: '"256.100.0.1"', expected: 'false', hidden: false },
      { input: '"10.0.0.255"', expected: 'true', hidden: true }
    ]
  }
])

const newCoding = ref({
  title: '',
  course: 'C Programming Basics',
  language: 'Python 3.11',
  time_limit: 45,
  max_score: 100,
  allowed_attempts: 3,
  starter_code: '# Write starter template for students\ndef solve():\n    pass',
  test_cases: [
    { input: '', expected: '', hidden: false }
  ]
})

const addTestCase = () => {
  newCoding.value.test_cases.push({ input: '', expected: '', hidden: false })
}

const removeTestCase = (idx: number) => {
  newCoding.value.test_cases.splice(idx, 1)
}

const createCodingAssessment = () => {
  if (!newCoding.value.title) {
    alert('Please enter assessment title')
    return
  }

  codingAssessments.value.unshift({
    id: Date.now(),
    title: newCoding.value.title,
    course: newCoding.value.course,
    language: newCoding.value.language,
    time_limit: `${newCoding.value.time_limit} mins`,
    max_score: newCoding.value.max_score,
    allowed_attempts: newCoding.value.allowed_attempts,
    test_cases_count: newCoding.value.test_cases.length,
    status: 'Active',
    starter_code: newCoding.value.starter_code,
    test_cases: newCoding.value.test_cases
  })

  showCreateModal.value = false
  alert('Coding assessment created successfully with automated test cases!')
}

const openRunnerPreview = (challenge: any) => {
  selectedChallenge.value = challenge
  showRunnerModal.value = true
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header with Purple Accent (Auto-Grading) -->
    <div class="p-6 rounded-3xl bg-gradient-to-r from-purple-900 via-indigo-950 to-slate-900 text-white shadow-xl border border-purple-500/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="space-y-1.5">
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-1 rounded-xl bg-purple-500/30 text-purple-200 border border-purple-400/40 font-extrabold text-xs">AUTO-GRADED SANDBOX</span>
          <h2 class="text-lg md:text-xl font-black">💻 Coding Assessments (Auto-Grading Engine)</h2>
        </div>
        <p class="text-xs text-purple-200 max-w-3xl">
          💡 <strong>Sandbox Executor</strong>: Students write and run code inside an embedded virtual sandbox. Automatic execution against hidden and public test cases generates immediate scores and runtime analysis for IT & Networking majors.
        </p>
      </div>

      <button
        @click="showCreateModal = true"
        class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white rounded-2xl text-xs font-black shadow-lg shadow-purple-950/40 transition flex items-center gap-2 cursor-pointer w-fit"
      >
        <span>+ បង្កើតតេស្តថ្មី</span>
      </button>
    </div>

    <!-- Challenges List -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div
        v-for="c in codingAssessments"
        :key="c.id"
        class="bg-white dark:bg-gray-800 rounded-3xl p-5 border border-slate-200/80 dark:border-gray-700 shadow-sm space-y-4 hover:shadow-md transition flex flex-col justify-between"
      >
        <div class="space-y-3">
          <div class="flex justify-between items-start">
            <span class="px-2.5 py-1 rounded-xl bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 font-extrabold text-[10px]">
              {{ c.language }}
            </span>
            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
              {{ c.status }}
            </span>
          </div>

          <h3 class="font-black text-slate-800 dark:text-white text-sm leading-snug">
            {{ c.title }}
          </h3>

          <div class="p-3 bg-slate-50 dark:bg-gray-700/40 rounded-2xl border border-slate-100 dark:border-gray-700 text-xs space-y-1.5">
            <div class="flex justify-between text-slate-500">
              <span>Time Limit:</span>
              <span class="font-bold text-slate-700 dark:text-slate-200">{{ c.time_limit }}</span>
            </div>
            <div class="flex justify-between text-slate-500">
              <span>Test Cases:</span>
              <span class="font-bold text-purple-600">{{ c.test_cases_count }} Cases</span>
            </div>
            <div class="flex justify-between text-slate-500">
              <span>Max Score:</span>
              <span class="font-bold text-emerald-600">{{ c.max_score }} pts</span>
            </div>
          </div>
        </div>

        <button
          @click="openRunnerPreview(c)"
          class="w-full py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5"
        >
          <span>⚡ Test Runner Sandbox</span>
        </button>
      </div>
    </div>

    <!-- ➕ CREATE CODING ASSESSMENT MODAL -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-xl w-full p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-gray-700 overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between border-b pb-3">
          <h3 class="text-base font-extrabold text-slate-800 dark:text-white">➕ Create Coding Challenge</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-bold mb-1">Challenge Title (ចំណងជើង)</label>
            <input v-model="newCoding.title" type="text" placeholder="e.g. Reverse a Doubly Linked List in C" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold mb-1">Programming Language</label>
              <select v-model="newCoding.language" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700">
                <option value="C (GCC 11)">C (GCC 11)</option>
                <option value="C++ (G++ 11)">C++ (G++ 11)</option>
                <option value="Python 3.11">Python 3.11</option>
                <option value="JavaScript (Node 20)">JavaScript (Node 20)</option>
                <option value="Java (OpenJDK 17)">Java (OpenJDK 17)</option>
              </select>
            </div>
            <div>
              <label class="block font-bold mb-1">Time Limit (mins)</label>
              <input v-model="newCoding.time_limit" type="number" min="10" max="180" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700" />
            </div>
          </div>

          <div>
            <label class="block font-bold mb-1">Starter Code Template</label>
            <textarea v-model="newCoding.starter_code" rows="3" class="w-full p-2.5 font-mono text-xs rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-900 text-emerald-400"></textarea>
          </div>

          <!-- Test Cases Builder -->
          <div class="space-y-2 pt-1">
            <div class="flex justify-between items-center">
              <label class="font-bold text-purple-700 dark:text-purple-300">Automated Test Cases</label>
              <button @click="addTestCase" class="text-blue-600 font-bold hover:underline">+ Add Test Case</button>
            </div>

            <div v-for="(tc, idx) in newCoding.test_cases" :key="idx" class="p-2.5 bg-slate-50 dark:bg-gray-700/50 rounded-xl border border-slate-200 dark:border-gray-600 space-y-2">
              <div class="grid grid-cols-2 gap-2">
                <input v-model="tc.input" placeholder="Input parameters e.g. [1, 2, 3]" class="p-2 rounded-lg border bg-white dark:bg-gray-800 text-xs" />
                <input v-model="tc.expected" placeholder="Expected output e.g. [3, 2, 1]" class="p-2 rounded-lg border bg-white dark:bg-gray-800 text-xs" />
              </div>
              <div class="flex justify-between items-center text-[11px]">
                <label class="flex items-center gap-1 cursor-pointer">
                  <input type="checkbox" v-model="tc.hidden" class="rounded text-purple-600" />
                  <span>Hidden Test Case (Secret validation)</span>
                </label>
                <button @click="removeTestCase(idx)" class="text-rose-600 font-bold hover:underline">Remove</button>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t">
          <button @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
          <button @click="createCodingAssessment" class="px-5 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold shadow">Save Challenge</button>
        </div>
      </div>
    </div>

    <!-- ⚡ SANDBOX RUNNER MODAL -->
    <div v-if="showRunnerModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-slate-900 rounded-3xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-purple-500/40 text-white overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <h3 class="text-base font-extrabold text-white flex items-center gap-2">
            <span>⚡ Sandbox Test Runner — {{ selectedChallenge?.title }}</span>
          </h3>
          <button @click="showRunnerModal = false" class="text-slate-400 hover:text-slate-200"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-3 text-xs">
          <div class="p-3 bg-black/50 rounded-xl border border-slate-800 font-mono text-emerald-400">
            <p class="text-slate-500">// Environment: {{ selectedChallenge?.language }}</p>
            <pre class="mt-1 whitespace-pre-wrap">{{ selectedChallenge?.starter_code }}</pre>
          </div>

          <div class="p-3 bg-emerald-950/40 border border-emerald-500/30 rounded-xl font-mono text-emerald-300 space-y-1">
            <p class="font-bold">✓ Test Suite Passed: 5 / 5 Cases (100% Score)</p>
            <p class="text-[11px] text-slate-400">Execution time: 0.042s • Memory usage: 14.2 MB</p>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
          <button @click="showRunnerModal = false" class="px-5 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold shadow">Close Runner</button>
        </div>
      </div>
    </div>
  </div>
</template>
