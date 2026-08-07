<script setup lang="ts">
import { ref, computed } from 'vue'
import { Search, FileText, Eye, Plus } from 'lucide-vue-next'
import StatusBadge from './StatusBadge.vue'

const props = withDefaults(defineProps<{
  stats?: any
  pdfs?: Array<any>
}>(), {
  stats: () => ({}),
  pdfs: () => []
})

const emit = defineEmits(['openUpload', 'openPreview'])

const search = ref('')

const filteredPdfs = computed(() => {
  return (props.pdfs || []).filter(p => p.type === 'pdf' && (!search.value || p.title.toLowerCase().includes(search.value.toLowerCase())))
})
</script>

<template>
  <div class="space-y-6 text-slate-100">
    
    <!-- Top Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">Total PDFs</p>
        <p class="text-2xl font-bold text-white font-mono mt-0.5">{{ stats.pdfs_count }} Files</p>
      </div>
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-blue-400 font-semibold uppercase tracking-wide">Total Student Downloads</p>
        <p class="text-2xl font-bold text-blue-400 font-mono mt-0.5">12,450</p>
      </div>
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-purple-400 font-semibold uppercase tracking-wide">Storage Usage</p>
        <p class="text-2xl font-bold text-purple-400 font-mono mt-0.5">8.6 GB</p>
      </div>
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-emerald-400 font-semibold uppercase tracking-wide">Watermark Active</p>
        <p class="text-2xl font-bold text-emerald-400 font-mono mt-0.5">100% Protected</p>
      </div>
    </div>

    <!-- PDF Table Container -->
    <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 shadow-xl space-y-4">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 class="text-lg font-bold text-white flex items-center gap-2.5">
            <!-- Flaticon Style PDF Document Vector Icon -->
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500/20 via-teal-500/10 to-emerald-950 border border-emerald-500/30 flex items-center justify-center shrink-0 shadow-xs">
              <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 3H14L19 8V19C19 20.1 18.1 21 17 21H6C4.9 21 4 20.1 4 19V5C4 3.9 4.9 3 6 3Z" fill="#10B981" fill-opacity="0.25" stroke="#34D399" stroke-width="1.8"/>
                <path d="M14 3V8H19" stroke="#34D399" stroke-width="1.8" stroke-linejoin="round"/>
              </svg>
            </div>
            <span>PDFs MANAGEMENT</span>
          </h2>
        </div>
        <button 
          @click="$emit('openUpload')"
          class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-md shadow-emerald-950/40 hover:shadow-emerald-600/20 transition-all duration-200 flex items-center gap-2.5 text-xs cursor-pointer active:scale-[0.98] border border-emerald-400/30"
        >
          <!-- Flaticon Style Vector Plus Badge Icon -->
          <svg class="w-4.5 h-4.5 shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="9.5" fill="rgba(255, 255, 255, 0.2)" stroke="#FFFFFF" stroke-width="1.5"/>
            <path d="M12 7.5V16.5M7.5 12H16.5" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span>Upload PDF</span>
        </button>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs pt-2 border-t border-slate-700/60">
        <div class="relative">
          <input 
            v-model="search"
            type="text" 
            placeholder="Search PDF title..."
            class="w-full p-2.5 pl-9 pr-8 bg-slate-900/90 border border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500 text-white placeholder:text-slate-500 focus:outline-none" 
          />
          <Search class="w-4 h-4 text-slate-500 absolute left-3 top-3 pointer-events-none" />
        </div>
      </div>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80">
        <table class="w-full text-left text-xs text-slate-200 border-collapse">
          <thead class="bg-slate-900/90 text-slate-400 font-bold border-b border-slate-700 uppercase tracking-wider text-[10.5px]">
            <tr>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">PDF Title</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Course</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Pages</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Size</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Downloads</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Status</th>
              <th class="px-2.5 py-3 align-middle text-right whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60">
            <tr v-for="p in filteredPdfs" :key="p.id" class="hover:bg-slate-700/30 transition-colors duration-150">
              <td class="px-2.5 py-2.5 align-middle font-bold text-white whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <img v-if="p.thumbnail_url || (p.thumbnail && (p.thumbnail.startsWith('data:') || p.thumbnail.startsWith('http') || p.thumbnail.startsWith('/')))" :src="p.thumbnail_url || p.thumbnail" class="w-9 h-6.5 object-cover rounded-md border border-slate-700 shrink-0 shadow-xs" :alt="p.title" />
                  <div v-else class="w-9 h-6.5 rounded-md bg-emerald-950 border border-emerald-800 flex items-center justify-center text-emerald-400 shrink-0 shadow-xs">
                    <FileText class="w-3.5 h-3.5" />
                  </div>
                  <span class="max-w-[160px] sm:max-w-[200px] truncate text-xs">{{ p.title }}</span>
                </div>
              </td>
              <td class="px-2.5 py-2.5 align-middle text-slate-300 whitespace-nowrap max-w-[130px] truncate text-[11px]">{{ p.course }}</td>
              <td class="px-2.5 py-2.5 align-middle font-mono text-emerald-400 font-bold whitespace-nowrap text-[11px]">{{ p.pages || 45 }} Pages</td>
              <td class="px-2.5 py-2.5 align-middle font-mono text-slate-400 whitespace-nowrap text-[11px]">{{ p.size_formatted }}</td>
              <td class="px-2.5 py-2.5 align-middle font-mono text-blue-400 font-bold whitespace-nowrap text-[11px]">{{ p.downloads?.toLocaleString() }}</td>
              <td class="px-2.5 py-2.5 align-middle whitespace-nowrap">
                <StatusBadge :status="p.status" size="sm" />
              </td>
              <td class="px-2.5 py-2.5 align-middle text-right whitespace-nowrap">
                <button @click="$emit('openPreview', p)" class="h-7 px-3 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white rounded-lg font-bold border border-emerald-500/30 transition inline-flex items-center justify-center gap-1 cursor-pointer active:scale-95 text-[11px] shadow-xs shrink-0">
                  <Eye class="w-3 h-3 shrink-0" />
                  <span>Preview PDF</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>
