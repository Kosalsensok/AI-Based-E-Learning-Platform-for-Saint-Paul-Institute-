<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const selectedScenario = ref('angkor-tour')

const scenarios = [
  {
    id: 'angkor-tour',
    title: 'Angkor Wat 3-Day VIP Tour Itinerary Planning',
    category: 'Tour Operations & Itinerary Design',
    context: 'A group of 8 European eco-tourists requests a sustainable cultural tour including sunrise at Angkor Wat, Banteay Srei, and Kulen Mountain waterfall with local guide engagement.',
    budget: '$3,200 Total',
    tasks: ['Design Day 1 to Day 3 Hourly Schedule', 'Calculate Transport & Ticket Costing', 'Select Eco-Friendly Dining Partners', 'Prepare Contingency Plan for Rain Season']
  },
  {
    id: 'hotel-conflict',
    title: 'Hotel Front-Office Conflict Resolution',
    category: 'Hospitality & Customer Experience',
    context: 'An international guest arrives at 2:00 PM with a confirmed reservation, but due to late check-outs, the suite is not ready for another 45 minutes.',
    budget: 'Comp Voucher $50',
    tasks: ['Apply the L.A.S.T. Customer Service Method', 'Provide Welcome Drink & Lounge Access', 'Coordinate Priority Housekeeping', 'Offer Late Checkout Compensation']
  }
]
</script>

<template>
  <StudentLayout title="Practice Lab — Tourism Management Case Study">
    <div class="space-y-6">
      
      <!-- Top Banner -->
      <div class="bg-gradient-to-r from-amber-950 via-slate-900 to-indigo-950 border border-amber-900/60 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold uppercase tracking-wider">
            ✈️ Department of Tourism Management & Hospitality
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>TOURISM CASE STUDY & SCENARIO ANALYSIS</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            Real-world hospitality simulations, tour pricing calculators, and guest conflict resolution frameworks
          </p>
        </div>

        <Link
          href="/student/practice-lab"
          class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition-colors"
        >
          All Major Labs
        </Link>
      </div>

      <!-- Scenarios Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          v-for="sc in scenarios"
          :key="sc.id"
          class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4 hover:border-amber-500/40 transition-colors"
        >
          <div class="flex items-center justify-between">
            <span class="px-2.5 py-1 rounded-xl bg-amber-500/10 text-amber-300 border border-amber-500/20 text-xs font-bold">
              {{ sc.category }}
            </span>
            <span class="text-xs font-bold text-slate-400 font-mono">{{ sc.budget }}</span>
          </div>

          <h3 class="text-base font-bold text-white">
            {{ sc.title }}
          </h3>

          <p class="text-xs text-slate-300 leading-relaxed p-3 bg-slate-900/60 rounded-2xl border border-slate-800">
            {{ sc.context }}
          </p>

          <div class="space-y-1.5 pt-2">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Required Deliverables:</p>
            <ul class="space-y-1 text-xs text-slate-300">
              <li v-for="t in sc.tasks" :key="t" class="flex items-center gap-2">
                <span class="text-amber-400">☑</span>
                <span>{{ t }}</span>
              </li>
            </ul>
          </div>

          <div class="pt-4 border-t border-slate-700/60">
            <button
              class="w-full px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-md transition-all text-center"
            >
              Start Scenario Simulation →
            </button>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
