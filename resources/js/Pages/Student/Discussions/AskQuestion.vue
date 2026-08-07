<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const questionTitle = ref('Why is my loop infinite?')
const questionDetail = ref('When I run my while loop with i < 10, the program keeps printing without stopping. How can I fix this?')
const selectedCourse = ref('C Programming Basics')
const selectedChapter = ref('Module 2 → Ch 2.3 Loops')
const targetItem = ref('🎥 Video')
const language = ref('Both')
const priority = ref('Normal')

const notifyEmail = ref(true)
const notifyInApp = ref(true)
const notifyPush = ref(true)

const showSmartHelper = ref(false)
const submittedSuccess = ref(false)

const handleSend = () => {
  if (!showSmartHelper.value) {
    showSmartHelper.value = true
    return
  }
  showSmartHelper.value = false
  submittedSuccess.value = true
  setTimeout(() => submittedSuccess.value = false, 3000)
}
</script>

<template>
  <StudentLayout title="Ask a Question">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 border border-blue-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold uppercase tracking-wider">
            ❓ TARGETED LESSON Q&amp;A
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>❓ ASK A QUESTION</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            សួរតាម Chapter/Item ដែលកំពុងរៀន ដើម្បីឱ្យគ្រូបង្រៀនឆ្លើយត្រូវចំណុច និងឆាប់រហ័ស
          </p>
        </div>
      </div>

      <!-- Success Toast -->
      <div v-if="submittedSuccess" class="p-4 bg-emerald-500/10 border border-emerald-500/40 rounded-2xl text-xs text-emerald-300 font-bold flex items-center justify-between">
        <span>✅ Question submitted successfully to Mr. Sophea! You will be notified when answered.</span>
      </div>

      <!-- FORM CARD (Matching Prompt Spec Layout) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        
        <!-- Selection Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
          <div class="space-y-1">
            <label class="font-bold text-white uppercase">Course:</label>
            <select v-model="selectedCourse" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
              <option>C Programming Basics</option>
              <option>Database Systems</option>
              <option>Web Development</option>
            </select>
          </div>
          <div class="space-y-1">
            <label class="font-bold text-white uppercase">Chapter:</label>
            <select v-model="selectedChapter" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
              <option>Module 2 → Ch 2.3 Loops</option>
              <option>Module 2 → Ch 2.1 Variables</option>
              <option>Module 3 → Ch 3.1 Functions</option>
            </select>
          </div>
          <div class="space-y-1">
            <label class="font-bold text-white uppercase">Target Item (optional):</label>
            <select v-model="targetItem" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
              <option>🎥 Video</option>
              <option>📄 PDF</option>
              <option>📊 Slide</option>
            </select>
          </div>
        </div>

        <!-- Question Title & Detail -->
        <div class="space-y-4 text-xs">
          <div class="space-y-1">
            <label class="font-bold text-white uppercase">Question Title:</label>
            <input
              v-model="questionTitle"
              type="text"
              placeholder="e.g. Why is my loop infinite?"
              class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-bold"
            />
          </div>

          <div class="space-y-1">
            <label class="font-bold text-white uppercase">Question Detail:</label>
            <textarea
              v-model="questionDetail"
              rows="4"
              placeholder="Type your question here…"
              class="w-full bg-slate-900 border border-slate-700 rounded-xl p-4 text-xs text-white focus:outline-none focus:border-blue-500 custom-scrollbar"
            ></textarea>
          </div>
        </div>

        <!-- Attachments Row -->
        <div class="space-y-2 text-xs">
          <label class="font-bold text-white uppercase">Attachments (optional):</label>
          <div class="flex flex-wrap items-center gap-2">
            <button class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700">
              📎 Screenshot
            </button>
            <button class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700">
              💻 Code snippet
            </button>
            <button class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700">
              📎 PDF / Img
            </button>
          </div>
        </div>

        <!-- Meta Controls Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-2 border-t border-slate-700/60">
          <div class="space-y-1">
            <label class="font-bold text-white uppercase">Language:</label>
            <select v-model="language" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
              <option>Both (Khmer &amp; English)</option>
              <option>Khmer</option>
              <option>English</option>
            </select>
          </div>

          <div class="space-y-1">
            <label class="font-bold text-white uppercase">Priority:</label>
            <select v-model="priority" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
              <option>Normal</option>
              <option>Urgent 🔥</option>
            </select>
          </div>
        </div>

        <!-- Notifications Row -->
        <div class="space-y-2 text-xs">
          <label class="font-bold text-white uppercase">Notify Me:</label>
          <div class="flex items-center gap-4 text-slate-300 font-bold">
            <label class="flex items-center gap-1.5 cursor-pointer">
              <input type="checkbox" v-model="notifyEmail" class="rounded bg-slate-900 border-slate-700 text-blue-500" />
              <span>✓ Email</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer">
              <input type="checkbox" v-model="notifyInApp" class="rounded bg-slate-900 border-slate-700 text-blue-500" />
              <span>✓ In-App</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer">
              <input type="checkbox" v-model="notifyPush" class="rounded bg-slate-900 border-slate-700 text-blue-500" />
              <span>✓ Push</span>
            </label>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-700/60">
          <button class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700">
            💾 Save Draft
          </button>
          <button @click="handleSend" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs shadow-md">
            📤 Send Question →
          </button>
        </div>

      </div>

      <!-- SMART HELPER MODAL (Matching Prompt Spec UX) -->
      <div v-if="showSmartHelper" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-blue-500/40 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
          <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
            <h3 class="text-sm font-black text-white">🤖 Smart Helper Suggestion</h3>
            <button @click="showSmartHelper = false" class="text-slate-400 hover:text-white font-bold text-sm">✕</button>
          </div>

          <p class="text-xs text-slate-300">
            យើងបានស្វែងរកសំណួរ FAQ ដែលស្រដៀងគ្នា មុននឹងផ្ញើទៅកាន់គ្រូ៖
          </p>

          <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 text-xs space-y-2">
            <span class="text-amber-400 font-bold">🟡 Similar FAQ Answer:</span>
            <p class="text-slate-200 font-bold">"Why is my loop infinite?"</p>
            <p class="text-slate-400">Answer: You likely forgot to update the loop counter variable inside the loop body (e.g. i++).</p>
          </div>

          <div class="flex items-center justify-end gap-3 pt-2">
            <button @click="showSmartHelper = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
              This Solved It! (Cancel)
            </button>
            <button @click="handleSend" class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-bold text-xs shadow-md">
              Send Question Anyway →
            </button>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
