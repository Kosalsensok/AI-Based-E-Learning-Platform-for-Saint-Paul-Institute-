<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const isFocusMode = ref(true)
const isPlaying = ref(false)
const currentTime = ref('08:42')
const duration = ref('20:10')
const ccMode = ref<'off' | 'kh' | 'en' | 'both'>('kh')
const quality = ref('1080p')
const speed = ref('1.25x')
const currentProgress = ref(72)

const rightTab = ref<'notes' | 'downloads' | 'qa' | 'links'>('notes')

const newNoteText = ref('')
const notes = ref([
  { time: '08:42', text: 'អថេរ i នឹងត្រូវកើនឡើង ១ ជានិច្ច រហូតដល់លក្ខខណ្ឌ i <= 10 លែងពិត', saved: true },
  { time: '12:30', text: 'ប្រយ័ត្ន Infinite Loop ត្រូវតែមាន increment i++', saved: true }
])

const attachments = ref([
  { name: 'Loop_Note.pdf', size: '1.2 MB', type: 'pdf' },
  { name: 'example.c', size: '12 KB', type: 'code' }
])

const timestamps = ref([
  { time: '00:00', title: 'ការណែនាំ Syntax នៃ While Loop' },
  { time: '05:15', title: 'ឧទាហរណ៍ជាក់ស្តែង 1 ដល់ 10' },
  { time: '12:30', title: 'ហានិភ័យនៃ Infinite Loop' }
])

const addNote = () => {
  if (newNoteText.value.trim()) {
    notes.value.unshift({
      time: currentTime.value,
      text: newNoteText.value.trim(),
      saved: true
    })
    newNoteText.value = ''
  }
}
</script>

<template>
  <StudentLayout title="Learning Content — Video Player">
    <div class="space-y-6">
      
      <!-- Top Focus Mode Bar -->
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800 rounded-3xl p-4 md:p-5 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-bold">
            🎓 E-LMS FOCUS MODE
          </span>
          <h1 class="text-base font-extrabold text-white truncate">
            📚 C Programming Basics
          </h1>
        </div>

        <div class="flex items-center gap-4">
          <!-- Overall Progress Bar -->
          <div class="flex items-center gap-2 text-xs font-bold">
            <span class="text-slate-400">Course Progress:</span>
            <div class="w-32 h-2 rounded-full bg-slate-800 overflow-hidden border border-slate-700">
              <div class="h-full bg-indigo-500 rounded-full" :style="{ width: currentProgress + '%' }"></div>
            </div>
            <span class="text-indigo-400">{{ currentProgress }}%</span>
          </div>

          <Link
            href="/student/dashboard"
            class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all"
          >
            ✕ Exit Focus
          </Link>
        </div>
      </div>

      <!-- 3-PANEL FOCUS MODE LAYOUT -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT PANEL: CURRICULUM TREE (3 Cols) -->
        <div class="lg:col-span-3 bg-slate-800/90 border border-slate-700/80 rounded-3xl p-4 shadow-xl space-y-3">
          <h3 class="text-xs font-bold text-white uppercase tracking-wider border-b border-slate-700/60 pb-2">
            📦 Curriculum Tree
          </h3>

          <div class="space-y-2 text-xs max-h-[650px] overflow-y-auto custom-scrollbar pr-1">
            <!-- Module 1 -->
            <div class="p-2 bg-slate-900/60 rounded-xl border border-slate-700/40 space-y-1">
              <p class="font-bold text-slate-300">📦 Module 1: Intro</p>
              <div class="pl-3 space-y-1 text-slate-400">
                <p class="text-emerald-400">✅ Ch 1.1 🎬 History</p>
                <p class="text-emerald-400">✅ Ch 1.2 📄 Setup</p>
              </div>
            </div>

            <!-- Module 2 -->
            <div class="p-2 bg-slate-900/60 rounded-xl border border-slate-700/40 space-y-1">
              <p class="font-bold text-slate-300">📦 Module 2: Variables</p>
              <div class="pl-3 space-y-1 text-slate-400">
                <p class="text-emerald-400">✅ Ch 2.1 🎬 Types</p>
                <p class="text-amber-400 font-bold">🟡 Ch 2.2 📊 Slide</p>
              </div>
            </div>

            <!-- Module 3 (Active) -->
            <div class="p-2.5 bg-indigo-900/30 rounded-xl border border-indigo-500/40 space-y-1">
              <p class="font-bold text-indigo-300">▶ Module 3: Loops</p>
              <div class="pl-3 space-y-1.5">
                <p class="text-emerald-400">✅ Ch 3.1 🎬 For Loop</p>
                <p class="text-white font-black bg-indigo-600/30 px-2 py-1 rounded-lg border border-indigo-500/50 flex items-center justify-between">
                  <span>◀ Ch 3.2 🎬 While Loop</span>
                  <span class="w-2 h-2 rounded-full bg-indigo-400 animate-ping"></span>
                </p>
              </div>
            </div>

            <!-- Module 4 -->
            <div class="p-2 bg-slate-900/60 rounded-xl border border-slate-700/40 text-slate-500">
              <p class="font-bold">🔒 Module 4: Functions</p>
            </div>
          </div>
        </div>

        <!-- CENTER PANEL: MAIN VIDEO PLAYER (6 Cols) -->
        <div class="lg:col-span-6 space-y-4">
          <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-md flex items-center justify-between">
            <div>
              <h2 class="text-sm font-bold text-white">🎬 Lesson 3.2: While Loop in C</h2>
              <p class="text-[10px] text-slate-400">👨‍🏫 Mr. Sophea • ⏱️ 20:10 min</p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold border border-emerald-500/30">
              Auto-Resume @ {{ currentTime }}
            </span>
          </div>

          <!-- SMART VIDEO PLAYER FRAME -->
          <div class="relative bg-slate-950 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl">
            <div class="relative aspect-video bg-slate-900 flex items-center justify-center">
              <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1000&auto=format&fit=crop&q=80" class="w-full h-full object-cover opacity-60" />
              
              <!-- Code overlay mockup inside video -->
              <div class="absolute top-6 left-6 p-4 rounded-xl bg-slate-950/80 backdrop-blur-md border border-slate-800 text-xs font-mono text-emerald-400">
                while (i &lt;= 10) {<br />
                &nbsp;&nbsp;printf("%d", i);<br />
                &nbsp;&nbsp;i++;<br />
                }
              </div>

              <!-- DUAL-LANGUAGE SUBTITLE OVERLAY BAR (Matching Prompt Specs) -->
              <div v-if="ccMode !== 'off'" class="absolute bottom-16 inset-x-6 p-3 rounded-2xl bg-slate-950/90 backdrop-blur-md border border-slate-700/80 text-center space-y-1 shadow-2xl">
                <p v-if="ccMode === 'kh' || ccMode === 'both'" class="text-xs font-bold text-amber-300">
                  💬 KH: អថេរ i នឹងត្រូវកើនឡើង ១ ជានិច្ច រហូតដល់លក្ខខណ្ឌ i &lt;= 10 លែងពិត...
                </p>
                <p v-if="ccMode === 'en' || ccMode === 'both'" class="text-[11px] font-medium text-cyan-300">
                  💬 EN: The variable i will increment by 1 until the condition i &lt;= 10 becomes false...
                </p>
              </div>

              <!-- PLAY / PAUSE BUTTON OVERLAY -->
              <button
                @click="isPlaying = !isPlaying"
                class="absolute inset-0 m-auto w-14 h-14 rounded-full bg-indigo-600/90 hover:bg-indigo-500 text-white flex items-center justify-center shadow-2xl transition-all hover:scale-110"
              >
                <span class="text-xl">{{ isPlaying ? '⏸' : '▶' }}</span>
              </button>
            </div>

            <!-- CONTROLS & SHORTCUTS BAR (Matching Prompt Specs) -->
            <div class="p-3 bg-slate-950 border-t border-slate-800 space-y-2 text-xs">
              <div class="w-full h-1.5 rounded-full bg-slate-800 cursor-pointer overflow-hidden">
                <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full w-[43%]"></div>
              </div>

              <div class="flex items-center justify-between text-slate-300">
                <div class="flex items-center gap-3">
                  <button @click="isPlaying = !isPlaying" class="hover:text-indigo-400 font-bold">
                    {{ isPlaying ? '⏸ Pause' : '▶ Play' }}
                  </button>
                  <span class="font-mono text-[11px] text-slate-400">{{ currentTime }} / {{ duration }}</span>
                </div>

                <div class="flex items-center gap-2">
                  <!-- CC Select -->
                  <select v-model="ccMode" class="bg-slate-900 text-[11px] text-slate-300 rounded-lg border border-slate-700 px-2 py-1">
                    <option value="off">CC: Off</option>
                    <option value="kh">CC 🇰🇭 Khmer</option>
                    <option value="en">CC 🇬🇧 English</option>
                    <option value="both">CC 🇰🇭+🇬🇧 Both</option>
                  </select>

                  <!-- Quality Select -->
                  <select v-model="quality" class="bg-slate-900 text-[11px] text-slate-300 rounded-lg border border-slate-700 px-2 py-1">
                    <option value="auto">Auto (HLS)</option>
                    <option value="1080p">1080p</option>
                    <option value="720p">720p</option>
                    <option value="480p">480p</option>
                  </select>

                  <!-- Speed Select -->
                  <select v-model="speed" class="bg-slate-900 text-[11px] text-slate-300 rounded-lg border border-slate-700 px-2 py-1">
                    <option value="0.75x">0.75x</option>
                    <option value="1.0x">1.0x</option>
                    <option value="1.25x">1.25x</option>
                    <option value="1.5x">1.5x</option>
                    <option value="2.0x">2.0x</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Lesson Navigation Bar -->
          <div class="flex items-center justify-between">
            <button class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700">
              ← Previous Lesson
            </button>
            <span class="text-[10px] text-slate-400 font-semibold">Watched 85% = ✅ Marked Completed</span>
            <button class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md">
              Next Lesson →
            </button>
          </div>

          <!-- INTERACTIVE TIMESTAMPS BAR (Matching Prompt Specs) -->
          <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-md space-y-2">
            <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
              <span>📌</span>
              <span>INTERACTIVE TIMESTAMPS</span>
            </h4>
            <div class="space-y-1.5 text-xs">
              <div
                v-for="ts in timestamps"
                :key="ts.time"
                @click="currentTime = ts.time"
                class="p-2 rounded-xl bg-slate-900/60 hover:bg-slate-700/50 border border-slate-700/40 flex items-center justify-between cursor-pointer transition-colors"
              >
                <span class="text-slate-200">{{ ts.title }}</span>
                <span class="px-2 py-0.5 rounded bg-indigo-600/30 text-indigo-300 font-mono text-[10px] font-bold border border-indigo-500/30">
                  {{ ts.time }}
                </span>
              </div>
            </div>
          </div>

        </div>

        <!-- RIGHT PANEL: STUDENT TOOLS & NOTES (3 Cols) -->
        <div class="lg:col-span-3 bg-slate-800/90 border border-slate-700/80 rounded-3xl p-4 shadow-xl space-y-4">
          
          <!-- Tool Tabs -->
          <div class="grid grid-cols-2 gap-1 bg-slate-900 p-1 rounded-2xl border border-slate-700">
            <button
              @click="rightTab = 'notes'"
              :class="[rightTab === 'notes' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400', 'py-1.5 text-[11px] rounded-xl transition-all']"
            >
              📝 My Notes
            </button>
            <button
              @click="rightTab = 'downloads'"
              :class="[rightTab === 'downloads' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400', 'py-1.5 text-[11px] rounded-xl transition-all']"
            >
              📥 Downloads
            </button>
          </div>

          <!-- TAB CONTENT: MY NOTES WITH TIMESTAMP LINK -->
          <div v-if="rightTab === 'notes'" class="space-y-3">
            <div class="space-y-2">
              <label class="text-[10px] text-slate-400 font-bold uppercase">Take Note at {{ currentTime }}:</label>
              <textarea
                v-model="newNoteText"
                rows="3"
                placeholder="Write your note here..."
                class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
              ></textarea>
              <button @click="addNote" class="w-full py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md">
                💾 Auto-Save Note
              </button>
            </div>

            <div class="space-y-2 pt-2 border-t border-slate-700/60 max-h-[350px] overflow-y-auto custom-scrollbar">
              <div v-for="n in notes" :key="n.time" class="p-2.5 bg-slate-900/80 rounded-xl border border-slate-700/60 space-y-1 text-xs">
                <div class="flex items-center justify-between text-[10px] text-slate-400">
                  <span class="px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-mono font-bold">{{ n.time }}</span>
                  <span class="text-emerald-400">💾 Cloud Synced</span>
                </div>
                <p class="text-slate-200 text-xs">{{ n.text }}</p>
              </div>
            </div>
          </div>

          <!-- TAB CONTENT: ATTACHMENTS & RESOURCES -->
          <div v-else class="space-y-3">
            <h4 class="text-xs font-bold text-white uppercase tracking-wider">📥 Lesson Attachments</h4>
            <div class="space-y-2 text-xs">
              <div v-for="att in attachments" :key="att.name" class="p-2.5 bg-slate-900/80 rounded-xl border border-slate-700/60 flex items-center justify-between">
                <div>
                  <p class="font-bold text-white text-xs">{{ att.name }}</p>
                  <p class="text-[10px] text-slate-400">{{ att.size }}</p>
                </div>
                <button class="px-2.5 py-1 rounded-lg bg-indigo-600 text-white text-[10px] font-bold">
                  Download
                </button>
              </div>
            </div>
          </div>

        </div>

      </div>

    </div>
  </StudentLayout>
</template>
