<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const searchQuery = ref('')
const selectedMajor = ref('all')
const selectedMode = ref('all')
const selectedPrice = ref('all')
const selectedLevel = ref('all')
const selectedRating = ref('all')
const sortBy = ref('popular')

const isPreviewOpen = ref(false)
const selectedCourseForPreview = ref<any>(null)

const isEnrollOpen = ref(false)
const enrollStep = ref<1 | 2 | 3>(1)
const selectedEnrollMode = ref<'teacher-led' | 'self-study'>('teacher-led')
const isPaymentVerified = ref(false)

const catalogCourses = ref([
  {
    id: 101,
    title: 'Laravel 11 Masterclass',
    teacher: 'Mr. Sophea',
    teacherAvatar: 'https://ui-avatars.com/api/?name=Sophea&background=3b82f6&color=fff',
    major: 'IT & Networking',
    faculty: 'Faculty of Computing',
    mode: 'Teacher-Led',
    level: 'Intermediate',
    price: 30,
    priceDisplay: '$30.00',
    isFree: false,
    rating: 4.9,
    reviewsCount: 120,
    studentsCount: 450,
    thumbnail: 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=600&auto=format&fit=crop&q=80',
    description: 'Master modern web development with Laravel 11, REST APIs, Sanctum authentication, and cloud deployment.',
    wishlist: false
  },
  {
    id: 102,
    title: 'Tourism Marketing & Promotion',
    teacher: 'Mr. Long',
    teacherAvatar: 'https://ui-avatars.com/api/?name=Long&background=10b981&color=fff',
    major: 'Tourism Management',
    faculty: 'Faculty of Business',
    mode: 'Self-Study',
    level: 'Beginner',
    price: 25,
    priceDisplay: '$25.00',
    isFree: false,
    rating: 4.7,
    reviewsCount: 85,
    studentsCount: 280,
    thumbnail: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=600&auto=format&fit=crop&q=80',
    description: 'Strategic digital marketing for travel agencies, hotels, and ecotourism ventures in Cambodia.',
    wishlist: true
  },
  {
    id: 103,
    title: 'English Academic Writing',
    teacher: 'Ms. Srey',
    teacherAvatar: 'https://ui-avatars.com/api/?name=Srey&background=ec4899&color=fff',
    major: 'English Literature',
    faculty: 'Faculty of Humanities',
    mode: 'Self-Study',
    level: 'Beginner',
    price: 0,
    priceDisplay: 'FREE',
    isFree: true,
    rating: 4.8,
    reviewsCount: 95,
    studentsCount: 320,
    thumbnail: 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&auto=format&fit=crop&q=80',
    description: 'Improve research paper writing, vocabulary, and grammar structures for international academic standards.',
    wishlist: false
  },
  {
    id: 104,
    title: 'Plant Disease Control & Pest Management',
    teacher: 'Mr. Vuthy',
    teacherAvatar: 'https://ui-avatars.com/api/?name=Vuthy&background=f59e0b&color=fff',
    major: 'Agronomy',
    faculty: 'Faculty of Agriculture',
    mode: 'Teacher-Led',
    level: 'Intermediate',
    price: 25,
    priceDisplay: '$25.00',
    isFree: false,
    rating: 4.6,
    reviewsCount: 60,
    studentsCount: 180,
    thumbnail: 'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=600&auto=format&fit=crop&q=80',
    description: 'Identify agricultural pests, crop diseases, and sustainable organic protection methods.',
    wishlist: true
  }
])

const filteredCatalog = computed(() => {
  return catalogCourses.value.filter(c => {
    const matchesSearch = c.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          c.teacher.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesMajor = selectedMajor.value === 'all' || c.major === selectedMajor.value
    const matchesMode = selectedMode.value === 'all' || c.mode === selectedMode.value
    const matchesPrice = selectedPrice.value === 'all' ||
                         (selectedPrice.value === 'free' && c.isFree) ||
                         (selectedPrice.value === 'low' && c.price > 0 && c.price <= 25) ||
                         (selectedPrice.value === 'high' && c.price > 25)
    return matchesSearch && matchesMajor && matchesMode && matchesPrice
  })
})

const openPreview = (course: any) => {
  selectedCourseForPreview.value = course
  isPreviewOpen.value = true
}

const openEnroll = (course: any) => {
  selectedCourseForPreview.value = course
  enrollStep.value = 1
  isPaymentVerified.value = false
  isEnrollOpen.value = true
}

const toggleWishlist = (course: any) => {
  course.wishlist = !course.wishlist
}

const verifyPayment = () => {
  isPaymentVerified.value = true
  enrollStep.value = 3
}
</script>

<template>
  <StudentLayout title="Course Catalog — Browse & Enroll">
    <div class="space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
          <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
            <span>🔍</span>
            <span>COURSE CATALOG — Browse & Enroll</span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            រកមុខវិជ្ជាថ្មីសម្រាប់ចុះឈ្មោះរៀន — តាម Major, Price, Mode, Rating
          </p>
        </div>

        <Link
          href="/student/my-courses/wishlist"
          class="px-4 py-2 rounded-xl bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/30 font-bold text-xs transition-all flex items-center gap-2"
        >
          <span>💜 បញ្ជីប្រាថ្នា Wishlist</span>
        </Link>
      </div>

      <!-- Search & Filters Container (Matching Prompt Spec) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-5 shadow-xl space-y-4">
        
        <!-- Search Input -->
        <div class="relative">
          <svg class="w-5 h-5 text-indigo-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search course name, teacher, topic..."
            class="w-full bg-slate-900 border border-slate-700 rounded-2xl pl-12 pr-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all shadow-inner"
          />
        </div>

        <!-- Filters Grid -->
        <div class="flex flex-wrap items-center gap-2.5 pt-1 text-xs">
          <!-- Major Buttons -->
          <button
            v-for="m in ['all', 'IT & Networking', 'Tourism Management', 'English Literature', 'Agronomy']"
            :key="m"
            @click="selectedMajor = m"
            :class="[selectedMajor === m ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-900/80 text-slate-300 hover:bg-slate-700', 'px-3 py-1.5 rounded-xl border border-slate-700/80 transition-all']"
          >
            {{ m === 'all' ? 'Major: All' : m }}
          </button>

          <!-- Mode Select -->
          <select v-model="selectedMode" class="bg-slate-900/80 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs text-slate-200">
            <option value="all">Mode: All</option>
            <option value="Teacher-Led">🎥 Teacher-Led</option>
            <option value="Self-Study">💻 Self-Study</option>
          </select>

          <!-- Price Select -->
          <select v-model="selectedPrice" class="bg-slate-900/80 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs text-slate-200">
            <option value="all">Price: All</option>
            <option value="free">🎁 Free</option>
            <option value="low">💳 Paid ($1-$25)</option>
            <option value="high">💳 Paid ($26-$50)</option>
          </select>
        </div>
      </div>

      <!-- POPULAR COURSES GRID -->
      <div class="space-y-4">
        <h3 class="text-sm font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
          <span>🔥</span>
          <span>POPULAR COURSES</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="course in filteredCatalog"
            :key="course.id"
            class="bg-slate-800/90 border border-slate-700/80 rounded-3xl overflow-hidden shadow-xl hover:border-indigo-500/40 transition-all flex flex-col justify-between group"
          >
            <!-- Thumbnail -->
            <div class="relative h-40 overflow-hidden bg-slate-900">
              <img :src="course.thumbnail" :alt="course.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
              
              <!-- Price Badge -->
              <div class="absolute top-3 right-3">
                <span :class="[course.isFree ? 'bg-emerald-600 text-white' : 'bg-slate-900/90 text-indigo-300 border-slate-700', 'px-2.5 py-1 rounded-full text-[10px] font-bold border shadow-md']">
                  {{ course.priceDisplay }}
                </span>
              </div>

              <!-- Mode Badge -->
              <div class="absolute top-3 left-3">
                <span class="px-2.5 py-1 rounded-full bg-slate-900/80 text-white text-[10px] font-bold shadow-md">
                  {{ course.mode === 'Teacher-Led' ? '🎥 Teacher-Led' : '💻 Self-Study' }}
                </span>
              </div>
            </div>

            <!-- Content Body -->
            <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
              <div class="space-y-1.5">
                <p class="text-[10px] text-slate-400 font-semibold">🏫 {{ course.major }}</p>
                <h4 class="text-sm font-bold text-white line-clamp-1 group-hover:text-indigo-300 transition-colors">
                  {{ course.title }}
                </h4>
                <p class="text-xs text-slate-400 font-medium">👨‍🏫 {{ course.teacher }}</p>

                <!-- Rating & Students Count -->
                <div class="flex items-center justify-between text-xs pt-1">
                  <span class="text-amber-400 font-bold">⭐ {{ course.rating }} ({{ course.reviewsCount }})</span>
                  <span class="text-slate-400 text-[10px]">👨‍🎓 {{ course.studentsCount }} Students</span>
                </div>
              </div>

              <!-- Action Buttons (Matching Prompt Specs) -->
              <div class="space-y-2 pt-2 border-t border-slate-700/60">
                <div class="grid grid-cols-2 gap-2">
                  <button
                    @click="openPreview(course)"
                    class="py-2 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-200 font-bold text-[11px] border border-slate-700 transition-all text-center"
                  >
                    👁 Preview
                  </button>
                  <button
                    @click="toggleWishlist(course)"
                    :class="[course.wishlist ? 'bg-purple-600/30 text-purple-300 border-purple-500/40' : 'bg-slate-900 text-slate-400 hover:text-white', 'py-2 rounded-xl font-bold text-[11px] border border-slate-700 transition-all text-center']"
                  >
                    {{ course.wishlist ? '💜 Saved' : '💜 Wishlist' }}
                  </button>
                </div>

                <button
                  @click="openEnroll(course)"
                  :class="[course.isFree ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-indigo-600 hover:bg-indigo-500', 'w-full py-2.5 rounded-xl text-white font-bold text-xs transition-all shadow-md']"
                >
                  {{ course.isFree ? '🚀 Enroll Free' : '🚀 Enroll Now' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- COURSE DETAIL / PREVIEW MODAL (Matching Prompt Specs) -->
      <div v-if="isPreviewOpen && selectedCourseForPreview" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="bg-slate-900 rounded-3xl max-w-3xl w-full border border-slate-700 shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
          <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-base font-bold text-white">📘 COURSE DETAIL — {{ selectedCourseForPreview.title }}</h3>
            <button @click="isPreviewOpen = false" class="text-slate-400 hover:text-white text-lg font-bold">✕</button>
          </div>

          <div class="p-6 overflow-y-auto custom-scrollbar space-y-6">
            <!-- Video Intro & Info Box -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="bg-slate-950 rounded-2xl overflow-hidden aspect-video relative flex items-center justify-center border border-slate-800">
                <img :src="selectedCourseForPreview.thumbnail" class="w-full h-full object-cover opacity-60" />
                <button class="absolute p-4 rounded-full bg-indigo-600 text-white font-bold shadow-xl">▶ INTRO PREVIEW (2m)</button>
              </div>

              <div class="space-y-2 text-xs">
                <p><span class="text-slate-400">👨‍🏫 Teacher:</span> <span class="font-bold text-white">{{ selectedCourseForPreview.teacher }}</span></p>
                <p><span class="text-slate-400">🏫 Major:</span> <span class="font-bold text-white">{{ selectedCourseForPreview.major }}</span></p>
                <p><span class="text-slate-400">🏛️ Faculty:</span> <span class="font-bold text-white">{{ selectedCourseForPreview.faculty }}</span></p>
                <p><span class="text-slate-400">🎥 Mode:</span> <span class="font-bold text-white">{{ selectedCourseForPreview.mode }}</span></p>
                <p><span class="text-slate-400">📅 Duration:</span> <span class="font-bold text-white">8 Weeks</span></p>
                <p><span class="text-slate-400">📚 Modules:</span> <span class="font-bold text-white">6 Modules • 24 Chapters</span></p>
                <p><span class="text-slate-400">⭐ Rating:</span> <span class="font-bold text-amber-400">{{ selectedCourseForPreview.rating }} ({{ selectedCourseForPreview.reviewsCount }} reviews)</span></p>
              </div>
            </div>

            <!-- Price & Action Banner -->
            <div class="p-4 bg-slate-800 rounded-2xl border border-slate-700 flex items-center justify-between">
              <div>
                <p class="text-xs text-slate-400">Course Price:</p>
                <p class="text-xl font-black text-indigo-400">{{ selectedCourseForPreview.priceDisplay }}</p>
              </div>
              <button @click="isPreviewOpen = false; openEnroll(selectedCourseForPreview)" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg">
                🚀 Enroll & Pay Now
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ABA ENROLLMENT FLOW MODAL (Matching Step 1, Step 2, Step 3 Specs) -->
      <div v-if="isEnrollOpen && selectedCourseForPreview" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md">
        <div class="bg-slate-900 rounded-3xl max-w-lg w-full border border-slate-700 shadow-2xl p-6 space-y-6">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-sm font-bold text-white">🚀 ENROLL — {{ selectedCourseForPreview.title }}</h3>
            <button @click="isEnrollOpen = false" class="text-slate-400 hover:text-white">✕</button>
          </div>

          <!-- STEP 1: CHOOSE LEARNING MODE -->
          <div v-if="enrollStep === 1" class="space-y-4 text-xs">
            <p class="font-bold text-slate-300">STEP 1: CHOOSE LEARNING MODE</p>
            <div class="grid grid-cols-2 gap-3">
              <div
                @click="selectedEnrollMode = 'teacher-led'"
                :class="[selectedEnrollMode === 'teacher-led' ? 'border-indigo-500 bg-indigo-500/10' : 'border-slate-700 bg-slate-800', 'p-3 rounded-2xl border cursor-pointer space-y-1.5']"
              >
                <p class="font-bold text-white">🎥 Teacher-Led</p>
                <p class="font-black text-indigo-400">$30.00</p>
                <ul class="text-[10px] text-slate-400 space-y-1">
                  <li>✓ Live Class (Zoom)</li>
                  <li>✓ Teacher Q&A</li>
                  <li>✓ Certificate</li>
                </ul>
              </div>

              <div
                @click="selectedEnrollMode = 'self-study'"
                :class="[selectedEnrollMode === 'self-study' ? 'border-indigo-500 bg-indigo-500/10' : 'border-slate-700 bg-slate-800', 'p-3 rounded-2xl border cursor-pointer space-y-1.5']"
              >
                <p class="font-bold text-white">💻 Self-Study</p>
                <p class="font-black text-indigo-400">$20.00</p>
                <ul class="text-[10px] text-slate-400 space-y-1">
                  <li>✓ All Videos</li>
                  <li>✓ AI Path</li>
                  <li>✓ Certificate</li>
                </ul>
              </div>
            </div>

            <button @click="enrollStep = 2" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md">
              Continue to Payment →
            </button>
          </div>

          <!-- STEP 2: ABA PAYMENT KHQR -->
          <div v-else-if="enrollStep === 2" class="space-y-4 text-xs text-center">
            <p class="font-bold text-slate-300">STEP 2: SCAN ABA KHQR PAYMENT</p>

            <div class="p-4 bg-white rounded-2xl w-48 mx-auto border shadow-xl flex flex-col items-center">
              <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=ABA_ELMS_PAYMENT" class="w-36 h-36 object-contain" />
              <p class="text-[10px] font-bold text-slate-900 mt-2">Scan with ABA Mobile App</p>
              <p class="text-[9px] text-slate-500">Order: PAY-25060201</p>
            </div>

            <button @click="verifyPayment" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md">
              ✅ I have paid via ABA
            </button>
          </div>

          <!-- STEP 3: CONFIRMATION SUCCESS -->
          <div v-else-if="enrollStep === 3" class="space-y-4 text-xs text-center">
            <div class="text-4xl">🎉</div>
            <h4 class="text-base font-extrabold text-white">Enrollment Successful!</h4>
            <p class="text-slate-300">Course unlocked! Receipt sent to your email.</p>
            <Link href="/student/my-courses/current" @click="isEnrollOpen = false" class="block w-full py-3 rounded-xl bg-indigo-600 text-white font-bold text-xs shadow-md">
              ▶ Start Learning Now
            </Link>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
