<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import CourseModuleHeader from '@/Components/Admin/CourseModuleHeader.vue'

const props = withDefaults(defineProps<{
  courses?: any[]
  summaryStats?: any
}>(), {
  courses: () => [],
  summaryStats: () => ({})
})

const viewMode = ref<'table' | 'grid'>('table')
const searchQuery = ref('')
const selectedIds = ref<number[]>([])

const activeDropdownId = ref<number | null>(null)
const toggleDropdown = (id: number) => {
  activeDropdownId.value = activeDropdownId.value === id ? null : id
}
const closeDropdown = () => {
  activeDropdownId.value = null
}

const currentPage = ref(1)
const pageSize = ref(10)

const isEditModalOpen = ref(false)
const isDetailsModalOpen = ref(false)
const isNotifyModalOpen = ref(false)

const selectedCourse = ref<any>(null)
const editForm = ref({
  title: '',
  teacher: '',
  schedule: '',
  price: 25,
  capacity: 60,
})

const notifyForm = ref({
  subject: 'Class Announcement',
  message: 'Please be reminded of our upcoming live session on Zoom.',
})

const coursesList = computed(() => {
  return Array.isArray(props.courses) && props.courses.length > 0 ? props.courses : [
    { id: 1, code: 'CRS-IT-CP101', title: 'C Programming Basics', major: 'IT & Networking', teacher: 'Mr. Sophea', schedule: 'Mon/Wed 2:00-4:00 PM', students: '52/60', price: 25, status: 'published', is_full: false },
    { id: 5, code: 'CRS-AG-PS101', title: 'Plant Science Advanced', major: 'Agronomy', teacher: 'Mr. Vuthy', schedule: 'Tue/Thu 8:00-10:00 AM', students: '60/60', price: 30, status: 'full', is_full: true },
    { id: 4, code: 'CRS-TM-TB101', title: 'Tourism Basics', major: 'Tourism Management', teacher: 'Mr. Long', schedule: 'Sat/Sun 9:00-11:00 AM', students: '25/50', price: 25, status: 'draft', is_full: false },
    { id: 7, code: 'CRS-ENG-EW101', title: 'English Writing Masterclass', major: 'English Literature', teacher: 'Ms. Srey', schedule: 'Friday 3:00-5:00 PM', students: '38/45', price: 20, status: 'published', is_full: false },
  ]
})

const filteredCourses = computed(() => {
  return coursesList.value.filter(crs => {
    return crs.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
           crs.code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
           crs.teacher.toLowerCase().includes(searchQuery.value.toLowerCase())
  })
})

watch([searchQuery, pageSize], () => {
  currentPage.value = 1
  selectedIds.value = []
})

const totalPages = computed(() => Math.ceil(filteredCourses.value.length / pageSize.value) || 1)
const paginatedCourses = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value
  return filteredCourses.value.slice(start, start + pageSize.value)
})

const isAllSelected = computed({
  get: () => paginatedCourses.value.length > 0 && paginatedCourses.value.every(crs => selectedIds.value.includes(crs.id)),
  set: (val: boolean) => {
    if (val) {
      const pageIds = paginatedCourses.value.map(crs => crs.id)
      selectedIds.value = Array.from(new Set([...selectedIds.value, ...pageIds]))
    } else {
      const pageIds = paginatedCourses.value.map(crs => crs.id)
      selectedIds.value = selectedIds.value.filter(id => !pageIds.includes(id))
    }
  }
})

const toggleSelectAll = () => {
  isAllSelected.value = !isAllSelected.value
}

const openEditModal = (crs: any) => {
  closeDropdown()
  selectedCourse.value = crs
  editForm.value = {
    title: crs.title,
    teacher: crs.teacher,
    schedule: crs.schedule,
    price: crs.price,
    capacity: 60,
  }
  isEditModalOpen.value = true
}

const openDetailsModal = (crs: any) => {
  closeDropdown()
  selectedCourse.value = crs
  isDetailsModalOpen.value = true
}

const openNotifyModal = (crs: any) => {
  closeDropdown()
  selectedCourse.value = crs
  notifyForm.value = {
    subject: `Announcement for ${crs.title}`,
    message: 'Please be reminded of our upcoming live class on Monday at 2:00 PM.',
  }
  isNotifyModalOpen.value = true
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

const saveEditCourse = () => {
  isSubmitting.value = true
  const courseTitle = editForm.value.title || selectedCourse.value?.title || 'វគ្គសិក្សា'

  if (selectedCourse.value?.id) {
    router.put(`/admin/course-module/update/${selectedCourse.value.id}`, editForm.value, {
      preserveScroll: true,
      onSuccess: () => {
        isSubmitting.value = false
        isEditModalOpen.value = false
        triggerToast('រក្សាទុកបានជោគជ័យ', `កាលវិភាគ និងព័ត៌មានវគ្គសិក្សា "${courseTitle}" ត្រូវបានបច្ចុប្បន្នភាព`)
      },
      onError: () => {
        isSubmitting.value = false
        triggerToast('មានបញ្ហាក្នុងការរក្សាទុក', 'សូមពិនិត្យមើលព័ត៌មានដែលបានបញ្ចូលឡើងវិញ', 'warning')
      },
      onFinish: () => { isSubmitting.value = false }
    })
  } else {
    isSubmitting.value = false
    isEditModalOpen.value = false
  }
}

const sendNotification = () => {
  triggerToast('ផ្ញើសារជូនដំណឹងជោគជ័យ 🎉', `សារជូនដំណឹងត្រូវបានផ្ញើទៅសិស្សទាំងអស់ក្នុងវគ្គសិក្សា "${selectedCourse.value?.title}"!`)
  isNotifyModalOpen.value = false
}
</script>

<template>
  <AdminLayout title="Teacher-Led Courses — Course & Subject Management">
    <div class="space-y-4 font-sans" @click="closeDropdown">
      <!-- Shared Header (1-Layer Clean Header) -->
      <CourseModuleHeader activeTab="teacher-led" :summaryStats="props.summaryStats" />

      <!-- CONTROLS BAR WITH GRID / TABLE VIEW TOGGLE -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-3.5 bg-slate-900/60 border border-slate-800 rounded-2xl backdrop-blur-xl">
        <div class="w-full sm:w-80">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="🔍 Search title, code, teacher..."
            class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-violet-500"
          />
        </div>

        <!-- View Mode Toggle -->
        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
          <div class="flex items-center gap-1 p-1 bg-slate-950 rounded-xl border border-slate-800 text-xs font-semibold">
            <button
              @click="viewMode = 'table'"
              class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1"
              :class="viewMode === 'table' ? 'bg-violet-600/30 text-violet-300 border border-violet-500/40' : 'text-slate-400 hover:text-white'"
            >
              <span>📊</span> Table View
            </button>
            <button
              @click="viewMode = 'grid'"
              class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1"
              :class="viewMode === 'grid' ? 'bg-violet-600/30 text-violet-300 border border-violet-500/40' : 'text-slate-400 hover:text-white'"
            >
              <span>🗂️</span> Grid Cards
            </button>
          </div>
        </div>
      </div>

      <!-- BULK ACTIONS TOOLBAR -->
      <div v-if="selectedIds.length > 0" class="flex items-center justify-between p-3 bg-violet-950/80 border border-violet-500/40 rounded-2xl text-xs backdrop-blur-xl animate-fade-in">
        <div class="flex items-center gap-2 text-violet-200 font-bold">
          <span>☑️</span>
          <span>{{ selectedIds.length }} course(s) selected</span>
        </div>
        <div class="flex items-center gap-2">
          <button @click="selectedIds = []" class="px-2.5 py-1.5 bg-slate-800 text-slate-300 hover:text-white rounded-xl font-bold">
            ✕ Clear Selection
          </button>
        </div>
      </div>

      <!-- TABLE VIEW (DEFAULT COMPACT VIEW ABOVE THE FOLD) -->
      <div v-if="viewMode === 'table'" class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-xl">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-800 bg-slate-800/40 text-[11px] font-bold text-slate-400 uppercase whitespace-nowrap">
              <th class="py-3 px-3 w-10 text-center">
                <input
                  type="checkbox"
                  :checked="isAllSelected"
                  @change="toggleSelectAll"
                  class="rounded border-slate-700 bg-slate-950 text-violet-500 focus:ring-violet-500/20"
                />
              </th>
              <th class="py-3.5 px-4 w-12">#</th>
              <th class="py-3.5 px-4">🎥 Course Title</th>
              <th class="py-3.5 px-4">👨‍🏫 Teacher</th>
              <th class="py-3.5 px-4">📅 Schedule</th>
              <th class="py-3.5 px-4 text-center">Capacity</th>
              <th class="py-3.5 px-4 text-center">Price</th>
              <th class="py-3.5 px-4 text-center">Status</th>
              <th class="py-3.5 px-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800 text-xs">
            <tr v-if="paginatedCourses.length === 0">
              <td colspan="9" class="py-8 text-center text-slate-400">
                No teacher-led courses found.
              </td>
            </tr>
            <tr
              v-for="(crs, idx) in paginatedCourses"
              :key="crs.id"
              class="hover:bg-slate-800/30 transition-all"
              :class="{ 'bg-violet-950/20': selectedIds.includes(crs.id) }"
            >
              <td class="py-4 px-3 text-center">
                <input
                  type="checkbox"
                  :value="crs.id"
                  v-model="selectedIds"
                  class="rounded border-slate-700 bg-slate-950 text-violet-500 focus:ring-violet-500/20"
                />
              </td>

              <td class="py-4 px-4 font-sans text-slate-400 font-semibold whitespace-nowrap">
                {{ String((currentPage - 1) * pageSize + idx + 1).padStart(2, '0') }}
              </td>

              <td class="py-4 px-4 whitespace-nowrap">
                <div class="font-bold text-white text-sm">{{ crs.title }}</div>
                <div class="text-[11px] font-sans text-slate-400 flex items-center gap-1.5 mt-0.5">
                  <span class="px-1.5 py-0.5 bg-slate-950 rounded text-slate-300 font-semibold border border-slate-800">{{ crs.code }}</span>
                  <span>• Major: {{ crs.major }}</span>
                </div>
              </td>

              <td class="py-4 px-4 whitespace-nowrap font-sans text-slate-200 font-semibold">
                {{ crs.teacher }}
              </td>

              <td class="py-4 px-4 whitespace-nowrap font-sans text-violet-300 font-medium">
                {{ crs.schedule }}
              </td>

              <td class="py-4 px-4 text-center whitespace-nowrap font-sans font-semibold">
                <span class="px-2.5 py-1 rounded-xl bg-slate-950 border border-slate-800 text-sky-300 text-xs">
                  👥 {{ crs.students }}
                </span>
              </td>

              <td class="py-4 px-4 text-center font-bold font-sans text-amber-300 whitespace-nowrap">
                ${{ crs.price }} USD
              </td>

              <td class="py-4 px-4 text-center whitespace-nowrap">
                <span v-if="crs.is_full" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-500/20 text-red-300 border border-red-500/30">
                  🔴 Full
                </span>
                <span v-else-if="crs.status === 'published'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                  🟢 Open
                </span>
                <span v-else class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                  🟡 Draft
                </span>
              </td>

              <!-- STANDARDIZED ACTION BUTTONS -->
              <td class="py-4 px-4 text-right whitespace-nowrap relative" @click.stop>
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    @click="openEditModal(crs)"
                    class="px-3 py-1.5 bg-violet-600/20 hover:bg-violet-500/30 text-violet-300 border border-violet-500/30 rounded-xl font-bold whitespace-nowrap inline-flex items-center gap-1"
                  >
                    <span>✏️</span>
                    <span>Edit</span>
                  </button>

                  <div class="relative">
                    <button
                      @click="toggleDropdown(crs.id)"
                      class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-bold transition-all border border-slate-700"
                      title="More Options"
                    >
                      <span>⋮</span>
                    </button>

                    <div
                      v-if="activeDropdownId === crs.id"
                      class="absolute right-0 mt-1 w-44 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl z-50 p-1.5 text-xs text-left space-y-1 backdrop-blur-xl animate-fade-in"
                    >
                      <button
                        @click="openDetailsModal(crs)"
                        class="w-full px-3 py-2 text-slate-200 hover:text-white hover:bg-slate-800 rounded-xl flex items-center gap-2 font-medium"
                      >
                        <span>👁️</span> Details
                      </button>

                      <button
                        @click="openNotifyModal(crs)"
                        class="w-full px-3 py-2 text-sky-300 hover:bg-sky-500/10 rounded-xl flex items-center gap-2 font-medium"
                      >
                        <span>📢</span> Notify Students
                      </button>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- GRID CARDS VIEW -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
        <div v-for="crs in paginatedCourses" :key="crs.id" class="p-5 bg-slate-900/60 border border-slate-800 rounded-3xl backdrop-blur-xl space-y-3 hover:border-violet-500/40 transition-all">
          <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-violet-500/20 text-violet-300 border border-violet-500/30">
              🎥 Live Class
            </span>
            <span v-if="crs.is_full" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-500/20 text-red-300 border border-red-500/30">
              🔴 Full Class
            </span>
            <span v-else-if="crs.status === 'published'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
              🟢 Open
            </span>
            <span v-else class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
              🟡 Draft
            </span>
          </div>

          <div>
            <h3 class="text-base font-bold text-white">{{ crs.title }}</h3>
            <p class="text-xs text-slate-400 font-sans">Major: {{ crs.major }} • Code: {{ crs.code }}</p>
          </div>

          <div class="grid grid-cols-2 gap-2 text-xs font-sans">
            <div class="p-2.5 bg-slate-950/60 rounded-xl border border-slate-800">
              <span class="text-[10px] text-slate-500">👨‍🏫 Teacher:</span>
              <div class="font-bold text-slate-200 mt-0.5">{{ crs.teacher }}</div>
            </div>
            <div class="p-2.5 bg-slate-950/60 rounded-xl border border-slate-800">
              <span class="text-[10px] text-slate-500">📅 Schedule:</span>
              <div class="font-bold text-violet-300 mt-0.5">{{ crs.schedule }}</div>
            </div>
            <div class="p-2.5 bg-slate-950/60 rounded-xl border border-slate-800">
              <span class="text-[10px] text-slate-500">👨‍🎓 Capacity:</span>
              <div class="font-bold text-sky-300 mt-0.5">{{ crs.students }}</div>
            </div>
            <div class="p-2.5 bg-slate-950/60 rounded-xl border border-slate-800">
              <span class="text-[10px] text-slate-500">💳 Price:</span>
              <div class="font-bold text-amber-300 mt-0.5">${{ crs.price }} USD via ABA</div>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800/80 text-xs font-sans">
            <button @click="openDetailsModal(crs)" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl font-bold border border-slate-700">
              👁️ Details
            </button>
            <button @click="openEditModal(crs)" class="px-3 py-1.5 bg-violet-600/20 hover:bg-violet-500/30 text-violet-300 rounded-xl font-bold border border-violet-500/30">
              ✏️ Edit
            </button>
            <button @click="openNotifyModal(crs)" class="px-3 py-1.5 bg-sky-600/20 hover:bg-sky-500/30 text-sky-300 rounded-xl font-bold border border-sky-500/30">
              📢 Notify
            </button>
          </div>
        </div>
      </div>

      <!-- PAGINATION CONTROLS -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-3.5 bg-slate-900/60 border border-slate-800 rounded-2xl text-xs text-slate-400 backdrop-blur-xl">
        <div class="flex items-center gap-3">
          <span>Showing <strong class="text-white">{{ filteredCourses.length === 0 ? 0 : (currentPage - 1) * pageSize + 1 }}</strong> to <strong class="text-white">{{ Math.min(currentPage * pageSize, filteredCourses.length) }}</strong> of <strong class="text-white">{{ filteredCourses.length }}</strong> courses</span>
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
              :class="currentPage === p ? 'bg-violet-600 text-white' : 'bg-slate-950 border border-slate-800 text-slate-400 hover:text-white'"
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

      <!-- EDIT MODAL -->
      <div v-if="isEditModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-violet-900/50 rounded-3xl max-w-xl w-full p-6 space-y-4 shadow-2xl">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              <span>✏️ EDIT TEACHER-LED COURSE</span>
            </h3>
            <button @click="isEditModalOpen = false" class="text-slate-400 hover:text-white text-lg">✕</button>
          </div>

          <form @submit.prevent="saveEditCourse" class="space-y-4 text-xs">
            <div>
              <label class="block text-slate-400 mb-1">Course Title</label>
              <input v-model="editForm.title" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-bold" />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-slate-400 mb-1">Main Teacher</label>
                <input v-model="editForm.teacher" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white" />
              </div>
              <div>
                <label class="block text-slate-400 mb-1">Schedule</label>
                <input v-model="editForm.schedule" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-sans" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-slate-400 mb-1">Price ($ USD)</label>
                <input v-model.number="editForm.price" type="number" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-300 font-bold font-sans" />
              </div>
              <div>
                <label class="block text-slate-400 mb-1">Max Capacity</label>
                <input v-model.number="editForm.capacity" type="number" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-sans" />
              </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
              <button type="button" @click="isEditModalOpen = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl font-bold">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white rounded-xl font-bold">Save Changes</button>
            </div>
          </form>
        </div>
      </div>

      <!-- DETAILS MODAL -->
      <div v-if="isDetailsModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 space-y-4 shadow-2xl">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white">🎥 COURSE DETAILS — {{ selectedCourse?.title }}</h3>
            <button @click="isDetailsModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
          </div>

          <div class="space-y-3 text-xs">
            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 font-sans space-y-1">
              <div><span class="text-slate-400">Teacher:</span> <strong class="text-white">{{ selectedCourse?.teacher }}</strong></div>
              <div><span class="text-slate-400">Schedule:</span> <strong class="text-violet-300">{{ selectedCourse?.schedule }}</strong></div>
              <div><span class="text-slate-400">Capacity:</span> <strong class="text-sky-300">{{ selectedCourse?.students }}</strong></div>
              <div><span class="text-slate-400">Price:</span> <strong class="text-amber-300">${{ selectedCourse?.price }} USD</strong></div>
            </div>

            <div class="p-3 bg-indigo-500/10 border border-indigo-500/20 rounded-xl text-indigo-300">
              ✅ Includes: Live Zoom/Meet sessions, Attendance tracking, Assignments, Quizzes & Teacher Feedback.
            </div>
          </div>

          <button @click="isDetailsModalOpen = false" class="w-full py-2 bg-slate-800 text-slate-300 font-bold text-xs rounded-xl">Close</button>
        </div>
      </div>

      <!-- NOTIFY MODAL -->
      <div v-if="isNotifyModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-sky-900/50 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white">📢 NOTIFY ENROLLED STUDENTS</h3>
            <button @click="isNotifyModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
          </div>

          <form @submit.prevent="sendNotification" class="space-y-3 text-xs">
            <div>
              <label class="block text-slate-400 mb-1">Subject</label>
              <input v-model="notifyForm.subject" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white" />
            </div>
            <div>
              <label class="block text-slate-400 mb-1">Message</label>
              <textarea v-model="notifyForm.message" rows="4" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
              <button type="button" @click="isNotifyModalOpen = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl font-bold">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl font-bold">🚀 Send Notification</button>
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
