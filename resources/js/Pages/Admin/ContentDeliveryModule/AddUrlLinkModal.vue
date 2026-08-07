<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  show: boolean
}>()

const emit = defineEmits(['close', 'addLink'])

const url = ref('')
const title = ref('')
const language = ref<'kh' | 'en'>('kh')
const displayMode = ref<'embed' | 'new_tab'>('embed')
const addSubtitlesLater = ref(false)

const detectedType = computed(() => {
  const u = url.value.toLowerCase().trim()
  if (!u) return null
  if (u.includes('youtube.com') || u.includes('youtu.be')) {
    return {
      type: 'youtube',
      label: '▶️ YouTube – will auto-embed as Video Player',
      icon: '▶️',
      badgeClass: 'bg-red-950 text-red-300 border-red-800'
    }
  } else if (u.includes('vimeo.com')) {
    return {
      type: 'vimeo',
      label: '🎬 Vimeo – will auto-embed as Video Player',
      icon: '🎬',
      badgeClass: 'bg-sky-950 text-sky-300 border-sky-800'
    }
  } else if (u.includes('docs.google.com') || u.includes('drive.google.com')) {
    return {
      type: 'gdoc',
      label: '📄 Google Docs / Drive – will auto-embed as Document Viewer',
      icon: '📄',
      badgeClass: 'bg-emerald-950 text-emerald-300 border-emerald-800'
    }
  } else {
    return {
      type: 'website',
      label: '🌐 External Web Page – Link Card or iFrame',
      icon: '🌐',
      badgeClass: 'bg-indigo-950 text-indigo-300 border-indigo-800'
    }
  }
})

const resetForm = () => {
  url.value = ''
  title.value = ''
  language.value = 'kh'
  displayMode.value = 'embed'
  addSubtitlesLater.value = false
}

const handleClose = () => {
  resetForm()
  emit('close')
}

const handleAdd = () => {
  if (!url.value || !title.value) return

  const item = {
    id: Date.now(),
    title: title.value,
    url: url.value,
    type: 'link',
    sub_type: detectedType.value?.type || 'website',
    language: language.value,
    display_mode: displayMode.value,
    add_subtitles_later: addSubtitlesLater.value,
    icon: detectedType.value?.icon || '🔗',
    info: detectedType.value?.type === 'youtube' || detectedType.value?.type === 'vimeo' ? 'External Video' : 'External Link',
    status: 'Published',
    has_kh: language.value === 'kh',
    has_en: language.value === 'en',
    has_cc: addSubtitlesLater.value
  }

  emit('addLink', item)
  handleClose()
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-slate-900 rounded-3xl border border-slate-700 shadow-2xl w-full max-w-lg overflow-hidden text-slate-100 animate-in fade-in zoom-in-95 duration-200">
      
      <!-- Header -->
      <div class="bg-gradient-to-r from-indigo-950 via-slate-900 to-slate-900 px-6 py-4 border-b border-slate-800 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-xl bg-indigo-600/20 border border-indigo-500/40 flex items-center justify-center text-lg text-indigo-400">
            🔗
          </div>
          <div>
            <h3 class="font-bold text-white text-sm">Add URL Link to Content Library</h3>
            <p class="text-[11px] text-slate-400">ភ្ជាប់ YouTube, Google Docs ឬ Web Page ទៅក្នុងប្រព័ន្ធ</p>
          </div>
        </div>
        <button @click="handleClose" class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition cursor-pointer">
          ✕
        </button>
      </div>

      <!-- Form Body -->
      <div class="p-6 space-y-4">
        
        <!-- URL Input -->
        <div class="space-y-1.5">
          <label class="block text-xs font-bold text-slate-300">
            URL Target: <span class="text-rose-400">*</span>
          </label>
          <input 
            v-model="url" 
            type="url" 
            placeholder="https://youtube.com/watch?v=abc123"
            class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-xs font-mono text-white placeholder:text-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition"
          />

          <!-- Auto Detection Banner -->
          <transition name="fade">
            <div v-if="detectedType" class="mt-2 p-2.5 rounded-xl border text-xs font-bold flex items-center gap-2" :class="detectedType.badgeClass">
              <span class="text-sm">🤖</span>
              <span>Detected: {{ detectedType.label }}</span>
            </div>
          </transition>
        </div>

        <!-- Title Input -->
        <div class="space-y-1.5">
          <label class="block text-xs font-bold text-slate-300">
            Title / ឈ្មោះតំណឯកសារ: <span class="text-rose-400">*</span>
          </label>
          <input 
            v-model="title" 
            type="text" 
            placeholder="e.g. C Loops Tutorial – External Guide"
            class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder:text-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition"
          />
        </div>

        <!-- Language Selector -->
        <div class="space-y-1.5">
          <label class="block text-xs font-bold text-slate-300">Language of content / ភាសាមាតិកា:</label>
          <div class="flex items-center gap-4 text-xs font-bold pt-1">
            <label class="flex items-center gap-2 cursor-pointer text-slate-200">
              <input type="radio" v-model="language" value="kh" class="text-indigo-600 focus:ring-indigo-500 bg-slate-950 border-slate-700" />
              <span>🇰🇭 Khmer (ខ្មែរ)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer text-slate-200">
              <input type="radio" v-model="language" value="en" class="text-indigo-600 focus:ring-indigo-500 bg-slate-950 border-slate-700" />
              <span>🇬🇧 English</span>
            </label>
          </div>
        </div>

        <!-- Display Mode -->
        <div class="space-y-1.5">
          <label class="block text-xs font-bold text-slate-300">Display mode / របៀបបង្ហាញ:</label>
          <div class="space-y-2 text-xs font-bold pt-1">
            <label class="flex items-center gap-2 cursor-pointer text-indigo-300 bg-slate-950/70 p-2.5 rounded-xl border border-slate-800 hover:border-indigo-500/40 transition">
              <input type="radio" v-model="displayMode" value="embed" class="text-indigo-600 focus:ring-indigo-500 bg-slate-950 border-slate-700" />
              <span>Embed inside platform (Recommended – និស្សិតមិនចាកចេញពី E.LMS)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer text-slate-300 bg-slate-950/40 p-2.5 rounded-xl border border-slate-800 hover:border-slate-700 transition">
              <input type="radio" v-model="displayMode" value="new_tab" class="text-indigo-600 focus:ring-indigo-500 bg-slate-950 border-slate-700" />
              <span>Open in new tab (បើកក្នុង Tab ថ្មី)</span>
            </label>
          </div>
        </div>

        <!-- Subtitles Flag Option -->
        <div v-if="detectedType?.type === 'youtube' || detectedType?.type === 'vimeo'" class="pt-1">
          <label class="flex items-center gap-2 text-xs font-bold text-indigo-300 cursor-pointer">
            <input type="checkbox" v-model="addSubtitlesLater" class="rounded bg-slate-950 border-slate-700 text-indigo-600 focus:ring-indigo-500" />
            <span>☑️ Add Khmer subtitles later (បើវីដេអូជាភាសាអង់គ្លេស)</span>
          </label>
        </div>

      </div>

      <!-- Footer Buttons -->
      <div class="bg-slate-950 px-6 py-4 border-t border-slate-800 flex items-center justify-end gap-3">
        <button @click="handleClose" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl text-xs transition cursor-pointer">
          ✕ Cancel
        </button>
        <button 
          @click="handleAdd" 
          :disabled="!url || !title"
          class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-40 text-white font-bold rounded-xl text-xs shadow-lg shadow-indigo-600/30 transition cursor-pointer active:scale-95 flex items-center gap-1.5"
        >
          <span>✅ Add Link</span>
        </button>
      </div>

    </div>
  </div>
</template>
