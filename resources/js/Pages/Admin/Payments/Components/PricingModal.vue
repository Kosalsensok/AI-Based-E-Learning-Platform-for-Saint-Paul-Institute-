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
    form.value.teacher_share_percent = newVal.teacher_share_percent ?? form.value.teacher_share_percent
    form.value.pricing_model = newVal.pricing_model || 'One-Time'
  }
}, { immediate: true })

const teacherShareAmount = computed(() => ((form.value.base_price || 0) * (form.value.teacher_share_percent || 0)) / 100)
const platformShareAmount = computed(() => (form.value.base_price || 0) - teacherShareAmount.value)

const handleSave = () => {
  emit('save', form.value)
  emit('close')
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-2xl overflow-hidden my-6 transform transition-all">
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
        <div>
          <h3 class="text-lg font-bold flex items-center gap-2">
            <span>💰</span> Edit Pricing – {{ form.course_title }}
          </h3>
          <p class="text-xs text-slate-300">Set per-course pricing, discounts, and revenue split</p>
        </div>
        <button @click="$emit('close')" class="text-slate-400 hover:text-white text-2xl font-bold">×</button>
      </div>

      <div class="p-6 space-y-5 text-xs text-slate-700 max-h-[75vh] overflow-y-auto">
        <!-- Base Price & Model -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Base Price ($):</label>
            <input v-model.number="form.base_price" type="number" step="1" class="w-full p-2.5 bg-white border border-slate-300 rounded-xl font-bold text-emerald-600 text-sm" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Pricing Model:</label>
            <div class="space-y-1 mt-1">
              <label class="inline-flex items-center gap-1.5 cursor-pointer mr-3">
                <input type="radio" value="One-Time" v-model="form.pricing_model" class="text-indigo-600" />
                <span>One-Time</span>
              </label>
              <label class="inline-flex items-center gap-1.5 cursor-pointer mr-3">
                <input type="radio" value="Per Semester" v-model="form.pricing_model" class="text-indigo-600" />
                <span>Per Semester</span>
              </label>
              <label class="inline-flex items-center gap-1.5 cursor-pointer">
                <input type="radio" value="Subscription" v-model="form.pricing_model" class="text-indigo-600" />
                <span>Subscription</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Discount Rules -->
        <div class="space-y-2 border-b border-slate-100 pb-4">
          <h4 class="font-bold text-slate-900 uppercase text-[11px] text-purple-700 flex items-center gap-1">
            <span>🏷️</span> DISCOUNT RULES
          </h4>
          <div class="space-y-2">
            <div class="flex items-center justify-between bg-slate-50 p-2.5 rounded-xl border border-slate-200">
              <label class="inline-flex items-center gap-2 cursor-pointer font-semibold">
                <input type="checkbox" v-model="form.early_bird" class="rounded text-purple-600" />
                <span>Early Bird Discount</span>
              </label>
              <div class="flex items-center gap-2">
                <input v-model.number="form.early_bird_discount" type="number" class="w-16 p-1 bg-white border border-slate-300 rounded-lg text-center" /> %
                <span>before Sep 1, 2025</span>
              </div>
            </div>

            <div class="flex items-center justify-between bg-slate-50 p-2.5 rounded-xl border border-slate-200">
              <label class="inline-flex items-center gap-2 cursor-pointer font-semibold">
                <input type="checkbox" v-model="form.student_referral" class="rounded text-purple-600" />
                <span>Student Referral Discount</span>
              </label>
              <div class="flex items-center gap-2">
                <input v-model.number="form.student_referral_discount" type="number" class="w-16 p-1 bg-white border border-slate-300 rounded-lg text-center" /> %
              </div>
            </div>
          </div>
        </div>

        <!-- Revenue Split -->
        <div class="bg-indigo-50/70 p-4 rounded-2xl border border-indigo-100 space-y-2">
          <h4 class="font-bold text-indigo-900 uppercase text-[11px] flex items-center gap-1">
            <span>💸</span> REVENUE SPLIT
          </h4>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Teacher Share (%):</label>
              <input v-model.number="form.teacher_share_percent" type="number" min="0" max="100" class="w-full p-2 bg-white border border-slate-300 rounded-xl font-bold text-indigo-700" />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Calculated Split:</label>
              <div class="p-2 bg-white rounded-xl border border-indigo-200 font-mono font-bold text-xs">
                Teacher: ${{ teacherShareAmount.toFixed(2) }} | Platform: ${{ platformShareAmount.toFixed(2) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Accepted Methods -->
        <div class="space-y-2">
          <h4 class="font-bold text-slate-900 uppercase text-[11px] flex items-center gap-1">
            <span>💳</span> ACCEPTED PAYMENT METHODS
          </h4>
          <div class="flex items-center gap-6 font-semibold">
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" v-model="form.accepted_aba" class="rounded text-indigo-600" />
              <span>ABA PayWay</span>
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" v-model="form.accepted_cash" class="rounded text-indigo-600" />
              <span>Cash on Campus</span>
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" v-model="form.accepted_transfer" class="rounded text-indigo-600" />
              <span>Bank Transfer</span>
            </label>
          </div>
        </div>
      </div>

      <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-between">
        <button @click="$emit('close')" class="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold rounded-xl">
          Cancel
        </button>
        <button @click="handleSave" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md">
          Save Changes
        </button>
      </div>
    </div>
  </div>
</template>
