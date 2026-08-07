<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import EnrollmentModuleHeader from '@/Components/Admin/EnrollmentModuleHeader.vue'

const props = withDefaults(defineProps<{
  enrollments?: any[]
  summaryStats?: any
}>(), {
  enrollments: () => [],
  summaryStats: () => ({})
})

const searchQuery = ref('')
const selectedSemester = ref('Sem 2')
const selectedMode = ref('all')
const selectedPayment = ref('all')

const isPaymentModalOpen = ref(false)
const selectedEnrollment = ref<any>(null)

const paymentForm = ref({
  payment_method: 'ABA Bank KHQR',
  transaction_id: 'ABA-TRX-98234719',
  amount: 25,
  note: 'Verified by Admin via ABA Merchant App',
})

const enrollmentsList = computed(() => {
  return Array.isArray(props.enrollments) && props.enrollments.length > 0 ? props.enrollments : [
    { id: 1, student_name: 'Chan Dara', student_id: 'STU24001', course_title: 'C Programming Basics', mode: '🎥 Live', price: 25, payment_status: 'paid', access: 'unlocked' },
    { id: 2, student_name: 'Chan Dara', student_id: 'STU24001', course_title: 'Web Development', mode: '🎥 Live', price: 30, payment_status: 'pending', access: 'locked' },
    { id: 3, student_name: 'Chan Dara', student_id: 'STU24001', course_title: 'English Grammar Basics', mode: '💻 Self', price: 0, payment_status: 'free', access: 'unlocked' },
    { id: 4, student_name: 'Sok Chanra', student_id: 'STU24002', course_title: 'Tourism Basics', mode: '🎥 Live', price: 25, payment_status: 'paid', access: 'unlocked' },
    { id: 5, student_name: 'Long Vichida', student_id: 'STU24003', course_title: 'Database Systems', mode: '💻 Self', price: 20, payment_status: 'unpaid', access: 'locked' },
  ]
})

const filteredEnrollments = computed(() => {
  return enrollmentsList.value.filter(enr => {
    const matchesSearch = enr.student_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          enr.course_title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          enr.student_id.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesPayment = selectedPayment.value === 'all' || enr.payment_status === selectedPayment.value
    return matchesSearch && matchesPayment
  })
})

const openPaymentModal = (enr: any) => {
  selectedEnrollment.value = enr
  paymentForm.value.amount = enr.price
  isPaymentModalOpen.value = true
}

const toggleAccessLock = (enr: any) => {
  const newStatus = enr.access === 'locked' ? 'unlocked' : 'locked'
  router.put(`/admin/enrollment/courses/toggle-access/${enr.id}`, { access: newStatus })
}

const confirmPayment = () => {
  if (selectedEnrollment.value?.id) {
    router.put(`/admin/enrollment/courses/verify-payment/${selectedEnrollment.value.id}`, paymentForm.value, {
      onSuccess: () => { isPaymentModalOpen.value = false },
      onError: () => { isPaymentModalOpen.value = false }
    })
  } else {
    isPaymentModalOpen.value = false
  }
}

const removeEnrollment = (enr: any) => {
  if (confirm(`Remove enrollment for '${enr.student_name}' from '${enr.course_title}'?`)) {
    router.delete(`/admin/enrollment/courses/remove/${enr.id}`)
  }
}
</script>

<template>
  <AdminLayout title="Course Enrollments — Enrollment Management">
    <div class="space-y-6 font-sans">
      <EnrollmentModuleHeader activeTab="courses" :summaryStats="props.summaryStats" />

      <!-- STATS SUMMARY CARDS -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="p-4 bg-slate-900/60 border border-slate-800 rounded-2xl backdrop-blur-xl">
          <div class="text-[10px] font-bold text-slate-400 uppercase">📚 Total Enrolled</div>
          <div class="text-xl font-black text-white font-mono mt-1">4,250</div>
        </div>
        <div class="p-4 bg-slate-900/60 border border-emerald-500/20 rounded-2xl backdrop-blur-xl">
          <div class="text-[10px] font-bold text-emerald-400 uppercase">✅ Paid ABA</div>
          <div class="text-xl font-black text-emerald-300 font-mono mt-1">3,120</div>
        </div>
        <div class="p-4 bg-slate-900/60 border border-amber-500/20 rounded-2xl backdrop-blur-xl">
          <div class="text-[10px] font-bold text-amber-400 uppercase">⏳ Pending Payment</div>
          <div class="text-xl font-black text-amber-300 font-mono mt-1">480</div>
        </div>
        <div class="p-4 bg-slate-900/60 border border-sky-500/20 rounded-2xl backdrop-blur-xl">
          <div class="text-[10px] font-bold text-sky-400 uppercase">🎁 Free Access</div>
          <div class="text-xl font-black text-sky-300 font-mono mt-1">650</div>
        </div>
      </div>

      <!-- FILTER BAR -->
      <div class="p-4 bg-slate-900/60 border border-slate-800 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-3 backdrop-blur-xl">
        <div class="flex items-center gap-2 w-full md:w-auto">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="🔍 Search student or course..."
            class="w-full md:w-72 bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-sky-500"
          />

          <select v-model="selectedPayment" class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-300">
            <option value="all">Payment: All</option>
            <option value="paid">✅ Paid</option>
            <option value="pending">⏳ Pending</option>
            <option value="free">🎁 Free</option>
            <option value="unpaid">❌ Unpaid</option>
          </select>
        </div>

        <div class="p-2.5 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-300 text-xs flex items-center gap-2">
          <span>⚠️ Access Rule:</span>
          <span class="text-[11px] text-amber-200">🔒 Locked = Student cannot view lessons until ABA payment is verified</span>
        </div>
      </div>

      <!-- COURSE ENROLLMENTS TABLE -->
      <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-xl">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-800 bg-slate-800/40 text-[11px] font-bold text-slate-400 uppercase whitespace-nowrap">
              <th class="py-3.5 px-4 w-12">#</th>
              <th class="py-3.5 px-4">👨‍🎓 Student</th>
              <th class="py-3.5 px-4">📚 Course Title</th>
              <th class="py-3.5 px-4">Mode</th>
              <th class="py-3.5 px-4 text-center">Price</th>
              <th class="py-3.5 px-4 text-center">Payment</th>
              <th class="py-3.5 px-4 text-center">Lesson Access</th>
              <th class="py-3.5 px-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800 text-xs">
            <tr v-for="(enr, idx) in filteredEnrollments" :key="enr.id" class="hover:bg-slate-800/30 transition-all">
              <td class="py-4 px-4 font-mono text-slate-500 whitespace-nowrap">{{ String(idx + 1).padStart(2, '0') }}</td>

              <td class="py-4 px-4 whitespace-nowrap">
                <div class="font-bold text-white text-sm">{{ enr.student_name }}</div>
                <div class="text-[10px] text-sky-400 font-mono">{{ enr.student_id }}</div>
              </td>

              <td class="py-4 px-4 whitespace-nowrap font-bold text-slate-200">
                {{ enr.course_title }}
              </td>

              <td class="py-4 px-4 whitespace-nowrap font-mono text-slate-300">
                {{ enr.mode }}
              </td>

              <td class="py-4 px-4 text-center font-mono font-bold text-amber-300">
                <span v-if="enr.price > 0">${{ enr.price }} USD</span>
                <span v-else class="text-emerald-400 font-bold">FREE</span>
              </td>

              <td class="py-4 px-4 text-center whitespace-nowrap">
                <span v-if="enr.payment_status === 'paid'" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 inline-flex items-center gap-1 shadow-sm">
                  <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Paid</span>
                </span>
                <span v-else-if="enr.payment_status === 'pending'" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 inline-flex items-center gap-1 shadow-sm">
                  <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Pending</span>
                </span>
                <span v-else-if="enr.payment_status === 'free'" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-500/15 text-sky-300 border border-sky-500/30 inline-flex items-center gap-1 shadow-sm">
                  <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V6a2 2 0 10-2 2h2zm0 13C10.832 19.477 9.246 19 7.5 19S4.168 19.477 3 20.253V7.253C4.168 6.477 5.754 6 7.5 6s3.332.477 4.5 1.253m0 13C13.168 19.477 14.754 19 16.5 19c1.747 0 3.332.477 4.5 1.253V7.253C19.832 6.477 18.247 6 16.5 6c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                  <span>Free</span>
                </span>
                <span v-else class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-500/15 text-red-300 border border-red-500/30 inline-flex items-center gap-1 shadow-sm">
                  <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  <span>Unpaid</span>
                </span>
              </td>

              <!-- LESSON ACCESS CLICKABLE TOGGLE BADGE -->
              <td class="py-4 px-4 text-center whitespace-nowrap">
                <button
                  @click="toggleAccessLock(enr)"
                  title="Click to toggle access manually"
                  :class="[
                    'px-3 py-1 rounded-full text-[10px] font-bold transition-all border inline-flex items-center gap-1.5 shadow-sm cursor-pointer hover:scale-105 active:scale-95',
                    enr.access === 'unlocked'
                      ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 hover:bg-emerald-500/30'
                      : 'bg-red-500/20 text-red-300 border-red-500/40 hover:bg-red-500/30'
                  ]"
                >
                  <svg v-if="enr.access === 'unlocked'" class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                  </svg>
                  <svg v-else class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                  <span>{{ enr.access === 'unlocked' ? 'Unlocked' : 'Locked' }}</span>
                </button>
              </td>

              <td class="py-4 px-4 text-right whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                  <button v-if="enr.payment_status !== 'paid' && enr.payment_status !== 'free'" @click="openPaymentModal(enr)" class="px-2.5 py-1.5 bg-emerald-600/20 hover:bg-emerald-500/30 text-emerald-300 rounded-xl font-bold border border-emerald-500/30 text-xs inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Confirm Payment</span>
                  </button>
                  <button @click="removeEnrollment(enr)" class="px-2.5 py-1.5 bg-red-600/20 hover:bg-red-500/30 text-red-300 rounded-xl font-bold border border-red-500/30 text-xs inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Remove</span>
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
          <span>Showing <strong class="text-white">1</strong> to <strong class="text-white">{{ filteredEnrollments.length }}</strong> of <strong class="text-white">{{ filteredEnrollments.length }}</strong> course enrollments</span>
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

      <!-- CONFIRM PAYMENT MODAL -->
      <div v-if="isPaymentModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-emerald-900/50 rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              <span>💳 CONFIRM ABA PAYMENT & UNLOCK</span>
            </h3>
            <button @click="isPaymentModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
          </div>

          <form @submit.prevent="confirmPayment" class="space-y-3 text-xs">
            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 font-mono space-y-1">
              <div><span class="text-slate-400">Student:</span> <strong class="text-white">{{ selectedEnrollment?.student_name }}</strong></div>
              <div><span class="text-slate-400">Course:</span> <strong class="text-emerald-300">{{ selectedEnrollment?.course_title }}</strong></div>
              <div><span class="text-slate-400">Price:</span> <strong class="text-amber-300">${{ selectedEnrollment?.price }} USD</strong></div>
            </div>

            <div>
              <label class="block text-slate-400 mb-1">ABA Transaction Reference / ID</label>
              <input v-model="paymentForm.transaction_id" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
              <button type="button" @click="isPaymentModalOpen = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl font-bold">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold">✅ Confirm & Unlock Access</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
