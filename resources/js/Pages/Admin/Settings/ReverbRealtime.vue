<script setup lang="ts">
const props = defineProps<{
  form: any
  saveSettings: () => void
  testReverbConnection: () => void
}>()
</script>

<template>
  <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 rounded-2xl p-6 space-y-6 shadow-xl">
    <div class="flex items-center justify-between border-b border-slate-700/60 pb-4">
      <div>
        <h2 class="text-lg font-bold text-white flex items-center gap-2">📡 Reverb / Real-time WebSocket Settings</h2>
        <p class="text-xs text-slate-400">Configure Laravel Reverb WebSockets for instant in-app alerts, live discussion replies, and live quiz updates.</p>
      </div>
      <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">🟢 Online</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">Broadcast Driver</label>
        <input v-model="form.broadcast_driver" type="text" readonly class="w-full bg-slate-900 border border-slate-700 text-xs text-slate-400 rounded-xl px-3 py-2.5" />
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">WebSocket Host</label>
        <input v-model="form.websocket_host" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5" />
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">WebSocket Port</label>
        <input v-model="form.websocket_port" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5" />
      </div>
    </div>

    <div class="flex items-center justify-between border-t border-slate-700/60 pt-4">
      <div class="flex items-center gap-2">
        <button @click="testReverbConnection" type="button" class="px-3 py-2 text-xs bg-slate-700 text-slate-200 hover:bg-slate-600 rounded-xl">📣 Send Test Event</button>
      </div>
      <button @click="saveSettings" type="button" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl shadow-lg">💾 Save Reverb Settings</button>
    </div>
  </div>
</template>
