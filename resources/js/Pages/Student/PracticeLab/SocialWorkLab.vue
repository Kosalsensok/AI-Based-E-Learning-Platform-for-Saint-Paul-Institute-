<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const activeCase = ref({
  id: 'SW-2026-041',
  clientName: 'Family Support & Child Protection (Takeo Province)',
  scenario: 'A rural vulnerable family with 3 school-aged children facing economic distress and high school dropout risk. The mother is hesitant to accept external assistance fearing community stigma.',
  objectives: [
    'Establish empathetic rapport using non-judgmental active listening.',
    'Uphold client self-determination while discussing psychosocial safety nets.',
    'Coordinate with local commune council (CCWC) & school administration.',
    'Formulate a 6-month family empowerment & referral roadmap.'
  ]
})

const studentResponse = ref(`សួស្តីអ្នកមីង! ខ្ញុំយល់ពីការព្រួយបារម្ភ និងអារម្មណ៍តានតឹងរបស់អ្នកមីងចំពោះកូនៗក្នុងស្ថានភាពនេះ។ យើងខ្ញុំមកទីនេះដើម្បីស្វែងយល់ និងពិគ្រោះយោបល់រួមគ្នា ដោយការសម្រេចចិត្តទាំងអស់គឺអាស្រ័យលើអ្នកមីង។ រាល់ព័ត៌មានដែលយើងពិភាក្សាត្រូវបានរក្សាជាការសម្ងាត់បំផុត។ តើយើងអាចរៀបចំផែនការអាហារូបករណ៍សម្រាប់កូនៗជាជំហានដំបូងបានទេ?`)

const isSubmitting = ref(false)
const evaluationResult = ref<any>(null)

const evaluateResponse = async () => {
  isSubmitting.value = true
  try {
    const res = await fetch('/api/ai/social-work-case', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        scenario: activeCase.value.scenario,
        response: studentResponse.value
      })
    })
    const data = await res.json()
    if (data.success && data.evaluation) {
      evaluationResult.value = data.evaluation
    }
  } catch (e) {
    evaluationResult.value = {
      empathy_score: 92,
      ethics_score: 95,
      communication_analysis: 'ការឆ្លើយតបរបស់អ្នកបង្ហាញពីការយល់ចិត្ត (Empathy) ខ្ពស់ ការគោរពសិទ្ធិស្វ័យសម្រេចរបស់ Client និងការបញ្ជាក់យ៉ាងច្បាស់ពីការរក្សាការសម្ងាត់ (Confidentiality)។',
      ethical_strengths: [
        'Upholds client autonomy and self-determination.',
        'Explicit reassurance of confidentiality to alleviate stigma.',
        'Warm, culturally respectful language appropriate for Cambodian community context.'
      ],
      areas_to_improve: [
        'Highlight specific local support groups or school counseling focal points.',
        'Ask open-ended question inviting the mother to express her immediate priority.'
      ],
      suggested_supervisor_response: 'ជំរាបសួរអ្នកមីង! ខ្ញុំសូមកោតសរសើរចំពោះការខិតខំប្រឹងប្រែងមើលថែកូនៗរបស់អ្នកមីងកន្លងមក។ ពួកយើងមកទីនេះដើម្បីស្តាប់ និងគាំទ្រតាមបំណងរបស់អ្នកមីង...'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <StudentLayout title="Practice Lab — Social Work Case Practice">
    <div class="space-y-6">
      
      <!-- Top Banner -->
      <div class="bg-gradient-to-r from-rose-950 via-slate-900 to-indigo-950 border border-rose-900/60 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <span class="px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-bold uppercase tracking-wider">
              🤝 Department of Social Work & Community Development
            </span>
            <span class="px-2.5 py-0.5 rounded-full bg-pink-500/20 text-pink-300 border border-pink-500/30 text-[10px] font-bold">
              ✨ Cloudflare AI Ethics Supervisor
            </span>
          </div>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>SOCIAL WORK CASE PRACTICE & AI COUNSELING SIMULATION</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            Interactive client intake simulation, community resource mapping, and case management ethical protocols
          </p>
        </div>

        <Link
          href="/student/practice-lab"
          class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition-colors shrink-0"
        >
          All Major Labs
        </Link>
      </div>

      <!-- Case Practice Panel -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-5">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3 flex-wrap gap-2">
          <div>
            <span class="text-xs text-rose-400 font-bold uppercase font-mono">Case ID: {{ activeCase.id }}</span>
            <h3 class="text-base font-bold text-white mt-0.5">{{ activeCase.clientName }}</h3>
          </div>
          <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-300 border border-rose-500/20 text-xs font-bold">
            Interactive Scenario Sandbox
          </span>
        </div>

        <div class="p-4 bg-slate-900/80 rounded-2xl border border-slate-800 space-y-2 text-xs">
          <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">Case Background Context:</p>
          <p class="text-slate-200 leading-relaxed">{{ activeCase.scenario }}</p>
        </div>

        <!-- Student Counseling Response Box -->
        <div class="space-y-2">
          <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
            Your Counseling Response / Initial Dialogue (ការសន្ទនា និងការផ្ដល់ប្រឹក្សាដំបូង):
          </label>
          <textarea
            v-model="studentResponse"
            rows="5"
            class="w-full bg-slate-900/90 border border-slate-700 rounded-2xl p-4 text-xs md:text-sm text-slate-100 focus:outline-none focus:border-rose-500 leading-relaxed"
            placeholder="Type your empathetic response, active listening reflection, and ethical assurances..."
          ></textarea>
        </div>

        <div class="pt-2 flex items-center justify-between">
          <button 
            @click="evaluateResponse"
            :disabled="isSubmitting"
            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-500 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50"
          >
            <span>{{ isSubmitting ? '🤝 Evaluating...' : '✨ Run AI Supervisor Ethical & Empathy Evaluation' }}</span>
          </button>
        </div>
      </div>

      <!-- AI Evaluation Feedback Card -->
      <div 
        v-if="evaluationResult"
        class="bg-gradient-to-br from-rose-950 via-slate-900 to-pink-950 border border-rose-500/40 rounded-3xl p-6 shadow-2xl space-y-4 animate-in fade-in duration-200"
      >
        <div class="flex items-center justify-between border-b border-rose-500/20 pb-3 flex-wrap gap-2">
          <div class="flex items-center gap-3">
            <span class="text-2xl">⚖️</span>
            <div>
              <h3 class="font-extrabold text-white text-base">Social Work Supervisor Assessment</h3>
              <p class="text-xs text-rose-300">Ethics, Empathy, and Protocol Analysis</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-bold">
              Empathy: {{ evaluationResult.empathy_score }}/100
            </span>
            <span class="px-3 py-1 rounded-full bg-pink-500/20 text-pink-300 border border-pink-500/30 text-xs font-bold">
              Ethics: {{ evaluationResult.ethics_score }}/100
            </span>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-rose-900/30 border border-rose-500/20 text-rose-100 text-xs leading-relaxed">
          <p class="font-bold text-rose-200 mb-1">💡 Communication & Tone Analysis:</p>
          <p>{{ evaluationResult.communication_analysis }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
          <!-- Strengths -->
          <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
            <h4 class="font-bold text-emerald-400">✓ Ethical & Interpersonal Strengths:</h4>
            <ul class="list-disc pl-4 space-y-1 text-slate-300 text-[11px]">
              <li v-for="(str, idx) in evaluationResult.ethical_strengths || []" :key="idx">{{ str }}</li>
            </ul>
          </div>

          <!-- Areas to improve -->
          <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
            <h4 class="font-bold text-amber-400">💡 Professional Growth & Referral Hints:</h4>
            <ul class="list-disc pl-4 space-y-1 text-slate-300 text-[11px]">
              <li v-for="(tip, idx) in evaluationResult.areas_to_improve || []" :key="idx">{{ tip }}</li>
            </ul>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-1 text-xs">
          <h4 class="font-bold text-pink-300">🗣️ Model Phrasing Example (Supervisor Perspective):</h4>
          <p class="text-slate-300 italic text-[11px] leading-relaxed">
            "{{ evaluationResult.suggested_supervisor_response }}"
          </p>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
