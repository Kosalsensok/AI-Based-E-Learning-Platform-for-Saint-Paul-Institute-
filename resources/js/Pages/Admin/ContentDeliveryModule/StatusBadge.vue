<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    default: 'Published'
  },
  size: {
    type: String,
    default: 'md' // 'sm', 'md', 'lg'
  }
})

const normalizedStatus = computed(() => {
  const s = (props.status || '').toLowerCase()
  if (s.includes('pub') || s.includes('live')) return 'Published'
  if (s.includes('draft') || s.includes('review')) return 'Draft'
  return 'Archived'
})
</script>

<template>
  <!-- Published Badge (Single Flaticon Style Verified Vector Icon) -->
  <span 
    v-if="normalizedStatus === 'Published'" 
    :class="[
      'inline-flex items-center justify-center rounded-full font-bold border backdrop-blur-md shadow-sm transition-all duration-200 hover:scale-[1.02] cursor-default select-none whitespace-nowrap shrink-0',
      size === 'sm' ? 'h-6 px-2.5 text-[10.5px] gap-1.5' : size === 'lg' ? 'h-9 px-4 text-xs gap-2 tracking-wide' : 'h-8 px-3 text-[11px] gap-1.5',
      'bg-emerald-950/80 text-emerald-300 border-emerald-500/40 hover:border-emerald-400'
    ]"
  >
    <!-- Flaticon Style Verified Check Circle Vector Icon -->
    <svg class="w-3.5 h-3.5 shrink-0 shadow-xs" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="12" cy="12" r="10" fill="url(#flaticon-pub-grad)" stroke="#34D399" stroke-width="1.2"/>
      <path d="M7.5 12.5L10.5 15.5L16.5 9.5" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
      <defs>
        <linearGradient id="flaticon-pub-grad" x1="2" y1="2" x2="22" y2="22" gradientUnits="userSpaceOnUse">
          <stop stop-color="#059669"/>
          <stop offset="1" stop-color="#10B981"/>
        </linearGradient>
      </defs>
    </svg>
    <span class="tracking-tight text-emerald-200">Published</span>
  </span>

  <!-- Draft Badge (Single Flaticon Style Draft Clock Vector Icon) -->
  <span 
    v-else-if="normalizedStatus === 'Draft'" 
    :class="[
      'inline-flex items-center justify-center rounded-full font-bold border backdrop-blur-md shadow-sm transition-all duration-200 hover:scale-[1.02] cursor-default select-none whitespace-nowrap shrink-0',
      size === 'sm' ? 'h-6 px-2.5 text-[10.5px] gap-1.5' : size === 'lg' ? 'h-9 px-4 text-xs gap-2 tracking-wide' : 'h-8 px-3 text-[11px] gap-1.5',
      'bg-amber-950/80 text-amber-300 border-amber-500/40 hover:border-amber-400'
    ]"
  >
    <!-- Flaticon Style Draft Edit Clock Vector Icon -->
    <svg class="w-3.5 h-3.5 shrink-0 shadow-xs" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="12" cy="12" r="10" fill="url(#flaticon-draft-grad)" stroke="#FBBF24" stroke-width="1.2"/>
      <path d="M12 7V12L15.5 14" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <defs>
        <linearGradient id="flaticon-draft-grad" x1="2" y1="2" x2="22" y2="22" gradientUnits="userSpaceOnUse">
          <stop stop-color="#D97706"/>
          <stop offset="1" stop-color="#F59E0B"/>
        </linearGradient>
      </defs>
    </svg>
    <span class="tracking-tight text-amber-200">Draft</span>
  </span>

  <!-- Archived Badge (Single Flaticon Style Vault Box Vector Icon) -->
  <span 
    v-else 
    :class="[
      'inline-flex items-center justify-center rounded-full font-bold border backdrop-blur-md shadow-sm transition-all duration-200 hover:scale-[1.02] cursor-default select-none whitespace-nowrap shrink-0',
      size === 'sm' ? 'h-6 px-2.5 text-[10.5px] gap-1.5' : size === 'lg' ? 'h-9 px-4 text-xs gap-2 tracking-wide' : 'h-8 px-3 text-[11px] gap-1.5',
      'bg-slate-900/90 text-slate-300 border-slate-700/80 hover:border-slate-600'
    ]"
  >
    <!-- Flaticon Style Archive Box Vector Icon -->
    <svg class="w-3.5 h-3.5 shrink-0 shadow-xs" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="12" cy="12" r="10" fill="url(#flaticon-arch-grad)" stroke="#94A3B8" stroke-width="1.2"/>
      <rect x="8" y="9" width="8" height="7" rx="1.5" stroke="#FFFFFF" stroke-width="1.5"/>
      <path d="M10 9V7A2 2 0 0 1 14 7V9" stroke="#FFFFFF" stroke-width="1.5"/>
      <defs>
        <linearGradient id="flaticon-arch-grad" x1="2" y1="2" x2="22" y2="22" gradientUnits="userSpaceOnUse">
          <stop stop-color="#475569"/>
          <stop offset="1" stop-color="#64748B"/>
        </linearGradient>
      </defs>
    </svg>
    <span class="tracking-tight text-slate-300">Archived</span>
  </span>
</template>
