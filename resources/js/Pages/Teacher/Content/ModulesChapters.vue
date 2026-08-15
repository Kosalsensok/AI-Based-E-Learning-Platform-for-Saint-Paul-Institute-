<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  courses: Array<any>
  selectedCourseId?: number | null
}>()

const activeLeftTab = ref<'videos' | 'pdfs' | 'slides' | 'notes' | 'links'>('videos')
const librarySearch = ref('')
const unlinkedOnly = ref(true)

interface ChapterItem {
  id: number
  type: string
  icon: string
  name: string
  status?: string
  duration?: string
  pages?: string
  slides?: string
  questions?: string
}

interface Chapter {
  id: number
  title_kh: string
  title_en: string
  estimated_time: string
  prerequisite: string
  items: ChapterItem[]
}

interface CourseModule {
  id: number
  title_kh: string
  title_en: string
  status: string
  chapters: Chapter[]
}

// Sample Left Library Content Items
const libraryVideos = ref([
  { id: 101, type: 'video', name: '🎬 01_Introduction.mp4', duration: '12:45' },
  { id: 102, type: 'video', name: '🎬 02_Variables.mp4', duration: '18:30' },
])

const libraryPdfs = ref([
  { id: 201, type: 'pdf', name: '📄 C_Notes.pdf', pages: '45 pages' },
  { id: 202, type: 'pdf', name: '📄 Variables_CheatSheet.pdf', pages: '5 pages' },
])

const librarySlides = ref([
  { id: 301, type: 'slide', name: '📊 Chapter_1_Intro.pptx', slides: '12 slides' },
])

// Right Panel Course Structure State
const courseStructure = ref<CourseModule[]>([
  {
    id: 1,
    title_kh: 'Module 1: ការណែនាំអំពី C Programming',
    title_en: 'Module 1: Introduction to C',
    status: 'Published',
    chapters: [
      {
        id: 11,
        title_kh: 'Chapter 1.1: ប្រវត្តិរបស់ C',
        title_en: 'Chapter 1.1: History of C',
        estimated_time: '25 minutes',
        prerequisite: 'None',
        items: [
          { id: 1001, type: 'video', icon: '🎬', name: '01_Introduction.mp4', duration: '12:45', status: 'Ready' },
          { id: 1002, type: 'pdf', icon: '📄', name: 'C_History_Notes.pdf', pages: '45 pages', status: 'Ready' },
        ]
      },
      {
        id: 12,
        title_kh: 'Chapter 1.2: ការដំឡើងកម្មវិធី Setup',
        title_en: 'Chapter 1.2: Setup Environment',
        estimated_time: '20 minutes',
        prerequisite: 'Must complete Chapter 1.1',
        items: [
          { id: 1003, type: 'slide', icon: '📊', name: 'Setup_Slides.pptx', slides: '12 slides', status: 'Ready' },
        ]
      }
    ]
  },
  {
    id: 2,
    title_kh: 'Module 2: អថេរ និងប្រភេទទិន្នន័យ',
    title_en: 'Module 2: Variables & Data Types',
    status: 'Published',
    chapters: [
      {
        id: 21,
        title_kh: 'Chapter 2.1: អថេរ Variables',
        title_en: 'Chapter 2.1: Variables',
        estimated_time: '30 minutes',
        prerequisite: 'Must complete Module 1',
        items: [
          { id: 1004, type: 'video', icon: '🎬', name: '02_Variables.mp4', duration: '18:30', status: 'Ready' },
          { id: 1005, type: 'quiz', icon: '📝', name: 'Practice Quiz - Variables', questions: '10 Qs', status: 'Active' },
        ]
      },
      {
        id: 22,
        title_kh: 'Chapter 2.2: ប្រភេទទិន្នន័យ Data Types',
        title_en: 'Chapter 2.2: Data Types',
        estimated_time: '35 minutes',
        prerequisite: 'Must pass Chapter 2.1 Quiz ≥ 60%',
        items: []
      }
    ]
  }
])

const activeChapterId = ref<number | null>(11)

// Add Content to Chapter
const addItemToActiveChapter = (item: any) => {
  if (!activeChapterId.value) {
    alert('Please select a chapter on the Right Panel first!')
    return
  }

  for (const mod of courseStructure.value) {
    for (const chap of mod.chapters) {
      if (chap.id === activeChapterId.value) {
        chap.items.push({
          id: Date.now(),
          type: item.type,
          icon: item.type === 'video' ? '🎬' : (item.type === 'pdf' ? '📄' : '📊'),
          name: item.name,
          status: 'Ready',
          duration: item.duration,
          pages: item.pages,
          slides: item.slides
        })
        alert(`Added ${item.name} to ${chap.title_en} successfully!`)
        return
      }
    }
  }
}

// Create Module Modal
const showCreateModuleModal = ref(false)
const moduleForm = useForm({
  title_kh: '',
  title_en: '',
  description: '',
  position: 'After Module 1',
  completion_rule: 'Complete at least 80%',
  release_rule: 'Available immediately',
})

const submitCreateModule = () => {
  courseStructure.value.push({
    id: Date.now(),
    title_kh: moduleForm.title_kh || 'Module 3: ថ្មី',
    title_en: moduleForm.title_en || 'Module 3: New Module',
    status: 'Draft',
    chapters: []
  })
  showCreateModuleModal.value = false
  moduleForm.reset()
}

// Create Chapter Modal
const showCreateChapterModal = ref(false)
const chapterForm = useForm({
  title_kh: '',
  title_en: '',
  parent_module_id: 1,
  estimated_time: '25 minutes',
  prerequisite: 'Must complete previous chapter',
  visibility: 'Visible to students',
})

const submitCreateChapter = () => {
  const mod = courseStructure.value.find(m => m.id === Number(chapterForm.parent_module_id))
  if (mod) {
    mod.chapters.push({
      id: Date.now(),
      title_kh: chapterForm.title_kh || 'Chapter ថ្មី',
      title_en: chapterForm.title_en || 'New Chapter',
      estimated_time: chapterForm.estimated_time,
      prerequisite: chapterForm.prerequisite,
      items: []
    })
  }
  showCreateChapterModal.value = false
  chapterForm.reset()
}

// Lock Rules Modal
const showLockModal = ref(false)
const selectedChapterRules = ref<Chapter | null>(null)
const openLockRules = (chap: Chapter) => {
  selectedChapterRules.value = chap
  showLockModal.value = true
}

// Publish Checklist Modal
const showPublishModal = ref(false)

// Student Preview Modal
const showStudentPreviewModal = ref(false)
</script>

<template>
  <div class="space-y-5">
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-4 rounded-2xl border border-slate-200/80 dark:border-gray-700 shadow-sm">
      <div>
        <h2 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
          <span>📁 Modules & Chapters Hierarchy Builder</span>
        </h2>
        <p class="text-xs text-slate-500">
          Dual Panel Layout: Drag & drop content from Left Panel into Right Panel Course Structure.
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <button @click="showCreateModuleModal = true" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
          ➕ Add Module
        </button>
        <button @click="showCreateChapterModal = true" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
          ➕ Add Chapter
        </button>
        <button @click="showStudentPreviewModal = true" class="px-3.5 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl text-xs font-bold transition">
          👁 Preview as Student
        </button>
        <button @click="showPublishModal = true" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-500/20 transition">
          🚀 Publish Course
        </button>
      </div>
    </div>

    <!-- DUAL PANEL CONTAINER -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- LEFT PANEL: CONTENT LIBRARY -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-xs text-slate-800 dark:text-white uppercase tracking-wider">LEFT PANEL: Content Library</h3>
          <span class="text-[10px] px-2 py-0.5 bg-blue-100 text-blue-800 rounded font-bold">5 Items</span>
        </div>

        <!-- Left Tabs -->
        <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-gray-700 rounded-xl text-[11px] font-bold">
          <button @click="activeLeftTab = 'videos'" :class="['flex-1 py-1.5 rounded-lg transition', activeLeftTab === 'videos' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-500']">🎬 Videos</button>
          <button @click="activeLeftTab = 'pdfs'" :class="['flex-1 py-1.5 rounded-lg transition', activeLeftTab === 'pdfs' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-500']">📄 PDFs</button>
          <button @click="activeLeftTab = 'slides'" :class="['flex-1 py-1.5 rounded-lg transition', activeLeftTab === 'slides' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-slate-500']">📊 Slides</button>
        </div>

        <!-- Search & Filter -->
        <div class="space-y-2">
          <input
            v-model="librarySearch"
            type="text"
            placeholder="Search item in library..."
            class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-xs"
          />
          <label class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 cursor-pointer">
            <input v-model="unlinkedOnly" type="checkbox" class="rounded text-blue-600" />
            <span>Unlinked Only ☑️</span>
          </label>
        </div>

        <!-- Library Items List -->
        <div class="space-y-2 text-xs">
          <!-- Videos List -->
          <template v-if="activeLeftTab === 'videos'">
            <div
              v-for="v in libraryVideos"
              :key="v.id"
              class="p-3 rounded-xl bg-slate-50 dark:bg-gray-700/50 border border-slate-200/80 dark:border-gray-700 flex items-center justify-between hover:bg-blue-50/70 transition group"
            >
              <div>
                <p class="font-bold text-slate-800 dark:text-white">{{ v.name }}</p>
                <p class="text-[10px] text-slate-400">Duration: {{ v.duration }}</p>
              </div>
              <button
                @click="addItemToActiveChapter(v)"
                class="px-2.5 py-1 bg-blue-600 text-white rounded-lg text-[10px] font-bold opacity-80 group-hover:opacity-100"
                title="Add to selected chapter"
              >
                + Add
              </button>
            </div>
          </template>

          <!-- PDFs List -->
          <template v-if="activeLeftTab === 'pdfs'">
            <div
              v-for="p in libraryPdfs"
              :key="p.id"
              class="p-3 rounded-xl bg-slate-50 dark:bg-gray-700/50 border border-slate-200/80 dark:border-gray-700 flex items-center justify-between hover:bg-rose-50/70 transition group"
            >
              <div>
                <p class="font-bold text-slate-800 dark:text-white">{{ p.name }}</p>
                <p class="text-[10px] text-slate-400">{{ p.pages }}</p>
              </div>
              <button
                @click="addItemToActiveChapter(p)"
                class="px-2.5 py-1 bg-rose-600 text-white rounded-lg text-[10px] font-bold opacity-80 group-hover:opacity-100"
              >
                + Add
              </button>
            </div>
          </template>

          <!-- Slides List -->
          <template v-if="activeLeftTab === 'slides'">
            <div
              v-for="s in librarySlides"
              :key="s.id"
              class="p-3 rounded-xl bg-slate-50 dark:bg-gray-700/50 border border-slate-200/80 dark:border-gray-700 flex items-center justify-between hover:bg-amber-50/70 transition group"
            >
              <div>
                <p class="font-bold text-slate-800 dark:text-white">{{ s.name }}</p>
                <p class="text-[10px] text-slate-400">{{ s.slides }}</p>
              </div>
              <button
                @click="addItemToActiveChapter(s)"
                class="px-2.5 py-1 bg-amber-600 text-white rounded-lg text-[10px] font-bold opacity-80 group-hover:opacity-100"
              >
                + Add
              </button>
            </div>
          </template>
        </div>
      </div>

      <!-- RIGHT PANEL: COURSE STRUCTURE -->
      <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <div>
            <h3 class="font-bold text-sm text-slate-800 dark:text-white uppercase tracking-wider">RIGHT PANEL: Course Structure</h3>
            <p class="text-xs text-slate-400">Course: <strong class="text-blue-600">C Programming Basics</strong> (🟢 Published)</p>
          </div>
          <span class="text-xs font-semibold text-slate-500">Selected Chapter: #{{ activeChapterId }}</span>
        </div>

        <!-- Modules Tree -->
        <div class="space-y-4 text-xs">
          <div
            v-for="mod in courseStructure"
            :key="mod.id"
            class="border border-slate-200 dark:border-gray-700 rounded-2xl p-4 bg-slate-50/50 dark:bg-gray-700/20 space-y-3"
          >
            <!-- Module Header -->
            <div class="flex items-center justify-between">
              <div>
                <h4 class="font-extrabold text-sm text-slate-800 dark:text-white">📦 {{ mod.title_en }}</h4>
                <p class="text-[11px] text-blue-600 font-semibold">{{ mod.title_kh }}</p>
              </div>
              <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">{{ mod.status }}</span>
                <button class="px-2 py-1 bg-white dark:bg-gray-700 rounded border border-slate-200 text-slate-600 font-semibold">✏️ Edit</button>
              </div>
            </div>

            <!-- Chapters List -->
            <div class="pl-4 space-y-3 border-l-2 border-blue-500/60 ml-2">
              <div
                v-for="chap in mod.chapters"
                :key="chap.id"
                @click="activeChapterId = chap.id"
                :class="[
                  'p-3.5 bg-white dark:bg-gray-800 rounded-xl border transition space-y-2 cursor-pointer',
                  activeChapterId === chap.id ? 'border-blue-500 ring-2 ring-blue-500/20 shadow-md' : 'border-slate-200 dark:border-gray-700'
                ]"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-800 dark:text-white">📖 {{ chap.title_en }}</span>
                    <span class="text-[10px] text-slate-400">({{ chap.estimated_time }})</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <button @click.stop="openLockRules(chap)" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[10px] font-bold">
                      🔒 Rules
                    </button>
                  </div>
                </div>

                <!-- Chapter Items inside -->
                <div v-if="chap.items && chap.items.length > 0" class="pl-3 space-y-1.5 pt-1 border-t border-slate-100 dark:border-gray-700">
                  <div
                    v-for="item in chap.items"
                    :key="item.id"
                    class="p-2 rounded-lg bg-slate-50 dark:bg-gray-700/50 flex items-center justify-between text-[11px] font-medium"
                  >
                    <span class="text-slate-800 dark:text-white">{{ item.icon }} {{ item.name }}</span>
                    <div class="flex gap-2">
                      <span class="text-slate-400">{{ item.duration || item.pages || item.slides || item.questions }}</span>
                      <span class="text-rose-500 font-bold hover:underline cursor-pointer">Remove</span>
                    </div>
                  </div>
                </div>
                <div v-else class="p-3 border border-dashed border-slate-300 dark:border-gray-700 rounded-lg text-center text-slate-400 text-[11px]">
                  Drop content here or select chapter and click "+ Add" on Left Panel
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ➕ CREATE MODULE MODAL -->
    <div v-if="showCreateModuleModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 dark:text-white">➕ Create New Module</h3>
        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-semibold mb-1">Module Title KH</label>
            <input v-model="moduleForm.title_kh" type="text" placeholder="ការណែនាំអំពី C" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
          </div>
          <div>
            <label class="block font-semibold mb-1">Module Title EN</label>
            <input v-model="moduleForm.title_en" type="text" placeholder="Introduction to C" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
          </div>
          <div>
            <label class="block font-semibold mb-1">Description</label>
            <textarea v-model="moduleForm.description" rows="2" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700"></textarea>
          </div>
        </div>
        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-gray-700">
          <button @click="showCreateModuleModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">❌ Cancel</button>
          <button @click="submitCreateModule" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold shadow">✅ Create Module</button>
        </div>
      </div>
    </div>

    <!-- ➕ CREATE CHAPTER MODAL -->
    <div v-if="showCreateChapterModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 dark:text-white">➕ Create New Chapter</h3>
        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-semibold mb-1">Parent Module</label>
            <select v-model="chapterForm.parent_module_id" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700">
              <option v-for="m in courseStructure" :key="m.id" :value="m.id">{{ m.title_en }}</option>
            </select>
          </div>
          <div>
            <label class="block font-semibold mb-1">Chapter Title KH</label>
            <input v-model="chapterForm.title_kh" type="text" placeholder="ប្រវត្តិរបស់ C" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
          </div>
          <div>
            <label class="block font-semibold mb-1">Chapter Title EN</label>
            <input v-model="chapterForm.title_en" type="text" placeholder="History of C" class="w-full p-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700" />
          </div>
        </div>
        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-gray-700">
          <button @click="showCreateChapterModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">❌ Cancel</button>
          <button @click="submitCreateChapter" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold shadow">✅ Create Chapter</button>
        </div>
      </div>
    </div>

    <!-- 🔒 LOCK & PREREQUISITE RULES MODAL -->
    <div v-if="showLockModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 dark:text-white">🔒 Access Rules – {{ selectedChapterRules?.title_en }}</h3>
        <div class="space-y-3 text-xs">
          <p class="font-bold text-slate-500">Prerequisites:</p>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" checked class="rounded text-blue-600" />
            <span>☑️ Must complete Chapter 2.1</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" checked class="rounded text-blue-600" />
            <span>☑️ Must pass Practice Quiz with score ≥ 60%</span>
          </label>
        </div>
        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-gray-700">
          <button @click="showLockModal = false" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold">💾 Save Rules</button>
        </div>
      </div>
    </div>

    <!-- 🚀 PUBLISH CHECKLIST MODAL -->
    <div v-if="showPublishModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 dark:text-white">🚀 Publish Course Checklist</h3>
        <div class="space-y-2 text-xs">
          <div class="p-2.5 bg-emerald-50 text-emerald-800 rounded-xl font-bold">✅ Has at least 1 chapter</div>
          <div class="p-2.5 bg-emerald-50 text-emerald-800 rounded-xl font-bold">✅ All videos are ready</div>
          <div class="p-2.5 bg-emerald-50 text-emerald-800 rounded-xl font-bold">✅ PDFs are attached</div>
          <div class="p-2.5 bg-amber-50 text-amber-800 rounded-xl font-bold">⚠️ Chapter 2.2 has no quiz</div>
        </div>
        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-gray-700">
          <button @click="showPublishModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">✏️ Fix Issues</button>
          <button @click="showPublishModal = false" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold shadow">🚀 Publish Course</button>
        </div>
      </div>
    </div>

    <!-- 👁 STUDENT PREVIEW MODAL -->
    <div v-if="showStudentPreviewModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
          <h3 class="text-base font-bold text-slate-800 dark:text-white">👁 Student View Preview: C Programming Basics</h3>
          <button @click="showStudentPreviewModal = false" class="text-slate-400 hover:text-slate-700"><i class="pi pi-times"></i></button>
        </div>
        <div class="space-y-3 text-xs max-h-80 overflow-y-auto p-3 bg-slate-50 dark:bg-gray-900 rounded-xl">
          <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-slate-200">
            <h4 class="font-bold text-emerald-600">📦 Module 1: Introduction to C ✅ Completed</h4>
            <p class="text-slate-500 pl-4">📖 Chapter 1.1: History of C (🎬 Video watched, 📄 PDF read)</p>
          </div>
          <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-slate-200">
            <h4 class="font-bold text-blue-600">📦 Module 2: Variables & Data Types 🟡 In Progress</h4>
            <p class="text-slate-500 pl-4">▶️ Chapter 2.1: Variables (🎬 Video, 📄 PDF, 📊 Slides)</p>
            <p class="text-amber-600 font-bold pl-4">🔒 Chapter 2.2: Data Types (Unlock after completing Chapter 2.1)</p>
          </div>
        </div>
        <div class="flex justify-end">
          <button @click="showStudentPreviewModal = false" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold">Close Student Preview</button>
        </div>
      </div>
    </div>
  </div>
</template>
