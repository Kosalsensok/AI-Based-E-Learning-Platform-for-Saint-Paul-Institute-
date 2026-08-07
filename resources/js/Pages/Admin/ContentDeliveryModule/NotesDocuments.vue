<script setup lang="ts">
import { ref, computed } from 'vue'
import { Search, StickyNote, Edit3, Plus, FileText } from 'lucide-vue-next'
import StatusBadge from './StatusBadge.vue'

const props = withDefaults(defineProps<{
  stats?: any
  notes?: Array<any>
}>(), {
  stats: () => ({}),
  notes: () => []
})

const emit = defineEmits(['openUpload', 'openEditor'])

const search = ref('')

const filteredNotes = computed(() => {
  return (props.notes || []).filter(n => n.type === 'note' && (!search.value || n.title.toLowerCase().includes(search.value.toLowerCase())))
})
</script>

<template>
  <div class="space-y-6 text-slate-100">
    
    <!-- Top Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">Total Notes</p>
        <p class="text-2xl font-bold text-white font-mono mt-0.5">{{ stats.notes_count }} Notes</p>
      </div>
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-blue-400 font-semibold uppercase tracking-wide">Downloads & Views</p>
        <p class="text-2xl font-bold text-blue-400 font-mono mt-0.5">4,820</p>
      </div>
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-emerald-400 font-semibold uppercase tracking-wide">Rich Web Markdown</p>
        <p class="text-2xl font-bold text-emerald-400 font-mono mt-0.5">45 Web Notes</p>
      </div>
      <div class="bg-slate-900/80 backdrop-blur-md p-4 rounded-xl border border-slate-800/80 shadow-sm">
        <p class="text-xs text-amber-400 font-semibold uppercase tracking-wide">Published</p>
        <p class="text-2xl font-bold text-amber-400 font-mono mt-0.5">120 Live</p>
      </div>
    </div>

    <!-- Notes Container -->
    <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 shadow-xl space-y-4">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 class="text-lg font-bold text-white flex items-center gap-2">
            <StickyNote class="w-5 h-5 text-purple-400" />
            <span>NOTES & DOCUMENTS MANAGEMENT</span>
          </h2>
        </div>
        <div class="flex items-center gap-2">
          <button 
            @click="$emit('openEditor')"
            class="px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow-md shadow-amber-950/40 hover:shadow-amber-600/20 transition-all duration-200 flex items-center gap-2.5 text-xs cursor-pointer active:scale-[0.98] border border-amber-400/30"
          >
            <!-- Flaticon Style Edit Note Vector Icon -->
            <svg class="w-4.5 h-4.5 shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="9.5" fill="rgba(255, 255, 255, 0.2)" stroke="#FFFFFF" stroke-width="1.5"/>
              <path d="M14.5 9.5L14.5 9.5M15.2 8.8L12.4 6L6 12.4V15.2H8.8L15.2 8.8Z" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Write Rich Note</span>
          </button>
          <button 
            @click="$emit('openUpload')"
            class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-xl border border-slate-700 transition-all duration-200 flex items-center gap-2 text-xs cursor-pointer active:scale-[0.98]"
          >
            <svg class="w-4.5 h-4.5 shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="9.5" fill="rgba(255, 255, 255, 0.1)" stroke="#CBD5E1" stroke-width="1.5"/>
              <path d="M12 7.5V16.5M7.5 12H16.5" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Upload File</span>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs pt-2 border-t border-slate-700/60">
        <div class="relative">
          <input 
            v-model="search"
            type="text" 
            placeholder="Search notes title..."
            class="w-full p-2.5 pl-9 pr-8 bg-slate-900/90 border border-slate-700 rounded-xl focus:ring-2 focus:ring-amber-500 text-white placeholder:text-slate-500 focus:outline-none" 
          />
          <Search class="w-4 h-4 text-slate-500 absolute left-3 top-3 pointer-events-none" />
        </div>
      </div>

      <div class="overflow-x-auto rounded-xl border border-slate-700/80">
        <table class="w-full text-left text-xs text-slate-200 border-collapse">
          <thead class="bg-slate-900/90 text-slate-400 font-bold border-b border-slate-700 uppercase tracking-wider text-[10.5px]">
            <tr>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Note Title</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Course</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Format</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Views</th>
              <th class="px-2.5 py-3 align-middle whitespace-nowrap">Status</th>
              <th class="px-2.5 py-3 align-middle text-right whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60">
            <tr v-for="n in filteredNotes" :key="n.id" class="hover:bg-slate-700/30 transition-colors duration-150">
              <td class="px-2.5 py-2.5 align-middle font-bold text-white whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <img v-if="n.thumbnail_url || (n.thumbnail && (n.thumbnail.startsWith('data:') || n.thumbnail.startsWith('http') || n.thumbnail.startsWith('/')))" :src="n.thumbnail_url || n.thumbnail" class="w-9 h-6.5 object-cover rounded-md border border-slate-700 shrink-0 shadow-xs" :alt="n.title" />
                  <div v-else class="w-9 h-6.5 rounded-md bg-purple-950 border border-purple-800 flex items-center justify-center text-purple-300 shrink-0 shadow-xs">
                    <StickyNote class="w-3.5 h-3.5" />
                  </div>
                  <span class="max-w-[160px] sm:max-w-[200px] truncate text-xs">{{ n.title }}</span>
                </div>
              </td>
              <td class="px-2.5 py-2.5 align-middle text-slate-300 whitespace-nowrap max-w-[130px] truncate text-[11px]">{{ n.course }}</td>
              <td class="px-2.5 py-2.5 align-middle font-mono text-amber-300 font-bold whitespace-nowrap text-[11px]">{{ n.type_label || 'Web Markdown' }}</td>
              <td class="px-2.5 py-2.5 align-middle font-mono text-blue-400 font-bold whitespace-nowrap text-[11px]">{{ n.views?.toLocaleString() }}</td>
              <td class="px-2.5 py-2.5 align-middle whitespace-nowrap">
                <StatusBadge :status="n.status" size="sm" />
              </td>
              <td class="px-2.5 py-2.5 align-middle text-right whitespace-nowrap">
                <button @click="$emit('openEditor', n)" class="h-7 px-3 bg-amber-600/20 hover:bg-amber-600 text-amber-300 hover:text-white rounded-lg font-bold border border-amber-500/30 transition inline-flex items-center justify-center gap-1 cursor-pointer active:scale-95 text-[11px] shadow-xs shrink-0">
                  <Edit3 class="w-3 h-3 shrink-0" />
                  <span>Edit Note</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>
