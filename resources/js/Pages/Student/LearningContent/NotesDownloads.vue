<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const newNoteText = ref('')
const newNoteTime = ref('08:42')

const myNotes = ref([
  {
    id: 1,
    time: '08:42',
    text: 'While loop ចាំបាច់ត្រូវមាន i++ បើមិនចឹងវានឹងក្លាយជា Infinite loop!'
  },
  {
    id: 2,
    time: '14:20',
    text: 'Syntax នៃ Do-While ត្រូវមាន semicolon (;) នៅចុងបញ្ចប់កូដ!'
  }
])

const attachments = ref([
  {
    id: 101,
    name: 'example_loop.c',
    category: 'Source Code',
    icon: '📕',
    size: '12 KB',
    downloads: 120,
    canPreview: true
  },
  {
    id: 102,
    name: 'Project_Files.zip',
    category: 'Exercise Pack',
    icon: '🗜️',
    size: '4.5 MB',
    downloads: 89,
    canPreview: false
  },
  {
    id: 103,
    name: 'Lecture_Summary.docx',
    category: 'Lecture Summary',
    icon: '📘',
    size: '45 KB',
    downloads: 210,
    canPreview: false
  }
])

const addNote = () => {
  if (newNoteText.value.trim()) {
    myNotes.value.unshift({
      id: Date.now(),
      time: newNoteTime.value,
      text: newNoteText.value.trim()
    })
    newNoteText.value = ''
  }
}

const removeNote = (id: number) => {
  myNotes.value = myNotes.value.filter(n => n.id !== id)
}
</script>

<template>
  <StudentLayout title="Notes & Downloads">
    <div class="space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
          <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
            <span>📝</span>
            <span>PERSONAL NOTES & RESOURCE DOWNLOADS</span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            កត់ត្រាផ្ទាល់ខ្លួន ភ្ជាប់ជាមួយនាទីវីដេអូ និងទាញយកឯកសារបន្ថែមស្កែនសុវត្ថិភាព
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md">
            📄 Export My Notes (PDF)
          </button>
        </div>
      </div>

      <!-- TWO COLUMNS LAYOUT (Matching Prompt Specs) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- LEFT COLUMN: MY PERSONAL NOTES -->
        <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
          <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
              <span>📝</span>
              <span>MY PERSONAL NOTES (Timestamped)</span>
            </h3>
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold border border-emerald-500/30">
              💾 Auto-Saved to Cloud
            </span>
          </div>

          <!-- Add Note Form -->
          <div class="p-4 bg-slate-900/80 rounded-2xl border border-slate-700/60 space-y-3">
            <div class="flex items-center justify-between text-xs">
              <span class="font-bold text-slate-300">✍️ Add Note at Timestamp:</span>
              <input
                v-model="newNoteTime"
                type="text"
                class="w-20 bg-slate-800 border border-slate-700 rounded-lg px-2 py-1 text-center text-xs font-mono font-bold text-indigo-400"
              />
            </div>
            <textarea
              v-model="newNoteText"
              rows="2"
              placeholder="Type your important note or key takeaway here..."
              class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
            ></textarea>
            <button @click="addNote" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md">
              Save Note to Cloud
            </button>
          </div>

          <!-- Notes List Cards (Matching Prompt Specs) -->
          <div class="space-y-3">
            <div
              v-for="note in myNotes"
              :key="note.id"
              class="p-4 bg-slate-900/90 rounded-2xl border border-slate-700/60 space-y-2 text-xs hover:border-indigo-500/30 transition-all"
            >
              <div class="flex items-center justify-between">
                <span class="px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-300 font-mono font-bold text-[11px] border border-indigo-500/30">
                  ⏱️ {{ note.time }}
                </span>

                <div class="flex items-center gap-2">
                  <Link
                    href="/student/learning-content/videos"
                    class="px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-bold text-[10px] shadow"
                  >
                    ▶ Jump
                  </Link>
                  <button @click="removeNote(note.id)" class="text-rose-400 hover:text-rose-300 text-xs">
                    🗑️
                  </button>
                </div>
              </div>

              <p class="text-slate-200 leading-relaxed font-medium pt-1">
                {{ note.text }}
              </p>
            </div>
          </div>
        </div>

        <!-- RIGHT COLUMN: ATTACHMENTS & RESOURCES DOWNLOADS -->
        <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
          <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
              <span>📥</span>
              <span>ATTACHMENTS & RESOURCES</span>
            </h3>
            <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 text-[10px] font-bold border border-cyan-500/30">
              🛡️ Virus Free Scanned
            </span>
          </div>

          <!-- File Attachments List (Matching Prompt Spec) -->
          <div class="space-y-3">
            <div
              v-for="file in attachments"
              :key="file.id"
              class="p-4 bg-slate-900/90 rounded-2xl border border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-slate-600 transition-all"
            >
              <div class="flex items-center gap-3">
                <span class="text-2xl p-2 rounded-xl bg-slate-800">{{ file.icon }}</span>
                <div>
                  <h4 class="font-bold text-white text-xs">{{ file.name }}</h4>
                  <p class="text-[10px] text-slate-400 mt-0.5">
                    Size: {{ file.size }}  •  Downloads: {{ file.downloads }}
                  </p>
                </div>
              </div>

              <div class="flex items-center gap-2 self-end sm:self-center">
                <button v-if="file.canPreview" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700">
                  👁 Preview Code
                </button>
                <button class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md">
                  ⬇️ Download
                </button>
              </div>
            </div>
          </div>

          <!-- Security Badge Banner -->
          <div class="p-3 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-center text-xs font-bold text-emerald-300">
            🔒 All downloadable files have been scanned and verified virus-free by E.LMS Security Shield.
          </div>
        </div>

      </div>

    </div>
  </StudentLayout>
</template>
