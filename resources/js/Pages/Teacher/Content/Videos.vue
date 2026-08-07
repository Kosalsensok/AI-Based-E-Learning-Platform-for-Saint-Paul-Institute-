<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  courses: Array<any>
  videos: Array<any>
}>()

const searchQuery = ref('')
const selectedCourse = ref('')
const statusFilter = ref('All')
const viewMode = ref<'table' | 'cards'>('table')
const unlinkedOnly = ref(false)

// Sample Video Library Data with exact UI states requested
const sampleVideos = ref([
  {
    id: 1,
    title: '01_Introduction.mp4',
    title_kh: 'ការណែនាំអំពី C Programming',
    title_en: 'Introduction to C Programming',
    course: 'C Programming Basics',
    module: 'Module 1: Introduction',
    chapter: 'Chapter 1.1: History of C',
    duration: '12:45',
    size: '128MB',
    status: 'Ready',
    sub_kh: 'kh_intro.vtt',
    sub_en: 'en_intro.vtt',
    quality: '1080p / 720p / 480p',
    downloadable: false,
    paid_required: true,
  },
  {
    id: 2,
    title: '02_Variables.mp4',
    title_kh: 'អថេរ និងប្រភេទទិន្នន័យ',
    title_en: 'Variables and Data Types',
    course: 'C Programming Basics',
    module: 'Module 2: Variables',
    chapter: 'Chapter 2.1: Data Types',
    duration: '18:30',
    size: '156MB',
    status: 'Ready',
    sub_kh: 'kh_variables.vtt',
    sub_en: 'en_variables.vtt',
    quality: '1080p / 720p / 480p',
    downloadable: false,
    paid_required: true,
  },
  {
    id: 3,
    title: '03_Loops.mp4',
    title_kh: 'រង្វិលជុំ Loops',
    title_en: 'Control Flow Loops',
    course: 'C Programming Basics',
    module: 'Module 3: Loops',
    chapter: 'Chapter 3.1: For Loop',
    duration: '--:--',
    size: '180MB',
    status: 'Encoding',
    sub_kh: null,
    sub_en: null,
    quality: 'Processing...',
    downloadable: false,
    paid_required: true,
  },
  {
    id: 4,
    title: '04_Functions.mp4',
    title_kh: 'អនុគមន៍ Functions',
    title_en: 'C Functions & Scope',
    course: 'C Programming Basics',
    module: 'Module 4: Functions',
    chapter: 'Chapter 4.1: Functions',
    duration: '--:--',
    size: '134MB',
    status: 'Failed',
    sub_kh: null,
    sub_en: null,
    quality: 'Error',
    downloadable: false,
    paid_required: true,
  }
])

const filteredVideos = computed(() => {
  return sampleVideos.value.filter(v => {
    const matchSearch = v.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        v.title_kh.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchStatus = statusFilter.value === 'All' || v.status === statusFilter.value
    return matchSearch && matchStatus
  })
})

// Upload Video Modal State & Form
const showUploadModal = ref(false)
const uploadForm = useForm({
  course_id: null,
  module_id: null,
  chapter_id: null,
  title_kh: '',
  title_en: '',
  description: '',
  tags: '#C #Programming #Beginner',
  auto_thumbnail: true,
  convert_qualities: true,
  allow_subtitles: true,
  allow_download: false,
  auto_add_chapter: true,
})

const submitUpload = () => {
  alert('Video upload started! Cloud CDN encoding in progress...')
  showUploadModal.value = false
  uploadForm.reset()
}

// Edit & Subtitles Screen Modal
const showEditModal = ref(false)
const editingVideo = ref<any>(null)
const openEdit = (v: any) => {
  editingVideo.value = v
  showEditModal.value = true
}

const generateAiSubtitle = () => {
  alert('AI is processing voice-to-text transcript for Khmer & English subtitles...')
}

const translateSubtitles = () => {
  alert('AI Translation EN ⇄ KH complete!')
}

// Video Preview Player Modal
const showPlayerModal = ref(false)
const playingVideo = ref<any>(null)
const openPlayer = (v: any) => {
  playingVideo.value = v
  showPlayerModal.value = true
}

const retryEncoding = (v: any) => {
  v.status = 'Encoding'
  alert(`Retrying video encoding for ${v.title}...`)
}
</script>

<template>
  <div class="space-y-5">
    <!-- Top Controls Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-4 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm">
      <div class="flex flex-wrap items-center gap-3">
        <!-- Course Dropdown -->
        <select v-model="selectedCourse" class="p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-semibold">
          <option value="">C Programming Basics ▼</option>
          <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
        </select>

        <!-- Search Bar -->
        <div class="relative">
          <i class="pi pi-search absolute left-3 top-3 text-slate-400 text-xs"></i>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search video..."
            class="pl-8 pr-3 py-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs focus:ring-2 focus:ring-blue-500 w-48"
          />
        </div>

        <!-- Filter Dropdowns -->
        <select v-model="statusFilter" class="p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs font-semibold">
          <option value="All">All Status ▼</option>
          <option value="Ready">Ready 🟢</option>
          <option value="Encoding">Encoding 🟡</option>
          <option value="Failed">Failed 🔴</option>
        </select>

        <label class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 cursor-pointer">
          <input v-model="unlinkedOnly" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
          <span>Unlinked Only</span>
        </label>
      </div>

      <div class="flex items-center gap-2">
        <!-- View Toggle Buttons -->
        <div class="flex items-center p-1 bg-slate-100 dark:bg-gray-700 rounded-xl">
          <button
            @click="viewMode = 'table'"
            :class="['px-2.5 py-1.5 rounded-lg text-xs font-bold transition', viewMode === 'table' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-500']"
            title="Table View"
          >
            <i class="pi pi-list"></i>
          </button>
          <button
            @click="viewMode = 'cards'"
            :class="['px-2.5 py-1.5 rounded-lg text-xs font-bold transition', viewMode === 'cards' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-500']"
            title="Card View"
          >
            <i class="pi pi-th-large"></i>
          </button>
        </div>

        <!-- Upload Video Button -->
        <button
          @click="showUploadModal = true"
          class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 transition flex items-center gap-2"
        >
          <i class="pi pi-cloud-upload"></i>
          <span>+ Upload Video</span>
        </button>
      </div>
    </div>

    <!-- VIEW 1: TABLE VIEW -->
    <div v-if="viewMode === 'table'" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 dark:bg-gray-700/50 text-slate-500 uppercase tracking-wider border-b border-slate-200/80 dark:border-gray-700">
            <th class="p-3.5">Thumbnail</th>
            <th class="p-3.5">Video Title</th>
            <th class="p-3.5">Duration</th>
            <th class="p-3.5">Size</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
          <tr v-for="v in filteredVideos" :key="v.id" class="hover:bg-slate-50/50 dark:hover:bg-gray-700/30 transition">
            <td class="p-3.5">
              <div
                @click="v.status === 'Ready' && openPlayer(v)"
                class="w-20 h-12 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs shadow-inner cursor-pointer relative overflow-hidden group border border-slate-700"
              >
                <span v-if="v.status === 'Ready'" class="group-hover:scale-125 transition">🎬 ▶</span>
                <span v-else-if="v.status === 'Encoding'" class="text-amber-400 animate-spin">⏳</span>
                <span v-else class="text-rose-400">❌</span>
              </div>
            </td>
            <td class="p-3.5">
              <p class="font-bold text-slate-800 dark:text-white">{{ v.title }}</p>
              <p class="text-[11px] text-blue-600 dark:text-blue-400 font-medium">{{ v.title_kh }}</p>
              <p class="text-[10px] text-slate-400">{{ v.module }} • {{ v.chapter }}</p>
            </td>
            <td class="p-3.5 font-medium text-slate-600 dark:text-slate-300">{{ v.duration }}</td>
            <td class="p-3.5 text-slate-500">{{ v.size }}</td>
            <td class="p-3.5">
              <span
                class="px-2.5 py-1 font-bold rounded-full text-[10px]"
                :class="{
                  'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300': v.status === 'Ready',
                  'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 animate-pulse': v.status === 'Encoding',
                  'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300': v.status === 'Failed',
                }"
              >
                {{ v.status === 'Ready' ? '🟢 Ready' : (v.status === 'Encoding' ? '🟡 Encoding' : '🔴 Failed') }}
              </span>
            </td>
            <td class="p-3.5 text-right space-x-1.5">
              <button v-if="v.status === 'Ready'" @click="openPlayer(v)" class="px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-bold">▶ Preview</button>
              <button v-if="v.status === 'Ready'" @click="openEdit(v)" class="px-2.5 py-1 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg font-semibold">✏ Edit</button>
              <button v-if="v.status === 'Failed'" @click="retryEncoding(v)" class="px-2.5 py-1 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded-lg font-bold">🔄 Retry</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- VIEW 2: CARD VIEW -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <div
        v-for="v in filteredVideos"
        :key="v.id"
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 overflow-hidden hover:shadow-md transition space-y-3 p-4"
      >
        <!-- Video Card Thumbnail -->
        <div
          @click="v.status === 'Ready' && openPlayer(v)"
          class="w-full h-36 rounded-xl bg-slate-900 text-white flex flex-col items-center justify-center font-bold text-sm shadow-inner cursor-pointer relative overflow-hidden group border border-slate-700"
        >
          <span v-if="v.status === 'Ready'" class="text-2xl group-hover:scale-125 transition">🎬 ▶</span>
          <span v-else-if="v.status === 'Encoding'" class="text-amber-400 animate-spin text-2xl">⏳</span>
          <span v-else class="text-rose-400 text-2xl">❌</span>
          <span class="absolute bottom-2 right-2 px-2 py-0.5 bg-black/70 rounded text-[10px]">{{ v.duration }}</span>
        </div>

        <div>
          <h3 class="font-bold text-sm text-slate-800 dark:text-white truncate">{{ v.title }}</h3>
          <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold truncate">{{ v.title_kh }}</p>
          <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ v.course }}</p>
        </div>

        <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-100 dark:border-gray-700">
          <span class="text-slate-500">Quality: {{ v.quality }}</span>
          <span
            class="px-2 py-0.5 font-bold rounded-full text-[10px]"
            :class="v.status === 'Ready' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
          >
            {{ v.status === 'Ready' ? '🟢 Ready' : '🟡 Encoding' }}
          </span>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button v-if="v.status === 'Ready'" @click="openPlayer(v)" class="flex-1 py-1.5 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700">▶ Preview</button>
          <button v-if="v.status === 'Ready'" @click="openEdit(v)" class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold hover:bg-slate-200">✏ Edit</button>
        </div>
      </div>
    </div>

    <!-- ☁️ UPLOAD VIDEO MODAL -->
    <div v-if="showUploadModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
            <span>☁️ Upload New Lesson Video</span>
          </h3>
          <button @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <!-- Drag & Drop Dropzone -->
        <div class="border-2 border-dashed border-blue-400 dark:border-blue-500/50 bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl p-6 text-center space-y-2 cursor-pointer">
          <i class="pi pi-cloud-upload text-3xl text-blue-600"></i>
          <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Drag & Drop video file here</p>
          <p class="text-[11px] text-slate-400">Support: MP4, MOV, AVI | Max File Size: 2GB</p>
          <button class="px-4 py-1.5 bg-blue-600 text-white rounded-xl text-xs font-bold shadow">Browse File</button>
        </div>

        <div class="space-y-3 text-xs">
          <div class="grid grid-cols-3 gap-3">
            <div>
              <label class="block font-semibold mb-1">Course</label>
              <select v-model="uploadForm.course_id" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700">
                <option :value="null">C Programming ▼</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">Module</label>
              <select v-model="uploadForm.module_id" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700">
                <option :value="null">Module 1: Intro ▼</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold mb-1">Chapter</label>
              <select v-model="uploadForm.chapter_id" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700">
                <option :value="null">Chapter 1.1: History ▼</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold mb-1">Title KH</label>
              <input v-model="uploadForm.title_kh" type="text" placeholder="ការណែនាំអំពី C Programming" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
            <div>
              <label class="block font-semibold mb-1">Title EN</label>
              <input v-model="uploadForm.title_en" type="text" placeholder="Introduction to C Programming" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
            </div>
          </div>

          <div>
            <label class="block font-semibold mb-1">Description</label>
            <textarea v-model="uploadForm.description" rows="2" placeholder="Write short lesson description..." class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700"></textarea>
          </div>

          <!-- Options Toggles -->
          <div class="p-3 bg-slate-50 dark:bg-gray-700/50 rounded-xl space-y-2">
            <p class="font-bold text-[11px] text-slate-500 uppercase">Video Options</p>
            <div class="grid grid-cols-2 gap-2">
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="uploadForm.auto_thumbnail" type="checkbox" class="rounded text-blue-600" />
                <span>☑️ Generate thumbnail automatically</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="uploadForm.convert_qualities" type="checkbox" class="rounded text-blue-600" />
                <span>☑️ Convert to 1080p / 720p / 480p</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="uploadForm.allow_subtitles" type="checkbox" class="rounded text-blue-600" />
                <span>☑️ Allow subtitles KH / EN</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="uploadForm.allow_download" type="checkbox" class="rounded text-blue-600" />
                <span>☐ Allow download for offline</span>
              </label>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-gray-700">
          <button @click="showUploadModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">❌ Cancel</button>
          <button @click="submitUpload" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow">☁️ Upload Video</button>
        </div>
      </div>
    </div>

    <!-- 🎬 EDIT VIDEO & SUBTITLE MODAL -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">🎬 Edit Video – {{ editingVideo?.title }}</h3>
          <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600"><i class="pi pi-times"></i></button>
        </div>

        <div class="space-y-4 text-xs">
          <!-- Subtitles Section -->
          <div class="p-4 rounded-xl bg-slate-50 dark:bg-gray-700/50 space-y-3">
            <h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
              <span>💬 Subtitle Management</span>
            </h4>
            <div class="flex items-center justify-between p-2.5 bg-white dark:bg-gray-800 rounded-lg border border-slate-200/80">
              <span>🇰🇭 Khmer Subtitle: <strong class="text-blue-600">{{ editingVideo?.sub_kh || 'kh_variables.vtt' }}</strong> ✅</span>
              <div class="space-x-1">
                <button class="px-2 py-1 bg-slate-100 text-slate-700 rounded font-semibold">Edit</button>
                <button class="px-2 py-1 bg-rose-50 text-rose-600 rounded font-semibold">Delete</button>
              </div>
            </div>
            <div class="flex items-center justify-between p-2.5 bg-white dark:bg-gray-800 rounded-lg border border-slate-200/80">
              <span>🇬🇧 English Subtitle: <strong class="text-indigo-600">{{ editingVideo?.sub_en || 'en_variables.vtt' }}</strong> ✅</span>
              <div class="space-x-1">
                <button class="px-2 py-1 bg-slate-100 text-slate-700 rounded font-semibold">Edit</button>
                <button class="px-2 py-1 bg-rose-50 text-rose-600 rounded font-semibold">Delete</button>
              </div>
            </div>

            <div class="flex gap-2 pt-1">
              <button @click="generateAiSubtitle" class="px-3 py-1.5 bg-purple-600 text-white rounded-lg font-bold shadow">🎙 Generate Subtitle by AI</button>
              <button @click="translateSubtitles" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg font-bold shadow">🌐 Translate EN ⇄ KH</button>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-gray-700">
          <button @click="showEditModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Close</button>
          <button @click="showEditModal = false" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold shadow">💾 Save Changes</button>
        </div>
      </div>
    </div>

    <!-- ▶ VIDEO PREVIEW PLAYER MODAL -->
    <div v-if="showPlayerModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-3xl w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">▶ Video Player Preview: {{ playingVideo?.title }}</h3>
          <button @click="showPlayerModal = false" class="text-slate-400 hover:text-white"><i class="pi pi-times text-lg"></i></button>
        </div>

        <div class="h-80 bg-slate-950 rounded-2xl flex flex-col items-center justify-center text-white relative overflow-hidden">
          <span class="text-4xl animate-bounce">🎬</span>
          <p class="font-bold text-sm mt-2">{{ playingVideo?.title_kh }}</p>
          <p class="text-xs text-slate-400 mt-1">[ SIMULATED 1080P VIDEO STREAM PLAYER ]</p>
          
          <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between bg-black/60 p-2.5 rounded-xl text-xs">
            <span>▶ 02:15 / {{ playingVideo?.duration }}</span>
            <div class="flex gap-2">
              <span class="px-2 py-0.5 bg-blue-600 rounded font-bold">CC: 🇰🇭 Khmer</span>
              <span class="px-2 py-0.5 bg-slate-700 rounded font-bold">Speed: 1x</span>
              <span class="px-2 py-0.5 bg-slate-700 rounded font-bold">Quality: 1080p</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
