<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const announcementList = ref([
  {
    id: 1,
    title: '📢 Announcement: Module 3 is now open!',
    priority: '🔴 High',
    course: 'C Programming Basics',
    date: '16 Jun 2025 · 09:30 AM',
    khText: 'Module 3 បានបើកហើយ សូមចាប់ផ្តើមរៀនតាមលំដាប់។',
    enText: 'Module 3 is now open. Please start learning in sequence.',
    pinned: true,
    hidden: false
  },
  {
    id: 2,
    title: '📢 System Maintenance Scheduled',
    priority: '🟡 Normal',
    course: 'Platform Wide',
    date: '15 Jun 2025 · 11:00 PM',
    khText: 'ប្រព័ន្ធនឹងធ្វើការរៀបចំ Maintenance រយៈពេល ៣០ នាទីនៅថ្ងៃស្អែក។',
    enText: 'System maintenance scheduled for 30 mins tomorrow midnight.',
    pinned: false,
    hidden: false
  }
])

const togglePin = (item: any) => {
  item.pinned = !item.pinned
}

const hideItem = (item: any) => {
  item.hidden = true
}
</script>

<template>
  <StudentLayout title="Notifications — Announcements">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 border border-blue-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold uppercase tracking-wider">
            📢 SYSTEM ANNOUNCEMENTS
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>📢 ANNOUNCEMENTS</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            សេចក្តីប្រកាសសំខាន់ៗពីគ្រូបង្រៀន និងសាលា បង្ហាញជាភាសាខ្មែរ និងអង់គ្លេស
          </p>
        </div>
      </div>

      <!-- ANNOUNCEMENT LIST (Matching Prompt Spec Layout) -->
      <div class="space-y-4">
        <template v-for="item in announcementList" :key="item.id">
          <div
            v-if="!item.hidden"
            :class="[item.pinned ? 'bg-slate-800/90 border-blue-500/50 shadow-2xl' : 'bg-slate-800/60 border-slate-700/80 shadow-xl', 'rounded-3xl p-6 border space-y-4 transition-all']"
          >
            <!-- Card Header -->
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
              <div class="flex items-center gap-2">
                <span v-if="item.pinned" class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 font-bold text-[10px]">📌 PINNED</span>
                <h3 class="text-base font-black text-white">{{ item.title }}</h3>
              </div>
              <span class="px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 font-bold text-xs border border-rose-500/30">
                {{ item.priority }}
              </span>
            </div>

            <!-- Card Body -->
            <div class="space-y-1.5 text-xs text-slate-300">
              <p class="text-slate-400 font-bold">Course: <span class="text-indigo-300">{{ item.course }}</span> · Date: {{ item.date }}</p>
              <p class="text-white font-bold pt-1">KH: {{ item.khText }}</p>
              <p class="text-slate-400 italic">EN: {{ item.enText }}</p>
            </div>

            <!-- Card Actions -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-700/60 text-xs">
              <Link href="/student/learning-content/videos" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold shadow-md">
                🚀 Start Learning
              </Link>
              <button @click="togglePin(item)" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 font-bold">
                {{ item.pinned ? '📌 Unpin' : '📌 Pin' }}
              </button>
              <button @click="hideItem(item)" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-400 font-bold">
                🙈 Hide
              </button>
            </div>
          </div>
        </template>
      </div>

    </div>
  </StudentLayout>
</template>
