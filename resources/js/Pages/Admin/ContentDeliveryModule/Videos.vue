<script setup lang="ts">
import { ref, computed } from 'vue'
import { Search, Video, Play, Plus } from 'lucide-vue-next'
import StatusBadge from './StatusBadge.vue'

const props = withDefaults(defineProps<{
  stats?: any
  videos?: Array<any>
}>(), {
  stats: () => ({}),
  videos: () => []
})

const emit = defineEmits(['openUpload', 'openPreview'])

const search = ref('')

const filteredVideos = computed(() => {
  return (props.videos || []).filter(v => v.type === 'video' && (!search.value || v.title.toLowerCase().includes(search.value.toLowerCase())))
})
</script>

<template>
  <div class="space-y-6 text-slate-100">
    
    <!-- Top Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">Total Videos</p>
        <p class="text-2xl font-bold text-white font-mono mt-0.5">{{ stats.videos_count }} Videos</p>
      </div>
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-emerald-400 font-semibold uppercase tracking-wide">Published</p>
        <p class="text-2xl font-bold text-emerald-400 font-mono mt-0.5">780 Live</p>
      </div>
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-amber-400 font-semibold uppercase tracking-wide">Transcoding / Processing</p>
        <p class="text-2xl font-bold text-amber-400 font-mono mt-0.5">12 Queue</p>
      </div>
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-indigo-400 font-semibold uppercase tracking-wide">Total Streaming Views</p>
        <p class="text-2xl font-bold text-indigo-400 font-mono mt-0.5">45,820</p>
      </div>
    </div>

    <!-- Video Table Container -->
    <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 shadow-xl space-y-4">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 class="text-lg font-bold text-white flex items-center gap-2.5">
            <!-- Flaticon Style Video Player Vector Icon -->
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-500/20 via-indigo-500/10 to-sky-950 border border-sky-500/30 flex items-center justify-center shrink-0 shadow-xs">
              <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="2" y="4" width="20" height="16" rx="3" fill="#38BDF8" fill-opacity="0.25" stroke="#38BDF8" stroke-width="1.8"/>
                <path d="M10 8.5L16 12L10 15.5V8.5Z" fill="#38BDF8" stroke="#38BDF8" stroke-width="1.5" stroke-linejoin="round"/>
              </svg>
            </div>
            <span>VIDEOS MANAGEMENT</span>
            <span class="text-xs bg-indigo-950 text-indigo-300 border border-indigo-800 px-2.5 py-0.5 rounded-full font-mono">Cloudinary CDN ●</span>
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
          <span>Upload Video</span>
        </button>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs pt-2 border-t border-slate-700/60">
        <div class="relative">
          <input 
            v-model="search"
            type="text" 
            placeholder="Search video title..."
            class="w-full p-2.5 pl-9 pr-8 bg-slate-900/90 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-white placeholder:text-slate-500 focus:outline-none" 
          />
          <Search class="w-4 h-4 text-slate-500 absolute left-3 top-3 pointer-events-none" />
        </div>
      </div>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80">
        <table class="w-full text-left text-xs text-slate-200 border-collapse">
          <thead class="bg-slate-900/90 text-slate-400 font-bold border-b border-slate-700 uppercase tracking-wider text-[10.5px]">
            <tr>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Video Title</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Course</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Duration</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Size</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Views</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">CDN & Protection</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Status</th>
              <th class="px-2.5 py-3 align-middle text-right whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60">
            <tr v-for="v in filteredVideos" :key="v.id" class="hover:bg-slate-700/30 transition-colors duration-150">
              <td class="px-2.5 py-2.5 align-middle font-bold text-white whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <img v-if="v.thumbnail_url || (v.thumbnail && (v.thumbnail.startsWith('data:') || v.thumbnail.startsWith('http') || v.thumbnail.startsWith('/')))" :src="v.thumbnail_url || v.thumbnail" class="w-9 h-6.5 object-cover rounded-md border border-slate-700 shrink-0 shadow-xs" :alt="v.title" />
                  <div v-else class="w-9 h-6.5 rounded-md bg-sky-950/70 border border-sky-800/60 flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                      <rect x="2" y="4" width="20" height="16" rx="3" fill="#38BDF8" fill-opacity="0.25" stroke="#38BDF8" stroke-width="1.8"/>
                      <path d="M10 8.5L16 12L10 15.5V8.5Z" fill="#38BDF8" stroke="#38BDF8" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                  </div>
                  <span class="max-w-[150px] sm:max-w-[200px] truncate text-xs">{{ v.title }}</span>
                </div>
              </td>
              <td class="px-2.5 py-2.5 align-middle text-slate-300 whitespace-nowrap max-w-[130px] truncate text-[11px]">{{ v.course }}</td>
              <td class="px-2.5 py-2.5 align-middle font-mono text-indigo-300 font-bold whitespace-nowrap text-[11px]">{{ v.duration || '14:20 min' }}</td>
              <td class="px-2.5 py-2.5 align-middle font-mono text-slate-400 whitespace-nowrap text-[11px]">{{ v.size_formatted }}</td>
              <td class="px-2.5 py-2.5 align-middle font-mono text-emerald-400 font-bold whitespace-nowrap text-[11px]">{{ (v.views || 1240).toLocaleString() }}</td>
              <td class="px-2.5 py-2.5 align-middle whitespace-nowrap">
                <div class="flex items-center gap-1 text-[10px] font-mono whitespace-nowrap">
                  <span class="bg-slate-950 text-indigo-300 border border-indigo-800/80 px-1.5 py-0.5 rounded">Watermark ✓</span>
                  <span class="bg-slate-950 text-emerald-400 border border-emerald-800/80 px-1.5 py-0.5 rounded">Signed HLS</span>
                </div>
              </td>
              <td class="px-2.5 py-2.5 align-middle whitespace-nowrap">
                <StatusBadge :status="v.status" size="sm" />
              </td>
              <td class="px-2.5 py-2.5 align-middle text-right whitespace-nowrap">
                <button @click="$emit('openPreview', v)" class="h-7 px-3 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white rounded-lg font-bold border border-indigo-500/30 transition cursor-pointer active:scale-95 inline-flex items-center justify-center gap-1 text-[11px] shadow-xs shrink-0">
                  <Play class="w-3 h-3 shrink-0" />
                  <span>Player & Analytics</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>
