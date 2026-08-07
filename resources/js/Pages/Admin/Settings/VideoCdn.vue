<script setup lang="ts">
const props = defineProps<{
  form: any
  saveSettings: () => void
  purgeCdnCache: () => void
}>()

const emit = defineEmits(['openCdnPlayback'])
</script>

<template>
  <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 rounded-2xl p-6 space-y-6 shadow-xl">
    <div class="flex items-center justify-between border-b border-slate-700/60 pb-4">
      <div>
        <h2 class="text-lg font-bold text-white flex items-center gap-2">🎬 Video CDN & Streaming Settings</h2>
        <p class="text-xs text-slate-400">Configure global content delivery network, HLS adaptive streaming profiles, DRM signed URLs, and watermarks.</p>
      </div>
      <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">🟢 CDN Active</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">CDN Provider</label>
        <select v-model="form.cdn_provider" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5">
          <option value="cloudfront">CloudFront (AWS)</option>
          <option value="cloudflare">Cloudflare Stream</option>
          <option value="fastly">Fastly</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">CDN Domain</label>
        <input v-model="form.cdn_domain" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5" />
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">Origin Storage</label>
        <input v-model="form.cdn_origin" type="text" readonly class="w-full bg-slate-900 border border-slate-700 text-xs text-slate-400 rounded-xl px-3 py-2.5" />
      </div>
    </div>

    <!-- Security & Quality -->
    <div class="border-t border-slate-700/60 pt-6 space-y-4">
      <h3 class="text-sm font-bold text-slate-200">🔒 Video Protection & Quality Profiles</h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <label class="flex items-center gap-3 cursor-pointer">
          <input v-model="form.cdn_require_signed_url" type="checkbox" class="w-4 h-4 rounded text-blue-600 bg-slate-900 border-slate-700" />
          <span class="text-xs text-slate-300">Require Signed CDN URL (Expirable Token)</span>
        </label>

        <label class="flex items-center gap-3 cursor-pointer">
          <input v-model="form.cdn_watermark_student_name" type="checkbox" class="w-4 h-4 rounded text-blue-600 bg-slate-900 border-slate-700" />
          <span class="text-xs text-slate-300">Watermark Student Name / ID over video playback</span>
        </label>
      </div>
    </div>

    <div class="flex items-center justify-between border-t border-slate-700/60 pt-4">
      <div class="flex items-center gap-2">
        <button @click="purgeCdnCache" type="button" class="px-3 py-2 text-xs bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl">🧹 Purge CDN Cache</button>
        <button @click="emit('openCdnPlayback')" type="button" class="px-3 py-2 text-xs bg-slate-700 text-slate-200 hover:bg-slate-600 rounded-xl">▶️ Test Video Playback</button>
      </div>
      <button @click="saveSettings" type="button" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl shadow-lg">💾 Save CDN Settings</button>
    </div>
  </div>
</template>
