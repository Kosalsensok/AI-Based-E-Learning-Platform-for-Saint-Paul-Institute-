<script setup lang="ts">
const props = defineProps<{
  form: any
  saveSettings: () => void
}>()

const emit = defineEmits(['openPwaPreview'])
</script>

<template>
  <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 rounded-2xl p-6 space-y-6 shadow-xl">
    <div class="flex items-center justify-between border-b border-slate-700/60 pb-4">
      <div>
        <h2 class="text-lg font-bold text-white flex items-center gap-2">📱 Progressive Web App (PWA) & Offline Rules</h2>
        <p class="text-xs text-slate-400">Configure installable web app options, mobile standalone theme colors, service worker caching, and offline content limits.</p>
      </div>
      <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/30">PWA Ready</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">App Name</label>
        <input v-model="form.pwa_app_name" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5" />
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">Short Name</label>
        <input v-model="form.pwa_short_name" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5" />
      </div>
    </div>

    <div class="flex items-center justify-between border-t border-slate-700/60 pt-4">
      <button @click="emit('openPwaPreview')" type="button" class="px-3 py-2 text-xs bg-slate-700 text-slate-200 hover:bg-slate-600 rounded-xl">📱 Preview App Frame</button>
      <button @click="saveSettings" type="button" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl shadow-lg">💾 Save PWA Settings</button>
    </div>
  </div>
</template>
