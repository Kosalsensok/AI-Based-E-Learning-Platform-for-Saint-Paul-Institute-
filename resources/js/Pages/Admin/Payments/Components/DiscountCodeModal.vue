<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
}>()

const emit = defineEmits(['close', 'save'])

const form = ref({
  code: 'WELCOME10',
  type: 'Percentage',
  value: 10,
  limit: 100,
  expires_at: '2025-12-31',
  status: 'Active'
})

const handleSave = () => {
  emit('save', form.value)
  emit('close')
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden my-6 transform transition-all">
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
        <div>
          <h3 class="text-base font-bold flex items-center gap-2">
            <span>🏷️</span> CREATE DISCOUNT CODE
          </h3>
          <p class="text-xs text-slate-300">Set discount promo code for courses</p>
        </div>
        <button @click="$emit('close')" class="text-slate-400 hover:text-white text-xl font-bold">×</button>
      </div>

      <div class="p-6 space-y-4 text-xs text-slate-700">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Discount Code String:</label>
          <input v-model="form.code" type="text" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-mono font-bold uppercase text-indigo-700" placeholder="e.g. WELCOME10" />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Discount Type:</label>
            <select v-model="form.type" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl">
              <option value="Percentage">Percentage (%)</option>
              <option value="Fixed Amount">Fixed Amount ($)</option>
            </select>
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Discount Value:</label>
            <input v-model.number="form.value" type="number" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Usage Limit:</label>
            <input v-model.number="form.limit" type="number" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl" placeholder="100" />
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Expiration Date:</label>
            <input v-model="form.expires_at" type="date" class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl" />
          </div>
        </div>
      </div>

      <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-between">
        <button @click="$emit('close')" class="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold rounded-xl">
          Cancel
        </button>
        <button @click="handleSave" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md">
          Save Code
        </button>
      </div>
    </div>
  </div>
</template>
