<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    required: true
  },
  size: {
    type: String,
    default: 'md' // 'sm', 'md'
  }
})

const normalizedType = computed(() => (props.type || '').toLowerCase())
</script>

<template>
  <!-- Video Type -->
  <span 
    v-if="normalizedType === 'video'" 
    :class="[
      'rounded-xl font-mono font-extrabold uppercase tracking-wider shadow-md inline-flex items-center gap-1.5 border backdrop-blur-md transition-all duration-200 hover:scale-105 select-none shrink-0',
      size === 'sm' ? 'h-6 px-2 text-[10px]' : 'h-7 px-2.5 text-[10px]',
      'bg-gradient-to-r from-sky-950/90 via-sky-900/90 to-slate-950 text-sky-200 border-sky-400/50 shadow-sky-950/60 ring-1 ring-sky-500/20'
    ]"
  >
    <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
      <rect x="2" y="5" width="13" height="14" rx="3" fill="#0284C7" fill-opacity="0.4" stroke="#38BDF8" stroke-width="1.5"/>
      <path d="M15 9L21 6.5V17.5L15 15V9Z" fill="#38BDF8" stroke="#38BDF8" stroke-width="1.2" stroke-linejoin="round"/>
      <polygon points="7.5,9.5 11.5,12 7.5,14.5" fill="#FFFFFF"/>
    </svg>
    <span>video</span>
  </span>

  <!-- PDF Type -->
  <span 
    v-else-if="normalizedType === 'pdf'" 
    :class="[
      'rounded-xl font-mono font-extrabold uppercase tracking-wider shadow-md inline-flex items-center gap-1.5 border backdrop-blur-md transition-all duration-200 hover:scale-105 select-none shrink-0',
      size === 'sm' ? 'h-6 px-2 text-[10px]' : 'h-7 px-2.5 text-[10px]',
      'bg-gradient-to-r from-rose-950/90 via-rose-900/90 to-slate-950 text-rose-200 border-rose-400/50 shadow-rose-950/60 ring-1 ring-rose-500/20'
    ]"
  >
    <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
      <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" fill="#E11D48" fill-opacity="0.35" stroke="#F43F5E" stroke-width="1.5"/>
      <path d="M14 2V8H20" stroke="#F43F5E" stroke-width="1.5"/>
      <path d="M8 13H16M8 17H13" stroke="#FDA4AF" stroke-width="1.5" stroke-linecap="round"/>
    </svg>
    <span>pdf</span>
  </span>

  <!-- Slide Type -->
  <span 
    v-else-if="normalizedType === 'slide'" 
    :class="[
      'rounded-xl font-mono font-extrabold uppercase tracking-wider shadow-md inline-flex items-center gap-1.5 border backdrop-blur-md transition-all duration-200 hover:scale-105 select-none shrink-0',
      size === 'sm' ? 'h-6 px-2 text-[10px]' : 'h-7 px-2.5 text-[10px]',
      'bg-gradient-to-r from-amber-950/90 via-amber-900/90 to-slate-950 text-amber-200 border-amber-400/50 shadow-amber-950/60 ring-1 ring-amber-500/20'
    ]"
  >
    <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
      <rect x="3" y="4" width="18" height="13" rx="2.5" fill="#D97706" fill-opacity="0.35" stroke="#F59E0B" stroke-width="1.5"/>
      <path d="M8 20L12 17L16 20" stroke="#FBBF24" stroke-width="1.5" stroke-linecap="round"/>
      <path d="M7 13L10 9.5L13 11.5L17 7.5" stroke="#FDE68A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span>slide</span>
  </span>

  <!-- Note / Document Type -->
  <span 
    v-else 
    :class="[
      'rounded-xl font-mono font-extrabold uppercase tracking-wider shadow-md inline-flex items-center gap-1.5 border backdrop-blur-md transition-all duration-200 hover:scale-105 select-none shrink-0',
      size === 'sm' ? 'h-6 px-2 text-[10px]' : 'h-7 px-2.5 text-[10px]',
      'bg-gradient-to-r from-purple-950/90 via-purple-900/90 to-slate-950 text-purple-200 border-purple-400/50 shadow-purple-950/60 ring-1 ring-purple-500/20'
    ]"
  >
    <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
      <rect x="4" y="3" width="16" height="18" rx="2.5" fill="#7C3AED" fill-opacity="0.35" stroke="#A855F7" stroke-width="1.5"/>
      <line x1="8" y1="8" x2="16" y2="8" stroke="#C084FC" stroke-width="1.5" stroke-linecap="round"/>
      <line x1="8" y1="12" x2="16" y2="12" stroke="#C084FC" stroke-width="1.5" stroke-linecap="round"/>
      <line x1="8" y1="16" x2="13" y2="16" stroke="#E9D5FF" stroke-width="1.5" stroke-linecap="round"/>
    </svg>
    <span>note</span>
  </span>
</template>
