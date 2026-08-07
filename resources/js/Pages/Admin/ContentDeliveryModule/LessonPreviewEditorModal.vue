<script setup lang="ts">
import { ref, watch, computed } from 'vue'

const props = defineProps<{
  show: boolean
  item?: any
}>()

const emit = defineEmits(['close', 'saved'])

const title = ref('')
const contentType = ref('video')
const contentBody = ref('')
const duration = ref('')
const status = ref('Published')
const toastMessage = ref<string | null>(null)

watch(() => props.item, (newVal) => {
  if (newVal) {
    title.value = newVal.title || ''
    contentType.value = newVal.type || 'video'
    duration.value = newVal.info || newVal.duration || '18:30 · 1080p HD'
    contentBody.value = newVal.content || newVal.description || `Lesson: ${newVal.title}\nType: ${newVal.type}\nDuration: ${newVal.info || '18:30 · 1080p HD'}\n\nKey Concepts & Notes:\n- Introduction and core concepts of ${newVal.title}\n- Code examples, memory allocation & syntax guidelines\n- Practice exercises for students`
    status.value = newVal.status || 'Published'
  }
}, { immediate: true })

const activeMediaUrl = computed(() => {
  if (!props.item) return 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'
  if (props.item.file_url) return props.item.file_url
  if (props.item.video_url) return props.item.video_url
  if (props.item.pdf_url) return props.item.pdf_url
  if (props.item.url) return props.item.url
  return 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'
})

const handleSave = () => {
  if (props.item) {
    props.item.title = title.value
    props.item.content = contentBody.value
    props.item.description = contentBody.value
  }
  toastMessage.value = '✅ Saved lesson content successfully!'
  setTimeout(() => {
    emit('saved', {
      ...props.item,
      title: title.value,
      content: contentBody.value
    })
    toastMessage.value = null
    emit('close')
  }, 1000)
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/85 backdrop-blur-xs flex items-center justify-center p-4">
    
    <!-- Toast notification -->
    <transition name="fade">
      <div v-if="toastMessage" class="fixed top-6 right-6 z-60 bg-emerald-900 border border-emerald-500 text-emerald-100 px-5 py-3 rounded-2xl shadow-2xl font-bold text-xs flex items-center gap-2">
        <span>⚡</span>
        <span>{{ toastMessage }}</span>
      </div>
    </transition>

    <div class="bg-slate-900 rounded-3xl shadow-2xl border border-slate-700/80 w-full max-w-3xl overflow-hidden my-6 transform transition-all text-slate-100">
      
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-indigo-950 via-slate-900 to-indigo-950 px-6 py-4 text-white flex items-center justify-between border-b border-indigo-900/60">
        <div class="flex items-center gap-3">
          <span class="p-2 bg-indigo-950 border border-indigo-800 rounded-xl text-xl">
            {{ contentType === 'video' ? '🎥' : contentType === 'pdf' ? '📄' : contentType === 'slide' ? '📊' : '📝' }}
          </span>
          <div>
            <h3 class="text-base font-black text-sky-400 flex items-center gap-2">
              <span>LESSON PREVIEW & EDITOR</span>
              <span class="px-2.5 py-0.5 rounded-full text-[10px] bg-indigo-950 text-indigo-300 border border-indigo-800 font-mono">
                {{ contentType.toUpperCase() }}
              </span>
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">{{ duration }}</p>
          </div>
        </div>
        <button @click="$emit('close')" class="text-slate-400 hover:text-white text-2xl font-bold w-8 h-8 flex items-center justify-center rounded-xl bg-slate-800/80 hover:bg-slate-700 transition cursor-pointer">✕</button>
      </div>

      <!-- Modal Body -->
      <div class="p-6 space-y-4 text-xs text-slate-200 max-h-[75vh] overflow-y-auto">
        
        <!-- Lesson Title Input -->
        <div>
          <label class="block font-bold text-slate-300 mb-1.5">Lesson Title:</label>
          <input 
            v-model="title" 
            type="text" 
            class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700/80 rounded-xl text-white font-bold text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            placeholder="Enter lesson title..."
          />
        </div>

        <!-- Video Player / Media Preview Embed if Video/PDF -->
        <div v-if="contentType === 'video'" class="space-y-1">
          <label class="block font-bold text-slate-300 mb-1">Media Player Preview:</label>
          <div class="rounded-2xl overflow-hidden border border-slate-800 bg-slate-950 aspect-video shadow-inner flex items-center justify-center">
            <video controls class="w-full h-full object-contain">
              <source :src="activeMediaUrl" type="video/mp4" />
              Your browser does not support HTML5 video player.
            </video>
          </div>
        </div>

        <!-- Lesson Content Body / Textarea -->
        <div>
          <label class="block font-bold text-slate-300 mb-1.5 flex justify-between items-center">
            <span>Lesson Description & Content Body (Editable):</span>
            <span class="text-[10px] text-slate-500 font-mono">{{ contentBody.length }} characters</span>
          </label>
          <textarea 
            v-model="contentBody" 
            rows="7" 
            class="w-full px-4 py-3 bg-slate-950 border border-slate-700/80 rounded-2xl text-slate-100 font-sans text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none leading-relaxed"
            placeholder="Type lesson content body, instructions or markdown..."
          ></textarea>
        </div>

      </div>

      <!-- Modal Footer -->
      <div class="bg-slate-950 px-6 py-4 border-t border-slate-800 flex items-center justify-between">
        <span class="text-xs text-slate-500 font-mono">Status: <span class="text-emerald-400 font-bold">🟢 Published</span></span>
        
        <div class="flex items-center gap-3">
          <button 
            @click="$emit('close')" 
            class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl border border-slate-700 transition cursor-pointer text-xs"
          >
            Cancel
          </button>
          <button 
            @click="handleSave" 
            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-xl shadow-lg shadow-blue-600/30 transition cursor-pointer text-xs active:scale-95 flex items-center gap-1.5"
          >
            <span>💾 Save Changes</span>
          </button>
        </div>
      </div>

    </div>
  </div>
</template>
