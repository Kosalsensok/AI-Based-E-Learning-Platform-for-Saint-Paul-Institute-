<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const activeEmbedded = ref<string | null>(null)

interface ResourceItem {
  id: number
  type: string
  badge: string
  title: string
  source: string
  language: string
  description: string
  embedUrl: string | null
  externalUrl: string
}

const resources = ref<ResourceItem[]>([
  {
    id: 1,
    type: 'youtube',
    badge: '▶️ YOUTUBE EMBEDDED',
    title: 'C Programming Loops Tutorial - Advanced Concepts',
    source: 'youtube.com (External Video)',
    language: '🇬🇧 English',
    description: 'វីដេអូពន្យល់បន្ថែមអំពី Loops ពី Channel ផ្លូវការ ជាមួយឧទាហរណ៍ជាក់ស្តែង 10 ផ្សេងទៀត។',
    embedUrl: 'https://www.youtube.com/embed/dQw4w9WgXcQ',
    externalUrl: 'https://youtube.com'
  },
  {
    id: 2,
    type: 'github',
    badge: '🐙 GITHUB REPOSITORY',
    title: 'Sample Source Code for Chapter 3 - C Loops',
    source: 'github.com/elms-edu/c-loops-samples',
    language: '🇬🇧 English',
    description: 'កូដគំរូផ្លូវការសម្រាប់អនុវត្តតាមក្នុងមេរៀន រួមមាន Exercise solutions និង Bug fixes។',
    embedUrl: null,
    externalUrl: 'https://github.com'
  },
  {
    id: 3,
    type: 'doc',
    badge: '🌐 DOCUMENTATION WEBSITE',
    title: 'C Control Flow - Cprogramming.com Reference',
    source: 'cprogramming.com/tutorial/c-tutorial.html',
    language: '🇬🇧 English',
    description: 'គេហទំព័រឯកសារយោងផ្លូវការសម្រាប់អានបន្ថែមអំពី Control Flow ក្នុង C។',
    embedUrl: 'https://example.com',
    externalUrl: 'https://cprogramming.com'
  }
])

const openEmbedded = (res: ResourceItem) => {
  if (res.embedUrl) {
    activeEmbedded.value = res.embedUrl
  }
}
</script>

<template>
  <StudentLayout title="URL Links & Resources">
    <div class="space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
          <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
            <span>🔗</span>
            <span>EXTERNAL LINKS & LEARNING RESOURCES</span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            ប្រមូលផ្តុំតំណភ្ជាប់បន្ថែមពីខាងក្រៅ (YouTube, GitHub, Docs) — មើលក្នុង LMS ផ្ទាល់គ្មានការរំខាន
          </p>
        </div>

        <span class="px-3.5 py-1.5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold self-start">
          📺 Embedded Webview Supported
        </span>
      </div>

      <!-- RESOURCE CARDS LIST (Matching Prompt Specs) -->
      <div class="space-y-4">
        <div
          v-for="res in resources"
          :key="res.id"
          class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4 hover:border-cyan-500/30 transition-all"
        >
          <!-- Badge & Language -->
          <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-700/60 pb-3">
            <span :class="[res.type === 'youtube' ? 'bg-red-500/20 text-red-300 border-red-500/30' : res.type === 'github' ? 'bg-purple-500/20 text-purple-300 border-purple-500/30' : 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30', 'px-3 py-1 rounded-full text-xs font-extrabold border']">
              {{ res.badge }}
            </span>

            <span class="px-2.5 py-1 rounded-full bg-slate-900 text-slate-300 text-[10px] font-bold border border-slate-700">
              Language: {{ res.language }}
            </span>
          </div>

          <!-- Title & Source Info -->
          <div class="space-y-1.5">
            <h3 class="text-base font-bold text-white">{{ res.title }}</h3>
            <p class="text-xs text-indigo-400 font-mono">Source: {{ res.source }}</p>
            <p class="text-xs text-slate-300 leading-relaxed pt-1">
              ពិពណ៌នា: {{ res.description }}
            </p>
          </div>

          <!-- EMBEDDED PLAYER IFRAME IF OPENED -->
          <div v-if="res.embedUrl && activeEmbedded === res.embedUrl" class="aspect-video bg-slate-950 rounded-2xl overflow-hidden border border-slate-700 shadow-2xl mt-3">
            <iframe :src="res.embedUrl || undefined" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-wrap items-center gap-3 pt-2">
            <button
              v-if="res.embedUrl"
              @click="openEmbedded(res)"
              class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-md"
            >
              {{ res.type === 'youtube' ? '▶ Open Embedded Player' : '📖 Read Embedded Webview' }}
            </button>

            <a
              :href="res.externalUrl"
              target="_blank"
              class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 flex items-center gap-1.5"
            >
              <span>🔗 Open External Link</span>
              <span>↗️</span>
            </a>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
