<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const searchQuery = ref('')
const selectedTag = ref('All')

const faqTags = ref(['All', 'Variables', 'Loops', 'Pointers', 'Compiler'])

const faqList = ref([
  {
    id: 1,
    question: 'What is a compiler?',
    answer: 'A program that translates high-level source code (written in C) into binary machine code that the computer CPU can execute.',
    tag: 'Compiler',
    status: '✅ Answered',
    helpfulCount: 42,
    open: true
  },
  {
    id: 2,
    question: 'Why is my loop infinite?',
    answer: 'You likely forgot to update the loop counter variable inside the loop body (e.g. i++ or count--), causing the termination condition to never evaluate to false.',
    tag: 'Loops',
    status: '🟡 Verified FAQ',
    helpfulCount: 89,
    open: false
  },
  {
    id: 3,
    question: 'What is the difference between float and double in C?',
    answer: 'A float is a single-precision 32-bit floating point type, while double is a double-precision 64-bit floating point type offering twice the precision and range.',
    tag: 'Variables',
    status: '✅ Answered',
    helpfulCount: 28,
    open: false
  }
])

const toggleFaq = (faq: any) => {
  faq.open = !faq.open
}
</script>

<template>
  <StudentLayout title="Course FAQ">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-indigo-950 border border-emerald-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider">
            📚 FREQUENTLY ASKED QUESTIONS
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>📚 COURSE FAQ — C Programming Basics</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            សំណួរចម្លើយដែលគ្រូបង្រៀនបានផ្ទៀងផ្ទាត់ និងឆ្លើយរួច ស្វែងរកចម្លើយលឿនបំផុត
          </p>
        </div>
      </div>

      <!-- SEARCH & TAG CHIPS -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search question keyword..."
          class="w-full bg-slate-900 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 font-bold"
        />

        <!-- Tag Chips -->
        <div class="flex flex-wrap items-center gap-2 text-xs">
          <span class="font-bold text-slate-400">Tags:</span>
          <button
            v-for="tag in faqTags"
            :key="tag"
            @click="selectedTag = tag"
            :class="[
              selectedTag === tag ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-900 text-slate-300 border border-slate-700',
              'px-3 py-1 rounded-full text-xs transition-all'
            ]"
          >
            [{{ tag }}]
          </button>
        </div>
      </div>

      <!-- ACCORDION FAQ LIST (Matching Prompt Mock Layout) -->
      <div class="space-y-4">
        <div
          v-for="faq in faqList"
          :key="faq.id"
          class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-3 transition-all"
        >
          <!-- Accordion Header -->
          <div @click="toggleFaq(faq)" class="flex items-center justify-between cursor-pointer">
            <h3 class="text-base font-black text-white flex items-center gap-2">
              <span class="text-emerald-400 font-mono">{{ faq.id }})</span>
              <span>{{ faq.question }}</span>
            </h3>
            <span class="text-slate-400 font-bold text-lg">{{ faq.open ? '▲' : '▼' }}</span>
          </div>

          <!-- Accordion Content -->
          <div v-if="faq.open" class="pt-2 border-t border-slate-700/60 space-y-3 text-xs">
            <p class="text-slate-200 leading-relaxed bg-slate-950 p-4 rounded-2xl border border-slate-800 font-mono">
              <strong class="text-emerald-400 block pb-1">A:</strong> {{ faq.answer }}
            </p>

            <div class="flex items-center justify-between pt-1">
              <div class="flex items-center gap-2">
                <button @click="faq.helpfulCount++" class="px-3 py-1.5 rounded-xl bg-emerald-600/20 text-emerald-300 font-bold border border-emerald-500/30">
                  👍 Helpful ({{ faq.helpfulCount }})
                </button>
                <button class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 font-bold">
                  🔎 View related topics
                </button>
              </div>
              <span class="text-[10px] text-slate-400 font-mono">Tag: {{ faq.tag }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
