<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const activeCase = ref({
  id: 'SW-2026-041',
  clientName: 'Family Support Case (Takeo Province)',
  scenario: 'A rural vulnerable family with 3 school-aged children facing economic distress and high school dropout risk.',
  objectives: [
    'Conduct trauma-informed psycho-social needs assessment',
    'Coordinate with local commune council & school principal for scholarship',
    'Develop 6-month family empowerment & self-reliance roadmap',
    'Document case notes upholding international social work confidentiality standards'
  ]
})
</script>

<template>
  <StudentLayout title="Practice Lab — Social Work Case Practice">
    <div class="space-y-6">
      
      <!-- Top Banner -->
      <div class="bg-gradient-to-r from-rose-950 via-slate-900 to-indigo-950 border border-rose-900/60 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-bold uppercase tracking-wider">
            🤝 Department of Social Work & Community Development
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>SOCIAL WORK CASE PRACTICE & COUNSELING LAB</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            Interactive client intake simulation, community resource mapping, and case management ethical protocols
          </p>
        </div>

        <Link
          href="/student/practice-lab"
          class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition-colors"
        >
          All Major Labs
        </Link>
      </div>

      <!-- Case Practice Panel -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-5">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
          <div>
            <span class="text-xs text-rose-400 font-bold uppercase font-mono">Case ID: {{ activeCase.id }}</span>
            <h3 class="text-base font-bold text-white mt-0.5">{{ activeCase.clientName }}</h3>
          </div>
          <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-300 border border-rose-500/20 text-xs font-bold">
            Interactive Case Simulation
          </span>
        </div>

        <div class="p-4 bg-slate-900/80 rounded-2xl border border-slate-800 space-y-2 text-xs">
          <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">Case Background Context:</p>
          <p class="text-slate-200 leading-relaxed">{{ activeCase.scenario }}</p>
        </div>

        <div class="space-y-2 text-xs">
          <p class="font-bold text-slate-300 uppercase tracking-wider">Counseling & Intervention Steps:</p>
          <ul class="space-y-1.5 text-slate-300">
            <li v-for="obj in activeCase.objectives" :key="obj" class="flex items-center gap-2">
              <span class="text-rose-400">❖</span>
              <span>{{ obj }}</span>
            </li>
          </ul>
        </div>

        <div class="pt-4 border-t border-slate-700/60 flex items-center gap-3">
          <button class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-md transition-all">
            Begin Client Interview Simulation →
          </button>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
