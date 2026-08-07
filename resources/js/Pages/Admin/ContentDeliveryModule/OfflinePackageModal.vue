<script setup lang="ts">
import { ref, watch } from 'vue'
import { DownloadCloud, X, Lock, HardDrive, RefreshCw, CheckCircle2 } from 'lucide-vue-next'

const props = defineProps<{
  show: boolean
  packageItem?: any
}>()

const emit = defineEmits(['close', 'saved'])

const courseTitle = ref('C Programming Basics')
const packageName = ref('C_Basics_Full_Offline_v1.zip')
const encryptZip = ref(true)
const allowPwaCache = ref(true)
const pwaQuota = ref('2.0 GB')

watch(() => props.packageItem, (newVal) => {
  if (newVal) {
    courseTitle.value = newVal.course || 'C Programming Basics'
    packageName.value = newVal.package_name || 'C_Basics_Full_Offline_v1.zip'
  }
}, { immediate: true })

const handleBuild = () => {
  emit('saved', {
    course: courseTitle.value,
    package_name: packageName.value
  })
  emit('close')
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 font-khmer">
    <div class="bg-slate-900/95 rounded-3xl shadow-2xl border border-slate-800/80 w-full max-w-lg overflow-hidden my-6 transform transition-all text-slate-100">
      
      <!-- Soft Glass Header -->
      <div class="bg-slate-950/90 px-6 py-4 text-white flex items-center justify-between border-b border-slate-800/80">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0">
            <DownloadCloud class="w-5 h-5" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-white flex items-center gap-2 khmer-title-clean">
              <span>CREATE OFFLINE ZIP PACKAGE</span>
            </h3>
            <p class="text-xs text-slate-400 mt-0.5 font-sans">Bundle videos, PDFs & slides for student PWA offline download</p>
          </div>
        </div>
        <button @click="$emit('close')" class="w-8 h-8 rounded-xl bg-slate-800/60 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition cursor-pointer">
          <X class="w-4 h-4" />
        </button>
      </div>

      <!-- Modal Body -->
      <div class="p-6 space-y-4 text-xs text-slate-200 max-h-[75vh] overflow-y-auto">
        <div class="space-y-1.5">
          <label class="block font-medium text-slate-300 text-xs">Target Course Bundle:</label>
          <input 
            v-model="courseTitle" 
            type="text" 
            class="w-full px-3.5 py-2.5 bg-slate-950/80 border border-slate-800/80 rounded-xl text-white font-medium text-xs focus:outline-none focus:border-indigo-500/60 khmer-text-clean transition" 
          />
        </div>

        <div class="space-y-1.5">
          <label class="block font-medium text-slate-300 text-xs">Package File Name:</label>
          <input 
            v-model="packageName" 
            type="text" 
            class="w-full px-3.5 py-2.5 bg-slate-950/80 border border-slate-800/80 rounded-xl text-sky-300 font-mono font-medium text-xs focus:outline-none focus:border-indigo-500/60 transition" 
          />
        </div>

        <div class="bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80 space-y-3">
          <h4 class="font-bold text-indigo-300 text-xs font-mono uppercase border-b border-slate-800/80 pb-2 flex items-center gap-1.5">
            <Lock class="w-3.5 h-3.5 text-indigo-400" />
            <span>Encryption & PWA Cache Rules</span>
          </h4>
          <label class="flex items-center gap-2.5 cursor-pointer font-medium text-slate-300 text-xs">
            <input type="checkbox" v-model="encryptZip" class="rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-indigo-500" />
            <span>Encrypt ZIP Archive with AES-256 (Student Device Key)</span>
          </label>
          <label class="flex items-center gap-2.5 cursor-pointer font-medium text-slate-300 text-xs">
            <input type="checkbox" v-model="allowPwaCache" class="rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-indigo-500" />
            <span>Enable PWA ServiceWorker Local Cache</span>
          </label>
        </div>
      </div>

      <!-- Action Footer -->
      <div class="bg-slate-950/80 px-6 py-4 border-t border-slate-800/80 flex items-center justify-between">
        <button @click="$emit('close')" class="px-4 py-2.5 bg-slate-800/80 hover:bg-slate-700/80 text-slate-300 rounded-2xl text-xs font-medium border border-slate-700/60 transition cursor-pointer">
          Cancel
        </button>
        <button @click="handleBuild" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-2xl shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5 text-xs cursor-pointer">
          <RefreshCw class="w-4 h-4" />
          <span>Build ZIP Package</span>
        </button>
      </div>

    </div>
  </div>
</template>
