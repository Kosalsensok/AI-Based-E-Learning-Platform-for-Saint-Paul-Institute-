<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  announcement: any
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()

const activeLang = ref<'kh' | 'en'>('kh')
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-xl w-full p-6 space-y-5 shadow-2xl border border-slate-200 dark:border-gray-700 animate-in fade-in zoom-in duration-200">
      <!-- Header -->
      <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-gray-700">
        <div class="flex items-center gap-2">
          <span class="text-xl">📢</span>
          <div>
            <h3 class="text-base font-black text-slate-900 dark:text-white">
              STUDENT VIEW PREVIEW — Announcement Card
            </h3>
            <p class="text-xs text-slate-500 font-semibold">
              How students will see this card on their dashboard and course page.
            </p>
          </div>
        </div>

        <button @click="emit('close')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white font-black text-xl">
          ✕
        </button>
      </div>

      <!-- Language Switcher for Preview -->
      <div class="flex items-center justify-between bg-slate-100 dark:bg-gray-700 p-1.5 rounded-xl text-xs font-bold">
        <span class="text-slate-500 pl-2">Toggle Preview Language:</span>
        <div class="flex items-center gap-1">
          <button
            @click="activeLang = 'kh'"
            :class="['px-3 py-1 rounded-lg transition', activeLang === 'kh' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm font-black' : 'text-slate-600 dark:text-slate-300']"
          >
            🇰🇭 Khmer
          </button>
          <button
            @click="activeLang = 'en'"
            :class="['px-3 py-1 rounded-lg transition', activeLang === 'en' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm font-black' : 'text-slate-600 dark:text-slate-300']"
          >
            🇬🇧 English
          </button>
        </div>
      </div>

      <!-- Preview Announcement Card Body -->
      <div class="bg-gradient-to-br from-blue-50/90 via-indigo-50/50 to-purple-50/50 dark:from-gray-800 dark:to-gray-900 p-5 rounded-2xl border-2 border-blue-200 dark:border-blue-900/60 shadow-md space-y-4">
        <!-- Top Title & Priority -->
        <div class="flex items-center justify-between gap-2">
          <div class="font-black text-base text-slate-900 dark:text-white flex items-center gap-2">
            <span>📢 {{ activeLang === 'kh' ? (announcement?.title_kh || announcement?.title_en) : (announcement?.title_en || announcement?.title_kh) }}</span>
          </div>

          <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
            🔴 High Priority
          </span>
        </div>

        <!-- Meta info -->
        <div class="text-[11px] text-slate-500 font-semibold flex items-center gap-3">
          <span>⏰ {{ announcement?.created_at || '16 June 2025' }}</span>
          <span>•</span>
          <span>From: <strong>Mr. Sophea (Instructor)</strong></span>
        </div>

        <!-- Content -->
        <p class="text-xs sm:text-sm text-slate-800 dark:text-slate-200 font-medium leading-relaxed bg-white/80 dark:bg-gray-800/80 p-4 rounded-xl border border-slate-200/60 dark:border-gray-700">
          {{ activeLang === 'kh' ? (announcement?.content_kh || announcement?.content_en) : (announcement?.content_en || announcement?.content_kh) }}
        </p>

        <!-- Action Button & Attachment -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
          <button class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black text-xs rounded-xl shadow-md flex items-center gap-1.5">
            <span>🚀</span> {{ activeLang === 'kh' ? (announcement?.action_text_kh || 'ចាប់ផ្តើមរៀន') : (announcement?.action_text_en || 'Start Learning') }}
          </button>

          <span class="text-xs text-slate-500 font-bold hover:underline cursor-pointer">
            📎 View Attachment (.pdf)
          </span>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex justify-end pt-2">
        <button
          @click="emit('close')"
          class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-xs shadow"
        >
          Close Preview
        </button>
      </div>
    </div>
  </div>
</template>
