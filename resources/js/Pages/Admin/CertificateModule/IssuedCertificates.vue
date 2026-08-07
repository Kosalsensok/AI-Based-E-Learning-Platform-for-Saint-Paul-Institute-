<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import dayjs from 'dayjs'

const props = defineProps<{
  certificates: Array<any>
}>()

const searchQuery = ref('')
const selectedMajorFilter = ref('All')
const selectedStatusFilter = ref('All')
const showDetailDrawer = ref(false)
const activeCertDetail = ref<any>(null)

const filteredCertificates = computed(() => {
  return props.certificates.filter(c => {
    const certNum = c.certificate_number || ''
    const studentName = c.student?.name || ''
    const courseTitle = c.course?.title || ''
    const matchSearch = certNum.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        studentName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        courseTitle.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchMajor = selectedMajorFilter.value === 'All' || c.major?.name === selectedMajorFilter.value
    const matchStatus = selectedStatusFilter.value === 'All' || c.status === selectedStatusFilter.value

    return matchSearch && matchMajor && matchStatus
  })
})

const openCertDetail = (cert: any) => {
  activeCertDetail.value = cert
  showDetailDrawer.value = true
}
</script>

<template>
  <AdminLayout title="Certificate Module — Issued Certificates">
    <div class="space-y-6">
      <!-- 🏅 Header Title -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl">
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            📜 Issued Certificates
            <span class="text-xs bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 px-2.5 py-0.5 rounded-full font-mono">
              Certificate Module
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            បញ្ជីវិញ្ញាបនបត្រទាំងអស់ដែលបានចេញ — Search, Download PDF, Send Email, ឬ Revoke។
          </p>
        </div>

        <Link
          href="/admin/certificates/issue"
          class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center gap-1.5 self-start md:self-auto"
        >
          🏅 Issue New Certificate
        </Link>
      </div>

      <!-- Submenu Tabs Header -->
      <div class="flex items-center gap-1 border-b border-slate-800 overflow-x-auto pb-1 custom-scrollbar">
        <Link href="/admin/certificates/templates" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🎨 Templates
        </Link>
        <Link href="/admin/certificates/issue" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🏅 Issue Certificate
        </Link>
        <Link href="/admin/certificates/issued" class="px-4 py-2.5 text-xs rounded-xl border bg-indigo-500/15 text-indigo-300 border-indigo-500/40 font-semibold shadow-sm shrink-0">
          📜 Issued Certificates
        </Link>
        <Link href="/admin/certificates/verify" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🔍 Certificate Verification
        </Link>
        <Link href="/admin/certificates/revoked" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🚫 Revoked Certificates
        </Link>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">🏅 Total Issued</span>
          <p class="text-2xl font-extrabold text-white">1,352</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">📅 This Month</span>
          <p class="text-2xl font-extrabold text-emerald-400">245 Issued</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">📥 Downloads</span>
          <p class="text-2xl font-extrabold text-cyan-400">3,240 Times</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">🔍 Verified</span>
          <p class="text-2xl font-extrabold text-indigo-400">890 Times</p>
        </div>
      </div>

      <!-- Search & Filter Bar -->
      <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl flex flex-col md:flex-row gap-3 items-center justify-between">
        <div class="relative w-full md:w-80">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search cert ID, student, course..."
            class="w-full pl-9 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500"
          />
          <span class="absolute left-3 top-2.5 text-slate-500 text-xs">🔍</span>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
          <select v-model="selectedMajorFilter" class="bg-slate-950 border border-slate-800 text-slate-300 text-xs rounded-xl px-3 py-2">
            <option value="All">Major: All</option>
            <option value="IT & Networking">IT & Networking</option>
            <option value="Agronomy">Agronomy</option>
            <option value="Social Work">Social Work</option>
          </select>

          <select v-model="selectedStatusFilter" class="bg-slate-950 border border-slate-800 text-slate-300 text-xs rounded-xl px-3 py-2">
            <option value="All">Status: All</option>
            <option value="valid">🟢 Valid</option>
            <option value="revoked">🔴 Revoked</option>
          </select>

          <button class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700 transition">
            📤 Export
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold border-b border-slate-800">
              <tr>
                <th class="p-3.5">Certificate ID</th>
                <th class="p-3.5">Student</th>
                <th class="p-3.5">Course</th>
                <th class="p-3.5">Grade</th>
                <th class="p-3.5">Issued Date</th>
                <th class="p-3.5">Status</th>
                <th class="p-3.5 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
              <tr v-for="c in filteredCertificates" :key="c.id" class="hover:bg-slate-800/40">
                <td class="p-3.5 font-mono text-indigo-400 font-bold">{{ c.certificate_number }}</td>
                <td class="p-3.5 font-semibold text-white">
                  {{ c.student?.name }}
                  <span class="block text-[10px] text-slate-400 font-mono">{{ c.student_id_code || 'STU24001' }}</span>
                </td>
                <td class="p-3.5 text-slate-300">{{ c.course?.title }}</td>
                <td class="p-3.5 font-bold text-emerald-400">{{ c.grade }} ({{ c.score }}%)</td>
                <td class="p-3.5 text-slate-400">{{ dayjs(c.issued_at).format('DD MMM YYYY') }}</td>
                <td class="p-3.5">
                  <span :class="c.status === 'valid' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-red-500/20 text-red-400 border-red-500/30'" class="px-2.5 py-0.5 text-[10px] font-semibold border rounded-full">
                    {{ c.status === 'valid' ? '🟢 Valid' : '🔴 Revoked' }}
                  </span>
                </td>
                <td class="p-3.5 text-right space-x-1">
                  <button @click="openCertDetail(c)" class="p-1 text-slate-400 hover:text-white" title="View Detail">👁</button>
                  <a :href="`/certificate/download/${c.id}`" target="_blank" class="p-1 text-slate-400 hover:text-cyan-300" title="Download PDF">📥</a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Certificate Detail Drawer -->
    <div v-if="showDetailDrawer" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex justify-end">
      <div class="w-full max-w-xl bg-slate-900 border-l border-slate-800 h-full p-6 space-y-6 overflow-y-auto">
        <div class="flex justify-between items-center border-b border-slate-800 pb-4">
          <h3 class="font-bold text-base text-white">🏅 CERTIFICATE DETAIL</h3>
          <button @click="showDetailDrawer = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div v-if="activeCertDetail" class="space-y-4 text-xs">
          <!-- Document Mockup -->
          <div class="bg-slate-950 p-6 rounded-xl border border-slate-800 text-center space-y-3 font-serif text-slate-200">
            <p class="text-[10px] text-indigo-400 tracking-widest font-sans uppercase">Certificate of Completion</p>
            <h4 class="text-xl font-bold text-white font-sans">{{ activeCertDetail.student?.name }}</h4>
            <p class="text-xs text-slate-400 font-sans">{{ activeCertDetail.course?.title }}</p>
            <p class="text-xs font-mono text-cyan-400 font-sans">{{ activeCertDetail.certificate_number }}</p>
          </div>

          <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2 text-slate-300">
            <p><strong>Certificate ID:</strong> <code class="text-cyan-400 font-mono">{{ activeCertDetail.certificate_number }}</code></p>
            <p><strong>Student:</strong> <span>{{ activeCertDetail.student?.name }}</span></p>
            <p><strong>Course:</strong> <span>{{ activeCertDetail.course?.title }}</span></p>
            <p><strong>Grade & Score:</strong> <span class="text-emerald-400 font-bold">{{ activeCertDetail.grade }} ({{ activeCertDetail.score }}%)</span></p>
            <p><strong>Status:</strong> <span :class="activeCertDetail.status === 'valid' ? 'text-emerald-400' : 'text-red-400'" class="font-bold uppercase">{{ activeCertDetail.status }}</span></p>
            <p><strong>Times Verified:</strong> <span>{{ activeCertDetail.verifications_count || 12 }} times</span></p>
            <p><strong>Downloads:</strong> <span>{{ activeCertDetail.downloads_count || 4 }} downloads</span></p>
          </div>

          <div class="flex gap-2">
            <a :href="`/certificate/download/${activeCertDetail.id}`" target="_blank" class="flex-1 py-2 bg-indigo-600 text-white font-semibold rounded-xl text-center">
              📥 Download PDF
            </a>
            <button @click="showDetailDrawer = false" class="py-2 px-4 bg-slate-800 text-slate-300 font-semibold rounded-xl">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
