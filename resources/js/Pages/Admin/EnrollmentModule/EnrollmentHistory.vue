<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import EnrollmentModuleHeader from '@/Components/Admin/EnrollmentModuleHeader.vue'

const props = withDefaults(defineProps<{
  historyLogs?: any[]
  summaryStats?: any
}>(), {
  historyLogs: () => [],
  summaryStats: () => ({})
})

const searchQuery = ref('')
const selectedAction = ref('all')

const isDetailModalOpen = ref(false)
const isReverseModalOpen = ref(false)
const selectedLog = ref<any>(null)
const reverseReason = ref('')

const logsList = computed(() => {
  return Array.isArray(props.historyLogs) && props.historyLogs.length > 0 ? props.historyLogs : [
    { id: 1, date: '15 Jun 2025 09:30 AM', student_name: 'Chan Dara', action: '➕ Major Enroll', target: 'IT & Networking', performed_by: 'Mr. Sophea (Admin)' },
    { id: 2, date: '15 Jun 2025 09:31 AM', student_name: 'Chan Dara', action: '➕ Course Enroll', target: 'C Programming ($25 Paid)', performed_by: 'Mr. Sophea (Admin)' },
    { id: 3, date: '14 Jun 2025 02:15 PM', student_name: 'Sok Chanra', action: '💳 Payment OK', target: 'Web Development (Unlocked)', performed_by: 'System Auto (ABA)' },
    { id: 4, date: '12 Jun 2025 11:00 AM', student_name: 'Long Vichida', action: '🔄 Transfer Major', target: 'English Lit → Tourism', performed_by: 'Ms. Rina (Admin)' },
    { id: 5, date: '10 Jun 2025 04:45 PM', student_name: 'Unknown X', action: '🚫 Withdraw', target: 'Social Work (Refunded)', performed_by: 'Mr. Sophea (Admin)' },
  ]
})

const filteredLogs = computed(() => {
  return logsList.value.filter(log => {
    const matchesSearch = log.student_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          log.target.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          log.performed_by.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesAction = selectedAction.value === 'all' || log.action.includes(selectedAction.value)
    return matchesSearch && matchesAction
  })
})

const openDetailModal = (log: any) => {
  selectedLog.value = log
  isDetailModalOpen.value = true
}

const openReverseModal = (log: any) => {
  selectedLog.value = log
  reverseReason.value = ''
  isReverseModalOpen.value = true
}

const submitReverse = () => {
  if (selectedLog.value?.id) {
    router.post(`/admin/enrollment/history/reverse/${selectedLog.value.id}`, { reason: reverseReason.value }, {
      onSuccess: () => { isReverseModalOpen.value = false },
      onError: () => { isReverseModalOpen.value = false }
    })
  } else {
    isReverseModalOpen.value = false
  }
}

const resendEmail = (log: any) => {
  alert(`Confirmation email resent to ${log.student_name}!`)
}
</script>

<template>
  <AdminLayout title="Enrollment History — Enrollment Management">
    <div class="space-y-6 font-sans">
      <EnrollmentModuleHeader activeTab="history" :summaryStats="props.summaryStats" />

      <!-- STATS SUMMARY CARDS -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="p-4 bg-slate-900/60 border border-slate-800 rounded-2xl backdrop-blur-xl">
          <div class="text-[10px] font-bold text-slate-400 uppercase">📅 This Month</div>
          <div class="text-xl font-black text-white font-mono mt-1">320 Enrolls</div>
        </div>
        <div class="p-4 bg-slate-900/60 border border-emerald-500/20 rounded-2xl backdrop-blur-xl">
          <div class="text-[10px] font-bold text-emerald-400 uppercase">📅 This Semester</div>
          <div class="text-xl font-black text-emerald-300 font-mono mt-1">2,458</div>
        </div>
        <div class="p-4 bg-slate-900/60 border border-sky-500/20 rounded-2xl backdrop-blur-xl">
          <div class="text-[10px] font-bold text-sky-400 uppercase">🔄 Major Transfers</div>
          <div class="text-xl font-black text-sky-300 font-mono mt-1">12</div>
        </div>
        <div class="p-4 bg-slate-900/60 border border-red-500/20 rounded-2xl backdrop-blur-xl">
          <div class="text-[10px] font-bold text-red-400 uppercase">🚫 Withdrawals</div>
          <div class="text-xl font-black text-red-300 font-mono mt-1">8</div>
        </div>
      </div>

      <!-- FILTER BAR -->
      <div class="p-4 bg-slate-900/60 border border-slate-800 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-3 backdrop-blur-xl">
        <div class="flex items-center gap-2 w-full md:w-auto">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="🔍 Search student, course, admin..."
            class="w-full md:w-72 bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
          />

          <select v-model="selectedAction" class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-300">
            <option value="all">Action: All</option>
            <option value="Major Enroll">➕ Major Enroll</option>
            <option value="Course Enroll">➕ Course Enroll</option>
            <option value="Payment">💳 Payment</option>
            <option value="Transfer">🔄 Transfer</option>
            <option value="Withdraw">🚫 Withdraw</option>
          </select>
        </div>

        <div class="flex items-center gap-2">
          <button class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl border border-slate-700">
            📤 Export CSV
          </button>
        </div>
      </div>

      <!-- HISTORY AUDIT LOG TABLE -->
      <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur-xl">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-800 bg-slate-800/40 text-[11px] font-bold text-slate-400 uppercase whitespace-nowrap">
              <th class="py-3.5 px-4 w-12">#</th>
              <th class="py-3.5 px-4">📅 Date & Time</th>
              <th class="py-3.5 px-4">👨‍🎓 Student</th>
              <th class="py-3.5 px-4">🎯 Action</th>
              <th class="py-3.5 px-4">📚 Target</th>
              <th class="py-3.5 px-4">👤 Performed By</th>
              <th class="py-3.5 px-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800 text-xs">
            <tr v-for="(log, idx) in filteredLogs" :key="log.id" class="hover:bg-slate-800/30 transition-all">
              <td class="py-4 px-4 font-mono text-slate-500 whitespace-nowrap">{{ String(idx + 1).padStart(2, '0') }}</td>

              <td class="py-4 px-4 whitespace-nowrap font-mono text-slate-400">
                {{ log.date }}
              </td>

              <td class="py-4 px-4 whitespace-nowrap font-bold text-white text-sm">
                {{ log.student_name }}
              </td>

              <td class="py-4 px-4 whitespace-nowrap">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                  {{ log.action }}
                </span>
              </td>

              <td class="py-4 px-4 whitespace-nowrap font-mono text-slate-200">
                {{ log.target }}
              </td>

              <td class="py-4 px-4 whitespace-nowrap font-mono text-slate-400">
                {{ log.performed_by }}
              </td>

              <td class="py-4 px-4 text-right whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                  <button @click="openDetailModal(log)" class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl font-bold border border-slate-700 text-xs inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Details</span>
                  </button>
                  <button @click="resendEmail(log)" class="px-2.5 py-1.5 bg-sky-600/20 hover:bg-sky-500/30 text-sky-300 rounded-xl font-bold border border-sky-500/30 text-xs inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Email</span>
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
          <span>Showing <strong class="text-white">1</strong> to <strong class="text-white">{{ filteredLogs.length }}</strong> of <strong class="text-white">{{ filteredLogs.length }}</strong> audit log entries</span>
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
            <button class="w-8 h-8 rounded-xl font-bold bg-indigo-600 text-white flex items-center justify-center">1</button>
          </div>
          <button disabled class="px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-950 font-bold transition-all opacity-40 cursor-not-allowed text-slate-400">Next ›</button>
        </div>
      </div>

      <!-- DETAIL MODAL -->
      <div v-if="isDetailModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900/95 border border-slate-700/60 rounded-2xl max-w-lg w-full p-5 space-y-4 shadow-2xl backdrop-blur-2xl">
          <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-xl bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-400">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <h3 class="text-sm font-bold text-slate-100 uppercase tracking-wide">AUDIT LOG ENTRY DETAILS</h3>
            </div>
            <button @click="isDetailModalOpen = false" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white">✕</button>
          </div>

          <div class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800 space-y-2 text-xs font-sans">
            <div><span class="text-slate-300">Timestamp:</span> <strong class="text-white">{{ selectedLog?.date }}</strong></div>
            <div><span class="text-slate-300">Student:</span> <strong class="text-sky-300">{{ selectedLog?.student_name }}</strong></div>
            <div><span class="text-slate-300">Action Type:</span> <strong class="text-indigo-300">{{ selectedLog?.action }}</strong></div>
            <div><span class="text-slate-300">Target Object:</span> <strong class="text-amber-300">{{ selectedLog?.target }}</strong></div>
            <div><span class="text-slate-300">Performed By:</span> <strong class="text-emerald-300">{{ selectedLog?.performed_by }}</strong></div>
          </div>

          <!-- HIGH RISK REVERSE ACTION MOVED INSIDE DETAIL MODAL -->
          <div class="flex items-center justify-between pt-3 border-t border-slate-800/80">
            <button @click="openReverseModal(selectedLog); isDetailModalOpen = false" class="px-3.5 py-2 bg-amber-600/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 rounded-xl font-bold text-xs flex items-center gap-1.5 transition-all">
              <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
              </svg>
              <span>Reverse Action (Admin Only)</span>
            </button>
            <button @click="isDetailModalOpen = false" class="px-4 py-2 bg-slate-800 text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-700 transition-all">Close</button>
          </div>
        </div>
      </div>

      <!-- REVERSE CONFIRMATION MODAL -->
      <div v-if="isReverseModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900/95 border border-amber-500/40 rounded-2xl max-w-md w-full p-5 space-y-4 shadow-2xl backdrop-blur-2xl">
          <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <h3 class="text-sm font-bold text-amber-300 uppercase tracking-wide">CONFIRM HIGH-RISK REVERSAL</h3>
            </div>
            <button @click="isReverseModalOpen = false" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white">✕</button>
          </div>

          <form @submit.prevent="submitReverse" class="space-y-3.5 text-xs">
            <div class="p-3 bg-amber-500/10 border border-amber-500/30 rounded-xl text-amber-200 font-medium">
              ⚠️ Reversing action for '{{ selectedLog?.student_name }}' on '{{ selectedLog?.target }}' requires Admin justification and confirmation.
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1.5">Administrative Reason for Reversal</label>
              <textarea v-model="reverseReason" rows="3" required placeholder="Enter administrative justification..." class="w-full bg-slate-950/80 border border-slate-800 focus:border-amber-500 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:ring-1 focus:ring-amber-500/20 transition-all"></textarea>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-800/80">
              <button type="button" @click="isReverseModalOpen = false" class="px-4 py-2 bg-slate-800/80 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl transition-all">Cancel</button>
              <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-amber-600/30 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
                <span>Confirm Reversal</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
