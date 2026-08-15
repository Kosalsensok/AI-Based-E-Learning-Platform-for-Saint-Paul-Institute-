<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const currentSlide = ref(5)
const totalSlides = ref(18)
const isFlipping = ref(false)
const flipDirection = ref<'next' | 'prev'>('next')
const enableSound = ref(true)
const viewMode = ref<'book3d' | 'flat'>('book3d')
const autoPlay = ref(false)
let autoPlayInterval: number | null = null

// Speaker notes per slide
const speakerNotes = ref([
  "ចំណុចនេះសំខាន់ណាស់សម្រាប់ប្រឡង Post-Test! ត្រូវចាំភាពខុសគ្នារវាង While និង Do-While ព្រោះ Do-While រ៉ាន់យ៉ាងហោចណាស់ 1 ដងជានិច្ច...",
  "សម្រាប់ For Loop គេប្រើនៅពេលដែលដឹងចំនួនជុំច្បាស់លាស់។",
  "While loop ប្រើពេល condition អាស្រ័យលើ User Input...",
  "រំលឹកបន្ថែម៖ Do-while មាន semicolon (;) នៅចុងបញ្ចប់ក្រោយបញ្ជាក់ while(cond);",
  "ឧទាហរណ៍ជាក់ស្តែង សម្រាប់ការគណនាចរន្តពិន្ទុនិស្សិត និងការបញ្ជូនតម្លៃ Array...",
  "ការប្រើប្រាស់ Break និង Continue Statement ក្នុង Loop...",
  "Nested Loop សម្រាប់ការបង្កើត Matrix ឬ Table layout...",
  "ចៀសវាង Infinite Loop ដោយត្រូវប្រាកដថា Condition ផ្លាស់ប្តូរតម្លៃ!"
])

// Slide contents definition for rich book rendering
const slideData = computed(() => [
  {
    title: 'MODULE 1: INTRODUCTION TO ALGORITHMS',
    subtitle: 'មូលដ្ឋានគ្រឹះនៃ Computational Thinking',
    content: [
      { type: 'heading', text: 'តើអ្វីជា Algorithm?' },
      { type: 'bullet', text: 'សំណុំនៃដំណាក់កាលណែនាំ (Step-by-step instructions) ក្នុងការដោះស្រាយបញ្ហា' },
      { type: 'bullet', text: 'មានភាពច្បាស់លាស់ (Definite), ទទួលផលសម្រេច (Finite) និងមានប្រសិទ្ធភាព (Efficient)' },
      { type: 'code', text: 'Input Data ➔ Process (Algorithm) ➔ Output Result' }
    ]
  },
  {
    title: 'MODULE 2: CONTROL STRUCTURES',
    subtitle: 'លំហូរនៃការសម្រេចចិត្តក្នុងកម្មវិធី',
    content: [
      { type: 'heading', text: 'ប្រភេទនៃ Decision Making' },
      { type: 'bullet', text: 'If-Else Conditional Statements' },
      { type: 'bullet', text: 'Switch-Case Multiple Branches' },
      { type: 'bullet', text: 'Ternary Operator (condition ? expr1 : expr2)' }
    ]
  },
  {
    title: 'MODULE 3: LOOPS IN C PROGRAMMING',
    subtitle: 'ការរៀបចំលូបសម្រាប់ Repetitive Tasks',
    content: [
      { type: 'heading', text: 'ប្រភេទ Loop សំខាន់ៗទាំង ៣' },
      { type: 'bullet', text: 'For Loop → នៅពេលដែលដឹងចំនួនជុំច្បាស់លាស់ (Fixed Iterations)' },
      { type: 'bullet', text: 'While Loop → នៅពេលដែល Condition អាស្រ័យលើ User Input ឬ Dynamic State' },
      { type: 'bullet', text: 'Do-While Loop → ប្រតិបត្តិយ៉ាងហោចណាស់ ១ដងជានិច្ច មុនពេលពិនិត្យ Condition' }
    ]
  },
  {
    title: 'MODULE 4: FOR LOOP DEEP DIVE',
    subtitle: 'រចនាសម្ព័ន្ធ syntax នៃ For Loop',
    content: [
      { type: 'heading', text: 'Syntax Breakdown' },
      { type: 'code', text: 'for (initialization; condition; increment) {\n    // Code block to be executed\n}' },
      { type: 'bullet', text: 'Initialization: កំណត់តម្លៃចាប់ផ្តើម counter variable' },
      { type: 'bullet', text: 'Condition: ពិនិត្យលក្ខខណ្ឌមុននឹងរ៉ាន់ជុំនីមួយៗ' }
    ]
  },
  {
    title: 'MODULE 5: WHILE & DO-WHILE COMPARISON',
    subtitle: 'ប្រៀបធៀបលក្ខណៈពិសេសរវាង While និង Do-While',
    content: [
      { type: 'heading', text: 'ភាពខុសគ្នាសំខាន់ៗ' },
      { type: 'bullet', text: 'While Loop: Entry-controlled loop (ពិនិត្យ Condition មុន)' },
      { type: 'bullet', text: 'Do-While Loop: Exit-controlled loop (ពិនិត្យ Condition ក្រោយ)' },
      { type: 'code', text: 'do {\n    printf("Executes at least once!\\n");\n} while (x < 0);' }
    ]
  }
])

// Get current slide details
const activeSlideData = computed(() => {
  const idx = (currentSlide.value - 1) % slideData.value.length
  return slideData.value[idx]
})

const prevSlideData = computed(() => {
  const prevIdx = (currentSlide.value - 2 + slideData.value.length) % slideData.value.length
  return slideData.value[prevIdx]
})

// Web Audio API Page Flip sound synthesizer
const playFlipSound = () => {
  if (!enableSound.value) return
  try {
    const AudioCtx = window.AudioContext || (window as unknown as { webkitAudioContext: typeof AudioContext }).webkitAudioContext
    if (!AudioCtx) return
    const ctx = new AudioCtx()
    
    // Page paper rustle noise simulation
    const bufferSize = ctx.sampleRate * 0.15 // 150ms
    const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate)
    const data = buffer.getChannelData(0)
    for (let i = 0; i < bufferSize; i++) {
      data[i] = (Math.random() * 2 - 1) * Math.exp(-i / (bufferSize * 0.3))
    }
    
    const noise = ctx.createBufferSource()
    noise.buffer = buffer
    
    const filter = ctx.createBiquadFilter()
    filter.type = 'bandpass'
    filter.frequency.setValueAtTime(800, ctx.currentTime)
    filter.Q.setValueAtTime(1.5, ctx.currentTime)
    
    const gain = ctx.createGain()
    gain.gain.setValueAtTime(0.3, ctx.currentTime)
    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.14)
    
    noise.connect(filter)
    filter.connect(gain)
    gain.connect(ctx.destination)
    
    noise.start()
  } catch {
    // Ignore audio context autoplay restriction errors gracefully
  }
}

const triggerFlipAnimation = (direction: 'next' | 'prev', targetPage: number) => {
  if (isFlipping.value) return
  flipDirection.value = direction
  isFlipping.value = true
  playFlipSound()
  
  setTimeout(() => {
    currentSlide.value = targetPage
    setTimeout(() => {
      isFlipping.value = false
    }, 350)
  }, 350)
}

const nextSlide = () => {
  if (currentSlide.value < totalSlides.value) {
    triggerFlipAnimation('next', currentSlide.value + 1)
  }
}

const prevSlide = () => {
  if (currentSlide.value > 1) {
    triggerFlipAnimation('prev', currentSlide.value - 1)
  }
}

const goToSlide = (page: number) => {
  if (page === currentSlide.value || isFlipping.value) return
  const dir = page > currentSlide.value ? 'next' : 'prev'
  triggerFlipAnimation(dir, page)
}

const toggleAutoPlay = () => {
  autoPlay.value = !autoPlay.value
  if (autoPlay.value) {
    autoPlayInterval = window.setInterval(() => {
      if (currentSlide.value < totalSlides.value) {
        nextSlide()
      } else {
        goToSlide(1)
      }
    }, 4000)
  } else if (autoPlayInterval) {
    clearInterval(autoPlayInterval)
    autoPlayInterval = null
  }
}

const handleKeydown = (e: KeyboardEvent) => {
  if (e.key === 'ArrowRight' || e.key === 'Space') {
    e.preventDefault()
    nextSlide()
  }
  if (e.key === 'ArrowLeft') {
    e.preventDefault()
    prevSlide()
  }
}

onMounted(() => window.addEventListener('keydown', handleKeydown))
onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
  if (autoPlayInterval) clearInterval(autoPlayInterval)
})
</script>

<template>
  <StudentLayout title="Learning Content — 3D Book Slide Player">
    <div class="space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
          <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
            <span class="text-2xl">📖</span>
            <span class="bg-gradient-to-r from-blue-400 via-indigo-300 to-purple-400 bg-clip-text text-transparent">
              3D BOOK SLIDE PRESENTATION PLAYER
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            អានស្លាយបទបង្ហាញក្នុងទម្រង់ចលនាសៀវភៅ 3D Flip ជាមួយ Speaker Notes និង Sound Effects
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <!-- View Mode Toggle -->
          <div class="flex items-center bg-slate-900 border border-slate-800 p-1 rounded-xl text-xs font-bold">
            <button
              @click="viewMode = 'book3d'"
              :class="[
                viewMode === 'book3d' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-slate-200',
                'px-3 py-1 rounded-lg transition-all flex items-center gap-1.5'
              ]"
            >
              <span>📖</span>
              <span>3D Book Flip</span>
            </button>
            <button
              @click="viewMode = 'flat'"
              :class="[
                viewMode === 'flat' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-slate-200',
                'px-3 py-1 rounded-lg transition-all flex items-center gap-1.5'
              ]"
            >
              <span>🖥️</span>
              <span>Flat View</span>
            </button>
          </div>

          <!-- Sound Toggle -->
          <button
            @click="enableSound = !enableSound"
            :class="[
              enableSound ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-slate-800 text-slate-400 border-slate-700',
              'px-3 py-1.5 rounded-xl border text-xs font-bold flex items-center gap-1.5 transition-all'
            ]"
            title="Toggle Page Flip Sound"
          >
            <span>{{ enableSound ? '🔊' : '🔇' }}</span>
            <span>{{ enableSound ? 'Sound ON' : 'Mute' }}</span>
          </button>

          <!-- AutoPlay Button -->
          <button
            @click="toggleAutoPlay"
            :class="[
              autoPlay ? 'bg-amber-500/20 text-amber-300 border-amber-500/40 animate-pulse' : 'bg-slate-800 text-slate-300 border-slate-700',
              'px-3 py-1.5 rounded-xl border text-xs font-bold flex items-center gap-1.5 transition-all'
            ]"
          >
            <span>{{ autoPlay ? '⏸️' : '▶️' }}</span>
            <span>{{ autoPlay ? 'Auto Playing' : 'Auto Play' }}</span>
          </button>
        </div>
      </div>

      <!-- MAIN 3D BOOK & BLUEPRINT CONTAINER (Matching User Image Blueprint Grid & Book Curl) -->
      <div class="relative rounded-3xl p-4 sm:p-6 md:p-10 border border-blue-900/50 shadow-2xl overflow-hidden bg-[#070e24]">
        
        <!-- Subtle Blueprint Dot Grid Backdrop (Exact match to screenshot matrix) -->
        <div
          class="absolute inset-0 pointer-events-none opacity-40"
          style="background-image: radial-gradient(#3b82f6 1.2px, transparent 1.2px); background-size: 24px 24px;"
        ></div>

        <!-- Ambient Blue Glow -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Top Header Info inside Blueprint Container -->
        <div class="relative z-10 flex items-center justify-between pb-6 border-b border-blue-900/40 mb-6">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-600/20 border border-blue-500/40 flex items-center justify-center text-blue-400 font-bold text-sm shadow-inner">
              📚
            </div>
            <div>
              <h3 class="text-sm sm:text-base font-bold text-white tracking-wide uppercase">
                COURSE SLIDE DECK — CHAPTER {{ currentSlide }}
              </h3>
              <p class="text-[11px] text-blue-300/70 font-mono">
                Interactive 3D Page Turning Engine • Saint Paul Institute
              </p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-blue-950/80 border border-blue-500/40 text-blue-300 font-mono text-xs font-bold shadow-lg">
              Slide {{ currentSlide }} / {{ totalSlides }}
            </span>
          </div>
        </div>

        <!-- 3D BOOK DISPLAY STAGE -->
        <div class="relative z-10 my-4 flex justify-center items-center perspective-stage">
          
          <!-- 3D Perspective Wrapper -->
          <div
            :class="[
              viewMode === 'book3d' ? 'book-3d-wrapper' : 'flat-wrapper',
              'relative w-full max-w-4xl aspect-[16/10] sm:aspect-[16/9]'
            ]"
          >

            <!-- Book Spine Shadow Base -->
            <div class="absolute inset-0 rounded-3xl bg-slate-950/80 shadow-2xl border border-blue-800/40 pointer-events-none transform translate-y-3 scale-[0.98] blur-sm"></div>

            <!-- MAIN ACTIVE 3D BOOK SHEET -->
            <div
              :class="[
                'book-page-sheet relative w-full h-full rounded-2xl md:rounded-3xl border border-blue-500/30 overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.6)] flex flex-col justify-between p-6 sm:p-8 md:p-12 transition-transform duration-500',
                isFlipping && flipDirection === 'next' ? 'animate-flip-next' : '',
                isFlipping && flipDirection === 'prev' ? 'animate-flip-prev' : ''
              ]"
              style="background: linear-gradient(135deg, #0b1a3a 0%, #0d224e 50%, #08132e 100%);"
            >
              <!-- Center Spine Crease Line (Visual Book Fold) -->
              <div class="absolute top-0 bottom-0 left-1/2 w-8 -translate-x-1/2 pointer-events-none bg-gradient-to-r from-transparent via-black/25 to-transparent z-20"></div>

              <!-- Top Right Page Curl & Fold Shadow (Exact match to uploaded screenshot curve!) -->
              <div
                @click="nextSlide"
                class="absolute top-0 right-0 w-24 h-24 cursor-pointer group z-30 overflow-hidden rounded-tr-2xl"
                title="Click corner to flip page"
              >
                <!-- Page Curl Shadow -->
                <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-bl from-blue-400/20 via-blue-900/60 to-transparent rounded-bl-3xl shadow-xl transform group-hover:scale-110 group-hover:-translate-x-1 group-hover:translate-y-1 transition-all duration-300"></div>
                <!-- Page Curl Highlight Flip Lip -->
                <div class="absolute top-0 right-0 w-12 h-12 bg-blue-500/20 border-l border-b border-blue-400/40 rounded-bl-2xl transform rotate-6 group-hover:rotate-12 transition-transform"></div>
              </div>

              <!-- Slide Header Content -->
              <div class="border-b border-blue-800/50 pb-4 flex items-center justify-between relative z-10">
                <div>
                  <span class="text-[10px] font-bold tracking-widest text-blue-400 uppercase bg-blue-950/80 px-2.5 py-1 rounded-md border border-blue-800/60">
                    {{ activeSlideData.subtitle }}
                  </span>
                  <h2 class="text-base sm:text-xl md:text-2xl font-black text-white uppercase tracking-tight mt-2 flex items-center gap-2">
                    <span>📊</span>
                    <span>{{ activeSlideData.title }}</span>
                  </h2>
                </div>

                <div class="hidden sm:flex items-center gap-1 text-xs text-blue-300/80 font-mono bg-blue-950/60 px-3 py-1.5 rounded-xl border border-blue-900">
                  <span>📖 Page {{ currentSlide }}</span>
                </div>
              </div>

              <!-- Slide Content Render Area -->
              <div class="py-6 space-y-4 text-xs sm:text-sm md:text-base text-slate-200 relative z-10 overflow-y-auto max-h-[260px] sm:max-h-none custom-scrollbar">
                <template v-for="(item, idx) in activeSlideData.content" :key="idx">
                  
                  <h4 v-if="item.type === 'heading'" class="text-sm sm:text-base font-bold text-blue-300 border-l-4 border-blue-500 pl-3 py-0.5">
                    {{ item.text }}
                  </h4>

                  <div v-else-if="item.type === 'bullet'" class="p-3 sm:p-4 bg-blue-950/50 rounded-xl border border-blue-900/60 flex items-start gap-3 hover:border-blue-700/60 transition-colors">
                    <span class="text-blue-400 font-bold text-lg leading-none">•</span>
                    <span class="leading-relaxed">{{ item.text }}</span>
                  </div>

                  <div v-else-if="item.type === 'code'" class="p-4 bg-[#050b1a] rounded-xl border border-blue-900 font-mono text-xs text-emerald-400 shadow-inner overflow-x-auto">
                    <pre>{{ item.text }}</pre>
                  </div>

                </template>
              </div>

              <!-- Slide Bottom Controls inside Page -->
              <div class="pt-4 border-t border-blue-900/60 flex items-center justify-between relative z-10">
                <button
                  @click="prevSlide"
                  :disabled="currentSlide <= 1 || isFlipping"
                  :class="[
                    currentSlide <= 1 ? 'opacity-40 cursor-not-allowed bg-slate-900 text-slate-500' : 'bg-blue-950 hover:bg-blue-900 text-blue-200 border-blue-800',
                    'px-4 py-2 rounded-xl font-bold text-xs border transition-all flex items-center gap-2 shadow-md active:scale-95'
                  ]"
                >
                  <span>◀</span>
                  <span>Previous Page</span>
                </button>

                <div class="hidden md:flex items-center gap-2 text-xs text-blue-300/60 font-mono">
                  <span>⌨️ Navigate: Left / Right Arrow Key</span>
                </div>

                <button
                  @click="nextSlide"
                  :disabled="currentSlide >= totalSlides || isFlipping"
                  :class="[
                    currentSlide >= totalSlides ? 'opacity-40 cursor-not-allowed bg-slate-900 text-slate-500' : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white border-blue-400/30',
                    'px-5 py-2 rounded-xl font-bold text-xs border transition-all flex items-center gap-2 shadow-lg active:scale-95'
                  ]"
                >
                  <span>Next Page</span>
                  <span>▶</span>
                </button>
              </div>

            </div>

          </div>

        </div>

        <!-- THUMBNAILS DRAWER WITH 3D PAGE SELECT -->
        <div class="relative z-10 mt-8 space-y-3">
          <div class="flex items-center justify-between">
            <h4 class="text-xs font-bold text-blue-300 uppercase tracking-wider flex items-center gap-2">
              <span>🖼️</span>
              <span>SLIDE PAGE THUMBNAILS</span>
            </h4>
            <span class="text-[11px] text-blue-400/70">Click thumbnail to flip to page</span>
          </div>

          <div class="flex items-center gap-3 overflow-x-auto custom-scrollbar pb-2">
            <div
              v-for="i in totalSlides"
              :key="i"
              @click="goToSlide(i)"
              :class="[
                currentSlide === i
                  ? 'border-blue-400 bg-blue-600/30 text-white font-black scale-105 shadow-lg shadow-blue-500/20'
                  : 'border-blue-900/60 bg-blue-950/40 text-blue-300/70 hover:border-blue-700 hover:text-blue-200',
                'w-24 h-16 rounded-xl border shrink-0 flex flex-col items-center justify-center cursor-pointer transition-all text-xs relative overflow-hidden group'
              ]"
            >
              <div class="absolute top-1 left-1 w-2 h-2 rounded-full bg-blue-400/40 group-hover:bg-blue-400"></div>
              <span class="text-[10px] font-mono opacity-60 uppercase">Page</span>
              <span class="font-mono font-bold text-sm">[{{ i }}]</span>
            </div>
          </div>
        </div>

        <!-- TEACHER SPEAKER NOTES DRAWER -->
        <div class="relative z-10 mt-6 p-5 bg-gradient-to-r from-amber-500/10 via-blue-950/60 to-indigo-950/40 rounded-2xl border border-amber-500/30 space-y-2 shadow-xl">
          <h4 class="text-xs font-bold text-amber-300 uppercase tracking-wider flex items-center gap-2">
            <span>🗣️</span>
            <span>TEACHER SPEAKER NOTES & GUIDANCE</span>
          </h4>
          <p class="text-xs sm:text-sm text-slate-200 leading-relaxed italic">
            "{{ speakerNotes[(currentSlide - 1) % speakerNotes.length] }}"
          </p>
        </div>

        <!-- Bottom Action Bar -->
        <div class="relative z-10 mt-6 flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-blue-900/40">
          <div class="flex items-center gap-2">
            <button
              @click="goToSlide(1)"
              class="px-4 py-2 rounded-xl bg-blue-950 hover:bg-blue-900 text-blue-200 font-bold text-xs border border-blue-800/80 transition-all"
            >
              ⏮️ First Slide
            </button>
            <button
              @click="goToSlide(totalSlides)"
              class="px-4 py-2 rounded-xl bg-blue-950 hover:bg-blue-900 text-blue-200 font-bold text-xs border border-blue-800/80 transition-all"
            >
              ⏭️ Last Slide
            </button>
          </div>

          <div class="flex items-center gap-3">
            <button class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2">
              <span>⬇️</span>
              <span>Download PDF Deck</span>
            </button>
          </div>
        </div>

      </div>

    </div>
  </StudentLayout>
</template>

<style scoped>
/* 3D Perspective Stage Styling */
.perspective-stage {
  perspective: 1600px;
}

.book-3d-wrapper {
  transform-style: preserve-3d;
  transition: transform 0.4s ease-out;
}

.book-page-sheet {
  transform-style: preserve-3d;
  backface-visibility: hidden;
  box-shadow: inset -10px 0 30px rgba(0, 0, 0, 0.4), 0 20px 50px rgba(0, 0, 0, 0.6);
}

/* Page Flip 3D Keyframe Animations */
.animate-flip-next {
  animation: pageFlipNext 0.7s cubic-bezier(0.645, 0.045, 0.355, 1.000) forwards;
}

.animate-flip-prev {
  animation: pageFlipPrev 0.7s cubic-bezier(0.645, 0.045, 0.355, 1.000) forwards;
}

@keyframes pageFlipNext {
  0% {
    transform: rotateY(0deg) scale(1);
    transform-origin: left center;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
  }
  50% {
    transform: rotateY(-90deg) scale(0.96) skewY(-2deg);
    transform-origin: left center;
    box-shadow: -20px 30px 60px rgba(0, 0, 0, 0.8);
  }
  100% {
    transform: rotateY(0deg) scale(1);
    transform-origin: left center;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
  }
}

@keyframes pageFlipPrev {
  0% {
    transform: rotateY(0deg) scale(1);
    transform-origin: right center;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
  }
  50% {
    transform: rotateY(90deg) scale(0.96) skewY(2deg);
    transform-origin: right center;
    box-shadow: 20px 30px 60px rgba(0, 0, 0, 0.8);
  }
  100% {
    transform: rotateY(0deg) scale(1);
    transform-origin: right center;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
  }
}

/* Custom Scrollbar for slide content */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(15, 23, 42, 0.6);
  border-radius: 9999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(59, 130, 246, 0.4);
  border-radius: 9999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(59, 130, 246, 0.7);
}
</style>

