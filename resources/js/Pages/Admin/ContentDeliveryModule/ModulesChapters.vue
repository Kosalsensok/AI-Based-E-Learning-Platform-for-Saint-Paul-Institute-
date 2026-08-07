<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import AddUrlLinkModal from './AddUrlLinkModal.vue'
import TranslationManagerModal from './TranslationManagerModal.vue'
import LearningReminderModal from './LearningReminderModal.vue'
import { 
  Monitor, 
  BookOpen, 
  Layers, 
  PlayCircle, 
  Video, 
  FileText, 
  Presentation, 
  StickyNote, 
  Link as LinkIcon, 
  Search, 
  Plus, 
  Globe, 
  Trash2, 
  ChevronUp, 
  ChevronDown, 
  GripVertical, 
  Eye, 
  Rocket, 
  Sparkles, 
  Package, 
  Download, 
  Lock, 
  Unlock, 
  Pin,
  X,
  Star,
  Users,
  Clock,
  Megaphone,
  Bell,
  Play,
  GraduationCap,
  HelpCircle,
  Maximize2,
  ExternalLink,
  MoreHorizontal,
  CheckCircle2,
  MousePointerClick,
  Move,
  Info
} from 'lucide-vue-next'

const props = withDefaults(defineProps<{
  courseModules?: Array<any>
  courses?: Array<any>
}>(), {
  courseModules: () => [],
  courses: () => []
})

const emit = defineEmits(['openModuleModal', 'openPreview', 'openUpload'])

// State & Options
const selectedCourseId = ref('1')
const activeLanguage = ref<'kh' | 'en'>('kh')
const isStudentView = ref(false)
const showQuickGuide = ref(false)
const studentTab = ref<'overview' | 'notes' | 'announcements' | 'reviews' | 'learning_tools'>('overview')
const studentNoteInput = ref('')
const studentReminders = ref<Array<string>>([
  'Every Monday & Thursday at 7:00 PM (Study C Loops)'
])

const libraryTab = ref<'video' | 'pdf' | 'slide' | 'note' | 'link'>('video')
const librarySearch = ref('')
const unlinkedOnly = ref(false)

// Modals & Inline Drawer State
const showAddUrlModal = ref(false)
const showTranslationModal = ref(false)
const showReminderModal = ref(false)
const activeTranslationItem = ref<any>(null)
const expandedInlineKey = ref<string | null>(null) // Single Active Player Rule: unique key (e.g. 'lib-1' or 'ch-101-1')
const attachTargetItem = ref<any>(null) // Item to attach via modal selector
const showChapterAttachModal = ref(false)
const previewItem = ref<any>(null)
const toastMessage = ref<string | null>(null)

const showToast = (msg: string) => {
  toastMessage.value = msg
  setTimeout(() => {
    toastMessage.value = null
  }, 3000)
}

const toggleInlineExpand = (item: any, sourceKey: string) => {
  if (!item || !sourceKey) return
  previewItem.value = item
  if (expandedInlineKey.value === sourceKey) {
    expandedInlineKey.value = null
  } else {
    // Single Active Player Rule: Auto-collapse any open player across the screen
    expandedInlineKey.value = sourceKey
  }
}

const scrollToSection = (sectionId: string) => {
  const el = document.getElementById(sectionId)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

const handleReminderSaved = (reminderSummary: string) => {
  studentReminders.value.push(reminderSummary)
  showToast('បានដំឡើង Learning Reminder ជោគជ័យ! 🔔')
}

// ─── Centralized Master Content Library List ───
const masterLibraryItems = ref<Array<any>>([
  { id: 1, type: 'video', title: 'Intro to C Programming', kh_title: 'ការណែនាំអំពី C Programming', duration: '12:34', size: '45.2 MB', icon: '🎬', info: '12:34 · 1080p HD', has_kh: true, has_en: true, has_cc: true, is_linked: true, is_free_preview: true },
  { id: 2, type: 'video', title: 'Variables & Data Types', kh_title: 'អថេរ និងប្រភេទទិន្នន័យ', duration: '18:30', size: '62.8 MB', icon: '🎬', info: '18:30 · 1080p HD', has_kh: true, has_en: true, has_cc: true, is_linked: true, is_free_preview: false },
  { id: 3, type: 'video', title: 'Loops & Conditionals', kh_title: 'រង្វិលជុំ (Loops) និងលក្ខខណ្ឌ', duration: '14:15', size: '51.0 MB', icon: '🎬', info: '14:15 · 1080p HD', has_kh: false, has_en: true, has_cc: true, is_linked: false, is_free_preview: false },
  { id: 4, type: 'pdf', title: 'C Notes & Syntax Guide', kh_title: 'កំណត់ចំណាំ និង Syntax C', pages: '12 pages', size: '2.4 MB', icon: '📄', info: '2.4 MB · PDF Document', has_kh: true, has_en: true, has_cc: false, is_linked: true, is_free_preview: true },
  { id: 5, type: 'slide', title: 'Chapter 1 Presentation Slides', kh_title: 'ស្លាយបទបង្ហាញជំពូកទី១', slide_count: '24 slides', size: '8.1 MB', icon: '📊', info: '24 slides · PPTX Deck', has_kh: false, has_en: true, has_cc: false, is_linked: true, is_free_preview: false },
  { id: 6, type: 'note', title: 'Chapter Summary Note', kh_title: 'សេចក្តីសង្ខេបមេរៀនជំពូកទី១', word_count: '450 words', size: '1.2 MB', icon: '📝', info: '450 words · Web Note', has_kh: true, has_en: false, has_cc: false, is_linked: true, is_free_preview: true },
  { id: 7, type: 'link', sub_type: 'youtube', title: 'YouTube: C Loops Video Tutorial', kh_title: 'វីដេអូបង្រៀន Loops ពី YouTube', url: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', icon: '🔗', info: 'YouTube Embedded Video', has_kh: true, has_en: true, has_cc: true, is_linked: true, is_free_preview: true },
  { id: 8, type: 'link', sub_type: 'gdoc', title: 'Google Docs: Khmer C Guide', kh_title: 'ឯកសារ C Guide (Google Docs)', url: 'https://docs.google.com', icon: '🔗', info: 'Google Docs Embedded Document', has_kh: true, has_en: false, has_cc: false, is_linked: false, is_free_preview: false }
])

// Filtered Library Items
const filteredLibrary = computed(() => {
  return masterLibraryItems.value.filter(item => {
    const matchesTab = item.type === libraryTab.value
    const matchesSearch = !librarySearch.value || 
      item.title.toLowerCase().includes(librarySearch.value.toLowerCase()) ||
      (item.kh_title && item.kh_title.toLowerCase().includes(librarySearch.value.toLowerCase()))
    const matchesUnlinked = !unlinkedOnly.value || !item.is_linked

    return matchesTab && matchesSearch && matchesUnlinked
  })
})

// ─── Curriculum Tree List State ───
const modulesList = ref<Array<any>>([])
const collapsedModules = ref<Record<string | number, boolean>>({})
const dragOverChapterId = ref<string | number | null>(null)

// Subtitles Mode State
const selectedSubtitlesMode = ref<'off' | 'kh' | 'en' | 'both'>('kh')

// Active Student Lesson Playing
const activeStudentLesson = ref<any>({
  id: 1,
  title: 'Intro to C Programming',
  kh_title: 'ការណែនាំអំពី C Programming',
  duration: '12:34',
  type: 'video',
  icon: '🎬'
})

// Flattened chapters helper list for target attachment modal
const allChaptersList = computed(() => {
  const list: Array<{ moduleTitle: string, chapter: any }> = []
  modulesList.value.forEach(mod => {
    if (mod.chapters) {
      mod.chapters.forEach((ch: any) => {
        list.push({
          moduleTitle: activeLanguage.value === 'kh' ? (mod.kh_title || mod.title) : mod.title,
          chapter: ch
        })
      })
    }
  })
  return list
})

// Sync props into mutable reactive list
watch(() => props.courseModules, (newVal) => {
  if (newVal && newVal.length > 0) {
    modulesList.value = JSON.parse(JSON.stringify(newVal))
  } else {
    // Standard Curriculum Tree Structure
    modulesList.value = [
      {
        id: 1,
        module_number: 1,
        title: 'Section 1: Introduction to C Programming',
        kh_title: 'ផ្នែកទី ១៖ មូលដ្ឋានគ្រឹះនៃ C Programming',
        description: 'Learn how to become a C programmer and easily build core software algorithms.',
        kh_description: 'រៀនអំពីប្រវត្តិគ្រឹះ ការដំឡើង GCC Compiler និងសរសេរកម្មវិធី C ដំបូង។',
        estimated_duration: '1/5 | 24min',
        status: 'Published',
        progress: 100,
        is_locked: false,
        chapters: [
          {
            id: 101,
            chapter_number: '1.1',
            title: 'Chapter 1.1: History & Setup Environment',
            kh_title: 'ជំពូក ១.១៖ ប្រវត្តិ និងការដំឡើង Environment',
            items: [
              { id: 1, type: 'video', title: 'Intro to C Programming', kh_title: 'ការណែនាំអំពី C Programming', info: '12:34 · 1080p HD', icon: '🎬', has_kh: true, has_en: true, has_cc: true, is_free_preview: true, is_completed: true },
              { id: 4, type: 'pdf', title: 'C Notes & Syntax Guide', kh_title: 'កំណត់ចំណាំ និង Syntax C', info: '2.4 MB · PDF Document', icon: '📄', has_kh: true, has_en: true, has_cc: false, is_free_preview: true, is_completed: false },
              { id: 6, type: 'note', title: 'Chapter Summary Note', kh_title: 'សេចក្តីសង្ខេបមេរៀនជំពូកទី១', info: '450 words · Web Note', icon: '📝', has_kh: true, has_en: false, has_cc: false, is_free_preview: false, is_completed: false }
            ]
          }
        ]
      },
      {
        id: 2,
        module_number: 2,
        title: 'Section 2: Variables, Data Types & Memory',
        kh_title: 'ផ្នែកទី ២៖ អថេរ ប្រភេទទិន្នន័យ និងអង្គចងចាំ',
        description: 'Understand data types, variable declarations, memory representation, and constants.',
        kh_description: 'យល់ដឹងអំពីប្រភេទទិន្នន័យ ការប្រកាសអថេរ និងអង្គចងចាំ។',
        estimated_duration: '0/3 | 35min',
        status: 'Published',
        progress: 40,
        is_locked: false,
        chapters: [
          {
            id: 103,
            chapter_number: '2.1',
            title: 'Chapter 2.1: Variables & Memory Allocation',
            kh_title: 'ជំពូក ២.១៖ អថេរ និងអង្គចងចាំ',
            items: [
              { id: 2, type: 'video', title: 'Variables & Data Types', kh_title: 'អថេរ និងប្រភេទទិន្នន័យ', info: '18:30 · 1080p HD', icon: '🎬', has_kh: true, has_en: true, has_cc: true, is_free_preview: false, is_completed: false }
            ]
          },
          {
            id: 104,
            chapter_number: '2.2',
            title: 'Chapter 2.2: Data Types (int, float, char)',
            kh_title: 'ជំពូក ២.២៖ ប្រភេទទិន្នន័យ (int, float, char)',
            items: [
              { id: 7, type: 'link', sub_type: 'youtube', title: 'YouTube: C Loops Video Tutorial', kh_title: 'វីដេអូបង្រៀន Loops ពី YouTube', info: 'YouTube Embedded Video', icon: '🔗', has_kh: true, has_en: true, has_cc: true, is_free_preview: true, is_completed: false }
            ]
          }
        ]
      }
    ]
  }
}, { immediate: true })

const toggleCollapseModule = (modId: number | string) => {
  collapsedModules.value[modId] = !collapsedModules.value[modId]
}

const openAttachModal = (item: any) => {
  attachTargetItem.value = item
  showChapterAttachModal.value = true
}

const attachItemToChapter = (chapterObj: any, itemObj: any) => {
  if (!chapterObj || !itemObj) return

  const exists = chapterObj.items.some((i: any) => i.id === itemObj.id)
  if (exists) {
    showToast(`"${itemObj.kh_title || itemObj.title}" មានរួចហើយក្នុង ${chapterObj.kh_title || chapterObj.title}`)
    return
  }

  chapterObj.items.push({ ...itemObj })
  itemObj.is_linked = true
  showChapterAttachModal.value = false
  showToast(`ភ្ជាប់ "${itemObj.kh_title || itemObj.title}" ចូល ${chapterObj.kh_title || chapterObj.title} ជោគជ័យ! ✅`)
}

const handleDragStart = (event: DragEvent, item: any) => {
  if (event.dataTransfer) {
    event.dataTransfer.setData('application/json', JSON.stringify(item))
    event.dataTransfer.effectAllowed = 'copy'
  }
}

const handleDragOver = (event: DragEvent, chapterId: number | string) => {
  event.preventDefault()
  dragOverChapterId.value = chapterId
}

const handleDragLeave = (chapterId: number | string) => {
  if (dragOverChapterId.value === chapterId) {
    dragOverChapterId.value = null
  }
}

const handleDrop = (event: DragEvent, chapterObj: any) => {
  event.preventDefault()
  dragOverChapterId.value = null
  const data = event.dataTransfer?.getData('application/json')
  if (data) {
    try {
      const itemObj = JSON.parse(data)
      attachItemToChapter(chapterObj, itemObj)
    } catch (e) {
      console.error('Drop parse error', e)
    }
  }
}

const removeItemFromChapter = (chapterObj: any, index: number | string) => {
  const i = Number(index)
  const removed = chapterObj.items.splice(i, 1)[0]
  if (removed) {
    showToast(`បានដក "${removed.kh_title || removed.title}" ចេញពីជំពូក`)
  }
}

const moveModuleUp = (idx: number | string) => {
  const i = Number(idx)
  if (i <= 0) return
  const temp = modulesList.value[i]
  modulesList.value[i] = modulesList.value[i - 1]
  modulesList.value[i - 1] = temp
}

const moveModuleDown = (idx: number | string) => {
  const i = Number(idx)
  if (i >= modulesList.value.length - 1) return
  const temp = modulesList.value[i]
  modulesList.value[i] = modulesList.value[i + 1]
  modulesList.value[i + 1] = temp
}

const moveChapterUp = (moduleItem: any, idx: number | string) => {
  const i = Number(idx)
  if (i <= 0) return
  const temp = moduleItem.chapters[i]
  moduleItem.chapters[i] = moduleItem.chapters[i - 1]
  moduleItem.chapters[i - 1] = temp
}

const moveChapterDown = (moduleItem: any, idx: number | string) => {
  const i = Number(idx)
  if (i >= moduleItem.chapters.length - 1) return
  const temp = moduleItem.chapters[i]
  moduleItem.chapters[i] = moduleItem.chapters[i + 1]
  moduleItem.chapters[i + 1] = temp
}

const handleLinkAdded = (newLink: any) => {
  masterLibraryItems.value.unshift(newLink)
  showToast(`បានបន្ថែមតំណ "${newLink.title}" ទៅក្នុង Content Library!`)
}

const openTranslationModal = (item: any) => {
  activeTranslationItem.value = item
  showTranslationModal.value = true
}

const handleTranslationSaved = (updatedData: any) => {
  const item = masterLibraryItems.value.find(i => i.id === updatedData.item_id)
  if (item) {
    item.kh_title = updatedData.kh_title
    item.en_title = updatedData.en_title
    item.has_kh = updatedData.has_kh
    item.has_en = updatedData.has_en
    item.has_cc = updatedData.has_cc
  }
  showToast('បានរក្សាទុកការបកប្រែ និងអក្សររត់ VTT ជោគជ័យ!')
}
</script>

<template>
  <div class="space-y-6 text-slate-100 relative font-khmer scroll-smooth">
    
    <!-- Floating Toast Notification -->
    <transition name="fade">
      <div v-if="toastMessage" class="fixed bottom-6 right-6 z-50 bg-slate-900/95 border border-emerald-500/50 text-emerald-300 px-5 py-3.5 rounded-2xl shadow-2xl khmer-text-clean text-xs flex items-center gap-2.5 backdrop-blur-xl">
        <span class="p-1.5 bg-emerald-500/10 rounded-lg text-emerald-400">
          <CheckCircle2 class="w-4 h-4" />
        </span>
        <span class="font-medium">{{ toastMessage }}</span>
      </div>
    </transition>

    <!-- Quick Target Chapter Attachment Picker Modal -->
    <div v-if="showChapterAttachModal && attachTargetItem" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-slate-900/95 rounded-3xl border border-slate-800 shadow-2xl p-6 w-full max-w-md space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
          <h3 class="font-bold text-white text-sm flex items-center gap-2 khmer-title-clean">
            <span class="p-1.5 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 rounded-xl">
              <Pin class="w-4 h-4" />
            </span>
            <span>ជ្រើសរើស Chapter ដើម្បីភ្ជាប់៖</span>
          </h3>
          <button @click="showChapterAttachModal = false" class="w-8 h-8 rounded-xl bg-slate-800/60 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition">
            <X class="w-4 h-4" />
          </button>
        </div>

        <p class="text-xs text-indigo-300 font-medium khmer-text-clean bg-indigo-500/10 p-3 rounded-xl border border-indigo-500/20">
          ឯកសារដែលត្រូវភ្ជាប់: <strong>{{ activeLanguage === 'kh' ? (attachTargetItem.kh_title || attachTargetItem.title) : attachTargetItem.title }}</strong>
        </p>

        <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
          <div 
            v-for="target in allChaptersList" 
            :key="target.chapter.id"
            @click="attachItemToChapter(target.chapter, attachTargetItem)"
            class="p-3.5 bg-slate-950/60 hover:bg-indigo-500/10 rounded-xl border border-slate-800 hover:border-indigo-500/40 transition cursor-pointer flex items-center justify-between group text-xs"
          >
            <div>
              <span class="text-[10px] text-slate-500 block font-mono">{{ target.moduleTitle }}</span>
              <span class="font-medium text-slate-200 group-hover:text-indigo-300 khmer-text-clean flex items-center gap-1.5 mt-0.5">
                <BookOpen class="w-3.5 h-3.5 text-indigo-400" />
                <span>{{ activeLanguage === 'kh' ? (target.chapter.kh_title || target.chapter.title) : target.chapter.title }}</span>
              </span>
            </div>
            <span class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-[11px] font-semibold transition shadow-sm flex items-center gap-1">
              <Plus class="w-3.5 h-3.5" /> Attach
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Streamlined Action Bar & Controls -->
    <div class="bg-slate-900/80 backdrop-blur-md p-4 sm:p-5 rounded-3xl border border-slate-800/80 shadow-xl space-y-4">
      <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div>
          <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold shrink-0">
              <Monitor class="w-5 h-5" />
            </span>
            <div>
              <h2 class="text-base sm:text-lg font-bold text-white tracking-wide flex items-center gap-2">
                <span>{{ activeLanguage === 'kh' ? 'រចនាសម្ព័ន្ធមេរៀន' : 'Curriculum Builder' }}</span>
              </h2>
              <p class="text-xs text-slate-400 mt-0.5 khmer-text-clean">
                {{ activeLanguage === 'kh' ? 'រៀបចំខ្លឹមសារមេរៀនដោយ Drag & Drop និងកំណត់ Subtitles VTT' : 'Organize course curriculum and configure VTT subtitles' }}
              </p>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
          
          <!-- Group A: Settings & Language Controls Capsule (Muted Utility Controls) -->
          <div class="bg-slate-950/80 p-1.5 rounded-2xl border border-slate-800/80 flex items-center gap-2 shadow-xs">
            <!-- Quick Help / Instruction Guide Toggle Button -->
            <button 
              @click="showQuickGuide = !showQuickGuide"
              :class="['px-3 py-1.5 rounded-xl font-medium text-xs transition flex items-center gap-1.5 cursor-pointer', showQuickGuide ? 'bg-indigo-600/40 text-indigo-200 border border-indigo-500/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900']"
              :title="activeLanguage === 'kh' ? 'របៀបប្រើប្រាស់' : 'Quick Guide'"
            >
              <HelpCircle class="w-3.5 h-3.5 text-indigo-400" />
              <span>{{ activeLanguage === 'kh' ? 'របៀបប្រើ' : 'Guide' }}</span>
            </button>

            <div class="h-4 w-px bg-slate-800/80"></div>

            <!-- Language Switcher Buttons -->
            <div class="flex items-center gap-1">
              <button 
                @click="activeLanguage = 'kh'"
                :class="['px-2.5 py-1 rounded-lg font-semibold text-xs transition cursor-pointer flex items-center gap-1', activeLanguage === 'kh' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-400 hover:text-slate-200']"
              >
                <span>KH</span>
              </button>
              <button 
                @click="activeLanguage = 'en'"
                :class="['px-2.5 py-1 rounded-lg font-semibold text-xs transition cursor-pointer flex items-center gap-1', activeLanguage === 'en' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-400 hover:text-slate-200']"
              >
                <span>EN</span>
              </button>
            </div>
          </div>

          <!-- Vertical Divider for Clean Visual Hierarchy -->
          <div class="hidden sm:block h-6 w-px bg-slate-800"></div>

          <!-- Group B: Primary Curriculum Action Buttons -->
          <div class="flex items-center gap-2 flex-wrap">
            <!-- Student View Toggle Button -->
            <button 
              @click="isStudentView = !isStudentView"
              :class="['px-3.5 py-2 rounded-2xl font-medium text-xs transition border flex items-center gap-1.5 cursor-pointer shadow-xs', isStudentView ? 'bg-emerald-600 border-emerald-500 text-white' : 'bg-slate-950/80 border-slate-800 text-emerald-400 hover:bg-slate-800']"
            >
              <Eye class="w-4 h-4" />
              <span>{{ isStudentView ? (activeLanguage === 'kh' ? 'ចាកចេញពី Student View' : 'Exit Student View') : (activeLanguage === 'kh' ? 'មើលទិដ្ឋភាពសិស្ស' : 'Student View') }}</span>
            </button>

            <button 
              @click="$emit('openModuleModal', { mode: 'module' })"
              class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-2xl shadow-md shadow-indigo-600/20 transition text-xs flex items-center gap-1.5 cursor-pointer active:scale-95"
            >
              <Plus class="w-4 h-4" />
              <span>Add Module</span>
            </button>

            <button 
              @click="showToast(activeLanguage === 'kh' ? 'បានបោះពុម្ពផ្សាយវគ្គសិក្សាជោគជ័យ!' : 'Course published successfully!')"
              class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-2xl shadow-md shadow-emerald-600/20 transition text-xs flex items-center gap-1.5 cursor-pointer active:scale-95"
            >
              <Rocket class="w-4 h-4" />
              <span>Publish Course</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Collapsible Quick Guide / Onboarding Banner (Appears only when user toggles 'Guide') -->
      <transition name="fade">
        <div v-if="showQuickGuide" class="p-4 bg-slate-950/80 rounded-2xl border border-indigo-500/30 text-xs space-y-3 relative">
          <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
            <span class="font-bold text-indigo-300 flex items-center gap-1.5 khmer-title-clean">
              <HelpCircle class="w-4 h-4 text-indigo-400" />
              <span>{{ activeLanguage === 'kh' ? 'របៀបប្រើប្រាស់ប្រព័ន្ធ (Quick Start Guide)' : 'Quick Start Guide' }}</span>
            </span>
            <button @click="showQuickGuide = false" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition">
              <X class="w-4 h-4" />
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div 
              @click="scrollToSection('content-library-section')"
              class="bg-slate-900/80 hover:bg-indigo-500/10 p-3 rounded-xl border border-slate-800 hover:border-indigo-500/40 transition cursor-pointer flex items-center gap-3 group"
            >
              <span class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white font-bold text-xs flex items-center justify-center shrink-0 transition">1</span>
              <span class="text-slate-300 group-hover:text-white khmer-text-clean"><strong>ជំហានទី១:</strong> ជ្រើសរើស ឬទាញឯកសារចេញពី Content Library</span>
            </div>
            <div 
              @click="scrollToSection('curriculum-tree-section')"
              class="bg-slate-900/80 hover:bg-indigo-500/10 p-3 rounded-xl border border-slate-800 hover:border-indigo-500/40 transition cursor-pointer flex items-center gap-3 group"
            >
              <span class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white font-bold text-xs flex items-center justify-center shrink-0 transition">2</span>
              <span class="text-slate-300 group-hover:text-white khmer-text-clean"><strong>ជំហានទី២:</strong> Drag & Drop ឬចុច (+) ភ្ជាប់ចូល Chapter</span>
            </div>
            <div 
              @click="scrollToSection('preview-player-section')"
              class="bg-slate-900/80 hover:bg-indigo-500/10 p-3 rounded-xl border border-slate-800 hover:border-indigo-500/40 transition cursor-pointer flex items-center gap-3 group"
            >
              <span class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white font-bold text-xs flex items-center justify-center shrink-0 transition">3</span>
              <span class="text-slate-300 group-hover:text-white khmer-text-clean"><strong>ជំហានទី៣:</strong> មើលសាកល្បង, កែ CC និង Subtitles</span>
            </div>
          </div>
        </div>
      </transition>
    </div>

    <!-- STUDENT LEARNING VIEW MODE (UDEMY STYLE SCREENSHOT MATCH) -->
    <div v-if="isStudentView" class="bg-slate-900/90 rounded-3xl border border-emerald-500/40 shadow-2xl overflow-hidden animate-in fade-in duration-300">
      
      <!-- Top Student Player Header -->
      <div class="bg-slate-950/80 p-4 border-b border-slate-800/80 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-mono flex items-center gap-1.5">
            <GraduationCap class="w-4 h-4 text-emerald-400" /> Student Course Player View
          </span>
          <h3 class="text-sm font-bold text-white khmer-title-clean">
            {{ activeLanguage === 'kh' ? 'មូលដ្ឋានគ្រឹះនៃ C Programming (C Programming Basics)' : 'C Programming Basics' }}
          </h3>
        </div>

        <button @click="isStudentView = false" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-medium cursor-pointer border border-slate-700/60 transition flex items-center gap-1">
          <X class="w-3.5 h-3.5" /> Exit Student View
        </button>
      </div>

      <!-- Main Udemy Player Split Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-12 min-h-[620px]">
        
        <!-- Left 8 Columns: Main Player Video & Bottom Tabs -->
        <div class="lg:col-span-8 bg-slate-950/60 p-6 space-y-5 border-r border-slate-800/80 flex flex-col justify-between">
          
          <!-- Main Player Container -->
          <div class="space-y-3">
            <div class="relative bg-slate-950 rounded-2xl border border-slate-800/80 overflow-hidden h-[340px] flex flex-col justify-between p-5 shadow-inner">
              <div class="flex items-center justify-between text-xs text-slate-400 font-mono">
                <span class="flex items-center gap-1.5"><PlayCircle class="w-4 h-4 text-emerald-400" /> Playing: {{ activeLanguage === 'kh' ? (activeStudentLesson.kh_title || activeStudentLesson.title) : activeStudentLesson.title }}</span>
                <span>0:02 / 9:52 · 1080p HD</span>
              </div>

              <!-- Main Code Canvas -->
              <div class="text-center my-auto">
                <div class="inline-block px-6 py-3 bg-slate-900/80 rounded-xl border border-slate-800 text-emerald-400 font-mono text-sm shadow-sm">
                  #include &lt;stdio.h&gt;<br />
                  int main() { printf("Hello C!"); return 0; }
                </div>
              </div>

              <!-- Video Controls Bar -->
              <div class="space-y-2">
                <div class="h-1.5 bg-slate-800 rounded-full overflow-hidden cursor-pointer">
                  <div class="h-full bg-emerald-500 w-[20%]"></div>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-300 font-mono">
                  <div class="flex items-center gap-3">
                    <button class="hover:text-emerald-400 transition flex items-center gap-1"><PlayCircle class="w-3.5 h-3.5" /> Play</button>
                    <button class="hover:text-emerald-400 transition">1x Speed</button>
                    <span>0:02 / 9:52</span>
                  </div>
                  <div class="flex items-center gap-3">
                    <button @click="selectedSubtitlesMode = selectedSubtitlesMode === 'kh' ? 'off' : 'kh'" class="px-2 py-0.5 rounded bg-slate-800 text-emerald-400 font-bold border border-slate-700/60">
                      CC: {{ selectedSubtitlesMode.toUpperCase() }}
                    </button>
                    <span>1080p</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bottom Subtitles Display -->
            <div v-if="selectedSubtitlesMode !== 'off'" class="bg-slate-900/90 border border-indigo-500/30 p-3.5 rounded-xl text-center shadow-md backdrop-blur-md">
              <p v-if="selectedSubtitlesMode === 'kh' || selectedSubtitlesMode === 'both'" class="text-xs font-medium text-amber-300 khmer-text-clean">
                សូមស្វាគមន៍មកកាន់មេរៀន C Programming! យើងនឹងរៀនអំពី Structure នៃកម្មវិធី...
              </p>
              <p v-if="selectedSubtitlesMode === 'en' || selectedSubtitlesMode === 'both'" class="text-[11px] text-slate-300 mt-0.5">
                Welcome to C Programming! In this section we cover core code structure...
              </p>
            </div>
          </div>

          <!-- Bottom Navigation Tabs -->
          <div class="space-y-4 pt-4 border-t border-slate-800/80">
            <div class="flex items-center gap-2 border-b border-slate-800/80 pb-2 text-xs font-medium overflow-x-auto">
              <button 
                @click="studentTab = 'overview'"
                :class="['px-4 py-2 rounded-xl transition cursor-pointer', studentTab === 'overview' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']"
              >
                Overview
              </button>
              <button 
                @click="studentTab = 'notes'"
                :class="['px-4 py-2 rounded-xl transition cursor-pointer', studentTab === 'notes' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']"
              >
                Notes
              </button>
              <button 
                @click="studentTab = 'announcements'"
                :class="['px-4 py-2 rounded-xl transition cursor-pointer', studentTab === 'announcements' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']"
              >
                Announcements
              </button>
              <button 
                @click="studentTab = 'reviews'"
                :class="['px-4 py-2 rounded-xl transition cursor-pointer', studentTab === 'reviews' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']"
              >
                Reviews
              </button>
              <button 
                @click="studentTab = 'learning_tools'"
                :class="['px-4 py-2 rounded-xl transition cursor-pointer', studentTab === 'learning_tools' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']"
              >
                Learning tools
              </button>
            </div>

            <!-- Tab Content Displays -->
            <div class="text-xs space-y-3 min-h-[140px]">
              
              <!-- Overview Tab -->
              <div v-if="studentTab === 'overview'" class="space-y-2">
                <h4 class="font-bold text-white text-sm khmer-title-clean">
                  {{ activeLanguage === 'kh' ? 'ការសង្ខេបអំពីមេរៀន C Programming' : 'Learn how to master C Programming & algorithms' }}
                </h4>
                <p class="text-slate-300 khmer-text-clean leading-relaxed">
                  មេរៀននេះរៀបចំឡើងសម្រាប់សិស្សានុសិស្សដែលចង់ចេះសរសេរកម្មវិធីពីកម្រិតដំបូង (Beginner) រហូតដល់កម្រិតមធ្យម។ គ្របដណ្តប់លើ Variables, Data Types, Conditionals, Loops, Arrays, និង Pointers។
                </p>
                <div class="flex items-center gap-4 text-[11px] text-slate-400 font-mono pt-2">
                  <span class="flex items-center gap-1"><Star class="w-3.5 h-3.5 text-amber-400 fill-amber-400" /> 4.8 Rating</span>
                  <span class="flex items-center gap-1"><Users class="w-3.5 h-3.5 text-indigo-400" /> 1,240 Enrolled Students</span>
                  <span class="flex items-center gap-1"><Clock class="w-3.5 h-3.5 text-sky-400" /> 12 Hours Content</span>
                </div>
              </div>

              <!-- Notes Tab -->
              <div v-else-if="studentTab === 'notes'" class="space-y-3">
                <div class="flex items-center gap-2">
                  <input 
                    v-model="studentNoteInput"
                    type="text" 
                    placeholder="Type personal note at 0:02..."
                    class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-white khmer-text-clean focus:outline-none focus:border-indigo-500/60"
                  />
                  <button @click="showToast('បានរក្សាទុកកំណត់ចំណាំផ្ទាល់ខ្លួន!'); studentNoteInput = ''" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold cursor-pointer shrink-0 transition">Save Note</button>
                </div>
                <div class="p-3 bg-slate-900/80 rounded-xl border border-slate-800 text-slate-300 khmer-text-clean flex items-center gap-2">
                  <StickyNote class="w-4 h-4 text-indigo-400 shrink-0" />
                  <span><em>Note at 0:02:</em> #include &lt;stdio.h&gt; គឺជា Header file សម្រាប់រៀបចំ Input/Output ក្នុង C។</span>
                </div>
              </div>

              <!-- Announcements Tab -->
              <div v-else-if="studentTab === 'announcements'" class="p-4 bg-slate-900/80 rounded-xl border border-slate-800 space-y-1">
                <span class="text-[10px] text-indigo-400 font-mono flex items-center gap-1"><Megaphone class="w-3.5 h-3.5 text-indigo-400" /> Teacher Announcement · 2 days ago</span>
                <p class="text-slate-200 khmer-text-clean">
                  សួស្តីនិស្សិតទាំងអស់គ្នា! លំហាត់អនុវត្តជំពូកទី១ (Chapter 1 Quiz) ត្រូវបានបន្ថែមរួចរាល់ហើយ។
                </p>
              </div>

              <!-- Reviews Tab -->
              <div v-else-if="studentTab === 'reviews'" class="space-y-2">
                <div class="p-3 bg-slate-900/80 rounded-xl border border-slate-800 flex items-center justify-between">
                  <div>
                    <div class="flex items-center gap-0.5 text-amber-400">
                      <Star class="w-3.5 h-3.5 fill-amber-400" />
                      <Star class="w-3.5 h-3.5 fill-amber-400" />
                      <Star class="w-3.5 h-3.5 fill-amber-400" />
                      <Star class="w-3.5 h-3.5 fill-amber-400" />
                      <Star class="w-3.5 h-3.5 fill-amber-400" />
                      <span class="ml-1 text-xs font-bold text-amber-300">5.0</span>
                    </div>
                    <p class="text-slate-300 khmer-text-clean mt-0.5">មេរៀនពន្យល់ច្បាស់ៗ ងាយយល់ខ្លាំងណាស់លោកគ្រូ!</p>
                  </div>
                  <span class="text-[10px] text-slate-500 font-mono">Sokha M.</span>
                </div>
              </div>

              <!-- Learning Tools Tab -->
              <div v-else-if="studentTab === 'learning_tools'" class="space-y-4">
                <div>
                  <h4 class="font-bold text-white text-base font-mono">Learning reminders</h4>
                  <p class="text-slate-400 text-xs mt-1">Set up push notifications or calendar events to stay on track for your learning goals.</p>
                </div>

                <div class="space-y-2">
                  <div v-for="(rem, rIdx) in studentReminders" :key="rIdx" class="p-3 bg-slate-900/80 rounded-xl border border-slate-800 flex items-center justify-between text-xs text-slate-200">
                    <span class="flex items-center gap-2">
                      <Bell class="w-4 h-4 text-indigo-400" />
                      <span>{{ rem }}</span>
                    </span>
                    <button @click="studentReminders.splice(rIdx, 1)" class="text-rose-400 hover:text-rose-300 flex items-center gap-1"><Trash2 class="w-3.5 h-3.5" /> Delete</button>
                  </div>
                </div>

                <button 
                  @click="showReminderModal = true"
                  class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-2xl shadow-md text-xs transition cursor-pointer flex items-center gap-2"
                >
                  <Plus class="w-4 h-4" />
                  <span>Add a learning reminder</span>
                </button>
              </div>

            </div>
          </div>

        </div>

        <!-- Right 4 Columns: Course Content Sidebar -->
        <div class="lg:col-span-4 bg-slate-900/80 p-5 space-y-4 border-l border-slate-800/80">
          
          <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
            <h4 class="font-bold text-white text-sm font-mono flex items-center gap-2">
              <BookOpen class="w-4 h-4 text-indigo-400" />
              <span>Course content</span>
            </h4>
            <span class="text-xs text-slate-400 font-mono">2 Sections · 5 Lessons</span>
          </div>

          <div class="space-y-3 max-h-[540px] overflow-y-auto pr-1">
            <div v-for="mod in modulesList" :key="mod.id" class="bg-slate-950/60 rounded-2xl border border-slate-800/80 overflow-hidden">
              
              <!-- Section Header -->
              <div 
                @click="toggleCollapseModule(mod.id)"
                class="p-3.5 bg-slate-900/70 border-b border-slate-800/80 flex items-center justify-between cursor-pointer hover:bg-slate-800/40 transition"
              >
                <div>
                  <h5 class="font-bold text-white text-xs khmer-title-clean">
                    {{ activeLanguage === 'kh' ? (mod.kh_title || mod.title) : mod.title }}
                  </h5>
                  <span class="text-[10px] text-slate-400 font-mono mt-0.5 block">
                    {{ mod.estimated_duration }}
                  </span>
                </div>
                <span class="text-slate-400 text-xs font-bold">
                  <ChevronDown v-if="collapsedModules[mod.id]" class="w-4 h-4" />
                  <ChevronUp v-else class="w-4 h-4" />
                </span>
              </div>

              <!-- Section Lessons List -->
              <div v-if="!collapsedModules[mod.id]" class="p-2 space-y-1.5">
                <template v-for="ch in mod.chapters" :key="ch.id">
                  <div 
                    v-for="item in ch.items" 
                    :key="item.id"
                    @click="activeStudentLesson = item"
                    :class="['p-3 rounded-xl transition cursor-pointer flex items-center justify-between text-xs border', activeStudentLesson.id === item.id ? 'bg-indigo-500/10 border-indigo-500/40 text-white' : 'bg-slate-950/40 border-slate-800/60 hover:border-slate-700 text-slate-300']"
                  >
                    <div class="flex items-center gap-2.5 min-w-0">
                      <input type="checkbox" :checked="item.is_completed" class="rounded bg-slate-900 border-slate-700 text-emerald-500" @click.stop />
                      <div class="min-w-0">
                        <p class="font-medium khmer-text-clean truncate leading-snug">
                          {{ activeLanguage === 'kh' ? (item.kh_title || item.title) : item.title }}
                        </p>
                        <span class="text-[10px] text-slate-500 font-mono block flex items-center gap-1 mt-0.5">
                          <Video v-if="item.type==='video'" class="w-3 h-3 text-sky-400" />
                          <FileText v-else-if="item.type==='pdf'" class="w-3 h-3 text-emerald-400" />
                          <Presentation v-else-if="item.type==='slide'" class="w-3 h-3 text-amber-400" />
                          <StickyNote v-else-if="item.type==='note'" class="w-3 h-3 text-purple-400" />
                          <LinkIcon v-else class="w-3 h-3 text-blue-400" />
                          <span>{{ item.info }}</span>
                        </span>
                      </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0 ml-2">
                      <span v-if="item.is_free_preview" class="px-1.5 py-0.5 rounded text-[9px] bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-medium flex items-center gap-1">
                        <Unlock class="w-2.5 h-2.5" /> Free
                      </span>
                      <span v-else class="text-amber-400 text-xs">
                        <Lock class="w-3 h-3 text-amber-400" />
                      </span>

                      <!-- Resources Button -->
                      <button @click.stop="showToast(`ទាញយក Resources សម្រាប់: ${item.title}`)" class="px-2 py-1 bg-slate-800/80 hover:bg-slate-700/80 text-indigo-300 rounded-lg text-[10px] border border-slate-700/60 flex items-center gap-1 transition">
                        <Download class="w-3 h-3" /> Resources
                      </button>
                    </div>
                  </div>
                </template>
              </div>

            </div>
          </div>

        </div>

      </div>

    </div>

    <!-- 🖥️ MAIN DUAL-PANEL BUILDER (TEACHER / ADMIN VIEW) -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
      <div id="content-library-section" class="lg:col-span-5 bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-800/80 shadow-xl p-5 space-y-4 flex flex-col justify-between scroll-mt-6 min-h-[680px]">
        
        <div>
          <!-- Panel Header -->
          <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
            <h3 class="font-bold text-white text-sm flex items-center gap-2">
              <span class="p-1.5 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 rounded-xl">
                <BookOpen class="w-4 h-4" />
              </span>
              <span>{{ activeLanguage === 'kh' ? 'បណ្ណាល័យមាតិកា' : 'Content Library' }}</span>
            </h3>
            <button 
              @click="showAddUrlModal = true" 
              class="px-3 py-1.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 rounded-xl border border-indigo-500/30 font-medium text-xs transition cursor-pointer flex items-center gap-1 active:scale-95"
            >
              <Plus class="w-3.5 h-3.5" />
              <span>Add URL Link</span>
            </button>
          </div>

          <!-- 5 Category Tabs -->
          <div class="flex items-center gap-1 bg-slate-950/60 p-1.5 rounded-2xl border border-slate-800/60 mt-3 text-xs font-medium overflow-x-auto scrollbar-none">
            <button 
              @click="libraryTab = 'video'"
              :class="['px-3.5 py-1.5 rounded-xl transition cursor-pointer flex items-center gap-1 shrink-0', libraryTab === 'video' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40']"
            >
              <Video class="w-3.5 h-3.5 text-sky-400" />
              <span>Videos</span>
            </button>
            <button 
              @click="libraryTab = 'pdf'"
              :class="['px-3.5 py-1.5 rounded-xl transition cursor-pointer flex items-center gap-1 shrink-0', libraryTab === 'pdf' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40']"
            >
              <FileText class="w-3.5 h-3.5 text-emerald-400" />
              <span>PDFs</span>
            </button>
            <button 
              @click="libraryTab = 'slide'"
              :class="['px-3.5 py-1.5 rounded-xl transition cursor-pointer flex items-center gap-1 shrink-0', libraryTab === 'slide' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40']"
            >
              <Presentation class="w-3.5 h-3.5 text-amber-400" />
              <span>Slides</span>
            </button>
            <button 
              @click="libraryTab = 'note'"
              :class="['px-3.5 py-1.5 rounded-xl transition cursor-pointer flex items-center gap-1 shrink-0', libraryTab === 'note' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40']"
            >
              <StickyNote class="w-3.5 h-3.5 text-purple-400" />
              <span>Notes</span>
            </button>
            <button 
              @click="libraryTab = 'link'"
              :class="['px-3.5 py-1.5 rounded-xl transition cursor-pointer flex items-center gap-1 shrink-0', libraryTab === 'link' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40']"
            >
              <LinkIcon class="w-3.5 h-3.5 text-blue-400" />
              <span>Links</span>
            </button>
          </div>

          <!-- Search & Filter Controls -->
          <div class="space-y-2 mt-3 text-xs">
            <div class="relative">
              <Search class="w-4 h-4 text-slate-500 absolute left-3.5 top-3" />
              <input 
                v-model="librarySearch"
                type="text" 
                placeholder="Search files..."
                class="w-full pl-9 pr-3.5 py-2.5 bg-slate-950/80 border border-slate-800/80 rounded-xl text-white placeholder:text-slate-500 focus:outline-none focus:border-indigo-500/60 khmer-text-clean transition"
              />
            </div>
            <div class="flex items-center justify-between text-slate-400 font-medium">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" v-model="unlinkedOnly" class="rounded bg-slate-950 border-slate-800 text-indigo-600" />
                <span class="khmer-text-clean">Unlinked only</span>
              </label>
              <span class="font-mono text-[11px] text-slate-400">{{ filteredLibrary.length }} items available</span>
            </div>
          </div>

          <!-- Library Items Draggable List (Click ANYWHERE on card to expand!) -->
          <div class="mt-4 space-y-3 max-h-[520px] overflow-y-auto pr-1">
            <div 
              v-for="item in filteredLibrary" 
              :key="item.id"
              draggable="true"
              @dragstart="handleDragStart($event, item)"
              @click="toggleInlineExpand(item, 'lib-' + item.id)"
              class="p-3.5 bg-slate-950/60 hover:bg-slate-800/40 rounded-2xl border border-slate-800/60 hover:border-indigo-500/40 transition duration-200 cursor-pointer flex flex-col justify-between group select-none shadow-xs"
              :class="{ 'border-indigo-500/60 bg-indigo-500/10': expandedInlineKey === 'lib-' + item.id }"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                  <GripVertical class="w-4 h-4 text-slate-600 group-hover:text-slate-400 shrink-0" />
                  <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <Video v-if="item.type==='video'" class="w-5 h-5 text-sky-400" />
                    <FileText v-else-if="item.type==='pdf'" class="w-5 h-5 text-emerald-400" />
                    <Presentation v-else-if="item.type==='slide'" class="w-5 h-5 text-amber-400" />
                    <StickyNote v-else-if="item.type==='note'" class="w-5 h-5 text-purple-400" />
                    <LinkIcon v-else class="w-5 h-5 text-blue-400" />
                  </div>
                  <div class="min-w-0 space-y-0.5">
                    <!-- Crystal Clear Khmer Title Typography -->
                    <p class="khmer-text-clean text-slate-100 text-xs font-semibold group-hover:text-indigo-300 leading-snug">
                      {{ activeLanguage === 'kh' ? (item.kh_title || item.title) : item.title }}
                    </p>
                    <p class="text-[11px] text-slate-400 font-mono">{{ item.info }}</p>
                  </div>
                </div>

                <!-- Sleek & Spacious Action Controls ([...] and [+]) -->
                <div class="flex items-center gap-1.5 shrink-0 ml-3">
                  <!-- Sleek Arrow Indicator -->
                  <ChevronDown class="w-4 h-4 text-indigo-400 transition-transform duration-200" :class="{ 'rotate-180': expandedInlineKey === 'lib-' + item.id }" />

                  <!-- Secondary Options Menu Button (...) -->
                  <button 
                    @click.stop="openTranslationModal(item)"
                    class="w-8.5 h-8.5 flex items-center justify-center rounded-xl bg-slate-900 hover:bg-indigo-600/30 text-slate-400 hover:text-indigo-300 border border-slate-800 transition cursor-pointer active:scale-95 shadow-xs"
                    :title="activeLanguage === 'kh' ? 'ជម្រើសបន្ថែម (Subtitles & Translations)' : 'More options (Subtitles & Translations)'"
                  >
                    <MoreHorizontal class="w-4 h-4" />
                  </button>

                  <!-- Primary Attach to Chapter Button (+) -->
                  <button 
                    @click.stop="openAttachModal(item)"
                    class="w-8.5 h-8.5 flex items-center justify-center rounded-xl bg-emerald-500/15 hover:bg-emerald-600 text-emerald-400 hover:text-white text-xs font-bold border border-emerald-500/30 transition cursor-pointer shadow-xs active:scale-95"
                    :title="activeLanguage === 'kh' ? 'ភ្ជាប់ទៅ Chapter' : 'Attach to Chapter'"
                  >
                    <Plus class="w-4 h-4" />
                  </button>
                </div>
              </div>

              <!-- 🔽 IN-PLACE INLINE EXPANDED CONTENT DRAWER (CLICK CARD TO EXPAND!) -->
              <transition name="fade">
                <div v-if="expandedInlineKey === 'lib-' + item.id" class="mt-3 p-4 rounded-2xl bg-slate-900/95 border border-indigo-500/40 shadow-2xl space-y-3 animate-in fade-in duration-200" @click.stop>
                  <div class="flex items-center justify-between gap-2 border-b border-slate-800/80 pb-2.5 flex-wrap sm:flex-nowrap">
                    <!-- Left Side: Title, Info Badge, and Pop-out Panel Button -->
                    <div class="flex items-center gap-2 min-w-0 flex-wrap sm:flex-nowrap">
                      <span class="text-xs font-bold text-sky-400 flex items-center gap-1.5 whitespace-nowrap shrink-0">
                        <PlayCircle class="w-4 h-4 text-sky-400 shrink-0" />
                        <span>Inline Player</span>
                      </span>
                      <span v-if="item.info" class="px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-mono text-[10px] whitespace-nowrap shrink-0">
                        {{ item.info }}
                      </span>
                      <button 
                        @click.stop="$emit('openPreview', item)" 
                        class="text-indigo-300 hover:text-white text-xs font-medium px-2.5 py-1 bg-indigo-500/15 hover:bg-indigo-600/30 rounded-lg border border-indigo-500/30 transition flex items-center gap-1.5 cursor-pointer active:scale-95 shadow-xs whitespace-nowrap shrink-0"
                        :title="activeLanguage === 'kh' ? 'ពង្រីកមើលទំហំធំ Pop-out Panel' : 'Pop-out Full Preview'"
                      >
                        <Maximize2 class="w-3.5 h-3.5 text-indigo-400 shrink-0" />
                        <span>Pop-out Panel</span>
                      </button>
                    </div>

                    <!-- Far Right Edge: Isolated Collapse Button -->
                    <div class="flex items-center shrink-0">
                      <button 
                        @click.stop="expandedInlineKey = null" 
                        class="text-slate-400 hover:text-white text-xs font-bold px-2.5 py-1 bg-slate-800/80 hover:bg-slate-700/80 rounded-lg border border-slate-700/60 transition flex items-center gap-1.5 cursor-pointer active:scale-95 whitespace-nowrap shrink-0"
                      >
                        <ChevronUp class="w-3.5 h-3.5 shrink-0" />
                        <span>Collapse</span>
                      </button>
                    </div>
                  </div>

                  <!-- Video & Closed Captions Player Tray -->
                  <div v-if="item.type === 'video' || (item.type === 'link' && item.sub_type === 'youtube')" class="space-y-3">
                    <div class="relative bg-slate-950 rounded-xl border border-slate-800/80 overflow-hidden min-h-[210px] flex flex-col justify-between p-3.5 shadow-inner">
                      <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono gap-2">
                        <span class="flex items-center gap-1.5 min-w-0">
                          <PlayCircle class="w-3.5 h-3.5 text-sky-400 shrink-0" />
                          <span class="truncate">Playing: {{ activeLanguage === 'kh' ? (item.kh_title || item.title) : item.title }}</span>
                        </span>
                        <span class="shrink-0 font-medium text-slate-300">08:12 / 18:30</span>
                      </div>

                      <div class="text-center my-auto py-2">
                        <div class="inline-block w-full max-w-full px-3 py-2 bg-slate-900/90 rounded-lg border border-slate-800 text-sky-300 font-mono text-xs overflow-x-auto whitespace-pre">
                          <code>int a = 10; // ការប្រកាសអថេរ a មានតម្លៃ ១០</code>
                        </div>
                      </div>

                      <div v-if="selectedSubtitlesMode !== 'off'" class="bg-slate-900/95 border border-indigo-500/30 p-2.5 rounded-lg text-center space-y-1 backdrop-blur-md shadow-sm">
                        <p v-if="selectedSubtitlesMode === 'kh' || selectedSubtitlesMode === 'both'" class="text-xs font-medium text-amber-300 khmer-text-clean leading-snug">
                          អថេរ (Variable) គឺជាកន្លែងសម្រាប់ផ្ទុកទិន្នន័យក្នុងអង្គចងចាំ ដែលមានឈ្មោះ និងតម្លៃ...
                        </p>
                        <p v-if="selectedSubtitlesMode === 'en' || selectedSubtitlesMode === 'both'" class="text-[11px] text-slate-300 mt-0.5">
                          A variable is a container that stores data values in memory...
                        </p>
                      </div>
                    </div>

                    <!-- Subtitle Switcher Bar inside inline drawer -->
                    <div class="flex items-center justify-between flex-wrap gap-2 pt-1 border-t border-slate-800/60 text-xs">
                      <div class="flex items-center gap-1 bg-slate-950/90 p-1 rounded-xl border border-slate-800/80 text-[11px]">
                        <span class="text-slate-400 px-1 font-semibold">CC:</span>
                        <button @click="selectedSubtitlesMode = 'off'" :class="['px-2 py-0.5 rounded transition font-medium', selectedSubtitlesMode === 'off' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']">Off</button>
                        <button @click="selectedSubtitlesMode = 'kh'" :class="['px-2 py-0.5 rounded transition font-medium', selectedSubtitlesMode === 'kh' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']">KH</button>
                        <button @click="selectedSubtitlesMode = 'en'" :class="['px-2 py-0.5 rounded transition font-medium', selectedSubtitlesMode === 'en' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']">EN</button>
                        <button @click="selectedSubtitlesMode = 'both'" :class="['px-2 py-0.5 rounded transition font-medium', selectedSubtitlesMode === 'both' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']">Both</button>
                      </div>

                      <button @click="openTranslationModal(item)" class="px-3 py-1 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-xl text-[11px] font-medium transition flex items-center gap-1.5 active:scale-95">
                        <Globe class="w-3.5 h-3.5 text-indigo-400" /> Translate Subtitles
                      </button>
                    </div>
                  </div>

                  <!-- Document Reader Tray -->
                  <div v-else class="bg-slate-950/80 rounded-xl border border-slate-800/80 p-4 space-y-2">
                    <h5 class="text-xs font-bold text-indigo-300 font-mono flex items-center gap-1.5">
                      <FileText class="w-4 h-4 text-indigo-400" /> Document Content Reader
                    </h5>
                    <p class="text-xs text-slate-200 khmer-text-clean leading-relaxed">
                      {{ activeLanguage === 'kh' ? 'មាតិកា និងសេចក្តីសង្ខេបមេរៀនត្រូវបានបង្ហាញទីនេះដោយផ្ទាល់ក្នងកាតមេរៀន ដោយមិនបាច់បើក Dialog Modal...' : 'Document contents rendered inline directly inside lesson item card...' }}
                    </p>
                  </div>
                </div>
              </transition>
            </div>

            <div v-if="filteredLibrary.length === 0" class="p-8 text-center text-slate-500 text-xs border border-dashed border-slate-800/80 rounded-2xl khmer-text-clean">
              មិនមានឯកសារក្នុង Tab នេះទេ
            </div>
          </div>

        </div>

        <p class="text-[11px] text-slate-400 italic text-center pt-3 border-t border-slate-800/60 khmer-text-clean flex items-center justify-center gap-1.5">
          <MousePointerClick class="w-3.5 h-3.5 text-indigo-400" />
          <span>ចុចលើកាតមេរៀនណាមួយ ឬទាញ (Drag) → ដើម្បីទម្លាក់មើលមាតិកានៅខាងក្រោម</span>
        </p>

      </div>

      <!-- 🌳 RIGHT PANEL: CURRICULUM TREE (MODULES & CHAPTERS) -->
      <div id="curriculum-tree-section" class="lg:col-span-7 bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-800/80 shadow-xl p-5 space-y-4 flex flex-col justify-between scroll-mt-6 min-h-[680px]">
        <div>
          <!-- Right Panel Header -->
          <div class="flex items-center justify-between border-b border-slate-800/80 pb-3 mb-4">
            <h3 class="font-bold text-white text-sm flex items-center gap-2">
              <span class="p-1.5 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 rounded-xl">
                <Layers class="w-4 h-4" />
              </span>
              <span>{{ activeLanguage === 'kh' ? 'រចនាសម្ព័ន្ធមេរៀន' : 'Curriculum Modules Tree' }}</span>
            </h3>
            <button 
              @click="$emit('openModuleModal', { mode: 'module' })" 
              class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-medium text-xs transition cursor-pointer flex items-center gap-1 active:scale-95 shadow-sm"
            >
              <Plus class="w-3.5 h-3.5" />
              <span>Add Module</span>
            </button>
          </div>

          <div class="space-y-4 max-h-[520px] overflow-y-auto pr-1">
            <div 
              v-for="(mod, mIdx) in modulesList" 
              :key="mod.id"
              class="bg-slate-950/60 backdrop-blur-md rounded-2xl border border-slate-800/80 shadow-md overflow-hidden"
            >
              <!-- Module Header -->
              <div class="bg-slate-950/80 p-4 border-b border-slate-800/60 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3 cursor-pointer" @click="toggleCollapseModule(mod.id)">
                  <button class="w-7 h-7 bg-slate-800/80 hover:bg-slate-700/80 text-slate-300 rounded-lg text-xs font-medium border border-slate-700/50 flex items-center justify-center transition">
                    <ChevronDown v-if="collapsedModules[mod.id]" class="w-4 h-4" />
                    <ChevronUp v-else class="w-4 h-4" />
                  </button>
                  <div class="w-11 h-11 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0">
                    <Package class="w-6 h-6" />
                  </div>
                  <div>
                    <h3 class="font-bold text-white text-sm flex items-center gap-2 khmer-title-clean">
                      <span>{{ activeLanguage === 'kh' ? (mod.kh_title || mod.title) : mod.title }}</span>
                      <span class="px-2.5 py-0.5 rounded-full text-[10px] bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-mono font-medium">
                        {{ mod.estimated_duration }}
                      </span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5 khmer-text-clean">
                      {{ activeLanguage === 'kh' ? (mod.kh_description || mod.description) : mod.description }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center gap-1.5">
                  <button @click="moveModuleUp(mIdx)" :disabled="mIdx === 0" class="w-7 h-7 bg-slate-800/80 hover:bg-slate-700/80 text-slate-300 rounded-lg text-xs font-medium border border-slate-700/50 disabled:opacity-20 cursor-pointer flex items-center justify-center transition">
                    <ChevronUp class="w-4 h-4" />
                  </button>
                  <button @click="moveModuleDown(mIdx)" :disabled="mIdx === modulesList.length - 1" class="w-7 h-7 bg-slate-800/80 hover:bg-slate-700/80 text-slate-300 rounded-lg text-xs font-medium border border-slate-700/50 disabled:opacity-20 cursor-pointer flex items-center justify-center transition">
                    <ChevronDown class="w-4 h-4" />
                  </button>
                  <button @click="$emit('openModuleModal', { mode: 'chapter' })" class="px-3 py-1.5 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 rounded-xl text-xs font-semibold transition cursor-pointer flex items-center gap-1">
                    <Plus class="w-3.5 h-3.5" /> Chapter
                  </button>
                </div>
              </div>

              <!-- Chapters Tree & Dropzones -->
              <div v-if="!collapsedModules[mod.id]" class="p-4 space-y-3.5 bg-slate-950/40">
                <div 
                  v-for="(ch, cIdx) in mod.chapters" 
                  :key="ch.id"
                  @dragover="handleDragOver($event, ch.id)"
                  @dragleave="handleDragLeave(ch.id)"
                  @drop="handleDrop($event, ch)"
                  class="bg-slate-900/60 rounded-2xl border p-4 space-y-3 transition duration-200"
                  :class="dragOverChapterId === ch.id ? 'border-2 border-dashed border-indigo-400 bg-indigo-500/10' : 'border-slate-800/80 hover:border-slate-700/80'"
                >
                  <!-- Chapter Header -->
                  <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                    <span class="font-bold text-white text-xs flex items-center gap-2 khmer-title-clean">
                      <span class="w-7 h-7 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20">
                        <BookOpen class="w-4 h-4" />
                      </span>
                      <span>{{ activeLanguage === 'kh' ? (ch.kh_title || ch.title) : ch.title }}</span>
                    </span>

                    <div class="flex items-center gap-1.5">
                      <button @click="moveChapterUp(mod, cIdx)" :disabled="cIdx === 0" class="w-6 h-6 bg-slate-800/80 hover:bg-slate-700/80 text-slate-300 rounded border border-slate-700/50 disabled:opacity-20 cursor-pointer flex items-center justify-center transition">
                        <ChevronUp class="w-3.5 h-3.5" />
                      </button>
                      <button @click="moveChapterDown(mod, cIdx)" :disabled="cIdx === mod.chapters.length - 1" class="w-6 h-6 bg-slate-800/80 hover:bg-slate-700/80 text-slate-300 rounded border border-slate-700/50 disabled:opacity-20 cursor-pointer flex items-center justify-center transition">
                        <ChevronDown class="w-3.5 h-3.5" />
                      </button>
                    </div>
                  </div>

                  <!-- Content Items Attached in Chapter (Click ANYWHERE on card to expand!) -->
                  <div class="space-y-2.5 pl-3 border-l-2 border-slate-800/80">
                    <div 
                      v-for="(item, aIdx) in ch.items" 
                      :key="item.id"
                      @click="toggleInlineExpand(item, 'ch-' + ch.id + '-' + item.id)"
                      class="p-3.5 bg-slate-950/60 hover:bg-slate-850/60 rounded-xl border border-slate-800/70 hover:border-indigo-500/40 flex flex-col justify-between text-xs cursor-pointer group transition duration-200 shadow-xs"
                      :class="{ 'border-indigo-500/60 bg-indigo-500/10': expandedInlineKey === 'ch-' + ch.id + '-' + item.id }"
                    >
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 font-semibold text-slate-100 text-xs">
                          <div class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-lg shrink-0">
                            <Video v-if="item.type==='video'" class="w-4 h-4 text-sky-400" />
                            <FileText v-else-if="item.type==='pdf'" class="w-4 h-4 text-emerald-400" />
                            <Presentation v-else-if="item.type==='slide'" class="w-4 h-4 text-amber-400" />
                            <StickyNote v-else-if="item.type==='note'" class="w-4 h-4 text-purple-400" />
                            <LinkIcon v-else class="w-4 h-4 text-blue-400" />
                          </div>
                          <span class="khmer-text-clean leading-snug">{{ activeLanguage === 'kh' ? (item.kh_title || item.title) : item.title }}</span>
                        </div>

                        <div class="flex items-center gap-1.5">
                          <span v-if="item.has_cc" class="px-2 py-0.5 rounded text-[10px] bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-medium">CC</span>
                          <span v-if="item.has_kh" class="px-1.5 py-0.5 rounded text-[10px] bg-sky-500/10 text-sky-300 border border-sky-500/20 font-bold font-mono">KH</span>
                          <span v-if="item.has_en" class="px-1.5 py-0.5 rounded text-[10px] bg-blue-500/10 text-blue-300 border border-blue-500/20 font-bold font-mono">EN</span>

                          <!-- Arrow Indicator -->
                          <ChevronDown class="w-4 h-4 text-indigo-400 transition-transform duration-200" :class="{ 'rotate-180': expandedInlineKey === 'ch-' + ch.id + '-' + item.id }" />

                          <button 
                            @click.stop="openTranslationModal(item)"
                            class="w-8.5 h-8.5 rounded-xl bg-slate-800/80 hover:bg-indigo-600 hover:text-white text-slate-300 border border-slate-700/60 text-xs flex items-center justify-center transition active:scale-95 shadow-xs"
                            :title="activeLanguage === 'kh' ? 'គ្រប់គ្រងការបកប្រែ & Subtitles' : 'Manage Subtitles & Translations'"
                          >
                            <Globe class="w-4 h-4" />
                          </button>
                          <button 
                            @click.stop="removeItemFromChapter(ch, aIdx)"
                            class="w-8.5 h-8.5 rounded-xl bg-rose-500/10 hover:bg-rose-600 hover:text-white text-rose-400 border border-rose-500/20 text-xs flex items-center justify-center transition active:scale-95 shadow-xs"
                            :title="activeLanguage === 'kh' ? 'ដកចេញពី Chapter' : 'Remove Item'"
                          >
                            <Trash2 class="w-4 h-4" />
                          </button>
                        </div>
                      </div>

                      <!-- 🔽 IN-PLACE INLINE EXPANDED PLAYER & READER DRAWER (CLICK CARD TO EXPAND!) -->
                      <transition name="fade">
                        <div v-if="expandedInlineKey === 'ch-' + ch.id + '-' + item.id" class="mt-3 p-4 rounded-2xl bg-slate-900/95 border border-indigo-500/40 shadow-2xl space-y-3 animate-in fade-in duration-200" @click.stop>
                          <div class="flex items-center justify-between gap-2 border-b border-slate-800/80 pb-2.5 flex-wrap sm:flex-nowrap">
                            <!-- Left Side: Title, Info Badge, and Pop-out Panel Button -->
                            <div class="flex items-center gap-2 min-w-0 flex-wrap sm:flex-nowrap">
                              <span class="text-xs font-bold text-sky-400 flex items-center gap-1.5 whitespace-nowrap shrink-0">
                                <PlayCircle class="w-4 h-4 text-sky-400 shrink-0" />
                                <span>Inline Player</span>
                              </span>
                              <span v-if="item.info" class="px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-mono text-[10px] whitespace-nowrap shrink-0">
                                {{ item.info }}
                              </span>
                              <button 
                                @click.stop="$emit('openPreview', item)" 
                                class="text-indigo-300 hover:text-white text-xs font-medium px-2.5 py-1 bg-indigo-500/15 hover:bg-indigo-600/30 rounded-lg border border-indigo-500/30 transition flex items-center gap-1.5 cursor-pointer active:scale-95 shadow-xs whitespace-nowrap shrink-0"
                                :title="activeLanguage === 'kh' ? 'ពង្រីកមើលទំហំធំ Pop-out Panel' : 'Pop-out Full Preview'"
                              >
                                <Maximize2 class="w-3.5 h-3.5 text-indigo-400 shrink-0" />
                                <span>Pop-out Panel</span>
                              </button>
                            </div>

                            <!-- Far Right Edge: Isolated Collapse Button -->
                            <div class="flex items-center shrink-0">
                              <button 
                                @click.stop="expandedInlineKey = null" 
                                class="text-slate-400 hover:text-white text-xs font-bold px-2.5 py-1 bg-slate-800/80 hover:bg-slate-700/80 rounded-lg border border-slate-700/60 transition flex items-center gap-1.5 cursor-pointer active:scale-95 whitespace-nowrap shrink-0"
                              >
                                <ChevronUp class="w-3.5 h-3.5 shrink-0" />
                                <span>Collapse</span>
                              </button>
                            </div>
                          </div>

                          <!-- Video Player simulation inside item drawer -->
                          <div v-if="item.type === 'video' || (item.type === 'link' && item.sub_type === 'youtube')" class="space-y-3">
                            <div class="relative bg-slate-950 rounded-xl border border-slate-800/80 overflow-hidden min-h-[220px] flex flex-col justify-between p-4 shadow-inner">
                              <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono">
                                <span class="flex items-center gap-1.5"><PlayCircle class="w-3.5 h-3.5 text-sky-400" /> Playing: {{ activeLanguage === 'kh' ? (item.kh_title || item.title) : item.title }}</span>
                                <span>08:12 / 18:30 · 1080p HD</span>
                              </div>

                              <div class="text-center my-auto py-2">
                                <div class="inline-block w-full max-w-full px-4 py-2 bg-slate-900/90 rounded-lg border border-slate-800 text-sky-300 font-mono text-xs overflow-x-auto whitespace-pre">
                                  <code>int a = 10; // ការប្រកាសអថេរ a មានតម្លៃ ១០</code>
                                </div>
                              </div>

                              <div v-if="selectedSubtitlesMode !== 'off'" class="bg-slate-900/95 border border-indigo-500/30 p-3 rounded-lg text-center backdrop-blur-md shadow-sm">
                                <p v-if="selectedSubtitlesMode === 'kh' || selectedSubtitlesMode === 'both'" class="text-xs font-medium text-amber-300 khmer-text-clean">
                                  អថេរ (Variable) គឺជាកន្លែងសម្រាប់ផ្ទុកទិន្នន័យក្នុងអង្គចងចាំ...
                                </p>
                                <p v-if="selectedSubtitlesMode === 'en' || selectedSubtitlesMode === 'both'" class="text-[11px] font-normal text-slate-200">
                                  A variable is a container that stores data values in memory...
                                </p>
                              </div>
                            </div>

                            <!-- Subtitle Switcher Bar inside inline drawer -->
                            <div class="flex items-center justify-between flex-wrap gap-2 pt-1 border-t border-slate-800/60 text-xs">
                              <div class="flex items-center gap-1 bg-slate-950/90 p-1 rounded-xl border border-slate-800/80 text-[11px]">
                                <span class="text-slate-400 px-1 font-semibold">CC:</span>
                                <button @click="selectedSubtitlesMode = 'off'" :class="['px-2 py-0.5 rounded transition font-medium', selectedSubtitlesMode === 'off' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']">Off</button>
                                <button @click="selectedSubtitlesMode = 'kh'" :class="['px-2 py-0.5 rounded transition font-medium', selectedSubtitlesMode === 'kh' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']">KH</button>
                                <button @click="selectedSubtitlesMode = 'en'" :class="['px-2 py-0.5 rounded transition font-medium', selectedSubtitlesMode === 'en' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']">EN</button>
                                <button @click="selectedSubtitlesMode = 'both'" :class="['px-2 py-0.5 rounded transition font-medium', selectedSubtitlesMode === 'both' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']">Both</button>
                              </div>

                              <button @click="openTranslationModal(item)" class="px-3 py-1 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-lg text-[11px] font-medium transition flex items-center gap-1.5 active:scale-95">
                                <Globe class="w-3.5 h-3.5 text-indigo-400" /> Translate Subtitles
                              </button>
                            </div>
                          </div>

                          <!-- Document Reader Tray inside item drawer -->
                          <div v-else class="bg-slate-950/80 rounded-xl border border-slate-800/80 p-4 space-y-2">
                            <h5 class="text-xs font-bold text-indigo-300 font-mono flex items-center gap-1.5">
                              <FileText class="w-4 h-4 text-indigo-400" /> Document Content Reader
                            </h5>
                            <p class="text-xs text-slate-200 khmer-text-clean leading-relaxed">
                              {{ activeLanguage === 'kh' ? 'មាតិកា និងសេចក្តីសង្ខេបមេរៀនត្រូវបានបង្ហាញទីនេះដោយផ្ទាល់ក្នងកាតមេរៀន ដោយមិនបាច់បើក Dialog Modal...' : 'Document contents rendered inline directly inside lesson item card...' }}
                            </p>
                          </div>
                        </div>
                      </transition>
                    </div>
                  </div>

                <!-- Enhanced Interactive Drop Zone -->
                <div 
                  class="py-5 px-4 border-2 border-dashed rounded-2xl text-center transition-all duration-300 cursor-pointer flex flex-col sm:flex-row items-center justify-center gap-3 select-none group shadow-inner"
                  :class="dragOverChapterId === ch.id 
                    ? 'border-indigo-400 bg-indigo-500/20 shadow-lg shadow-indigo-500/10 scale-[1.01]' 
                    : 'border-slate-800 hover:border-indigo-500/50 bg-slate-950/40 hover:bg-indigo-500/5'"
                >
                  <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-200">
                    <Download class="w-5 h-5 text-indigo-400 group-hover:animate-bounce" />
                  </div>
                  <div class="text-left space-y-0.5">
                    <p class="text-xs font-semibold text-slate-200 group-hover:text-indigo-300 khmer-text-clean transition">
                      {{ activeLanguage === 'kh' ? 'ទម្លាក់មាតិកានៅទីនេះ' : 'Drop content here to attach' }}
                    </p>
                    <p class="text-[11px] text-slate-400">
                      {{ activeLanguage === 'kh' ? 'ទាញរំកិលចេញពី Content Library ខាងឆ្វេង ឬចុច (+) លើកាត' : 'Drag items from left library or click (+) on card' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

        <p class="text-[11px] text-slate-400 italic text-center pt-3 border-t border-slate-800/60 khmer-text-clean flex items-center justify-center gap-1.5">
          <Move class="w-3.5 h-3.5 text-indigo-400" />
          <span>ទាញ Drag & Drop មាតិកាពីខាងឆ្វេង ដើម្បីរៀបចំ Curriculum របស់អ្នក</span>
        </p>

      </div>

    </div>

    <!-- ▶️ BOTTOM PANEL: PREVIEW PLAYER (VIDEO FRAME / SUBTITLES / DOCUMENT) -->
    <transition name="fade">
      <div id="preview-player-section" v-if="previewItem" class="bg-slate-900/90 backdrop-blur-md p-6 rounded-3xl border border-indigo-500/40 shadow-2xl space-y-4 scroll-mt-6">
        
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3.5">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0">
              <Video v-if="previewItem.type==='video'" class="w-6 h-6 text-sky-400" />
              <FileText v-else-if="previewItem.type==='pdf'" class="w-6 h-6 text-emerald-400" />
              <Presentation v-else-if="previewItem.type==='slide'" class="w-6 h-6 text-amber-400" />
              <StickyNote v-else-if="previewItem.type==='note'" class="w-6 h-6 text-purple-400" />
              <LinkIcon v-else class="w-6 h-6 text-blue-400" />
            </div>
            <div>
              <h4 class="font-bold text-white text-sm flex items-center gap-2 khmer-title-clean">
                <span>{{ activeLanguage === 'kh' ? (previewItem.kh_title || previewItem.title) : previewItem.title }}</span>
                <span class="text-[10px] font-mono bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 px-2.5 py-0.5 rounded-full font-medium">
                  {{ previewItem.info || 'Preview Player Mode' }}
                </span>
              </h4>
              <p class="text-xs text-slate-400 mt-0.5 font-sans">
                Preview Player Mode — VTT Subtitles & Translation Tester (ចំណុចទី៣)
              </p>
            </div>
          </div>

          <div class="flex items-center gap-2.5">
            
            <!-- Closed Captions Subtitle Mode Dropdown -->
            <div class="flex items-center gap-1 bg-slate-950/80 p-1.5 rounded-xl border border-slate-800/80 text-xs font-medium">
              <span class="text-slate-400 text-xs px-2">CC:</span>
              <button @click="selectedSubtitlesMode = 'off'" :class="['px-2.5 py-1 rounded-lg transition', selectedSubtitlesMode === 'off' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']">Off</button>
              <button @click="selectedSubtitlesMode = 'kh'" :class="['px-2.5 py-1 rounded-lg transition', selectedSubtitlesMode === 'kh' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']">KH (ខ្មែរ)</button>
              <button @click="selectedSubtitlesMode = 'en'" :class="['px-2.5 py-1 rounded-lg transition', selectedSubtitlesMode === 'en' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']">EN (English)</button>
              <button @click="selectedSubtitlesMode = 'both'" :class="['px-2.5 py-1 rounded-lg transition', selectedSubtitlesMode === 'both' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-slate-200']">Both</button>
            </div>

            <button @click="openTranslationModal(previewItem)" class="px-4 py-2 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 font-medium text-xs rounded-xl transition cursor-pointer flex items-center gap-1.5">
              <Globe class="w-4 h-4" /> Manage Subtitles
            </button>

            <button @click="previewItem = null" class="w-8 h-8 rounded-xl bg-slate-800/60 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition">
              <X class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Video Player Frame Simulation with Dual Subtitles Overlay -->
        <div v-if="previewItem.type === 'video' || (previewItem.type === 'link' && previewItem.sub_type === 'youtube')" class="relative bg-slate-950 rounded-2xl border border-slate-800/80 overflow-hidden h-80 flex flex-col justify-between p-5 shadow-inner">
          <div class="flex items-center justify-between text-xs text-slate-400 font-mono">
            <span class="flex items-center gap-1.5"><PlayCircle class="w-4 h-4 text-sky-400" /> Playing: {{ previewItem.title }}</span>
            <span>08:12 / 18:30 · 1080p HD</span>
          </div>

          <!-- Video Simulation Frame & Code Output -->
          <div class="text-center space-y-2 my-auto">
            <div class="inline-block px-5 py-2.5 bg-slate-900/80 rounded-xl border border-slate-800 text-sky-300 font-mono text-xs shadow-sm">
              int a = 10; // ការប្រកាសអថេរ a មានតម្លៃ ១០
            </div>
          </div>

          <!-- Closed Caption Subtitles Overlay Bar -->
          <div v-if="selectedSubtitlesMode !== 'off'" class="bg-slate-900/90 border border-indigo-500/30 p-4 rounded-xl text-center space-y-1 backdrop-blur-md shadow-md">
            <p v-if="selectedSubtitlesMode === 'kh' || selectedSubtitlesMode === 'both'" class="text-sm font-medium text-amber-300 khmer-text-clean">
              អថេរ (Variable) គឺជាកន្លែងសម្រាប់ផ្ទុកទិន្នន័យក្នុងអង្គចងចាំ ដែលមានឈ្មោះ និងតម្លៃ...
            </p>
            <p v-if="selectedSubtitlesMode === 'en' || selectedSubtitlesMode === 'both'" class="text-xs font-normal text-slate-200 leading-relaxed">
              A variable is a container that stores data values in memory...
            </p>
          </div>
        </div>

        <!-- Document Viewer Simulation -->
        <div v-else class="bg-slate-950/80 rounded-2xl border border-slate-800/80 p-6 space-y-3">
          <h5 class="text-xs font-bold text-indigo-300 uppercase font-mono flex items-center gap-1.5">
            <FileText class="w-4 h-4 text-indigo-400" /> Document Content Viewer
          </h5>
          <p class="text-xs text-slate-200 khmer-text-clean">
            {{ activeLanguage === 'kh' ? 'អត្ថបទ និងមាតិកាឯកសារត្រូវបានបង្ហាញទីនេះក្នុង Document Viewer ដោយមិនបាច់ចាកចេញពីប្រព័ន្ធ LMS...' : 'Document content viewer rendered directly inside E.LMS window...' }}
          </p>
        </div>

      </div>
    </transition>

    <!-- Modals Integration -->
    <AddUrlLinkModal 
      :show="showAddUrlModal"
      @close="showAddUrlModal = false"
      @addLink="handleLinkAdded"
    />

    <TranslationManagerModal 
      :show="showTranslationModal"
      :item="activeTranslationItem"
      @close="showTranslationModal = false"
      @save="handleTranslationSaved"
    />

    <LearningReminderModal 
      :show="showReminderModal"
      @close="showReminderModal = false"
      @saved="handleReminderSaved"
    />

  </div>
</template>
