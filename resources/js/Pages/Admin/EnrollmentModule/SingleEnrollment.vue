<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import EnrollmentModuleHeader from '@/Components/Admin/EnrollmentModuleHeader.vue'

const currentStep = ref(1)

const wizardForm = ref({
  student_name: 'Chan Dara',
  student_id: 'STU24001',
  email: 'dara@elms.edu',
  faculty: 'Computing',
  department: 'Computing',
  major: 'IT & Networking',
  academic_year: '2024-2025',
  semester: 'Semester 2 — 2024/2025',
  selected_courses: [
    { id: 1, title: 'C Programming Basics', mode: '🎥 Teacher-Led', teacher: 'Mr. Sophea', price: 25, type: 'Required', checked: true },
    { id: 2, title: 'Web Development (HTML/CSS)', mode: '🎥 Teacher-Led', teacher: 'Ms. Dara', price: 30, type: 'Elective', checked: true },
    { id: 3, title: 'English Grammar Basics', mode: '💻 Self-Study', teacher: 'Ms. Srey', price: 0, type: 'Required', checked: true },
    { id: 4, title: 'Database Systems', mode: '💻 Self-Study', teacher: 'Mr. Sophea', price: 20, type: 'Elective', checked: false },
  ],
  send_email: true,
})

const calculateTotal = () => {
  return wizardForm.value.selected_courses
    .filter(c => c.checked)
    .reduce((sum, c) => sum + c.price, 0)
}

const nextStep = () => {
  if (currentStep.value < 5) {
    currentStep.value++
  }
}

const prevStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--
  }
}

const completeWizard = () => {
  router.post('/admin/enrollment/single/store', wizardForm.value, {
    onSuccess: () => {
      alert('Enrollment completed successfully!')
      currentStep.value = 1
    }
  })
}
</script>

<template>
  <AdminLayout title="Single Enrollment — Enrollment Management">
    <div class="space-y-6 font-sans">
      <EnrollmentModuleHeader activeTab="single" />

      <!-- WIZARD STEP INDICATOR HEADER -->
      <div class="bg-slate-900/80 p-5 rounded-3xl border border-slate-800 backdrop-blur-xl space-y-4">
        <div class="flex items-center justify-between text-xs font-bold text-slate-300">
          <span>👤 SINGLE ENROLLMENT STEP-BY-STEP WIZARD</span>
          <span class="font-mono text-emerald-400">Step {{ currentStep }} of 5</span>
        </div>

        <div class="grid grid-cols-5 gap-2 text-center text-[11px] font-bold">
          <div :class="['p-2 rounded-xl border transition-all', currentStep >= 1 ? 'bg-emerald-600/20 text-emerald-300 border-emerald-500/40' : 'bg-slate-950 text-slate-500 border-slate-800']">
            ① Select Student
          </div>
          <div :class="['p-2 rounded-xl border transition-all', currentStep >= 2 ? 'bg-emerald-600/20 text-emerald-300 border-emerald-500/40' : 'bg-slate-950 text-slate-500 border-slate-800']">
            ② Choose Major
          </div>
          <div :class="['p-2 rounded-xl border transition-all', currentStep >= 3 ? 'bg-emerald-600/20 text-emerald-300 border-emerald-500/40' : 'bg-slate-950 text-slate-500 border-slate-800']">
            ③ Pick Courses
          </div>
          <div :class="['p-2 rounded-xl border transition-all', currentStep >= 4 ? 'bg-emerald-600/20 text-emerald-300 border-emerald-500/40' : 'bg-slate-950 text-slate-500 border-slate-800']">
            ④ Payment ABA
          </div>
          <div :class="['p-2 rounded-xl border transition-all', currentStep >= 5 ? 'bg-emerald-600/20 text-emerald-300 border-emerald-500/40' : 'bg-slate-950 text-slate-500 border-slate-800']">
            ⑤ Confirm & Complete
          </div>
        </div>
      </div>

      <!-- WIZARD CONTENT CONTAINERS -->
      <div class="bg-slate-900/60 p-6 rounded-3xl border border-slate-800 backdrop-blur-xl text-xs space-y-6">

        <!-- STEP 1: SELECT STUDENT -->
        <div v-if="currentStep === 1" class="space-y-4">
          <h3 class="text-sm font-bold text-white uppercase border-b border-slate-800/80 pb-2 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>STEP 1 — SELECT STUDENT ACCOUNT</span>
          </h3>
          <div class="space-y-3.5 max-w-lg">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1.5">Student Full Name</label>
              <input v-model="wizardForm.student_name" type="text" class="w-full bg-slate-950/80 border border-slate-700/70 hover:border-slate-600 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none transition-all shadow-inner" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1.5">Student ID</label>
              <input v-model="wizardForm.student_id" type="text" class="w-full bg-slate-950/80 border border-slate-700/70 hover:border-slate-600 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 rounded-xl px-3.5 py-2.5 text-xs text-sky-300 font-sans font-bold focus:outline-none transition-all shadow-inner" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address</label>
              <input v-model="wizardForm.email" type="email" class="w-full bg-slate-950/80 border border-slate-700/70 hover:border-slate-600 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 rounded-xl px-3.5 py-2.5 text-xs text-white font-sans focus:outline-none transition-all shadow-inner" />
            </div>
          </div>
        </div>

        <!-- STEP 2: CHOOSE MAJOR -->
        <div v-if="currentStep === 2" class="space-y-4">
          <h3 class="text-sm font-bold text-white uppercase border-b border-slate-800/80 pb-2 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span>STEP 2 — CHOOSE MAJOR & ADMISSION</span>
          </h3>
          <div class="space-y-3.5 max-w-lg">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1.5">Faculty</label>
              <select v-model="wizardForm.faculty" class="w-full bg-slate-950/80 border border-slate-700/70 hover:border-slate-600 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 rounded-xl px-3.5 py-2.5 text-xs text-white font-medium focus:outline-none transition-all">
                <option value="Computing">Faculty of Computing</option>
                <option value="Tourism">Faculty of Tourism</option>
                <option value="Education">Faculty of Education</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1.5">Major</label>
              <select v-model="wizardForm.major" class="w-full bg-slate-950/80 border border-slate-700/70 hover:border-slate-600 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none transition-all">
                <option value="IT & Networking">IT & Networking</option>
                <option value="Tourism Management">Tourism Management</option>
                <option value="English Literature">English Literature</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1.5">Academic Year</label>
              <select v-model="wizardForm.academic_year" class="w-full bg-slate-950/80 border border-slate-700/70 hover:border-slate-600 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 rounded-xl px-3.5 py-2.5 text-xs text-white font-sans focus:outline-none transition-all">
                <option value="2024-2025">2024 - 2025</option>
              </select>
            </div>
            <div class="p-3 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-300 inline-flex items-center gap-2 font-medium">
              <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Capacity: 520 / 600 Students Enrolled (85% Full — Available)</span>
            </div>
          </div>
        </div>

        <!-- STEP 3: PICK COURSES -->
        <div v-if="currentStep === 3" class="space-y-4">
          <h3 class="text-sm font-bold text-white uppercase border-b border-slate-800 pb-2">STEP 3 — PICK SEMESTER COURSES</h3>
          <div class="space-y-3">
            <div v-for="crs in wizardForm.selected_courses" :key="crs.id" class="p-3 bg-slate-950 rounded-2xl border border-slate-800 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <input v-model="crs.checked" type="checkbox" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-emerald-500" />
                <div>
                  <div class="font-bold text-white text-sm">{{ crs.title }}</div>
                  <div class="text-[11px] text-slate-400 font-mono">{{ crs.mode }} • {{ crs.teacher }} • {{ crs.type }}</div>
                </div>
              </div>
              <div class="font-mono font-bold text-amber-300">
                <span v-if="crs.price > 0">${{ crs.price }} USD</span>
                <span v-else class="text-emerald-400">FREE</span>
              </div>
            </div>

            <div class="p-3 bg-slate-950 border border-slate-800 rounded-2xl text-right font-mono font-bold text-sm text-amber-300">
              Subtotal: ${{ calculateTotal() }} USD
            </div>
          </div>
        </div>

        <!-- STEP 4: PAYMENT ABA KHQR -->
        <div v-if="currentStep === 4" class="space-y-4">
          <h3 class="text-sm font-bold text-white uppercase border-b border-slate-800 pb-2">STEP 4 — ABA PAYMENT & RECEIVER DETAILS</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-2 font-mono">
              <div class="text-slate-400 font-sans font-bold">Selected Courses Payment Breakdown:</div>
              <div v-for="crs in wizardForm.selected_courses.filter(c => c.checked)" :key="crs.id" class="flex justify-between border-b border-slate-900 pb-1 text-slate-300">
                <span>{{ crs.title }}</span>
                <span class="text-amber-300 font-bold">${{ crs.price }} USD</span>
              </div>
              <div class="flex justify-between pt-2 text-base font-bold text-white border-t border-slate-800">
                <span>Total Amount:</span>
                <span class="text-amber-300">${{ calculateTotal() }} USD</span>
              </div>
            </div>

            <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 text-center space-y-2">
              <div class="text-xs text-slate-400 font-bold">SCAN ABA KHQR TO PAY NOW</div>
              <div class="w-36 h-36 bg-white p-2 rounded-xl inline-block shadow-lg border-2 border-red-600">
                <div class="text-red-700 font-black text-[10px]">KHQR ABA</div>
                <div class="w-full h-24 bg-slate-100 flex items-center justify-center text-slate-500 text-[10px] font-mono">[ABA QR CODE]</div>
              </div>
              <div class="text-[10px] text-amber-300 font-mono">Status: ⏳ Pending Verification</div>
            </div>
          </div>
        </div>

        <!-- STEP 5: CONFIRM & COMPLETE -->
        <div v-if="currentStep === 5" class="space-y-4">
          <h3 class="text-sm font-bold text-white uppercase border-b border-slate-800 pb-2">STEP 5 — CONFIRM ENROLLMENT & SUBMIT</h3>
          <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-2 font-mono text-xs">
            <div><span class="text-slate-400">Student:</span> <strong class="text-white">{{ wizardForm.student_name }} ({{ wizardForm.student_id }})</strong></div>
            <div><span class="text-slate-400">Major:</span> <strong class="text-emerald-300">{{ wizardForm.major }}</strong></div>
            <div><span class="text-slate-400">Courses Picked:</span> <strong class="text-sky-300">{{ wizardForm.selected_courses.filter(c => c.checked).length }} Enrolled Courses</strong></div>
            <div><span class="text-slate-400">Total Price:</span> <strong class="text-amber-300">${{ calculateTotal() }} USD</strong></div>
          </div>

          <div class="flex items-center gap-2 text-slate-300">
            <input v-model="wizardForm.send_email" type="checkbox" id="sendmail" class="rounded bg-slate-950 border-slate-800" />
            <label for="sendmail">Send Enrollment Confirmation Email to {{ wizardForm.email }}</label>
          </div>
        </div>

        <!-- WIZARD NAVIGATION FOOTER BUTTONS -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-800">
          <button v-if="currentStep > 1" @click="prevStep" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl font-bold">
            ← Back
          </button>
          <div v-else></div>

          <button v-if="currentStep < 5" @click="nextStep" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold">
            Next Step →
          </button>
          <button v-else @click="completeWizard" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-sky-600 hover:from-emerald-500 hover:to-sky-500 text-white rounded-xl font-bold shadow-lg shadow-emerald-600/30">
            ✅ Confirm Enrollment
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
