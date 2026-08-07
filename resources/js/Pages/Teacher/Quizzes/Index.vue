<script setup lang="ts">
import { ref, onMounted } from 'vue'
import TeacherLayout from '@/Layouts/TeacherLayout.vue'

// Import sub-component views
import QuestionBankTab from './QuestionBank.vue'
import QuizzesListTab from './QuizzesList.vue'
import PreTestTab from './PreTest.vue'
import PostTestTab from './PostTest.vue'
import PracticeQuizTab from './PracticeQuiz.vue'
import QuizResultsTab from './QuizResults.vue'

const props = defineProps<{
  courses: Array<any>
  quizzes: Array<any>
}>()

const activeTab = ref<'questions' | 'quizzes' | 'pretest' | 'posttest' | 'practice' | 'results'>('questions')

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tab = urlParams.get('tab')
  if (tab === 'questions' || tab === 'quizzes' || tab === 'pretest' || tab === 'posttest' || tab === 'practice' || tab === 'results') {
    activeTab.value = tab
  }
})

const changeTab = (tab: 'questions' | 'quizzes' | 'pretest' | 'posttest' | 'practice' | 'results') => {
  activeTab.value = tab
  const url = new URL(window.location.href)
  url.searchParams.set('tab', tab)
  window.history.pushState({}, '', url.toString())
}
</script>

<template>
  <TeacherLayout title="Quiz & Assessment Module">
    <div class="space-y-6 max-w-7xl mx-auto pb-12">
      <!-- MAIN MODULE HEADER BANNER -->
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
        <!-- Background Glow Accents -->
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-12 left-1/3 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl"></div>

        <div class="relative z-10 space-y-5">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-5">
            <div>
              <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight flex items-center gap-3">
                <span class="p-2.5 bg-blue-600/30 rounded-2xl border border-blue-400/30">📝</span>
                <span>Quiz & Assessment Module</span>
              </h1>
              <p class="text-xs md:text-sm text-slate-300 mt-1">
                Comprehensive teacher evaluation suite — Question bank, quizzes, AI grading, and knowledge gap diagnostics.
              </p>
            </div>
          </div>

          <!-- COLOR SYSTEM LEGEND BAR -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
            <div
              @click="changeTab('pretest')"
              :class="[
                'p-3.5 rounded-2xl border transition cursor-pointer flex items-center gap-3',
                activeTab === 'pretest' ? 'bg-blue-600/30 border-blue-400 ring-2 ring-blue-500/40 shadow' : 'bg-blue-950/40 border-blue-800/50 hover:bg-blue-900/50'
              ]"
            >
              <div class="w-8 h-8 rounded-xl bg-blue-500 text-white font-bold flex items-center justify-center text-sm shadow">
                🟦
              </div>
              <div class="text-xs">
                <p class="font-extrabold text-blue-300">BLUE = Pre-Test</p>
                <p class="text-slate-300 text-[11px]">មុនរៀន • វាស់កម្រិតដំបូង • មិនប៉ះពាល់ពិន្ទុ</p>
              </div>
            </div>

            <div
              @click="changeTab('practice')"
              :class="[
                'p-3.5 rounded-2xl border transition cursor-pointer flex items-center gap-3',
                activeTab === 'practice' ? 'bg-emerald-600/30 border-emerald-400 ring-2 ring-emerald-500/40 shadow' : 'bg-emerald-950/40 border-emerald-800/50 hover:bg-emerald-900/50'
              ]"
            >
              <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white font-bold flex items-center justify-center text-sm shadow">
                🟩
              </div>
              <div class="text-xs">
                <p class="font-extrabold text-emerald-300">GREEN = Practice Quiz</p>
                <p class="text-slate-300 text-[11px]">ក្នុងពេលរៀន • ហាត់បានច្រើនដង • មាន Feedback</p>
              </div>
            </div>

            <div
              @click="changeTab('posttest')"
              :class="[
                'p-3.5 rounded-2xl border transition cursor-pointer flex items-center gap-3',
                activeTab === 'posttest' ? 'bg-rose-600/30 border-rose-400 ring-2 ring-rose-500/40 shadow' : 'bg-rose-950/40 border-rose-800/50 hover:bg-rose-900/50'
              ]"
            >
              <div class="w-8 h-8 rounded-xl bg-rose-500 text-white font-bold flex items-center justify-center text-sm shadow">
                🟥
              </div>
              <div class="text-xs">
                <p class="font-extrabold text-rose-300">RED = Post-Test</p>
                <p class="text-slate-300 text-[11px]">ក្រោយរៀន • ប៉ះពាល់ Completion • បើក Module ថ្មី</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- MAIN TABS SWITCHER -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-1.5 border border-slate-200/80 dark:border-gray-700 shadow-sm flex flex-wrap gap-1">
        <button
          @click="changeTab('questions')"
          :class="[
            'px-4 py-2.5 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2',
            activeTab === 'questions'
              ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20'
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>❓ Question Bank</span>
        </button>

        <button
          @click="changeTab('quizzes')"
          :class="[
            'px-4 py-2.5 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2',
            activeTab === 'quizzes'
              ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20'
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>🧾 All Quizzes</span>
        </button>

        <button
          @click="changeTab('pretest')"
          :class="[
            'px-4 py-2.5 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2',
            activeTab === 'pretest'
              ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20'
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>🟦 Pre-Test</span>
        </button>

        <button
          @click="changeTab('posttest')"
          :class="[
            'px-4 py-2.5 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2',
            activeTab === 'posttest'
              ? 'bg-rose-600 text-white shadow-md shadow-rose-500/20'
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>🟥 Post-Test</span>
        </button>

        <button
          @click="changeTab('practice')"
          :class="[
            'px-4 py-2.5 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2',
            activeTab === 'practice'
              ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/20'
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>🟩 Practice Quiz</span>
        </button>

        <button
          @click="changeTab('results')"
          :class="[
            'px-4 py-2.5 rounded-xl font-bold text-xs md:text-sm transition-all flex items-center gap-2',
            activeTab === 'results'
              ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20'
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-700'
          ]"
        >
          <span>📊 Quiz Results</span>
        </button>
      </div>

      <!-- TAB CONTENTS -->
      <div>
        <QuestionBankTab v-if="activeTab === 'questions'" :courses="props.courses" />
        <QuizzesListTab v-if="activeTab === 'quizzes'" :courses="props.courses" :quizzes="props.quizzes" />
        <PreTestTab v-if="activeTab === 'pretest'" :courses="props.courses" />
        <PostTestTab v-if="activeTab === 'posttest'" :courses="props.courses" />
        <PracticeQuizTab v-if="activeTab === 'practice'" :courses="props.courses" />
        <QuizResultsTab v-if="activeTab === 'results'" :courses="props.courses" />
      </div>
    </div>
  </TeacherLayout>
</template>
