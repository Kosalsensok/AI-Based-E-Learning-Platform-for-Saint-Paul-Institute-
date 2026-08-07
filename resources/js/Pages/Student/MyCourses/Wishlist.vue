<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const notifySale = ref(true)
const notifyMajor = ref(true)
const remindWeekly = ref(true)

const wishlistedCourses = ref([
  {
    id: 1,
    title: 'Laravel 11 Masterclass',
    teacher: 'Mr. Sophea',
    mode: 'Teacher-Led',
    price: '$30.00',
    salePrice: null,
    rating: 4.9,
    studentsCount: 450,
    modulesCount: 6,
    addedDate: '10 Jun 2025'
  },
  {
    id: 2,
    title: 'Plant Disease Control',
    teacher: 'Mr. Vuthy',
    mode: 'Teacher-Led',
    price: '$25.00',
    salePrice: null,
    rating: 4.6,
    studentsCount: 180,
    modulesCount: 4,
    addedDate: '08 Jun 2025'
  },
  {
    id: 3,
    title: 'Social Work in Communities',
    teacher: 'Mr. Rithy',
    mode: 'Self-Study',
    price: '$15.00',
    salePrice: '$10.00',
    discountText: '🔥 SALE: $10.00 (33% off until 20 Jun)',
    rating: 4.5,
    studentsCount: 120,
    modulesCount: 5,
    addedDate: '05 Jun 2025'
  }
])

const removeItem = (id: number) => {
  wishlistedCourses.value = wishlistedCourses.value.filter(c => c.id !== id)
}
</script>

<template>
  <StudentLayout title="My Wishlist">
    <div class="space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
          <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
            <span>💜</span>
            <span>MY WISHLIST</span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            មុខវិជ្ជាដែលខ្ញុំចង់រៀន — រក្សាទុកសម្រាប់ចុះឈ្មោះពេលក្រោយ
          </p>
        </div>

        <span class="px-3.5 py-1.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold self-start">
          💜 {{ wishlistedCourses.length }} Courses in Wishlist
        </span>
      </div>

      <!-- Wishlist Empty State -->
      <div v-if="wishlistedCourses.length === 0" class="p-12 text-center bg-slate-800/80 rounded-3xl border border-slate-700/80 space-y-3">
        <div class="text-4xl">💜</div>
        <h3 class="text-base font-bold text-white">Your Wishlist is Empty</h3>
        <p class="text-xs text-slate-400">Browse the course catalog to save interesting courses for later!</p>
        <Link href="/student/my-courses/browse" class="inline-block px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-xs">
          🔍 Browse Course Catalog
        </Link>
      </div>

      <!-- Wishlist Items Cards List -->
      <div v-else class="space-y-4">
        <div
          v-for="course in wishlistedCourses"
          :key="course.id"
          class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-5 shadow-xl space-y-4 hover:border-purple-500/30 transition-all"
        >
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-700/60 pb-3">
            <div>
              <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span>💜</span>
                <span>{{ course.title }}</span>
              </h3>
              <p class="text-xs text-slate-400 mt-1">
                👨‍🏫 {{ course.teacher }} • {{ course.mode === 'Teacher-Led' ? '🎥 Teacher-Led' : '💻 Self-Study' }} • <span class="font-bold text-indigo-300">{{ course.salePrice || course.price }}</span>
              </p>
            </div>

            <div class="text-xs text-slate-400">
              Added: {{ course.addedDate }}
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-4 text-xs">
            <span class="text-amber-400 font-bold">⭐ {{ course.rating }}</span>
            <span class="text-slate-400">👨‍🎓 {{ course.studentsCount }} Students</span>
            <span class="text-slate-400">📚 {{ course.modulesCount }} Modules</span>
          </div>

          <!-- Sale Banner if applicable -->
          <div v-if="course.discountText" class="p-3 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-2xl text-xs font-bold">
            {{ course.discountText }}
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-wrap items-center gap-3 pt-2">
            <Link
              href="/student/my-courses/browse"
              class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition-all"
            >
              🚀 Enroll Now {{ course.salePrice ? `— ${course.salePrice}` : '' }}
            </Link>

            <Link
              href="/student/my-courses/browse"
              class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all"
            >
              👁 Preview
            </Link>

            <button
              @click="removeItem(course.id)"
              class="px-4 py-2.5 rounded-xl bg-rose-900/30 hover:bg-rose-900/50 text-rose-300 font-bold text-xs border border-rose-500/30 transition-all"
            >
              🗑 Remove
            </button>
          </div>
        </div>
      </div>

      <!-- WISHLIST ALERT PREFERENCES (Matching Prompt Spec) -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-5 shadow-xl space-y-3">
        <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
          <span>🔔</span>
          <span>WISHLIST ALERTS PREFERENCES</span>
        </h3>

        <div class="space-y-2 text-xs text-slate-300">
          <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" v-model="notifySale" class="rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-0" />
            <span>[✓] Notify me when a wishlisted course goes on sale</span>
          </label>

          <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" v-model="notifyMajor" class="rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-0" />
            <span>[✓] Notify me when a new course matches my major</span>
          </label>

          <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" v-model="remindWeekly" class="rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-0" />
            <span>[✓] Remind me weekly about my wishlist</span>
          </label>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
