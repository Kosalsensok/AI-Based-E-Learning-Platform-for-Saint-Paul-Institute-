<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const currentPage = ref(3)
const totalPages = ref(12)
const zoomLevel = ref(100)
const readMode = ref<'day' | 'night' | 'sepia'>('night')
const readingProgress = ref(25) // Page 3 of 12 = 25%
const isFlipping = ref(false)
const flipDirection = ref<'next' | 'prev'>('next')

const studentWatermark = ref('Chan Dara - STU241001')

const triggerFlip = (dir: 'next' | 'prev', targetPage: number) => {
  if (isFlipping.value) return
  flipDirection.value = dir
  isFlipping.value = true
  
  setTimeout(() => {
    currentPage.value = targetPage
    readingProgress.value = Math.round((currentPage.value / totalPages.value) * 100)
    setTimeout(() => {
      isFlipping.value = false
    }, 350)
  }, 350)
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    triggerFlip('next', currentPage.value + 1)
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
    triggerFlip('prev', currentPage.value - 1)
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
  <StudentLayout title="Learning Content — 3D Book PDF Reader">
    <div class="space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
          <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
            <span>📖</span>
            <span class="bg-gradient-to-r from-blue-400 via-indigo-300 to-purple-400 bg-clip-text text-transparent">
              3D BOOK PDF DOCUMENT READER
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            អានឯកសារមេរៀន E-books, Lecture Notes, Cheat Sheets ក្នុងទម្រង់ 3D Book Flip ជាមួយ Watermark
          </p>
        </div>

        <div class="flex items-center gap-2">
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold">
            Progress: {{ readingProgress }}% {{ readingProgress === 100 ? '✅ Completed' : '' }}
          </span>
        </div>
      </div>

      <!-- EMBEDDED PDF READER TOOLBAR BAR -->
      <div class="bg-slate-900 border border-blue-900/60 rounded-2xl p-4 shadow-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-xs font-bold text-white">
          <span>📄 Loops_Summary_Notes.pdf</span>
          <span class="text-blue-300 font-normal">(Page {{ currentPage }} of {{ totalPages }})</span>
        </div>

        <!-- Navigation Controls -->
        <div class="flex items-center gap-2 text-xs">
          <button
            @click="prevPage"
            :disabled="currentPage <= 1 || isFlipping"
            class="px-3 py-1.5 rounded-xl bg-blue-950 hover:bg-blue-900 disabled:opacity-40 text-blue-200 font-bold border border-blue-800 transition-all"
          >
            ← Prev
          </button>
          <span class="px-3 py-1.5 rounded-xl bg-blue-950 text-blue-300 font-bold border border-blue-800">
            {{ currentPage }} / {{ totalPages }}
          </span>
          <button
            @click="nextPage"
            :disabled="currentPage >= totalPages || isFlipping"
            class="px-3 py-1.5 rounded-xl bg-blue-950 hover:bg-blue-900 disabled:opacity-40 text-blue-200 font-bold border border-blue-800 transition-all"
          >
            Next →
          </button>

          <!-- Zoom Controls -->
          <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-xl border border-blue-900">
            <button @click="zoomOut" class="px-2 py-0.5 text-blue-300 hover:text-white font-bold">🔍 -</button>
            <span class="text-[10px] text-blue-300 font-mono">{{ zoomLevel }}%</span>
            <button @click="zoomIn" class="px-2 py-0.5 text-blue-300 hover:text-white font-bold">🔍 +</button>
          </div>

          <!-- Read Mode Switcher -->
          <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-xl border border-blue-900">
            <button @click="readMode = 'day'" :class="[readMode === 'day' ? 'bg-white text-slate-900 font-bold' : 'text-slate-400', 'px-2 py-0.5 text-[10px] rounded']">
              ☀️ Day
            </button>
            <button @click="readMode = 'night'" :class="[readMode === 'night' ? 'bg-blue-900 text-white font-bold' : 'text-slate-400', 'px-2 py-0.5 text-[10px] rounded']">
              🌙 Night
            </button>
            <button @click="readMode = 'sepia'" :class="[readMode === 'sepia' ? 'bg-amber-200 text-amber-900 font-bold' : 'text-slate-400', 'px-2 py-0.5 text-[10px] rounded']">
              📜 Sepia
            </button>
          </div>
        </div>
      </div>

      <!-- EMBEDDED PDF DOCUMENT VIEWER BOX (3D BOOK CONTAINER WITH BLUEPRINT GRID) -->
      <div class="relative bg-[#070e24] border border-blue-900/60 rounded-3xl overflow-hidden shadow-2xl p-6 md:p-10 flex flex-col items-center">
        
        <!-- Blueprint Dot Grid Matrix Backdrop -->
        <div
          class="absolute inset-0 pointer-events-none opacity-40"
          style="background-image: radial-gradient(#3b82f6 1.2px, transparent 1.2px); background-size: 24px 24px;"
        ></div>

        <!-- 3D Document Page Frame -->
        <div class="perspective-stage w-full max-w-3xl flex justify-center py-4 relative z-10">
          <div
            :class="[
              readMode === 'day' ? 'bg-slate-50 text-slate-900' :
              readMode === 'sepia' ? 'bg-[#fbf0d9] text-amber-950' : 'bg-[#0d224e] text-slate-100 border-blue-500/30',
              isFlipping && flipDirection === 'next' ? 'animate-pdf-flip-next' : '',
              isFlipping && flipDirection === 'prev' ? 'animate-pdf-flip-prev' : '',
              'book-page-sheet relative w-full min-h-[580px] p-8 md:p-12 rounded-2xl md:rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.6)] transition-transform duration-500 border space-y-6 overflow-hidden'
            ]"
            :style="{ transform: `scale(${zoomLevel / 100})`, transformOrigin: 'top center' }"
          >
            <!-- Top Right Page Peel Corner (Matching User Image) -->
            <div
              @click="nextPage"
              class="absolute top-0 right-0 w-24 h-24 cursor-pointer group z-30 overflow-hidden rounded-tr-2xl"
              title="Click corner to flip page"
            >
              <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-bl from-blue-400/30 via-blue-900/60 to-transparent rounded-bl-3xl shadow-xl transform group-hover:scale-110 group-hover:-translate-x-1 group-hover:translate-y-1 transition-all duration-300"></div>
              <div class="absolute top-0 right-0 w-12 h-12 bg-blue-500/20 border-l border-b border-blue-400/40 rounded-bl-2xl transform rotate-6 group-hover:rotate-12 transition-transform"></div>
            </div>

            <!-- Dynamic Watermark -->
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center rotate-[30deg] opacity-10 select-none z-10">
              <span class="text-3xl md:text-5xl font-black text-blue-300 tracking-widest uppercase border-4 border-blue-400 px-6 py-2 rounded-2xl">
                🔒 {{ studentWatermark }}
              </span>
            </div>

            <!-- Document Content Header -->
            <div class="border-b border-blue-800/40 pb-4 text-center space-y-1 relative z-20">
              <h2 class="text-lg md:text-xl font-black uppercase tracking-wider">CHAPTER {{ currentPage }}: LOOPS & FUNCTIONS IN C</h2>
              <p class="text-xs opacity-75 font-mono">Module {{ currentPage }} Lecture Notes • Page {{ currentPage }} of {{ totalPages }}</p>
            </div>

            <!-- Document Content Body -->
            <div class="space-y-4 text-xs md:text-sm leading-relaxed relative z-20">
              <h3 class="font-bold text-base text-blue-300">1. While Loop Syntax & Structure</h3>
              <p>
                In C programming, a <code class="px-2 py-0.5 rounded bg-blue-950 text-emerald-400 font-mono border border-blue-900">while</code> loop enables execution of a block of code repeatedly as long as a given boolean condition evaluates to true.
              </p>

              <div class="p-4 rounded-xl bg-slate-950 text-emerald-400 font-mono text-xs border border-blue-900 space-y-1 shadow-inner">
                <p>while (condition) {</p>
                <p>&nbsp;&nbsp;&nbsp;&nbsp;// statement to execute repeatedly</p>
                <p>}</p>
              </div>

              <h3 class="font-bold text-base pt-2 text-blue-300">2. Key Rules & Differences</h3>
              <ul class="list-disc pl-5 space-y-2">
                <li>Condition is evaluated before executing the loop body.</li>
                <li>If condition is initially false, the loop body will never execute.</li>
                <li>Always ensure the counter variable increments to prevent infinite execution loops.</li>
              </ul>
            </div>

            <!-- Page Bottom Footer Controls -->
            <div class="pt-4 border-t border-blue-800/40 flex items-center justify-between relative z-20 text-xs font-mono opacity-80">
              <span>Page {{ currentPage }} of {{ totalPages }}</span>
              <div class="flex items-center gap-2">
                <button @click="prevPage" class="hover:underline">◀ Prev Page</button>
                <span>|</span>
                <button @click="nextPage" class="hover:underline">Next Page ▶</button>
              </div>
            </div>

          </div>
        </div>

      </div>

      <!-- PDF OPTIONS & PROTECTIONS BAR -->
      <div class="bg-slate-900 border border-blue-900/60 rounded-3xl p-5 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3 text-xs text-slate-300">
          <span class="font-bold text-white">Options:</span>
          <span>View Mode: 3D Book Page Flip</span>
          <span>•</span>
          <span class="text-emerald-400 font-bold">🔒 Dynamic Watermark Active</span>
        </div>

        <div class="flex items-center gap-3">
          <button class="px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-md">
            ⬇️ Download PDF
          </button>
          <button class="px-4 py-2 rounded-xl bg-slate-950 text-slate-500 text-xs font-bold border border-slate-800 cursor-not-allowed" title="Protected">
            🖨️ Print Protected 🔒
          </button>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>

<style scoped>
.perspective-stage {
  perspective: 1600px;
}

.book-page-sheet {
  transform-style: preserve-3d;
  backface-visibility: hidden;
}

.animate-pdf-flip-next {
  animation: pdfFlipNext 0.7s cubic-bezier(0.645, 0.045, 0.355, 1.000) forwards;
}

.animate-pdf-flip-prev {
  animation: pdfFlipPrev 0.7s cubic-bezier(0.645, 0.045, 0.355, 1.000) forwards;
}

@keyframes pdfFlipNext {
  0% {
    transform: rotateY(0deg) scale(1);
    transform-origin: left center;
  }
  50% {
    transform: rotateY(-90deg) scale(0.95);
    transform-origin: left center;
  }
  100% {
    transform: rotateY(0deg) scale(1);
    transform-origin: left center;
  }
}

@keyframes pdfFlipPrev {
  0% {
    transform: rotateY(0deg) scale(1);
    transform-origin: right center;
  }
  50% {
    transform: rotateY(90deg) scale(0.95);
    transform-origin: right center;
  }
  100% {
    transform: rotateY(0deg) scale(1);
    transform-origin: right center;
  }
}
</style>

