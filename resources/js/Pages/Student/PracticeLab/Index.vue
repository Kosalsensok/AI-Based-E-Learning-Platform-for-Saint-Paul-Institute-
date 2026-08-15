<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const props = defineProps<{
  activeTab?: string
}>()

const majorLabs = [
  {
    major: 'IT & Networking',
    title: 'Interactive Coding Lab & Terminal Environment',
    khTitle: 'បន្ទប់អនុវត្តសរសេរកូដ & Terminal',
    icon: '💻',
    href: '/student/practice-lab/it',
    desc: 'Online sandbox environment with interactive GCC C/C++ compiler, terminal simulator, and automated test cases.',
    topics: ['C/C++ Code Runner', 'Linux Terminal Simulation', 'Database Query Playground', 'Git & Branching Practice'],
    badge: 'Real-time Compiler',
    gradient: 'from-blue-600/20 to-cyan-600/20 border-cyan-500/30 text-cyan-300'
  },
  {
    major: 'Tourism Management',
    title: 'Tourism Case Study & Scenario Analysis',
    khTitle: 'វិភាគករណីសិក្សា & សេវាកម្មទេសចរណ៍',
    icon: '✈️',
    href: '/student/practice-lab/tourism',
    desc: 'Simulate hotel guest reservations, itinerary planning for Angkor Wat & coastal tours, and customer conflict resolution.',
    topics: ['Hotel Front-Desk Simulation', 'Cambodia Tour Package Pricing', 'Eco-Tourism Case Analysis', 'Crisis Handling in Hospitality'],
    badge: 'Scenario Simulator',
    gradient: 'from-amber-600/20 to-yellow-600/20 border-amber-500/30 text-amber-300'
  },
  {
    major: 'English Literature',
    title: 'English Writing Lab & Grammar Checker',
    khTitle: 'បន្ទប់អនុវត្តតែងសេចក្តី & វេយ្យាករណ៍',
    icon: '📖',
    href: '/student/practice-lab/english',
    desc: 'Essay structure workshop, paragraph cohesion builder, and vocabulary refinement with instant AI evaluation.',
    topics: ['Argumentative Essay Builder', 'Grammar Drill Challenges', 'Phonetics & Pronunciation Guide', 'Academic Abstract Writing'],
    badge: 'AI Writing Evaluator',
    gradient: 'from-purple-600/20 to-pink-600/20 border-purple-500/30 text-purple-300'
  },
  {
    major: 'Agronomy',
    title: 'Agronomy Field Simulation & Crop Case Practice',
    khTitle: 'ការពិសោធន៍កសិកម្ម & ជំងឺដំណាំ',
    icon: '🌾',
    href: '/student/practice-lab/agronomy',
    desc: 'Soil pH calculator, rice crop irrigation schedule generator, pest diagnosis guide, and organic fertilizer recipes.',
    topics: ['Soil Nutrient & NPK Calculator', 'Pest & Fungus Diagnosis Tool', 'Drip Irrigation Planning', 'Rice Paddy Yield Estimation'],
    badge: 'Crop Simulator',
    gradient: 'from-emerald-600/20 to-green-600/20 border-emerald-500/30 text-emerald-300'
  },
  {
    major: 'Social Work',
    title: 'Social Work Case Practice & Counseling Simulations',
    khTitle: 'ករណីសិក្សាការងារសង្គម & ការប្រឹក្សា',
    icon: '🤝',
    href: '/student/practice-lab/social-work',
    desc: 'Interactive role-play scenarios, community needs assessment templates, and crisis intervention counseling workflows.',
    topics: ['Client Intake Assessment Tool', 'Child Protection Protocol Simulator', 'Community Resource Mapping', 'Ethics & Confidentiality Case Studies'],
    badge: 'Counseling Lab',
    gradient: 'from-rose-600/20 to-red-600/20 border-rose-500/30 text-rose-300'
  }
]
</script>

<template>
  <StudentLayout title="Practice Lab — 5 Majors Interactive Labs">
    <div class="space-y-6">
      
      <!-- Hero Header -->
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-indigo-800/60 rounded-3xl p-6 md:p-8 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold uppercase tracking-wider">
            🧪 Dynamic Departmental Simulation
          </span>
          <h1 class="text-2xl md:text-3xl font-black text-white mt-2 flex items-center gap-3">
            <span>7. PRACTICE LAB (5 MAJORS)</span>
          </h1>
          <p class="text-xs md:text-sm text-slate-300 mt-1 max-w-2xl">
            បរិស្ថានអនុវត្តជាក់ស្តែង Dynamic តាមជំនាញទាំង 5 មហាវិទ្យាល័យ របស់វិទ្យាស្ថានសន្តប៉ូល (Saint Paul Institute)
          </p>
        </div>
      </div>

      <!-- 5 Majors Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="lab in majorLabs"
          :key="lab.major"
          class="bg-slate-800/90 border border-slate-700/80 hover:border-cyan-500/50 rounded-3xl p-6 flex flex-col justify-between shadow-xl transition-all hover:-translate-y-1 group"
        >
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-3xl p-3 bg-slate-900/80 rounded-2xl border border-slate-700/80 group-hover:scale-110 transition-transform">
                {{ lab.icon }}
              </span>
              <span class="px-2.5 py-1 rounded-xl bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 text-[11px] font-bold">
                {{ lab.badge }}
              </span>
            </div>

            <div>
              <p class="text-[11px] text-cyan-400 font-bold uppercase tracking-wider">{{ lab.major }}</p>
              <h3 class="text-base font-bold text-white mt-0.5 group-hover:text-cyan-300 transition-colors">
                {{ lab.title }}
              </h3>
              <p class="text-xs text-slate-400 font-medium mt-0.5">{{ lab.khTitle }}</p>
              <p class="text-xs text-slate-300 mt-2 leading-relaxed">
                {{ lab.desc }}
              </p>
            </div>

            <div class="space-y-1.5 pt-3 border-t border-slate-700/60 text-xs">
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lab Modules:</p>
              <ul class="space-y-1 text-slate-300 text-[11px]">
                <li v-for="t in lab.topics" :key="t" class="flex items-center gap-2">
                  <span class="text-cyan-400">▹</span>
                  <span>{{ t }}</span>
                </li>
              </ul>
            </div>
          </div>

          <div class="mt-6 pt-4 border-t border-slate-700/60">
            <Link
              :href="lab.href"
              class="w-full block text-center px-4 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-md transition-all"
            >
              Launch {{ lab.major }} Lab →
            </Link>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
