<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { 
  Package, 
  CheckCircle2, 
  Clock, 
  Archive, 
  Search, 
  X, 
  Trash2, 
  Eye, 
  MoreVertical, 
  HardDrive, 
  FileText,
  RotateCcw,
  Sparkles,
  CheckSquare,
  DownloadCloud,
  LayoutGrid,
  List,
  Video,
  Presentation,
  StickyNote,
  Plus,
  BookOpen
} from 'lucide-vue-next'

import StatusBadge from './StatusBadge.vue'
import AssetTypeBadge from './AssetTypeBadge.vue'

const props = withDefaults(defineProps<{
  stats?: any
  contentItems?: Array<any>
  courses?: Array<any>
  activeType?: string
}>(), {
  stats: () => ({}),
  contentItems: () => [],
  courses: () => [],
  activeType: 'All'
})

const emit = defineEmits(['openUpload', 'openPreview', 'updateType'])

const search = ref('')
const selectedType = ref(props.activeType || 'All')
const selectedStatus = ref('All')
const selectedCourse = ref('All')
const viewMode = ref<'grid' | 'list'>('grid')

// Sync prop activeType with internal selectedType
watch(() => props.activeType, (newVal) => {
  if (newVal && newVal !== selectedType.value) {
    selectedType.value = newVal
  }
})

const handleTypeDropdownChange = () => {
  emit('updateType', selectedType.value)
}

// Selection & Bulk Action state
const selectedIds = ref<number[]>([])
const activeActionMenuId = ref<number | null>(null)

const toggleActionMenu = (id: number, event: Event) => {
  event.stopPropagation()
  activeActionMenuId.value = activeActionMenuId.value === id ? null : id
}

const closeMenus = () => {
  activeActionMenuId.value = null
}

const filteredItems = computed(() => {
  return (props.contentItems || []).filter(item => {
    const matchesSearch = !search.value || 
      item.title.toLowerCase().includes(search.value.toLowerCase()) ||
      (item.course && item.course.toLowerCase().includes(search.value.toLowerCase())) ||
      (item.teacher && item.teacher.toLowerCase().includes(search.value.toLowerCase()))
    
    const matchesType = selectedType.value === 'All' || item.type.toLowerCase() === selectedType.value.toLowerCase()
    const matchesStatus = selectedStatus.value === 'All' || item.status.toLowerCase() === selectedStatus.value.toLowerCase()
    const matchesCourse = selectedCourse.value === 'All' || item.course === selectedCourse.value

    return matchesSearch && matchesType && matchesStatus && matchesCourse
  })
})

const hasActiveFilters = computed(() => {
  return search.value !== '' || selectedType.value !== 'All' || selectedStatus.value !== 'All' || selectedCourse.value !== 'All'
})

const resetFilters = () => {
  search.value = ''
  selectedType.value = 'All'
  selectedStatus.value = 'All'
  selectedCourse.value = 'All'
  emit('updateType', 'All')
}

// Select All logic
const isAllSelected = computed({
  get() {
    if (filteredItems.value.length === 0) return false
    return filteredItems.value.every(item => selectedIds.value.includes(item.id))
  },
  set(val: boolean) {
    if (val) {
      selectedIds.value = filteredItems.value.map(item => item.id)
    } else {
      selectedIds.value = []
    }
  }
})

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedIds.value = []
  } else {
    selectedIds.value = filteredItems.value.map(item => item.id)
  }
}

const toggleSelectCard = (id: number, event: Event) => {
  event.stopPropagation()
  const idx = selectedIds.value.indexOf(id)
  if (idx > -1) {
    selectedIds.value.splice(idx, 1)
  } else {
    selectedIds.value.push(id)
  }
}

const handleCardClick = (item: any, event: Event) => {
  // Prevent preview if clicking dropdown or checkbox
  const target = event.target as HTMLElement
  if (target.closest('.no-card-click') || target.tagName === 'INPUT' || target.tagName === 'BUTTON') {
    return
  }
  closeMenus()
  emit('openPreview', item)
}

// Single Item Actions
const toggleStatus = (item: any) => {
  item.status = item.status === 'Published' ? 'Draft' : 'Published'
  closeMenus()
}

const deleteSingleItem = (id: number) => {
  if (confirm('Are you sure you want to delete this asset?')) {
    const idx = props.contentItems.findIndex(i => i.id === id)
    if (idx !== -1) {
      props.contentItems.splice(idx, 1)
    }
    const selIdx = selectedIds.value.indexOf(id)
    if (selIdx !== -1) selectedIds.value.splice(selIdx, 1)
  }
  closeMenus()
}

// Bulk Actions
const bulkPublish = () => {
  props.contentItems.forEach(item => {
    if (selectedIds.value.includes(item.id)) {
      item.status = 'Published'
    }
  })
  selectedIds.value = []
}

const bulkDraft = () => {
  props.contentItems.forEach(item => {
    if (selectedIds.value.includes(item.id)) {
      item.status = 'Draft'
    }
  })
  selectedIds.value = []
}

const bulkExport = () => {
  const selectedItems = props.contentItems.filter(item => selectedIds.value.includes(item.id))
  if (selectedItems.length === 0) return
  const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(selectedItems, null, 2))
  const downloadAnchor = document.createElement('a')
  downloadAnchor.setAttribute("href", dataStr)
  downloadAnchor.setAttribute("download", `content_export_${Date.now()}.json`)
  document.body.appendChild(downloadAnchor)
  downloadAnchor.click()
  downloadAnchor.remove()
}

const bulkDelete = () => {
  if (confirm(`Are you sure you want to delete the ${selectedIds.value.length} selected asset(s)?`)) {
    for (let i = props.contentItems.length - 1; i >= 0; i--) {
      if (selectedIds.value.includes(props.contentItems[i].id)) {
        props.contentItems.splice(i, 1)
      }
    }
    selectedIds.value = []
  }
}

// Computed stats for display fallback
const computedPublishedCount = computed(() => props.stats.published_count ?? (props.contentItems || []).filter(i => i.status === 'Published').length)
const computedDraftCount = computed(() => props.stats.draft_count ?? (props.contentItems || []).filter(i => i.status === 'Draft').length)
const computedArchivedCount = computed(() => props.stats.archived_count ?? (props.contentItems || []).filter(i => i.status === 'Archived').length)
</script>

<template>
  <div class="space-y-6 text-slate-100" @click="closeMenus">
    
    <!-- Executive KPI Cards Grid (4 Spacious High-Value Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- 1. Total Content Items -->
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm flex items-center justify-between group hover:border-indigo-500/30 hover:bg-slate-900/95 transition-all duration-200">
        <div class="space-y-1 pr-2">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs font-semibold text-slate-400 tracking-wide uppercase">TOTAL ASSETS</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-950/80 text-indigo-300 font-medium border border-indigo-800/40">All Types</span>
          </div>
          <p class="text-2xl font-bold text-white font-mono mt-0.5 tracking-tight">
            {{ (props.stats.total_items || props.contentItems.length).toLocaleString() }}
          </p>
          <p class="text-[11px] text-slate-400 font-normal">Total system digital assets</p>
        </div>
        <!-- Flaticon Style Isometric Digital Package Box Vector Icon -->
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500/20 via-sky-500/10 to-indigo-950 border border-indigo-500/30 flex items-center justify-center shrink-0 group-hover:scale-105 transition-all duration-200 shadow-md shadow-indigo-950/40">
          <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L3 6.5L12 11L21 6.5L12 2Z" fill="#818CF8" fill-opacity="0.4" stroke="#818CF8" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M3 6.5V17.5L12 22V11L3 6.5Z" fill="#6366F1" fill-opacity="0.6" stroke="#6366F1" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M21 6.5V17.5L12 22V11L21 6.5Z" fill="#38BDF8" fill-opacity="0.45" stroke="#38BDF8" stroke-width="1.8" stroke-linejoin="round"/>
            <circle cx="12" cy="11" r="2" fill="#34D399"/>
          </svg>
        </div>
      </div>

      <!-- 2. Published Live -->
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm flex items-center justify-between group hover:border-emerald-500/30 hover:bg-slate-900/95 transition-all duration-200">
        <div class="space-y-1 pr-2">
          <div class="flex items-center gap-1.5">
            <span class="text-xs font-semibold text-slate-400 tracking-wide uppercase">PUBLISHED & LIVE</span>
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          </div>
          <p class="text-2xl font-bold text-emerald-400 font-mono mt-0.5 tracking-tight">
            {{ computedPublishedCount.toLocaleString() }}
          </p>
          <p class="text-[11px] text-slate-400 font-normal">Active & student accessible</p>
        </div>
        <!-- Flaticon Style Verified Live Checkmark Shield Vector Icon -->
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 via-teal-500/10 to-emerald-950 border border-emerald-500/30 flex items-center justify-center shrink-0 group-hover:scale-105 transition-all duration-200 shadow-md shadow-emerald-950/40">
          <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" fill="#10B981" fill-opacity="0.25" stroke="#34D399" stroke-width="1.8"/>
            <path d="M8 12.5L10.5 15L16 9.5" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="6" stroke="#6EE7B7" stroke-width="1" stroke-dasharray="2 2"/>
          </svg>
        </div>
      </div>

      <!-- 3. Draft / In Review -->
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm flex items-center justify-between group hover:border-amber-500/30 hover:bg-slate-900/95 transition-all duration-200">
        <div class="space-y-1 pr-2">
          <span class="text-xs font-semibold text-slate-400 tracking-wide uppercase">DRAFT / IN REVIEW</span>
          <p class="text-2xl font-bold text-amber-400 font-mono mt-0.5 tracking-tight">
            {{ computedDraftCount.toLocaleString() }}
          </p>
          <p class="text-[11px] text-slate-400 font-normal">Pending review or publish</p>
        </div>
        <!-- Flaticon Style Pending Document Edit Clock Vector Icon -->
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 via-yellow-500/10 to-amber-950 border border-amber-500/30 flex items-center justify-center shrink-0 group-hover:scale-105 transition-all duration-200 shadow-md shadow-amber-950/40">
          <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="4" y="3" width="12" height="17" rx="2" fill="#F59E0B" fill-opacity="0.25" stroke="#FBBF24" stroke-width="1.8"/>
            <line x1="7" y1="7" x2="13" y2="7" stroke="#FDE047" stroke-width="1.8" stroke-linecap="round"/>
            <line x1="7" y1="10" x2="11" y2="10" stroke="#FDE047" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="16" cy="16" r="5.5" fill="#78350F" stroke="#F59E0B" stroke-width="1.8"/>
            <path d="M16 13.5V16L18 17.2" stroke="#FBBF24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>

      <!-- 4. Archived / Inactive -->
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm flex items-center justify-between group hover:border-slate-700/80 hover:bg-slate-900/95 transition-all duration-200">
        <div class="space-y-1 pr-2">
          <span class="text-xs font-semibold text-slate-400 tracking-wide uppercase">ARCHIVED</span>
          <p class="text-2xl font-bold text-slate-200 font-mono mt-0.5 tracking-tight">
            {{ computedArchivedCount.toLocaleString() }}
          </p>
          <p class="text-[11px] text-slate-400 font-normal">Stored & inactive items</p>
        </div>
        <!-- Flaticon Style Archive Box Lock Vault Vector Icon -->
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-slate-700/30 via-slate-800/40 to-slate-950 border border-slate-700/60 flex items-center justify-center shrink-0 group-hover:scale-105 transition-all duration-200 shadow-md shadow-slate-950/40">
          <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="5" width="18" height="5" rx="1.5" fill="#94A3B8" fill-opacity="0.3" stroke="#CBD5E1" stroke-width="1.8"/>
            <path d="M4.5 10H19.5V18.5C19.5 19.6046 18.6046 20.5 17.5 20.5H6.5C5.39543 20.5 4.5 19.6046 4.5 18.5V10Z" fill="#64748B" fill-opacity="0.25" stroke="#94A3B8" stroke-width="1.8"/>
            <rect x="9.5" y="13.5" width="5" height="2" rx="1" fill="#E2E8F0"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- Main Toolbar & Control Bar -->
    <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-xl space-y-4">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 class="text-xl font-extrabold text-white flex items-center gap-3 tracking-tight">
            <!-- Flaticon Style Open Book Library Vector Icon -->
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500/20 via-sky-500/10 to-indigo-950 border border-indigo-500/30 flex items-center justify-center shrink-0 shadow-xs">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5C10 3.8 6.5 3.5 3 5V18C6.5 16.5 10 16.8 12 18V5Z" fill="#818CF8" fill-opacity="0.4" stroke="#818CF8" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M12 5C14 3.8 17.5 3.5 21 5V18C17.5 16.5 14 16.8 12 18V5Z" fill="#38BDF8" fill-opacity="0.45" stroke="#38BDF8" stroke-width="1.8" stroke-linejoin="round"/>
                <line x1="12" y1="5" x2="12" y2="18" stroke="#34D399" stroke-width="1.8" stroke-linecap="round"/>
              </svg>
            </div>
            <span>CONTENT LIBRARY MANAGEMENT</span>
          </h2>
        </div>
        <button 
          @click="$emit('openUpload')"
          class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md shadow-indigo-950/40 hover:shadow-indigo-600/20 transition-all duration-200 flex items-center gap-2.5 text-xs cursor-pointer active:scale-[0.98] border border-indigo-400/30"
        >
          <!-- Flaticon Style Vector Plus Badge Icon -->
          <svg class="w-4.5 h-4.5 shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="9.5" fill="rgba(255, 255, 255, 0.2)" stroke="#FFFFFF" stroke-width="1.5"/>
            <path d="M12 7.5V16.5M7.5 12H16.5" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span>Upload New Content</span>
        </button>
      </div>

      <!-- Controls Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs pt-3 border-t border-slate-800">
        <!-- Search Input -->
        <div class="relative">
          <input 
            v-model="search"
            type="text" 
            placeholder="Search filename, course, teacher..."
            class="w-full p-2.5 pl-9 pr-8 bg-slate-950/90 border border-slate-700/80 rounded-xl focus:ring-2 focus:ring-indigo-500 text-white placeholder:text-slate-500 focus:outline-none shadow-inner" 
          />
          <Search class="w-4 h-4 text-slate-500 absolute left-3 top-3 pointer-events-none" />
          <button v-if="search" @click="search = ''" class="absolute right-3 top-3 text-slate-400 hover:text-white">
            <X class="w-3.5 h-3.5" />
          </button>
        </div>

        <!-- Type Select -->
        <select v-model="selectedType" @change="handleTypeDropdownChange" class="p-2.5 bg-slate-950/90 border border-slate-700/80 rounded-xl text-slate-200 focus:ring-2 focus:ring-indigo-500 font-medium">
          <option value="All">All Asset Types</option>
          <option value="video">Videos</option>
          <option value="pdf">PDFs</option>
          <option value="slide">Slides</option>
          <option value="note">Notes / Documents</option>
        </select>

        <!-- Status Select -->
        <select v-model="selectedStatus" class="p-2.5 bg-slate-950/90 border border-slate-700/80 rounded-xl text-slate-200 focus:ring-2 focus:ring-indigo-500 font-medium">
          <option value="All">All Statuses</option>
          <option value="Published">Published (Live)</option>
          <option value="Draft">Draft</option>
          <option value="Archived">Archived</option>
        </select>

        <!-- View Mode Switches -->
        <div class="flex items-center gap-2 justify-end">
          <div class="bg-slate-950 p-1 rounded-xl border border-slate-800 flex items-center gap-1">
            <button 
              @click="viewMode = 'grid'" 
              :class="['px-3.5 py-1.5 rounded-lg font-bold text-xs transition flex items-center gap-1.5 cursor-pointer', viewMode === 'grid' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white']"
            >
              <LayoutGrid class="w-3.5 h-3.5" />
              <span>Grid</span>
            </button>
            <button 
              @click="viewMode = 'list'" 
              :class="['px-3.5 py-1.5 rounded-lg font-bold text-xs transition flex items-center gap-1.5 cursor-pointer', viewMode === 'list' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white']"
            >
              <List class="w-3.5 h-3.5" />
              <span>List</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Active Filter Badges & 1-Click Reset Bar -->
      <div v-if="hasActiveFilters" class="flex items-center justify-between gap-3 pt-2 text-xs flex-wrap border-t border-slate-800/60 bg-indigo-950/30 p-2.5 rounded-xl border">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="text-slate-400 font-bold text-[11px] uppercase tracking-wider">Active Filters:</span>
          
          <span v-if="search" class="px-2.5 py-1 rounded-lg bg-indigo-900/80 border border-indigo-700 text-indigo-200 font-mono flex items-center gap-1 text-[11px]">
            <span>Search: "{{ search }}"</span>
            <button @click="search = ''" class="hover:text-white ml-1">✕</button>
          </span>

          <span v-if="selectedType !== 'All'" class="px-2.5 py-1 rounded-lg bg-indigo-900/80 border border-indigo-700 text-indigo-200 font-mono flex items-center gap-1 text-[11px]">
            <span>Type: {{ selectedType }}</span>
            <button @click="selectedType = 'All'" class="hover:text-white ml-1">✕</button>
          </span>

          <span v-if="selectedStatus !== 'All'" class="px-2.5 py-1 rounded-lg bg-indigo-900/80 border border-indigo-700 text-indigo-200 font-mono flex items-center gap-1 text-[11px]">
            <span>Status: {{ selectedStatus }}</span>
            <button @click="selectedStatus = 'All'" class="hover:text-white ml-1">✕</button>
          </span>

          <span class="px-2 py-0.5 rounded text-[11px] bg-slate-800 text-slate-300 font-mono font-bold">
            Showing {{ filteredItems.length }} of {{ props.contentItems.length }}
          </span>
        </div>

        <button 
          @click="resetFilters" 
          class="px-3 py-1 bg-slate-800 hover:bg-rose-900/80 hover:border-rose-700 text-rose-300 rounded-lg border border-slate-700 transition flex items-center gap-1 font-bold text-xs cursor-pointer active:scale-95"
        >
          <RotateCcw class="w-3.5 h-3.5" />
          <span>Reset Filters</span>
        </button>
      </div>
    </div>

    <!-- Grid View (Clickable Modern Cards with 3-Dots Action Menu) -->
    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
      <div 
        v-for="item in filteredItems" 
        :key="item.id"
        @click="handleCardClick(item, $event)"
        :class="[
          'bg-slate-900/90 border rounded-2xl p-4 shadow-xl hover:shadow-2xl hover:shadow-indigo-500/10 transition duration-300 group flex flex-col justify-between cursor-pointer relative',
          selectedIds.includes(item.id) ? 'border-indigo-500/80 bg-indigo-950/20 shadow-md shadow-indigo-950/50' : 'border-slate-800/90 hover:border-indigo-500/50'
        ]"
      >
        <div class="space-y-3">
          <!-- Card Thumbnail Box (Pixel-Perfect Concentric Frame & Zero Subpixel Bleed) -->
          <div class="relative w-full bg-slate-950 rounded-xl border border-slate-800/90 overflow-hidden shadow-sm group-hover:border-indigo-500/40 transition-all duration-300 aspect-video flex items-center justify-center isolate">
            <!-- Multi-select Checkbox (Top Left) -->
            <div class="absolute top-2.5 left-2.5 z-20 no-card-click flex items-center h-7" @click.stop>
              <div class="w-7 h-7 flex items-center justify-center rounded-xl bg-slate-950/80 border border-slate-700/80 backdrop-blur-md shadow-xs transition hover:bg-slate-900">
                <input 
                  type="checkbox" 
                  :checked="selectedIds.includes(item.id)" 
                  @change="toggleSelectCard(item.id, $event)"
                  class="w-3.5 h-3.5 rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-1 focus:ring-indigo-500/40 cursor-pointer"
                />
              </div>
            </div>

            <!-- Asset Type Badge & 3-Dots Action Menu (Top Right) -->
            <div class="absolute top-2.5 right-2.5 z-20 flex items-center gap-1.5 no-card-click h-7" @click.stop>
              <AssetTypeBadge :type="item.type" />

              <!-- 3-Dots Dropdown Toggle -->
              <div class="relative flex items-center h-7">
                <button 
                  @click="toggleActionMenu(item.id, $event)" 
                  class="w-7 h-7 flex items-center justify-center rounded-xl bg-slate-950/80 border border-slate-700/80 text-slate-300 hover:text-white hover:bg-slate-800 backdrop-blur-md transition cursor-pointer shadow-xs shrink-0"
                >
                  <MoreVertical class="w-3.5 h-3.5" />
                </button>

                <!-- Action Menu Dropdown -->
                <div 
                  v-if="activeActionMenuId === item.id" 
                  class="absolute right-0 top-7 w-44 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl z-30 py-1.5 text-xs font-medium divide-y divide-slate-800"
                >
                  <div class="py-1">
                    <button @click="$emit('openPreview', item)" class="w-full px-3 py-1.5 text-left text-slate-200 hover:bg-indigo-600 hover:text-white flex items-center gap-2">
                      <Eye class="w-3.5 h-3.5 text-indigo-400" />
                      <span>Preview Content</span>
                    </button>
                    <button @click="toggleStatus(item)" class="w-full px-3 py-1.5 text-left text-slate-200 hover:bg-indigo-600 hover:text-white flex items-center gap-2">
                      <CheckCircle2 class="w-3.5 h-3.5 text-emerald-400" />
                      <span>Set to {{ item.status === 'Published' ? 'Draft' : 'Published' }}</span>
                    </button>
                  </div>
                  <div class="py-1">
                    <button @click="deleteSingleItem(item.id)" class="w-full px-3 py-1.5 text-left text-rose-400 hover:bg-rose-600 hover:text-white flex items-center gap-2">
                      <Trash2 class="w-3.5 h-3.5" />
                      <span>Delete Asset</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Thumbnail Image or Flaticon Vector Icon Fallback -->
            <template v-if="item.thumbnail_url || (item.thumbnail && (item.thumbnail.startsWith('data:') || item.thumbnail.startsWith('http') || item.thumbnail.startsWith('/')))">
              <img 
                :src="item.thumbnail_url || item.thumbnail" 
                class="absolute inset-0 w-full h-full object-cover object-center group-hover:scale-[1.03] transition-transform duration-300 ease-out" 
                :alt="item.title" 
              />
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-black/30"></div>

              <!-- Centered Flaticon Play Vector Overlay for Video Thumbnails -->
              <div v-if="item.type === 'video'" class="absolute inset-0 flex items-center justify-center z-10 pointer-events-none">
                <div class="w-11 h-11 rounded-full bg-slate-950/60 border border-white/30 backdrop-blur-md flex items-center justify-center shadow-xl group-hover:scale-110 group-hover:bg-indigo-600/85 group-hover:border-indigo-400 transition-all duration-300">
                  <svg class="w-5 h-5 ml-0.5" viewBox="0 0 24 24" fill="none">
                    <polygon points="8,5 19,12 8,19" fill="#FFFFFF"/>
                  </svg>
                </div>
              </div>
            </template>

            <!-- Single Emoji Thumbnail Fallback -->
            <template v-else-if="item.thumbnail && item.thumbnail.length <= 4">
              <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-950 to-indigo-950">
                <span class="text-4xl transform group-hover:scale-110 transition duration-300 drop-shadow-md select-none">
                  {{ item.thumbnail }}
                </span>
              </div>
            </template>

            <!-- Flaticon Vector Icon Fallback per Asset Type (Centered Perfectly) -->
            <template v-else>
              <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-950 to-indigo-950 p-4">
                <!-- Video Icon -->
                <div v-if="item.type === 'video'" class="w-12 h-12 rounded-2xl bg-sky-500/20 border border-sky-500/30 flex items-center justify-center shadow-lg shadow-sky-950/50 group-hover:scale-110 transition duration-300">
                  <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                    <rect x="2" y="4" width="20" height="16" rx="3" fill="#0284C7" fill-opacity="0.3" stroke="#38BDF8" stroke-width="1.8"/>
                    <path d="M10 8.5L16 12L10 15.5V8.5Z" fill="#38BDF8" stroke="#38BDF8" stroke-width="1.5" stroke-linejoin="round"/>
                  </svg>
                </div>

                <!-- PDF Icon -->
                <div v-else-if="item.type === 'pdf'" class="w-12 h-12 rounded-2xl bg-rose-500/20 border border-rose-500/30 flex items-center justify-center shadow-lg shadow-rose-950/50 group-hover:scale-110 transition duration-300">
                  <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                    <path d="M14 2H6C4.89 2 4 2.89 4 4V20C4 21.1 4.89 22 6 22H18C19.1 22 20 21.1 20 20V8L14 2Z" fill="#E11D48" fill-opacity="0.3" stroke="#F43F5E" stroke-width="1.8"/>
                    <path d="M14 2V8H20" stroke="#F43F5E" stroke-width="1.8"/>
                  </svg>
                </div>

                <!-- Slide Icon -->
                <div v-else-if="item.type === 'slide'" class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center shadow-lg shadow-amber-950/50 group-hover:scale-110 transition duration-300">
                  <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="4" width="18" height="12" rx="2.5" fill="#D97706" fill-opacity="0.3" stroke="#F59E0B" stroke-width="1.8"/>
                    <path d="M8 20L12 16L16 20" stroke="#FBBF24" stroke-width="1.8" stroke-linecap="round"/>
                  </svg>
                </div>

                <!-- Note / Document Icon -->
                <div v-else class="w-12 h-12 rounded-2xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center shadow-lg shadow-purple-950/50 group-hover:scale-110 transition duration-300">
                  <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                    <rect x="4" y="3" width="16" height="18" rx="2.5" fill="#7C3AED" fill-opacity="0.3" stroke="#A855F7" stroke-width="1.8"/>
                    <line x1="8" y1="8" x2="16" y2="8" stroke="#C084FC" stroke-width="1.8" stroke-linecap="round"/>
                    <line x1="8" y1="12" x2="16" y2="12" stroke="#C084FC" stroke-width="1.8" stroke-linecap="round"/>
                  </svg>
                </div>
              </div>
            </template>
          </div>

          <!-- Metadata Section (Clear Visual Hierarchy & High Contrast) -->
          <div class="space-y-2 pt-1">
            <h4 class="font-bold text-white text-base line-clamp-1 group-hover:text-indigo-300 transition-colors duration-200 leading-snug">
              {{ item.title }}
            </h4>
            
            <p class="text-xs text-indigo-300 font-semibold line-clamp-1 flex items-center gap-1.5">
              <span class="text-indigo-400">📚</span>
              <span>{{ item.course }}</span>
            </p>
            
            <!-- Instructor & Info Badges -->
            <div class="flex items-center justify-between text-xs text-slate-200 font-medium pt-2 border-t border-slate-800">
              <div class="flex items-center gap-1.5 truncate">
                <img v-if="item.teacher_avatar" :src="item.teacher_avatar" class="w-4.5 h-4.5 rounded-full object-cover border border-slate-700 shrink-0" :alt="item.teacher" />
                <span class="truncate text-slate-100 font-medium">By {{ item.teacher }}</span>
              </div>

              <div class="flex items-center gap-1 font-mono text-[11px] shrink-0">
                <span v-if="item.duration" class="px-2 py-0.5 rounded-lg bg-sky-950/90 text-sky-200 border border-sky-800/80 flex items-center gap-1 shadow-xs">
                  <Clock class="w-3 h-3 text-sky-400" /> {{ item.duration }}
                </span>
                <span v-else-if="item.size_formatted" class="px-2 py-0.5 rounded-lg bg-slate-950 text-slate-200 border border-slate-800 flex items-center gap-1 shadow-xs">
                  <HardDrive class="w-3 h-3 text-slate-400" /> {{ item.size_formatted }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Actions (Spacious Touch & Click Target Sizes) -->
        <div class="pt-3 mt-3 border-t border-slate-800 flex items-center justify-between text-xs no-card-click h-8" @click.stop>
          <!-- Status Badge -->
          <div class="flex items-center h-8">
            <StatusBadge :status="item.status" />
          </div>

          <button 
            @click="$emit('openPreview', item)" 
            class="h-8 px-3.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-full font-bold text-xs shadow-md shadow-indigo-600/20 transition flex items-center justify-center gap-1.5 cursor-pointer active:scale-95 border border-indigo-400/30 shrink-0"
          >
            <Eye class="w-3.5 h-3.5 shrink-0" />
            <span>Preview</span>
          </button>
        </div>
      </div>
    </div>

    <!-- List View Table (Clear Icon Badges for Duration vs File Size + Bulk Select) -->
    <div v-else class="bg-slate-900/90 rounded-2xl border border-slate-800/90 shadow-xl overflow-hidden backdrop-blur-md">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-200 border-collapse">
          <thead class="bg-slate-950/95 text-slate-400 font-bold border-b border-slate-800 uppercase tracking-wider text-[10.5px]">
            <tr>
              <th class="px-2.5 py-3 w-10 text-center align-middle">
                <input 
                  type="checkbox" 
                  v-model="isAllSelected" 
                  class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-indigo-500 cursor-pointer shadow-xs"
                />
              </th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Thumbnail / Asset</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Title</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Course</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Teacher</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Size / Info</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Status</th>
              <th class="px-2.5 py-3 align-middle text-right whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/80">
            <tr 
              v-for="item in filteredItems" 
              :key="item.id" 
              :class="[
                'hover:bg-slate-800/40 transition-colors duration-150',
                selectedIds.includes(item.id) ? 'bg-indigo-950/25' : ''
              ]"
            >
              <!-- Checkbox Cell -->
              <td class="px-2.5 py-2.5 text-center align-middle">
                <input 
                  type="checkbox" 
                  :value="item.id" 
                  v-model="selectedIds" 
                  class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-indigo-500 cursor-pointer shadow-xs"
                />
              </td>

              <!-- Thumbnail & Asset Type Badge Cell -->
              <td class="px-2.5 py-2.5 align-middle whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <img v-if="item.thumbnail_url || (item.thumbnail && (item.thumbnail.startsWith('data:') || item.thumbnail.startsWith('http') || item.thumbnail.startsWith('/')))" :src="item.thumbnail_url || item.thumbnail" class="w-9 h-6.5 object-cover rounded-md border border-slate-700/80 shadow-xs shrink-0" :alt="item.title" />
                  <div v-else class="w-9 h-6.5 rounded-md bg-indigo-950/80 border border-indigo-800/80 flex items-center justify-center text-indigo-300 font-bold shrink-0 shadow-xs">
                    <Video v-if="item.type === 'video'" class="w-3.5 h-3.5 text-sky-400" />
                    <FileText v-else-if="item.type === 'pdf'" class="w-3.5 h-3.5 text-rose-400" />
                    <Presentation v-else-if="item.type === 'slide'" class="w-3.5 h-3.5 text-amber-400" />
                    <StickyNote v-else class="w-3.5 h-3.5 text-purple-400" />
                  </div>
                  <AssetTypeBadge :type="item.type" size="sm" />
                </div>
              </td>

              <!-- Title Cell -->
              <td class="px-2.5 py-2.5 align-middle font-bold text-white max-w-[140px] sm:max-w-[170px] lg:max-w-[200px] truncate text-xs">
                {{ item.title }}
              </td>

              <!-- Course Cell -->
              <td class="px-2.5 py-2.5 align-middle text-slate-300 max-w-[120px] sm:max-w-[150px] truncate whitespace-nowrap font-medium text-[11px]">
                {{ item.course }}
              </td>

              <!-- Teacher Cell -->
              <td class="px-2.5 py-2.5 align-middle whitespace-nowrap">
                <div class="flex items-center gap-1.5">
                  <img v-if="item.teacher_avatar" :src="item.teacher_avatar" class="w-5 h-5 rounded-full object-cover border border-slate-700/80 shrink-0 shadow-xs" :alt="item.teacher" />
                  <span class="text-slate-200 font-medium text-[11px] whitespace-nowrap max-w-[90px] truncate">{{ item.teacher }}</span>
                </div>
              </td>

              <!-- Size / Info Column (Stacked Badges for Ultra-Compact Width) -->
              <td class="px-2.5 py-2.5 align-middle whitespace-nowrap font-mono">
                <div class="flex flex-col gap-1 items-start justify-center">
                  <!-- Duration Badge (Clock Icon) -->
                  <span v-if="item.duration" class="inline-flex items-center gap-1 text-sky-300 bg-sky-950/80 px-2 py-0.5 rounded border border-sky-800/60 text-[10px] font-bold shadow-2xs">
                    <Clock class="w-3 h-3 text-sky-400" />
                    <span>{{ item.duration }}</span>
                  </span>

                  <!-- File Size Badge (HardDrive Icon) -->
                  <span v-if="item.size_formatted" class="inline-flex items-center gap-1 text-slate-300 bg-slate-950/80 px-2 py-0.5 rounded border border-slate-800 text-[10px] font-medium shadow-2xs">
                    <HardDrive class="w-3 h-3 text-slate-400" />
                    <span>{{ item.size_formatted }}</span>
                  </span>

                  <!-- Pages Badge (FileText Icon) -->
                  <span v-if="item.pages" class="inline-flex items-center gap-1 text-purple-300 bg-purple-950/80 px-2 py-0.5 rounded border border-purple-800/60 text-[10px] font-medium shadow-2xs">
                    <FileText class="w-3 h-3 text-purple-400" />
                    <span>{{ item.pages }} p.</span>
                  </span>
                </div>
              </td>

              <!-- Status Badge Column -->
              <td class="px-2.5 py-2.5 align-middle whitespace-nowrap">
                <StatusBadge :status="item.status" size="sm" />
              </td>

              <!-- Row Action Buttons -->
              <td class="px-2.5 py-2.5 align-middle text-right whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5 whitespace-nowrap">
                  <button 
                    @click="$emit('openPreview', item)" 
                    class="w-16 h-7 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white rounded-lg font-bold border border-indigo-500/30 transition-all duration-150 cursor-pointer active:scale-95 flex items-center justify-center gap-1 text-[11px] shadow-2xs shrink-0"
                  >
                    <Eye class="w-3 h-3 shrink-0" />
                    <span>View</span>
                  </button>
                  <button 
                    @click="toggleStatus(item)" 
                    class="w-[74px] h-7 bg-slate-950/80 hover:bg-slate-800 text-slate-300 hover:text-white rounded-lg border border-slate-700/80 transition-all duration-150 cursor-pointer active:scale-95 flex items-center justify-center gap-1 text-[11px] shadow-2xs shrink-0"
                    :title="'Toggle Status'"
                  >
                    <CheckCircle2 v-if="item.status !== 'Published'" class="w-3 h-3 text-emerald-400 shrink-0" />
                    <Clock v-else class="w-3 h-3 text-amber-400 shrink-0" />
                    <span>{{ item.status === 'Published' ? 'Draft' : 'Publish' }}</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Floating Sticky Bulk Action Bar (Appears when items are selected) -->
    <div 
      v-if="selectedIds.length > 0"
      class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-slate-900/95 backdrop-blur-xl border border-indigo-500/40 p-3 sm:px-4 sm:py-3 rounded-2xl shadow-2xl shadow-slate-950/90 flex items-center justify-between gap-3 text-xs animate-in fade-in slide-in-from-bottom-4 duration-300 max-w-4xl w-full"
    >
      <!-- Selected Counter Badge -->
      <div class="flex items-center gap-2 font-mono text-indigo-300 font-bold border-r border-slate-800/90 pr-3 sm:pr-4 shrink-0 whitespace-nowrap">
        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs font-black shadow-md shadow-indigo-600/30">
          {{ selectedIds.length }}
        </span>
        <span class="text-xs">Selected</span>
      </div>

      <!-- Action Buttons Row (Single Horizontal Line, No Wrap) -->
      <div class="flex items-center gap-2 flex-nowrap overflow-x-auto scrollbar-none flex-1 justify-end">
        <button 
          @click="toggleSelectAll" 
          class="px-2.5 py-1.5 bg-slate-800/80 hover:bg-slate-700 text-slate-200 font-bold rounded-xl border border-slate-700/80 transition-all duration-150 flex items-center gap-1.5 cursor-pointer active:scale-95 whitespace-nowrap text-xs shadow-2xs shrink-0"
        >
          <CheckSquare class="w-3.5 h-3.5 text-indigo-400 shrink-0" />
          <span>{{ isAllSelected ? 'Deselect All' : 'Select All' }}</span>
        </button>

        <button 
          @click="bulkPublish" 
          class="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white font-bold rounded-xl border border-emerald-500/30 transition-all duration-150 flex items-center gap-1.5 cursor-pointer active:scale-95 whitespace-nowrap text-xs shadow-2xs shrink-0"
        >
          <CheckCircle2 class="w-3.5 h-3.5 shrink-0" />
          <span>Publish</span>
        </button>

        <button 
          @click="bulkDraft" 
          class="px-3 py-1.5 bg-amber-600/20 hover:bg-amber-600 text-amber-300 hover:text-white font-bold rounded-xl border border-amber-500/30 transition-all duration-150 flex items-center gap-1.5 cursor-pointer active:scale-95 whitespace-nowrap text-xs shadow-2xs shrink-0"
        >
          <Clock class="w-3.5 h-3.5 shrink-0" />
          <span>Draft</span>
        </button>

        <button 
          @click="bulkExport" 
          class="px-3 py-1.5 bg-sky-600/20 hover:bg-sky-600 text-sky-300 hover:text-white font-bold rounded-xl border border-sky-500/30 transition-all duration-150 flex items-center gap-1.5 cursor-pointer active:scale-95 whitespace-nowrap text-xs shadow-2xs shrink-0"
        >
          <DownloadCloud class="w-3.5 h-3.5 text-sky-300 shrink-0" />
          <span>Export</span>
        </button>

        <button 
          @click="bulkDelete" 
          class="px-3 py-1.5 bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white font-bold rounded-xl border border-rose-500/30 transition-all duration-150 flex items-center gap-1.5 cursor-pointer active:scale-95 whitespace-nowrap text-xs shadow-2xs shrink-0"
        >
          <Trash2 class="w-3.5 h-3.5 shrink-0" />
          <span>Delete</span>
        </button>

        <button 
          @click="selectedIds = []" 
          class="px-2.5 py-1.5 bg-slate-950/80 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl border border-slate-800 transition-all duration-150 cursor-pointer active:scale-95 whitespace-nowrap text-xs shrink-0"
        >
          ✕ Cancel
        </button>
      </div>
    </div>

  </div>
</template>
