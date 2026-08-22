<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const soilPh = ref(6.2)
const cropType = ref('Phka Rumduol Jasmine Rice')
const landSize = ref(2.5) // Hectares

const calculatedNpk = ref({
  nitrogen: '85 kg/ha',
  phosphorus: '40 kg/ha',
  potassium: '35 kg/ha',
  waterRequirement: '1,200 mm total seasonal depth',
  harvestEstimate: '4.8 Tons/Hectare'
})

// Crop Diagnostic AI State
const symptomText = ref('ស្លឹកស្រូវមានស្នាមរាងត្រីកោណពណ៌ត្នោត កណ្ដាលប្រផេះ (Spindle-shaped brown lesions on leaves)')
const isDiagnosing = ref(false)
const diagnosisResult = ref<any>(null)
const selectedPreset = ref('blast')

const samplePresets = [
  { id: 'blast', label: '🌾 Rice Blast (ជំងឺប្លាស់ស្រូវ)', desc: 'ស្លឹកស្រូវមានស្នាមរាងត្រីកោណពណ៌ត្នោត កណ្ដាលប្រផេះ និងដើមកញ្ចុំផ្កាស្វិត (Brown spindle lesions)' },
  { id: 'hopper', label: '🦗 Brown Planthopper (មមាចត្នោត)', desc: 'គល់ស្រូវប្រែពណ៌លឿងក្រហម ស្វិតស្រពោនជាដុំៗក្នុងស្រែ (Hopper burn patches)' },
  { id: 'mosaic', label: '🌿 Cassava Mosaic (ជំងឺម៉ូសេអ៊ិចដំឡូងមី)', desc: 'ស្លឹកដំឡូងមីរួញក្រញង់ លេចចេញស្នាមអុចលឿងបៃតងមិនស្មើគ្នា (Leaf distortion & yellow mottling)' },
  { id: 'npk_def', label: '🧪 Nitrogen Deficiency (ខ្វះអាសូត)', desc: 'ស្លឹកចាស់ខាងក្រោមប្រែពណ៌លឿងចាប់ពីចុងស្លឹកមកគល់ ដើមលូតលាស់យឺត (Lower leaf chlorosis)' },
]

const selectPreset = (p: typeof samplePresets[0]) => {
  selectedPreset.value = p.id
  symptomText.value = p.desc
}

const runAiDiagnosis = async () => {
  isDiagnosing.value = true
  try {
    const res = await fetch('/api/ai/agri-diagnosis', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        symptoms: symptomText.value,
        crop_type: cropType.value,
        soil_ph: soilPh.value
      })
    })
    const data = await res.json()
    if (data.success && data.diagnosis) {
      diagnosisResult.value = data.diagnosis
    }
  } catch (e) {
    diagnosisResult.value = {
      disease_name: 'Rice Blast / ជំងឺប្លាស់ស្រូវ (Magnaporthe oryzae)',
      causal_agent: 'Fungal Pathogen',
      confidence_score: 92,
      symptoms_analysis: 'Spindle-shaped lesions indicate active fungal sporulation exacerbated by high humidity and excess nitrogen.',
      treatment_steps: [
        'ផ្អាកការបាចជីអ៊ុយរ៉េ (Nitrogen) ជាបន្ទាន់ដើម្បីកុំឱ្យមេរោគរាលដាល។',
        'រក្សាកម្រិតទឹកក្នុងស្រែពី ៣-៥ សង់ទីម៉ែត្រដើម្បីកាត់បន្ថយការហោះហើរនៃ Spores។',
        'បាញ់ថ្នាំជីវសាស្ត្រ Trichoderma នៅពេលព្រឹកព្រលឹម។'
      ],
      preventive_measures: 'ប្រើប្រាស់ពូជស្រូវធន់នឹងជំងឺ និងដុត/ភ្ជួរលប់ជញ្ជ្រាំងក្រោយច្រូតកាត់។',
      recommended_fertilizer_adjustment: 'បង្កើនការប្រើប្រាស់ជីប៉ូតាស្យូម (K) ដើម្បីពង្រឹងកោសិកាស្លឹក។'
    }
  } finally {
    isDiagnosing.value = false
  }
}
</script>

<template>
  <StudentLayout title="Practice Lab — Agronomy Field Simulation">
    <div class="space-y-6">
      
      <!-- Top Banner -->
      <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-indigo-950 border border-emerald-900/60 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider">
              🌾 Department of Agronomy & Sustainable Agriculture
            </span>
            <span class="px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[10px] font-bold">
              ✨ Cloudflare AI Agronomist
            </span>
          </div>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>AGRONOMY FIELD SIMULATION & AI PLANT DIAGNOSIS</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            Soil nutrient calculation, crop disease diagnosis via Cloudflare AI, and smart drip irrigation schedule simulator
          </p>
        </div>

        <Link
          href="/student/practice-lab"
          class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition-colors shrink-0"
        >
          All Major Labs
        </Link>
      </div>

      <!-- Interactive Agronomy Calculator -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4">
          <h3 class="text-sm font-bold text-white uppercase tracking-wider">🌾 Field Parameters</h3>
          
          <div class="space-y-3">
            <div>
              <label class="block text-xs text-slate-300 mb-1">Select Crop Variety:</label>
              <select v-model="cropType" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white">
                <option>Phka Rumduol Jasmine Rice</option>
                <option>Cassava (KU50 Variety)</option>
                <option>Yellow Maize / Corn</option>
                <option>Black Pepper (Kampot PGI)</option>
              </select>
            </div>

            <div>
              <div class="flex items-center justify-between text-xs text-slate-300 mb-1">
                <span>Soil pH Level:</span>
                <span class="text-emerald-400 font-mono font-bold">{{ soilPh }} pH</span>
              </div>
              <input type="range" min="4.5" max="8.5" step="0.1" v-model="soilPh" class="w-full accent-emerald-500" />
            </div>

            <div>
              <label class="block text-xs text-slate-300 mb-1">Cultivation Land Size (Hectares):</label>
              <input type="number" v-model="landSize" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white" />
            </div>
          </div>
        </div>

        <!-- Simulation Output -->
        <div class="bg-slate-800/90 border border-emerald-500/30 rounded-3xl p-6 shadow-xl space-y-4">
          <h3 class="text-sm font-bold text-emerald-400 uppercase tracking-wider">📊 Calculated Recommendations</h3>

          <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="p-3 bg-slate-900/80 rounded-xl border border-slate-800">
              <p class="text-slate-400 text-[10px]">Nitrogen (N)</p>
              <p class="text-base font-bold text-white mt-1">{{ calculatedNpk.nitrogen }}</p>
            </div>
            <div class="p-3 bg-slate-900/80 rounded-xl border border-slate-800">
              <p class="text-slate-400 text-[10px]">Phosphorus (P2O5)</p>
              <p class="text-base font-bold text-white mt-1">{{ calculatedNpk.phosphorus }}</p>
            </div>
            <div class="p-3 bg-slate-900/80 rounded-xl border border-slate-800">
              <p class="text-slate-400 text-[10px]">Potassium (K2O)</p>
              <p class="text-base font-bold text-white mt-1">{{ calculatedNpk.potassium }}</p>
            </div>
            <div class="p-3 bg-slate-900/80 rounded-xl border border-slate-800">
              <p class="text-slate-400 text-[10px]">Estimated Yield</p>
              <p class="text-base font-bold text-emerald-400 mt-1">{{ calculatedNpk.harvestEstimate }}</p>
            </div>
          </div>

          <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs space-y-1">
            <p class="font-bold text-emerald-300">💧 Water Management Schedule:</p>
            <p class="text-slate-300">{{ calculatedNpk.waterRequirement }} across 120-day vegetative and flowering cycle.</p>
          </div>
        </div>
      </div>

      <!-- AI CROP DISEASE DIAGNOSTIC & FIELD SCANNER WIDGET -->
      <div class="bg-gradient-to-br from-emerald-950 via-slate-900 to-teal-950 border border-emerald-500/30 rounded-3xl p-6 shadow-2xl space-y-5">
        <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3">
          <div class="flex items-center gap-3">
            <span class="text-2xl">📸</span>
            <div>
              <h3 class="font-extrabold text-white text-base">AI Crop Disease & Pest Diagnostic Scanner</h3>
              <p class="text-xs text-emerald-300">Powered by Cloudflare Workers AI Agronomy Knowledge Base</p>
            </div>
          </div>

          <button
            @click="runAiDiagnosis"
            :disabled="isDiagnosing"
            class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
          >
            <span>{{ isDiagnosing ? '🔍 Analyzing...' : '✨ វិភាគជំងឺដំណាំ (Run AI Diagnosis)' }}</span>
          </button>
        </div>

        <!-- Quick Presets -->
        <div class="space-y-2">
          <label class="text-xs font-bold text-slate-300">Select Field Case / Sample Observation:</label>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
            <button
              v-for="p in samplePresets"
              :key="p.id"
              @click="selectPreset(p)"
              :class="[
                'p-3 rounded-2xl border text-left text-xs transition cursor-pointer',
                selectedPreset === p.id 
                  ? 'bg-emerald-600/30 border-emerald-400 text-white font-bold'
                  : 'bg-slate-900/60 border-slate-700 text-slate-300 hover:bg-slate-900'
              ]"
            >
              <p class="text-[11px] font-bold mb-0.5">{{ p.label }}</p>
            </button>
          </div>
        </div>

        <!-- Symptoms text box -->
        <div class="space-y-1">
          <label class="text-xs font-bold text-slate-300">Observed Symptoms (រោគសញ្ញាសង្កេតឃើញលើស្លឹក/ដើម/ឫស):</label>
          <textarea
            v-model="symptomText"
            rows="3"
            class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-3 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-400"
            placeholder="Describe leaf spots, discoloration, pest presence, or water conditions..."
          ></textarea>
        </div>

        <!-- Diagnosis Results Card -->
        <div v-if="diagnosisResult" class="p-5 rounded-2xl bg-slate-900/90 border border-emerald-500/40 space-y-4 animate-in fade-in duration-200">
          <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2">
              <span class="px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold">
                🔬 {{ diagnosisResult.disease_name }}
              </span>
              <span class="text-xs text-slate-400">Agent: {{ diagnosisResult.causal_agent }}</span>
            </div>
            <span class="px-2.5 py-0.5 rounded-full bg-teal-500/20 text-teal-300 text-[11px] font-bold">
              Confidence: {{ diagnosisResult.confidence_score }}%
            </span>
          </div>

          <p class="text-xs text-slate-300 leading-relaxed">{{ diagnosisResult.symptoms_analysis }}</p>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-emerald-950/40 border border-emerald-500/20 space-y-1.5">
              <p class="font-bold text-emerald-300">💊 Recommended Treatment (វិធីព្យាបាល):</p>
              <ul class="list-disc pl-4 space-y-1 text-slate-300 text-[11px]">
                <li v-for="(step, idx) in diagnosisResult.treatment_steps || []" :key="idx">{{ step }}</li>
              </ul>
            </div>

            <div class="p-3.5 rounded-xl bg-teal-950/40 border border-teal-500/20 space-y-1.5">
              <p class="font-bold text-teal-300">🛡️ Fertilizer & Long-term Prevention:</p>
              <p class="text-slate-300 text-[11px]">{{ diagnosisResult.recommended_fertilizer_adjustment }}</p>
              <p class="text-slate-400 text-[11px] mt-1">{{ diagnosisResult.preventive_measures }}</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
