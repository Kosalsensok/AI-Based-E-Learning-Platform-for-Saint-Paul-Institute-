<script setup lang="ts">
import { ref, watch, computed } from 'vue'

const props = defineProps<{
  show: boolean
  pricingData?: any
  majors: any[]
  teachers: any[]
}>()

const emit = defineEmits(['close', 'save'])

const form = ref({
  course_title: 'C Programming Basics',
  subject: 'C Programming',
  major: 'IT & Networking',
  teacher: 'Mr. Sophea',
  pricing_model: 'One-Time',
  base_price: 50.00,
  currency: 'USD',
  early_bird: true,
  early_bird_discount: 10,
  early_bird_until: '2025-09-01',
  student_referral: true,
  student_referral_discount: 5,
  bulk_discount: false,
  bulk_discount_percent: 15,
  teacher_share_percent: 70,
  accepted_aba: true,
  accepted_cash: true,
  accepted_transfer: false,
  payment_window_days: 7,
  lock_content: true,
})

watch(() => props.pricingData, (newVal) => {
  if (newVal) {
    form.value.course_title = newVal.course || form.value.course_title
    form.value.teacher = newVal.teacher || form.value.teacher
    form.value.base_price = newVal.base_price ?? form.value.base_price
    form.value.teacher_share_percent = (newVal.teacher_share_percent !== undefined && newVal.teacher_share_percent !== null)
      ? newVal.teacher_share_percent
      : 70
    form.value.pricing_model = newVal.pricing_model || 'One-Time'
  }
}, { immediate: true })

const teacherSharePercent = computed(() => form.value.teacher_share_percent ?? 70)
const platformSharePercent = computed(() => Math.max(0, 100 - teacherSharePercent.value))

const teacherShareAmount = computed(() => ((form.value.base_price || 0) * (teacherSharePercent.value)) / 100)
const platformShareAmount = computed(() => (form.value.base_price || 0) - teacherShareAmount.value)

const handleSave = () => {
  emit('save', form.value)
  emit('close')
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-slate-900/95 rounded-2xl shadow-2xl border border-slate-700/60 w-full max-w-2xl overflow-hidden my-6 transform transition-all text-slate-100 font-sans">
      
      <!-- Sleek Soft Modal Header -->
      <div class="bg-slate-950/80 px-6 py-4 text-white flex items-center justify-between border-b border-slate-800/80">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-indigo-500/10 border border-indigo-500/20 rounded-xl text-indigo-400 shrink-0 inline-flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-bold text-slate-100">Edit Pricing – {{ form.course_title }}</h3>
            <p class="text-xs text-slate-400 font-normal mt-0.5">Set course price tier, promotional discounts, and revenue split</p>
          </div>
        </div>
        <button @click="$emit('close')" class="text-slate-400 hover:text-white text-lg font-bold w-8 h-8 flex items-center justify-center rounded-xl bg-slate-800/60 hover:bg-slate-700 transition cursor-pointer">×</button>
      </div>

      <!-- Modal Body (Soft Eye-Friendly Layout) -->
      <div class="p-6 space-y-5 text-xs text-slate-200 max-h-[75vh] overflow-y-auto">
        <!-- 1. Base Price & Pricing Model -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-950/40 p-4 rounded-xl border border-slate-800/80">
          <div>
            <label class="block font-semibold text-slate-300 mb-1.5 flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span>Base Price ($)</span>
            </label>
            <div class="relative">
              <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-bold text-emerald-400 text-sm pointer-events-none">$</span>
              <input 
                v-model.number="form.base_price" 
                type="number" 
                min="0"
                step="1" 
                placeholder="0.00"
                class="w-full pl-8 pr-3 py-2 bg-slate-950 border border-slate-700/80 rounded-xl font-mono font-bold text-emerald-400 text-sm focus:outline-none focus:border-indigo-500 transition" 
              />
            </div>
          </div>

          <div>
            <label class="block font-semibold text-slate-300 mb-1.5 flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>Pricing Model</span>
            </label>
            <div class="flex items-center p-1 bg-slate-950 rounded-xl border border-slate-800">
              <button 
                type="button"
                v-for="m in ['One-Time', 'Per Semester', 'Subscription']" 
                :key="m"
                @click="form.pricing_model = m"
                class="flex-1 py-1.5 px-2 text-center text-xs font-semibold rounded-lg transition-all cursor-pointer whitespace-nowrap"
                :class="form.pricing_model === m ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-400 hover:text-slate-200'"
              >
                {{ m }}
              </button>
            </div>
          </div>
        </div>

        <!-- 2. Discount Rules -->
        <div class="space-y-2.5 border-b border-slate-800/80 pb-4">
          <h4 class="font-bold text-purple-300 uppercase text-[11px] flex items-center gap-2 tracking-wider">
            <span class="p-1 bg-purple-500/10 border border-purple-500/20 rounded-lg text-purple-400 inline-flex items-center justify-center">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
            </span>
            <span>DISCOUNT RULES</span>
          </h4>

          <div class="space-y-2">
            <!-- Early Bird Discount Row -->
            <div 
              class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 rounded-xl border transition-all"
              :class="form.early_bird ? 'bg-slate-950/60 border-slate-700/80' : 'bg-slate-950/20 border-slate-800/40 opacity-40'"
            >
              <label class="inline-flex items-center gap-2.5 cursor-pointer font-semibold text-slate-200 text-xs shrink-0">
                <input type="checkbox" v-model="form.early_bird" class="rounded text-indigo-500 focus:ring-indigo-500/40 bg-slate-950 border-slate-700 w-4 h-4 cursor-pointer" />
                <span>Early Bird Discount</span>
              </label>

              <div class="flex items-center gap-2.5 text-slate-300 flex-wrap">
                <div class="relative w-20">
                  <input 
                    v-model.number="form.early_bird_discount" 
                    type="number" 
                    min="0"
                    max="100"
                    :disabled="!form.early_bird"
                    class="w-full pr-6 pl-2.5 py-1.5 border rounded-xl text-center font-mono font-bold text-xs transition"
                    :class="form.early_bird ? 'bg-slate-950 border-slate-700 text-purple-300 focus:border-purple-500' : 'bg-slate-900 border-slate-800 text-slate-600 cursor-not-allowed'"
                  />
                  <span class="absolute right-2.5 top-1/2 -translate-y-1/2 font-mono font-bold text-xs" :class="form.early_bird ? 'text-purple-300' : 'text-slate-600'">%</span>
                </div>

                <div class="flex items-center gap-1.5 text-xs">
                  <span class="text-slate-400 font-medium text-[11px]">Expires:</span>
                  <input 
                    v-model="form.early_bird_until" 
                    type="date" 
                    :disabled="!form.early_bird"
                    class="px-2.5 py-1.5 border rounded-xl text-xs font-mono transition"
                    :class="form.early_bird ? 'bg-slate-950 border-slate-700 text-slate-200 focus:border-purple-500' : 'bg-slate-900 border-slate-800 text-slate-600 cursor-not-allowed'"
                  />
                </div>
              </div>
            </div>

            <!-- Student Referral Discount Row -->
            <div 
              class="flex items-center justify-between p-3 rounded-xl border transition-all"
              :class="form.student_referral ? 'bg-slate-950/60 border-slate-700/80' : 'bg-slate-950/20 border-slate-800/40 opacity-40'"
            >
              <label class="inline-flex items-center gap-2.5 cursor-pointer font-semibold text-slate-200 text-xs">
                <input type="checkbox" v-model="form.student_referral" class="rounded text-indigo-500 focus:ring-indigo-500/40 bg-slate-950 border-slate-700 w-4 h-4 cursor-pointer" />
                <span>Student Referral Discount</span>
              </label>

              <div class="relative w-20">
                <input 
                  v-model.number="form.student_referral_discount" 
                  type="number" 
                  min="0"
                  max="100"
                  :disabled="!form.student_referral"
                  class="w-full pr-6 pl-2.5 py-1.5 border rounded-xl text-center font-mono font-bold text-xs transition"
                  :class="form.student_referral ? 'bg-slate-950 border-slate-700 text-purple-300 focus:border-purple-500' : 'bg-slate-900 border-slate-800 text-slate-600 cursor-not-allowed'"
                />
                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 font-mono font-bold text-xs" :class="form.student_referral ? 'text-purple-300' : 'text-slate-600'">%</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. Revenue Split Configuration -->
        <div class="bg-indigo-950/30 p-4 rounded-xl border border-indigo-900/40 space-y-2.5">
          <h4 class="font-bold text-indigo-300 uppercase text-[11px] flex items-center gap-2 tracking-wider">
            <span class="p-1 bg-indigo-500/10 border border-indigo-500/20 rounded-lg text-indigo-400 inline-flex items-center justify-center">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
              </svg>
            </span>
            <span>REVENUE SPLIT CONFIGURATION</span>
          </h4>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-slate-300 mb-1.5">Teacher Share (%)</label>
              <div class="relative">
                <input 
                  v-model.number="form.teacher_share_percent" 
                  type="number" 
                  min="0" 
                  max="100" 
                  class="w-full pr-7 pl-3.5 py-2 bg-slate-950 border border-indigo-800/60 rounded-xl font-mono font-bold text-indigo-300 text-xs focus:border-indigo-500" 
                />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 font-mono font-bold text-xs text-indigo-400">%</span>
              </div>
              <span class="text-[11px] text-slate-400 font-medium mt-1 block">Platform Share: <strong class="text-emerald-400 font-mono">{{ platformSharePercent }}%</strong></span>
            </div>

            <div>
              <label class="block font-semibold text-slate-300 mb-1.5">Calculated Split Amount</label>
              <div class="p-2.5 bg-slate-950/80 rounded-xl border border-indigo-800/40 font-mono text-xs space-y-1">
                <div class="flex items-center justify-between">
                  <span class="text-slate-400 font-medium">Teacher ({{ teacherSharePercent }}%):</span>
                  <strong class="text-indigo-300 font-bold">${{ teacherShareAmount.toFixed(2) }}</strong>
                </div>
                <div class="flex items-center justify-between border-t border-slate-800/80 pt-1">
                  <span class="text-slate-400 font-medium">Platform ({{ platformSharePercent }}%):</span>
                  <strong class="text-emerald-400 font-bold">${{ platformShareAmount.toFixed(2) }}</strong>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 4. Accepted Payment Methods -->
        <div class="space-y-2">
          <h4 class="font-bold text-slate-300 uppercase text-[11px] flex items-center gap-2 tracking-wider">
            <span class="p-1 bg-slate-800 border border-slate-700/80 rounded-lg text-indigo-400 inline-flex items-center justify-center">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </span>
            <span>ACCEPTED PAYMENT METHODS</span>
          </h4>

          <div class="flex flex-wrap items-center gap-2.5 font-semibold text-slate-200">
            <label 
              v-for="pm in [
                { key: 'accepted_aba', label: 'ABA PayWay', color: 'text-indigo-400' },
                { key: 'accepted_cash', label: 'Cash on Campus', color: 'text-emerald-400' },
                { key: 'accepted_transfer', label: 'Bank Transfer', color: 'text-amber-400' }
              ]"
              :key="pm.key"
              class="px-3 py-2 rounded-xl border transition-all cursor-pointer flex items-center gap-2 text-xs"
              :class="form[pm.key] ? 'bg-slate-950 border-slate-700 text-slate-100 shadow-xs' : 'bg-slate-950/20 border-slate-800/60 text-slate-500 opacity-60'"
            >
              <input type="checkbox" v-model="form[pm.key]" class="rounded text-indigo-600 focus:ring-indigo-500/40 bg-slate-950 border-slate-700 w-4 h-4 cursor-pointer" />
              <span :class="pm.color">{{ pm.label }}</span>
            </label>
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
          class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer flex items-center gap-1.5"
        >
          <span>Save Changes</span>
        </button>
      </div>
    </div>
  </div>
</template>
