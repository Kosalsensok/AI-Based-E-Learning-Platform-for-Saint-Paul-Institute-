<script setup lang="ts">
const props = defineProps<{
  form: any
  saveSettings: () => void
}>()

const emit = defineEmits(['openAbaTest'])
</script>

<template>
  <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 rounded-2xl p-6 space-y-6 shadow-xl">
    <div class="flex items-center justify-between border-b border-slate-700/60 pb-4">
      <div>
        <h2 class="text-lg font-bold text-white flex items-center gap-2">💳 ABA PayWay & KHQR Payment Settings</h2>
        <p class="text-xs text-slate-400">Configure ABA KHQR integration, merchant API keys, checkout return URLs, and callback signature validation.</p>
      </div>
      <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">🟢 Sandbox Connected</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">Environment</label>
        <select v-model="form.aba_environment" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5">
          <option value="sandbox">Sandbox / Testing</option>
          <option value="production">Production / Live Payment</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">Merchant ID</label>
        <input v-model="form.aba_merchant_id" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5" />
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">API Base URL</label>
        <input v-model="form.aba_api_base_url" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5" />
      </div>
    </div>

    <div class="flex items-center justify-between border-t border-slate-700/60 pt-4">
      <button @click="emit('openAbaTest')" type="button" class="px-3 py-2 text-xs bg-slate-700 text-slate-200 hover:bg-slate-600 rounded-xl">🧪 Test ABA Payment Modal</button>
      <button @click="saveSettings" type="button" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl shadow-lg">💾 Save ABA Settings</button>
    </div>
  </div>
</template>
