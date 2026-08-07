<script setup lang="ts">
import { ref, computed } from 'vue'
import { Search, Presentation, Eye, Plus } from 'lucide-vue-next'
import StatusBadge from './StatusBadge.vue'

const props = withDefaults(defineProps<{
  stats?: any
  slides?: Array<any>
}>(), {
  stats: () => ({}),
  slides: () => []
})

const emit = defineEmits(['openUpload', 'openPreview'])

const search = ref('')

const filteredSlides = computed(() => {
  return (props.slides || []).filter(s => s.type === 'slide' && (!search.value || s.title.toLowerCase().includes(search.value.toLowerCase())))
})
</script>

<template>
  <div class="space-y-6 text-slate-100">
    
    <!-- Top Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">Total Slides</p>
        <p class="text-2xl font-bold text-white font-mono mt-0.5">{{ stats.slides_count }} Decks</p>
      </div>
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-blue-400 font-semibold uppercase tracking-wide">Slide Views</p>
        <p class="text-2xl font-bold text-blue-400 font-mono mt-0.5">18,240</p>
      </div>
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-emerald-400 font-semibold uppercase tracking-wide">Web Viewer Ready</p>
        <p class="text-2xl font-bold text-emerald-400 font-mono mt-0.5">260 Decks</p>
      </div>
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-amber-400 font-semibold uppercase tracking-wide">Storage Used</p>
        <p class="text-2xl font-bold text-amber-400 font-mono mt-0.5">3.2 GB</p>
      </div>
    </div>

    <!-- Slides Table Container -->
    <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 shadow-xl space-y-4">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 class="text-lg font-bold text-white flex items-center gap-2.5">
            <!-- Flaticon Style Presentation Screen Vector Icon -->
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500/20 via-pink-500/10 to-purple-950 border border-purple-500/30 flex items-center justify-center shrink-0 shadow-xs">
              <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="4" width="18" height="12" rx="2" fill="#A855F7" fill-opacity="0.3" stroke="#C084FC" stroke-width="1.8"/>
                <path d="M8 20L12 16L16 20" stroke="#C084FC" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <span>SLIDES MANAGEMENT</span>
          </h2>
        </div>
        <button 
          @click="$emit('openUpload')"
          class="px-4 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl shadow-md shadow-purple-950/40 hover:shadow-purple-600/20 transition-all duration-200 flex items-center gap-2.5 text-xs cursor-pointer active:scale-[0.98] border border-purple-400/30"
        >
          <!-- Flaticon Style Vector Plus Badge Icon -->
          <svg class="w-4.5 h-4.5 shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="9.5" fill="rgba(255, 255, 255, 0.2)" stroke="#FFFFFF" stroke-width="1.5"/>
            <path d="M12 7.5V16.5M7.5 12H16.5" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span>Upload Slide</span>
        </button>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs pt-2 border-t border-slate-700/60">
        <div class="relative">
          <input 
            v-model="search"
            type="text" 
            placeholder="Search slide title..."
            class="w-full p-2.5 pl-9 pr-8 bg-slate-900/90 border border-slate-700 rounded-xl focus:ring-2 focus:ring-purple-500 text-white placeholder:text-slate-500 focus:outline-none" 
          />
          <Search class="w-4 h-4 text-slate-500 absolute left-3 top-3 pointer-events-none" />
        </div>
      </div>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80">
        <table class="w-full text-left text-xs text-slate-200 border-collapse">
          <thead class="bg-slate-900/90 text-slate-400 font-bold border-b border-slate-700 uppercase tracking-wider text-[10.5px]">
            <tr>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Slide Title</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Course</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Pages</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Views</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Status</th>
              <th class="px-2.5 py-3 align-middle text-right whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60">
            <tr v-for="s in filteredSlides" :key="s.id" class="hover:bg-slate-700/30 transition-colors duration-150">
              <td class="px-2.5 py-2.5 align-middle font-bold text-white whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <img v-if="s.thumbnail_url || (s.thumbnail && (s.thumbnail.startsWith('data:') || s.thumbnail.startsWith('http') || s.thumbnail.startsWith('/')))" :src="s.thumbnail_url || s.thumbnail" class="w-9 h-6.5 object-cover rounded-md border border-slate-700 shrink-0 shadow-xs" :alt="s.title" />
                  <div v-else class="w-9 h-6.5 rounded-md bg-purple-950/70 border border-purple-800/60 flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                      <rect x="3" y="4" width="18" height="12" rx="2" fill="#A855F7" fill-opacity="0.3" stroke="#C084FC" stroke-width="1.8"/>
                      <path d="M8 20L12 16L16 20" stroke="#C084FC" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </div>
                  <span class="max-w-[160px] sm:max-w-[200px] truncate text-xs">{{ s.title }}</span>
                </div>
              </td>
              <td class="px-2.5 py-2.5 align-middle text-slate-300 whitespace-nowrap max-w-[130px] truncate text-[11px]">{{ s.course }}</td>
              <td class="px-2.5 py-2.5 align-middle font-mono text-purple-300 font-bold whitespace-nowrap text-[11px]">{{ s.pages }} Pages</td>
              <td class="px-2.5 py-2.5 align-middle font-mono text-blue-400 font-bold whitespace-nowrap text-[11px]">{{ s.views?.toLocaleString() }}</td>
              <td class="px-2.5 py-2.5 align-middle whitespace-nowrap">
                <StatusBadge :status="s.status" size="sm" />
              </td>
              <td class="px-2.5 py-2.5 align-middle text-right whitespace-nowrap">
                <button @click="$emit('openPreview', s)" class="h-7 px-3 bg-purple-600/20 hover:bg-purple-600 text-purple-300 hover:text-white rounded-lg font-bold border border-purple-500/30 transition inline-flex items-center justify-center gap-1 cursor-pointer active:scale-95 text-[11px] shadow-xs shrink-0">
                  <Eye class="w-3 h-3 shrink-0" />
                  <span>Launch Web Viewer</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>
