<script setup lang="ts">
const props = defineProps<{
  form: any
  saveSettings: () => void
  testS3Upload: () => void
}>()

const emit = defineEmits(['openS3Browser'])
</script>

<template>
  <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 rounded-2xl p-6 space-y-6 shadow-xl">
    <div class="flex items-center justify-between border-b border-slate-700/60 pb-4">
      <div>
        <h2 class="text-lg font-bold text-white flex items-center gap-2">☁️ S3 Cloud Storage Settings</h2>
        <p class="text-xs text-slate-400">Manage cloud storage for videos, course slides, PDF notes, certificates, and backups.</p>
      </div>
      <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">🟢 Connected</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">Storage Provider</label>
        <select v-model="form.storage_provider" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5">
          <option value="aws_s3">AWS S3</option>
          <option value="digitalocean">DigitalOcean Spaces</option>
          <option value="wasabi">Wasabi</option>
          <option value="r2">Cloudflare R2</option>
          <option value="minio">MinIO (Local Server)</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">Region</label>
        <input v-model="form.s3_region" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5" />
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">Bucket Name</label>
        <input v-model="form.s3_bucket" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5" />
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">Endpoint URL</label>
        <input v-model="form.s3_endpoint_url" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5" />
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">Folder Prefix</label>
        <input v-model="form.s3_folder_prefix" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5" />
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">Access Key ID</label>
        <input v-model="form.s3_access_key_id" type="text" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5" />
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-300 mb-1">Secret Access Key</label>
        <input v-model="form.s3_secret_access_key" type="password" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5" />
      </div>
    </div>

    <!-- Access & Security -->
    <div class="border-t border-slate-700/60 pt-6 space-y-4">
      <h3 class="text-sm font-bold text-slate-200">🔐 Access & Security</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">File Visibility</label>
          <select v-model="form.s3_file_visibility" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5">
            <option value="private">🔒 Private (Signed URLs)</option>
            <option value="public">🌐 Public Read</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">Signed URL Expiry (mins)</label>
          <input v-model="form.s3_signed_url_expiry" type="number" class="w-full bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-2.5" />
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-300 mb-1">Server Encryption</label>
          <input v-model="form.s3_encryption" type="text" readonly class="w-full bg-slate-900 border border-slate-700 text-xs text-slate-400 rounded-xl px-3 py-2.5" />
        </div>
      </div>
    </div>

    <div class="flex items-center justify-between border-t border-slate-700/60 pt-4">
      <div class="flex items-center gap-2">
        <button @click="testS3Upload" type="button" class="px-3 py-2 text-xs bg-slate-700 text-slate-200 hover:bg-slate-600 rounded-xl">🧪 Test Upload</button>
        <button @click="emit('openS3Browser')" type="button" class="px-3 py-2 text-xs bg-slate-700 text-slate-200 hover:bg-slate-600 rounded-xl">📂 Browse Bucket</button>
      </div>
      <button @click="saveSettings" type="button" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl shadow-lg">💾 Save Storage Settings</button>
    </div>
  </div>
</template>
