<script setup lang="ts">
import { ref, computed, watch } from 'vue'

const props = defineProps<{
  show: boolean
  pdf?: any
}>()

const emit = defineEmits(['close'])

const viewMode = ref<'canvas' | 'embed'>('canvas')
const currentPage = ref(1)
const totalPages = computed(() => props.pdf?.pages || 45)
const zoomLevel = ref(100)
const watermarkPdf = ref(false)
const downloadAllowed = ref(true)
const printDisabled = ref(false)
const downloadCount = ref(props.pdf?.downloads || 1250)
const isDownloading = ref(false)

const pdfUrl = computed(() => {
  if (!props.pdf) return 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'
  if (props.pdf.raw_file && props.pdf.raw_file instanceof File) {
    return URL.createObjectURL(props.pdf.raw_file)
  }
  if (props.pdf.file_url && typeof props.pdf.file_url === 'string' && props.pdf.file_url.length > 5) return props.pdf.file_url
  if (props.pdf.url && typeof props.pdf.url === 'string' && (props.pdf.url.startsWith('http') || props.pdf.url.startsWith('blob'))) return props.pdf.url
  return 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'
})

const pdfTitle = computed(() => props.pdf?.title || 'C Programming Notes & Handbook')
const pdfSize = computed(() => props.pdf?.size_formatted || '4.1 MB')
const pdfTeacher = computed(() => props.pdf?.teacher || 'Mr. Sophea')

watch(() => props.pdf, (newPdf) => {
  if (newPdf) {
    downloadCount.value = newPdf.downloads || 1250
    currentPage.value = 1
  }
}, { immediate: true })

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++
}

const zoomOut = () => {
  if (zoomLevel.value > 75) zoomLevel.value -= 25
}

const zoomIn = () => {
  if (zoomLevel.value < 150) zoomLevel.value += 25
}

const handleDownload = () => {
  if (!downloadAllowed.value) return
  isDownloading.value = true
  setTimeout(() => {
    downloadCount.value++
    isDownloading.value = false
    
    // Create temporary download anchor for real or mock file
    const link = document.createElement('a')
    link.href = pdfUrl.value
    link.download = `${pdfTitle.value}.pdf`
    link.target = '_blank'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }, 400)
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-slate-900 rounded-3xl shadow-2xl border border-slate-700/80 w-full max-w-4xl overflow-hidden my-6 transform transition-all text-slate-100">
      
      <!-- Clean & Elegant Header -->
      <div class="bg-slate-900 px-6 py-4 text-white flex items-center justify-between border-b border-slate-800">
        <div class="flex items-center gap-3">
          <!-- Flaticon Vector PDF Icon -->
          <div class="w-10 h-10 rounded-2xl bg-blue-950/80 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-md">
            <svg class="w-6 h-6" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M7 6C7 4.34315 8.34315 3 10 3H21L29 11V30C29 31.6569 27.6569 33 26 33H10C8.34315 33 7 31.6569 7 30V6Z" fill="#3B82F6"/>
              <path d="M21 3V11H29L21 3Z" fill="#1D4ED8"/>
              <rect x="11" y="16" width="14" height="2" rx="1" fill="white"/>
              <rect x="11" y="21" width="14" height="2" rx="1" fill="white"/>
              <rect x="11" y="26" width="9" height="2" rx="1" fill="white"/>
            </svg>
          </div>

          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h3 class="text-sm font-bold text-white flex items-center gap-1.5">
                <span class="text-slate-300 uppercase tracking-wider font-extrabold text-xs">PDF DOCUMENT</span>
                <span class="text-slate-600">—</span>
                <span class="text-blue-400 font-semibold truncate max-w-[260px] sm:max-w-[380px]">{{ pdfTitle }}</span>
              </h3>
              <span class="px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-400 border border-blue-500/30 text-[10px] font-mono font-bold flex items-center gap-1">
                <span>{{ totalPages }} PAGES</span>
              </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5 font-normal flex items-center gap-2">
              <span>Interactive Reader Engine</span>
              <span class="text-slate-600">•</span>
              <span class="text-slate-300 font-mono">{{ pdfSize }}</span>
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
          
          <!-- PDF Document Workspace -->
          <div class="lg:col-span-2 space-y-3">
            
            <!-- View Mode Switcher -->
            <div class="flex items-center justify-between bg-slate-950 p-2 rounded-xl border border-slate-800 text-xs">
              <span class="font-bold text-slate-400 px-2">Engine Mode:</span>
              <div class="flex items-center gap-1">
                <button 
                  @click="viewMode = 'canvas'"
                  :class="['px-3.5 py-1.5 rounded-lg font-medium transition cursor-pointer', viewMode === 'canvas' ? 'bg-blue-600 text-white shadow-xs font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-850']"
                >
                  📄 Interactive Reader
                </button>
                <button 
                  @click="viewMode = 'embed'"
                  :class="['px-3.5 py-1.5 rounded-lg font-medium transition cursor-pointer', viewMode === 'embed' ? 'bg-blue-600 text-white shadow-xs font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-850']"
                >
                  🌐 Embedded PDF
                </button>
              </div>
            </div>

            <!-- Interactive Reader Mode (Real PDF Page Render) -->
            <div v-if="viewMode === 'canvas'" class="space-y-3">
              <div class="bg-slate-950 rounded-2xl border border-slate-800 p-3 flex flex-col items-center justify-center min-h-[340px] shadow-2xl relative overflow-hidden">
                
                <!-- Watermark Overlay Demo -->
                <div v-if="watermarkPdf" class="absolute inset-0 pointer-events-none flex items-center justify-center opacity-25 rotate-45 select-none z-10">
                  <span class="text-lg font-black text-emerald-400 font-mono tracking-widest border-2 border-emerald-400 p-2.5 rounded-xl shadow-lg bg-slate-950/80">
                    PREPARED FOR STUDENT #STU-9821
                  </span>
                </div>

                <!-- Real PDF Page Rendering Container (Native Vector Zoom for Razor-Sharp Text) -->
                <div 
                  class="w-full rounded-xl overflow-hidden shadow-2xl transition-all duration-200"
                  :style="{ height: `${Math.min(520, Math.max(340, Math.round(360 * (zoomLevel / 100))))}px` }"
                >
                  <object
                    :key="`${pdfUrl}_${currentPage}_${zoomLevel}`"
                    :data="`${pdfUrl}#page=${currentPage}&zoom=${zoomLevel}&toolbar=0&navpanes=0`"
                    type="application/pdf"
                    class="w-full h-full rounded-xl border border-slate-800 bg-slate-900"
                  >
                    <iframe 
                      :key="`frame_${pdfUrl}_${currentPage}_${zoomLevel}`"
                      :src="`${pdfUrl}#page=${currentPage}&zoom=${zoomLevel}&toolbar=0&navpanes=0`" 
                      class="w-full h-full rounded-xl border border-slate-800 bg-slate-900"
                      title="PDF Interactive Reader"
                    ></iframe>
                  </object>
                </div>

              </div>

              <!-- Page Control Bar -->
              <div class="w-full bg-slate-950 p-3 rounded-xl border border-slate-800 flex items-center justify-between font-mono text-xs shadow-inner">
                <div class="flex items-center gap-2">
                  <button 
                    @click="prevPage" 
                    :disabled="currentPage <= 1"
                    :class="['px-3 py-1 rounded-lg font-bold border transition cursor-pointer', currentPage > 1 ? 'bg-slate-800 hover:bg-slate-700 text-slate-200 border-slate-700' : 'bg-slate-900 text-slate-600 border-slate-850 cursor-not-allowed']"
                  >
                    ◀ Prev
                  </button>
                  <span class="text-slate-300 font-bold px-2">Page {{ currentPage }} of {{ totalPages }}</span>
                  <button 
                    @click="nextPage" 
                    :disabled="currentPage >= totalPages"
                    :class="['px-3 py-1 rounded-lg font-bold border transition cursor-pointer', currentPage < totalPages ? 'bg-slate-800 hover:bg-slate-700 text-slate-200 border-slate-700' : 'bg-slate-900 text-slate-600 border-slate-850 cursor-not-allowed']"
                  >
                    Next ▶
                  </button>
                </div>
                <div class="flex items-center gap-2">
                  <button @click="zoomOut" class="p-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg text-slate-300 font-bold cursor-pointer flex items-center justify-center" title="Zoom Out">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" />
                    </svg>
                  </button>
                  <span class="text-blue-400 font-bold text-[11px] px-1">{{ zoomLevel }}%</span>
                  <button @click="zoomIn" class="p-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg text-slate-300 font-bold cursor-pointer flex items-center justify-center" title="Zoom In">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Embedded PDF Iframe / Object Mode -->
            <div v-else class="relative bg-slate-950 rounded-2xl border border-slate-800 p-2 min-h-[380px] shadow-2xl">
              <div v-if="watermarkPdf" class="absolute inset-0 pointer-events-none flex items-center justify-center opacity-25 rotate-45 select-none z-10">
                <span class="text-xl font-black text-emerald-400 font-mono tracking-widest border-2 border-emerald-400 p-3 rounded-xl shadow-2xl bg-slate-950/80">
                  PREPARED FOR STUDENT #STU-9821
                </span>
              </div>
              <object
                :data="pdfUrl"
                type="application/pdf"
                class="w-full h-[380px] rounded-xl border border-slate-800 bg-slate-900"
              >
                <iframe 
                  :src="pdfUrl" 
                  class="w-full h-[380px] rounded-xl border border-slate-800 bg-slate-900"
                  title="PDF Document Preview"
                ></iframe>
              </object>
            </div>

          </div>

          <!-- PDF Info & Security Rules -->
          <div class="space-y-4">
            
            <div class="bg-slate-950/70 p-4 rounded-2xl border border-slate-800 space-y-2 text-xs">
              <h4 class="font-black text-slate-300 border-b border-slate-800 pb-2">PDF METADATA</h4>
              <div class="flex justify-between py-1 border-b border-slate-900">
                <span class="text-slate-500 font-bold">Total Pages:</span>
                <span class="text-blue-400 font-mono font-bold">{{ totalPages }} Pages</span>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-900">
                <span class="text-slate-500 font-bold">File Size:</span>
                <span class="text-slate-200 font-mono font-bold">{{ pdfSize }}</span>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-900">
                <span class="text-slate-500 font-bold">Downloads:</span>
                <span class="text-blue-400 font-mono font-bold">{{ downloadCount.toLocaleString() }}</span>
              </div>
              <div class="flex justify-between py-1">
                <span class="text-slate-500 font-bold">Author/Teacher:</span>
                <span class="text-slate-300 font-bold">{{ pdfTeacher }}</span>
              </div>
            </div>

            <div class="bg-slate-950/70 p-4 rounded-2xl border border-slate-800 space-y-3">
              <h4 class="font-black text-slate-300 border-b border-slate-800 pb-2">SECURITY & WATERMARK</h4>
              <label class="flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-slate-200">
                <input type="checkbox" v-model="watermarkPdf" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-blue-500 cursor-pointer" />
                <span>Watermark PDF with Student Name & ID</span>
              </label>
              <label class="flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-slate-200">
                <input type="checkbox" v-model="downloadAllowed" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-blue-500 cursor-pointer" />
                <span>Allow Enrolled Students to Download</span>
              </label>
              <label class="flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-slate-200">
                <input type="checkbox" v-model="printDisabled" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-blue-500 cursor-pointer" />
                <span>Disable Printing & Copying Text</span>
              </label>
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
          <button 
            @click="handleDownload" 
            :disabled="!downloadAllowed || isDownloading"
            :class="[
              'px-5 py-2.5 text-xs font-semibold rounded-xl shadow-xs transition flex items-center gap-2 border cursor-pointer active:scale-95',
              downloadAllowed ? 'bg-blue-600 hover:bg-blue-500 border-blue-500/30 text-white' : 'bg-slate-800 border-slate-700 text-slate-500 cursor-not-allowed'
            ]"
          >
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span>{{ isDownloading ? 'Downloading...' : 'Download PDF' }}</span>
          </button>
        </div>
      </div>

    </div>
  </div>
</template>
