<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const academicEvents = ref([
  {
    id: 1,
    title: 'Midterm Examination Week',
    type: 'Exam',
    date: 'Aug 25 - Aug 30, 2026',
    location: 'Main Examination Halls A & B',
    status: 'Upcoming',
    description: 'Comprehensive mid-semester assessment across all enrolled courses and practical labs.',
    tagClass: 'bg-red-500/20 text-red-300 border-red-500/30'
  },
  {
    id: 2,
    title: 'Annual Technology & AI Project Showcase 2026',
    type: 'Event',
    date: 'Sep 15, 2026 (08:30 AM - 04:30 PM)',
    location: 'Auditorium Hall, Saint Paul Institute',
    status: 'Scheduled',
    description: 'Students present interactive capstone coding projects, IoT systems, and agricultural technology models.',
    tagClass: 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30'
  },
  {
    id: 3,
    title: 'Pchum Ben Festival Holiday (Official Break)',
    type: 'Holiday',
    date: 'Oct 01 - Oct 04, 2026',
    location: 'Campus Closed',
    status: 'Holiday',
    description: 'National holiday break. Online LMS resources and AI Tutor remain accessible 24/7.',
    tagClass: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30'
  },
  {
    id: 4,
    title: 'Final Semester Examinations & Thesis Defenses',
    type: 'Exam',
    date: 'Nov 18 - Nov 28, 2026',
    location: 'Faculty Departments',
    status: 'Scheduled',
    description: 'End-of-semester final exams, module assessments, and final year graduation project defenses.',
    tagClass: 'bg-amber-500/20 text-amber-300 border-amber-500/30'
  }
])
</script>

<template>
  <StudentLayout title="Calendar — Academic Events">
    <div class="space-y-6">
      
      <!-- Top Banner -->
      <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 border border-blue-900/60 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold uppercase tracking-wider">
            📅 Academic Schedule
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🏛️ ACADEMIC EVENTS & KEY DATES</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            Official institute calendar, midterm & final exam periods, showcases, and national holidays
          </p>
        </div>

        <div class="flex items-center gap-2">
          <Link
            href="/student/calendar/upcoming-quiz"
            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-medium text-xs transition-colors"
          >
            Quiz Dates
          </Link>
          <Link
            href="/student/calendar/assignment-due"
            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-medium text-xs transition-colors"
          >
            Assignment Deadlines
          </Link>
        </div>
      </div>

      <!-- Events List -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="event in academicEvents"
          :key="event.id"
          class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-5 shadow-lg space-y-3 flex flex-col justify-between hover:border-blue-500/40 transition-colors"
        >
          <div class="space-y-2.5">
            <div class="flex items-center justify-between">
              <span :class="['px-2.5 py-0.5 rounded-full text-[11px] font-bold border', event.tagClass]">
                {{ event.type }}
              </span>
              <span class="text-xs text-slate-400 font-mono">📍 {{ event.location }}</span>
            </div>

            <h3 class="text-base font-bold text-white">
              {{ event.title }}
            </h3>

            <p class="text-xs text-slate-300 leading-relaxed">
              {{ event.description }}
            </p>
          </div>

          <div class="pt-3 border-t border-slate-700/60 flex items-center justify-between text-xs">
            <span class="text-blue-300 font-bold font-mono">🗓️ {{ event.date }}</span>
            <span class="text-[11px] px-2 py-0.5 rounded-lg bg-slate-700/60 text-slate-300 font-medium">{{ event.status }}</span>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
