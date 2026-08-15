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
</script>

<template>
  <StudentLayout title="Practice Lab — Agronomy Field Simulation">
    <div class="space-y-6">
      
      <!-- Top Banner -->
      <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-indigo-950 border border-emerald-900/60 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider">
            🌾 Department of Agronomy & Sustainable Agriculture
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>AGRONOMY FIELD SIMULATION & CROP PRACTICE</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            Soil nutrient calculation, crop disease diagnosis, and smart drip irrigation schedule simulator
          </p>
        </div>

        <Link
          href="/student/practice-lab"
          class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition-colors"
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

    </div>
  </StudentLayout>
</template>
