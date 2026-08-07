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
const isViewModalOpen = ref(false)
const isConvertModalOpen = ref(false)

const selectedCourse = ref<any>(null)
const editForm = ref({
  title: '',
  major: '',
  access_type: 'Public Access',
})
const convertPrice = ref(20)

const coursesList = computed(() => {
  return Array.isArray(props.courses) && props.courses.length > 0 ? props.courses : [
    { id: 3, code: 'CRS-ENG-EG101', title: 'English Grammar Basics', major: 'English Literature', access_type: 'Public Access', students_count: 1200, status: 'published' },
    { id: 9, code: 'CRS-IT-CB100', title: 'Computer Basics & Windows', major: 'IT & Networking', access_type: 'Public Access', students_count: 980, status: 'published' },
    { id: 10, code: 'CRS-TM-TI100', title: 'Tourism Industry Overview', major: 'Tourism Management', access_type: 'Intro Only', students_count: 410, status: 'published' },
    { id: 11, code: 'CRS-SW-SA100', title: 'Social Awareness & Ethics', major: 'Social Work', access_type: 'Scholarship Only', students_count: 548, status: 'published' },
  ]
})

const filteredCourses = computed(() => {
  return coursesList.value.filter(crs => {
    return crs.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
           crs.code.toLowerCase().includes(searchQuery.value.toLowerCase())
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
    major: crs.major,
    access_type: crs.access_type,
  }
  isEditModalOpen.value = true
}

const openViewModal = (crs: any) => {
  closeDropdown()
  selectedCourse.value = crs
  isViewModalOpen.value = true
}

const openConvertModal = (crs: any) => {
  closeDropdown()
  selectedCourse.value = crs
  convertPrice.value = 20
  isConvertModalOpen.value = true
}

const saveEditCourse = () => {
  if (selectedCourse.value?.id) {
    router.put(`/admin/course-module/update/${selectedCourse.value.id}`, editForm.value, {
      onSuccess: () => { isEditModalOpen.value = false },
      onError: () => { isEditModalOpen.value = false }
    })
  } else {
    isEditModalOpen.value = false
  }
}

const convertToPaid = () => {
  if (selectedCourse.value?.id) {
    router.put(`/admin/course-module/update/${selectedCourse.value.id}`, { is_paid: true, price: convertPrice.value }, {
      onSuccess: () => { isConvertModalOpen.value = false },
      onError: () => { isConvertModalOpen.value = false }
    })
  } else {
    isConvertModalOpen.value = false
  }
}

const deleteCourse = (crs: any) => {
  closeDropdown()
  if (confirm(`Are you sure you want to delete '${crs.title}'?`)) {
    router.delete(`/admin/course-module/destroy/${crs.id}`)
  }
}
</script>

<template>
  <AdminLayout title="Free Courses — Course & Subject Management">
    <div class="space-y-4 font-sans" @click="closeDropdown">
      <!-- Shared Header (1-Layer Clean Header) -->
      <CourseModuleHeader activeTab="free" :summaryStats="props.summaryStats" />

      <!-- CONTROLS BAR -->
      <div class="flex items-center justify-between p-3.5 bg-slate-900/60 border border-slate-800 rounded-2xl backdrop-blur-xl">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="🔍 Search title, code..."
          class="w-full sm:w-80 bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500"
        />
      </div>

      <!-- BULK ACTIONS TOOLBAR -->
      <div v-if="selectedIds.length > 0" class="flex items-center justify-between p-3 bg-emerald-950/80 border border-emerald-500/40 rounded-2xl text-xs backdrop-blur-xl animate-fade-in">
        <div class="flex items-center gap-2 text-emerald-200 font-bold">
          <span>☑️</span>
          <span>{{ selectedIds.length }} free course(s) selected</span>
        </div>
        <div class="flex items-center gap-2">
          <button @click="selectedIds = []" class="px-2.5 py-1.5 bg-slate-800 text-slate-300 hover:text-white rounded-xl font-bold">
            ✕ Clear
          </button>
        </div>
      </div>

      <!-- TABLE -->
      <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-xl">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-800 bg-slate-800/40 text-[11px] font-bold text-slate-400 uppercase whitespace-nowrap">
              <th class="py-3 px-3 w-10 text-center">
                <input
                  type="checkbox"
                  :checked="isAllSelected"
                  @change="toggleSelectAll"
                  class="rounded border-slate-700 bg-slate-950 text-emerald-500 focus:ring-emerald-500/20"
                />
              </th>
              <th class="py-3.5 px-4 w-12">#</th>
              <th class="py-3.5 px-4">🎁 Course Title</th>
              <th class="py-3.5 px-4">Major</th>
              <th class="py-3.5 px-4">Access Type</th>
              <th class="py-3.5 px-4 text-center">Enrolled Students</th>
              <th class="py-3.5 px-4 text-center">Status</th>
              <th class="py-3.5 px-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800 text-xs">
            <tr v-if="paginatedCourses.length === 0">
              <td colspan="8" class="py-8 text-center text-slate-400">
                No free courses found.
              </td>
            </tr>
            <tr
              v-for="(crs, idx) in paginatedCourses"
              :key="crs.id"
              class="hover:bg-slate-800/30 transition-all"
              :class="{ 'bg-emerald-950/20': selectedIds.includes(crs.id) }"
            >
              <td class="py-4 px-3 text-center">
                <input
                  type="checkbox"
                  :value="crs.id"
                  v-model="selectedIds"
                  class="rounded border-slate-700 bg-slate-950 text-emerald-500 focus:ring-emerald-500/20"
                />
              </td>

              <td class="py-4 px-4 font-sans text-slate-400 font-semibold whitespace-nowrap">
                {{ String((currentPage - 1) * pageSize + idx + 1).padStart(2, '0') }}
              </td>

              <td class="py-4 px-4 whitespace-nowrap font-bold text-white text-sm">
                <div>{{ crs.title }}</div>
                <div class="text-[11px] font-sans text-slate-400 font-normal mt-0.5">Code: {{ crs.code }}</div>
              </td>

              <td class="py-4 px-4 whitespace-nowrap text-slate-300 font-medium">{{ crs.major }}</td>
              <td class="py-4 px-4 whitespace-nowrap font-sans text-emerald-300 font-medium">{{ crs.access_type }}</td>

              <td class="py-4 px-4 text-center whitespace-nowrap font-sans text-sky-300 font-semibold">
                <span class="px-2.5 py-1 rounded-xl bg-slate-950 border border-slate-800">
                  👥 {{ crs.students_count ? crs.students_count.toLocaleString() : 0 }} Students
                </span>
              </td>

              <td class="py-4 px-4 text-center whitespace-nowrap">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                  🟢 Active Free
                </span>
              </td>

              <!-- STANDARDIZED ROW ACTIONS -->
              <td class="py-4 px-4 text-right whitespace-nowrap relative" @click.stop>
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    @click="openEditModal(crs)"
                    class="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 rounded-xl font-bold whitespace-nowrap inline-flex items-center gap-1"
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
                        @click="openViewModal(crs)"
                        class="w-full px-3 py-2 text-slate-200 hover:text-white hover:bg-slate-800 rounded-xl flex items-center gap-2 font-medium"
                      >
                        <span>👁️</span> View Details
                      </button>

                      <button
                        @click="openConvertModal(crs)"
                        class="w-full px-3 py-2 text-amber-300 hover:bg-amber-500/10 rounded-xl flex items-center gap-2 font-medium"
                      >
                        <span>🔒</span> Convert to Paid
                      </button>

                      <div class="border-t border-slate-800 my-1"></div>

                      <button
                        @click="deleteCourse(crs)"
                        class="w-full px-3 py-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-xl flex items-center gap-2 font-medium"
                      >
                        <span>🗑️</span> Delete
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
              :class="currentPage === p ? 'bg-emerald-600 text-white' : 'bg-slate-950 border border-slate-800 text-slate-400 hover:text-white'"
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
        <div class="bg-slate-900 border border-emerald-900/50 rounded-3xl max-w-xl w-full p-6 space-y-4 shadow-2xl">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white">✏️ EDIT FREE COURSE</h3>
            <button @click="isEditModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
          </div>

          <form @submit.prevent="saveEditCourse" class="space-y-4 text-xs">
            <div>
              <label class="block text-slate-400 mb-1">Course Title</label>
              <input v-model="editForm.title" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-bold" />
            </div>

            <div>
              <label class="block text-slate-400 mb-1">Access Type</label>
              <select v-model="editForm.access_type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                <option value="Public Access">Public Access</option>
                <option value="Intro Only">Intro Only</option>
                <option value="Scholarship Only">Scholarship Only</option>
              </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
              <button type="button" @click="isEditModalOpen = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl font-bold">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold">Save Changes</button>
            </div>
          </form>
        </div>
      </div>

      <!-- CONVERT TO PAID MODAL -->
      <div v-if="isConvertModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-amber-900/50 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white">🔒 CONVERT TO PAID COURSE</h3>
            <button @click="isConvertModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
          </div>

          <div class="space-y-3 text-xs">
            <p class="text-slate-300">Set a price for <strong class="text-white">{{ selectedCourse?.title }}</strong> to start accepting payments via ABA Bank QR:</p>
            <div>
              <label class="block text-slate-400 mb-1">Price ($ USD)</label>
              <input v-model.number="convertPrice" type="number" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-amber-300 font-bold font-sans text-sm" />
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
            <button type="button" @click="isConvertModalOpen = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl font-bold">Cancel</button>
            <button type="button" @click="convertToPaid" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl font-bold">Convert to Paid</button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
