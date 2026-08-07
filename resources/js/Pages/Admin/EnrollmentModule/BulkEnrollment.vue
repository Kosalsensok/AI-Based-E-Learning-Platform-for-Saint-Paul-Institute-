<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import EnrollmentModuleHeader from '@/Components/Admin/EnrollmentModuleHeader.vue'

const isFileUploaded = ref(false)
const uploadedFileName = ref('')
const isImporting = ref(false)
const progressPercent = ref(0)
const paymentHandling = ref('pending')
const scholarshipCode = ref('')

const bulkData = ref([
  { id: 1, name: 'Chan Dara', email: 'dara@elms.edu', major: 'IT & Networking', courses: 'CP101, WD101, EG101', valid: true, note: 'Valid & Ready' },
  { id: 2, name: 'Sok Chanra', email: 'chanra@elms.edu', major: 'Tourism Management', courses: 'TB101, HM101', valid: true, note: 'Valid & Ready' },
  { id: 3, name: 'Unknown X', email: 'dara@elms.edu', major: 'IT & Networking', courses: 'CP101', valid: false, note: 'Duplicate email' },
  { id: 4, name: 'Mao Srey', email: 'mao@elms.edu', major: 'INVALID_CODE', courses: 'SW101', valid: false, note: 'Invalid Major Code' },
  { id: 5, name: 'Long Vichida', email: 'vichida@elms.edu', major: 'English Literature', courses: 'EG101, EW101', valid: true, note: 'Valid & Ready' },
])

const handleFileUpload = (e: any) => {
  if (e.target.files && e.target.files.length > 0) {
    uploadedFileName.value = e.target.files[0].name
    isFileUploaded.value = true
  }
}

const triggerFileSelect = () => {
  const fileInput = document.getElementById('bulkfile')
  if (fileInput) fileInput.click()
}

const startBulkImport = () => {
  isImporting.value = true
  progressPercent.value = 0
  const interval = setInterval(() => {
    if (progressPercent.value < 100) {
      progressPercent.value += 20
    } else {
      clearInterval(interval)
      alert('Bulk import completed! 148 valid records processed.')
      isImporting.value = false
    }
  }, 350)
}
</script>

<template>
  <AdminLayout title="Bulk Enrollment — Enrollment Management">
    <div class="space-y-6 font-sans">
      <EnrollmentModuleHeader activeTab="bulk" />

      <!-- TOP BANNER -->
      <div class="bg-gradient-to-r from-emerald-950/40 via-slate-900 to-sky-950/40 p-5 rounded-3xl border border-emerald-500/30 flex items-center justify-between backdrop-blur-xl shadow-xl">
        <div class="flex items-center gap-3.5">
          <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-500/20 to-sky-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-300 text-2xl font-bold shadow-lg shadow-emerald-500/10">
            📦
          </div>
          <div>
            <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-2">
              <span>BULK ENROLLMENT (ចុះឈ្មោះសិស្សច្រើននាក់)</span>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">Mass Admission</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
              ចុះឈ្មោះសិស្សច្រើននាក់ម្តង តាមរយៈ CSV / Excel Upload សម្រាប់ដំណាក់កាល Admission ធំៗ
            </p>
          </div>
        </div>
      </div>

      <!-- STEP 1 & STEP 2 GRID (SUPER CLEAN & BEAUTIFUL UI) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        <!-- STEP 1: DOWNLOAD TEMPLATES -->
        <div class="p-6 bg-slate-900/80 border border-slate-800 rounded-3xl backdrop-blur-xl space-y-4 shadow-xl hover:border-emerald-500/30 transition-all flex flex-col justify-between">
          <div class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
              <div class="font-bold text-white text-xs uppercase tracking-wider flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 flex items-center justify-center text-xs font-mono font-bold">1</span>
                <span>STEP 1 — DOWNLOAD TEMPLATE FILE</span>
              </div>
              <span class="text-[10px] text-emerald-400 font-mono font-bold bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">Official Format</span>
            </div>

            <p class="text-xs text-slate-400">
              ទាញយកទម្រង់ CSV ឬ Excel គំរូសម្រាប់បំពេញទិន្នន័យសិស្ស និង Course Codes ឲ្យត្រូវតាមស្តង់ដារប្រព័ន្ធ៖
            </p>

            <div class="grid grid-cols-2 gap-3 pt-1">
              <button class="group p-3.5 bg-slate-950 hover:bg-slate-900 border border-slate-800 hover:border-emerald-500/40 rounded-2xl text-xs font-bold text-slate-200 transition-all text-left flex items-center gap-3 shadow-md">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                  📄
                </div>
                <div>
                  <div class="text-white font-bold text-xs">CSV Template</div>
                  <div class="text-[10px] text-slate-500 font-mono">.csv format</div>
                </div>
              </button>

              <button class="group p-3.5 bg-slate-950 hover:bg-slate-900 border border-slate-800 hover:border-sky-500/40 rounded-2xl text-xs font-bold text-slate-200 transition-all text-left flex items-center gap-3 shadow-md">
                <div class="w-9 h-9 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                  📊
                </div>
                <div>
                  <div class="text-white font-bold text-xs">Excel Template</div>
                  <div class="text-[10px] text-slate-500 font-mono">.xlsx format</div>
                </div>
              </button>
            </div>
          </div>

          <!-- COLUMNS TAGS PREVIEW -->
          <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800/80 space-y-1.5 mt-2">
            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider flex items-center justify-between">
              <span>Required Header Columns:</span>
              <span class="text-emerald-400 font-mono">6 Columns</span>
            </div>
            <div class="flex flex-wrap gap-1.5 font-mono text-[10px]">
              <span class="px-2 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-300">Name</span>
              <span class="px-2 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-300">Email</span>
              <span class="px-2 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-300">Phone</span>
              <span class="px-2 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-300">Major Code</span>
              <span class="px-2 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-300">Course Codes</span>
              <span class="px-2 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-300">Semester</span>
            </div>
          </div>
        </div>

        <!-- STEP 2: UPLOAD DROPZONE -->
        <div class="p-6 bg-slate-900/80 border border-slate-800 rounded-3xl backdrop-blur-xl space-y-4 shadow-xl hover:border-sky-500/30 transition-all flex flex-col justify-between">
          <div class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
              <div class="font-bold text-white text-xs uppercase tracking-wider flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-500/20 border border-sky-500/30 text-sky-300 flex items-center justify-center text-xs font-mono font-bold">2</span>
                <span>STEP 2 — UPLOAD DATA FILE</span>
              </div>
              <span class="text-[10px] text-sky-400 font-mono font-bold bg-sky-500/10 px-2 py-0.5 rounded-full border border-sky-500/20">CSV / XLSX</span>
            </div>

            <!-- GLOWING DASHED DROPZONE OR FILE PREVIEW STATE -->
            <div
              v-if="!isFileUploaded"
              @click="triggerFileSelect"
              class="relative p-6 bg-slate-950/90 border-2 border-dashed border-sky-500/40 hover:border-sky-400 rounded-2xl text-center space-y-3 cursor-pointer group hover:bg-sky-950/20 transition-all shadow-inner"
            >
              <input type="file" @change="handleFileUpload" id="bulkfile" class="hidden" accept=".csv,.xlsx" />

              <div class="w-12 h-12 rounded-2xl bg-sky-500/10 border border-sky-500/30 text-sky-300 flex items-center justify-center text-2xl mx-auto group-hover:scale-110 transition-transform shadow-lg shadow-sky-500/10">
                <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
              </div>

              <div>
                <div class="text-xs font-bold text-white group-hover:text-sky-300 transition-colors">
                  Click to Browse or Drag & Drop File Here
                </div>
                <div class="text-[10px] text-slate-400 font-sans mt-0.5">Supports .CSV, .XLSX (Max file size: 10MB)</div>
              </div>
            </div>

            <!-- FILE PREVIEW CARD STATE (NAME + SIZE + CHECKMARK + REMOVE X) -->
            <div v-else class="p-4 bg-emerald-950/40 border border-emerald-500/40 rounded-2xl space-y-3 shadow-xl backdrop-blur-xl animate-fade-in">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 flex items-center justify-center font-bold font-sans text-xs">
                    XLSX
                  </div>
                  <div>
                    <div class="text-xs font-bold text-white flex items-center gap-1.5">
                      <span>{{ uploadedFileName || 'students_list.xlsx' }}</span>
                      <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                    <div class="text-[11px] text-emerald-300 font-sans mt-0.5">2.4 MB • 150 student rows ready for import</div>
                  </div>
                </div>

                <button
                  @click="isFileUploaded = false; uploadedFileName = ''"
                  class="p-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-red-400 border border-slate-700 transition-all text-xs font-bold"
                  title="Remove File"
                >
                  ✕ Remove
                </button>
              </div>

              <div class="w-full bg-slate-950 rounded-full h-1.5 overflow-hidden border border-slate-800">
                <div class="bg-emerald-500 h-full w-full rounded-full"></div>
              </div>
            </div>
          </div>

          <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800/80 flex items-center justify-between text-[11px]">
            <span class="text-slate-300">Automatic validation before import</span>
            <span class="text-emerald-300 font-bold font-sans inline-flex items-center gap-1">
              <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              100% Secure Upload
            </span>
          </div>
        </div>
      </div>

      <!-- STEP 3: PREVIEW & VALIDATION TABLE (SHOWS WHEN FILE IS UPLOADED) -->
      <div v-if="isFileUploaded" class="space-y-4 pt-2">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-lg bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 flex items-center justify-center text-xs font-mono font-bold">3</span>
            <h3 class="text-xs font-bold text-white uppercase tracking-wider">STEP 3 — PREVIEW & VALIDATE IMPORT DATA</h3>
          </div>
          <div class="flex items-center gap-2 text-xs font-mono">
            <span class="px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">✅ Valid: 148 Records</span>
            <span class="px-3 py-1 rounded-xl bg-red-500/20 text-red-300 border border-red-500/30 font-bold">❌ Error: 2 Records</span>
          </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-xl shadow-xl">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="border-b border-slate-800 bg-slate-800/40 text-[11px] font-bold text-slate-400 uppercase whitespace-nowrap">
                <th class="py-3.5 px-4 w-12">#</th>
                <th class="py-3.5 px-4">Student Name</th>
                <th class="py-3.5 px-4">Email</th>
                <th class="py-3.5 px-4">Target Major</th>
                <th class="py-3.5 px-4">Courses</th>
                <th class="py-3.5 px-4 text-center">Validation Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 font-sans">
              <tr v-for="(row, idx) in bulkData" :key="row.id" class="hover:bg-slate-800/30 transition-colors">
                <td class="py-3.5 px-4 font-mono text-slate-500">{{ String(idx + 1).padStart(2, '0') }}</td>
                <td class="py-3.5 px-4 font-bold text-white text-sm">{{ row.name }}</td>
                <td class="py-3.5 px-4 font-mono text-slate-300">{{ row.email }}</td>
                <td class="py-3.5 px-4 text-slate-200 font-medium">{{ row.major }}</td>
                <td class="py-3.5 px-4 font-mono text-sky-300">{{ row.courses }}</td>
                <td class="py-3.5 px-4 text-center">
                  <span v-if="row.valid" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    ✅ {{ row.note }}
                  </span>
                  <span v-else class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-500/20 text-red-300 border border-red-500/30">
                    ❌ {{ row.note }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- PAYMENT HANDLING & CONFIRMATION -->
        <div class="p-5 bg-slate-900/90 rounded-3xl border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4 backdrop-blur-xl text-xs shadow-xl">
          <div class="flex items-center gap-3 w-full md:w-auto">
            <label class="text-slate-300 font-bold whitespace-nowrap">Bulk Payment Handling:</label>
            <select v-model="paymentHandling" class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white font-medium">
              <option value="pending">Mark as Pending Payment</option>
              <option value="qr">Generate ABA QR per Student</option>
              <option value="scholarship">Apply Scholarship Code</option>
            </select>
          </div>

          <button @click="startBulkImport" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-sky-600 hover:from-emerald-500 hover:to-sky-500 text-white rounded-xl font-bold text-xs shadow-lg shadow-emerald-600/30 whitespace-nowrap transition-all">
            🚀 Confirm Import (148 Valid Records)
          </button>
        </div>

        <!-- IMPORT PROGRESS BAR -->
        <div v-if="isImporting" class="p-5 bg-slate-950 rounded-3xl border border-sky-500/40 space-y-2 shadow-2xl">
          <div class="flex justify-between text-xs font-mono text-sky-300 font-bold">
            <span>Processing Bulk Enrollments...</span>
            <span>{{ progressPercent }}%</span>
          </div>
          <div class="w-full bg-slate-900 rounded-full h-2.5 overflow-hidden border border-slate-800">
            <div class="bg-gradient-to-r from-emerald-500 to-sky-500 h-full rounded-full transition-all duration-300" :style="{ width: progressPercent + '%' }"></div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
