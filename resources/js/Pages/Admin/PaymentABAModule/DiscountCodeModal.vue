<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  show: boolean
}>()

const emit = defineEmits(['close', 'save'])

const isUnlimited = ref(false)

const form = ref({
  code: 'WELCOME10',
  type: 'Percentage',
  value: 10,
  limit: 100 as number | string,
  expires_at: '2025-12-31',
  status: 'Active'
})

const handleCodeInput = (e: Event) => {
  const target = e.target as HTMLInputElement
  form.value.code = target.value.toUpperCase().replace(/\s+/g, '')
}

const generateRandomCode = () => {
  const prefixes = ['PROMO', 'OFF', 'SAVE', 'DEAL', 'SPECIAL', 'BONUS']
  const prefix = prefixes[Math.floor(Math.random() * prefixes.length)]
  const num = Math.floor(10 + Math.random() * 89)
  const letter = String.fromCharCode(65 + Math.floor(Math.random() * 26))
  form.value.code = `${prefix}${num}${letter}`
}

const toggleUnlimited = () => {
  if (isUnlimited.value) {
    form.value.limit = 'Unlimited'
  } else {
    form.value.limit = 100
  }
}

const formattedDateDisplay = computed(() => {
  if (!form.value.expires_at) return ''
  const d = new Date(form.value.expires_at)
  if (isNaN(d.getTime())) return ''
  return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
})

const handleSave = () => {
  if (isUnlimited.value) {
    form.value.limit = 'Unlimited'
  }
  emit('save', form.value)
  emit('close')
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-slate-900/95 rounded-2xl shadow-2xl border border-slate-700/60 w-full max-w-md overflow-hidden my-6 transform transition-all text-slate-100 font-sans">
      
      <!-- Sleek Modal Header -->
      <div class="bg-slate-950/80 px-6 py-4 text-white flex items-center justify-between border-b border-slate-800/80">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-purple-500/10 border border-purple-500/20 rounded-xl text-purple-400 shrink-0 inline-flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-bold text-slate-100">CREATE DISCOUNT CODE</h3>
            <p class="text-xs text-slate-400 font-normal mt-0.5">Set promotional voucher code & usage limits</p>
          </div>
        </div>
        <button @click="$emit('close')" class="text-slate-400 hover:text-white text-lg font-bold w-8 h-8 flex items-center justify-center rounded-xl bg-slate-800/60 hover:bg-slate-700 transition cursor-pointer">×</button>
      </div>

      <!-- Modal Body -->
      <div class="p-6 space-y-4 text-xs text-slate-200">
        <!-- 1. Discount Code String & Generate Button -->
        <div>
          <label class="block font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
            <span>Discount Code String</span>
            <span class="text-[10px] text-slate-400 font-normal">Auto UPPERCASE</span>
          </label>
          
          <div class="flex items-center gap-2">
            <input 
              :value="form.code"
              @input="handleCodeInput"
              type="text" 
              class="w-full px-3 py-2 bg-slate-950 border border-slate-700/80 rounded-xl font-mono font-bold uppercase text-purple-300 text-sm focus:outline-none focus:border-purple-500 transition shadow-inner" 
              placeholder="e.g. WELCOME10" 
            />
            <button 
              type="button"
              @click="generateRandomCode"
              class="px-3 py-2 bg-purple-950/80 hover:bg-purple-900 text-purple-300 hover:text-white font-semibold rounded-xl border border-purple-800/60 transition cursor-pointer shrink-0 flex items-center gap-1.5 text-xs shadow-xs"
              title="Generate Random Promo Code"
            >
              <svg class="w-3.5 h-3.5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              <span>Generate</span>
            </button>
          </div>
          <p class="text-[10px] text-slate-400 mt-1">Auto-converted to UPPERCASE, no spaces</p>
        </div>

        <!-- 2. Discount Type & Value (Dynamic Prefix/Suffix $ vs %) -->
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-semibold text-slate-300 mb-1.5">Discount Type</label>
            <select v-model="form.type" class="w-full px-3 py-2 bg-slate-950 border border-slate-700/80 rounded-xl text-slate-100 font-semibold focus:outline-none focus:border-purple-500">
              <option value="Percentage">Percentage (%)</option>
              <option value="Fixed Amount">Fixed Amount ($)</option>
            </select>
          </div>
          <div>
            <label class="block font-semibold text-slate-300 mb-1.5">Discount Value</label>
            <div class="relative">
              <span v-if="form.type === 'Fixed Amount'" class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-emerald-400 text-sm pointer-events-none">$</span>
              <input 
                v-model.number="form.value" 
                type="number" 
                min="0"
                :class="form.type === 'Fixed Amount' ? 'pl-7 pr-3' : 'pl-3 pr-7'"
                class="w-full py-2 bg-slate-950 border border-slate-700/80 rounded-xl font-mono font-bold text-emerald-400 text-sm focus:outline-none focus:border-purple-500 transition" 
              />
              <span v-if="form.type === 'Percentage'" class="absolute right-3 top-1/2 -translate-y-1/2 font-mono font-bold text-purple-400 text-xs pointer-events-none">%</span>
            </div>
          </div>
        </div>

        <!-- 3. Usage Limit (with Unlimited Checkbox) & Expiration Date -->
        <div class="grid grid-cols-2 gap-3">
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block font-semibold text-slate-300">Usage Limit</label>
              <label class="inline-flex items-center gap-1 cursor-pointer text-[10px] text-slate-400 hover:text-white">
                <input type="checkbox" v-model="isUnlimited" @change="toggleUnlimited" class="rounded text-purple-500 focus:ring-purple-500/40 bg-slate-950 border-slate-700 w-3 h-3 cursor-pointer" />
                <span>Unlimited</span>
              </label>
            </div>
            
            <input 
              v-if="!isUnlimited"
              v-model.number="form.limit" 
              type="number" 
              min="1"
              class="w-full px-3 py-2 bg-slate-950 border border-slate-700/80 rounded-xl font-mono font-bold text-white text-xs focus:outline-none focus:border-purple-500 transition" 
              placeholder="100" 
            />
            <div v-else class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl font-mono font-bold text-slate-500 text-xs opacity-60">
              Unlimited
            </div>
          </div>

          <div>
            <label class="block font-semibold text-slate-300 mb-1.5">Expiration Date</label>
            <input 
              v-model="form.expires_at" 
              type="date" 
              class="w-full px-3 py-2 bg-slate-950 border border-slate-700/80 rounded-xl text-slate-200 font-semibold text-xs focus:outline-none focus:border-purple-500 transition" 
            />
            <p v-if="formattedDateDisplay" class="text-[10px] text-slate-400 mt-1 font-mono">Date: <strong class="text-purple-300">{{ formattedDateDisplay }}</strong></p>
          </div>
        </div>
      </div>

      <!-- Action Footer -->
      <div class="bg-slate-950 px-6 py-4 border-t border-slate-800 flex items-center justify-between">
        <button 
          @click="$emit('close')" 
          class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold text-xs rounded-xl border border-slate-700/80 transition cursor-pointer"
        >
          Cancel
        </button>
        <button 
          @click="handleSave" 
          class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer flex items-center gap-1.5"
        >
          <span>Save Code</span>
        </button>
      </div>
    </div>
  </div>
</template>
