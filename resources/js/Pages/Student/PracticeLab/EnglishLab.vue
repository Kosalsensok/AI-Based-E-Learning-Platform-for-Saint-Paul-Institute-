<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const essayTitle = ref('The Impact of Artificial Intelligence on Higher Education in Cambodia')
const essayBody = ref(`In recent years, artificial intelligence has emerged as a revolutionary force across global academic institutions. In Cambodia, universities like Saint Paul Institute are adopting AI-driven learning management systems to enhance student engagement and personalize study paths.

Firstly, AI tools assist instructors in evaluating student submissions and providing instant feedback. Furthermore, students can access 24/7 intelligent tutoring to clarify challenging concepts independently. Consequently, educational equity and resource accessibility are significantly improved.`)

const wordCount = ref(74)
</script>

<template>
  <StudentLayout title="Practice Lab — English Writing Lab">
    <div class="space-y-6">
      
      <!-- Top Banner -->
      <div class="bg-gradient-to-r from-purple-950 via-slate-900 to-indigo-950 border border-purple-900/60 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold uppercase tracking-wider">
            📖 Department of English Literature & Applied Linguistics
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>ENGLISH WRITING LAB & GRAMMAR CHECKER</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            Academic essay structuring, transition word checker, and paragraph coherence analysis
          </p>
        </div>

        <Link
          href="/student/practice-lab"
          class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition-colors"
        >
          All Major Labs
        </Link>
      </div>

      <!-- Writing Workbench -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
        <div>
          <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Essay Title:</label>
          <input
            v-model="essayTitle"
            type="text"
            class="w-full bg-slate-900/80 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-purple-500"
          />
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Essay Draft Body:</label>
            <span class="text-xs text-slate-400 font-mono">Word Count: ~{{ wordCount }} words</span>
          </div>
          <textarea
            v-model="essayBody"
            rows="8"
            class="w-full bg-slate-900/90 border border-slate-700 rounded-2xl p-4 text-xs md:text-sm text-slate-100 focus:outline-none focus:border-purple-500 leading-relaxed font-serif"
          ></textarea>
        </div>

        <div class="flex items-center justify-between pt-2">
          <button
            class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2"
          >
            <span>✨ Run Academic Cohesion & Grammar Evaluation</span>
          </button>
          <Link
            href="/student/ai-tutor/english"
            class="text-xs text-purple-400 hover:text-purple-300 font-semibold"
          >
            Open AI English Tutor →
          </Link>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
