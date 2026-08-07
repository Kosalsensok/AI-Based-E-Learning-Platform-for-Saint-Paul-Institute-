<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { 
  Package, 
  BookOpen, 
  Video, 
  FileText, 
  Presentation, 
  StickyNote, 
  FolderTree, 
  DownloadCloud 
} from 'lucide-vue-next'

// Sub-modules
import ContentLibrary from './ContentLibrary.vue'
import Videos from './Videos.vue'
import Pdfs from './Pdfs.vue'
import Slides from './Slides.vue'
import NotesDocuments from './NotesDocuments.vue'
import ModulesChapters from './ModulesChapters.vue'
import OfflineContent from './OfflineContent.vue'

// Modals
import UniversalUploadModal from './UniversalUploadModal.vue'
import VideoPlayerModal from './VideoPlayerModal.vue'
import PdfViewerModal from './PdfViewerModal.vue'
import SlideViewerModal from './SlideViewerModal.vue'
import NoteEditorModal from './NoteEditorModal.vue'
import LinkViewerModal from './LinkViewerModal.vue'
import ModuleChapterModal from './ModuleChapterModal.vue'
import OfflinePackageModal from './OfflinePackageModal.vue'
import LessonPreviewEditorModal from './LessonPreviewEditorModal.vue'

const props = withDefaults(defineProps<{
  tab?: string
  stats?: any
  contentItems?: Array<any>
  courseModules?: Array<any>
  offlinePackages?: Array<any>
  courses?: Array<any>
  teachers?: Array<any>
  majors?: Array<any>
}>(), {
  tab: 'library',
  stats: () => ({}),
  contentItems: () => [],
  courseModules: () => [],
  offlinePackages: () => [],
  courses: () => [],
  teachers: () => [],
  majors: () => []
})

const activeTab = ref(props.tab || 'library')

const typeToTabMap: Record<string, string> = {
  All: 'library',
  video: 'videos',
  pdf: 'pdfs',
  slide: 'slides',
  note: 'notes'
}

const tabToTypeMap: Record<string, string> = {
  library: 'All',
  videos: 'video',
  pdfs: 'pdf',
  slides: 'slide',
  notes: 'note'
}

const currentAssetType = computed(() => tabToTypeMap[activeTab.value] || 'All')

const handleTypeChange = (newType: string) => {
  const targetTab = typeToTabMap[newType] || 'library'
  if (targetTab !== activeTab.value) {
    activeTab.value = targetTab
    router.get('/admin/content', { tab: targetTab }, { preserveState: true, replace: true })
  }
}

const switchTab = (tabKey: string) => {
  activeTab.value = tabKey
  router.get('/admin/content', { tab: tabKey }, { preserveState: true, replace: true })
}

// Modal State Management
const showUploadModal = ref(false)
const showVideoModal = ref(false)
const showPdfModal = ref(false)
const showSlideModal = ref(false)
const showNoteModal = ref(false)
const showLinkModal = ref(false)
const showModuleModal = ref(false)
const showOfflineModal = ref(false)
const showLessonEditorModal = ref(false)

const activeItem = ref<any>(null)
const moduleModalMode = ref<'module' | 'chapter'>('module')

const handleOpenPreview = (item: any) => {
  activeItem.value = item
  if (!item) return
  if (item.type === 'video') {
    showVideoModal.value = true
  } else if (item.type === 'pdf') {
    showPdfModal.value = true
  } else if (item.type === 'slide') {
    showSlideModal.value = true
  } else if (item.type === 'note') {
    showNoteModal.value = true
  } else if (item.type === 'link') {
    showLinkModal.value = true
  } else {
    showLessonEditorModal.value = true
  }
}

const handleOpenOfflineModal = (pkg?: any) => {
  activeItem.value = pkg || null
  showOfflineModal.value = true
}

const handleOfflinePackageSaved = (savedPackage: any) => {
  if (props.offlinePackages) {
    const idx = props.offlinePackages.findIndex(p => p.package_name === savedPackage.package_name || p.course === savedPackage.course)
    if (idx !== -1) {
      props.offlinePackages[idx] = {
        ...props.offlinePackages[idx],
        ...savedPackage,
        updated_at: 'Just now'
      }
    } else {
      props.offlinePackages.unshift({
        id: Date.now(),
        course: savedPackage.course,
        package_name: savedPackage.package_name,
        size_formatted: '1.25 GB',
        download_count: 0,
        encrypted: true,
        pwa_cached: true,
        updated_at: 'Just now',
        status: 'Active'
      })
    }
  }
}

const handleOpenModuleModal = (payload: { mode: 'module' | 'chapter', item?: any }) => {
  moduleModalMode.value = payload.mode
  activeItem.value = payload.item || null
  showModuleModal.value = true
}

const handleOpenNoteEditor = (note?: any) => {
  activeItem.value = note || null
  showNoteModal.value = true
}

const handleAssetUploaded = (newItem: any) => {
  const itemObj = {
    id: Date.now(),
    title: newItem.title,
    file_name: newItem.file_name || `${newItem.title}.${newItem.type}`,
    type: newItem.type,
    content: newItem.content,
    course: newItem.course || 'C Programming Basics',
    teacher: newItem.teacher || 'Mr. Sophea',
    teacher_avatar: newItem.teacher_avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
    size_formatted: newItem.size_formatted || '12.4 MB',
    raw_file: newItem.raw_file,
    file_url: newItem.file_url,
    pdf_url: newItem.pdf_url,
    video_url: newItem.video_url,
    url: newItem.url,
    thumbnail: newItem.thumbnail || '📝',
    thumbnail_url: newItem.thumbnail_url || (typeof newItem.thumbnail === 'string' && (newItem.thumbnail.startsWith('data:') || newItem.thumbnail.startsWith('http')) ? newItem.thumbnail : undefined),
    status: newItem.status || 'Published',
    duration: newItem.type === 'video' ? '12:45 min' : undefined
  }

  if (props.contentItems) {
    props.contentItems.unshift(itemObj)
  }
  
  if (props.stats) {
    props.stats.total_items++
    if (newItem.type === 'video') props.stats.videos_count++
    else if (newItem.type === 'pdf') props.stats.pdfs_count++
    else if (newItem.type === 'slide') props.stats.slides_count++
    else if (newItem.type === 'note') props.stats.notes_count++

    if (newItem.status === 'Published') props.stats.published_count++
    else props.stats.draft_count++
  }

  // If uploading a note, automatically open NoteEditorModal to preview/edit the text content!
  if (newItem.type === 'note') {
    handleOpenNoteEditor(itemObj)
  }
}

const handleNoteSaved = (savedNote: any) => {
  if (props.contentItems) {
    const idx = props.contentItems.findIndex(n => n.id === savedNote.id || (n.title === savedNote.title && n.type === 'note'))
    if (idx !== -1) {
      props.contentItems[idx] = { ...props.contentItems[idx], ...savedNote }
    } else {
      const newNoteObj = {
        id: Date.now(),
        title: savedNote.title,
        file_name: savedNote.file_name || `${savedNote.title}.txt`,
        type: 'note',
        content: savedNote.content,
        course: savedNote.course || 'C Programming Basics',
        teacher: 'Mr. Sophea',
        teacher_avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
        size_formatted: '2.4 KB',
        thumbnail: '📝',
        status: savedNote.status || 'Published'
      }
      props.contentItems.unshift(newNoteObj)
      if (props.stats) {
        props.stats.total_items++
        props.stats.notes_count++
        if (savedNote.status === 'Published') props.stats.published_count++
        else props.stats.draft_count++
      }
    }
  }
}
</script>

<template>
  <AdminLayout title="Content Delivery Module">
    <div class="p-6 space-y-6 text-slate-100 min-h-screen bg-[#0b1120] relative overflow-hidden">
      
      <!-- Soft Ambient Background Glows for Eye-Soothing Atmosphere -->
      <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>
      <div class="absolute top-1/4 -right-40 w-96 h-96 bg-sky-600/10 rounded-full blur-[120px] pointer-events-none"></div>

      <!-- Top Header Card (Executive Glassmorphism Standard Frame) -->
      <div class="bg-slate-900/80 backdrop-blur-xl p-4 sm:p-5 rounded-2xl border border-slate-800/80 shadow-md flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10">
        
        <!-- Left Side: Icon, Module Title & Status Badge -->
        <div class="flex items-center gap-3.5">
          <!-- Vecteezy Style Content Delivery Network (CDN) Vector Illustration Icon Container -->
          <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center shrink-0 shadow-xs relative group hover:scale-105 transition-all duration-200">
            <svg class="w-8 h-8 sm:w-9 sm:h-9" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
              <!-- Outer Glow Ring -->
              <circle cx="24" cy="24" r="20" fill="url(#cdn-glow)" fill-opacity="0.15"/>
              
              <!-- Cloud Background Layer -->
              <path d="M36 28C38.7614 28 41 25.7614 41 23C41 20.441 39.076 18.3323 36.5916 18.0469C35.8364 13.5186 31.8694 10 27 10C22.6101 10 18.9174 12.8797 17.6749 16.8286C16.8927 16.2996 15.9575 16 14.9545 16C12.2182 16 10 18.2386 10 21C10 21.3653 10.0392 21.7212 10.1136 22.0645C8.30932 23.0039 7 24.8732 7 27C7 29.7614 9.23858 32 12 32H36Z" fill="url(#cloud-grad)" fill-opacity="0.3" stroke="url(#cloud-stroke)" stroke-width="1.8" stroke-linejoin="round"/>
              
              <!-- Fast Delivery Forward Arrow Signals -->
              <path d="M16 26L24 18L32 26" stroke="#38BDF8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M24 18V34" stroke="#818CF8" stroke-width="2.2" stroke-linecap="round"/>
              
              <!-- Digital Server Disk Nodes -->
              <circle cx="16" cy="34" r="2.5" fill="#34D399"/>
              <circle cx="24" cy="37" r="3" fill="#60A5FA"/>
              <circle cx="32" cy="34" r="2.5" fill="#A78BFA"/>
              
              <!-- Vector Gradients -->
              <defs>
                <radialGradient id="cdn-glow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(24 24) rotate(90) scale(20)">
                  <stop stop-color="#38BDF8"/>
                  <stop offset="1" stop-color="#818CF8" stop-opacity="0"/>
                </radialGradient>
                <linearGradient id="cloud-grad" x1="7" y1="10" x2="41" y2="32" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#818CF8"/>
                  <stop offset="0.5" stop-color="#38BDF8"/>
                  <stop offset="1" stop-color="#34D399"/>
                </linearGradient>
                <linearGradient id="cloud-stroke" x1="7" y1="10" x2="41" y2="32" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#A78BFA"/>
                  <stop offset="0.5" stop-color="#38BDF8"/>
                  <stop offset="1" stop-color="#34D399"/>
                </linearGradient>
              </defs>
            </svg>
          </div>
          
          <div>
            <div class="flex items-center gap-2.5 flex-wrap">
              <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight">Content Delivery Module</h1>
              
              <!-- Soft CDN Active Status Badge -->
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-950/60 text-emerald-300 border border-emerald-500/30 flex items-center gap-1.5 backdrop-blur-md">
                <svg class="w-3 h-3 text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none">
                  <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z" fill="#10B981" fill-opacity="0.2" stroke="#34D399" stroke-width="1.5"/>
                </svg>
                <span>Cloudinary CDN Active</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              </span>
            </div>
            <p class="text-xs text-slate-400 font-normal mt-0.5">
              Centralized Digital Asset & Course Content Delivery System
            </p>
          </div>
        </div>

        <!-- Right Side: Storage Bar Meter (Sleek Moderate Card) -->
        <div class="bg-slate-950/60 p-3.5 rounded-xl border border-slate-800/80 space-y-2 shrink-0 w-full lg:w-72 backdrop-blur-md">
          <div class="flex justify-between items-center text-xs font-mono">
            <span class="text-slate-300 font-semibold flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-sky-400 shrink-0" viewBox="0 0 24 24" fill="none">
                <rect x="2" y="4" width="20" height="6" rx="2" fill="#0284C7" fill-opacity="0.25" stroke="#38BDF8" stroke-width="1.5"/>
                <rect x="2" y="14" width="20" height="6" rx="2" fill="#0284C7" fill-opacity="0.25" stroke="#38BDF8" stroke-width="1.5"/>
                <circle cx="6" cy="7" r="1" fill="#38BDF8"/>
                <circle cx="6" cy="17" r="1" fill="#38BDF8"/>
              </svg>
              <span>Cloud CDN Storage</span>
            </span>
            <span class="text-xs font-semibold text-slate-200">128.4 GB <span class="text-slate-500 font-normal text-[11px]">/ 500 GB</span></span>
          </div>
          
          <!-- Progress Bar -->
          <div class="w-full bg-slate-950 rounded-full h-2 overflow-hidden border border-slate-800/60">
            <div class="bg-gradient-to-r from-indigo-500 to-sky-400 h-full rounded-full transition-all duration-500" style="width: 25.6%"></div>
          </div>
          
          <!-- Badges -->
          <div class="flex justify-between text-[11px] font-mono">
            <span class="text-emerald-400 font-medium">25.6% Used</span>
            <span class="text-slate-400 font-medium">371.6 GB Free</span>
          </div>
        </div>
      </div>

      <!-- Soft Balanced Navigation Sub-Tabs Bar -->
      <div class="bg-slate-900/80 backdrop-blur-md p-1.5 rounded-2xl border border-slate-800/90 shadow-lg flex items-center justify-between gap-1 overflow-x-auto text-[11px] xl:text-xs font-semibold scrollbar-none relative z-10 w-full">
        <button 
          @click="switchTab('library')" 
          :class="[
            'px-2.5 xl:px-3 py-1.5 sm:py-2 rounded-xl transition-all duration-200 flex items-center justify-center gap-1.5 shrink-0 xl:flex-1 cursor-pointer border select-none', 
            activeTab === 'library' 
              ? 'bg-indigo-600/25 border-indigo-500/50 text-indigo-200 font-bold shadow-sm' 
              : 'bg-transparent border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium'
          ]"
        >
          <BookOpen class="w-3.5 h-3.5 xl:w-4 xl:h-4 text-indigo-400 shrink-0" />
          <span class="whitespace-nowrap">Content Library</span>
          <span :class="['px-1.5 py-0.5 rounded-full text-[10px] font-mono border shrink-0', activeTab === 'library' ? 'bg-indigo-950/80 text-indigo-300 border-indigo-500/40 font-bold' : 'bg-slate-950/70 text-slate-400 border-slate-800']">{{ stats.total_items }}</span>
        </button>

        <button 
          @click="switchTab('videos')" 
          :class="[
            'px-2.5 xl:px-3 py-1.5 sm:py-2 rounded-xl transition-all duration-200 flex items-center justify-center gap-1.5 shrink-0 xl:flex-1 cursor-pointer border select-none', 
            activeTab === 'videos' 
              ? 'bg-indigo-600/25 border-indigo-500/50 text-indigo-200 font-bold shadow-sm' 
              : 'bg-transparent border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium'
          ]"
        >
          <Video class="w-3.5 h-3.5 xl:w-4 xl:h-4 text-sky-400 shrink-0" />
          <span class="whitespace-nowrap">Videos</span>
          <span :class="['px-1.5 py-0.5 rounded-full text-[10px] font-mono border shrink-0', activeTab === 'videos' ? 'bg-indigo-950/80 text-indigo-300 border-indigo-500/40 font-bold' : 'bg-slate-950/70 text-slate-400 border-slate-800']">{{ stats.videos_count }}</span>
        </button>

        <button 
          @click="switchTab('pdfs')" 
          :class="[
            'px-2.5 xl:px-3 py-1.5 sm:py-2 rounded-xl transition-all duration-200 flex items-center justify-center gap-1.5 shrink-0 xl:flex-1 cursor-pointer border select-none', 
            activeTab === 'pdfs' 
              ? 'bg-indigo-600/25 border-indigo-500/50 text-indigo-200 font-bold shadow-sm' 
              : 'bg-transparent border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium'
          ]"
        >
          <FileText class="w-3.5 h-3.5 xl:w-4 xl:h-4 text-emerald-400 shrink-0" />
          <span class="whitespace-nowrap">PDFs</span>
          <span :class="['px-1.5 py-0.5 rounded-full text-[10px] font-mono border shrink-0', activeTab === 'pdfs' ? 'bg-indigo-950/80 text-indigo-300 border-indigo-500/40 font-bold' : 'bg-slate-950/70 text-slate-400 border-slate-800']">{{ stats.pdfs_count }}</span>
        </button>

        <button 
          @click="switchTab('slides')" 
          :class="[
            'px-2.5 xl:px-3 py-1.5 sm:py-2 rounded-xl transition-all duration-200 flex items-center justify-center gap-1.5 shrink-0 xl:flex-1 cursor-pointer border select-none', 
            activeTab === 'slides' 
              ? 'bg-indigo-600/25 border-indigo-500/50 text-indigo-200 font-bold shadow-sm' 
              : 'bg-transparent border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium'
          ]"
        >
          <Presentation class="w-3.5 h-3.5 xl:w-4 xl:h-4 text-amber-400 shrink-0" />
          <span class="whitespace-nowrap">Slides</span>
          <span :class="['px-1.5 py-0.5 rounded-full text-[10px] font-mono border shrink-0', activeTab === 'slides' ? 'bg-indigo-950/80 text-indigo-300 border-indigo-500/40 font-bold' : 'bg-slate-950/70 text-slate-400 border-slate-800']">{{ stats.slides_count }}</span>
        </button>

        <button 
          @click="switchTab('notes')" 
          :class="[
            'px-2.5 xl:px-3 py-1.5 sm:py-2 rounded-xl transition-all duration-200 flex items-center justify-center gap-1.5 shrink-0 xl:flex-1 cursor-pointer border select-none', 
            activeTab === 'notes' 
              ? 'bg-indigo-600/25 border-indigo-500/50 text-indigo-200 font-bold shadow-sm' 
              : 'bg-transparent border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium'
          ]"
        >
          <StickyNote class="w-3.5 h-3.5 xl:w-4 xl:h-4 text-purple-400 shrink-0" />
          <span class="whitespace-nowrap">Notes & Docs</span>
          <span :class="['px-1.5 py-0.5 rounded-full text-[10px] font-mono border shrink-0', activeTab === 'notes' ? 'bg-indigo-950/80 text-indigo-300 border-indigo-500/40 font-bold' : 'bg-slate-950/70 text-slate-400 border-slate-800']">{{ stats.notes_count }}</span>
        </button>

        <button 
          @click="switchTab('modules')" 
          :class="[
            'px-2.5 xl:px-3 py-1.5 sm:py-2 rounded-xl transition-all duration-200 flex items-center justify-center gap-1.5 shrink-0 xl:flex-1 cursor-pointer border select-none', 
            activeTab === 'modules' 
              ? 'bg-indigo-600/25 border-indigo-500/50 text-indigo-200 font-bold shadow-sm' 
              : 'bg-transparent border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium'
          ]"
        >
          <FolderTree class="w-3.5 h-3.5 xl:w-4 xl:h-4 text-indigo-400 shrink-0" />
          <span class="whitespace-nowrap">Modules & Chapters</span>
        </button>

        <button 
          @click="switchTab('offline')" 
          :class="[
            'px-2.5 xl:px-3 py-1.5 sm:py-2 rounded-xl transition-all duration-200 flex items-center justify-center gap-1.5 shrink-0 xl:flex-1 cursor-pointer border select-none', 
            activeTab === 'offline' 
              ? 'bg-indigo-600/25 border-indigo-500/50 text-indigo-200 font-bold shadow-sm' 
              : 'bg-transparent border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 font-medium'
          ]"
        >
          <DownloadCloud class="w-3.5 h-3.5 xl:w-4 xl:h-4 text-sky-400 shrink-0" />
          <span class="whitespace-nowrap">Offline Content</span>
          <span :class="['px-1.5 py-0.5 rounded-full text-[10px] font-mono border shrink-0', activeTab === 'offline' ? 'bg-indigo-950/80 text-indigo-300 border-indigo-500/40 font-bold' : 'bg-slate-950/70 text-slate-400 border-slate-800']">2</span>
        </button>
      </div>

      <!-- Active Tab Content Component Rendering -->
      <ContentLibrary 
        v-if="['library', 'videos', 'pdfs', 'slides', 'notes'].includes(activeTab)" 
        :stats="stats" 
        :contentItems="contentItems" 
        :courses="courses" 
        :activeType="currentAssetType"
        @updateType="handleTypeChange"
        @openUpload="showUploadModal = true"
        @openPreview="handleOpenPreview"
      />

      <ModulesChapters 
        v-else-if="activeTab === 'modules'" 
        :courseModules="courseModules" 
        :courses="courses" 
        @openModuleModal="handleOpenModuleModal"
        @openPreview="handleOpenPreview"
        @openUpload="showUploadModal = true"
      />

      <OfflineContent 
        v-else-if="activeTab === 'offline'" 
        :stats="stats" 
        :offlinePackages="offlinePackages" 
        @openOfflineModal="handleOpenOfflineModal"
      />

      <!-- Modals -->
      <UniversalUploadModal 
        :show="showUploadModal" 
        :courses="courses" 
        @close="showUploadModal = false" 
        @uploaded="handleAssetUploaded"
        @preview="handleOpenPreview"
      />
      <VideoPlayerModal :show="showVideoModal" :video="activeItem" @close="showVideoModal = false" />
      <PdfViewerModal :show="showPdfModal" :pdf="activeItem" @close="showPdfModal = false" />
      <SlideViewerModal :show="showSlideModal" :slide="activeItem" @close="showSlideModal = false" />
      <NoteEditorModal :show="showNoteModal" :note="activeItem" @close="showNoteModal = false" @saved="handleNoteSaved" />
      <LinkViewerModal :show="showLinkModal" :link="activeItem" @close="showLinkModal = false" />
      <ModuleChapterModal :show="showModuleModal" :mode="moduleModalMode" :item="activeItem" @close="showModuleModal = false" />
      <OfflinePackageModal :show="showOfflineModal" :packageItem="activeItem" @close="showOfflineModal = false" @saved="handleOfflinePackageSaved" />
      <LessonPreviewEditorModal :show="showLessonEditorModal" :item="activeItem" @close="showLessonEditorModal = false" />

    </div>
  </AdminLayout>
</template>
