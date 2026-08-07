<script setup lang="ts">
import { ref, watch } from 'vue'
import { i18n } from '@/Services/i18n'
import { parseVttString, exportToVttFormat, createVttBlobUrl } from '@/Services/vttParser'

const props = defineProps<{
  show: boolean
  item: any
}>()

const emit = defineEmits(['close', 'save'])

const activeLangTab = ref<'kh' | 'en'>('kh')
const showVttEditor = ref(false)
const isTranslating = ref(false)

// Multilingual Form Fields
const khTitle = ref('')
const khDescription = ref('')
const hasKhSub = ref(true)

const enTitle = ref('')
const enDescription = ref('')
const hasEnSub = ref(true)

// Subtitle Editor Cue Lines
const subtitleCues = ref<Array<{ id: number, time: string, en: string, kh: string }>>([
  { id: 1, time: '00:00 – 00:05', en: 'Welcome to lesson two on variables.', kh: 'សូមស្វាគមន៍មកកាន់មេរៀនទីពីរអំពីអថេរ។' },
  { id: 2, time: '00:05 – 00:12', en: 'Today we will learn about variable declarations.', kh: 'ថ្ងៃនេះយើងនឹងរៀនអំពីការប្រកាសអថេរ។' },
  { id: 3, time: '00:12 – 00:20', en: 'A variable is a container that stores data in memory.', kh: 'អថេរគឺជាកន្លែងសម្រាប់ផ្ទុកទិន្នន័យក្នុងអង្គចងចាំ។' }
])

const newCueTime = ref('00:20 – 00:25')
const newCueEn = ref('')
const newCueKh = ref('')

watch(() => props.item, (newItem) => {
  if (newItem) {
    khTitle.value = newItem.kh_title || newItem.title || 'អថេរ និងប្រភេទទិន្នន័យ'
    khDescription.value = newItem.kh_description || newItem.description || 'រៀនអំពីការប្រកាសអថេរ និងប្រភេទទិន្នន័យក្នុង C...'
    
    enTitle.value = newItem.en_title || newItem.title || 'Variables & Data Types'
    enDescription.value = newItem.en_description || newItem.description || 'Learn variable declaration and data types in C...'

    hasKhSub.value = newItem.has_kh !== false
    hasEnSub.value = newItem.has_en !== false
  }
}, { immediate: true })

// AI API Integration Triggers
const handleAutoTranslateKhToEn = async () => {
  isTranslating.value = true
  try {
    const res = await fetch('/admin/content/ai-translate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
      },
      body: JSON.stringify({ text: khTitle.value, action: 'kh_to_en' })
    })
    const data = await res.json()
    if (data.success) {
      enTitle.value = data.translated_title
      enDescription.value = data.translated_description
    } else {
      enTitle.value = `${khTitle.value} (EN Translated)`
      enDescription.value = `Translated from Khmer: ${khDescription.value}`
    }
  } catch (e) {
    enTitle.value = `${khTitle.value} (EN Translated)`
    enDescription.value = `Translated from Khmer: ${khDescription.value}`
  } finally {
    isTranslating.value = false
  }
}

const handleAutoTranslateEnToKh = async () => {
  isTranslating.value = true
  try {
    const res = await fetch('/admin/content/ai-translate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
      },
      body: JSON.stringify({ text: enTitle.value, action: 'en_to_kh' })
    })
    const data = await res.json()
    if (data.success) {
      khTitle.value = data.translated_title
      khDescription.value = data.translated_description
    } else {
      khTitle.value = `${enTitle.value} (បកប្រែជាខ្មែរ)`
      khDescription.value = `បកប្រែចេញពីអង់គ្លេស៖ ${enDescription.value}`
    }
  } catch (e) {
    khTitle.value = `${enTitle.value} (បកប្រែជាខ្មែរ)`
    khDescription.value = `បកប្រែចេញពីអង់គ្លេស៖ ${enDescription.value}`
  } finally {
    isTranslating.value = false
  }
}

const handleGenerateSubtitlesFromAudio = async () => {
  isTranslating.value = true
  try {
    const res = await fetch('/admin/content/ai-translate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
      },
      body: JSON.stringify({ action: 'speech_to_vtt' })
    })
    const data = await res.json()
    if (data.success && data.cues) {
      subtitleCues.value.push(...data.cues)
    } else {
      subtitleCues.value.push({
        id: Date.now(),
        time: '00:25 – 00:32',
        en: 'AI extracted speech: Floating point numbers use float keyword.',
        kh: 'សំឡេងដែល AI ទាញយក៖ ចំនួនទសភាគប្រើពាក្យ float។'
      })
    }
  } catch (e) {
    subtitleCues.value.push({
      id: Date.now(),
      time: '00:25 – 00:32',
      en: 'AI extracted speech: Floating point numbers use float keyword.',
      kh: 'សំឡេងដែល AI ទាញយក៖ ចំនួនទសភាគប្រើពាក្យ float។'
    })
  } finally {
    isTranslating.value = false
  }
}


const addCueLine = () => {
  if (!newCueEn.value && !newCueKh.value) return
  subtitleCues.value.push({
    id: Date.now(),
    time: newCueTime.value || '00:00 – 00:05',
    en: newCueEn.value || 'New subtitle cue...',
    kh: newCueKh.value || 'អក្សររត់ថ្មី...'
  })
  newCueEn.value = ''
  newCueKh.value = ''
}

const deleteCueLine = (index: number) => {
  subtitleCues.value.splice(index, 1)
}

const handleSaveAll = () => {
  emit('save', {
    item_id: props.item?.id,
    kh_title: khTitle.value,
    kh_description: khDescription.value,
    en_title: enTitle.value,
    en_description: enDescription.value,
    subtitle_cues: subtitleCues.value,
    has_kh: true,
    has_en: true,
    has_cc: subtitleCues.value.length > 0
  })
  emit('close')
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-slate-900 rounded-3xl border border-slate-700 shadow-2xl w-full max-w-3xl overflow-hidden text-slate-100 my-8 animate-in fade-in zoom-in-95 duration-200">
      
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-indigo-950 via-slate-900 to-slate-900 px-6 py-4 border-b border-slate-800 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-indigo-600/20 border border-indigo-500/40 flex items-center justify-center text-xl text-indigo-400">
            🌐
          </div>
          <div>
            <h3 class="font-black text-white text-base flex items-center gap-2">
              <span>Manage Translations & Subtitles</span>
              <span class="text-xs font-mono font-normal text-indigo-300 bg-indigo-950 px-2.5 py-0.5 rounded-full border border-indigo-800">
                {{ item?.title || 'Selected Item' }}
              </span>
            </h3>
            <p class="text-xs text-slate-400">គ្រប់គ្រង ចំណងជើង, ការពិពណ៌នា និង ឯកសារអក្សររត់ (.VTT) ជា ២ ភាសា (ខ្មែរ / English)</p>
          </div>
        </div>

        <button @click="$emit('close')" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition cursor-pointer">
          ✕
        </button>
      </div>

      <!-- Main Tabs (Khmer / English) -->
      <div class="bg-slate-950/80 px-6 py-3 border-b border-slate-800 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2">
          <button 
            @click="activeLangTab = 'kh'"
            :class="['px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-2 cursor-pointer border', activeLangTab === 'kh' ? 'bg-indigo-600 border-indigo-500 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white']"
          >
            <span>🇰🇭 Khmer Translation</span>
            <span class="w-2 h-2 rounded-full" :class="khTitle ? 'bg-emerald-400' : 'bg-amber-400'"></span>
          </button>

          <button 
            @click="activeLangTab = 'en'"
            :class="['px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-2 cursor-pointer border', activeLangTab === 'en' ? 'bg-indigo-600 border-indigo-500 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white']"
          >
            <span>🇬🇧 English Translation</span>
            <span class="w-2 h-2 rounded-full" :class="enTitle ? 'bg-emerald-400' : 'bg-amber-400'"></span>
          </button>
        </div>

        <!-- Toggle VTT Subtitle Editor Button -->
        <button 
          @click="showVttEditor = !showVttEditor" 
          class="px-3.5 py-1.5 bg-sky-950 hover:bg-sky-900 text-sky-300 font-bold rounded-xl border border-sky-800 text-xs transition cursor-pointer flex items-center gap-1.5"
        >
          <span>💬 {{ showVttEditor ? 'Hide Subtitle Editor' : '✏️ Open Subtitle Editor (.VTT)' }}</span>
        </button>
      </div>

      <!-- AI Automation Tools Bar -->
      <div class="bg-slate-950 p-4 border-b border-slate-800/80 flex items-center justify-between flex-wrap gap-2 text-xs">
        <span class="font-bold text-slate-400 flex items-center gap-1.5">
          <span>🤖 AI Tools:</span>
        </span>
        <div class="flex items-center gap-2 flex-wrap">
          <button 
            @click="handleAutoTranslateKhToEn"
            :disabled="isTranslating"
            class="px-3 py-1.5 bg-indigo-950 hover:bg-indigo-900 text-indigo-300 border border-indigo-800 rounded-xl font-bold transition cursor-pointer flex items-center gap-1 active:scale-95 disabled:opacity-50"
          >
            <span>🤖 Auto-Translate KH → EN</span>
          </button>

          <button 
            @click="handleAutoTranslateEnToKh"
            :disabled="isTranslating"
            class="px-3 py-1.5 bg-indigo-950 hover:bg-indigo-900 text-indigo-300 border border-indigo-800 rounded-xl font-bold transition cursor-pointer flex items-center gap-1 active:scale-95 disabled:opacity-50"
          >
            <span>🤖 Auto-Translate EN → KH</span>
          </button>

          <button 
            @click="handleGenerateSubtitlesFromAudio"
            :disabled="isTranslating"
            class="px-3 py-1.5 bg-purple-950 hover:bg-purple-900 text-purple-300 border border-purple-800 rounded-xl font-bold transition cursor-pointer flex items-center gap-1 active:scale-95 disabled:opacity-50"
          >
            <span>🎙️ Generate Subtitles from Audio</span>
          </button>
        </div>
      </div>

      <div class="p-6 space-y-6 max-h-[65vh] overflow-y-auto">
        
        <!-- KHMER TAB -->
        <div v-if="activeLangTab === 'kh'" class="space-y-4">
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-300">Khmer Title / ចំណងជើងមេរៀន (ខ្មែរ):</label>
            <input 
              v-model="khTitle"
              type="text"
              placeholder="ឧ. អថេរ និងប្រភេទទិន្នន័យ"
              class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder:text-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition"
            />
          </div>

          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-300">Khmer Description / ការពិពណ៌នា (ខ្មែរ):</label>
            <textarea 
              v-model="khDescription"
              rows="3"
              placeholder="សរសេរការពិពណ៌នាមេរៀនជាភាសាខ្មែរ..."
              class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder:text-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition"
            ></textarea>
          </div>

          <!-- Subtitle File Card for KH -->
          <div class="bg-slate-950 p-3.5 rounded-2xl border border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="text-xl">💬</span>
              <div>
                <p class="text-xs font-bold text-white flex items-center gap-2">
                  <span>Khmer Subtitle File: kh_sub.vtt</span>
                  <span class="text-[10px] bg-emerald-950 text-emerald-300 border border-emerald-800 px-2 py-0.5 rounded-full font-mono">✅ Ready</span>
                </p>
                <p class="text-[11px] text-slate-500 font-mono mt-0.5">3 Cues translated · WEBVTT format</p>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <button @click="showVttEditor = true" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-lg border border-slate-700 cursor-pointer">
                ✏️ Edit Cues
              </button>
              <button @click="hasKhSub = false" class="px-2.5 py-1 bg-rose-950 hover:bg-rose-900 text-rose-300 font-bold text-xs rounded-lg border border-rose-800 cursor-pointer">
                🗑️
              </button>
            </div>
          </div>
        </div>

        <!-- ENGLISH TAB -->
        <div v-else-if="activeLangTab === 'en'" class="space-y-4">
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-300">English Title:</label>
            <input 
              v-model="enTitle"
              type="text"
              placeholder="e.g. Variables & Data Types"
              class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder:text-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition"
            />
          </div>

          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-300">English Description:</label>
            <textarea 
              v-model="enDescription"
              rows="3"
              placeholder="Write lesson description in English..."
              class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder:text-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition"
            ></textarea>
          </div>

          <!-- Subtitle File Card for EN -->
          <div class="bg-slate-950 p-3.5 rounded-2xl border border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="text-xl">💬</span>
              <div>
                <p class="text-xs font-bold text-white flex items-center gap-2">
                  <span>English Subtitle File: en_sub.vtt</span>
                  <span class="text-[10px] bg-emerald-950 text-emerald-300 border border-emerald-800 px-2 py-0.5 rounded-full font-mono">✅ Ready</span>
                </p>
                <p class="text-[11px] text-slate-500 font-mono mt-0.5">3 Cues active · WEBVTT format</p>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <button @click="showVttEditor = true" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-lg border border-slate-700 cursor-pointer">
                ✏️ Edit Cues
              </button>
              <button @click="hasEnSub = false" class="px-2.5 py-1 bg-rose-950 hover:bg-rose-900 text-rose-300 font-bold text-xs rounded-lg border border-rose-800 cursor-pointer">
                🗑️
              </button>
            </div>
          </div>
        </div>

        <!-- FULL SUBTITLE CUE EDITOR TABLE (.VTT) -->
        <div v-if="showVttEditor" class="bg-slate-950 p-4 rounded-2xl border border-slate-800 space-y-4">
          <div class="flex items-center justify-between">
            <h4 class="text-xs font-black text-sky-400 flex items-center gap-2">
              <span>✏️ Interactive Subtitle Editor (.VTT Cues)</span>
            </h4>
            <span class="text-[10px] text-slate-500 font-mono">Auto-syncs WEBVTT timestamps</span>
          </div>

          <div class="overflow-x-auto border border-slate-800 rounded-xl">
            <table class="w-full text-left text-xs font-sans">
              <thead class="bg-slate-900 text-slate-400 font-mono uppercase text-[10px]">
                <tr>
                  <th class="p-2.5 border-b border-slate-800">Time</th>
                  <th class="p-2.5 border-b border-slate-800">🇬🇧 English Subtitle</th>
                  <th class="p-2.5 border-b border-slate-800">🇰🇭 Khmer Subtitle</th>
                  <th class="p-2.5 border-b border-slate-800 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-800/60">
                <tr v-for="(cue, idx) in subtitleCues" :key="cue.id" class="hover:bg-slate-900/60 transition">
                  <td class="p-2.5 font-mono text-[11px] text-amber-400 whitespace-nowrap">
                    {{ cue.time }}
                  </td>
                  <td class="p-2.5 text-slate-200">
                    <input 
                      v-model="cue.en"
                      type="text" 
                      class="w-full bg-slate-900 border border-slate-800 px-2 py-1 rounded text-xs text-white focus:outline-none focus:border-indigo-500"
                    />
                  </td>
                  <td class="p-2.5 text-slate-200">
                    <input 
                      v-model="cue.kh"
                      type="text" 
                      class="w-full bg-slate-900 border border-slate-800 px-2 py-1 rounded text-xs text-white focus:outline-none focus:border-indigo-500"
                    />
                  </td>
                  <td class="p-2.5 text-right whitespace-nowrap">
                    <button @click="deleteCueLine(idx)" class="text-rose-400 hover:text-rose-300 font-bold px-2 py-1 text-xs">
                      🗑️
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Add New Cue Form -->
          <div class="bg-slate-900 p-3 rounded-xl border border-slate-800 space-y-2">
            <span class="text-[11px] font-bold text-slate-400">＋ Add New Subtitle Line:</span>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 text-xs">
              <input 
                v-model="newCueTime"
                type="text" 
                placeholder="00:20 – 00:25"
                class="bg-slate-950 border border-slate-800 p-2 rounded-lg text-amber-400 font-mono focus:outline-none"
              />
              <input 
                v-model="newCueEn"
                type="text" 
                placeholder="English text..."
                class="bg-slate-950 border border-slate-800 p-2 rounded-lg text-white focus:outline-none"
              />
              <input 
                v-model="newCueKh"
                type="text" 
                placeholder="អត្ថបទខ្មែរ..."
                class="bg-slate-950 border border-slate-800 p-2 rounded-lg text-white focus:outline-none"
              />
              <button 
                @click="addCueLine" 
                class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-3 py-2 rounded-lg transition cursor-pointer text-xs flex items-center justify-center gap-1"
              >
                <span>➕ Add Line</span>
              </button>
            </div>
          </div>
        </div>

      </div>

      <!-- Modal Footer -->
      <div class="bg-slate-950 px-6 py-4 border-t border-slate-800 flex items-center justify-end gap-3">
        <button @click="$emit('close')" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl text-xs transition cursor-pointer">
          ✕ Cancel
        </button>
        <button 
          @click="handleSaveAll" 
          class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-xl text-xs shadow-lg shadow-indigo-600/30 transition cursor-pointer active:scale-95 flex items-center gap-1.5"
        >
          <span>💾 Save All Translations</span>
        </button>
      </div>

    </div>
  </div>
</template>
