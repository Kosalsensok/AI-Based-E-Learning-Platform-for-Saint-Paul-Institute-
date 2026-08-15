<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const studentInput = ref('I has been studying computer science since two years and I wants to improving my speaking.')
const isAnalyzing = ref(false)
const correctionResult = ref({
  original: 'I has been studying computer science since two years and I wants to improving my speaking.',
  corrected: 'I have been studying computer science for two years, and I want to improve my speaking skills.',
  score: 78,
  explanations: [
    { error: 'I has', fix: 'I have', rule: 'Subject-Verb Agreement: First-person singular "I" takes "have".' },
    { error: 'since two years', fix: 'for two years', rule: '"For" is used for durations/periods of time, while "since" is for specific start points.' },
    { error: 'I wants to improving', fix: 'I want to improve', rule: 'Infinitive after "want to" takes base verb form ("improve").' }
  ],
  vocabularySuggestions: [
    { original: 'studying', enhanced: 'pursuing an academic degree in' },
    { original: 'improve my speaking', enhanced: 'enhance my verbal fluency and articulation' }
  ]
})

const analyzeText = () => {
  isAnalyzing.value = true
  setTimeout(() => {
    isAnalyzing.value = false
  }, 600)
}
</script>

<template>
  <StudentLayout title="AI Assistant — AI English Tutor">
    <div class="space-y-6">
      
      <!-- Top Banner -->
      <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 border border-blue-900/60 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold uppercase tracking-wider">
            🗣️ AI English Writing & Speaking Tutor
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>AI ENGLISH TUTOR (ជួយកែប្រយោគ, វេយ្យាករណ៍)</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            Realtime sentence analysis, grammar explanation, and vocabulary enhancer for academic & professional English
          </p>
        </div>

        <div class="flex items-center gap-2">
          <Link
            href="/student/ai-tutor/chat"
            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition-colors"
          >
            💬 AI Chat Support
          </Link>
          <Link
            href="/student/ai-tutor/feedback"
            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition-colors"
          >
            ⚡ Quiz Feedback
          </Link>
        </div>
      </div>

      <!-- Main Interactive Panel -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Input Column -->
        <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 space-y-4 shadow-xl">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
              <span>✍️ Enter Sentence or Paragraph</span>
            </h3>
            <span class="text-[11px] text-slate-400">English Input</span>
          </div>

          <textarea
            v-model="studentInput"
            rows="6"
            class="w-full bg-slate-900/90 border border-slate-700 rounded-2xl p-4 text-xs md:text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-colors leading-relaxed"
            placeholder="Type or paste your English text here for instant AI grammar correction..."
          ></textarea>

          <div class="flex items-center justify-between pt-2">
            <button
              @click="analyzeText"
              :disabled="isAnalyzing"
              class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2"
            >
              <span v-if="isAnalyzing">⏳ Analyzing with AI...</span>
              <span v-else>✨ Analyze & Correct Now</span>
            </button>
            <button
              @click="studentInput = ''"
              class="text-xs text-slate-400 hover:text-slate-200"
            >
              Clear
            </button>
          </div>
        </div>

        <!-- Output & Explanation Column -->
        <div class="bg-slate-800/90 border border-blue-500/30 rounded-3xl p-6 space-y-5 shadow-xl">
          <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <h3 class="text-sm font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-2">
              <span>🤖 AI Analysis & Polished Version</span>
            </h3>
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-xs font-bold font-mono">
              Score: {{ correctionResult.score }}/100
            </span>
          </div>

          <!-- Corrected Text -->
          <div class="p-4 bg-slate-900/80 border border-slate-700 rounded-2xl space-y-1.5">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Polished English Sentence:</p>
            <p class="text-sm font-bold text-emerald-300 leading-relaxed">
              "{{ correctionResult.corrected }}"
            </p>
          </div>

          <!-- Grammar Rules Explanations -->
          <div class="space-y-2">
            <p class="text-xs font-bold text-slate-200 uppercase tracking-wider">Grammar Fixes & Rules:</p>
            <div
              v-for="(item, idx) in correctionResult.explanations"
              :key="idx"
              class="p-3 bg-slate-900/60 border border-slate-800 rounded-xl space-y-1 text-xs"
            >
              <div class="flex items-center gap-2">
                <span class="line-through text-red-400 font-semibold">{{ item.error }}</span>
                <span class="text-slate-500">→</span>
                <span class="text-emerald-400 font-bold">{{ item.fix }}</span>
              </div>
              <p class="text-slate-400 text-[11px] leading-normal">{{ item.rule }}</p>
            </div>
          </div>

          <!-- Vocabulary Enrichments -->
          <div class="space-y-2 pt-1 border-t border-slate-700/60">
            <p class="text-xs font-bold text-indigo-300 uppercase tracking-wider">Advanced Vocabulary Suggestions:</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
              <div
                v-for="(vocab, idx) in correctionResult.vocabularySuggestions"
                :key="idx"
                class="p-2.5 rounded-xl bg-indigo-500/10 border border-indigo-500/20"
              >
                <p class="text-[10px] text-slate-400">Instead of "{{ vocab.original }}":</p>
                <p class="font-bold text-indigo-200 mt-0.5">{{ vocab.enhanced }}</p>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </StudentLayout>
</template>
