<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const showSubmitModal = ref(false)
const showGradedView = ref(false)
const academicConfirmed = ref(true)
const coverLetter = ref('ពិពណ៌នាខ្លីៗអំពីគម្រោងរបស់ខ្ញុំ... Code ដំណើរការបាន ៤ operation...')

const uploadedFiles = ref([
  { name: 'calculator.c', size: '12 KB' },
  { name: 'documentation.pdf', size: '2.1 MB' }
])

const submitAssignment = () => {
  showSubmitModal.value = false
  showGradedView.value = true
}
</script>

<template>
  <StudentLayout title="Assignments & Projects">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-purple-950 via-slate-900 to-indigo-950 border border-purple-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold uppercase tracking-wider">
            📄 ASSIGNMENTS MODULE
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>📄 ASSIGNMENTS (កិច្ចការ/គម្រោង)</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ប្រគល់កិច្ចការផ្ទះ គម្រោងមេរៀន តាមដាន Rubric ស្ដង់ដារ និងទទួលបាន Feedback ពីគ្រូ
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="px-4 py-2.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Submitted Assignments</p>
            <p class="text-base font-black text-purple-400">1 / 2 Graded</p>
          </div>
        </div>
      </div>

      <!-- MAIN ASSIGNMENT DASHBOARD -->
      <div v-if="!showGradedView" class="space-y-6">
        
        <!-- ACTIVE ASSIGNMENT CARD -->
        <div class="bg-slate-800/90 border border-purple-500/40 rounded-3xl p-6 shadow-xl space-y-4">
          <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-700/60 pb-3">
            <div>
              <span class="px-3 py-1 rounded-full bg-purple-600/20 text-purple-300 font-bold text-xs border border-purple-500/40">
                📄 Assignment 1: Build a Calculator Program
              </span>
              <p class="text-xs text-slate-400 mt-1">
                Module 2  •  Max Score: 100 pts  •  Weight: 20% of grade
              </p>
            </div>

            <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/40">
              🟡 DUE SOON (⏳ 3 days, 4 hours left)
            </span>
          </div>

          <p class="text-xs text-rose-300 font-semibold">
            Due: 30/05/2025 11:59 PM  •  Late Policy: -10% per day (max 3 days late allowed)
          </p>

          <!-- Requirements & Rubric Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="p-4 bg-slate-900/70 rounded-2xl border border-slate-700/60 space-y-2">
              <h4 class="font-bold text-white uppercase text-[11px]">📋 Requirements:</h4>
              <ul class="space-y-1 text-slate-300">
                <li>• Build a C program that performs +, -, ×, ÷</li>
                <li>• Must handle division by zero error</li>
                <li>• Submit: .c file + documentation (.pdf)</li>
              </ul>
            </div>

            <div class="p-4 bg-slate-900/70 rounded-2xl border border-slate-700/60 space-y-2">
              <h4 class="font-bold text-white uppercase text-[11px]">🏆 Grading Rubric (100 pts):</h4>
              <ul class="space-y-1 text-slate-300">
                <li>Code Works: 25 pts  •  Code Quality: 25 pts</li>
                <li>Documentation: 25 pts  •  Creativity: 25 pts</li>
              </ul>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-wrap items-center gap-3 pt-2">
            <button
              @click="showSubmitModal = true"
              class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-black text-xs shadow-lg transition-all hover:scale-105"
            >
              📤 Submit Assignment →
            </button>
            <button class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700">
              💬 Ask Teacher
            </button>
            <button class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700">
              📄 Download Guide PDF
            </button>
          </div>
        </div>

        <!-- LOCKED ASSIGNMENT CARD -->
        <div class="bg-slate-800/60 border border-slate-700/60 rounded-3xl p-6 shadow-xl space-y-2 opacity-60">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-300">📄 Final Project: Console-Based Student Grade System</h3>
            <span class="px-3 py-1 rounded-full bg-slate-900 text-slate-400 text-xs font-bold border border-slate-700">🔒 Locked</span>
          </div>
          <p class="text-xs text-slate-400">Unlocks automatically after completing Module 4 content.</p>
        </div>

      </div>

      <!-- SUBMISSION MODAL -->
      <div v-if="showSubmitModal" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-purple-500/50 rounded-3xl max-w-xl w-full p-6 space-y-5 shadow-2xl">
          <div class="border-b border-slate-800 pb-3">
            <h3 class="text-lg font-black text-white">📤 Submit Assignment 1 – Calculator Program</h3>
            <p class="text-xs text-slate-400">Submission: 1 of 1 (No resubmit after deadline)</p>
          </div>

          <!-- Drag & Drop Dropzone -->
          <div class="p-6 bg-slate-950 rounded-2xl border-2 border-dashed border-purple-500/40 text-center space-y-2">
            <p class="text-xs font-bold text-slate-300">📁 Drag &amp; Drop files here (Max 50MB each)</p>
            <p class="text-[10px] text-slate-400">Supported extensions: .c .cpp .py .zip .pdf .docx</p>

            <div class="pt-2 space-y-2 text-xs">
              <div v-for="file in uploadedFiles" :key="file.name" class="p-2.5 bg-slate-900 rounded-xl border border-slate-800 flex items-center justify-between text-left">
                <span class="font-mono text-emerald-400 font-bold">✅ {{ file.name }} ({{ file.size }})</span>
                <button class="text-rose-400 hover:text-rose-300 text-xs">🗑️ Remove</button>
              </div>
            </div>
          </div>

          <!-- Cover Letter -->
          <div class="space-y-1.5 text-xs">
            <label class="font-bold text-slate-300">Cover Letter (Optional):</label>
            <textarea
              v-model="coverLetter"
              rows="2"
              class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white placeholder-slate-500"
            ></textarea>
          </div>

          <!-- Academic Integrity -->
          <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
            <input type="checkbox" v-model="academicConfirmed" class="text-purple-600 rounded" />
            <span>☑️ I confirm this is my own work (Academic Integrity)</span>
          </label>

          <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
            <button @click="showSubmitModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
              ❌ Cancel
            </button>
            <button @click="submitAssignment" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-black text-xs shadow-lg">
              📤 Final Submit
            </button>
          </div>
        </div>
      </div>

      <!-- GRADED ASSIGNMENT RESULT VIEW -->
      <div v-if="showGradedView" class="bg-slate-900 border border-purple-500/50 rounded-3xl p-6 md:p-8 shadow-2xl space-y-6">
        <div class="border-b border-slate-800 pb-4 flex flex-wrap items-center justify-between gap-3">
          <div>
            <h2 class="text-lg font-black text-white">📄 Assignment 1 – GRADED by Mr. Sophea</h2>
            <p class="text-xs text-slate-400">Submitted: 28/05/2025 14:30 (On Time ✅)  •  Graded: 29/05/2025 10:15</p>
          </div>

          <span class="px-4 py-2 rounded-2xl bg-emerald-500/20 text-emerald-300 font-extrabold text-base border border-emerald-500/40">
            Total Score: 85 / 100 (Grade: B+)
          </span>
        </div>

        <!-- Rubric Breakdown -->
        <div class="p-5 bg-slate-950 rounded-2xl border border-slate-800 space-y-3 text-xs">
          <h4 class="font-bold text-white uppercase tracking-wider">🏆 Rubric Breakdown:</h4>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-slate-300 font-medium">
            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
              <p class="text-[10px] text-slate-400 font-bold">Code Functionality</p>
              <p class="text-sm font-bold text-white">23/25 ⭐⭐⭐⭐</p>
            </div>
            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
              <p class="text-[10px] text-slate-400 font-bold">Code Quality</p>
              <p class="text-sm font-bold text-white">22/25 ⭐⭐⭐⭐</p>
            </div>
            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
              <p class="text-[10px] text-slate-400 font-bold">Documentation</p>
              <p class="text-sm font-bold text-white">22/25 ⭐⭐⭐⭐</p>
            </div>
            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
              <p class="text-[10px] text-slate-400 font-bold">Creativity</p>
              <p class="text-sm font-bold text-white">18/25 ⭐⭐⭐</p>
            </div>
          </div>
        </div>

        <!-- Teacher Feedback -->
        <div class="p-5 bg-indigo-950/40 border border-indigo-500/30 rounded-2xl space-y-2 text-xs">
          <h4 class="font-bold text-indigo-300 uppercase tracking-wider">👨‍🏫 Teacher Feedback (Mr. Sophea):</h4>
          <p class="text-slate-200 leading-relaxed italic">
            "ការងារល្អ! Code ដំណើរការ ៤ operation ។ ចំណុចខ្សោយ: Division by zero handler ត្រូវបានថ្លែងអំពី ប៉ុន្ដែ UX/output message អាចធ្វើឲ្យជាក់ស្ដែងជាងនេះ។ Continue the great work!"
          </p>
        </div>

        <div class="flex items-center justify-between pt-2">
          <button @click="showGradedView = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
            ← Back to Assignments
          </button>
          <button class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md">
            📥 Download Graded Rubric PDF
          </button>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
