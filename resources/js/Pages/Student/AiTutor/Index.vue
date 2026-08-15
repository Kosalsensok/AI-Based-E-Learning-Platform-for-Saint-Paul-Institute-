<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const props = defineProps<{
  activeTab?: string
}>()

const tutorCards = [
  {
    title: 'AI English Tutor (គ្រូភាសាអង់គ្លេស AI)',
    desc: 'ជួយកែសម្រួលប្រយោគ, វេយ្យាករណ៍, Speaking, Writing និងពង្រឹងការសន្ទនាអន្តរជាតិ.',
    icon: '🗣️',
    href: '/student/ai-tutor/english',
    features: ['Realtime Sentence Corrector', 'Grammar Explainer', 'Vocabulary Enrichment', 'IELTS / Academic Writing Support'],
    colorClass: 'from-blue-600/20 to-indigo-600/20 border-blue-500/30 text-blue-300'
  },
  {
    title: 'AI Chat Support 24/7 (សួរសំណួរមេរៀន)',
    desc: 'សួរសំណួរលើមេរៀនគ្រប់មុខវិជ្ជា, គន្លឹះដោះស្រាយកូដ, និងការពន្យល់ស៊ីជម្រៅ.',
    icon: '💬',
    href: '/student/ai-tutor/chat',
    features: ['Instant Q&A Any Time', 'Step-by-step Explanations', 'Code Debugging Assistance', 'Khmer & English Bilingual'],
    colorClass: 'from-emerald-600/20 to-teal-600/20 border-emerald-500/30 text-emerald-300'
  },
  {
    title: 'AI Instant Feedback (មតិស្ថាបនាភ្លាមៗ)',
    desc: 'វិភាគលទ្ធផល Quiz, កិច្ចការ Assignment និងផ្តល់អនុសាសន៍កែលម្អចំណុចខ្សោយ.',
    icon: '⚡',
    href: '/student/ai-tutor/feedback',
    features: ['Detailed Score Diagnostic', 'Targeted Improvement Steps', 'Next Recommended Exercises', 'Exam Readiness Score'],
    colorClass: 'from-amber-600/20 to-orange-600/20 border-amber-500/30 text-amber-300'
  }
]
</script>

<template>
  <StudentLayout title="AI Assistant & Tutor">
    <div class="space-y-6">
      
      <!-- Hero Header -->
      <div class="bg-gradient-to-r from-indigo-950 via-slate-900 to-purple-950 border border-indigo-800/60 rounded-3xl p-6 md:p-8 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-bold uppercase tracking-wider">
            🤖 AI-Powered Personalized Assistance
          </span>
          <h1 class="text-2xl md:text-3xl font-black text-white mt-2 flex items-center gap-3">
            <span>6. AI ASSISTANT / TUTOR</span>
          </h1>
          <p class="text-xs md:text-sm text-slate-300 mt-1 max-w-2xl">
            ជំនួយការ AI ឆ្លាតវៃជួយអ្នករៀនភាសាអង់គ្លេស សួរសំណួរមេរៀន 24/7 និងទទួលបានមតិស្ថាបនាលើ Quiz/Assignment ភ្លាមៗ
          </p>
        </div>
      </div>

      <!-- 3 Tutor Pillars Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          v-for="card in tutorCards"
          :key="card.title"
          class="bg-slate-800/90 border border-slate-700/80 hover:border-indigo-500/50 rounded-3xl p-6 flex flex-col justify-between shadow-xl transition-all hover:-translate-y-1 group"
        >
          <div class="space-y-4">
            <div class="w-12 h-12 rounded-2xl bg-slate-900/80 border border-slate-700 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 transition-transform">
              {{ card.icon }}
            </div>

            <div>
              <h3 class="text-lg font-bold text-white group-hover:text-indigo-300 transition-colors">
                {{ card.title }}
              </h3>
              <p class="text-xs text-slate-300 mt-2 leading-relaxed">
                {{ card.desc }}
              </p>
            </div>

            <div class="space-y-2 pt-2 border-t border-slate-700/60">
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Core Capabilities:</p>
              <ul class="space-y-1 text-xs text-slate-300">
                <li v-for="feat in card.features" :key="feat" class="flex items-center gap-2">
                  <span class="text-emerald-400">✓</span>
                  <span>{{ feat }}</span>
                </li>
              </ul>
            </div>
          </div>

          <div class="mt-6 pt-4 border-t border-slate-700/60">
            <Link
              :href="card.href"
              class="w-full block text-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition-all"
            >
              Open AI Assistant →
            </Link>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
