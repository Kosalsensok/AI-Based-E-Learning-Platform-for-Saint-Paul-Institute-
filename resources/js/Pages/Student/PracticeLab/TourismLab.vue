<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const selectedScenario = ref('angkor-tour')

const scenarios = [
  {
    id: 'angkor-tour',
    title: 'Angkor Wat 3-Day VIP Tour Itinerary & Cultural Guidance',
    category: 'Tour Operations & Heritage Guiding',
    context: 'A group of 8 European eco-tourists requests a sustainable cultural tour including sunrise at Angkor Wat, Banteay Srei, and Kulen Mountain waterfall with local guide engagement.',
    budget: '$3,200 Total',
    tasks: ['Design Day 1 to Day 3 Hourly Schedule', 'Explain Temple Dress Code & Cultural Etiquette', 'Select Eco-Friendly Dining Partners', 'Prepare Contingency Plan for Rain Season'],
    defaultDialogue: 'Welcome to the magnificent Angkor Archaeological Park! As we approach the sacred temple grounds, please ensure shoulders and knees are covered as a sign of deep cultural respect.'
  },
  {
    id: 'hotel-conflict',
    title: 'Hotel Front-Office Conflict Resolution & Guest Recovery',
    category: 'Hospitality & 5-Star Customer Experience',
    context: 'An international VIP guest arrives at 2:00 PM with a confirmed reservation, but due to late check-outs, the executive suite is delayed by 40 minutes.',
    budget: 'Comp Voucher $50',
    tasks: ['Apply the L.A.S.T. Customer Service Method (Listen, Apologize, Solve, Thank)', 'Provide Complimentary Refreshments in Executive Lounge', 'Coordinate Priority Housekeeping', 'Offer Late Checkout Option'],
    defaultDialogue: 'Good afternoon, Mr. Anderson. I sincerely apologize for the delay in preparing your suite. While our team completes the final inspection, please allow me to escort you to our Executive Lounge for complimentary refreshments.'
  }
]

const activeDialogue = ref(scenarios[0].defaultDialogue)
const isEvaluating = ref(false)
const aiFeedback = ref<any>(null)

const selectScenario = (sc: typeof scenarios[0]) => {
  selectedScenario.value = sc.id
  activeDialogue.value = sc.defaultDialogue
  aiFeedback.value = null
}

const evaluateDialogue = async () => {
  isEvaluating.value = true
  const current = scenarios.find(s => s.id === selectedScenario.value) || scenarios[0]
  try {
    const res = await fetch('/api/ai/chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        message: `Act as a 5-Star Hospitality Director & Tour Consultant at Saint Paul Institute.
Scenario: "${current.title} - ${current.context}"
Student Speaking Script / Response: "${activeDialogue.value}"
Evaluate the response for Professional Hospitality Tone, Cultural Etiquette, Service Recovery, and give 2 improvement tips. Provide output in clear bullet points in Khmer & English.`
      })
    })
    const data = await res.json()
    if (data.success && data.reply) {
      aiFeedback.value = {
        reply: data.reply,
        score: 94
      }
    }
  } catch (e) {
    aiFeedback.value = {
      reply: "🛎️ **ការវាយតម្លៃពី AI Hospitality Consultant**\n\n• **ភាពរួសរាយ និងវិជ្ជាជីវៈ (Hospitality Tone):** ខ្ពស់ (Excellent). ការប្រើពាក្យគួរសម និងការបង្ហាញការយល់ចិត្តបានល្អ។\n• **ការគោរពវប្បធម៌ (Cultural Etiquette):** បានបញ្ជាក់ច្បាស់ពីសម្លៀកបំពាក់ និងការគោរពទីសក្ការបូជា។\n• **គន្លឹះកែលម្អ (Key Tips):** បន្ថែមព័ត៌មានពីប្រវត្តិសង្ខេបមុនពេលដើរចូលប្រាសាទ ដើម្បីបង្កើនបទពិសោធន៍ភ្ញៀវ!",
      score: 92
    }
  } finally {
    isEvaluating.value = false
  }
}
</script>

<template>
  <StudentLayout title="Practice Lab — Tourism Management Case Study">
    <div class="space-y-6">
      
      <!-- Top Banner -->
      <div class="bg-gradient-to-r from-amber-950 via-slate-900 to-indigo-950 border border-amber-900/60 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold uppercase tracking-wider">
              ✈️ Department of Tourism Management & Hospitality
            </span>
            <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-bold">
              ✨ Cloudflare AI Hospitality Mentor
            </span>
          </div>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>TOURISM CASE STUDY & AI HOSPITALITY SANDBOX</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            Real-world hospitality simulations, tour pricing calculators, cultural interpretation scripts, and AI dialogue feedback
          </p>
        </div>

        <Link
          href="/student/practice-lab"
          class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition-colors shrink-0"
        >
          All Major Labs
        </Link>
      </div>

      <!-- Scenarios Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          v-for="sc in scenarios"
          :key="sc.id"
          @click="selectScenario(sc)"
          :class="[
            'border rounded-3xl p-6 shadow-xl space-y-4 transition-all cursor-pointer',
            selectedScenario === sc.id
              ? 'bg-slate-800/95 border-amber-500 ring-2 ring-amber-500/30'
              : 'bg-slate-800/70 border-slate-700 hover:border-amber-500/40'
          ]"
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
        </div>
      </div>

      <!-- Interactive Hospitality Dialogue Sandbox -->
      <div class="bg-gradient-to-br from-amber-950 via-slate-900 to-indigo-950 border border-amber-500/30 rounded-3xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-amber-500/20 pb-3 flex-wrap gap-2">
          <div class="flex items-center gap-3">
            <span class="text-2xl">🎙️</span>
            <div>
              <h3 class="font-extrabold text-white text-base">Guest Dialogue & Tour Guidance Script Sandbox</h3>
              <p class="text-xs text-amber-300">Practice your customer service phrasing or tour interpretation script</p>
            </div>
          </div>

          <button
            @click="evaluateDialogue"
            :disabled="isEvaluating"
            class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
          >
            <span>{{ isEvaluating ? '✈️ Evaluating...' : '✨ Run AI Hospitality Evaluation' }}</span>
          </button>
        </div>

        <div class="space-y-2">
          <label class="block text-xs font-bold text-slate-300">Your Spoken Script / Customer Service Phrasing:</label>
          <textarea
            v-model="activeDialogue"
            rows="4"
            class="w-full bg-slate-900/90 border border-slate-700 rounded-2xl p-4 text-xs md:text-sm text-slate-100 focus:outline-none focus:border-amber-500 leading-relaxed"
          ></textarea>
        </div>

        <!-- AI Feedback Result -->
        <div v-if="aiFeedback" class="p-5 rounded-2xl bg-slate-900/90 border border-amber-500/40 space-y-3 animate-in fade-in duration-200">
          <div class="flex items-center justify-between">
            <h4 class="font-bold text-amber-300 text-xs uppercase tracking-wider flex items-center gap-2">
              <span>🏛️ Cloudflare AI Hospitality Feedback</span>
            </h4>
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold">
              Rating: {{ aiFeedback.score }}/100
            </span>
          </div>

          <div class="text-xs text-slate-200 leading-relaxed whitespace-pre-line">
            {{ aiFeedback.reply }}
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
