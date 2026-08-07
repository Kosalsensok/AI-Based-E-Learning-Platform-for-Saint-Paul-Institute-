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

const isPriceModalOpen = ref(false)
const isQrModalOpen = ref(false)
const selectedCourse = ref<any>(null)

const priceForm = ref({
  price: 25,
  receiver: 'School ABA Account',
  teacher_share: 70,
  school_share: 30,
})

const coursesList = computed(() => {
  return Array.isArray(props.courses) && props.courses.length > 0 ? props.courses : [
    { id: 1, code: 'CRS-IT-CP101', title: 'C Programming Basics', major: 'IT & Networking', teacher: 'Mr. Sophea', price: 25, receiver: 'Teacher ABA (000123456)', status: 'published' },
    { id: 12, code: 'CRS-IT-WD101', title: 'Web Development (HTML/CSS/JS)', major: 'IT & Networking', teacher: 'Ms. Dara', price: 30, receiver: 'School ABA (000123456)', status: 'published' },
    { id: 2, code: 'CRS-IT-DB102', title: 'Database Systems', major: 'IT & Networking', teacher: 'Mr. Sophea', price: 20, receiver: 'Teacher ABA (000123456)', status: 'published' },
    { id: 5, code: 'CRS-AG-PS101', title: 'Plant Science Advanced', major: 'Agronomy', teacher: 'Mr. Vuthy', price: 30, receiver: 'Teacher ABA (000987654)', status: 'published' },
    { id: 6, code: 'CRS-SW-SW101', title: 'Social Work 101', major: 'Social Work', teacher: 'Mr. Rithy', price: 15, receiver: 'School ABA (000123456)', status: 'published' },
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

const openPriceModal = (crs: any) => {
  closeDropdown()
  selectedCourse.value = crs
  priceForm.value = {
    price: crs.price || 25,
    receiver: crs.receiver || 'School ABA Account',
    teacher_share: 70,
    school_share: 30,
  }
  isPriceModalOpen.value = true
}

const openQrModal = (crs: any) => {
  closeDropdown()
  selectedCourse.value = crs
  isQrModalOpen.value = true
}

const savePriceSettings = () => {
  if (selectedCourse.value?.id) {
    router.put(`/admin/course-module/update/${selectedCourse.value.id}`, { price: priceForm.value.price }, {
      onSuccess: () => { isPriceModalOpen.value = false },
      onError: () => { isPriceModalOpen.value = false }
    })
  } else {
    isPriceModalOpen.value = false
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
  <AdminLayout title="Paid Courses — Course & Subject Management">
    <div class="space-y-4 font-sans" @click="closeDropdown">
      <!-- Shared Header (1-Layer Clean Header) -->
      <CourseModuleHeader activeTab="paid" :summaryStats="props.summaryStats" />

      <!-- CONTROLS BAR -->
      <div class="flex items-center justify-between p-3.5 bg-slate-900/60 border border-slate-800 rounded-2xl backdrop-blur-xl">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="🔍 Search title, code, teacher..."
          class="w-full sm:w-80 bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500"
        />
      </div>

      <!-- BULK ACTIONS TOOLBAR -->
      <div v-if="selectedIds.length > 0" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 p-3 bg-amber-950/70 border border-amber-500/40 rounded-2xl text-xs backdrop-blur-xl animate-fade-in">
        <div class="flex items-center gap-2 text-amber-200 font-bold">
          <span>☑️</span>
          <span>{{ selectedIds.length }} paid course(s) selected</span>
        </div>
        <div class="flex items-center gap-2">
          <button @click="selectedIds = []" class="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 rounded-xl font-bold">
            🟢 Bulk Publish
          </button>
          <button @click="selectedIds = []" class="px-3 py-1.5 bg-red-600/20 hover:bg-red-500/30 text-red-300 border border-red-500/30 rounded-xl font-bold">
            🗑️ Bulk Delete
          </button>
          <button @click="selectedIds = []" class="px-2.5 py-1.5 bg-slate-800 text-slate-200 hover:text-white rounded-xl font-bold">
            ✕ Clear
          </button>
        </div>
      </div>

      <!-- TABLE -->
      <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-xl">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-800 bg-slate-800/40 text-[11px] font-bold text-slate-300 uppercase whitespace-nowrap">
              <th class="py-3 px-3 w-10 text-center">
                <input
                  type="checkbox"
                  :checked="isAllSelected"
                  @change="toggleSelectAll"
                  class="rounded border-slate-700 bg-slate-950 text-amber-500 focus:ring-amber-500/20"
                />
              </th>
              <th class="py-3.5 px-4 w-12">#</th>
              <th class="py-3.5 px-4">💳 Course Title</th>
              <th class="py-3.5 px-4">Major</th>
              <th class="py-3.5 px-4">Teacher</th>
              <th class="py-3.5 px-4 text-center">Price ($ USD)</th>
              <th class="py-3.5 px-4">ABA Receiver Account</th>
              <th class="py-3.5 px-4 text-center">Status</th>
              <th class="py-3.5 px-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800 text-xs">
            <tr v-if="paginatedCourses.length === 0">
              <td colspan="9" class="py-8 text-center text-slate-300">
                No paid courses found.
              </td>
            </tr>
            <tr
              v-for="(crs, idx) in paginatedCourses"
              :key="crs.id"
              class="hover:bg-slate-800/30 transition-all"
              :class="{ 'bg-amber-950/20': selectedIds.includes(crs.id) }"
            >
              <td class="py-4 px-3 text-center">
                <input
                  type="checkbox"
                  :value="crs.id"
                  v-model="selectedIds"
                  class="rounded border-slate-700 bg-slate-950 text-amber-500 focus:ring-amber-500/20"
                />
              </td>

              <td class="py-4 px-4 font-sans text-slate-300 font-semibold whitespace-nowrap">
                {{ String((currentPage - 1) * pageSize + idx + 1).padStart(2, '0') }}
              </td>

              <td class="py-4 px-4 whitespace-nowrap font-bold text-white text-sm">
                <div>{{ crs.title }}</div>
                <div class="text-[11px] font-sans text-sky-300 font-semibold mt-0.5">Code: {{ crs.code }}</div>
              </td>

              <td class="py-4 px-4 whitespace-nowrap text-slate-200 font-medium">{{ crs.major }}</td>
              <td class="py-4 px-4 whitespace-nowrap font-sans text-slate-100 font-semibold">{{ crs.teacher }}</td>

              <td class="py-4 px-4 text-center font-sans font-bold text-amber-300 text-sm whitespace-nowrap">
                ${{ crs.price }} USD
              </td>

              <td class="py-4 px-4 whitespace-nowrap font-sans text-sky-300 font-semibold">
                {{ crs.receiver }}
              </td>

              <td class="py-4 px-4 text-center whitespace-nowrap">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 inline-flex items-center gap-1.5 shadow-sm">
                  <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                  <span>Paid Course</span>
                </span>
              </td>

              <!-- STANDARDIZED ROW ACTIONS -->
              <td class="py-4 px-4 text-right whitespace-nowrap relative" @click.stop>
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    @click="openPriceModal(crs)"
                    class="px-3 py-1.5 bg-amber-600/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 rounded-xl font-bold whitespace-nowrap inline-flex items-center gap-1"
                  >
                    <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Pricing</span>
                  </button>

                  <div class="relative">
                    <button
                      @click="toggleDropdown(crs.id)"
                      class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl font-bold transition-all border border-slate-700"
                      title="More Options"
                    >
                      <span>⋮</span>
                    </button>

                    <div
                      v-if="activeDropdownId === crs.id"
                      class="absolute right-0 mt-1 w-44 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl z-50 p-1.5 text-xs text-left space-y-1 backdrop-blur-xl animate-fade-in"
                    >
                      <button
                        @click="openQrModal(crs)"
                        class="w-full px-3 py-2 text-sky-300 hover:bg-sky-500/10 rounded-xl flex items-center gap-2 font-medium"
                      >
                        <span>📲</span> ABA KHQR Code
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
              :class="currentPage === p ? 'bg-amber-600 text-white' : 'bg-slate-950 border border-slate-800 text-slate-400 hover:text-white'"
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

      <!-- PRICE SETTINGS MODAL -->
      <div v-if="isPriceModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-amber-900/50 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              <span>💳 COURSE PRICING & ABA SETTINGS</span>
            </h3>
            <button @click="isPriceModalOpen = false" class="text-slate-400 hover:text-white text-lg">✕</button>
          </div>

          <form @submit.prevent="savePriceSettings" class="space-y-4 text-xs">
            <div>
              <label class="block text-slate-400 mb-1">Course Title</label>
              <div class="font-bold text-white text-sm bg-slate-950 p-2.5 rounded-xl border border-slate-800">{{ selectedCourse?.title }}</div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-slate-400 mb-1">Course Price ($ USD)</label>
                <input v-model.number="priceForm.price" type="number" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-300 font-bold font-sans text-sm" />
              </div>
              <div>
                <label class="block text-slate-400 mb-1">Receiver ABA Account</label>
                <select v-model="priceForm.receiver" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-sans">
                  <option value="School ABA Account">School Main ABA (000123456)</option>
                  <option value="Teacher ABA Account">Teacher Direct ABA Account</option>
                </select>
              </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
              <button type="button" @click="isPriceModalOpen = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl font-bold">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl font-bold">Save Price</button>
            </div>
          </form>
        </div>
      </div>

      <!-- ABA QR MODAL -->
      <div v-if="isQrModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl text-center">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white">📲 ABA KHQR PAYMENTS</h3>
            <button @click="isQrModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
          </div>

          <div class="space-y-3 text-xs">
            <div class="p-4 bg-white rounded-2xl border border-slate-700 flex flex-col items-center">
              <div class="text-slate-900 font-black text-lg">ABA PAY</div>
              <div class="w-36 h-36 bg-slate-900 rounded-xl my-2 flex items-center justify-center text-white text-3xl">
                📱
              </div>
              <div class="text-slate-700 font-sans font-bold text-sm">${{ selectedCourse?.price }} USD</div>
            </div>
            <p class="text-slate-400 font-sans">Scan KHQR or click deep link to pay directly into: <br/><strong class="text-sky-300">{{ selectedCourse?.receiver }}</strong></p>
          </div>

          <button @click="isQrModalOpen = false" class="w-full py-2 bg-slate-800 text-slate-300 font-bold text-xs rounded-xl">Close</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
