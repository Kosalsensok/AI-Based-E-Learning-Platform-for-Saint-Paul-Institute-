<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const nextCourses = ref([
  {
    id: 1,
    title: 'Data Structures & Algorithms in C',
    category: 'Computer Science & Software',
    duration: '8 Weeks (32 Hours)',
    matchScore: 98,
    level: 'Intermediate',
    prerequisite: 'C Programming Basics (Completed 85%)',
    description: 'Master linked lists, stacks, queues, trees, and algorithmic complexity to build high-performance software.',
    skills: ['Pointers', 'Memory Allocation', 'Big-O', 'Trees & Graphs']
  },
  {
    id: 2,
    title: 'Database Management Systems (SQL & Relational Design)',
    category: 'Database & Cloud',
    duration: '6 Weeks (24 Hours)',
    matchScore: 94,
    level: 'Beginner to Intermediate',
    prerequisite: 'Basic Programming Foundation',
    description: 'Learn relational database modeling, normalization, complex SQL queries, and transaction management.',
    skills: ['SQL', 'Normalization', 'Indexing', 'Transactions']
  },
  {
    id: 3,
    title: 'Modern Web Development with Vue & TypeScript',
    category: 'Web Engineering',
    duration: '10 Weeks (40 Hours)',
    matchScore: 89,
    level: 'Intermediate',
    prerequisite: 'HTML/CSS/JS Basics',
    description: 'Build enterprise-grade single-page web applications with reactive state, component systems, and TypeScript.',
    skills: ['Vue 3', 'TypeScript', 'TailwindCSS', 'InertiaJS']
  }
])
</script>

<template>
  <StudentLayout title="AI Learning Path — Next Course Roadmap">
    <div class="space-y-6">
      
      <!-- Header Banner -->
      <div class="bg-gradient-to-r from-purple-950 via-slate-900 to-indigo-950 border border-purple-900/60 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold uppercase tracking-wider">
            🎯 Career & Skill Roadmap
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🚀 NEXT COURSE (ផ្លូវសិក្សាបន្តបន្ទាប់)</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            AI-curated next course recommendations aligned with your major, prerequisites, and learning velocity
          </p>
        </div>

        <Link
          href="/student/browse"
          class="px-5 py-2.5 rounded-2xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg transition-all text-center"
        >
          Explore All Catalog
        </Link>
      </div>

      <!-- Sub-Tab Navigation Bar -->
      <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-700/60 custom-scrollbar">
        <Link
          href="/student/ai-path/recommended"
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700"
        >
          🎯 1. Recommended Lesson
        </Link>
        <Link
          href="/student/ai-path/review"
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700"
        >
          🔄 2. Review Lessons
        </Link>
        <Link
          href="/student/ai-path/weak-topics"
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700"
        >
          ⚠️ 3. Weak Topics (<40%)
        </Link>
        <Link
          href="/student/ai-path/next-module"
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700"
        >
          ⏭️ 4. Next Module
        </Link>
        <Link
          href="/student/ai-path/next-course"
          class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap bg-purple-600 text-white shadow-md"
        >
          🗺️ 5. Next Course Roadmap
        </Link>
      </div>

      <!-- Course Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div
          v-for="course in nextCourses"
          :key="course.id"
          class="bg-slate-800/90 border border-slate-700/80 hover:border-purple-500/50 rounded-3xl p-6 flex flex-col justify-between shadow-xl transition-all hover:-translate-y-1 group"
        >
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <span class="px-2.5 py-1 rounded-xl bg-purple-500/10 text-purple-300 border border-purple-500/20 text-[11px] font-bold">
                {{ course.level }}
              </span>
              <span class="px-2.5 py-1 rounded-xl bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 text-[11px] font-bold">
                ⚡ {{ course.matchScore }}% AI Match
              </span>
            </div>

            <div>
              <p class="text-[11px] text-purple-400 font-semibold uppercase tracking-wider">{{ course.category }}</p>
              <h3 class="text-base font-bold text-white mt-1 group-hover:text-purple-300 transition-colors">
                {{ course.title }}
              </h3>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed">
              {{ course.description }}
            </p>

            <div class="p-3 bg-slate-900/60 rounded-2xl border border-slate-800 space-y-1 text-[11px]">
              <p class="text-slate-400">🔑 <span class="text-slate-300 font-semibold">Prerequisite:</span> {{ course.prerequisite }}</p>
              <p class="text-slate-400">⏱️ <span class="text-slate-300 font-semibold">Duration:</span> {{ course.duration }}</p>
            </div>

            <div class="flex flex-wrap gap-1.5 pt-1">
              <span
                v-for="skill in course.skills"
                :key="skill"
                class="px-2 py-0.5 rounded-lg bg-slate-700/50 text-slate-300 text-[10px] font-medium"
              >
                #{{ skill }}
              </span>
            </div>
          </div>

          <div class="mt-6 pt-4 border-t border-slate-700/60 flex items-center justify-between gap-3">
            <Link
              :href="`/student/browse`"
              class="w-full text-center px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md transition-all"
            >
              Enroll / View Syllabus →
            </Link>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
