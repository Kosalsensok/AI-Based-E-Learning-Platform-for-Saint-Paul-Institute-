<script setup lang="ts">
import { ref } from 'vue'
import { 
  DownloadCloud, 
  Package, 
  ShieldCheck, 
  RefreshCw, 
  Plus, 
  Sparkles, 
  HardDrive, 
  Lock, 
  CheckCircle2, 
  Download 
} from 'lucide-vue-next'

const props = withDefaults(defineProps<{
  stats?: any
  offlinePackages?: Array<any>
}>(), {
  stats: () => ({}),
  offlinePackages: () => []
})

const emit = defineEmits(['openOfflineModal'])
</script>

<template>
  <div class="space-y-6 text-slate-100 font-khmer">
    
    <!-- Top Modern Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-slate-900/80 backdrop-blur-md p-5 rounded-3xl border border-slate-800/80 shadow-xl flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0">
          <Package class="w-6 h-6" />
        </div>
        <div>
          <p class="text-[11px] text-slate-400 font-mono uppercase tracking-wider">OFFLINE ZIP BUNDLES</p>
          <p class="text-lg font-bold text-white mt-0.5">2 Active Packages</p>
        </div>
      </div>

      <div class="bg-slate-900/80 backdrop-blur-md p-5 rounded-3xl border border-slate-800/80 shadow-xl flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center shrink-0">
          <DownloadCloud class="w-6 h-6" />
        </div>
        <div>
          <p class="text-[11px] text-slate-400 font-mono uppercase tracking-wider">PWA CACHE DOWNLOADS</p>
          <p class="text-lg font-bold text-sky-300 mt-0.5 font-mono">790 Downloads</p>
        </div>
      </div>

      <div class="bg-slate-900/80 backdrop-blur-md p-5 rounded-3xl border border-slate-800/80 shadow-xl flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
          <ShieldCheck class="w-6 h-6" />
        </div>
        <div>
          <p class="text-[11px] text-slate-400 font-mono uppercase tracking-wider">ENCRYPTION STATUS</p>
          <p class="text-lg font-bold text-emerald-300 mt-0.5 font-mono">AES-256 Enabled</p>
        </div>
      </div>

      <div class="bg-slate-900/80 backdrop-blur-md p-5 rounded-3xl border border-slate-800/80 shadow-xl flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
          <HardDrive class="w-6 h-6" />
        </div>
        <div>
          <p class="text-[11px] text-slate-400 font-mono uppercase tracking-wider">DEVICE SYNC ENGINE</p>
          <p class="text-lg font-bold text-amber-300 mt-0.5 font-mono">PWA Ready</p>
        </div>
      </div>
    </div>

    <!-- Package Table Container -->
    <div class="bg-slate-900/80 backdrop-blur-md p-6 rounded-3xl border border-slate-800/80 shadow-xl space-y-5">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-800/80 pb-4">
        <div>
          <h2 class="text-base font-bold text-white flex items-center gap-2.5 khmer-title-clean">
            <span class="p-2 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
              <DownloadCloud class="w-5 h-5" />
            </span>
            <span>OFFLINE CONTENT & ZIP BUNDLES</span>
          </h2>
          <p class="text-xs text-slate-400 mt-1 khmer-text-clean">
            គ្រប់គ្រង Offline Package (ZIP) សម្រាប់និស្សិតទាញយកទុកមើលក្នុង Mobile/Desktop App ពេលគ្មាន Internet
          </p>
        </div>
        <button 
          @click="$emit('openOfflineModal')"
          class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-2xl shadow-md shadow-indigo-600/20 transition text-xs flex items-center gap-1.5 cursor-pointer shrink-0"
        >
          <Plus class="w-4 h-4" />
          <span>Create ZIP Package</span>
        </button>
      </div>

      <div class="overflow-x-auto rounded-2xl border border-slate-800/80 bg-slate-950/40">
        <table class="w-full text-left text-xs text-slate-200">
          <thead class="bg-slate-950/80 text-slate-400 font-mono border-b border-slate-800/80">
            <tr>
              <th class="p-4">Course</th>
              <th class="p-4">Package ZIP File</th>
              <th class="p-4">Bundle Size</th>
              <th class="p-4">Downloads</th>
              <th class="p-4">Security</th>
              <th class="p-4">Status</th>
              <th class="p-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/60">
            <tr v-for="pkg in offlinePackages" :key="pkg.id" class="hover:bg-slate-850/50 transition">
              <td class="p-4 font-semibold text-white khmer-text-clean">{{ pkg.course }}</td>
              <td class="p-4 font-mono text-sky-300 font-medium">{{ pkg.package_name }}</td>
              <td class="p-4 font-mono text-slate-400">{{ pkg.size_formatted }}</td>
              <td class="p-4 font-mono text-indigo-300 font-bold flex items-center gap-1">
                <Download class="w-3.5 h-3.5 text-indigo-400" />
                <span>{{ pkg.download_count }}</span>
              </td>
              <td class="p-4">
                <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 font-mono text-[10px] flex items-center gap-1.5 w-fit">
                  <Lock class="w-3 h-3 text-emerald-400" /> AES-256 Encrypted
                </span>
              </td>
              <td class="p-4">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border bg-emerald-500/10 text-emerald-300 border-emerald-500/20 flex items-center gap-1 w-fit">
                  <CheckCircle2 class="w-3 h-3 text-emerald-400" /> {{ pkg.status }}
                </span>
              </td>
              <td class="p-4 text-right">
                <button 
                  @click="$emit('openOfflineModal', pkg)" 
                  class="px-3 py-1.5 bg-slate-800/80 hover:bg-slate-700/80 text-indigo-300 hover:text-white rounded-xl text-xs font-medium border border-slate-700/60 transition cursor-pointer flex items-center gap-1.5 ml-auto"
                >
                  <RefreshCw class="w-3.5 h-3.5 text-indigo-400" />
                  <span>Re-build Bundle</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>
