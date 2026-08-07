<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
}>()

const emit = defineEmits(['close'])

const studentMode = ref<'instructor_led' | 'self_paced'>('instructor_led')
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-200 dark:border-gray-700 my-8 overflow-hidden transform transition-all">
      <!-- Modal Header -->
      <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-700 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="text-2xl">👨‍🎓</span>
          <div>
            <h3 class="font-bold text-lg leading-tight">
              MY LEARNING CALENDAR — Student View Preview
            </h3>
            <p class="text-xs text-emerald-100">ពិនិត្យមើលរូបរាង Calendar ដែលសិស្សឃើញនៅលើ Dashboard</p>
          </div>
        </div>
        <button @click="$emit('close')" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white font-bold transition">✕</button>
      </div>

      <!-- Mode Switcher Toggle -->
      <div class="px-6 py-3 bg-slate-100 dark:bg-gray-700/60 border-b border-slate-200 dark:border-gray-700 flex items-center justify-between text-xs">
        <span class="font-bold text-slate-700 dark:text-slate-200">តេស្តមើលតាមប្រភេទសិស្ស:</span>
        <div class="flex bg-white dark:bg-gray-800 p-1 rounded-xl border border-slate-200 dark:border-gray-700 font-bold">
          <button
            @click="studentMode = 'instructor_led'"
            :class="studentMode === 'instructor_led' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300'"
            class="px-3 py-1.5 rounded-lg transition"
          >
            📖 Instructor-Led Student
          </button>
          <button
            @click="studentMode = 'self_paced'"
            :class="studentMode === 'self_paced' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300'"
            class="px-3 py-1.5 rounded-lg transition"
          >
            ⚡ Self-Paced Student
          </button>
        </div>
      </div>

      <!-- Student View Interface Content -->
      <div class="p-6 space-y-5 text-xs text-slate-800 dark:text-slate-100">
        <!-- Mock Student Card -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-gray-700/50 border border-slate-200/80 dark:border-gray-700 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white font-bold flex items-center justify-center text-base">
              CD
            </div>
            <div>
              <h4 class="font-bold text-sm">Chan Dara</h4>
              <p class="text-[11px] text-slate-400">
                Mode: <span class="font-bold text-blue-600 dark:text-blue-400">{{ studentMode === 'instructor_led' ? 'Instructor-Led (Live + Deadlines)' : 'Self-Paced (Deadlines Only)' }}</span>
              </p>
            </div>
          </div>
          <span class="text-xs font-bold text-slate-500">Today: May 15, 2025</span>
        </div>

        <!-- Instructor-Led View -->
        <div v-if="studentMode === 'instructor_led'" class="space-y-4">
          <div class="p-4 rounded-2xl bg-blue-50/60 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-800 space-y-3">
            <h5 class="font-bold text-blue-900 dark:text-blue-200 flex items-center gap-2">
              <span>📅 TODAY'S SCHEDULE & DEADLINES</span>
            </h5>

            <!-- Live Class Row -->
            <div class="p-3.5 rounded-xl bg-white dark:bg-gray-800 border border-blue-200 dark:border-gray-700 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
              <div class="flex items-center gap-3">
                <span class="w-3 h-3 rounded-full bg-blue-600 animate-ping"></span>
                <div>
                  <div class="font-bold text-blue-800 dark:text-blue-300">🟦 02:00 PM Live: C Functions</div>
                  <div class="text-[11px] text-slate-500">Instructor: Mr. Sophea · Zoom Link</div>
                </div>
              </div>

              <button class="px-4 py-2 bg-blue-600 text-white font-bold rounded-xl shadow-md hover:bg-blue-700 transition">
                Join Now →
              </button>
            </div>

            <!-- Assignment Row with Urgency Timer -->
            <div class="p-3.5 rounded-xl bg-white dark:bg-gray-800 border border-orange-200 dark:border-gray-700 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
              <div class="flex items-center gap-3">
                <span class="text-lg">🟧</span>
                <div>
                  <div class="font-bold text-slate-800 dark:text-white">11:59 PM Due: Assignment First C Program</div>
                  <div class="text-[11px] text-slate-500">Course: C Programming Basics</div>
                </div>
              </div>

              <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-[11px] font-black bg-red-100 text-red-700 border border-red-200 animate-pulse">
                  ⏳ 6 hours left
                </span>

                <button class="px-4 py-2 bg-orange-600 text-white font-bold rounded-xl shadow-md hover:bg-orange-700 transition">
                  Submit →
                </button>
              </div>
            </div>
          </div>

          <!-- Upcoming Week -->
          <div class="space-y-2">
            <h5 class="font-bold text-slate-700 dark:text-slate-200">THIS WEEK DEADLINES & EVENTS</h5>
            <div class="p-3 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-800 space-y-1.5">
              <div class="flex items-center justify-between font-bold text-red-600">
                <span>🟥 May 16: Post-Test Module 1 Due</span>
                <span>11:59 PM</span>
              </div>
              <div class="flex items-center justify-between font-bold text-yellow-600">
                <span>🟨 May 20: ABA Payment for Database Systems</span>
                <span>11:59 PM</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Self-Paced View -->
        <div v-else class="space-y-4">
          <div class="p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800 space-y-3">
            <div class="flex items-center justify-between font-bold text-amber-900 dark:text-amber-200">
              <span>⚡ SELF-PACED DEADLINES ONLY</span>
              <span class="text-[11px] text-slate-400">(មិនមាន Live Class ទេ)</span>
            </div>

            <div class="p-3.5 rounded-xl bg-white dark:bg-gray-800 border border-red-200 dark:border-gray-700 shadow-sm flex items-center justify-between">
              <div>
                <div class="font-bold text-red-600">🟥 May 16: Post-Test Module 1 Due</div>
                <div class="text-[11px] text-slate-500">Auto-lock after deadline</div>
              </div>
              <button class="px-3.5 py-1.5 bg-red-600 text-white font-bold rounded-xl">
                Start Test
              </button>
            </div>

            <div class="p-3.5 rounded-xl bg-white dark:bg-gray-800 border border-yellow-200 dark:border-gray-700 shadow-sm flex items-center justify-between">
              <div>
                <div class="font-bold text-yellow-700">🟨 May 20: ABA Payment for Database Systems</div>
                <div class="text-[11px] text-slate-500">Course continuation fee</div>
              </div>
              <button class="px-3.5 py-1.5 bg-yellow-600 text-white font-bold rounded-xl">
                Pay Now
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-6 py-4 bg-slate-100 dark:bg-gray-700/80 border-t border-slate-200 dark:border-gray-700 flex justify-end">
        <button @click="$emit('close')" class="px-5 py-2.5 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-900 transition">
          Close Preview
        </button>
      </div>
    </div>
  </div>
</template>
