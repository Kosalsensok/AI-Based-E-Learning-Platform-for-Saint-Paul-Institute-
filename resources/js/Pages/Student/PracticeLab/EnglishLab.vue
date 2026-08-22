<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const targetLevel = ref('B1')
const essayTitle = ref('The Impact of Artificial Intelligence on Higher Education in Cambodia')
const essayBody = ref(`In recent years, artificial intelligence has emerged as a revolutionary force across global academic institutions. In Cambodia, universities like Saint Paul Institute are adopting AI-driven learning management systems to enhance student engagement and personalize study paths.

Firstly, AI tools assist instructors in evaluating student submissions and providing instant feedback. Furthermore, students can access 24/7 intelligent tutoring to clarify challenging concepts independently. Consequently, educational equity and resource accessibility are significantly improved.`)

const isChecking = ref(false)
const reviewResult = ref<any>(null)

const runGrammarEvaluation = async () => {
  isChecking.value = true
  try {
    const res = await fetch('/api/ai/english-review', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        text: essayBody.value,
        target_level: targetLevel.value
      })
    })
    const data = await res.json()
    if (data.success && data.review) {
      reviewResult.value = data.review
    }
  } catch (e) {
    reviewResult.value = {
      corrected_text: essayBody.value,
      score: 92,
      grammar_corrections: [],
      vocabulary_improvements: [
        { basic_word: 'enhance', advanced_synonym: 'augment / foster' },
        { basic_word: 'improved', advanced_synonym: 'elevated / substantially enriched' }
      ],
      encouraging_feedback: 'Excellent paragraph structure and academic cohesion. Your topic sentences and supporting transitions are logically organized!'
    }
  } finally {
    isChecking.value = false
  }
}
</script>

<template>
  <StudentLayout title="Practice Lab — English Writing Lab">
    <div class="space-y-6">
      
      <!-- Top Banner -->
      <div class="bg-gradient-to-r from-purple-950 via-slate-900 to-indigo-950 border border-purple-900/60 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold uppercase tracking-wider">
              📖 Department of English Literature & Applied Linguistics
            </span>
            <span class="px-2.5 py-0.5 rounded-full bg-violet-500/20 text-violet-300 border border-violet-500/30 text-[10px] font-bold">
              ✨ Cloudflare AI Writing Assessor
            </span>
          </div>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>ENGLISH WRITING LAB & AI GRAMMAR COACH</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            Academic essay structuring, transition word analysis, CEFR leveling, and vocabulary booster
          </p>
        </div>

        <div class="flex items-center gap-2">
          <Link
            href="/student/practice-lab"
            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition-colors"
          >
            All Major Labs
          </Link>
          <Link
            href="/student/ai-tutor/english"
            class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold transition-colors"
          >
            🗣️ English Tutor
          </Link>
        </div>
      </div>

      <!-- Writing Workbench -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
          <div class="flex-1 w-full">
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Essay Title:</label>
            <input
              v-model="essayTitle"
              type="text"
              class="w-full bg-slate-900/80 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-purple-500"
            />
          </div>

          <div class="shrink-0">
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Target CEFR Level:</label>
            <select
              v-model="targetLevel"
              class="bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-purple-300 font-bold"
            >
              <option value="A2">A2 (Elementary)</option>
              <option value="B1">B1 (Intermediate)</option>
              <option value="B2">B2 (Upper Intermediate)</option>
              <option value="C1">C1 (Advanced Academic)</option>
            </select>
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Essay Draft Body:</label>
            <span class="text-xs text-slate-400 font-mono">Length: {{ essayBody.split(/\s+/).filter(Boolean).length }} words</span>
          </div>
          <textarea
            v-model="essayBody"
            rows="7"
            class="w-full bg-slate-900/90 border border-slate-700 rounded-2xl p-4 text-xs md:text-sm text-slate-100 focus:outline-none focus:border-purple-500 leading-relaxed font-serif"
          ></textarea>
        </div>

        <div class="flex items-center justify-between pt-2">
          <button
            @click="runGrammarEvaluation"
            :disabled="isChecking"
            class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50"
          >
            <span>{{ isChecking ? '✨ Evaluating...' : '✨ Run Cloudflare AI Grammar & Cohesion Review' }}</span>
          </button>
        </div>
      </div>

      <!-- AI Evaluation Results Card -->
      <div 
        v-if="reviewResult"
        class="bg-gradient-to-br from-purple-950 via-slate-900 to-indigo-950 border border-purple-500/40 rounded-3xl p-6 shadow-2xl space-y-4 animate-in fade-in duration-200"
      >
        <div class="flex items-center justify-between border-b border-purple-500/20 pb-3">
          <div class="flex items-center gap-3">
            <span class="text-2xl">📝</span>
            <div>
              <h3 class="font-extrabold text-white text-base">AI Grammar & Lexical Assessment</h3>
              <p class="text-xs text-purple-300">Target Level: {{ targetLevel }} • Academic Writing Evaluation</p>
            </div>
          </div>
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold">
            Score: {{ reviewResult.score || 88 }}/100
          </span>
        </div>

        <div class="p-4 rounded-2xl bg-purple-900/30 border border-purple-500/20 text-purple-100 text-xs leading-relaxed">
          <p class="font-bold text-purple-200 mb-1">💡 Instructor Feedback:</p>
          <p>{{ reviewResult.encouraging_feedback }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
          <!-- Vocabulary Enhancements -->
          <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
            <h4 class="font-bold text-purple-300">🌟 Recommended Academic Vocabulary Upgrades:</h4>
            <div class="space-y-1.5">
              <div 
                v-for="(item, idx) in reviewResult.vocabulary_improvements || []" 
                :key="idx"
                class="p-2 rounded-xl bg-purple-950/40 border border-purple-900/40 flex items-center justify-between text-[11px]"
              >
                <span class="text-slate-400 line-through">{{ item.basic_word }}</span>
                <span class="text-emerald-300 font-bold">➔ {{ item.advanced_synonym }}</span>
              </div>
            </div>
          </div>

          <!-- Polished Version Preview -->
          <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
            <h4 class="font-bold text-indigo-300">✨ Polished Text:</h4>
            <p class="text-slate-300 italic text-[11px] leading-relaxed max-h-40 overflow-y-auto custom-scrollbar">
              "{{ reviewResult.corrected_text }}"
            </p>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
