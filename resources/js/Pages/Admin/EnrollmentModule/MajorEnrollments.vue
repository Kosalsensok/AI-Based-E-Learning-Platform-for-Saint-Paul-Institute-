<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import EnrollmentModuleHeader from '@/Components/Admin/EnrollmentModuleHeader.vue'

const props = withDefaults(defineProps<{
  students?: any[]
  summaryStats?: any
}>(), {
  students: () => [],
  summaryStats: () => ({})
})

const searchQuery = ref('')
const selectedMajor = ref('all')
const selectedYear = ref('2024-2025')
const selectedStatus = ref('all')

const isEnrollModalOpen = ref(false)
const isTransferModalOpen = ref(false)
const isViewModalOpen = ref(false)

const selectedStudent = ref<any>(null)

const enrollForm = ref({
  student_name: '',
  student_id: '',
  email: '',
  faculty: 'Computing',
  department: 'Computing',
  major: 'IT & Networking',
  academic_year: '2024-2025',
  status: 'active'
})

const transferForm = ref({
  new_major: 'Tourism Management',
  reason: 'Student requested change of academic interest',
  effective_date: 'Next Semester Start',
  transfer_credits: true,
})

const majorsCards = [
  { id: 'it', name: 'IT & Networking', faculty: 'Computing', icon: '💻', count: 520, capacity: 600, percent: 85, color: 'sky' },
  { id: 'tm', name: 'Tourism Management', faculty: 'Tourism', icon: '🏢', count: 410, capacity: 600, percent: 70, color: 'indigo' },
  { id: 'eng', name: 'English Literature', faculty: 'Education', icon: '📖', count: 380, capacity: 600, percent: 65, color: 'purple' },
  { id: 'ag', name: 'Agronomy', faculty: 'Agriculture', icon: '🌱', count: 600, capacity: 650, percent: 90, color: 'emerald' },
  { id: 'sw', name: 'Social Work', faculty: 'Social Science', icon: '🤝', count: 548, capacity: 700, percent: 80, color: 'amber' },
]

const studentsList = computed(() => {
  return Array.isArray(props.students) && props.students.length > 0 ? props.students : [
    { id: 1, student_id: 'STU24001', name: 'Chan Dara', email: 'dara@elms.edu', major: 'IT & Networking', enrolled_date: '01 Sep 2024', status: 'active', courses_count: 3 },
    { id: 2, student_id: 'STU24002', name: 'Sok Chanra', email: 'chanra@elms.edu', major: 'Tourism Management', enrolled_date: '01 Sep 2024', status: 'active', courses_count: 2 },
    { id: 3, student_id: 'STU24003', name: 'Long Vichida', email: 'vichida@elms.edu', major: 'English Literature', enrolled_date: '01 Sep 2024', status: 'active', courses_count: 4 },
    { id: 4, student_id: 'STU24004', name: 'Pov Sreynich', email: 'sreynich@elms.edu', major: 'Agronomy', enrolled_date: '01 Sep 2024', status: 'active', courses_count: 2 },
    { id: 5, student_id: 'STU24005', name: 'Mao Sreynich', email: 'mao@elms.edu', major: 'Social Work', enrolled_date: '01 Sep 2024', status: 'pending', courses_count: 1 },
  ]
})

const filteredStudents = computed(() => {
  return studentsList.value.filter(stu => {
    const matchesSearch = stu.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          stu.student_id.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          stu.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesMajor = selectedMajor.value === 'all' || stu.major === selectedMajor.value
    const matchesStatus = selectedStatus.value === 'all' || stu.status === selectedStatus.value
    return matchesSearch && matchesMajor && matchesStatus
  })
})

const openEnrollModal = () => {
  enrollForm.value = {
    student_name: '',
    student_id: `STU${Math.floor(10000 + Math.random() * 90000)}`,
    email: '',
    faculty: 'Computing',
    department: 'Computing',
    major: 'IT & Networking',
    academic_year: '2024-2025',
    status: 'active'
  }
  isEnrollModalOpen.value = true
}

const openTransferModal = (stu: any) => {
  selectedStudent.value = stu
  transferForm.value = {
    new_major: stu.major === 'IT & Networking' ? 'Tourism Management' : 'IT & Networking',
    reason: 'Student requested change of academic interest',
    effective_date: 'Next Semester Start',
    transfer_credits: true,
  }
  isTransferModalOpen.value = true
}

const openViewModal = (stu: any) => {
  selectedStudent.value = stu
  isViewModalOpen.value = true
}

const toast = ref<{ show: boolean; type: 'success' | 'info' | 'warning'; title: string; message: string }>({
  show: false,
  type: 'success',
  title: '',
  message: ''
})
let toastTimer: any = null

const triggerToast = (title: string, message: string, type: 'success' | 'info' | 'warning' = 'success') => {
  if (toastTimer) clearTimeout(toastTimer)
  toast.value = { show: true, type, title, message }
  toastTimer = setTimeout(() => {
    toast.value.show = false
  }, 4500)
}

const isSubmitting = ref(false)

const saveMajorEnrollment = () => {
  if (!enrollForm.value.student_name) {
    triggerToast('សូមបញ្ចូលឈ្មោះសិស្ស', 'សូមបញ្ចូល Student Full Name មុនពេលចុះឈ្មោះ', 'warning')
    return
  }

  isSubmitting.value = true
  const stuName = enrollForm.value.student_name || 'សិស្ស'
  const targetMajor = enrollForm.value.major || 'ជំនាញ'

  router.post('/admin/enrollment/majors/store', enrollForm.value, {
    preserveScroll: true,
    onSuccess: () => {
      isSubmitting.value = false
      isEnrollModalOpen.value = false
      triggerToast(
        'ចុះឈ្មោះសិស្សចូលរៀនជំនាញជោគជ័យ 🎉',
        `សិស្ស "${stuName}" ត្រូវបានចុះឈ្មោះចូលរៀនជំនាញ "${targetMajor}" ដោយជោគជ័យ`
      )
    },
    onError: () => {
      isSubmitting.value = false
      triggerToast('មានបញ្ហាក្នុងការចុះឈ្មោះ', 'សូមពិនិត្យមើលព័ត៌មានដែលបានបញ្ចូលឡើងវិញ', 'warning')
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}

const submitTransfer = () => {
  isSubmitting.value = true
  const stuName = selectedStudent.value?.name || 'សិស្ស'
  const newMajor = transferForm.value.new_major || 'ជំនាញថ្មី'

  if (selectedStudent.value?.id) {
    router.put(`/admin/enrollment/majors/transfer/${selectedStudent.value.id}`, transferForm.value, {
      preserveScroll: true,
      onSuccess: () => {
        isSubmitting.value = false
        isTransferModalOpen.value = false
        triggerToast('ផ្លាស់ប្តូរជំនាញជោគជ័យ', `សិស្ស "${stuName}" ត្រូវបានផ្លាស់ប្តូរទៅជំនាញ "${newMajor}" ដោយជោគជ័យ`)
      },
      onError: () => {
        isSubmitting.value = false
        triggerToast('មានបញ្ហាក្នុងការប្តូរជំនាញ', 'សូមពិនិត្យមើលព័ត៌មានឡើងវិញ', 'warning')
      },
      onFinish: () => {
        isSubmitting.value = false
      }
    })
  } else {
    isSubmitting.value = false
    isTransferModalOpen.value = false
  }
}

const withdrawStudent = (stu: any) => {
  if (confirm(`Withdraw '${stu.name}' (${stu.student_id}) from major '${stu.major}'?`)) {
    router.delete(`/admin/enrollment/majors/withdraw/${stu.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        triggerToast('ដកឈ្មោះសិស្សជោគជ័យ', `សិស្ស "${stu.name}" ត្រូវបានដកចេញពីជំនាញ "${stu.major}"`)
      },
      onError: () => {
        triggerToast('មានបញ្ហាក្នុងការដកឈ្មោះ', 'មិនអាចដកឈ្មោះសិស្សបានទេ', 'warning')
      }
    })
  }
}
</script>

<template>
  <AdminLayout title="Major Enrollments — Enrollment Management">
    <div class="space-y-6 font-sans">
      <EnrollmentModuleHeader activeTab="majors" :summaryStats="props.summaryStats" />

      <!-- VISUAL CARDS GRID (ENROLLMENT BY MAJOR) -->
      <div class="space-y-2.5">
        <div class="flex items-center justify-between text-xs">
          <h2 class="font-bold text-white uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span>ENROLLMENT BY MAJOR (5 MAJORS ADMISSION)</span>
          </h2>
          <span class="text-slate-300 font-mono">Total Enrolled: <strong class="text-emerald-300 font-bold">2,458 Students</strong></span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
          <div v-for="card in majorsCards" :key="card.id" class="p-4 bg-slate-900/60 border border-slate-800 rounded-2xl backdrop-blur-xl space-y-2.5 hover:border-emerald-500/40 transition-all">
            <div class="flex items-center justify-between">
              <span class="text-lg">{{ card.icon }}</span>
              <span
                class="text-[10px] font-bold font-mono px-2 py-0.5 rounded-full border"
                :class="card.percent >= 90 ? 'bg-red-500/20 text-red-300 border-red-500/40 animate-pulse' : card.percent >= 80 ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30'"
              >
                {{ card.percent }}% Full {{ card.percent >= 90 ? '⚠️ High' : '' }}
              </span>
            </div>

            <div>
              <div class="text-xs font-bold text-white truncate">{{ card.name }}</div>
              <div class="text-[11px] text-slate-300 font-mono mt-0.5">{{ card.count }} / {{ card.capacity }} Students</div>
            </div>

            <!-- ENHANCED DYNAMIC PROGRESS BAR -->
            <div class="w-full bg-slate-950 rounded-full h-2.5 p-0.5 overflow-hidden border border-slate-800">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="card.percent >= 90 ? 'bg-gradient-to-r from-red-500 to-rose-600' : card.percent >= 80 ? 'bg-gradient-to-r from-amber-500 to-amber-600' : 'bg-gradient-to-r from-emerald-500 to-sky-500'"
                :style="{ width: card.percent + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- FILTER & SEARCH BAR -->
      <div class="p-3.5 bg-slate-900/60 border border-slate-800 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-3 backdrop-blur-xl">
        <div class="flex items-center gap-2.5 w-full md:w-auto">
          <div class="w-full md:w-72 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search student name, ID, email..."
              class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-9 pr-3.5 py-2 text-xs text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500"
            />
          </div>

          <select v-model="selectedMajor" class="bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-200">
            <option value="all">Major: All</option>
            <option value="IT & Networking">IT & Networking</option>
            <option value="Tourism Management">Tourism Management</option>
            <option value="English Literature">English Literature</option>
            <option value="Agronomy">Agronomy</option>
            <option value="Social Work">Social Work</option>
          </select>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto justify-end">
          <button @click="openEnrollModal" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-1.5 whitespace-nowrap">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Enroll Student to Major</span>
          </button>
        </div>
      </div>

      <!-- MAJOR ENROLLMENTS TABLE -->
      <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-xl">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-800 bg-slate-800/40 text-[11px] font-bold text-slate-300 uppercase whitespace-nowrap">
              <th class="py-3.5 px-4 w-12">#</th>
              <th class="py-3.5 px-4">
                <div class="flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <span>Student</span>
                </div>
              </th>
              <th class="py-3.5 px-4">Student ID</th>
              <th class="py-3.5 px-4">Major</th>
              <th class="py-3.5 px-4">Enrolled Date</th>
              <th class="py-3.5 px-4 text-center">Status</th>
              <th class="py-3.5 px-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800 text-xs">
            <tr v-for="(stu, idx) in filteredStudents" :key="stu.id" class="hover:bg-slate-800/30 transition-all">
              <td class="py-4 px-4 font-sans text-slate-300 font-semibold whitespace-nowrap">{{ String(idx + 1).padStart(2, '0') }}</td>

              <td class="py-4 px-4 whitespace-nowrap">
                <div class="font-bold text-white text-sm">{{ stu.name }}</div>
                <div class="text-[11px] text-slate-300 font-sans">{{ stu.email }}</div>
              </td>

              <td class="py-4 px-4 whitespace-nowrap font-sans font-semibold text-sky-300">
                <span class="px-2 py-0.5 bg-slate-950 rounded border border-slate-800">{{ stu.student_id }}</span>
              </td>

              <td class="py-4 px-4 whitespace-nowrap text-slate-200 font-medium">
                {{ stu.major }}
              </td>

              <td class="py-4 px-4 whitespace-nowrap font-sans text-slate-300 font-medium">
                {{ stu.enrolled_date }}
              </td>

              <td class="py-4 px-4 text-center whitespace-nowrap">
                <span v-if="stu.status === 'active'" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 inline-flex items-center gap-1.5 shadow-sm">
                  <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Active</span>
                </span>
                <span v-else class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 inline-flex items-center gap-1.5 shadow-sm">
                  <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Pending</span>
                </span>
              </td>

              <td class="py-4 px-4 text-right whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                  <button @click="openViewModal(stu)" class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl font-bold border border-slate-700 text-xs inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>View</span>
                  </button>
                  <button @click="openTransferModal(stu)" class="px-2.5 py-1.5 bg-sky-600/20 hover:bg-sky-500/30 text-sky-300 rounded-xl font-bold border border-sky-500/30 text-xs inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span>Transfer</span>
                  </button>
                  <button @click="withdrawStudent(stu)" class="px-2.5 py-1.5 bg-red-600/20 hover:bg-red-500/30 text-red-300 rounded-xl font-bold border border-red-500/30 text-xs inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Withdraw</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION CONTROLS -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-3.5 bg-slate-900/60 border border-slate-800 rounded-2xl text-xs text-slate-300 backdrop-blur-xl">
        <div class="flex items-center gap-3">
          <span>Showing <strong class="text-white">1</strong> to <strong class="text-white">{{ filteredStudents.length }}</strong> of <strong class="text-white">{{ filteredStudents.length }}</strong> students</span>
          <div class="flex items-center gap-1.5 ml-2">
            <span>Per page:</span>
            <select class="bg-slate-950 border border-slate-800 rounded-lg px-2 py-1 text-white">
              <option :value="10">10</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
            </select>
          </div>
        </div>

        <div class="flex items-center gap-1.5">
          <button disabled class="px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-950 font-bold transition-all opacity-40 cursor-not-allowed text-slate-400">‹ Prev</button>
          <div class="flex items-center gap-1 px-1">
            <button class="w-8 h-8 rounded-xl font-bold bg-emerald-600 text-white flex items-center justify-center">1</button>
          </div>
          <button disabled class="px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-950 font-bold transition-all opacity-40 cursor-not-allowed text-slate-400">Next ›</button>
        </div>
      </div>

      <!-- ENROLL MAJOR MODAL -->
      <div v-if="isEnrollModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-emerald-900/50 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              <span>🎓 ENROLL STUDENT TO MAJOR</span>
            </h3>
            <button @click="isEnrollModalOpen = false" class="text-slate-400 hover:text-white text-lg">✕</button>
          </div>

          <form @submit.prevent="saveMajorEnrollment" class="space-y-3 text-xs">
            <div>
              <label class="block text-slate-400 mb-1">Student Full Name</label>
              <input v-model="enrollForm.student_name" type="text" placeholder="e.g. Chan Dara" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-bold" />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-slate-400 mb-1">Student ID</label>
                <input v-model="enrollForm.student_id" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sky-300 font-mono font-bold" />
              </div>
              <div>
                <label class="block text-slate-400 mb-1">Email Address</label>
                <input v-model="enrollForm.email" type="email" placeholder="student@elms.edu" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-slate-400 mb-1">Target Major</label>
                <select v-model="enrollForm.major" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                  <option value="IT & Networking">IT & Networking</option>
                  <option value="Tourism Management">Tourism Management</option>
                  <option value="English Literature">English Literature</option>
                  <option value="Agronomy">Agronomy</option>
                  <option value="Social Work">Social Work</option>
                </select>
              </div>
              <div>
                <label class="block text-slate-400 mb-1">Academic Year</label>
                <select v-model="enrollForm.academic_year" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
                  <option value="2024-2025">2024 - 2025</option>
                  <option value="2025-2026">2025 - 2026</option>
                </select>
              </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
              <button type="button" @click="isEnrollModalOpen = false" :disabled="isSubmitting" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl font-bold disabled:opacity-50">Cancel</button>
              <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold flex items-center gap-1.5 disabled:opacity-50 cursor-pointer">
                <svg v-if="isSubmitting" class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ isSubmitting ? 'កំពុងចុះឈ្មោះ...' : 'Confirm Enrollment' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- TRANSFER MAJOR MODAL -->
      <div v-if="isTransferModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-sky-900/50 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-sky-400 flex items-center gap-2">
              <span>🔄 TRANSFER MAJOR (ប្តូរជំនាញ)</span>
            </h3>
            <button @click="isTransferModalOpen = false" class="text-slate-400 hover:text-white text-lg">✕</button>
          </div>

          <form @submit.prevent="submitTransfer" class="space-y-3 text-xs">
            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 font-mono space-y-1">
              <div><span class="text-slate-400">Student:</span> <strong class="text-white">{{ selectedStudent?.name }} ({{ selectedStudent?.student_id }})</strong></div>
              <div><span class="text-slate-400">Current Major:</span> <strong class="text-emerald-300">{{ selectedStudent?.major }}</strong></div>
            </div>

            <div>
              <label class="block text-slate-400 mb-1">New Target Major</label>
              <select v-model="transferForm.new_major" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-bold">
                <option value="IT & Networking">IT & Networking</option>
                <option value="Tourism Management">Tourism Management</option>
                <option value="English Literature">English Literature</option>
                <option value="Agronomy">Agronomy</option>
                <option value="Social Work">Social Work</option>
              </select>
            </div>

            <div>
              <label class="block text-slate-400 mb-1">Reason for Transfer</label>
              <input v-model="transferForm.reason" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white" />
            </div>

            <div class="flex items-center gap-2 text-slate-300">
              <input v-model="transferForm.transfer_credits" type="checkbox" id="credits" class="rounded bg-slate-950 border-slate-800" />
              <label for="credits">Keep completed course credits where applicable</label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
              <button type="button" @click="isTransferModalOpen = false" :disabled="isSubmitting" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl font-bold disabled:opacity-50">Cancel</button>
              <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl font-bold flex items-center gap-1.5 disabled:opacity-50 cursor-pointer">
                <svg v-if="isSubmitting" class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ isSubmitting ? 'កំពុងប្តូរជំនាញ...' : 'Confirm Transfer' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- VIEW MODAL -->
      <div v-if="isViewModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white">🎓 MAJOR ADMISSION DETAILS — {{ selectedStudent?.name }}</h3>
            <button @click="isViewModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
          </div>

          <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-2 text-xs font-mono">
            <div><span class="text-slate-400">Student Name:</span> <strong class="text-white">{{ selectedStudent?.name }}</strong></div>
            <div><span class="text-slate-400">Student ID:</span> <strong class="text-sky-300">{{ selectedStudent?.student_id }}</strong></div>
            <div><span class="text-slate-400">Email:</span> <strong class="text-slate-300">{{ selectedStudent?.email }}</strong></div>
            <div><span class="text-slate-400">Major:</span> <strong class="text-emerald-300">{{ selectedStudent?.major }}</strong></div>
            <div><span class="text-slate-400">Enrolled Date:</span> <strong class="text-slate-400">{{ selectedStudent?.enrolled_date }}</strong></div>
            <div><span class="text-slate-400">Enrolled Courses:</span> <strong class="text-amber-300">{{ selectedStudent?.courses_count }} Courses</strong></div>
          </div>

          <button @click="isViewModalOpen = false" class="w-full py-2 bg-slate-800 text-slate-300 font-bold text-xs rounded-xl">Close</button>
        </div>
      </div>

      <!-- Clean & User-Friendly Toast Notification -->
      <Teleport to="body">
        <Transition
          enter-active-class="transform ease-out duration-250 transition"
          enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-3"
          enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div
            v-if="toast.show"
            class="fixed top-5 right-5 z-[9999] max-w-sm w-full pointer-events-auto"
          >
            <div
              :class="[
                toast.type === 'success'
                  ? 'bg-slate-800/95 border-slate-700/80 text-white'
                  : 'bg-slate-800/95 border-amber-500/40 text-white',
                'relative rounded-xl border p-3 shadow-xl backdrop-blur-md'
              ]"
            >
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center filter drop-shadow-sm">
                  <img
                    v-if="toast.type === 'success'"
                    :src="'/images/actions/toast-success.svg'"
                    alt="Success"
                    class="w-full h-full object-contain"
                  />
                  <img
                    v-else-if="toast.type === 'warning'"
                    :src="'/images/actions/toast-warning.svg'"
                    alt="Warning"
                    class="w-full h-full object-contain"
                  />
                  <img
                    v-else
                    :src="'/images/actions/toast-info.svg'"
                    alt="Info"
                    class="w-full h-full object-contain"
                  />
                </div>

                <div class="flex-1 min-w-0">
                  <h4 class="text-xs font-bold text-white tracking-tight leading-snug">
                    {{ toast.title }}
                  </h4>
                  <p class="text-[11px] text-slate-300 mt-0.5 leading-normal">
                    {{ toast.message }}
                  </p>
                </div>

                <button
                  @click="toast.show = false"
                  class="text-slate-400 hover:text-white p-1 rounded-md hover:bg-slate-700/60 transition-colors shrink-0 cursor-pointer"
                >
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </Teleport>
    </div>
  </AdminLayout>
</template>
