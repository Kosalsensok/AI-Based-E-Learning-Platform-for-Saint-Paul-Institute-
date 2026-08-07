<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AcademicModuleHeader from '@/Components/Admin/AcademicModuleHeader.vue'

const props = withDefaults(defineProps<{
  semesters?: any[]
  summaryStats?: any
}>(), {
  semesters: () => [
    { id: 1, code: 'SEM-1-2024-2025', name: 'Semester 1 — 2024-2025', parent_year: 'Academic Year 2024–2025', semester_num: 'Semester 1', start_date: '01 Sep 2024', end_date: '15 Feb 2025', status: 'completed', is_active: false },
    { id: 2, code: 'SEM-2-2024-2025', name: 'Semester 2 — 2024-2025', parent_year: 'Academic Year 2024–2025', semester_num: 'Semester 2', start_date: '16 Feb 2025', end_date: '31 Aug 2025', status: 'active', is_active: true },
    { id: 3, code: 'SEM-1-2023-2024', name: 'Semester 1 — 2023-2024', parent_year: 'Academic Year 2023–2024', semester_num: 'Semester 1', start_date: '01 Sep 2023', end_date: '15 Feb 2024', status: 'completed', is_active: false },
    { id: 4, code: 'SEM-2-2023-2024', name: 'Semester 2 — 2023-2024', parent_year: 'Academic Year 2023–2024', semester_num: 'Semester 2', start_date: '16 Feb 2024', end_date: '31 Aug 2024', status: 'completed', is_active: false },
    { id: 5, code: 'SEM-1-2025-2026', name: 'Semester 1 — 2025-2026', parent_year: 'Academic Year 2025–2026', semester_num: 'Semester 1', start_date: '01 Sep 2025', end_date: '15 Feb 2026', status: 'planned', is_active: false },
  ],
  summaryStats: () => ({})
})

const defaultSemesters = [
  { id: 1, code: 'SEM-1-2024-2025', name: 'Semester 1 — 2024-2025', parent_year: 'Academic Year 2024–2025', semester_num: 'Semester 1', start_date: '01 Sep 2024', end_date: '15 Feb 2025', status: 'completed', is_active: false },
  { id: 2, code: 'SEM-2-2024-2025', name: 'Semester 2 — 2024-2025', parent_year: 'Academic Year 2024–2025', semester_num: 'Semester 2', start_date: '16 Feb 2025', end_date: '31 Aug 2025', status: 'active', is_active: true },
  { id: 3, code: 'SEM-1-2023-2024', name: 'Semester 1 — 2023-2024', parent_year: 'Academic Year 2023–2024', semester_num: 'Semester 1', start_date: '01 Sep 2023', end_date: '15 Feb 2024', status: 'completed', is_active: false },
  { id: 4, code: 'SEM-2-2023-2024', name: 'Semester 2 — 2023-2024', parent_year: 'Academic Year 2023–2024', semester_num: 'Semester 2', start_date: '16 Feb 2024', end_date: '31 Aug 2024', status: 'completed', is_active: false },
  { id: 5, code: 'SEM-1-2025-2026', name: 'Semester 1 — 2025-2026', parent_year: 'Academic Year 2025–2026', semester_num: 'Semester 1', start_date: '01 Sep 2025', end_date: '15 Feb 2026', status: 'planned', is_active: false },
]

const searchQuery = ref('')
const activeStatusIconUrl = '/images/nav/active-status.svg'
const inactiveStatusIconUrl = '/images/nav/inactive-status.svg'
const saveIconUrl = '/images/nav/save-icon.svg'
const cancelIconUrl = '/images/nav/cancel-icon.svg'
const semesterIconUrl = '/images/nav/semester-icon.svg'

// Tree Accordion Open State
const expandedFacultyId = ref<number | null>(1)

const toggleFacultyAccordion = (id: number) => {
  expandedFacultyId.value = expandedFacultyId.value === id ? null : id
}

// Sorting State
const sortKey = ref('name')
const sortOrder = ref<'asc' | 'desc'>('asc')

// Pagination State
const currentPage = ref(1)
const itemsPerPage = ref(5)

const toggleSort = (key: string) => {
  if (sortKey.value === key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortOrder.value = 'asc'
  }
}

const clearSearch = () => {
  searchQuery.value = ''
}

const isModalOpen = ref(false)
const editingSemester = ref<any>(null)
const form = ref({
  code: '',
  name: '',
  parent_year: 'Academic Year 2024–2025',
  semester_num: 'Semester 2',
  start_date: '2025-02-16',
  end_date: '2025-08-31',
  enrollment_open: '2025-02-01',
  enrollment_close: '2025-02-15',
  midterm_exam: '15 - 20 May 2025',
  final_exam: '20 - 30 Aug 2025',
  payment_due: '2025-02-15',
  late_fee: '$5 per week overdue',
  status: 'active'
})

const semestersList = computed(() => {
  const list = Array.isArray(props.semesters) && props.semesters.length > 0 ? props.semesters : defaultSemesters

  let result = list.filter(sem => {
    const name = sem.name || ''
    const code = sem.code || ''
    const year = typeof sem.parent_year === 'object' ? (sem.parent_year?.name || '') : (sem.parent_year || '')
    const q = searchQuery.value.toLowerCase()
    return name.toLowerCase().includes(q) || code.toLowerCase().includes(q) || year.toLowerCase().includes(q)
  })

  result.sort((a, b) => {
    let valA = a[sortKey.value]
    let valB = b[sortKey.value]

    if (valA === valB) return 0
    if (valA === undefined || valA === null) return 1
    if (valB === undefined || valB === null) return -1

    if (typeof valA === 'string') {
      return sortOrder.value === 'asc'
        ? valA.localeCompare(valB)
        : valB.localeCompare(valA)
    }

    return sortOrder.value === 'asc' ? valA - valB : valB - valA
  })

  return result
})

const totalPages = computed(() => {
  return Math.ceil(semestersList.value.length / itemsPerPage.value) || 1
})

const paginatedSemesters = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return semestersList.value.slice(start, start + itemsPerPage.value)
})

const openAddModal = () => {
  editingSemester.value = null
  form.value = {
    code: 'SEM-NEW-01',
    name: 'Semester 1 — 2025-2026',
    parent_year: 'Academic Year 2025–2026',
    semester_num: 'Semester 1',
    start_date: '2025-09-01',
    end_date: '2026-02-15',
    enrollment_open: '2025-08-15',
    enrollment_close: '2025-08-31',
    midterm_exam: '15 - 20 Nov 2025',
    final_exam: '01 - 10 Feb 2026',
    payment_due: '2025-08-31',
    late_fee: '$5 per week overdue',
    status: 'planned'
  }
  isModalOpen.value = true
}

const openEditModal = (sem: any) => {
  editingSemester.value = sem
  form.value = { ...sem }
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

const targetRoute = (name: string, param?: any) => {
  try {
    return param !== undefined ? (window as any).route(`admin.${name}`, param) : (window as any).route(`admin.${name}`)
  } catch (e) {
    return param !== undefined ? (window as any).route(name, param) : (window as any).route(name)
  }
}

const getSemesterStatus = (sem: any) => {
  if (sem.status) return sem.status
  return sem.is_active ? 'active' : 'completed'
}

const saveSemester = () => {
  if (!form.value.name) {
    triggerToast('សូមបញ្ចូលឈ្មោះឆមាស', 'សូមបញ្ចូល Semester Name មុនពេលរក្សាទុក', 'warning')
    return
  }

  isSubmitting.value = true
  const semName = form.value.name || 'ឆមាស'

  if (editingSemester.value?.id) {
    router.put(targetRoute('academic-structure.semesters.update', editingSemester.value.id), form.value, {
      preserveScroll: true,
      onSuccess: () => {
        isSubmitting.value = false
        isModalOpen.value = false
        triggerToast(
          'រក្សាទុកបានជោគជ័យ',
          `ព័ត៌មានឆមាស "${semName}" ត្រូវបានបច្ចុប្បន្នភាពដោយជោគជ័យ`
        )
      },
      onError: () => {
        isSubmitting.value = false
        triggerToast(
          'មានបញ្ហាក្នុងការរក្សាទុក',
          'សូមពិនិត្យមើលព័ត៌មានដែលបានបញ្ចូលឡើងវិញ',
          'warning'
        )
      },
      onFinish: () => {
        isSubmitting.value = false
      }
    })
  } else {
    router.post(targetRoute('academic-structure.semesters.store'), form.value, {
      preserveScroll: true,
      onSuccess: () => {
        isSubmitting.value = false
        isModalOpen.value = false
        triggerToast(
          'បង្កើតឆមាសបានជោគជ័យ',
          `ឆមាស "${semName}" ត្រូវបានបង្កើតក្នុងប្រព័ន្ធដោយជោគជ័យ`
        )
      },
      onError: () => {
        isSubmitting.value = false
        triggerToast(
          'មានបញ្ហាក្នុងការបង្កើតឆមាស',
          'សូមពិនិត្យមើលព័ត៌មានដែលបានបញ្ចូលឡើងវិញ',
          'warning'
        )
      },
      onFinish: () => {
        isSubmitting.value = false
      }
    })
  }
}

const deleteSemester = (sem: any) => {
  if (confirm(`Are you sure you want to delete '${sem.name}'?`)) {
    router.delete(targetRoute('academic-structure.semesters.destroy', sem.id), {
      preserveScroll: true,
      onSuccess: () => {
        triggerToast(
          'លុបឆមាសបានជោគជ័យ',
          `ឆមាស "${sem.name}" ត្រូវបានលុបចេញពីប្រព័ន្ធដោយជោគជ័យ`
        )
      },
      onError: () => {
        triggerToast(
          'មានបញ្ហាក្នុងការលុប',
          'មិនអាចលុបឆមាសនេះបានទេ',
          'warning'
        )
      }
    })
  }
}

const setActiveSemester = (sem: any) => {
  if (confirm(`Set '${sem.name}' as the active semester?`)) {
    router.post(targetRoute('academic-structure.semesters.set-active', sem.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        triggerToast(
          'កំណត់ឆមាសសកម្មជោគជ័យ',
          `ឆមាស "${sem.name}" ត្រូវបានកំណត់ជាឆមាសសកម្ម`
        )
      },
      onError: () => {
        triggerToast('មានបញ្ហាក្នុងការកំណត់ឆមាស', 'សូមព្យាយាមម្តងទៀត', 'warning')
      }
    })
  }
}

const toggleSemesterStatus = (sem: any) => {
  const currentStatus = getSemesterStatus(sem)
  const newStatus = currentStatus === 'completed' ? 'active' : 'completed'
  const newIsActive = newStatus === 'active'

  if (sem.id) {
    router.put(
      targetRoute('academic-structure.semesters.update', sem.id),
      { ...sem, status: newStatus, is_active: newIsActive },
      {
        preserveScroll: true,
        onSuccess: () => {
          sem.status = newStatus
          sem.is_active = newIsActive
          triggerToast(
            'បច្ចុប្បន្នភាពស្ថានភាពជោគជ័យ',
            `ស្ថានភាពឆមាស "${sem.name}" ត្រូវបានផ្លាស់ប្តូរទៅជា ${newStatus.toUpperCase()}`
          )
        },
        onError: () => {
          triggerToast('មានបញ្ហាក្នុងការផ្លាស់ប្តូរស្ថានភាព', 'សូមព្យាយាមម្តងទៀត', 'warning')
        }
      }
    )
  } else {
    sem.status = newStatus
    sem.is_active = newIsActive
  }
}
</script>

<template>
  <AdminLayout title="Semesters — Academic Structure">
    <div class="space-y-5 font-sans mb-8">
      <!-- Shared Header -->
      <AcademicModuleHeader activeTab="semesters" :summaryStats="props.summaryStats" />

      <!-- CURRENT SEMESTER OVERVIEW (Soft Glassmorphic Frame) -->
      <div class="relative group bg-gradient-to-br from-slate-900/95 via-slate-900/85 to-amber-950/20 border border-amber-500/20 hover:border-amber-500/35 rounded-3xl p-6 backdrop-blur-xl space-y-4 shadow-xl overflow-hidden transition-all duration-300">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/15 transition-all pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-4">
          <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-300 border border-amber-500/20 whitespace-nowrap shadow-xs">
              <img :src="activeStatusIconUrl" class="w-3.5 h-3.5 object-contain" alt="Active" />
              <span>CURRENT ACTIVE SEMESTER</span>
            </span>
            <h3 class="text-lg font-black text-white tracking-tight">Semester 2 — Academic Year 2024-2025</h3>
          </div>

          <button @click="openEditModal(semestersList[1] || semestersList[0])" class="px-4 py-2 bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700/80 rounded-xl text-xs font-bold whitespace-nowrap transition-all cursor-pointer">
            ✏️ Edit Semester Dates
          </button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
          <div class="p-3.5 bg-slate-950/70 border border-slate-800/80 rounded-2xl space-y-1">
            <div class="text-[11px] text-slate-400 font-medium">📅 Duration:</div>
            <div class="font-bold text-white text-xs">16 Feb 2025 → 31 Aug 2025</div>
          </div>

          <div class="p-3.5 bg-slate-950/70 border border-slate-800/80 rounded-2xl space-y-1">
            <div class="text-[11px] text-slate-400 font-medium">📝 Enrollment Period:</div>
            <div class="font-bold text-amber-300 text-xs">01 Feb – 15 Feb 2025 (Closed)</div>
          </div>

          <div class="p-3.5 bg-slate-950/70 border border-slate-800/80 rounded-2xl space-y-1">
            <div class="text-[11px] text-slate-400 font-medium">📖 Midterm Exams:</div>
            <div class="font-bold text-cyan-300 text-xs">15 May – 20 May 2025</div>
          </div>

          <div class="p-3.5 bg-slate-950/70 border border-slate-800/80 rounded-2xl space-y-1">
            <div class="text-[11px] text-slate-400 font-medium">🎓 Final Exams:</div>
            <div class="font-bold text-emerald-300 text-xs">20 Aug – 30 Aug 2025</div>
          </div>
        </div>
      </div>

      <!-- SEARCH, FILTER & ACTION TOOLBAR -->
      <div class="flex flex-col md:flex-row items-center justify-between gap-3 p-4 bg-slate-900/80 border border-slate-700/80 rounded-2xl backdrop-blur-xl shadow-lg">
        <div class="relative w-full md:w-96">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-amber-400 text-sm">
            🔍
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search semester name, code, parent year..."
            class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-2 focus:ring-amber-500/20 rounded-xl pl-9 pr-9 py-2.5 text-xs text-white placeholder-slate-400 shadow-inner transition-all font-medium"
          />
          <button 
            v-if="searchQuery"
            @click="clearSearch"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white transition-colors"
          >
            ✕
          </button>
        </div>

        <button 
          @click="openAddModal" 
          class="w-full md:w-auto px-4.5 py-2.5 bg-amber-500/15 hover:bg-amber-500/30 text-amber-200 border border-amber-500/25 hover:border-amber-500/50 font-bold text-xs rounded-xl backdrop-blur-md transition-all flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer shadow-xs"
        >
          <span>➕</span>
          <span>Add New Semester</span>
        </button>
      </div>

      <!-- SEMESTERS DATA TABLE -->
      <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/80 backdrop-blur-xl shadow-xl">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-800 bg-slate-800/60 text-xs font-extrabold text-slate-200 uppercase whitespace-nowrap select-none">
              <th class="py-4 px-4 w-12 text-slate-400">#</th>

              <!-- Semester Name -->
              <th @click="toggleSort('name')" class="py-4 px-4 cursor-pointer hover:text-white transition-colors group">
                <div class="flex items-center gap-2">
                  <span>📆 Semester</span>
                  <span :class="[sortKey === 'name' ? 'bg-amber-500/20 text-amber-300 font-extrabold border border-amber-500/40 px-1.5 py-0.5 rounded' : 'text-slate-500 group-hover:text-slate-300', 'text-[11px] transition-all']">
                    {{ sortKey === 'name' ? (sortOrder === 'asc' ? '▲ ASC' : '▼ DESC') : '↕' }}
                  </span>
                </div>
              </th>

              <!-- Parent Year -->
              <th @click="toggleSort('parent_year')" class="py-4 px-4 cursor-pointer hover:text-white transition-colors group">
                <div class="flex items-center gap-2">
                  <span>📅 Parent Year</span>
                  <span :class="[sortKey === 'parent_year' ? 'bg-indigo-500/20 text-indigo-300 font-extrabold border border-indigo-500/40 px-1.5 py-0.5 rounded' : 'text-slate-500 group-hover:text-slate-300', 'text-[11px] transition-all']">
                    {{ sortKey === 'parent_year' ? (sortOrder === 'asc' ? '▲ ASC' : '▼ DESC') : '↕' }}
                  </span>
                </div>
              </th>

              <th class="py-4 px-4">Start Date</th>
              <th class="py-4 px-4">End Date</th>

              <!-- Status -->
              <th @click="toggleSort('status')" class="py-4 px-4 text-center cursor-pointer hover:text-white transition-colors group">
                <div class="flex items-center justify-center gap-1.5">
                  <span>Status</span>
                  <span :class="[sortKey === 'status' ? 'bg-slate-700 text-slate-200 font-extrabold px-1.5 py-0.5 rounded' : 'text-slate-500 group-hover:text-slate-300', 'text-[11px] transition-all']">
                    {{ sortKey === 'status' ? (sortOrder === 'asc' ? '▲' : '▼') : '↕' }}
                  </span>
                </div>
              </th>

              <th class="py-4 px-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/80 text-xs">
            <tr v-for="(sem, idx) in paginatedSemesters" :key="sem.id" class="hover:bg-slate-800/50 transition-all">
              <td class="py-4 px-4 font-bold text-slate-300 whitespace-nowrap">
                {{ String((currentPage - 1) * itemsPerPage + idx + 1).padStart(2, '0') }}
              </td>

              <td class="py-4 px-4 whitespace-nowrap">
                <div>
                  <div class="font-black text-white text-sm tracking-tight">{{ sem.name }}</div>
                  <div class="mt-1">
                    <span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700 text-amber-300 font-bold text-[11px]">
                      Code: {{ sem.code }}
                    </span>
                  </div>
                </div>
              </td>

              <td class="py-4 px-4 whitespace-nowrap text-slate-200 font-bold">
                {{ typeof sem.parent_year === 'object' ? (sem.parent_year?.name || 'Academic Year 2024–2025') : (sem.parent_year || 'Academic Year 2024–2025') }}
              </td>

              <td class="py-4 px-4 whitespace-nowrap text-slate-200 font-bold">
                {{ sem.start_date || '01 Sep 2024' }}
              </td>

              <td class="py-4 px-4 whitespace-nowrap text-slate-200 font-bold">
                {{ sem.end_date || '31 Aug 2025' }}
              </td>

              <!-- Interactive Status Badge with Flaticon SVG Icon -->
              <td class="py-4 px-4 text-center whitespace-nowrap">
                <button
                  @click="toggleSemesterStatus(sem)"
                  title="Click to toggle Status"
                  :class="[sem.status === 'completed' ? 'bg-slate-800 text-slate-400 border-slate-700' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 hover:bg-emerald-500/30']"
                  class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold border transition-all cursor-pointer hover:scale-105 shadow-xs"
                >
                  <img 
                    :src="sem.status === 'completed' ? inactiveStatusIconUrl : activeStatusIconUrl" 
                    :alt="sem.status" 
                    class="w-4 h-4 object-contain shrink-0 filter drop-shadow-xs" 
                  />
                  <span>{{ sem.status === 'completed' ? 'Completed' : (sem.is_active ? 'Active' : 'Planned') }}</span>
                  <span class="text-[10px] opacity-70">▾</span>
                </button>
              </td>

              <td class="py-4 px-4 text-right whitespace-nowrap">
                <div class="flex items-center justify-end gap-2">
                  <button
                    v-if="!sem.is_active"
                    @click="setActiveSemester(sem)"
                    class="px-3 py-1.5 bg-amber-500/15 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 rounded-xl font-bold whitespace-nowrap inline-flex items-center gap-1.5 transition-all cursor-pointer shadow-xs text-xs"
                  >
                    <span>⚡</span>
                    <span>Set Active</span>
                  </button>

                  <button
                    @click="openEditModal(sem)"
                    class="px-3.5 py-1.5 bg-indigo-500/15 hover:bg-indigo-500/30 text-indigo-300 border border-indigo-500/40 rounded-xl font-bold whitespace-nowrap inline-flex items-center gap-1.5 transition-all cursor-pointer shadow-xs"
                  >
                    <span>✏️</span>
                    <span>Edit</span>
                  </button>

                  <button
                    @click="deleteSemester(sem)"
                    class="px-3.5 py-1.5 bg-rose-500/10 hover:bg-rose-500/25 text-rose-400 border border-rose-500/30 hover:border-rose-500/60 rounded-xl font-medium whitespace-nowrap inline-flex items-center gap-1.5 transition-all cursor-pointer shadow-xs"
                  >
                    <span>🗑️</span>
                    <span>Delete</span>
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="semestersList.length === 0">
              <td colspan="7" class="py-10 text-center text-slate-300 text-xs">
                No semesters found matching your search query.
              </td>
            </tr>
          </tbody>
        </table>

        <!-- PAGINATION & ROW COUNT FOOTER -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 border-t border-slate-800 bg-slate-900/60 text-xs text-slate-300">
          <div class="flex items-center gap-2">
            <span>Show</span>
            <select v-model="itemsPerPage" class="bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1 text-xs text-white focus:outline-none focus:border-amber-500">
              <option :value="5">5</option>
              <option :value="10">10</option>
              <option :value="25">25</option>
            </select>
            <span>entries per page • Showing <strong class="text-white font-bold">{{ semestersList.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0 }}</strong> to <strong class="text-white font-bold">{{ Math.min(currentPage * itemsPerPage, semestersList.length) }}</strong> of <strong class="text-white font-bold">{{ semestersList.length }}</strong> Semesters</span>
          </div>

          <div class="flex items-center gap-2">
            <button
              @click="currentPage = Math.max(1, currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-3.5 py-1.5 rounded-xl border border-slate-700 bg-slate-800 text-slate-200 hover:text-white hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all font-semibold cursor-pointer"
            >
              ◀ Prev
            </button>

            <span class="px-3.5 py-1.5 rounded-xl bg-amber-500/20 border border-amber-500/30 text-amber-300 font-extrabold">
              {{ currentPage }} / {{ totalPages }}
            </span>

            <button
              @click="currentPage = Math.min(totalPages, currentPage + 1)"
              :disabled="currentPage >= totalPages"
              class="px-3.5 py-1.5 rounded-xl border border-slate-700 bg-slate-800 text-slate-200 hover:text-white hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all font-semibold cursor-pointer"
            >
              Next ▶
            </button>
          </div>
        </div>
      </div>

      <!-- REFACTORED CLEAN ACCORDION VISUAL TREE HIERARCHY SECTION -->
      <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 backdrop-blur-xl space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
          <div class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
            <span>🌳 ACADEMIC STRUCTURE — INTERACTIVE TREE NAVIGATION</span>
          </div>
          <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">
            Interactive Hierarchy
          </span>
        </div>

        <div class="space-y-3 text-xs">
          <!-- Accordion Item 1: Faculty of Computing -->
          <div class="border border-slate-800 rounded-2xl bg-slate-950/70 overflow-hidden transition-all">
            <button 
              @click="toggleFacultyAccordion(1)"
              class="w-full p-4 flex items-center justify-between hover:bg-slate-800/40 text-left transition-colors cursor-pointer"
            >
              <div class="flex items-center gap-3 font-bold text-white text-sm">
                <span class="text-sky-400">🏫</span>
                <span>Faculty of Computing</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700 font-normal">2 Departments</span>
              </div>
              <span class="text-slate-400 font-bold">{{ expandedFacultyId === 1 ? '▲ Collapse' : '▼ Expand' }}</span>
            </button>

            <div v-if="expandedFacultyId === 1" class="p-4 border-t border-slate-800/80 space-y-3 bg-slate-900/40">
              <!-- Dept 1 -->
              <div class="pl-4 border-l-2 border-indigo-500/40 space-y-2">
                <div class="flex items-center justify-between font-bold text-indigo-300">
                  <span class="flex items-center gap-2">🏢 Dept: Computing & IT</span>
                  <span class="text-[11px] text-slate-400">Head: Mr. Sophea</span>
                </div>
                <div class="pl-4 space-y-1.5">
                  <div class="flex items-center justify-between text-cyan-300 font-bold">
                    <span>🎓 Major: IT & Networking (Bachelor — 4 Years)</span>
                    <span class="text-slate-400 font-normal">520 Enrolled</span>
                  </div>
                  <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-slate-300 font-medium">📅 AY 2024-2025</span>
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">📆 Semester 2 (Active)</span>
                    <span class="px-2.5 py-1 rounded-lg bg-slate-800/80 text-amber-300 border border-slate-700 font-bold">📚 C Programming, Web Dev ($25/subject)</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Accordion Item 2: Faculty of Tourism -->
          <div class="border border-slate-800 rounded-2xl bg-slate-950/70 overflow-hidden transition-all">
            <button 
              @click="toggleFacultyAccordion(2)"
              class="w-full p-4 flex items-center justify-between hover:bg-slate-800/40 text-left transition-colors cursor-pointer"
            >
              <div class="flex items-center gap-3 font-bold text-white text-sm">
                <span class="text-sky-400">🏫</span>
                <span>Faculty of Tourism</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700 font-normal">2 Departments</span>
              </div>
              <span class="text-slate-400 font-bold">{{ expandedFacultyId === 2 ? '▲ Collapse' : '▼ Expand' }}</span>
            </button>

            <div v-if="expandedFacultyId === 2" class="p-4 border-t border-slate-800/80 space-y-3 bg-slate-900/40">
              <div class="pl-4 border-l-2 border-indigo-500/40 space-y-2">
                <div class="flex items-center justify-between font-bold text-indigo-300">
                  <span class="flex items-center gap-2">🏢 Dept: Tourism & Hospitality</span>
                  <span class="text-[11px] text-slate-400">Head: Mr. Long</span>
                </div>
                <div class="pl-4 space-y-1.5">
                  <div class="flex items-center justify-between text-cyan-300 font-bold">
                    <span>🎓 Major: Tourism Management (Bachelor — 4 Years)</span>
                    <span class="text-slate-400 font-normal">410 Enrolled</span>
                  </div>
                  <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-slate-300 font-medium">📅 AY 2024-2025</span>
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">📆 Semester 2 (Active)</span>
                    <span class="px-2.5 py-1 rounded-lg bg-slate-800/80 text-amber-300 border border-slate-700 font-bold">📚 Tourism Basics, Hospitality ($20/subject)</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ADD / EDIT SEMESTER MODAL -->
      <div v-if="isModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-xl z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-700/80 rounded-3xl max-w-xl w-full p-6 space-y-5 shadow-2xl overflow-y-auto max-h-[90vh] transition-all">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3.5">
            <div class="flex items-center gap-3">
              <img :src="semesterIconUrl" class="w-9 h-9 object-contain rounded-xl shadow-xs" alt="Semester" />
              <div>
                <h3 class="text-base font-black text-white tracking-tight">
                  {{ editingSemester ? 'EDIT SEMESTER RECORD' : 'ADD NEW SEMESTER' }}
                </h3>
                <p class="text-[11px] text-slate-400 font-medium">Specify details below to save semester information</p>
              </div>
            </div>
            <button @click="isModalOpen = false" class="w-8 h-8 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer">✕</button>
          </div>

          <div class="space-y-4 text-xs">
            <div>
              <label class="block font-bold text-slate-300 mb-1.5">Semester Code</label>
              <input v-model="form.code" type="text" class="w-full bg-slate-950/70 border border-slate-700/80 focus:border-amber-400 focus:ring-2 focus:ring-amber-500/20 rounded-xl px-3.5 py-2.5 text-white font-bold tracking-wide" />
            </div>

            <div>
              <label class="block font-bold text-slate-300 mb-1.5">Semester Name</label>
              <input v-model="form.name" type="text" placeholder="Semester 1 — 2025-2026" class="w-full bg-slate-950/70 border border-slate-700/80 focus:border-amber-400 focus:ring-2 focus:ring-amber-500/20 rounded-xl px-3.5 py-2.5 text-white placeholder-slate-500 font-medium" />
            </div>

            <div>
              <label class="block font-bold text-slate-300 mb-1.5">Parent Academic Year</label>
              <input v-model="form.parent_year" type="text" placeholder="Academic Year 2025–2026" class="w-full bg-slate-950/70 border border-slate-700/80 focus:border-amber-400 focus:ring-2 focus:ring-amber-500/20 rounded-xl px-3.5 py-2.5 text-white placeholder-slate-500 font-medium" />
            </div>

            <div class="grid grid-cols-2 gap-3.5">
              <div>
                <label class="block font-bold text-slate-300 mb-1.5">Start Date</label>
                <input v-model="form.start_date" type="text" placeholder="01 Sep 2025" class="w-full bg-slate-950/70 border border-slate-700/80 focus:border-amber-400 focus:ring-2 focus:ring-amber-500/20 rounded-xl px-3.5 py-2.5 text-white placeholder-slate-500 font-medium" />
              </div>

              <div>
                <label class="block font-bold text-slate-300 mb-1.5">End Date</label>
                <input v-model="form.end_date" type="text" placeholder="15 Feb 2026" class="w-full bg-slate-950/70 border border-slate-700/80 focus:border-amber-400 focus:ring-2 focus:ring-amber-500/20 rounded-xl px-3.5 py-2.5 text-white placeholder-slate-500 font-medium" />
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 border-t border-slate-800/80 pt-4">
            <button @click="isModalOpen = false" :disabled="isSubmitting" class="px-4.5 py-2.5 bg-slate-800/80 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs rounded-xl transition-all cursor-pointer inline-flex items-center gap-2 disabled:opacity-50">
              <img :src="cancelIconUrl" class="w-4 h-4 object-contain opacity-80" alt="Cancel" />
              <span>Cancel</span>
            </button>
            <button @click="saveSemester" :disabled="isSubmitting" class="px-5 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-amber-600/25 transition-all cursor-pointer inline-flex items-center gap-2 disabled:opacity-50">
              <svg v-if="isSubmitting" class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <img v-else :src="saveIconUrl" class="w-4 h-4 object-contain brightness-200" alt="Save" />
              <span>{{ isSubmitting ? (editingSemester ? 'កំពុងរក្សាទុក...' : 'កំពុងបង្កើត...') : (editingSemester ? 'Save Semester' : 'Create Semester') }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Clean & Subtle Toast Notification -->
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
