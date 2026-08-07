<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import LiveClassSchedule from './LiveClassSchedule.vue'
import UpcomingQuiz from './UpcomingQuiz.vue'
import AssignmentDue from './AssignmentDue.vue'

const props = defineProps<{
  activeTab?: string
}>()

const currentTab = ref(props.activeTab || 'live-class')

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tabParam = urlParams.get('tab')
  if (tabParam && ['live-class', 'live', 'upcoming-quiz', 'quiz', 'assignment-due', 'assignment'].includes(tabParam)) {
    if (tabParam === 'live') currentTab.value = 'live-class'
    else if (tabParam === 'quiz') currentTab.value = 'upcoming-quiz'
    else if (tabParam === 'assignment') currentTab.value = 'assignment-due'
    else currentTab.value = tabParam
  }
})

watch(() => props.activeTab, (newTab) => {
  if (newTab) {
    if (newTab === 'live') currentTab.value = 'live-class'
    else if (newTab === 'quiz') currentTab.value = 'upcoming-quiz'
    else if (newTab === 'assignment') currentTab.value = 'assignment-due'
    else currentTab.value = newTab
  }
})
</script>

<template>
  <div class="space-y-6">
    
    <!-- CALENDAR LAYOUT MAIN VIEW HEADER (Matching Prompt Spec) -->
    <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 border border-blue-900/60 rounded-3xl p-6 shadow-2xl space-y-4">
      
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold uppercase tracking-wider">
            📅 LEARNING SCHEDULE &amp; DEADLINES
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5">
            📅 MY LEARNING CALENDAR
          </h1>
        </div>

        <!-- View Controls -->
        <div class="flex items-center gap-2 text-xs">
          <select class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white">
            <option>May 2025</option>
            <option>June 2025</option>
          </select>
          <button class="px-3 py-1.5 rounded-xl bg-blue-600 text-white font-bold">Today</button>
          <button class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 font-bold">Week</button>
          <button class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 font-bold">List</button>
        </div>
      </div>

      <!-- Filters & Color Legend Row -->
      <div class="flex flex-wrap items-center justify-between gap-4 text-xs">
        <div class="flex items-center gap-2">
          <select class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white">
            <option>All Courses</option>
            <option>C Programming Basics</option>
            <option>Database Systems</option>
          </select>
          <select class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white">
            <option>Status: All</option>
            <option>Upcoming</option>
            <option>Completed</option>
          </select>
        </div>

        <!-- COLOR LEGEND (Matching Prompt Spec) -->
        <div class="flex flex-wrap items-center gap-3 font-bold">
          <span class="flex items-center gap-1.5 text-blue-400"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> 🟦 Live Class</span>
          <span class="flex items-center gap-1.5 text-emerald-400"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> 🟩 Quiz</span>
          <span class="flex items-center gap-1.5 text-amber-400"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> 🟧 Assignment</span>
          <span class="flex items-center gap-1.5 text-slate-400"><span class="w-2.5 h-2.5 rounded-full bg-slate-500"></span> ⬜ General</span>
        </div>
      </div>

      <!-- TABS SWITCHER -->
      <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-800 text-xs">
        <button
          @click="currentTab = 'live-class'"
          :class="[currentTab === 'live-class' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-900 text-slate-300 border border-slate-700', 'px-4 py-2 rounded-xl text-xs transition-all']"
        >
          🟦 Live Class Schedule
        </button>
        <button
          @click="currentTab = 'upcoming-quiz'"
          :class="[currentTab === 'upcoming-quiz' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-900 text-slate-300 border border-slate-700', 'px-4 py-2 rounded-xl text-xs transition-all']"
        >
          🟩 Upcoming Quiz
        </button>
        <button
          @click="currentTab = 'assignment-due'"
          :class="[currentTab === 'assignment-due' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-900 text-slate-300 border border-slate-700', 'px-4 py-2 rounded-xl text-xs transition-all']"
        >
          🟧 Assignment Due
        </button>
      </div>

    </div>

    <!-- TAB CONTENT RENDER -->
    <div>
      <LiveClassSchedule v-if="currentTab === 'live-class' || currentTab === 'live'" />
      <UpcomingQuiz v-else-if="currentTab === 'upcoming-quiz' || currentTab === 'quiz'" />
      <AssignmentDue v-else-if="currentTab === 'assignment-due' || currentTab === 'assignment'" />
      <LiveClassSchedule v-else />
    </div>

  </div>
</template>
