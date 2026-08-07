<script setup lang="ts">
import { ref, computed, watch } from 'vue'

const props = defineProps<{
  show: boolean
  courses?: Array<any>
}>()

const emit = defineEmits(['close', 'uploaded', 'preview'])

const triggerInstantPreview = () => {
  const rawFileObj = selectedFile.value?.raw
  const sampleVideoUrl = 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'
  const blobUrl = rawFileObj ? URL.createObjectURL(rawFileObj) : (externalUrl.value && externalUrl.value.startsWith('http') ? externalUrl.value : sampleVideoUrl)
  const thumbUrl = thumbnailPreviewUrl.value || thumbnailUrlInput.value || undefined
  const filename = selectedFile.value ? selectedFile.value.name : (title.value ? `${title.value}.${fileType.value}` : `New_${fileType.value}_file`)
  const filesize = selectedFile.value ? selectedFile.value.size : '14.5 MB'

  emit('preview', {
    title: title.value || (fileType.value.toUpperCase() + ' Asset'),
    file_name: filename,
    type: fileType.value,
    status: 'Draft',
    size_formatted: filesize,
    course: selectedCourse.value,
    teacher: 'Mr. Sophea',
    raw_file: rawFileObj,
    file_url: blobUrl,
    video_url: fileType.value === 'video' ? blobUrl : undefined,
    url: externalUrl.value || blobUrl,
    thumbnail_url: thumbUrl,
    thumbnail: thumbUrl || (fileType.value === 'video' ? '🎥' : fileType.value === 'pdf' ? '📄' : fileType.value === 'slide' ? '📊' : fileType.value === 'note' ? '📝' : '🔗')
  })
}

const fileType = ref<'video' | 'pdf' | 'slide' | 'note' | 'link'>('video')
const title = ref('')
const description = ref('')
const tags = ref('C, Programming, Beginner')
const externalUrl = ref('')
const selectedCourse = ref('C Programming Basics')
const selectedModule = ref('Module 1: Introduction to C Basics')
const selectedChapter = ref('Chapter 1.1: Environment Setup & Hello World')
const visibility = ref('Enrolled Students Only')
const allowPreview = ref(true)
const requiresPayment = ref(true)

// Dynamic Module & Chapter lists based on course selection
const availableModules = computed(() => {
  if (selectedCourse.value === 'Database Systems & SQL') {
    return [
      'Module 1: Relational Database Foundations',
      'Module 2: Advanced SQL Queries & Joins',
      'Module 3: Database Indexing & Performance Tuning'
    ]
  } else if (selectedCourse.value === 'Tourism Management Principles') {
    return [
      'Module 1: Introduction to Global Tourism',
      'Module 2: Hospitality & Hotel Operations',
      'Module 3: Destination Marketing & Strategy'
    ]
  }
  return [
    'Module 1: Introduction to C Basics',
    'Module 2: Control Structures & Loops',
    'Module 3: Functions & Memory Pointers'
  ]
})

const availableChapters = computed(() => {
  if (selectedCourse.value === 'Database Systems & SQL') {
    return [
      'Chapter 1.1: ER Diagrams & Schema Design',
      'Chapter 1.2: SQL DDL & DML Commands',
      'Chapter 1.3: Normalization 1NF to 3NF'
    ]
  } else if (selectedCourse.value === 'Tourism Management Principles') {
    return [
      'Chapter 1.1: Tourism Eco-Systems',
      'Chapter 1.2: Cultural & Heritage Tourism',
      'Chapter 1.3: Sustainable Tourism Analytics'
    ]
  }
  return [
    'Chapter 1.1: Environment Setup & Hello World',
    'Chapter 1.2: Variables, Constants & Data Types',
    'Chapter 1.3: Operators & Logical Expressions'
  ]
})

// Auto sync default selection when course changes
watch(selectedCourse, () => {
  if (availableModules.value.length > 0) {
    selectedModule.value = availableModules.value[0]
  }
  if (availableChapters.value.length > 0) {
    selectedChapter.value = availableChapters.value[0]
  }
})

// Real File Picking & Drag-and-Drop
const fileInputRef = ref<HTMLInputElement | null>(null)
const selectedFile = ref<{ name: string; size: string; raw: File } | null>(null)
const isDragging = ref(false)

// Thumbnail / Representative Intro Image
const thumbnailInputRef = ref<HTMLInputElement | null>(null)
const thumbnailPreviewUrl = ref<string>('')
const thumbnailUrlInput = ref<string>('')

const triggerThumbnailBrowse = () => {
  thumbnailInputRef.value?.click()
}

const onThumbnailFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    const reader = new FileReader()
    reader.onload = (event) => {
      thumbnailPreviewUrl.value = event.target?.result as string
    }
    reader.readAsDataURL(file)
  }
}

const clearThumbnailImage = () => {
  thumbnailPreviewUrl.value = ''
  thumbnailUrlInput.value = ''
  if (thumbnailInputRef.value) {
    thumbnailInputRef.value.value = ''
  }
}

const fileAcceptTypes = computed(() => {
  if (fileType.value === 'video') return 'video/mp4,video/quicktime,video/webm'
  if (fileType.value === 'pdf') return 'application/pdf'
  if (fileType.value === 'slide') return '.pptx,.ppt,.pdf'
  if (fileType.value === 'note') return '.md,.txt,.docx,.pdf'
  return '*'
})

const fileHintText = computed(() => {
  if (fileType.value === 'video') return 'Supported: MP4, MOV, WEBM | Max size: 2 GB | Cloudinary Streaming CDN'
  if (fileType.value === 'pdf') return 'Supported: PDF | Max size: 100 MB | Dynamic Watermarking DRM Active'
  if (fileType.value === 'slide') return 'Supported: PPTX, PPT, PDF Decks | Max size: 200 MB | Web Slide Deck Viewer'
  if (fileType.value === 'note') return 'Supported: MD, TXT, DOCX | Max size: 50 MB | Markdown / Rich Text Engine'
  return 'Paste direct URL link from YouTube, Vimeo, Google Drive, or Cloudinary'
})

const placeholderTitle = computed(() => {
  if (fileType.value === 'video') return 'e.g. Introduction to C Programming Video'
  if (fileType.value === 'pdf') return 'e.g. C Syntax Reference & Cheat Sheet'
  if (fileType.value === 'slide') return 'e.g. Database Systems Lecture Deck'
  if (fileType.value === 'note') return 'e.g. Memory & Pointers Core Notes'
  return 'e.g. Vimeo Stream - Advanced Data Structures'
})

const triggerFileBrowse = () => {
  fileInputRef.value?.click()
}

const onFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    attachFile(target.files[0])
  }
}

const onFileDrop = (e: DragEvent) => {
  isDragging.value = false
  if (e.dataTransfer?.files && e.dataTransfer.files[0]) {
    attachFile(e.dataTransfer.files[0])
  }
}

const noteContent = ref('')

const attachFile = (file: File) => {
  const sizeMb = (file.size / (1024 * 1024)).toFixed(1)
  selectedFile.value = {
    name: file.name,
    size: `${sizeMb} MB`,
    raw: file
  }
  
  // 1. Auto set note title from file name without extension
  const fileNameWithoutExt = file.name.replace(/\.[^/.]+$/, "").replace(/_/g, " ")
  title.value = fileNameWithoutExt

  // 2. If .txt or .md file, auto-switch type to 'note' and read content using FileReader (100% Client-Side)
  const ext = file.name.split('.').pop()?.toLowerCase() || ''
  if (ext === 'txt' || ext === 'md' || ext === 'markdown' || file.type.includes('text')) {
    fileType.value = 'note'
    const reader = new FileReader()
    reader.onload = (event) => {
      noteContent.value = (event.target?.result as string) || ''
    }
    reader.readAsText(file, 'UTF-8')
  }
}

// Upload Progress Simulation
const uploading = ref(false)
const uploadProgress = ref(0)
const uploadFilesList = ref<Array<{ name: string; size: string; progress: number; done: boolean }>>([])

const handleUpload = (isDraft = false) => {
  uploading.value = true
  uploadProgress.value = 15

  const filename = selectedFile.value ? selectedFile.value.name : (title.value ? `${title.value}.${fileType.value}` : `New_${fileType.value}_file`)
  const filesize = selectedFile.value ? selectedFile.value.size : '14.5 MB'

  uploadFilesList.value = [
    { name: filename, size: filesize, progress: 30, done: false }
  ]

  const interval = setInterval(async () => {
    uploadProgress.value += 25
    if (uploadFilesList.value.length > 0) {
      uploadFilesList.value[0].progress = Math.min(100, uploadProgress.value)
    }

    if (uploadProgress.value >= 100) {
      clearInterval(interval)
      if (uploadFilesList.value.length > 0) uploadFilesList.value[0].done = true
      
      const rawFileObj = selectedFile.value?.raw
      const blobUrl = rawFileObj ? URL.createObjectURL(rawFileObj) : (externalUrl.value || undefined)
      const thumbUrl = thumbnailPreviewUrl.value || thumbnailUrlInput.value || undefined

      let txtNoteContent = undefined
      if (rawFileObj && (rawFileObj.name.endsWith('.txt') || rawFileObj.name.endsWith('.md') || rawFileObj.name.endsWith('.markdown') || rawFileObj.type.includes('text'))) {
        try {
          txtNoteContent = await rawFileObj.text()
        } catch (e) {
          console.warn('Could not read text file:', e)
        }
      }

      setTimeout(() => {
        uploading.value = false
        emit('uploaded', {
          title: title.value || (fileType.value.toUpperCase() + ' Asset'),
          file_name: filename,
          type: fileType.value,
          content: noteContent.value || txtNoteContent,
          status: isDraft ? 'Draft' : 'Published',
          size_formatted: filesize,
          course: selectedCourse.value,
          module: selectedModule.value,
          chapter: selectedChapter.value,
          teacher: 'Mr. Sophea',
          teacher_avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
          raw_file: rawFileObj,
          file_url: blobUrl,
          slide_url: blobUrl,
          pdf_url: (rawFileObj && rawFileObj.name.toLowerCase().endsWith('.pdf')) ? blobUrl : undefined,
          video_url: fileType.value === 'video' ? blobUrl : undefined,
          url: externalUrl.value || blobUrl,
          thumbnail_url: thumbUrl,
          thumbnail: thumbUrl || (fileType.value === 'video' ? '🎥' : fileType.value === 'pdf' ? '📄' : fileType.value === 'slide' ? '📊' : fileType.value === 'note' ? '📝' : '🔗')
        })
        emit('close')
      }, 350)
    }
  }, 250)
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900/95 rounded-3xl shadow-2xl border border-slate-800/80 w-full max-w-4xl overflow-hidden my-6 transform transition-all text-slate-100 backdrop-blur-md">
      
      <!-- Hidden File Inputs -->
      <input 
        ref="fileInputRef" 
        type="file" 
        :accept="fileAcceptTypes" 
        class="hidden" 
        @change="onFileChange" 
      />
      <input 
        ref="thumbnailInputRef" 
        type="file" 
        accept="image/*" 
        class="hidden" 
        @change="onThumbnailFileChange" 
      />

      <!-- Clean & Soothing Header -->
      <div class="bg-slate-900/90 px-6 py-4 text-white flex items-center justify-between border-b border-slate-800/80">
        <div class="flex items-center gap-3.5 min-w-0">
          <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center shrink-0 shadow-sm">
            <svg class="w-5 h-5 text-indigo-400" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 20C9.79086 20 8 21.7909 8 24C8 26.2091 9.79086 28 12 28H25C27.7614 28 30 25.7614 30 23C30 20.3705 27.9744 18.2144 25.3995 18.0267C24.7176 13.5042 20.8037 10 16 10C11.5817 10 7.87677 12.9238 6.64993 17.0261C4.01955 17.5855 2 19.9271 2 22.75C2 25.9256 4.57436 28.5 7.75 28.5" stroke="#6366F1" stroke-width="2.5" stroke-linecap="round"/>
              <path d="M18 22V14M18 14L14 18M18 14L22 18" stroke="#818CF8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>

          <div class="min-w-0">
            <h3 class="text-sm font-semibold text-white uppercase tracking-wider">
              UPLOAD NEW CONTENT
            </h3>
            <p class="text-[10px] text-slate-400/80 mt-0.5 font-normal tracking-wide">
              Upload Video, PDF, Slide, Note, or URL Link
            </p>
          </div>
        </div>

        <button 
          @click="$emit('close')" 
          class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-800/60 hover:bg-slate-700/80 text-slate-400 hover:text-white border border-slate-700/50 text-lg font-bold transition-all duration-200 cursor-pointer shrink-0 ml-4"
          title="Close"
        >
          ×
        </button>
      </div>

      <!-- Modal Body (2-Column Grid Layout for Compact Zero-Scroll Experience) -->
      <div class="p-6 space-y-5 text-xs text-slate-200 max-h-[78vh] overflow-y-auto">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">
          
          <!-- LEFT COLUMN: Content Type, Upload/URL Zone, Title & Description -->
          <div class="space-y-4">
            
            <!-- Select Content Type Chips -->
            <div class="bg-slate-800/40 p-3.5 rounded-2xl border border-slate-700/40 backdrop-blur-xs">
              <label class="block font-semibold text-slate-200 text-xs mb-2">Select Content Type:</label>
              <div class="grid grid-cols-5 gap-2">
                <button 
                  @click="fileType = 'video'" 
                  :class="[
                    'p-2.5 rounded-xl font-semibold border transition-all flex flex-col items-center gap-1 cursor-pointer text-[11px]', 
                    fileType === 'video' ? 'bg-blue-600/20 border-blue-500/80 text-white shadow-xs' : 'bg-slate-900/60 border-slate-800 text-slate-300 hover:bg-slate-900 hover:text-white'
                  ]"
                >
                  <span class="text-lg">🎥</span>
                  <span>Video</span>
                </button>

                <button 
                  @click="fileType = 'pdf'" 
                  :class="[
                    'p-2.5 rounded-xl font-semibold border transition-all flex flex-col items-center gap-1 cursor-pointer text-[11px]', 
                    fileType === 'pdf' ? 'bg-emerald-600/20 border-emerald-500/80 text-white shadow-xs' : 'bg-slate-900/60 border-slate-800 text-slate-300 hover:bg-slate-900 hover:text-white'
                  ]"
                >
                  <span class="text-lg">📄</span>
                  <span>PDF</span>
                </button>

                <button 
                  @click="fileType = 'slide'" 
                  :class="[
                    'p-2.5 rounded-xl font-semibold border transition-all flex flex-col items-center gap-1 cursor-pointer text-[11px]', 
                    fileType === 'slide' ? 'bg-purple-600/20 border-purple-500/80 text-white shadow-xs' : 'bg-slate-900/60 border-slate-800 text-slate-300 hover:bg-slate-900 hover:text-white'
                  ]"
                >
                  <span class="text-lg">📊</span>
                  <span>Slide</span>
                </button>

                <button 
                  @click="fileType = 'note'" 
                  :class="[
                    'p-2.5 rounded-xl font-semibold border transition-all flex flex-col items-center gap-1 cursor-pointer text-[11px]', 
                    fileType === 'note' ? 'bg-amber-600/20 border-amber-500/80 text-white shadow-xs' : 'bg-slate-900/60 border-slate-800 text-slate-300 hover:bg-slate-900 hover:text-white'
                  ]"
                >
                  <span class="text-lg">📝</span>
                  <span>Note</span>
                </button>

                <button 
                  @click="fileType = 'link'" 
                  :class="[
                    'p-2.5 rounded-xl font-semibold border transition-all flex flex-col items-center gap-1 cursor-pointer text-[11px]', 
                    fileType === 'link' ? 'bg-sky-600/20 border-sky-500/80 text-white shadow-xs' : 'bg-slate-900/60 border-slate-800 text-slate-300 hover:bg-slate-900 hover:text-white'
                  ]"
                >
                  <span class="text-lg">🔗</span>
                  <span>URL Link</span>
                </button>
              </div>
            </div>

            <!-- Drag & Drop Zone (ONLY for Video, PDF, Slide, Note) -->
            <div v-if="fileType !== 'link'" 
              @click="triggerFileBrowse"
              @dragover.prevent="isDragging = true"
              @dragleave.prevent="isDragging = false"
              @drop.prevent="onFileDrop"
              :class="[
                'border-2 border-dashed rounded-2xl p-5 text-center transition-all cursor-pointer group shadow-inner',
                isDragging ? 'border-indigo-400 bg-indigo-950/40' : selectedFile ? 'border-emerald-500/80 bg-emerald-950/20' : 'border-slate-700/60 hover:border-indigo-500/80 bg-slate-800/30'
              ]"
            >
              <div v-if="selectedFile" class="flex items-center justify-center gap-3">
                <span class="text-2xl">✅</span>
                <div class="text-left">
                  <p class="font-semibold text-white text-xs line-clamp-1">{{ selectedFile.name }}</p>
                  <p class="text-emerald-400 text-[11px] font-mono font-medium mt-0.5">Size: {{ selectedFile.size }} — Ready</p>
                </div>
              </div>
              <div v-else>
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 mx-auto flex items-center justify-center text-xl group-hover:scale-110 transition-all">
                  📁
                </div>
                <p class="font-semibold text-slate-200 mt-2 text-xs">Drag & Drop file here or <span class="text-indigo-400 underline">Browse File</span></p>
                <p class="text-slate-400/80 text-[10px] mt-0.5">{{ fileHintText }}</p>
              </div>
            </div>

            <!-- Tip Banner for Slide PDF Conversion (Slide only) -->
            <div v-if="fileType === 'slide'" class="p-3 bg-purple-950/30 border border-purple-500/20 rounded-xl text-purple-200 text-[11px] space-y-1">
              <div class="flex items-center gap-1.5 font-semibold text-purple-300 text-[11px]">
                <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>💡 Tip for Large PPTX Decks:</span>
              </div>
              <p class="text-slate-300 leading-relaxed text-[10.5px]">
                Save As <strong class="text-purple-300">.pdf</strong> in PowerPoint before uploading for instant HD slide rendering.
              </p>
            </div>

            <!-- External URL Link Input Box (ONLY when URL Link is selected) -->
            <div v-if="fileType === 'link'" class="space-y-1.5 bg-slate-800/40 p-3.5 rounded-2xl border border-slate-700/40">
              <label class="block font-semibold text-indigo-300 text-xs">External URL Link Target:</label>
              <input 
                v-model="externalUrl"
                type="url" 
                placeholder="https://vimeo.com/video-id or https://drive.google.com/file/..." 
                class="w-full px-3 py-2 bg-slate-900/80 border border-slate-700/60 rounded-xl text-white font-medium text-xs focus:ring-2 focus:ring-indigo-500/50 outline-none" 
              />
              <p class="text-slate-400/80 text-[10px]">{{ fileHintText }}</p>
            </div>

            <!-- Progress Box (Simulated when uploading) -->
            <div v-if="uploading" class="bg-slate-800/60 p-3.5 rounded-2xl border border-slate-700/60 space-y-2.5">
              <div class="flex justify-between font-semibold text-xs">
                <span class="text-indigo-400 flex items-center gap-2">
                  <span class="animate-spin">⏳</span> Uploading asset...
                </span>
                <span class="font-mono text-emerald-400">{{ uploadProgress }}%</span>
              </div>
              <div class="w-full bg-slate-900 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-emerald-400 h-2 rounded-full transition-all duration-300" :style="{ width: uploadProgress + '%' }"></div>
              </div>
            </div>

            <!-- Meta Information Card -->
            <div class="space-y-3 bg-slate-800/40 p-4 rounded-2xl border border-slate-700/40 backdrop-blur-xs">
              <h4 class="font-bold text-slate-200 text-xs border-b border-slate-800/80 pb-2 uppercase tracking-wider">META INFORMATION</h4>
              <div>
                <label class="block font-semibold text-slate-200 mb-1">Content Title:</label>
                <input v-model="title" type="text" :placeholder="placeholderTitle" class="w-full px-3 py-2 bg-slate-900/80 border border-slate-700/60 rounded-xl text-white font-medium text-xs focus:ring-2 focus:ring-indigo-500/50 outline-none placeholder:text-slate-500" />
              </div>
              <div>
                <label class="block font-semibold text-slate-200 mb-1">Description:</label>
                <textarea v-model="description" rows="2" placeholder="Brief summary of what students will learn..." class="w-full px-3 py-1.5 bg-slate-900/80 border border-slate-700/60 rounded-xl text-slate-200 text-xs focus:ring-2 focus:ring-indigo-500/50 outline-none placeholder:text-slate-500"></textarea>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block font-semibold text-slate-200 mb-1">Tags:</label>
                  <input v-model="tags" type="text" class="w-full px-3 py-1.5 bg-slate-900/80 border border-slate-700/60 rounded-xl text-slate-200 font-medium text-xs outline-none" />
                </div>
                <div>
                  <label class="block font-semibold text-slate-200 mb-1">Language:</label>
                  <select class="w-full px-3 py-1.5 bg-slate-900/80 border border-slate-700/60 rounded-xl text-slate-200 font-medium text-xs outline-none cursor-pointer">
                    <option>Khmer + English</option>
                    <option>Khmer Only</option>
                    <option>English Only</option>
                  </select>
                </div>
              </div>
            </div>

          </div>

          <!-- RIGHT COLUMN: Lesson Thumbnail, Course Assignment, Access & Security -->
          <div class="space-y-4">
            
            <!-- Thumbnail Section -->
            <div class="bg-slate-800/40 p-4 rounded-2xl border border-slate-700/40 space-y-3 backdrop-blur-xs">
              <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                <h4 class="font-bold text-slate-200 text-xs uppercase tracking-wider">LESSON THUMBNAIL</h4>
                <button v-if="thumbnailPreviewUrl || thumbnailUrlInput" @click="clearThumbnailImage" type="button" class="text-[10px] text-rose-400 hover:underline font-medium cursor-pointer">
                  ✕ Clear Image
                </button>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
                <!-- Thumbnail Preview Box -->
                <div 
                  @click="triggerThumbnailBrowse" 
                  class="relative bg-slate-900 border-2 border-dashed border-slate-700 hover:border-indigo-500 rounded-xl h-24 flex flex-col items-center justify-center text-center overflow-hidden cursor-pointer group shadow-inner transition-all"
                >
                  <img v-if="thumbnailPreviewUrl || thumbnailUrlInput" :src="thumbnailPreviewUrl || thumbnailUrlInput" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-all" />
                  <div v-else class="p-2 space-y-0.5">
                    <span class="text-xl block group-hover:scale-110 transition-all">🖼️</span>
                    <p class="text-[10px] font-medium text-indigo-400 underline">Upload Cover</p>
                  </div>
                </div>

                <!-- Upload Option Controls -->
                <div class="sm:col-span-2 space-y-2 text-xs">
                  <div>
                    <label class="block font-semibold text-slate-300 text-[11px] mb-1">Image URL Link (Optional):</label>
                    <input 
                      v-model="thumbnailUrlInput" 
                      type="url" 
                      placeholder="https://images.unsplash.com/..." 
                      class="w-full px-2.5 py-1.5 bg-slate-900/80 border border-slate-700/60 rounded-xl text-white text-[11px] focus:ring-2 focus:ring-indigo-500/50 outline-none font-mono placeholder:text-slate-500" 
                    />
                  </div>
                  <div class="flex items-center gap-2">
                    <button 
                      type="button"
                      @click="triggerThumbnailBrowse" 
                      class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-indigo-300 border border-slate-700/60 rounded-xl font-medium text-[11px] flex items-center gap-1 transition-all cursor-pointer"
                    >
                      <span>📁 Browse Cover...</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Course Assignment -->
            <div class="bg-slate-800/40 p-4 rounded-2xl border border-slate-700/40 space-y-2.5 backdrop-blur-xs">
              <h4 class="font-bold text-slate-200 text-xs border-b border-slate-800/80 pb-2 uppercase tracking-wider">COURSE ASSIGNMENT</h4>
              <div>
                <label class="block font-semibold text-slate-300 mb-1">Select Course:</label>
                <select v-model="selectedCourse" class="w-full px-3 py-2 bg-slate-900/80 border border-slate-700/60 rounded-xl text-white font-medium text-xs outline-none cursor-pointer">
                  <option value="C Programming Basics">C Programming Basics</option>
                  <option value="Database Systems & SQL">Database Systems & SQL</option>
                  <option value="Tourism Management Principles">Tourism Management Principles</option>
                </select>
              </div>
              <div>
                <label class="block font-semibold text-slate-300 mb-1">Select Module:</label>
                <select v-model="selectedModule" class="w-full px-3 py-2 bg-slate-900/80 border border-slate-700/60 rounded-xl text-white font-medium text-xs outline-none cursor-pointer">
                  <option v-for="mod in availableModules" :key="mod" :value="mod">{{ mod }}</option>
                </select>
              </div>
              <div>
                <label class="block font-semibold text-slate-300 mb-1">Select Chapter:</label>
                <select v-model="selectedChapter" class="w-full px-3 py-2 bg-slate-900/80 border border-slate-700/60 rounded-xl text-white font-medium text-xs outline-none cursor-pointer">
                  <option v-for="chap in availableChapters" :key="chap" :value="chap">{{ chap }}</option>
                </select>
              </div>
            </div>

            <!-- Access & Security -->
            <div class="bg-slate-800/40 p-4 rounded-2xl border border-slate-700/40 space-y-2.5 backdrop-blur-xs">
              <h4 class="font-bold text-slate-200 text-xs border-b border-slate-800/80 pb-2 uppercase tracking-wider">ACCESS & SECURITY</h4>
              <div>
                <label class="block font-semibold text-slate-300 mb-1">Visibility:</label>
                <select v-model="visibility" class="w-full px-3 py-2 bg-slate-900/80 border border-slate-700/60 rounded-xl text-white font-medium text-xs outline-none cursor-pointer">
                  <option>Enrolled Students Only</option>
                  <option>Public Preview</option>
                  <option>Locked / Paid Students Only</option>
                </select>
              </div>
              <div class="space-y-2 pt-1 text-slate-300 font-medium">
                <label class="flex items-center gap-2.5 cursor-pointer">
                  <input type="checkbox" v-model="allowPreview" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-indigo-500 focus:ring-indigo-500/30 cursor-pointer" />
                  <span>Free 2-minute preview for guests</span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer">
                  <input type="checkbox" v-model="requiresPayment" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-indigo-500 focus:ring-indigo-500/30 cursor-pointer" />
                  <span>Payment verification required for full access</span>
                </label>
              </div>
            </div>

          </div>

        </div>

      </div>

      <!-- Refined Action Footer with Clear Button Hierarchy -->
      <div class="bg-slate-900/90 px-6 py-3.5 border-t border-slate-800/80 flex items-center justify-between">
        <!-- Ghost Cancel Button -->
        <button 
          @click="$emit('close')" 
          class="px-3.5 py-2 text-slate-400 hover:text-white hover:bg-slate-800/60 font-medium text-xs rounded-xl transition-all duration-200 cursor-pointer"
        >
          Cancel
        </button>

        <div class="flex items-center gap-2">
          <!-- Ghost Action with Eye Icon for Test Preview -->
          <button 
            @click="triggerInstantPreview" 
            class="px-3.5 py-2 text-slate-300 hover:text-white hover:bg-slate-800/60 font-medium text-xs rounded-xl transition-all duration-200 cursor-pointer flex items-center gap-1.5"
          >
            <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <span>Test Preview</span>
          </button>

          <!-- Secondary Solid Button for Save Draft -->
          <button 
            @click="handleUpload(true)" 
            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700/80 shadow-xs transition-all duration-200 cursor-pointer"
          >
            Save Draft
          </button>

          <!-- Primary Solid Gradient Accent Button for Upload & Publish -->
          <button 
            @click="handleUpload(false)" 
            class="px-5 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-blue-500/20 active:scale-95 transition-all duration-200 cursor-pointer"
          >
            Upload & Publish
          </button>
        </div>
      </div>

    </div>
  </div>
</template>
