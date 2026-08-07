<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AcademicModuleHeader from '@/Components/Admin/AcademicModuleHeader.vue'

const props = withDefaults(defineProps<{
  faculties?: any[]
  summaryStats?: any
}>(), {
  faculties: () => [
    { id: 1, code: 'FAC-001', name: 'Faculty of Computing', name_kh: 'មហាវិទ្យាល័យ វិទ្យាសាស្ត្រកុំព្យូទ័រ', dean: 'Dr. Sok Vichea', email: 'computing@elms.edu', est_year: 2010, depts_count: 2, majors_count: 1, students_count: 520, status: 'active', description: 'Faculty focused on IT and Computer Science' },
    { id: 2, code: 'FAC-002', name: 'Faculty of Tourism', name_kh: 'មហាវិទ្យាល័យ ទេសចរណ៍', dean: 'Dr. Keo Samnang', email: 'tourism@elms.edu', est_year: 2012, depts_count: 2, majors_count: 1, students_count: 410, status: 'active', description: 'Faculty dedicated to Hospitality and Tourism' },
    { id: 3, code: 'FAC-003', name: 'Faculty of Education', name_kh: 'មហាវិទ្យាល័យ អប់រំ', dean: 'Dr. Chan Srey', email: 'education@elms.edu', est_year: 2008, depts_count: 3, majors_count: 1, students_count: 380, status: 'active', description: 'Faculty providing Pedagogy and Language Studies' },
    { id: 4, code: 'FAC-004', name: 'Faculty of Agriculture', name_kh: 'មហាវិទ្យាល័យ កសិកម្ម', dean: 'Dr. Heng Vuthy', email: 'agriculture@elms.edu', est_year: 2014, depts_count: 2, majors_count: 1, students_count: 600, status: 'active', description: 'Faculty for Agricultural Technology and Plant Science' },
    { id: 5, code: 'FAC-005', name: 'Faculty of Social Science', name_kh: 'មហាវិទ្យាល័យ វិទ្យាសាស្ត្រសង្គម', dean: 'Dr. Pov Rithy', email: 'social@elms.edu', est_year: 2015, depts_count: 3, majors_count: 1, students_count: 548, status: 'active', description: 'Faculty for Development & Social Studies' },
  ],
  summaryStats: () => ({})
})

const defaultFaculties = [
  { id: 1, code: 'FAC-001', name: 'Faculty of Computing', name_kh: 'មហាវិទ្យាល័យ វិទ្យាសាស្ត្រកុំព្យូទ័រ', dean: 'Dr. Sok Vichea', email: 'computing@elms.edu', est_year: 2010, depts_count: 2, majors_count: 1, students_count: 520, status: 'active', description: 'Faculty focused on IT and Computer Science' },
  { id: 2, code: 'FAC-002', name: 'Faculty of Tourism', name_kh: 'មហាវិទ្យាល័យ ទេសចរណ៍', dean: 'Dr. Keo Samnang', email: 'tourism@elms.edu', est_year: 2012, depts_count: 2, majors_count: 1, students_count: 410, status: 'active', description: 'Faculty dedicated to Hospitality and Tourism' },
  { id: 3, code: 'FAC-003', name: 'Faculty of Education', name_kh: 'មហាវិទ្យាល័យ អប់រំ', dean: 'Dr. Chan Srey', email: 'education@elms.edu', est_year: 2008, depts_count: 3, majors_count: 1, students_count: 380, status: 'active', description: 'Faculty providing Pedagogy and Language Studies' },
  { id: 4, code: 'FAC-004', name: 'Faculty of Agriculture', name_kh: 'មហាវិទ្យាល័យ កសិកម្ម', dean: 'Dr. Heng Vuthy', email: 'agriculture@elms.edu', est_year: 2014, depts_count: 2, majors_count: 1, students_count: 600, status: 'active', description: 'Faculty for Agricultural Technology and Plant Science' },
  { id: 5, code: 'FAC-005', name: 'Faculty of Social Science', name_kh: 'មហាវិទ្យាល័យ វិទ្យាសាស្ត្រសង្គម', dean: 'Dr. Pov Rithy', email: 'social@elms.edu', est_year: 2015, depts_count: 3, majors_count: 1, students_count: 548, status: 'active', description: 'Faculty for Development & Social Studies' },
]

const searchQuery = ref('')
const statusFilter = ref('all')

// Sorting State
const sortKey = ref('name')
const sortOrder = ref<'asc' | 'desc'>('asc')

// Pagination State
const currentPage = ref(1)
const itemsPerPage = ref(5)

const activeStatusIconUrl = '/images/nav/active-status.svg'
const inactiveStatusIconUrl = '/images/nav/inactive-status.svg'
const saveIconUrl = '/images/nav/save-icon.svg'
const cancelIconUrl = '/images/nav/cancel-icon.svg'
const facultyIconUrl = '/images/nav/faculty-icon.svg'

const targetRoute = (name: string, param?: any) => {
  try {
    return param !== undefined ? (window as any).route(`admin.${name}`, param) : (window as any).route(`admin.${name}`)
  } catch (e) {
    return param !== undefined ? (window as any).route(name, param) : (window as any).route(name)
  }
}

const getFacStatus = (fac: any) => {
  if (fac.status) return fac.status
  return fac.is_active === false || fac.is_active === 0 ? 'inactive' : 'active'
}

const toggleSort = (key: string) => {
  if (sortKey.value === key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortOrder.value = 'asc'
  }
}

const toggleFacultyStatus = (fac: any) => {
  const currentStatus = getFacStatus(fac)
  const newStatus = currentStatus === 'inactive' ? 'active' : 'inactive'
  const newIsActive = newStatus === 'active'

  if (fac.id) {
    router.put(
      targetRoute('academic-structure.faculties.update', fac.id),
      { ...fac, status: newStatus, is_active: newIsActive },
      {
        preserveScroll: true,
        onSuccess: () => {
          fac.status = newStatus
          fac.is_active = newIsActive
          triggerToast(
            'បច្ចុប្បន្នភាពស្ថានភាពជោគជ័យ',
            `ស្ថានភាពមហាវិទ្យាល័យ "${fac.name}" ត្រូវបានផ្លាស់ប្តូរទៅជា ${newStatus.toUpperCase()}`
          )
        },
        onError: () => {
          triggerToast('មានបញ្ហាក្នុងការផ្លាស់ប្តូរស្ថានភាព', 'សូមព្យាយាមម្តងទៀត', 'warning')
        }
      }
    )
  } else {
    fac.status = newStatus
    fac.is_active = newIsActive
  }
}

const isModalOpen = ref(false)
const editingFaculty = ref<any>(null)
const form = ref({
  code: '',
  name: '',
  name_kh: '',
  dean: '',
  email: '',
  est_year: 2024,
  status: 'active',
  is_active: true,
  description: ''
})

const filteredAndSortedFaculties = computed(() => {
  const list = Array.isArray(props.faculties) && props.faculties.length > 0 ? props.faculties : defaultFaculties

  let result = list.filter(fac => {
    const name = fac.name || ''
    const nameKh = fac.name_kh || ''
    const code = fac.code || ''
    const dean = fac.dean || ''
    const q = searchQuery.value.toLowerCase()
    const matchesSearch = name.toLowerCase().includes(q) ||
                          nameKh.includes(searchQuery.value) ||
                          code.toLowerCase().includes(q) ||
                          dean.toLowerCase().includes(q)
    const currentFacStatus = getFacStatus(fac)
    const matchesStatus = statusFilter.value === 'all' || currentFacStatus === statusFilter.value
    return matchesSearch && matchesStatus
  })

  // Sorting logic
  result.sort((a, b) => {
    let valA = a[sortKey.value]
    let valB = b[sortKey.value]

    if (sortKey.value === 'status') {
      valA = getFacStatus(a)
      valB = getFacStatus(b)
    } else if (sortKey.value === 'depts_count') {
      valA = a.departments_count ?? a.depts_count ?? 0
      valB = b.departments_count ?? b.depts_count ?? 0
    } else if (sortKey.value === 'majors_count') {
      valA = a.majors_count ?? 0
      valB = b.majors_count ?? 0
    } else if (sortKey.value === 'students_count') {
      valA = a.students_count ?? 0
      valB = b.students_count ?? 0
    }

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
  return Math.ceil(filteredAndSortedFaculties.value.length / itemsPerPage.value) || 1
})

const paginatedFaculties = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredAndSortedFaculties.value.slice(start, start + itemsPerPage.value)
})

const openAddModal = () => {
  editingFaculty.value = null
  const currentList = Array.isArray(props.faculties) ? props.faculties : defaultFaculties
  form.value = {
    code: `FAC-00${currentList.length + 1}`,
    name: '',
    name_kh: '',
    dean: '',
    email: '',
    est_year: 2025,
    status: 'active',
    is_active: true,
    description: ''
  }
  isModalOpen.value = true
}

const openEditModal = (faculty: any) => {
  editingFaculty.value = faculty
  form.value = {
    ...faculty,
    status: getFacStatus(faculty),
    is_active: getFacStatus(faculty) === 'active'
  }
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

const saveFaculty = () => {
  if (!form.value.name) {
    triggerToast('សូមបញ្ចូលឈ្មោះមហាវិទ្យាល័យ', 'សូមបញ្ចូល Faculty Name (English) មុនពេលរក្សាទុក', 'warning')
    return
  }

  isSubmitting.value = true
  const facName = form.value.name || 'មហាវិទ្យាល័យ'

  if (editingFaculty.value?.id) {
    router.put(targetRoute('academic-structure.faculties.update', editingFaculty.value.id), form.value, {
      preserveScroll: true,
      onSuccess: () => {
        isSubmitting.value = false
        isModalOpen.value = false
        triggerToast(
          'រក្សាទុកបានជោគជ័យ',
          `ព័ត៌មានមហាវិទ្យាល័យ "${facName}" ត្រូវបានបច្ចុប្បន្នភាពដោយជោគជ័យ`
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
    router.post(targetRoute('academic-structure.faculties.store'), form.value, {
      preserveScroll: true,
      onSuccess: () => {
        isSubmitting.value = false
        isModalOpen.value = false
        triggerToast(
          'បង្កើតមហាវិទ្យាល័យបានជោគជ័យ',
          `មហាវិទ្យាល័យ "${facName}" ត្រូវបានបង្កើតក្នុងប្រព័ន្ធដោយជោគជ័យ`
        )
      },
      onError: () => {
        isSubmitting.value = false
        triggerToast(
          'មានបញ្ហាក្នុងការបង្កើតមហាវិទ្យាល័យ',
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

const deleteFaculty = (faculty: any) => {
  if (confirm(`Are you sure you want to delete '${faculty.name}'?`)) {
    router.delete(targetRoute('academic-structure.faculties.destroy', faculty.id), {
      preserveScroll: true,
      onSuccess: () => {
        triggerToast(
          'លុបមហាវិទ្យាល័យបានជោគជ័យ',
          `មហាវិទ្យាល័យ "${faculty.name}" ត្រូវបានលុបចេញពីប្រព័ន្ធដោយជោគជ័យ`
        )
      },
      onError: () => {
        triggerToast(
          'មានបញ្ហាក្នុងការលុប',
          'មិនអាចលុបមហាវិទ្យាល័យនេះបានទេ',
          'warning'
        )
      }
    })
  }
}
</script>

<template>
  <AdminLayout title="Faculties — Academic Structure">
    <div class="space-y-5 font-sans mb-8">
      <!-- Shared Navigation Header -->
      <AcademicModuleHeader activeTab="faculties" :summaryStats="props.summaryStats" />

      <!-- OVERVIEW STATS CARDS (Soft Glassmorphic Glowing Border Frames) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Card 1: Total Faculties -->
        <div class="relative group p-4.5 bg-gradient-to-b from-slate-900/90 via-slate-900/70 to-sky-950/20 border border-sky-500/20 hover:border-sky-500/35 rounded-2xl backdrop-blur-xl transition-all duration-300 shadow-lg hover:shadow-sky-500/10 space-y-1 overflow-hidden">
          <div class="absolute -right-6 -top-6 w-20 h-20 bg-sky-500/10 rounded-full blur-xl group-hover:bg-sky-500/20 transition-all pointer-events-none"></div>
          <div class="flex items-center justify-between">
            <div class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
              <span>🏛️</span>
              <span>Total Faculties</span>
            </div>
            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-sky-500/10 text-sky-300 border border-sky-500/20">College</span>
          </div>
          <div class="text-3xl font-black text-white tracking-tight pt-1">
            {{ filteredAndSortedFaculties.length }}
          </div>
          <div class="text-xs text-sky-300/90 font-medium flex items-center gap-1.5 pt-0.5">
            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
            <span>Top-Level Colleges</span>
          </div>
        </div>

        <!-- Card 2: Total Departments -->
        <div class="relative group p-4.5 bg-gradient-to-b from-slate-900/90 via-slate-900/70 to-indigo-950/20 border border-indigo-500/20 hover:border-indigo-500/35 rounded-2xl backdrop-blur-xl transition-all duration-300 shadow-lg hover:shadow-indigo-500/10 space-y-1 overflow-hidden">
          <div class="absolute -right-6 -top-6 w-20 h-20 bg-indigo-500/10 rounded-full blur-xl group-hover:bg-indigo-500/20 transition-all pointer-events-none"></div>
          <div class="flex items-center justify-between">
            <div class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
              <span>🏢</span>
              <span>Total Departments</span>
            </div>
            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">Sub-units</span>
          </div>
          <div class="text-3xl font-black text-indigo-300 tracking-tight pt-1">
            12
          </div>
          <div class="text-xs text-indigo-200/90 font-medium flex items-center gap-1.5 pt-0.5">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
            <span>Under Faculties</span>
          </div>
        </div>

        <!-- Card 3: Total Programs -->
        <div class="relative group p-4.5 bg-gradient-to-b from-slate-900/90 via-slate-900/70 to-cyan-950/20 border border-cyan-500/20 hover:border-cyan-500/35 rounded-2xl backdrop-blur-xl transition-all duration-300 shadow-lg hover:shadow-cyan-500/10 space-y-1 overflow-hidden">
          <div class="absolute -right-6 -top-6 w-20 h-20 bg-cyan-500/10 rounded-full blur-xl group-hover:bg-cyan-500/20 transition-all pointer-events-none"></div>
          <div class="flex items-center justify-between">
            <div class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
              <span>🎓</span>
              <span>Total Programs</span>
            </div>
            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20">Degrees</span>
          </div>
          <div class="text-3xl font-black text-cyan-300 tracking-tight pt-1">
            5
          </div>
          <div class="text-xs text-cyan-200/90 font-medium flex items-center gap-1.5 pt-0.5">
            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
            <span>Degree Majors</span>
          </div>
        </div>

        <!-- Card 4: Students Enrolled -->
        <div class="relative group p-4.5 bg-gradient-to-b from-slate-900/90 via-slate-900/70 to-emerald-950/20 border border-emerald-500/20 hover:border-emerald-500/35 rounded-2xl backdrop-blur-xl transition-all duration-300 shadow-lg hover:shadow-emerald-500/10 space-y-1 overflow-hidden">
          <div class="absolute -right-6 -top-6 w-20 h-20 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition-all pointer-events-none"></div>
          <div class="flex items-center justify-between">
            <div class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
              <span>👥</span>
              <span>Students Enrolled</span>
            </div>
            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">Active</span>
          </div>
          <div class="text-3xl font-black text-emerald-300 tracking-tight pt-1">
            2,458
          </div>
          <div class="text-xs text-emerald-200/90 font-medium flex items-center gap-1.5 pt-0.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
            <span>Active Students</span>
          </div>
        </div>
      </div>

      <!-- SEARCH, FILTER & ACTION TOOLBAR (Prominent Search Bar & High Visual Hierarchy) -->
      <div class="flex flex-col md:flex-row items-center justify-between gap-3 p-4 bg-slate-900/80 border border-slate-700/80 rounded-2xl backdrop-blur-xl shadow-lg">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto flex-1">
          <!-- Prominent High-Hierarchy Table Search Box -->
          <div class="relative w-full sm:w-80">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-sky-400 text-sm">
              🔍
            </div>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search faculty name, code, dean..."
              class="w-full bg-slate-950 border border-slate-700/80 focus:border-sky-400 focus:ring-2 focus:ring-sky-500/20 rounded-xl pl-9 pr-9 py-2.5 text-xs text-white placeholder-slate-400 shadow-inner transition-all font-medium"
            />
            <button 
              v-if="searchQuery"
              @click="searchQuery = ''"
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white transition-colors"
            >
              ✕
            </button>
          </div>

          <select v-model="statusFilter" class="w-full sm:w-auto bg-slate-950 border border-slate-700/80 focus:border-sky-400 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none transition-colors font-medium">
            <option value="all">Status: All Statuses</option>
            <option value="active">🟢 Active Only</option>
            <option value="inactive">🔴 Inactive Only</option>
          </select>
        </div>

        <!-- Add New Faculty Action Button -->
        <button 
          @click="openAddModal" 
          class="w-full md:w-auto px-4.5 py-2.5 bg-sky-500/15 hover:bg-sky-500/30 text-sky-200 border border-sky-500/40 hover:border-sky-500/70 font-bold text-xs rounded-xl backdrop-blur-md transition-all flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer shadow-xs"
        >
          <span>➕</span>
          <span>Add New Faculty</span>
        </button>
      </div>

      <!-- FACULTIES DATA TABLE -->
      <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/80 backdrop-blur-xl shadow-xl">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-800 bg-slate-800/60 text-xs font-extrabold text-slate-200 uppercase whitespace-nowrap select-none">
              <th class="py-4 px-4 w-12 text-slate-400">#</th>
              
              <!-- Sortable Faculty Name -->
              <th @click="toggleSort('name')" class="py-4 px-4 cursor-pointer hover:text-white transition-colors group">
                <div class="flex items-center gap-2">
                  <span>🏛️ Faculty Name</span>
                  <span :class="[sortKey === 'name' ? 'bg-sky-500/20 text-sky-300 font-extrabold border border-sky-500/40 px-1.5 py-0.5 rounded' : 'text-slate-500 group-hover:text-slate-300', 'text-[11px] transition-all']">
                    {{ sortKey === 'name' ? (sortOrder === 'asc' ? '▲ ASC' : '▼ DESC') : '↕' }}
                  </span>
                </div>
              </th>

              <!-- Sortable Dean -->
              <th @click="toggleSort('dean')" class="py-4 px-4 cursor-pointer hover:text-white transition-colors group">
                <div class="flex items-center gap-2">
                  <span>👤 Dean & Contact</span>
                  <span :class="[sortKey === 'dean' ? 'bg-sky-500/20 text-sky-300 font-extrabold border border-sky-500/40 px-1.5 py-0.5 rounded' : 'text-slate-500 group-hover:text-slate-300', 'text-[11px] transition-all']">
                    {{ sortKey === 'dean' ? (sortOrder === 'asc' ? '▲ ASC' : '▼ DESC') : '↕' }}
                  </span>
                </div>
              </th>

              <!-- Sortable Depts -->
              <th @click="toggleSort('depts_count')" class="py-4 px-4 text-center cursor-pointer hover:text-white transition-colors group">
                <div class="flex items-center justify-center gap-1.5">
                  <span>🏢 Depts</span>
                  <span :class="[sortKey === 'depts_count' ? 'bg-indigo-500/20 text-indigo-300 font-extrabold border border-indigo-500/40 px-1.5 py-0.5 rounded' : 'text-slate-500 group-hover:text-slate-300', 'text-[11px] transition-all']">
                    {{ sortKey === 'depts_count' ? (sortOrder === 'asc' ? '▲' : '▼') : '↕' }}
                  </span>
                </div>
              </th>

              <!-- Sortable Majors -->
              <th @click="toggleSort('majors_count')" class="py-4 px-4 text-center cursor-pointer hover:text-white transition-colors group">
                <div class="flex items-center justify-center gap-1.5">
                  <span>🎓 Majors</span>
                  <span :class="[sortKey === 'majors_count' ? 'bg-cyan-500/20 text-cyan-300 font-extrabold border border-cyan-500/40 px-1.5 py-0.5 rounded' : 'text-slate-500 group-hover:text-slate-300', 'text-[11px] transition-all']">
                    {{ sortKey === 'majors_count' ? (sortOrder === 'asc' ? '▲' : '▼') : '↕' }}
                  </span>
                </div>
              </th>

              <!-- Sortable Students -->
              <th @click="toggleSort('students_count')" class="py-4 px-4 text-center cursor-pointer hover:text-white transition-colors group">
                <div class="flex items-center justify-center gap-1.5">
                  <span>👨‍🎓 Students</span>
                  <span :class="[sortKey === 'students_count' ? 'bg-emerald-500/20 text-emerald-300 font-extrabold border border-emerald-500/40 px-1.5 py-0.5 rounded' : 'text-slate-500 group-hover:text-slate-300', 'text-[11px] transition-all']">
                    {{ sortKey === 'students_count' ? (sortOrder === 'asc' ? '▲' : '▼') : '↕' }}
                  </span>
                </div>
              </th>

              <!-- Sortable Status -->
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
            <tr v-for="(fac, idx) in paginatedFaculties" :key="fac.id" class="hover:bg-slate-800/50 transition-all">
              <td class="py-4 px-4 font-bold text-slate-300 whitespace-nowrap">
                {{ String((currentPage - 1) * itemsPerPage + idx + 1).padStart(2, '0') }}
              </td>

              <!-- Faculty Name & High-Contrast Code Badge -->
              <td class="py-4 px-4 whitespace-nowrap">
                <div>
                  <div class="font-black text-white text-sm tracking-tight">{{ fac.name }}</div>
                  <div class="text-xs text-sky-300 font-medium mt-0.5">{{ fac.name_kh || fac.name }}</div>
                  <div class="flex items-center gap-2 mt-1.5">
                    <span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700 text-sky-300 font-bold text-[11px]">
                      Code: {{ fac.code }}
                    </span>
                    <span class="text-xs text-slate-300 font-medium">
                      Est. {{ fac.est_year || 2010 + ((fac.id || 1) % 5) }}
                    </span>
                  </div>
                </div>
              </td>

              <!-- Dean & Contact -->
              <td class="py-4 px-4 whitespace-nowrap">
                <div class="text-white font-bold text-xs">{{ fac.dean || (fac.id == 1 ? 'Dr. Sok Vichea' : fac.id == 2 ? 'Dr. Keo Samnang' : fac.id == 3 ? 'Dr. Chan Srey' : fac.id == 4 ? 'Dr. Heng Vuthy' : 'Dr. Pov Rithy') }}</div>
                <div class="text-xs text-slate-300 mt-0.5 font-medium">{{ fac.email || ('faculty' + (fac.id || 1) + '@elms.edu') }}</div>
              </td>

              <!-- Counts in Clean Bold Sans-Serif Font -->
              <td class="py-4 px-4 text-center font-extrabold text-indigo-300 whitespace-nowrap text-sm">
                {{ fac.departments_count ?? fac.depts_count ?? 2 }}
              </td>

              <td class="py-4 px-4 text-center font-extrabold text-cyan-300 whitespace-nowrap text-sm">
                {{ fac.majors_count ?? 1 }}
              </td>

              <td class="py-4 px-4 text-center font-extrabold text-emerald-300 whitespace-nowrap text-sm">
                {{ (fac.students_count ?? (400 + ((fac.id || 1) * 35))).toLocaleString() }}
              </td>

              <!-- Clickable Interactive Status Toggle Badge -->
              <td class="py-4 px-4 text-center whitespace-nowrap">
                <button
                  @click="toggleFacultyStatus(fac)"
                  title="Click to toggle Status"
                  :class="[getFacStatus(fac) === 'inactive' ? 'bg-rose-500/20 text-rose-300 border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 hover:bg-emerald-500/30']"
                  class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold border transition-all cursor-pointer hover:scale-105 shadow-xs"
                >
                  <img 
                    :src="getFacStatus(fac) === 'inactive' ? inactiveStatusIconUrl : activeStatusIconUrl" 
                    :alt="getFacStatus(fac)" 
                    class="w-4 h-4 object-contain shrink-0 filter drop-shadow-xs" 
                  />
                  <span>{{ getFacStatus(fac) === 'inactive' ? 'Inactive' : 'Active' }}</span>
                  <span class="text-[10px] opacity-70">▾</span>
                </button>
              </td>

              <!-- Action Buttons (Clear Visual Hierarchy: Edit vs Ghost Red Delete) -->
              <td class="py-4 px-4 text-right whitespace-nowrap">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="openEditModal(fac)"
                    class="px-3.5 py-1.5 bg-sky-500/15 hover:bg-sky-500/30 text-sky-300 border border-sky-500/40 rounded-xl font-bold whitespace-nowrap inline-flex items-center gap-1.5 transition-all cursor-pointer shadow-xs"
                  >
                    <span>✏️</span>
                    <span>Edit</span>
                  </button>

                  <button
                    @click="deleteFaculty(fac)"
                    class="px-3.5 py-1.5 bg-rose-500/10 hover:bg-rose-500/25 text-rose-400 border border-rose-500/30 hover:border-rose-500/60 rounded-xl font-medium whitespace-nowrap inline-flex items-center gap-1.5 transition-all cursor-pointer shadow-xs"
                  >
                    <span>🗑️</span>
                    <span>Delete</span>
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="filteredAndSortedFaculties.length === 0">
              <td colspan="8" class="py-10 text-center text-slate-300 text-xs">
                No faculties found matching your search query.
              </td>
            </tr>
          </tbody>
        </table>

        <!-- PAGINATION & ROW COUNT FOOTER -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 border-t border-slate-800 bg-slate-900/60 text-xs text-slate-300">
          <div class="flex items-center gap-2">
            <span>Show</span>
            <select v-model="itemsPerPage" class="bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1 text-xs text-white focus:outline-none focus:border-sky-500">
              <option :value="5">5</option>
              <option :value="10">10</option>
              <option :value="25">25</option>
            </select>
            <span>entries per page • Showing <strong class="text-white font-bold">{{ filteredAndSortedFaculties.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0 }}</strong> to <strong class="text-white font-bold">{{ Math.min(currentPage * itemsPerPage, filteredAndSortedFaculties.length) }}</strong> of <strong class="text-white font-bold">{{ filteredAndSortedFaculties.length }}</strong> Faculties</span>
          </div>

          <div class="flex items-center gap-2">
            <button
              @click="currentPage = Math.max(1, currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-3.5 py-1.5 rounded-xl border border-slate-700 bg-slate-800 text-slate-200 hover:text-white hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all font-semibold cursor-pointer"
            >
              ◀ Prev
            </button>

            <span class="px-3.5 py-1.5 rounded-xl bg-sky-500/20 border border-sky-500/30 text-sky-300 font-extrabold">
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

      <!-- ADD / EDIT FACULTY FORM MODAL (Soft Glassmorphic Soothing Dialog) -->
      <div v-if="isModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-xl z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-700/80 rounded-3xl max-w-xl w-full p-6 space-y-5 shadow-2xl overflow-y-auto max-h-[90vh] transition-all">
          <!-- Header -->
          <div class="flex items-center justify-between border-b border-slate-800 pb-3.5">
            <div class="flex items-center gap-3">
              <img :src="facultyIconUrl" class="w-9 h-9 object-contain rounded-xl shadow-xs" alt="Faculty" />
              <div>
                <h3 class="text-base font-black text-white tracking-tight">
                  {{ editingFaculty ? 'EDIT FACULTY RECORD' : 'ADD NEW FACULTY' }}
                </h3>
                <p class="text-[11px] text-slate-400 font-medium">Specify details below to save faculty information</p>
              </div>
            </div>
            <button @click="isModalOpen = false" class="w-8 h-8 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer">✕</button>
          </div>

          <!-- Form Fields -->
          <div class="space-y-4 text-xs">
            <div class="grid grid-cols-2 gap-3.5">
              <div>
                <label class="block font-bold text-slate-300 mb-1.5">Faculty Code</label>
                <input v-model="form.code" type="text" class="w-full bg-slate-950/70 border border-slate-700/80 focus:border-sky-400 focus:ring-2 focus:ring-sky-500/20 rounded-xl px-3.5 py-2.5 text-white font-bold tracking-wide" />
              </div>
              <div>
                <label class="block font-bold text-slate-300 mb-1.5">Established Year</label>
                <input v-model="form.est_year" type="number" class="w-full bg-slate-950/70 border border-slate-700/80 focus:border-sky-400 focus:ring-2 focus:ring-sky-500/20 rounded-xl px-3.5 py-2.5 text-white font-bold" />
              </div>
            </div>

            <div>
              <label class="block font-bold text-slate-300 mb-1.5">Faculty Name (English)</label>
              <input v-model="form.name" type="text" placeholder="e.g. Faculty of Computing" class="w-full bg-slate-950/70 border border-slate-700/80 focus:border-sky-400 focus:ring-2 focus:ring-sky-500/20 rounded-xl px-3.5 py-2.5 text-white placeholder-slate-500 font-medium" />
            </div>

            <div>
              <label class="block font-bold text-slate-300 mb-1.5">Faculty Name (Khmer)</label>
              <input v-model="form.name_kh" type="text" placeholder="e.g. មហាវិទ្យាល័យ វិទ្យាសាស្ត្រកុំព្យូទ័រ" class="w-full bg-slate-950/70 border border-slate-700/80 focus:border-sky-400 focus:ring-2 focus:ring-sky-500/20 rounded-xl px-3.5 py-2.5 text-white placeholder-slate-500 font-medium" />
            </div>

            <div class="grid grid-cols-2 gap-3.5">
              <div>
                <label class="block font-bold text-slate-300 mb-1.5">Dean Name</label>
                <input v-model="form.dean" type="text" placeholder="Dr. Sok Vichea" class="w-full bg-slate-950/70 border border-slate-700/80 focus:border-sky-400 focus:ring-2 focus:ring-sky-500/20 rounded-xl px-3.5 py-2.5 text-white placeholder-slate-500 font-medium" />
              </div>
              <div>
                <label class="block font-bold text-slate-300 mb-1.5">Email Address</label>
                <input v-model="form.email" type="email" placeholder="computing@elms.edu" class="w-full bg-slate-950/70 border border-slate-700/80 focus:border-sky-400 focus:ring-2 focus:ring-sky-500/20 rounded-xl px-3.5 py-2.5 text-white placeholder-slate-500 font-medium" />
              </div>
            </div>

            <div>
              <label class="block font-bold text-slate-300 mb-1.5">Description / Overview</label>
              <textarea v-model="form.description" rows="3" placeholder="Additional details..." class="w-full bg-slate-950/70 border border-slate-700/80 focus:border-sky-400 focus:ring-2 focus:ring-sky-500/20 rounded-xl px-3.5 py-2.5 text-white placeholder-slate-500 font-medium"></textarea>
            </div>
          </div>

          <!-- Footer Actions -->
          <div class="flex items-center justify-end gap-3 border-t border-slate-800/80 pt-4">
            <button @click="isModalOpen = false" :disabled="isSubmitting" class="px-4.5 py-2.5 bg-slate-800/80 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs rounded-xl transition-all cursor-pointer inline-flex items-center gap-2 disabled:opacity-50">
              <img :src="cancelIconUrl" class="w-4 h-4 object-contain opacity-80" alt="Cancel" />
              <span>Cancel</span>
            </button>
            <button @click="saveFaculty" :disabled="isSubmitting" class="px-5 py-2.5 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-sky-600/25 transition-all cursor-pointer inline-flex items-center gap-2 disabled:opacity-50">
              <svg v-if="isSubmitting" class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <img v-else :src="saveIconUrl" class="w-4 h-4 object-contain brightness-200" alt="Save" />
              <span>{{ isSubmitting ? (editingFaculty ? 'កំពុងរក្សាទុក...' : 'កំពុងបង្កើត...') : (editingFaculty ? 'Save Faculty' : 'Create Faculty') }}</span>
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
