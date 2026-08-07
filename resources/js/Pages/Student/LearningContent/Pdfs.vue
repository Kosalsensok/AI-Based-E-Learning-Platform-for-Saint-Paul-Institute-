<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const currentPage = ref(3)
const totalPages = ref(12)
const zoomLevel = ref(100)
const readMode = ref<'day' | 'night' | 'sepia'>('night')
const scrollMode = ref<'continuous' | 'single'>('continuous')
const readingProgress = ref(25) // Page 3 of 12 = 25%

const studentWatermark = ref('Chan Dara - STU241001')

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    readingProgress.value = Math.round((currentPage.value / totalPages.value) * 100)
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    readingProgress.value = Math.round((currentPage.value / totalPages.value) * 100)
  }
}

const zoomIn = () => {
  if (zoomLevel.value < 200) zoomLevel.value += 15
}

const zoomOut = () => {
  if (zoomLevel.value > 50) zoomLevel.value -= 15
}
</script>

<template>
  <StudentLayout title="Learning Content — PDF Reader">
    <div class="space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
          <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
            <span>📄</span>
            <span>PDF DOCUMENT READER</span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            អានឯកសារមេរៀន E-books, Lecture Notes, Cheat Sheets — ការពារសិទ្ធិ Watermark
          </p>
        </div>

        <div class="flex items-center gap-2">
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold">
            Reading Progress: {{ readingProgress }}% {{ readingProgress === 100 ? '✅ Completed' : '' }}
          </span>
        </div>
      </div>

      <!-- EMBEDDED PDF READER TOOLBAR BAR (Matching Prompt Spec) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-xs font-bold text-white">
          <span>📄 Loops_Summary_Notes.pdf</span>
          <span class="text-slate-400 font-normal">(Page {{ currentPage }} of {{ totalPages }})</span>
        </div>

        <!-- Navigation Controls -->
        <div class="flex items-center gap-2 text-xs">
          <button @click="prevPage" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold border border-slate-700">
            ← Prev
          </button>
          <span class="px-3 py-1.5 rounded-xl bg-slate-900 text-indigo-300 font-bold border border-slate-700">
            {{ currentPage }} / {{ totalPages }}
          </span>
          <button @click="nextPage" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold border border-slate-700">
            Next →
          </button>

          <!-- Zoom Controls -->
          <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-700">
            <button @click="zoomOut" class="px-2 py-0.5 text-slate-300 hover:text-white font-bold">🔍 -</button>
            <span class="text-[10px] text-slate-400 font-mono">{{ zoomLevel }}%</span>
            <button @click="zoomIn" class="px-2 py-0.5 text-slate-300 hover:text-white font-bold">🔍 +</button>
          </div>

          <!-- Read Mode Switcher -->
          <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-700">
            <button @click="readMode = 'day'" :class="[readMode === 'day' ? 'bg-white text-slate-900 font-bold' : 'text-slate-400', 'px-2 py-0.5 text-[10px] rounded']">
              ☀️ Day
            </button>
            <button @click="readMode = 'night'" :class="[readMode === 'night' ? 'bg-slate-700 text-white font-bold' : 'text-slate-400', 'px-2 py-0.5 text-[10px] rounded']">
              🌙 Night
            </button>
            <button @click="readMode = 'sepia'" :class="[readMode === 'sepia' ? 'bg-amber-200 text-amber-900 font-bold' : 'text-slate-400', 'px-2 py-0.5 text-[10px] rounded']">
              📜 Sepia
            </button>
          </div>
        </div>
      </div>

      <!-- EMBEDDED PDF DOCUMENT VIEWER BOX (Matching Prompt Spec with Dynamic Watermark) -->
      <div class="relative bg-slate-950 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl p-6 md:p-10 flex flex-col items-center">
        
        <!-- Document Page Frame -->
        <div
          :class="[
            readMode === 'day' ? 'bg-white text-slate-900' :
            readMode === 'sepia' ? 'bg-amber-50 text-amber-950' : 'bg-slate-900 text-slate-100',
            'relative max-w-3xl w-full min-h-[600px] p-8 md:p-12 rounded-2xl shadow-2xl transition-all duration-300 border border-slate-700/50 space-y-6 overflow-hidden'
          ]"
          :style="{ transform: `scale(${zoomLevel / 100})`, transformOrigin: 'top center' }"
        >
          <!-- Dynamic Anti-Piracy Watermark Overlay (Matching Prompt Spec) -->
          <div class="absolute inset-0 pointer-events-none flex items-center justify-center rotate-[30deg] opacity-15 select-none z-10">
            <span class="text-3xl md:text-5xl font-black text-slate-400 tracking-widest uppercase border-4 border-slate-400 px-6 py-2 rounded-2xl">
              🔒 {{ studentWatermark }}
            </span>
          </div>

          <!-- Document Content Header -->
          <div class="border-b border-slate-700/40 pb-4 text-center space-y-1">
            <h2 class="text-xl font-black uppercase tracking-wider">CHAPTER 3: LOOPS IN C PROGRAMMING</h2>
            <p class="text-xs text-slate-400 font-mono">Module 3 Lecture Notes • Page {{ currentPage }} of {{ totalPages }}</p>
          </div>

          <!-- Document Content Body -->
          <div class="space-y-4 text-xs md:text-sm leading-relaxed">
            <h3 class="font-bold text-base">1. While Loop Syntax & Structure</h3>
            <p>
              In C programming, a <code class="px-2 py-0.5 rounded bg-slate-800 text-emerald-400 font-mono">while</code> loop enables execution of a block of code repeatedly as long as a given boolean condition evaluates to true.
            </p>

            <div class="p-4 rounded-xl bg-slate-950 text-emerald-400 font-mono text-xs border border-slate-800 space-y-1">
              <p>while (condition) {</p>
              <p>&nbsp;&nbsp;&nbsp;&nbsp;// statement to execute</p>
              <p>}</p>
            </div>

            <h3 class="font-bold text-base pt-2">2. Key Rules & Differences</h3>
            <ul class="list-disc pl-5 space-y-1.5">
              <li>Condition is evaluated before executing the loop body.</li>
              <li>If the condition is initially false, the loop body will never run.</li>
              <li>Always ensure the loop variable increments or terminates to avoid infinite loops.</li>
            </ul>
          </div>
        </div>

      </div>

      <!-- PDF OPTIONS & PROTECTIONS BAR -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-5 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3 text-xs text-slate-300">
          <span class="font-bold text-white">Options:</span>
          <span>Scroll Mode: Vertical Continuous</span>
          <span>•</span>
          <span class="text-emerald-400 font-bold">🔒 Watermark Security Active</span>
        </div>

        <div class="flex items-center gap-3">
          <button class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md">
            ⬇️ Download PDF
          </button>
          <button class="px-4 py-2 rounded-xl bg-slate-900 text-slate-400 text-xs font-bold border border-slate-700 cursor-not-allowed" title="Protected">
            🖨️ Print Protected 🔒
          </button>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
