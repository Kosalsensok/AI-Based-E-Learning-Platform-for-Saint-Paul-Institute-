<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  show: boolean
  link?: any
}>()

const emit = defineEmits(['close'])

const copied = ref(false)

const targetUrl = computed(() => {
  if (!props.link) return 'https://vimeo.com'
  return props.link.url || props.link.file_url || props.link.external_url || 'https://vimeo.com'
})

const copyLinkUrl = () => {
  navigator.clipboard.writeText(targetUrl.value)
  copied.value = true
  setTimeout(() => { copied.value = false }, 2000)
}

const openExternal = () => {
  window.open(targetUrl.value, '_blank')
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-slate-900 rounded-3xl shadow-2xl border border-slate-700/80 w-full max-w-4xl overflow-hidden my-6 transform transition-all text-slate-100">
      
      <!-- Clean & Elegant Header -->
      <div class="bg-slate-900 px-6 py-4 text-white flex items-center justify-between border-b border-slate-800">
        <div class="flex items-center gap-3">
          <!-- Flaticon Vector Link Icon -->
          <div class="w-10 h-10 rounded-2xl bg-sky-950/80 border border-sky-500/30 flex items-center justify-center shrink-0 shadow-md">
            <svg class="w-6 h-6" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M14 22L22 14" stroke="#0EA5E9" stroke-width="3" stroke-linecap="round"/>
              <path d="M19 9L23.5 4.5C26 2 30 2 32.5 4.5C35 7 35 11 32.5 13.5L28 18" stroke="#38BDF8" stroke-width="3" stroke-linecap="round"/>
              <path d="M17 27L12.5 31.5C10 34 6 34 3.5 31.5C1 29 1 25 3.5 22.5L8 18" stroke="#38BDF8" stroke-width="3" stroke-linecap="round"/>
            </svg>
          </div>

          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h3 class="text-sm font-bold text-white flex items-center gap-1.5">
                <span class="text-slate-300 uppercase tracking-wider font-extrabold text-xs">WEB REFERENCE</span>
                <span class="text-slate-600">—</span>
                <span class="text-sky-400 font-semibold truncate max-w-[260px] sm:max-w-[380px]">{{ link?.title || 'External Reference Resource' }}</span>
              </h3>
              <span class="px-2 py-0.5 rounded-md bg-sky-500/10 text-sky-400 border border-sky-500/30 text-[10px] font-mono font-bold flex items-center gap-1">
                <span>EXTERNAL LINK</span>
              </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5 font-normal flex items-center gap-2">
              <span>Embedded Web Page Viewer</span>
              <span class="text-slate-600">•</span>
              <span class="text-slate-300 font-mono truncate max-w-[200px]">{{ targetUrl }}</span>
            </p>
          </div>
        </div>

        <button 
          @click="$emit('close')" 
          class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white border border-slate-700 text-lg font-bold transition cursor-pointer shrink-0"
          title="Close"
        >
          ×
        </button>
      </div>

      <!-- Modal Body -->
      <div class="p-6 space-y-5 text-xs text-slate-200 max-h-[75vh] overflow-y-auto">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <!-- Web Iframe Workspace -->
          <div class="lg:col-span-2 space-y-3">
            <div class="bg-slate-950 p-2 rounded-2xl border border-slate-800 shadow-2xl relative">
              <div class="flex items-center justify-between bg-slate-900 px-4 py-2 rounded-xl border border-slate-800 mb-2 font-mono text-xs">
                <span class="text-slate-400 font-bold flex items-center gap-2 truncate">
                  <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                  <span class="truncate">{{ targetUrl }}</span>
                </span>
                <button @click="copyLinkUrl" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-sky-400 rounded-lg font-bold transition cursor-pointer shrink-0">
                  {{ copied ? '✓ Copied!' : '📋 Copy' }}
                </button>
              </div>

              <!-- Iframe Preview -->
              <iframe 
                :src="targetUrl" 
                class="w-full h-[360px] rounded-xl border border-slate-800 bg-white"
                title="External Link Preview"
              ></iframe>
            </div>
          </div>

          <!-- Link Specs & Security Sidebar -->
          <div class="space-y-4">
            
            <div class="bg-slate-950/70 p-4 rounded-2xl border border-slate-800 space-y-2 text-xs">
              <h4 class="font-black text-slate-300 border-b border-slate-800 pb-2">LINK METADATA</h4>
              <div class="flex justify-between py-1 border-b border-slate-900">
                <span class="text-slate-500 font-bold">Title:</span>
                <span class="text-white font-bold truncate max-w-[140px]">{{ link?.title || 'Reference Link' }}</span>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-900">
                <span class="text-slate-500 font-bold">Target URL:</span>
                <span class="text-sky-400 font-mono text-[11px] truncate max-w-[140px]">{{ targetUrl }}</span>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-900">
                <span class="text-slate-500 font-bold">Course:</span>
                <span class="text-slate-200 font-bold truncate max-w-[140px]">{{ link?.course || 'C Programming Basics' }}</span>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-900">
                <span class="text-slate-500 font-bold">Teacher:</span>
                <span class="text-slate-200 font-bold">{{ link?.teacher || 'Mr. Sophea' }}</span>
              </div>
              <div class="flex justify-between py-1">
                <span class="text-slate-500 font-bold">Status:</span>
                <span class="text-emerald-400 font-bold">{{ link?.status || 'Published' }}</span>
              </div>
            </div>

            <div class="bg-slate-950/70 p-4 rounded-2xl border border-slate-800 space-y-3">
              <h4 class="font-black text-slate-300 border-b border-slate-800 pb-2">EXTERNAL ACTION</h4>
              <p class="text-slate-400 text-xs leading-relaxed">
                Some third-party websites restrict embedding via X-Frame-Options header. You can open the external site directly in a new browser tab.
              </p>
              <button @click="openExternal" class="w-full py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-black rounded-xl shadow-lg shadow-sky-600/30 transition flex items-center justify-center gap-2 cursor-pointer">
                <span>🌐 Open in New Tab</span>
              </button>
            </div>

          </div>

        </div>

      </div>

      <!-- Action Footer -->
      <div class="bg-slate-950 px-6 py-4 border-t border-slate-800 flex items-center justify-between">
        <button @click="$emit('close')" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl border border-slate-700 transition cursor-pointer">
          Close
        </button>
        <div class="flex items-center gap-3">
          <button @click="openExternal" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-black rounded-xl shadow-lg shadow-sky-600/30 transition cursor-pointer">
            Open URL Link ↗
          </button>
        </div>
      </div>

    </div>
  </div>
</template>
