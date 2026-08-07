<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import CourseModuleHeader from '@/Components/Admin/CourseModuleHeader.vue'

const props = withDefaults(defineProps<{
  assignments?: any[]
  summaryStats?: any
}>(), {
  assignments: () => [],
  summaryStats: () => ({})
})

const searchQuery = ref('')
const selectedTeacher = ref('all')
const isModalOpen = ref(false)
const editingAssignment = ref<any>(null)

// Bulk selection
const selectedIds = ref<number[]>([])

// Dropdown row actions
const activeDropdownId = ref<number | null>(null)
const toggleDropdown = (id: number) => {
  activeDropdownId.value = activeDropdownId.value === id ? null : id
}
const closeDropdown = () => {
  activeDropdownId.value = null
}

// Pagination
const currentPage = ref(1)
const pageSize = ref(10)

const form = ref({
  teacher: 'Mr. Sophea',
  subject: 'C Programming',
  course: 'C Programming Basics',
  major: 'IT & Networking',
  role: 'Main Teacher',
  mode: '🎥 Teacher-Led',
  schedule: 'Mon/Wed/Fri — 2:00 PM - 4:00 PM',
  price: 25,
  commission: '70% Teacher / 30% School',
  aba: 'Mr. Sophea — ABA 000123456',
  status: 'active'
})

const assignmentsList = computed(() => {
  return Array.isArray(props.assignments) && props.assignments.length > 0 ? props.assignments : [
    { id: 1, teacher: 'Mr. Sophea', subject: 'C Programming', course: 'C Programming Basics', major: 'IT & Networking', mode: '🎥 Teacher-Led', role: 'Main Teacher', schedule: 'Mon/Wed/Fri — 2:00 PM - 4:00 PM', price: 25, commission: '70% Teacher / 30% School', aba: 'Mr. Sophea — ABA 000123456', status: 'active' },
    { id: 2, teacher: 'Mr. Sophea', subject: 'Database Systems', course: 'Database Systems', major: 'IT & Networking', mode: '💻 Self-Study', role: 'Main Author', schedule: 'Flexible Self-Paced', price: 20, commission: '60% Teacher / 40% School', aba: 'Mr. Sophea — ABA 000123456', status: 'active' },
    { id: 3, teacher: 'Ms. Srey', subject: 'English Grammar', course: 'English Grammar Basics', major: 'English Literature', mode: '💻 Self-Study', role: 'Main Teacher', schedule: 'Flexible Self-Paced', price: 0, commission: 'Fixed Monthly Stipend', aba: 'School ABA — 000123456', status: 'active' },
    { id: 4, teacher: 'Mr. Vuthy', subject: 'Plant Science', course: 'Plant Science Advanced', major: 'Agronomy', mode: '🎥 Teacher-Led', role: 'Main Teacher', schedule: 'Tue/Thu — 8:00 AM - 10:00 AM', price: 30, commission: '70% Teacher / 30% School', aba: 'Mr. Vuthy — ABA 000987654', status: 'active' },
    { id: 5, teacher: 'Mr. Rithy', subject: 'Social Work 101', course: 'Social Work 101', major: 'Social Work', mode: '💻 Self-Study', role: 'Main Author', schedule: 'Flexible Self-Paced', price: 15, commission: '50% Teacher / 50% School', aba: 'School ABA — 000123456', status: 'pending' },
  ]
})

const filteredAssignments = computed(() => {
  return assignmentsList.value.filter(asg => {
    const matchesSearch = asg.teacher.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          asg.subject.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesTeacher = selectedTeacher.value === 'all' || asg.teacher === selectedTeacher.value
    return matchesSearch && matchesTeacher
  })
})

watch([searchQuery, selectedTeacher, pageSize], () => {
  currentPage.value = 1
  selectedIds.value = []
})

const totalPages = computed(() => Math.ceil(filteredAssignments.value.length / pageSize.value) || 1)
const paginatedAssignments = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value
  return filteredAssignments.value.slice(start, start + pageSize.value)
})

const isAllSelected = computed({
  get: () => paginatedAssignments.value.length > 0 && paginatedAssignments.value.every(asg => selectedIds.value.includes(asg.id)),
  set: (val: boolean) => {
    if (val) {
      const pageIds = paginatedAssignments.value.map(asg => asg.id)
      selectedIds.value = Array.from(new Set([...selectedIds.value, ...pageIds]))
    } else {
      const pageIds = paginatedAssignments.value.map(asg => asg.id)
      selectedIds.value = selectedIds.value.filter(id => !pageIds.includes(id))
    }
  }
})

const toggleSelectAll = () => {
  isAllSelected.value = !isAllSelected.value
}

const bulkDelete = () => {
  if (confirm(`Remove ${selectedIds.value.length} selected teacher assignments?`)) {
    alert(`Removed ${selectedIds.value.length} assignments.`)
    selectedIds.value = []
  }
}

const openAddModal = () => {
  editingAssignment.value = null
  form.value = {
    teacher: 'Mr. Sophea',
    subject: 'C Programming',
    course: 'C Programming Basics',
    major: 'IT & Networking',
    role: 'Main Teacher',
    mode: '🎥 Teacher-Led',
    schedule: 'Mon/Wed/Fri — 2:00 PM - 4:00 PM',
    price: 25,
    commission: '70% Teacher / 30% School',
    aba: 'Mr. Sophea — ABA 000123456',
    status: 'active'
  }
  isModalOpen.value = true
}

const openEditModal = (asg: any) => {
  closeDropdown()
  editingAssignment.value = asg
  form.value = { ...asg }
  isModalOpen.value = true
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

const saveAssignment = () => {
  isSubmitting.value = true
  const teacherName = form.value.teacher || 'លោកគ្រូ/អ្នកគ្រូ'
  const subjectName = form.value.subject || 'មុខវិជ្ជា'

  if (editingAssignment.value?.id) {
    router.put(`/admin/course-module/assignments/update/${editingAssignment.value.id}`, form.value, {
      preserveScroll: true,
      onSuccess: () => {
        isSubmitting.value = false
        isModalOpen.value = false
        triggerToast('រក្សាទុកបានជោគជ័យ', `ការចាត់តាំងគ្រូ "${teacherName}" លើមុខវិជ្ជា "${subjectName}" ត្រូវបានបច្ចុប្បន្នភាព`)
      },
      onError: () => {
        isSubmitting.value = false
        triggerToast('មានបញ្ហាក្នុងការរក្សាទុក', 'សូមពិនិត្យមើលព័ត៌មានដែលបានបញ្ចូលឡើងវិញ', 'warning')
      },
      onFinish: () => { isSubmitting.value = false }
    })
  } else {
    router.post('/admin/course-module/assignments/store', form.value, {
      preserveScroll: true,
      onSuccess: () => {
        isSubmitting.value = false
        isModalOpen.value = false
        triggerToast('ចាត់តាំងគ្រូបានជោគជ័យ 🎉', `បានចាត់តាំងគ្រូ "${teacherName}" បង្រៀនមុខវិជ្ជា "${subjectName}" ដោយជោគជ័យ`)
      },
      onError: () => {
        isSubmitting.value = false
        triggerToast('មានបញ្ហាក្នុងការចាត់តាំង', 'សូមពិនិត្យមើលព័ត៌មានដែលបានបញ្ចូលឡើងវិញ', 'warning')
      },
      onFinish: () => { isSubmitting.value = false }
    })
  }
}

const deleteAssignment = (asg: any) => {
  closeDropdown()
  if (confirm(`Remove teacher assignment for '${asg.teacher}' on '${asg.subject}'?`)) {
    router.delete(`/admin/course-module/assignments/destroy/${asg.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        triggerToast('លុបការចាត់តាំងជោគជ័យ', `បានដកចេញការចាត់តាំងគ្រូ "${asg.teacher}" លើមុខវិជ្ជា "${asg.subject}"`)
      },
      onError: () => {
        triggerToast('មានបញ្ហាក្នុងការលុប', 'មិនអាចដកចេញការចាត់តាំងនេះបានទេ', 'warning')
      }
    })
  }
}

const viewCommissionDetails = (asg: any) => {
  closeDropdown()
  triggerToast('ព័ត៌មានគណនី និងកម្រៃជើងសារ ABA', `គ្រូ: ${asg.teacher} | គណនី: ${asg.aba} | កម្រៃ: ${asg.commission}`, 'info')
}
</script>

<template>
  <AdminLayout title="Teacher Assignments — Course & Subject Management">
    <div class="space-y-4 font-sans" @click="closeDropdown">
      <!-- Shared Header (1-Layer Clean Header) -->
      <CourseModuleHeader activeTab="teacher-assignments" :summaryStats="props.summaryStats" />

      <!-- CONTROLS BAR -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-3.5 bg-slate-900/60 border border-slate-800 rounded-2xl backdrop-blur-xl">
        <div class="w-full sm:w-80 relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search teacher name or subject..."
            class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-9 pr-3.5 py-2 text-xs text-white placeholder-slate-400 focus:outline-none focus:border-purple-500"
          />
        </div>

        <button @click="openAddModal" class="w-full sm:w-auto px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-purple-600/30 transition-all flex items-center justify-center gap-1.5 whitespace-nowrap">
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Assign Teacher</span>
        </button>
      </div>

      <!-- BULK ACTIONS TOOLBAR -->
      <div v-if="selectedIds.length > 0" class="flex items-center justify-between p-3 bg-purple-950/80 border border-purple-500/40 rounded-2xl text-xs backdrop-blur-xl animate-fade-in">
        <div class="flex items-center gap-2 text-purple-200 font-bold">
          <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ selectedIds.length }} assignment(s) selected</span>
        </div>
        <div class="flex items-center gap-2">
          <button @click="bulkDelete" class="px-3 py-1.5 bg-red-600/20 hover:bg-red-500/30 text-red-300 border border-red-500/30 rounded-xl font-bold flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Bulk Remove
          </button>
          <button @click="selectedIds = []" class="px-2.5 py-1.5 bg-slate-800 text-slate-300 hover:text-white rounded-xl font-bold">
            ✕ Clear
          </button>
        </div>
      </div>

      <!-- ASSIGNMENTS TABLE -->
      <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-xl">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-800 bg-slate-800/40 text-[11px] font-bold text-slate-300 uppercase whitespace-nowrap">
              <th class="py-3 px-3 w-10 text-center">
                <input
                  type="checkbox"
                  :checked="isAllSelected"
                  @change="toggleSelectAll"
                  class="rounded border-slate-700 bg-slate-950 text-purple-500 focus:ring-purple-500/20"
                />
              </th>
              <th class="py-3.5 px-4 w-12">#</th>
              <th class="py-3.5 px-4">
                <div class="flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <span>Teacher Name</span>
                </div>
              </th>
              <th class="py-3.5 px-4">
                <div class="flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                  <span>Subject & Course</span>
                </div>
              </th>
              <th class="py-3.5 px-4">
                <div class="flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span>Schedule</span>
                </div>
              </th>
              <th class="py-3.5 px-4">
                <div class="flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                  <span>ABA & Commission</span>
                </div>
              </th>
              <th class="py-3.5 px-4 text-center">Status</th>
              <th class="py-3.5 px-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800 text-xs">
            <tr v-if="paginatedAssignments.length === 0">
              <td colspan="8" class="py-8 text-center text-slate-300">
                No teacher assignments found.
              </td>
            </tr>
            <tr
              v-for="(asg, idx) in paginatedAssignments"
              :key="asg.id"
              class="hover:bg-slate-800/30 transition-all"
              :class="{ 'bg-purple-950/20': selectedIds.includes(asg.id) }"
            >
              <td class="py-4 px-3 text-center">
                <input
                  type="checkbox"
                  :value="asg.id"
                  v-model="selectedIds"
                  class="rounded border-slate-700 bg-slate-950 text-purple-500 focus:ring-purple-500/20"
                />
              </td>

              <td class="py-4 px-4 font-sans text-slate-300 font-semibold whitespace-nowrap">
                {{ String((currentPage - 1) * pageSize + idx + 1).padStart(2, '0') }}
              </td>

              <td class="py-4 px-4 whitespace-nowrap">
                <div class="font-bold text-white text-sm">{{ asg.teacher }}</div>
                <div class="text-[11px] text-purple-300 font-medium">{{ asg.role }} • {{ asg.major }}</div>
              </td>

              <td class="py-4 px-4 whitespace-nowrap">
                <div class="font-bold text-slate-200">{{ asg.subject }}</div>
                <div class="text-[10px] text-slate-300">{{ asg.course }} ({{ asg.mode }})</div>
              </td>

              <td class="py-4 px-4 whitespace-nowrap font-sans text-slate-200 font-medium">
                {{ asg.schedule }}
              </td>

              <td class="py-4 px-4 whitespace-nowrap font-sans">
                <div class="text-amber-300 font-bold">${{ asg.price }} USD</div>
                <div class="text-[10px] text-slate-300 font-medium">{{ asg.commission }}</div>
              </td>

              <td class="py-4 px-4 text-center whitespace-nowrap">
                <span v-if="asg.status === 'active'" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 inline-flex items-center gap-1.5 shadow-sm">
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

              <!-- STANDARDIZED ACTION BUTTONS -->
              <td class="py-4 px-4 text-right whitespace-nowrap relative" @click.stop>
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    @click="openEditModal(asg)"
                    class="px-3 py-1.5 bg-purple-600/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/30 rounded-xl font-bold whitespace-nowrap inline-flex items-center gap-1"
                  >
                    <svg class="w-3.5 h-3.5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Edit</span>
                  </button>

                  <div class="relative">
                    <button
                      @click="toggleDropdown(asg.id)"
                      class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-bold transition-all border border-slate-700"
                      title="More Options"
                    >
                      <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                      </svg>
                    </button>

                    <div
                      v-if="activeDropdownId === asg.id"
                      class="absolute right-0 mt-1 w-44 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl z-50 p-1.5 text-xs text-left space-y-1 backdrop-blur-xl animate-fade-in"
                    >
                      <button
                        @click="viewCommissionDetails(asg)"
                        class="w-full px-3 py-2 text-slate-200 hover:text-white hover:bg-slate-800 rounded-xl flex items-center gap-2 font-medium"
                      >
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        ABA & Revenue
                      </button>

                      <div class="border-t border-slate-800 my-1"></div>

                      <button
                        @click="deleteAssignment(asg)"
                        class="w-full px-3 py-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-xl flex items-center gap-2 font-medium"
                      >
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Remove Assignment
                      </button>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION CONTROLS -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-3.5 bg-slate-900/60 border border-slate-800 rounded-2xl text-xs text-slate-400 backdrop-blur-xl">
        <div class="flex items-center gap-3">
          <span>Showing <strong class="text-white">{{ filteredAssignments.length === 0 ? 0 : (currentPage - 1) * pageSize + 1 }}</strong> to <strong class="text-white">{{ Math.min(currentPage * pageSize, filteredAssignments.length) }}</strong> of <strong class="text-white">{{ filteredAssignments.length }}</strong> assignments</span>

          <div class="flex items-center gap-1.5 ml-2">
            <span>Per page:</span>
            <select v-model="pageSize" class="bg-slate-950 border border-slate-800 rounded-lg px-2 py-1 text-white">
              <option :value="5">5</option>
              <option :value="10">10</option>
              <option :value="25">25</option>
            </select>
          </div>
        </div>

        <div class="flex items-center gap-1.5">
          <button
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-950 font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed hover:bg-slate-800 hover:text-white text-slate-300"
          >
            ‹ Prev
          </button>

          <div class="flex items-center gap-1 px-1">
            <button
              v-for="p in totalPages"
              :key="p"
              @click="currentPage = p"
              class="w-8 h-8 rounded-xl font-bold transition-all flex items-center justify-center"
              :class="currentPage === p ? 'bg-purple-600 text-white' : 'bg-slate-950 border border-slate-800 text-slate-400 hover:text-white'"
            >
              {{ p }}
            </button>
          </div>

          <button
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            class="px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-950 font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed hover:bg-slate-800 hover:text-white text-slate-300"
          >
            Next ›
          </button>
        </div>
      </div>

      <!-- MODAL -->
      <div v-if="isModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900/95 border border-slate-700/60 rounded-2xl max-w-xl w-full p-5 space-y-4 shadow-2xl backdrop-blur-2xl overflow-y-auto max-h-[90vh]">
          <!-- Modal Header -->
          <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-xl bg-purple-500/15 border border-purple-500/30 flex items-center justify-center text-purple-400 shadow-sm">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <h3 class="text-sm font-bold text-slate-100 tracking-wide uppercase">
                {{ editingAssignment ? 'EDIT TEACHER ASSIGNMENT' : 'ASSIGN TEACHER TO COURSE' }}
              </h3>
            </div>
            <button @click="isModalOpen = false" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-all">✕</button>
          </div>

          <form @submit.prevent="saveAssignment" class="space-y-3.5 text-xs">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Select Teacher</label>
                <input v-model="form.teacher" type="text" placeholder="e.g. Mr. Sophea" class="w-full bg-slate-950/80 border border-slate-800 focus:border-purple-500 rounded-xl px-3.5 py-2 text-xs text-slate-100 font-semibold focus:outline-none focus:ring-1 focus:ring-purple-500/20 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Teaching Role</label>
                <select v-model="form.role" class="w-full bg-slate-950/80 border border-slate-800 focus:border-purple-500 rounded-xl px-3.5 py-2 text-xs text-slate-100 font-medium focus:outline-none focus:ring-1 focus:ring-purple-500/20 transition-all">
                  <option value="Main Teacher">Main Teacher</option>
                  <option value="Main Author">Main Author</option>
                  <option value="Assistant Teacher">Assistant Teacher</option>
                  <option value="Reviewer">Reviewer</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Subject</label>
                <input v-model="form.subject" type="text" class="w-full bg-slate-950/80 border border-slate-800 focus:border-purple-500 rounded-xl px-3.5 py-2 text-xs text-slate-100 focus:outline-none focus:ring-1 focus:ring-purple-500/20 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Course Title</label>
                <input v-model="form.course" type="text" class="w-full bg-slate-950/80 border border-slate-800 focus:border-purple-500 rounded-xl px-3.5 py-2 text-xs text-slate-100 focus:outline-none focus:ring-1 focus:ring-purple-500/20 transition-all" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Class Schedule</label>
                <input v-model="form.schedule" type="text" placeholder="e.g. Mon/Wed/Fri — 2:00 PM - 4:00 PM" class="w-full bg-slate-950/80 border border-slate-800 focus:border-purple-500 rounded-xl px-3.5 py-2 text-xs text-slate-100 font-sans focus:outline-none focus:ring-1 focus:ring-purple-500/20 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Student Price ($ USD)</label>
                <input v-model.number="form.price" type="number" class="w-full bg-slate-950/80 border border-slate-800 focus:border-purple-500 rounded-xl px-3.5 py-2 text-xs text-amber-300 font-bold font-sans focus:outline-none focus:ring-1 focus:ring-purple-500/20 transition-all" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Revenue Commission</label>
                <input v-model="form.commission" type="text" placeholder="e.g. 70% Teacher / 30% School" class="w-full bg-slate-950/80 border border-slate-800 focus:border-purple-500 rounded-xl px-3.5 py-2 text-xs text-slate-100 font-sans focus:outline-none focus:ring-1 focus:ring-purple-500/20 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">ABA Receiver Account</label>
                <input v-model="form.aba" type="text" class="w-full bg-slate-950/80 border border-slate-800 focus:border-purple-500 rounded-xl px-3.5 py-2 text-xs text-sky-300 font-semibold font-sans focus:outline-none focus:ring-1 focus:ring-purple-500/20 transition-all" />
              </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-800/80">
              <button type="button" @click="isModalOpen = false" :disabled="isSubmitting" class="px-4 py-2 bg-slate-800/80 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl transition-all disabled:opacity-50">Cancel</button>
              <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-purple-600/30 transition-all flex items-center gap-1.5 disabled:opacity-50 cursor-pointer">
                <svg v-if="isSubmitting" class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ isSubmitting ? (editingAssignment ? 'កំពុងរក្សាទុក...' : 'កំពុងចាត់តាំងគ្រូ...') : (editingAssignment ? 'Save Assignment' : 'Assign Teacher') }}</span>
              </button>
            </div>
          </form>
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
